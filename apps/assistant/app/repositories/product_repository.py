import psycopg2

from app.config import settings

_PRODUCT_QUERY = """
    SELECT
        pv.id,
        p.name                        AS product_name,
        pv.name                       AS variant_name,
        pv.price,
        (pv.price - d.price)          AS discounted_price,
        pv.description,
        b.name                        AS brand,
        c.name                        AS category,
        t.name                        AS type,
        array_agg(DISTINCT st.name)   AS subtypes,
        pv.url
    FROM product_variants pv
    JOIN products p               ON p.id  = pv.product_id
    JOIN brands b                 ON b.id  = p.brand_id
    JOIN categories c             ON c.id  = p.category_id
    JOIN product_variant_stocks s ON s.variant_id = pv.id
    LEFT JOIN LATERAL (
        SELECT price FROM product_variant_discounts
        WHERE variant_id = pv.id
        ORDER BY created_at DESC
        LIMIT 1
    ) d ON true
    LEFT JOIN types t             ON t.id          = p.type_id
    LEFT JOIN product_subtypes ps ON ps.product_id = p.id
    LEFT JOIN subtypes st         ON st.id         = ps.subtype_id
    WHERE pv.status            = 'available'
      AND s.quantity_available > 0
      AND s.status             = 'in_stock'
      AND EXISTS (
          SELECT 1
          FROM product_variant_eans e
          WHERE e.variant_id = pv.id
            AND e.status = 'active'
      )
    GROUP BY pv.id, p.name, pv.name, pv.price, d.price,
             pv.description, b.name, c.name, t.name, pv.url
"""

_OUT_OF_STOCK_QUERY = """
    SELECT
        pv.id,
        p.name                        AS product_name,
        pv.name                       AS variant_name,
        b.name                        AS brand,
        c.name                        AS category,
        pv.url
    FROM product_variants pv
    JOIN products p               ON p.id  = pv.product_id
    JOIN brands b                 ON b.id  = p.brand_id
    JOIN categories c             ON c.id  = p.category_id
    JOIN product_variant_stocks s ON s.variant_id = pv.id
    WHERE pv.status = 'available'
      AND (s.quantity_available = 0 OR s.status != 'in_stock')
    GROUP BY pv.id, p.name, pv.name, b.name, c.name, pv.url
"""

_CATALOG_SUMMARY_QUERY = """
    SELECT
        array_agg(DISTINCT b.name  ORDER BY b.name)  AS brands,
        array_agg(DISTINCT c.name  ORDER BY c.name)  AS categories,
        array_agg(DISTINCT t.name  ORDER BY t.name)  AS types,
        array_agg(DISTINCT st.name ORDER BY st.name) AS subtypes
    FROM product_variants pv
    JOIN products p               ON p.id = pv.product_id
    JOIN brands b                 ON b.id = p.brand_id
    JOIN categories c             ON c.id = p.category_id
    JOIN product_variant_stocks s ON s.variant_id = pv.id
    LEFT JOIN types t             ON t.id = p.type_id
    LEFT JOIN product_subtypes ps ON ps.product_id = p.id
    LEFT JOIN subtypes st         ON st.id = ps.subtype_id
    WHERE pv.status            = 'available'
      AND s.quantity_available > 0
      AND s.status             = 'in_stock'
"""

_TOP_ORDERED_QUERY = """
    SELECT
        pv.id,
        p.name                        AS product_name,
        pv.name                       AS variant_name,
        pv.price,
        (pv.price - d.price)          AS discounted_price,
        b.name                        AS brand,
        c.name                        AS category,
        COUNT(DISTINCT oi.order_id)   AS order_count,
        pv.url,
        pv.description,
        (SELECT i.path FROM product_variant_images i
         WHERE i.variant_id = pv.id ORDER BY i.position LIMIT 1) AS image_path,
        LEAST(s.quantity_available, 5)                            AS stock_level
    FROM order_items oi
    JOIN product_variants pv ON pv.id = oi.variant_id
    JOIN products p          ON p.id  = pv.product_id
    JOIN brands b            ON b.id  = p.brand_id
    JOIN categories c        ON c.id  = p.category_id
    JOIN product_variant_stocks s ON s.variant_id = pv.id
    LEFT JOIN LATERAL (
        SELECT price FROM product_variant_discounts
        WHERE variant_id = pv.id
        ORDER BY created_at DESC
        LIMIT 1
    ) d ON true
    WHERE pv.status            = 'available'
      AND s.quantity_available > 0
      AND s.status             = 'in_stock'
    GROUP BY pv.id, p.name, pv.name, pv.price, d.price, b.name, c.name,
             pv.url, pv.description, s.quantity_available
    ORDER BY order_count DESC
    LIMIT 10
"""

_TOP_REVIEWED_QUERY = """
    SELECT
        pv.id,
        p.name                                          AS product_name,
        pv.name                                         AS variant_name,
        pv.price,
        (pv.price - d.price)                            AS discounted_price,
        b.name                                          AS brand,
        c.name                                          AS category,
        COUNT(r.id) FILTER (WHERE r.type = 'feedback') AS feedback_count,
        COUNT(r.id) FILTER (WHERE r.type = 'rating')   AS rating_count,
        ROUND(AVG(r.value)::numeric, 1)                 AS avg_rating,
        pv.url,
        pv.description,
        (SELECT i.path FROM product_variant_images i
         WHERE i.variant_id = pv.id ORDER BY i.position LIMIT 1) AS image_path,
        LEAST(s.quantity_available, 5)                            AS stock_level
    FROM reviews r
    JOIN product_variants pv ON pv.id = r.variant_id
    JOIN products p          ON p.id  = pv.product_id
    JOIN brands b            ON b.id  = p.brand_id
    JOIN categories c        ON c.id  = p.category_id
    JOIN product_variant_stocks s ON s.variant_id = pv.id
    LEFT JOIN LATERAL (
        SELECT price FROM product_variant_discounts
        WHERE variant_id = pv.id
        ORDER BY created_at DESC
        LIMIT 1
    ) d ON true
    WHERE pv.status            = 'available'
      AND s.quantity_available > 0
      AND s.status             = 'in_stock'
    GROUP BY pv.id, p.name, pv.name, pv.price, d.price, b.name, c.name,
             pv.url, pv.description, s.quantity_available
    HAVING COUNT(r.id) >= 1
    ORDER BY avg_rating DESC, COUNT(r.id) DESC
    LIMIT 20
"""


def fetch_out_of_stock_products() -> list[tuple[object, ...]]:
    conn = psycopg2.connect(settings.database_url)

    try:
        cursor = conn.cursor()
        cursor.execute(_OUT_OF_STOCK_QUERY)

        rows: list[tuple[object, ...]] = cursor.fetchall()

        cursor.close()
    finally:
        conn.close()
    return rows


def fetch_available_products() -> list[tuple[object, ...]]:
    """Return product variants that are available, have stock and an active EAN."""

    conn = psycopg2.connect(settings.database_url)

    try:
        cursor = conn.cursor()
        cursor.execute(_PRODUCT_QUERY)

        rows: list[tuple[object, ...]] = cursor.fetchall()

        cursor.close()
    finally:
        conn.close()
    return rows


def fetch_catalog_summary() -> dict[str, list[str]]:
    """Return all active brands, categories and types that have available products."""

    conn = psycopg2.connect(settings.database_url)

    try:
        cursor = conn.cursor()
        cursor.execute(_CATALOG_SUMMARY_QUERY)

        row = cursor.fetchone()

        cursor.close()
    finally:
        conn.close()

    if not row:
        return {"brands": [], "categories": [], "types": [], "subtypes": []}

    return {
        "brands": [x for x in (row[0] or []) if x],
        "categories": [x for x in (row[1] or []) if x],
        "types": [x for x in (row[2] or []) if x],
        "subtypes": [x for x in (row[3] or []) if x],
    }


def fetch_top_reviewed_products() -> list[tuple[object, ...]]:
    """Return top reviewed product variants ordered by review count and avg rating."""

    conn = psycopg2.connect(settings.database_url)

    try:
        cursor = conn.cursor()
        cursor.execute(_TOP_REVIEWED_QUERY)

        rows: list[tuple[object, ...]] = cursor.fetchall()

        cursor.close()
    finally:
        conn.close()
    return rows


def fetch_top_ordered_products() -> list[tuple[object, ...]]:
    """Return most ordered product variants."""

    conn = psycopg2.connect(settings.database_url)

    try:
        cursor = conn.cursor()
        cursor.execute(_TOP_ORDERED_QUERY)

        rows: list[tuple[object, ...]] = cursor.fetchall()

        cursor.close()
    finally:
        conn.close()
    return rows

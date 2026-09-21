import psycopg2

from app.config import settings

_PRODUCT_QUERY = """
    SELECT
        pv.id,
        p.name                        AS product_name,
        pv.name                       AS variant_name,
        pv.sku,
        pv.price,
        (pv.price - d.price)          AS discounted_price,
        pv.description,
        b.name                        AS brand,
        c.name                        AS category,
        t.name                        AS type,
        array_agg(DISTINCT st.name)   AS subtypes
    FROM product_variants pv
    JOIN products p               ON p.id  = pv.product_id
    JOIN brands b                 ON b.id  = p.brand_id
    JOIN categories c             ON c.id  = p.category_id
    JOIN product_variant_stocks s ON s.variant_id = pv.id
    LEFT JOIN product_variant_discounts d  ON d.variant_id  = pv.id
    LEFT JOIN types t                      ON t.id          = p.type_id
    LEFT JOIN product_subtypes ps          ON ps.product_id = p.id
    LEFT JOIN subtypes st                  ON st.id         = ps.subtype_id
    WHERE pv.status            = 'available'
      AND s.quantity_available > 0
      AND s.status             = 'in_stock'
      AND EXISTS (
          SELECT 1
          FROM product_variant_eans e
          WHERE e.variant_id = pv.id
            AND e.status = 'active'
      )
    GROUP BY pv.id, p.name, pv.name, pv.sku, pv.price, d.price,
             pv.description, b.name, c.name, t.name
"""

_CATALOG_SUMMARY_QUERY = """
    SELECT
        array_agg(DISTINCT b.name ORDER BY b.name) AS brands,
        array_agg(DISTINCT c.name ORDER BY c.name) AS categories,
        array_agg(DISTINCT t.name ORDER BY t.name) AS types
    FROM product_variants pv
    JOIN products p               ON p.id = pv.product_id
    JOIN brands b                 ON b.id = p.brand_id
    JOIN categories c             ON c.id = p.category_id
    JOIN product_variant_stocks s ON s.variant_id = pv.id
    LEFT JOIN types t             ON t.id = p.type_id
    WHERE pv.status            = 'available'
      AND s.quantity_available > 0
      AND s.status             = 'in_stock'
"""

_TOP_REVIEWED_QUERY = """
    SELECT
        pv.id,
        p.name                        AS product_name,
        pv.name                       AS variant_name,
        pv.price,
        (pv.price - d.price)          AS discounted_price,
        b.name                        AS brand,
        c.name                        AS category,
        COUNT(r.id)                   AS review_count,
        ROUND(AVG(r.value)::numeric, 1) AS avg_rating
    FROM reviews r
    JOIN product_variants pv ON pv.id = r.variant_id
    JOIN products p          ON p.id  = pv.product_id
    JOIN brands b            ON b.id  = p.brand_id
    JOIN categories c        ON c.id  = p.category_id
    JOIN product_variant_stocks s ON s.variant_id = pv.id
    LEFT JOIN product_variant_discounts d ON d.variant_id = pv.id
    WHERE pv.status            = 'available'
      AND s.quantity_available > 0
      AND s.status             = 'in_stock'
    GROUP BY pv.id, p.name, pv.name, pv.price, d.price, b.name, c.name
    HAVING COUNT(r.id) >= 3
    ORDER BY review_count DESC, avg_rating DESC
    LIMIT 20
"""


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
        return {"brands": [], "categories": [], "types": []}

    return {
        "brands": [x for x in (row[0] or []) if x],
        "categories": [x for x in (row[1] or []) if x],
        "types": [x for x in (row[2] or []) if x],
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

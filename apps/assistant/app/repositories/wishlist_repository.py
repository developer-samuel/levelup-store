import psycopg2

from app.config import settings

_USER_WISHLIST_QUERY = """
    SELECT
        p.name                                          AS product_name,
        pv.name                                         AS variant_name,
        b.name                                          AS brand,
        pv.price,
        (pv.price - d.price)                            AS discounted_price,
        pv.url,
        (SELECT i.path FROM product_variant_images i
         WHERE i.variant_id = pv.id ORDER BY i.position LIMIT 1) AS image_path,
        COUNT(r.id) FILTER (WHERE r.type = 'feedback')  AS feedback_count,
        COUNT(r.id) FILTER (WHERE r.type = 'rating')    AS rating_count,
        ROUND(AVG(r.value)::numeric, 1)                 AS avg_rating,
        LEAST(pvs.quantity_available, 5)                AS stock_level
    FROM wishlists w
    JOIN product_variants pv      ON pv.id = w.variant_id
    JOIN products p               ON p.id  = pv.product_id
    JOIN brands b                 ON b.id  = p.brand_id
    JOIN product_variant_stocks pvs ON pvs.variant_id = pv.id
    LEFT JOIN LATERAL (
        SELECT price FROM product_variant_discounts
        WHERE variant_id = pv.id
        ORDER BY created_at DESC
        LIMIT 1
    ) d ON true
    LEFT JOIN reviews r ON r.variant_id = pv.id
    WHERE w.user_id = %s
    GROUP BY p.name, pv.name, b.name, pv.price, d.price, pv.url, pv.id,
             w.created_at, pvs.quantity_available
    ORDER BY w.created_at DESC
    LIMIT 50
"""


def fetch_user_wishlist(user_id: int) -> list[tuple[object, ...]]:
    conn = psycopg2.connect(settings.database_url)

    try:
        cursor = conn.cursor()
        cursor.execute(_USER_WISHLIST_QUERY, (user_id,))

        rows: list[tuple[object, ...]] = cursor.fetchall()

        cursor.close()
    finally:
        conn.close()
    return rows

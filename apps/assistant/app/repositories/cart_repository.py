import psycopg2

from app.config import settings

_USER_CART_QUERY = """
    SELECT
        c.id,
        p.name                        AS product_name,
        pv.name                       AS variant_name,
        b.name                        AS brand,
        pv.price,
        (pv.price - d.price)          AS discounted_price,
        pv.url,
        (SELECT i.path FROM product_variant_images i
         WHERE i.variant_id = pv.id ORDER BY i.position LIMIT 1) AS image_path,
        LEAST(pvs.quantity_available, 5)                          AS stock_level
    FROM carts c
    JOIN cart_items ci              ON ci.cart_id   = c.id
    JOIN product_variants pv        ON pv.id        = ci.variant_id
    JOIN products p                 ON p.id         = pv.product_id
    JOIN brands b                   ON b.id         = p.brand_id
    JOIN product_variant_stocks pvs ON pvs.variant_id = pv.id
    LEFT JOIN LATERAL (
        SELECT price FROM product_variant_discounts
        WHERE variant_id = pv.id
        ORDER BY created_at DESC
        LIMIT 1
    ) d ON true
    WHERE c.user_id = %s
    ORDER BY ci.created_at DESC
"""


def fetch_user_cart(user_id: int) -> list[tuple[object, ...]]:
    conn = psycopg2.connect(settings.database_url)

    try:
        cursor = conn.cursor()
        cursor.execute(_USER_CART_QUERY, (user_id,))

        rows: list[tuple[object, ...]] = cursor.fetchall()

        cursor.close()
    finally:
        conn.close()
    return rows

import psycopg2

from app.config import settings

_ORDERS_QUERY = """
    SELECT
        o.code,
        o.price,
        o.status,
        o.created_at
    FROM orders o
    WHERE o.user_id = %s
    ORDER BY o.created_at DESC
    LIMIT 20
"""

_ORDER_PROFILE_QUERY = """
    SELECT DISTINCT
        b.name  AS brand,
        c.name  AS category,
        t.name  AS type
    FROM orders o
    JOIN order_items oi    ON oi.order_id   = o.id
    JOIN product_variants pv ON pv.id       = oi.variant_id
    JOIN products p          ON p.id        = pv.product_id
    JOIN brands b            ON b.id        = p.brand_id
    JOIN categories c        ON c.id        = p.category_id
    LEFT JOIN types t        ON t.id        = p.type_id
    WHERE o.user_id = %s
    ORDER BY brand, category
"""


def fetch_user_orders(user_id: int) -> list[tuple[object, ...]]:
    conn = psycopg2.connect(settings.database_url)

    try:
        cursor = conn.cursor()
        cursor.execute(_ORDERS_QUERY, (user_id,))
        rows: list[tuple[object, ...]] = cursor.fetchall()
        cursor.close()
    finally:
        conn.close()

    return rows


def fetch_user_order_profile(user_id: int) -> list[tuple[object, ...]]:
    conn = psycopg2.connect(settings.database_url)

    try:
        cursor = conn.cursor()
        cursor.execute(_ORDER_PROFILE_QUERY, (user_id,))
        rows: list[tuple[object, ...]] = cursor.fetchall()
        cursor.close()
    finally:
        conn.close()

    return rows

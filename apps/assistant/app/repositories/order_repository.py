import psycopg2

from app.config import settings

_USER_ORDERS_QUERY = """
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


def fetch_user_orders(user_id: int) -> list[tuple[object, ...]]:
    conn = psycopg2.connect(settings.database_url)

    try:
        cursor = conn.cursor()
        cursor.execute(_USER_ORDERS_QUERY, (user_id,))
        
        rows: list[tuple[object, ...]] = cursor.fetchall()

        cursor.close()
    finally:
        conn.close()
    return rows

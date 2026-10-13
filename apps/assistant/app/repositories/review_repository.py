import psycopg2

from app.config import settings

_REVIEWS_QUERY = """
    SELECT
        r.id,
        p.name                AS product_name,
        pv.name               AS variant_name,
        b.name                AS brand,
        r.body,
        r.value               AS rating,
        r.type,
        pv.url
    FROM reviews r
    JOIN product_variants pv ON pv.id = r.variant_id
    JOIN products p          ON p.id  = pv.product_id
    JOIN brands b            ON b.id  = p.brand_id
    WHERE r.body IS NOT NULL
      AND r.body != ''
      AND r.type = 'feedback'
    ORDER BY r.created_at DESC
    LIMIT 500
"""

_USER_REVIEWS_QUERY = """
    SELECT
        p.name                AS product_name,
        pv.name               AS variant_name,
        b.name                AS brand,
        r.value               AS rating,
        r.body,
        r.type,
        r.created_at,
        pv.url
    FROM reviews r
    JOIN product_variants pv ON pv.id = r.variant_id
    JOIN products p          ON p.id  = pv.product_id
    JOIN brands b            ON b.id  = p.brand_id
    WHERE r.user_id = %s
    ORDER BY r.created_at DESC
    LIMIT 50
"""


def fetch_reviews() -> list[tuple[object, ...]]:
    """Return recent customer feedback reviews for ChromaDB ingest."""

    conn = psycopg2.connect(settings.database_url)

    try:
        cursor = conn.cursor()
        cursor.execute(_REVIEWS_QUERY)

        rows: list[tuple[object, ...]] = cursor.fetchall()

        cursor.close()
    finally:
        conn.close()
    return rows


def fetch_user_reviews(user_id: int) -> list[tuple[object, ...]]:
    conn = psycopg2.connect(settings.database_url)

    try:
        cursor = conn.cursor()
        cursor.execute(_USER_REVIEWS_QUERY, (user_id,))

        rows: list[tuple[object, ...]] = cursor.fetchall()

        cursor.close()
    finally:
        conn.close()
    return rows

from app.database import get_connection

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

_ALL_REVIEWS_QUERY = """
    SELECT
        p.id,
        p.name                              AS product_name,
        b.name                              AS brand,
        MIN(pv.url)                         AS url,
        COUNT(r.id)                         AS review_count,
        ROUND(AVG(r.value)::numeric, 1)     AS avg_rating,
        STRING_AGG(r.body, ' | '
            ORDER BY r.created_at DESC)
            FILTER (WHERE r.body IS NOT NULL AND r.body <> '') AS review_texts
    FROM reviews r
    JOIN product_variants pv ON pv.id = r.variant_id
    JOIN products p          ON p.id  = pv.product_id
    JOIN brands b            ON b.id  = p.brand_id
    GROUP BY p.id, p.name, b.name
    ORDER BY COUNT(r.id) DESC
"""


def fetch_reviews() -> list[tuple[object, ...]]:
    with get_connection() as conn:
        cursor = conn.cursor()
        cursor.execute(_ALL_REVIEWS_QUERY)
        rows: list[tuple[object, ...]] = cursor.fetchall()
        cursor.close()
    return rows


def fetch_user_reviews(user_id: int) -> list[tuple[object, ...]]:
    with get_connection() as conn:
        cursor = conn.cursor()
        cursor.execute(_USER_REVIEWS_QUERY, (user_id,))
        rows: list[tuple[object, ...]] = cursor.fetchall()
        cursor.close()
    return rows

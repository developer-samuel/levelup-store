from app.database import get_connection

_USER_NAME_QUERY = """
    SELECT first_name, last_name
    FROM users
    WHERE id = %s
    LIMIT 1
"""


def fetch_user_name(user_id: int) -> tuple[str, str] | None:
    with get_connection() as conn:
        cursor = conn.cursor()
        cursor.execute(_USER_NAME_QUERY, (user_id,))
        row = cursor.fetchone()
        cursor.close()
    if not row:
        return None
    return (str(row[0]) if row[0] else "", str(row[1]) if row[1] else "")

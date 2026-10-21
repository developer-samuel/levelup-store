from unittest.mock import MagicMock, patch

import app.database as db_module


def test_get_connection_yields_and_returns() -> None:
    mock_pool = MagicMock()
    mock_conn = MagicMock()
    mock_pool.getconn.return_value = mock_conn

    db_module._pool = None

    with patch("app.database.psycopg2.pool.ThreadedConnectionPool", return_value=mock_pool):
        with db_module.get_connection() as conn:
            assert conn is mock_conn

    mock_pool.putconn.assert_called_once_with(mock_conn)


def test_get_connection_puts_back_on_exception() -> None:
    mock_pool = MagicMock()
    mock_conn = MagicMock()
    mock_pool.getconn.return_value = mock_conn

    db_module._pool = None

    with patch("app.database.psycopg2.pool.ThreadedConnectionPool", return_value=mock_pool):
        try:
            with db_module.get_connection():
                raise ValueError("test error")
        except ValueError:
            pass

    mock_pool.putconn.assert_called_once_with(mock_conn)


def test_pool_created_once() -> None:
    mock_pool = MagicMock()
    mock_pool.getconn.return_value = MagicMock()

    db_module._pool = None

    with patch(
        "app.database.psycopg2.pool.ThreadedConnectionPool", return_value=mock_pool
    ) as mock_cls:
        with db_module.get_connection():
            pass
        with db_module.get_connection():
            pass

    mock_cls.assert_called_once()

    db_module._pool = None

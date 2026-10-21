from collections.abc import Callable, Generator
from contextlib import AbstractContextManager, contextmanager
from unittest.mock import MagicMock, patch

from app.repositories.product_repository import fetch_catalog_summary
from app.repositories.user_repository import fetch_user_name


def _mock_get_connection(
    fetchone_return: object = None,
) -> Callable[[], AbstractContextManager[MagicMock]]:
    cursor = MagicMock()
    cursor.fetchone.return_value = fetchone_return
    conn = MagicMock()
    conn.cursor.return_value = cursor

    @contextmanager
    def _ctx() -> Generator[MagicMock, None, None]:
        yield conn

    return _ctx


def test_fetch_user_name_returns_none_when_no_row() -> None:
    with patch("app.repositories.user_repository.get_connection", _mock_get_connection(None)):
        result = fetch_user_name(999)

    assert result is None


def test_fetch_user_name_returns_tuple_when_row_exists() -> None:
    with patch(
        "app.repositories.user_repository.get_connection", _mock_get_connection(("John", "Doe"))
    ):
        result = fetch_user_name(1)

    assert result == ("John", "Doe")


def test_fetch_catalog_summary_returns_empty_when_no_row() -> None:
    with patch("app.repositories.product_repository.get_connection", _mock_get_connection(None)):
        result = fetch_catalog_summary()

    assert result == {"brands": [], "categories": [], "types": [], "subtypes": []}

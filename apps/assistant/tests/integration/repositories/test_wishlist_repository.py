import pytest

from app.repositories.wishlist_repository import fetch_user_wishlist

pytestmark = pytest.mark.integration

_NONEXISTENT_USER = 999999999


def test_fetch_user_wishlist_empty_for_unknown_user() -> None:
    rows = fetch_user_wishlist(_NONEXISTENT_USER)
    assert rows == []


def test_fetch_user_wishlist_returns_list() -> None:
    rows = fetch_user_wishlist(_NONEXISTENT_USER)
    assert isinstance(rows, list)

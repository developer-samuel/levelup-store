import pytest

from app.repositories.user_repository import fetch_user_name

pytestmark = pytest.mark.integration

_NONEXISTENT_USER = 999999999


def test_fetch_user_name_returns_none_for_unknown_user() -> None:
    result = fetch_user_name(_NONEXISTENT_USER)
    assert result is None

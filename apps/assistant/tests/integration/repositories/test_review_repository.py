import pytest

from app.repositories.review_repository import fetch_reviews, fetch_user_reviews

pytestmark = pytest.mark.integration

_NONEXISTENT_USER = 999999999


def test_fetch_reviews_returns_list() -> None:
    rows = fetch_reviews()
    assert isinstance(rows, list)


def test_fetch_reviews_row_shape() -> None:
    rows = fetch_reviews()
    if rows:
        assert len(rows[0]) == 7


def test_fetch_reviews_aggregated_per_product() -> None:
    rows = fetch_reviews()
    if rows:
        product_id, product_name, brand, url, review_count, avg_rating, review_texts = rows[0]
        assert isinstance(product_id, int)
        assert isinstance(product_name, str)
        assert isinstance(brand, str)
        assert isinstance(review_count, int)
        assert review_count >= 1


def test_fetch_user_reviews_empty_for_unknown_user() -> None:
    rows = fetch_user_reviews(_NONEXISTENT_USER)
    assert rows == []


def test_fetch_user_reviews_returns_list() -> None:
    rows = fetch_user_reviews(_NONEXISTENT_USER)
    assert isinstance(rows, list)

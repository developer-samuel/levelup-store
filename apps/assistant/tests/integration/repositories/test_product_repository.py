import pytest

from app.repositories.product_repository import (
    fetch_available_products,
    fetch_catalog_summary,
    fetch_out_of_stock_products,
    fetch_top_ordered_products,
    fetch_top_reviewed_products,
)

pytestmark = pytest.mark.integration


def test_fetch_available_products_returns_list() -> None:
    rows = fetch_available_products()
    assert isinstance(rows, list)


def test_fetch_available_products_row_shape() -> None:
    rows = fetch_available_products()
    if rows:
        assert len(rows[0]) == 11


def test_fetch_out_of_stock_products_returns_list() -> None:
    rows = fetch_out_of_stock_products()
    assert isinstance(rows, list)


def test_fetch_catalog_summary_keys() -> None:
    result = fetch_catalog_summary()
    assert set(result.keys()) == {"brands", "categories", "types", "subtypes"}
    for v in result.values():
        assert isinstance(v, list)


def test_fetch_top_reviewed_products_returns_list() -> None:
    rows = fetch_top_reviewed_products()
    assert isinstance(rows, list)


def test_fetch_top_reviewed_products_row_shape() -> None:
    rows = fetch_top_reviewed_products()
    if rows:
        assert len(rows[0]) == 14


def test_fetch_top_ordered_products_returns_list() -> None:
    rows = fetch_top_ordered_products()
    assert isinstance(rows, list)


def test_fetch_top_ordered_products_row_shape() -> None:
    rows = fetch_top_ordered_products()
    if rows:
        assert len(rows[0]) == 12

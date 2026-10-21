from pathlib import Path
from unittest.mock import MagicMock, patch

from jobs.ingest import (
    _build_document,
    _get_existing_hashes,
    _hash,
    _ingest_catalog_summary,
    _ingest_documents,
    _ingest_out_of_stock,
    _ingest_products,
    _ingest_reviews,
    _ingest_top_ordered,
    _ingest_top_reviewed,
    ingest,
)

Row = tuple[object, ...]


def test_hash_is_deterministic() -> None:
    assert _hash("hello") == _hash("hello")
    assert _hash("hello") != _hash("world")


def test_hash_returns_hex_string() -> None:
    result = _hash("test")
    assert isinstance(result, str)
    assert len(result) == 64


def test_build_document_returns_none_for_missing_product_name() -> None:
    row: Row = (1, None, "variant", 10.0, None, "desc", "Brand", "Cat", "Type", [], "url")
    assert _build_document(row) is None


def test_build_document_returns_none_for_zero_price() -> None:
    row: Row = (1, "Product", "variant", 0, None, "desc", "Brand", "Cat", "Type", [], "url")
    assert _build_document(row) is None


def test_build_document_returns_none_for_missing_brand() -> None:
    row: Row = (1, "Product", "variant", 10.0, None, "desc", None, "Cat", "Type", [], "url")
    assert _build_document(row) is None


def test_build_document_includes_sale_price() -> None:
    row: Row = (
        1,
        "Laptop",
        "Pro",
        1000.0,
        800.0,
        None,
        "Dell",
        "Electronics",
        None,
        [],
        "dell-laptop",
    )
    result = _build_document(row)

    assert result is not None

    doc_id, text = result

    assert doc_id == "1"
    assert "800.0 EUR (on sale" in text
    assert "1000.0 EUR" in text


def test_build_document_includes_regular_price() -> None:
    row: Row = (
        2,
        "Mouse",
        "Wireless",
        50.0,
        None,
        None,
        "Logitech",
        "Peripherals",
        None,
        [],
        "mouse",
    )
    result = _build_document(row)

    assert result is not None

    _, text = result

    assert "50.0 EUR" in text
    assert "on sale" not in text


def test_build_document_includes_subtypes() -> None:
    row: tuple[object, ...] = (
        3,
        "Chair",
        "Ergonomic",
        300.0,
        None,
        None,
        "IKEA",
        "Furniture",
        "Office",
        ["mesh", "adjustable"],
        "chair",
    )
    result = _build_document(row)

    assert result is not None

    _, text = result

    assert "mesh" in text
    assert "adjustable" in text


def test_build_document_includes_url() -> None:
    row: tuple[object, ...] = (
        4,
        "Desk",
        "Standing",
        500.0,
        None,
        None,
        "FlexiSpot",
        "Furniture",
        None,
        [],
        "standing-desk",
    )
    result = _build_document(row)

    assert result is not None

    _, text = result

    assert "/product/show/standing-desk" in text


def test_get_existing_hashes_filters_by_prefix() -> None:
    collection = MagicMock()
    collection.get.return_value = {
        "ids": ["oos_1", "review_1", "product_1"],
        "metadatas": [{"hash": "a"}, {"hash": "b"}, {"hash": "c"}],
    }

    result = _get_existing_hashes(collection, id_prefix="oos_")

    assert result == {"oos_1": "a"}


def test_get_existing_hashes_no_prefix_returns_all() -> None:
    collection = MagicMock()
    collection.get.return_value = {
        "ids": ["a", "b"],
        "metadatas": [{"hash": "x"}, {"hash": "y"}],
    }

    result = _get_existing_hashes(collection)

    assert result == {"a": "x", "b": "y"}


def test_ingest_products_skips_unchanged() -> None:
    row: Row = (1, "Laptop", "Pro", 1000.0, None, None, "Dell", "Electronics", None, [], "dell")
    text = "Dell Laptop - Pro. Category: Electronics. Price: 1000.0 EUR. URL: /product/show/dell"

    collection = MagicMock()
    collection.get.return_value = {
        "ids": ["1"],
        "metadatas": [{"hash": _hash(text)}],
    }

    with patch("jobs.ingest.embed", return_value=[0.1] * 768):
        _ingest_products(collection, [row])

    collection.upsert.assert_not_called()


def test_ingest_products_upserts_changed() -> None:
    row: Row = (1, "Laptop", "Pro", 1000.0, None, None, "Dell", "Electronics", None, [], "dell")

    collection = MagicMock()
    collection.get.return_value = {"ids": [], "metadatas": []}

    with patch("jobs.ingest.embed", return_value=[0.1] * 768):
        _ingest_products(collection, [row])

    collection.upsert.assert_called_once()


def test_ingest_products_deletes_stale() -> None:
    collection = MagicMock()
    collection.get.return_value = {
        "ids": ["999"],
        "metadatas": [{"hash": "oldhash"}],
    }

    with patch("jobs.ingest.embed", return_value=[0.1] * 768):
        _ingest_products(collection, [])

    collection.delete.assert_called_once_with(ids=["999"])


def test_ingest_documents_skips_missing_pdf_dir() -> None:
    with patch("jobs.ingest._PDF_DIR") as mock_dir:
        mock_dir.exists.return_value = False
        _ingest_documents()


def test_ingest_skips_when_no_products() -> None:
    with patch("jobs.ingest.product_repository.fetch_available_products", return_value=[]):
        with patch("jobs.ingest.get_collection") as mock_col:
            ingest()
            mock_col.assert_not_called()


def test_ingest_catalog_summary_upserts() -> None:
    collection = MagicMock()
    catalog = {
        "brands": ["Dell"],
        "categories": ["Electronics"],
        "types": ["Laptop"],
        "subtypes": [],
    }

    with (
        patch("jobs.ingest.product_repository.fetch_catalog_summary", return_value=catalog),
        patch("jobs.ingest.embed", return_value=[0.1] * 768),
    ):
        _ingest_catalog_summary(collection)

    collection.upsert.assert_called_once()


def test_ingest_catalog_summary_skips_empty() -> None:
    collection = MagicMock()
    empty_catalog: dict[str, list[str]] = {
        "brands": [],
        "categories": [],
        "types": [],
        "subtypes": [],
    }
    with patch("jobs.ingest.product_repository.fetch_catalog_summary", return_value=empty_catalog):
        _ingest_catalog_summary(collection)

    collection.upsert.assert_not_called()


def test_ingest_reviews_upserts() -> None:
    collection = MagicMock()
    collection.get.return_value = {"ids": [], "metadatas": []}
    rows = [(1, "Laptop", "Dell", "dell-laptop", 5, 4.5, "Great product")]

    with patch("jobs.ingest.fetch_reviews", return_value=rows):
        with patch("jobs.ingest.embed", return_value=[0.1] * 768):
            _ingest_reviews(collection)

    collection.upsert.assert_called_once()


def test_ingest_reviews_skips_empty() -> None:
    collection = MagicMock()

    with patch("jobs.ingest.fetch_reviews", return_value=[]):
        _ingest_reviews(collection)

    collection.upsert.assert_not_called()


def test_ingest_reviews_deletes_stale() -> None:
    collection = MagicMock()
    collection.get.return_value = {"ids": ["review_99"], "metadatas": [{"hash": "old"}]}

    with patch("jobs.ingest.fetch_reviews", return_value=[]):
        _ingest_reviews(collection)

    collection.delete.assert_called_once_with(ids=["review_99"])


def test_ingest_out_of_stock_upserts() -> None:
    collection = MagicMock()
    collection.get.return_value = {"ids": [], "metadatas": []}
    rows = [(1, "Laptop", "Pro", "Dell", "Electronics", "dell-laptop")]

    with (
        patch("jobs.ingest.product_repository.fetch_out_of_stock_products", return_value=rows),
        patch("jobs.ingest.embed", return_value=[0.1] * 768),
    ):
        _ingest_out_of_stock(collection)

    collection.upsert.assert_called_once()


def test_ingest_out_of_stock_deletes_stale() -> None:
    collection = MagicMock()
    collection.get.return_value = {"ids": ["oos_99"], "metadatas": [{"hash": "old"}]}

    with patch("jobs.ingest.product_repository.fetch_out_of_stock_products", return_value=[]):
        _ingest_out_of_stock(collection)

    collection.delete.assert_called_once_with(ids=["oos_99"])


def test_ingest_top_reviewed_upserts() -> None:
    collection = MagicMock()
    rows = [
        (
            1,
            "Laptop",
            "Pro",
            1000.0,
            None,
            "Dell",
            "Electronics",
            3,
            2,
            4.5,
            "dell",
            "Great laptop",
            None,
            5,
        ),
    ]

    with (
        patch("jobs.ingest.product_repository.fetch_top_reviewed_products", return_value=rows),
        patch("jobs.ingest.embed", return_value=[0.1] * 768),
    ):
        _ingest_top_reviewed(collection)

    collection.upsert.assert_called_once()


def test_ingest_top_reviewed_skips_empty() -> None:
    collection = MagicMock()

    with patch("jobs.ingest.product_repository.fetch_top_reviewed_products", return_value=[]):
        _ingest_top_reviewed(collection)

    collection.upsert.assert_not_called()


def test_ingest_top_ordered_upserts() -> None:
    collection = MagicMock()
    rows = [(1, "Laptop", "Pro", 1000.0, None, "Dell", "Electronics", 10, "dell", None)]

    with (
        patch("jobs.ingest.product_repository.fetch_top_ordered_products", return_value=rows),
        patch("jobs.ingest.embed", return_value=[0.1] * 768),
    ):
        _ingest_top_ordered(collection)

    collection.upsert.assert_called_once()


def test_ingest_top_ordered_skips_empty() -> None:
    collection = MagicMock()

    with patch("jobs.ingest.product_repository.fetch_top_ordered_products", return_value=[]):
        _ingest_top_ordered(collection)

    collection.upsert.assert_not_called()


def test_ingest_documents_skips_no_pdfs() -> None:
    with patch("jobs.ingest._PDF_DIR") as mock_dir:
        mock_dir.exists.return_value = True
        mock_dir.glob.return_value = []
        _ingest_documents()


def test_ingest_documents_embeds_pdf_pages() -> None:
    mock_page = MagicMock()
    mock_page.extract_text.return_value = "Some text content"

    mock_reader = MagicMock()
    mock_reader.pages = [mock_page]

    mock_pdf = MagicMock(spec=Path)
    mock_pdf.stem = "privacy-policy"

    mock_collection = MagicMock()

    with (
        patch("jobs.ingest._PDF_DIR") as mock_dir,
        patch("jobs.ingest.get_documents_collection", return_value=mock_collection),
        patch("jobs.ingest.PdfReader", return_value=mock_reader),
        patch("jobs.ingest.embed", return_value=[0.1] * 768),
    ):
        mock_dir.exists.return_value = True
        mock_dir.glob.return_value = [mock_pdf]
        _ingest_documents()

    mock_collection.upsert.assert_called_once()


def test_ingest_full_run() -> None:
    row: Row = (1, "Laptop", "Pro", 1000.0, None, None, "Dell", "Electronics", None, [], "dell")
    collection = MagicMock()
    collection.get.return_value = {"ids": [], "metadatas": []}

    with (
        patch("jobs.ingest.product_repository.fetch_available_products", return_value=[row]),
        patch("jobs.ingest.product_repository.fetch_out_of_stock_products", return_value=[]),
        patch(
            "jobs.ingest.product_repository.fetch_catalog_summary",
            return_value={
                "brands": [],
                "categories": [],
                "types": [],
                "subtypes": [],
            },
        ),
        patch("jobs.ingest.product_repository.fetch_top_reviewed_products", return_value=[]),
        patch("jobs.ingest.product_repository.fetch_top_ordered_products", return_value=[]),
        patch("jobs.ingest.fetch_reviews", return_value=[]),
        patch("jobs.ingest.get_collection", return_value=collection),
        patch("jobs.ingest._ingest_documents"),
        patch("jobs.ingest.embed", return_value=[0.1] * 768),
    ):
        ingest()

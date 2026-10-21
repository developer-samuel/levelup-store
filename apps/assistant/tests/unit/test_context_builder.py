import datetime
from unittest.mock import patch

from app.context_builder import (
    format_catalog_summary,
    format_price,
    format_rating,
    format_top_ordered,
    format_top_reviewed,
    format_user_cart,
    format_user_name,
    format_user_order_profile,
    format_user_orders,
    format_user_reviews,
    format_user_wishlist,
)


def test_format_price_integer() -> None:
    assert format_price(10.0) == "10"


def test_format_price_decimal() -> None:
    assert format_price(10.5) == "10.50"


def test_format_price_two_decimals() -> None:
    assert format_price(9.99) == "9.99"


def test_format_rating_integer() -> None:
    assert format_rating(4.0) == "4"


def test_format_rating_decimal() -> None:
    assert format_rating(4.5) == "4.5"


def _make_reviewed_row(
    feedback_count: int | None,
    rating_count: int | None,
    avg_rating: float = 4.5,
) -> tuple[object, ...]:
    return (
        1,  # id
        "Product",  # product_name
        "Variant",  # variant_name
        100.0,  # price
        None,  # discounted_price
        "Brand",  # brand
        "Category",  # category
        feedback_count,
        rating_count,
        avg_rating,
        "slug",  # url
        None,  # description
        None,  # image_path
        5,  # stock_level
    )


def test_reviewed_both_counts() -> None:
    result = format_top_reviewed([_make_reviewed_row(2, 3)])
    assert "2 reviews, 3 ratings" in result


def test_reviewed_only_feedback() -> None:
    result = format_top_reviewed([_make_reviewed_row(2, 0)])
    assert "2 reviews" in result
    assert "ratings" not in result


def test_reviewed_only_ratings() -> None:
    result = format_top_reviewed([_make_reviewed_row(0, 3)])
    assert "3 ratings" in result
    assert "reviews," not in result


def test_reviewed_no_ratings() -> None:
    result = format_top_reviewed([_make_reviewed_row(0, 0)])
    assert "No reviews yet" in result


def test_reviewed_empty_rows() -> None:
    assert format_top_reviewed([]) == ""


def _make_ordered_row() -> tuple[object, ...]:
    return (
        1,  # id
        "Product",  # product_name
        "Variant",  # variant_name
        100.0,  # price
        None,  # discounted_price
        "Brand",  # brand
        "Category",  # category
        10,  # order_count
        "slug",  # url
        None,  # description
        None,  # image_path
        5,  # stock_level
    )


def test_ordered_empty_rows() -> None:
    assert format_top_ordered([]) == ""


def test_ordered_contains_variant_name() -> None:
    result = format_top_ordered([_make_ordered_row()])
    assert "Variant" in result


def test_ordered_no_order_count_shown() -> None:
    result = format_top_ordered([_make_ordered_row()])
    assert "Orders" not in result
    assert "order_count" not in result


def test_reviewed_image_shown_when_present() -> None:
    row = list(_make_reviewed_row(2, 3))
    row[12] = "img.jpg"  # image_path
    result = format_top_reviewed([tuple(row)])
    assert "img.jpg" in result


def test_ordered_image_shown_when_present() -> None:
    row = list(_make_ordered_row())
    row[10] = "img.jpg"  # image_path
    result = format_top_ordered([tuple(row)])

    assert "img.jpg" in result


def test_image_url_without_minio() -> None:
    from app.context_builder import image_url

    with patch("app.context_builder.settings") as s:
        s.minio_enabled = False
        s.ecommerce_url = "http://shop.test"
        result = image_url("photo.jpg")

    assert result == "http://shop.test/uploads/photo.jpg"


def test_image_url_with_minio() -> None:
    from app.context_builder import image_url

    with patch("app.context_builder.settings") as s:
        s.minio_enabled = True
        s.minio_public_url = "http://minio.test"
        s.minio_bucket = "store"
        result = image_url("photo.jpg")

    assert result == "http://minio.test/store/uploads/photo.jpg"


# --- format_user_orders ---


def _make_order_row(price: float = 50.0, status: str = "paid") -> tuple[object, ...]:
    return ("ORD-001", price, status, datetime.date(2024, 6, 15))


def test_user_orders_empty() -> None:
    assert format_user_orders([]) == ""


def test_user_orders_contains_code() -> None:
    result = format_user_orders([_make_order_row()])
    assert "ORD-001" in result


def test_user_orders_contains_price() -> None:
    result = format_user_orders([_make_order_row(price=99.99)])
    assert "99.99" in result


def test_user_orders_contains_status() -> None:
    result = format_user_orders([_make_order_row(status="shipped")])
    assert "shipped" in result


def test_user_orders_date_formatted() -> None:
    result = format_user_orders([_make_order_row()])
    assert "15.06.2024" in result


def test_user_orders_date_fallback_string() -> None:
    result = format_user_orders([("ORD-002", 10.0, "paid", "2024-06-15")])
    assert "2024-06-15" in result


# --- format_user_cart ---


def _make_cart_row(
    price: float = 100.0,
    discounted_price: float | None = None,
    image_path: str | None = None,
) -> tuple[object, ...]:
    return (
        1,  # id
        "Product Name",  # product_name
        "Variant Name",  # variant_name
        "Brand",  # brand
        price,  # price
        discounted_price,  # discounted_price
        "slug",  # url
        image_path,  # image_path
        5,  # stock_level
    )


def test_user_cart_empty() -> None:
    assert format_user_cart([]) == ""


def test_user_cart_contains_variant() -> None:
    result = format_user_cart([_make_cart_row()])
    assert "Variant Name" in result


def test_user_cart_total_calculated() -> None:
    result = format_user_cart([_make_cart_row(price=50.0), _make_cart_row(price=30.0)])
    assert "80" in result


def test_user_cart_discounted_price_shown() -> None:
    result = format_user_cart([_make_cart_row(price=100.0, discounted_price=80.0)])

    assert "80" in result
    assert "~~100" in result


def test_user_cart_no_discount() -> None:
    result = format_user_cart([_make_cart_row(price=100.0)])
    assert "~~" not in result


def test_user_cart_image_shown_when_present() -> None:
    result = format_user_cart([_make_cart_row(image_path="img.jpg")])
    assert "img.jpg" in result


def test_user_cart_no_image_when_absent() -> None:
    result = format_user_cart([_make_cart_row(image_path=None)])
    assert "![" not in result


# --- format_user_reviews ---


def _make_review_row(review_type: str = "feedback") -> tuple[object, ...]:
    return (
        "Product",
        "Variant",
        "Brand",
        4.5,
        "Great product",
        review_type,
        datetime.date(2024, 3, 10),
        "slug",
    )


def test_user_reviews_empty() -> None:
    assert format_user_reviews([]) == ""


def test_user_reviews_feedback_label() -> None:
    result = format_user_reviews([_make_review_row("feedback")])
    assert "1 review" in result


def test_user_reviews_rating_label() -> None:
    result = format_user_reviews([_make_review_row("rating")])
    assert "1 rating" in result


def test_user_reviews_contains_variant() -> None:
    result = format_user_reviews([_make_review_row()])
    assert "Variant" in result


def test_user_reviews_body_truncated() -> None:
    long_body = "x" * 200
    row = ("P", "V", "B", 4.0, long_body, "feedback", datetime.date(2024, 1, 1), "slug")
    result = format_user_reviews([row])

    assert "x" * 100 in result
    assert "x" * 101 not in result


def test_user_reviews_no_body() -> None:
    row = ("P", "V", "B", 4.0, None, "feedback", datetime.date(2024, 1, 1), "slug")
    result = format_user_reviews([row])

    assert "Variant" not in result or "None" not in result


# --- format_user_wishlist ---


def _make_wishlist_row(
    feedback_count: int = 0,
    rating_count: int = 0,
    discounted_price: float | None = None,
    image_path: str | None = None,
) -> tuple[object, ...]:
    return (
        "Product",
        "Variant",
        "Brand",
        100.0,
        discounted_price,
        "slug",
        image_path,
        feedback_count,
        rating_count,
        4.5,
        5,
    )


def test_user_wishlist_empty() -> None:
    assert format_user_wishlist([]) == ""


def test_user_wishlist_contains_variant() -> None:
    result = format_user_wishlist([_make_wishlist_row()])
    assert "Variant" in result


def test_user_wishlist_rating_both() -> None:
    result = format_user_wishlist([_make_wishlist_row(feedback_count=2, rating_count=3)])
    assert "2 reviews, 3 ratings" in result


def test_user_wishlist_rating_only_feedback() -> None:
    result = format_user_wishlist([_make_wishlist_row(feedback_count=1, rating_count=0)])
    assert "1 reviews" in result


def test_user_wishlist_rating_only_ratings() -> None:
    result = format_user_wishlist([_make_wishlist_row(feedback_count=0, rating_count=2)])
    assert "2 ratings" in result


def test_user_wishlist_no_rating_line() -> None:
    result = format_user_wishlist([_make_wishlist_row(feedback_count=0, rating_count=0)])
    assert "Rating:" not in result


def test_user_wishlist_sale_price_shown() -> None:
    result = format_user_wishlist([_make_wishlist_row(discounted_price=80.0)])
    assert "Sale price" in result


def test_user_wishlist_image_shown() -> None:
    result = format_user_wishlist([_make_wishlist_row(image_path="img.jpg")])
    assert "img.jpg" in result


# --- format_user_name ---


def test_user_name_returns_first() -> None:
    assert format_user_name(("John", "Doe")) == "Customer name: John"


def test_user_name_empty_first() -> None:
    assert format_user_name(("", "Doe")) == ""


def test_user_name_none() -> None:
    assert format_user_name(None) == ""


# --- format_user_order_profile ---


def test_order_profile_empty() -> None:
    assert format_user_order_profile([]) == ""


def test_order_profile_contains_brand() -> None:
    result = format_user_order_profile([("Nike", "Shoes", "Running")])
    assert "Nike" in result


def test_order_profile_deduplicates() -> None:
    rows = [("Nike", "Shoes", "Running"), ("Nike", "Bags", "Training")]
    result = format_user_order_profile(rows)

    assert result.count("Nike") == 1


def test_order_profile_skips_none_values() -> None:
    result = format_user_order_profile([(None, "Shoes", None)])
    assert "None" not in result


# --- format_catalog_summary ---


def test_catalog_summary_empty() -> None:
    empty: dict[str, list[str]] = {"brands": [], "categories": [], "types": [], "subtypes": []}
    assert format_catalog_summary(empty) == ""


def test_catalog_summary_brands() -> None:
    result = format_catalog_summary(
        {"brands": ["Nike"], "categories": [], "types": [], "subtypes": []}
    )
    assert "Nike" in result


def test_catalog_summary_all_fields() -> None:
    result = format_catalog_summary(
        {
            "brands": ["Nike"],
            "categories": ["Shoes"],
            "types": ["Running"],
            "subtypes": ["Trail"],
        }
    )

    assert "Nike" in result
    assert "Shoes" in result
    assert "Running" in result
    assert "Trail" in result

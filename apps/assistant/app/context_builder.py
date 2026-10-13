from typing import cast

from app.config import settings


def format_price(price: float) -> str:
    return str(int(price)) if price == int(price) else f"{price:.2f}"


def product_url(url: object) -> str:
    return f"{settings.ecommerce_url}/product/show/{url}" if url else ""


def image_url(path: object) -> str:
    if not path:
        return ""

    if settings.minio_enabled:
        return f"{settings.minio_public_url}/{settings.minio_bucket}/uploads/{path}"

    return f"{settings.ecommerce_url}/uploads/{path}"


def format_top_reviewed(rows: list[tuple[object, ...]]) -> str:
    if not rows:
        return ""

    blocks = ["Top rated products (by customer reviews):"]

    for row in rows:
        (
            _,
            product_name,
            variant_name,
            price,
            discounted_price,
            brand,
            category,
            feedback_count,
            rating_count,
            avg_rating,
            url,
            description,
            image_path,
            stock_level,
        ) = row

        link = product_url(url)
        image = image_url(image_path)

        if feedback_count and rating_count:
            rating_label = f"{avg_rating}★ ({feedback_count} reviews, {rating_count} ratings)"
        elif feedback_count:
            rating_label = f"{avg_rating}★ ({feedback_count} reviews)"
        elif rating_count:
            rating_label = f"{avg_rating}★ ({rating_count} ratings)"
        else:
            rating_label = "No reviews yet"

        block = (
            f"\nProduct: {variant_name}"
            f"\nBrand: {brand}"
            f"\nCategory: {category}"
            f"\nOriginal price: {format_price(cast(float, price))} EUR"
            + (
                f"\nSale price: {format_price(cast(float, discounted_price))} EUR"
                if discounted_price
                else ""
            )
            + f"\nRating: {rating_label}"
            + (f"\nDescription: {description}" if description else "")
            + f"\nStock: {stock_level}"
            f"\nURL: {link}"
        )

        if image:
            block += f"\nImage: {image}"

        blocks.append(block)

    return "\n".join(blocks)


def format_top_ordered(rows: list[tuple[object, ...]]) -> str:
    if not rows:
        return ""

    blocks = ["Most purchased products:"]

    for row in rows:
        (
            _,
            product_name,
            variant_name,
            price,
            discounted_price,
            brand,
            category,
            order_count,
            url,
            description,
            image_path,
            stock_level,
        ) = row

        link = product_url(url)
        image = image_url(image_path)

        block = (
            f"\nProduct: {variant_name}"
            f"\nBrand: {brand}"
            f"\nCategory: {category}"
            f"\nOriginal price: {format_price(cast(float, price))} EUR"
            + (
                f"\nSale price: {format_price(cast(float, discounted_price))} EUR"
                if discounted_price
                else ""
            )
            + f"\nOrders: {order_count}"
            + (f"\nDescription: {description}" if description else "")
            + f"\nStock: {stock_level}"
            f"\nURL: {link}"
        )

        if image:
            block += f"\nImage: {image}"

        blocks.append(block)

    return "\n".join(blocks)


def format_user_orders(rows: list[tuple[object, ...]]) -> str:
    if not rows:
        return ""

    lines = ["Customer orders:"]

    for row in rows:
        code, price, status, created_at = row
        date = (
            created_at.strftime("%d.%m.%Y") if hasattr(created_at, "strftime") else str(created_at)
        )
        url = f"{settings.ecommerce_url}/orders/show/{code}"
        lines.append(
            f"- Order [{code}]({url}) | {date}"
            f" | {format_price(cast(float, price))} EUR | Status: {status}"
        )

    return "\n".join(lines)


def format_user_cart(rows: list[tuple[object, ...]]) -> str:
    if not rows:
        return ""

    lines = ["Cart:"]
    total = 0.0

    for row in rows:
        product_name = row[1]
        variant_name = row[2]
        price = row[4]
        discounted_price = row[5]
        url = row[6]
        image_path = row[7]
        stock_level = row[8]
        price_f = float(cast(float, price))
        disc_f = float(cast(float, discounted_price)) if discounted_price else None
        price_str = (
            f"~~{format_price(price_f)} EUR~~\n{format_price(disc_f)} EUR"
            if disc_f is not None
            else f"{format_price(price_f)} EUR"
        )
        display_price = disc_f if disc_f is not None else price_f
        link = product_url(url)
        img = image_url(image_path)

        line = f"- {variant_name} | {price_str} | Stock: {stock_level} | [Show product]({link})"

        if img:
            line += f" ![{product_name}]({img})"

        lines.append(line)
        total += display_price

    lines.append(f"Total: {format_price(total)} EUR")

    return "\n".join(lines)


def format_user_reviews(rows: list[tuple[object, ...]]) -> str:
    if not rows:
        return ""

    lines = ["My reviews:"]

    for row in rows:
        product_name, variant_name, brand, rating, body, review_type, created_at, url = row
        date = created_at.strftime("%d.%m.%Y") if hasattr(created_at, "strftime") else ""
        link = product_url(url)
        comment_short = str(body)[:100] if body else ""

        rating_label = (
            f"{rating}★ (1 review)" if review_type == "feedback" else f"{rating}★ (1 rating)"
        )

        lines.append(
            f"- {variant_name}"
            f" | {rating_label} | {review_type} | {date} | {comment_short} | [Show]({link})"
        )

    return "\n".join(lines)


def format_user_wishlist(rows: list[tuple[object, ...]]) -> str:
    if not rows:
        return ""

    lines = ["Wishlist:"]

    for row in rows:
        (
            product_name,
            variant_name,
            brand,
            price,
            discounted_price,
            url,
            image_path,
            feedback_count,
            rating_count,
            avg_rating,
            stock_level,
        ) = row

        price_f = float(cast(float, price))
        disc_f = float(cast(float, discounted_price)) if discounted_price else None
        link = product_url(url)
        img = image_url(image_path)

        if feedback_count and rating_count:
            rating_label = f"{avg_rating}★ ({feedback_count} reviews, {rating_count} ratings)"
        elif feedback_count:
            rating_label = f"{avg_rating}★ ({feedback_count} reviews)"
        elif rating_count:
            rating_label = f"{avg_rating}★ ({rating_count} ratings)"
        else:
            rating_label = ""

        line = f"\nProduct: {variant_name}"
        line += f"\nOriginal price: {format_price(price_f)} EUR"

        if disc_f is not None:
            line += f"\nSale price: {format_price(disc_f)} EUR"
        if rating_label:
            line += f"\nRating: {rating_label}"

        line += f"\nStock: {stock_level}"
        line += f"\nURL: {link}"

        if img:
            line += f"\nImage: {img}"

        lines.append(line)

    return "\n".join(lines)


def format_user_name(name: tuple[str, str] | None) -> str:
    if not name:
        return ""

    first = name[0].strip()

    return f"Customer name: {first}" if first else ""


def format_catalog_summary(catalog: dict[str, list[str]]) -> str:
    parts = []

    if catalog["brands"]:
        parts.append(f"Brands we carry: {', '.join(catalog['brands'])}")
    if catalog["categories"]:
        parts.append(f"Product categories: {', '.join(catalog['categories'])}")
    if catalog["types"]:
        parts.append(f"Product types: {', '.join(catalog['types'])}")
    if catalog["subtypes"]:
        parts.append(f"Product subtypes: {', '.join(catalog['subtypes'])}")

    return "\n".join(parts)

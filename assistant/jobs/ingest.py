"""
Ingest products from PostgreSQL into ChromaDB.

Usage:
    python -m jobs.ingest
"""

import sys
from pathlib import Path

sys.path.append(str(Path(__file__).parent.parent))

from typing import cast

import numpy as np
from chromadb.api.types import Embeddings

from app.rag import embed, get_collection
from app.repositories import product_repository


def _build_document(row: tuple[object, ...]) -> tuple[str, str] | None:
    id_, product_name, variant_name, sku, price, discounted_price, description, brand, category, type_, subtypes = row

    if not product_name or not variant_name or not brand or not category:
        return None

    if not price or price <= 0:
        return None

    if discounted_price is not None and discounted_price > 0 and discounted_price < price:
        price_text = f"Price: {discounted_price} EUR (on sale, original price: {price} EUR)"
    else:
        price_text = f"Price: {price} EUR"

    sku_text = f"SKU: {sku}. " if sku else ""
    type_text = f"Type: {type_}. " if type_ else ""
    valid_subtypes = [s for s in (subtypes or []) if s is not None]
    subtypes_text = f"Subtypes: {', '.join(valid_subtypes)}. " if valid_subtypes else ""

    text = (
        f"{brand} {product_name} - {variant_name}. "
        f"Category: {category}. {type_text}{subtypes_text}{sku_text}{price_text}."
    )

    if description:
        text += f" {description}"

    return str(id_), text


def _ingest_products(collection, rows: list[tuple[object, ...]]) -> int:
    """Embed and upsert all valid product variants. Returns the number ingested."""
    ids: list[str] = []
    documents: list[str] = []
    embeddings: list[np.ndarray[tuple[int, ...], np.dtype[np.float32]]] = []

    for row in rows:
        result = _build_document(row)

        if result is None:
            continue

        doc_id, text = result

        ids.append(doc_id)
        documents.append(text)
        embeddings.append(np.array(embed(text), dtype=np.float32))

        print(f"Embedded: {text[:80]}...")

    collection.upsert(ids=ids, documents=documents, embeddings=cast(Embeddings, embeddings))

    print(f"\nIngested {len(ids)} products into ChromaDB.")

    return len(ids)


def _ingest_catalog_summary(collection) -> None:
    """Build a catalog summary document and upsert it under a fixed ID.

    The text is intentionally keyword-rich so it surfaces for queries like
    'what brands do you have', 'what categories', 'what do you sell', etc.
    """

    catalog = product_repository.fetch_catalog_summary()

    if not catalog["brands"] and not catalog["categories"]:
        return

    parts: list[str] = []

    if catalog["brands"]:
        parts.append(f"Brands we carry: {', '.join(catalog['brands'])}")
    if catalog["categories"]:
        parts.append(f"Product categories: {', '.join(catalog['categories'])}")
    if catalog["types"]:
        parts.append(f"Product types: {', '.join(catalog['types'])}")

    summary_text = "LevelUp Store catalog summary. " + ". ".join(parts) + "."
    summary_embedding = np.array(embed(summary_text), dtype=np.float32)

    collection.upsert(
        ids=["catalog_summary"],
        documents=[summary_text],
        embeddings=cast(Embeddings, [summary_embedding]),
    )

    print(f"Ingested catalog summary: {summary_text[:120]}...")


def _ingest_top_reviewed(collection) -> None:
    """Build a top-reviewed products document and upsert it under a fixed ID."""

    rows = product_repository.fetch_top_reviewed_products()

    if not rows:
        return

    lines: list[str] = []

    for row in rows:
        _, product_name, variant_name, price, discounted_price, brand, category, review_count, avg_rating = row
        if not product_name or not brand:
            continue

        if discounted_price is not None and discounted_price > 0 and discounted_price < price:
            price_text = f"{discounted_price} EUR (on sale)"
        else:
            price_text = f"{price} EUR"

        lines.append(
            f"{brand} {product_name} - {variant_name}: {price_text}, "
            f"rated {avg_rating}/5 by {review_count} customers"
        )

    if not lines:
        return

    text = "Top rated and most reviewed products at LevelUp Store. " + ". ".join(lines) + "."
    embedding = np.array(embed(text), dtype=np.float32)

    collection.upsert(
        ids=["top_reviewed"],
        documents=[text],
        embeddings=cast(Embeddings, [embedding]),
    )

    print(f"Ingested top reviewed: {len(lines)} products.")


def ingest() -> None:
    rows = product_repository.fetch_available_products()

    if not rows:
        print("No products found.")
        return

    collection = get_collection()

    _ingest_products(collection, rows)
    _ingest_catalog_summary(collection)
    _ingest_top_reviewed(collection)

    print("")
    print("╔═════════════════════════════════════════════════╗")
    print("║           LEVELUP STORE - SETUP DONE            ║")
    print("╚═════════════════════════════════════════════════╝")
    print("🛒  E-commerce: LevelUp Store is ready")
    print("🤖  AI Assistant is ready")
    print("")


if __name__ == "__main__":
    ingest()

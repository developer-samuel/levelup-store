"""
Ingest products from PostgreSQL into ChromaDB.

Usage:
    python -m jobs.ingest
"""

import hashlib
import sys
from pathlib import Path

sys.path.append(str(Path(__file__).parent.parent))

from typing import cast

import numpy as np
from chromadb.api.types import Embeddings, Metadatas

from pypdf import PdfReader

from app.services.rag_service import embed, get_collection, get_documents_collection
from app.repositories import product_repository
from app.repositories.review_repository import fetch_reviews

_PDF_DIR = Path(__file__).parent.parent.parent / "ecommerce" / "public" / "pdf"


def _hash(text: str) -> str:
    return hashlib.md5(text.encode()).hexdigest()


def _get_existing_hashes(collection, id_prefix: str = "") -> dict[str, str]:
    results = collection.get(include=["metadatas"])
    return {
        id_: str((meta or {}).get("hash", ""))
        for id_, meta in zip(results["ids"], results["metadatas"] or [])
        if id_.startswith(id_prefix)
    }


def _build_document(row: tuple[object, ...]) -> tuple[str, str] | None:
    id_, product_name, variant_name, price, discounted_price, description, brand, category, type_, subtypes, url = row

    if not product_name or not variant_name or not brand or not category:
        return None

    if not price or price <= 0:
        return None

    if discounted_price is not None and discounted_price > 0 and discounted_price < price:
        price_text = f"Price: {discounted_price} EUR (on sale, original price: {price} EUR)"
    else:
        price_text = f"Price: {price} EUR"

    type_text = f"Type: {type_}. " if type_ else ""
    valid_subtypes = [s for s in (subtypes or []) if s is not None]
    subtypes_text = f"Subtypes: {', '.join(valid_subtypes)}. " if valid_subtypes else ""
    url_text = f" URL: /product/show/{url}" if url else ""

    text = (
        f"{brand} {product_name} - {variant_name}. "
        f"Category: {category}. {type_text}{subtypes_text}{price_text}."
    )

    if description:
        text += f" {description}"

    text += url_text

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
        _, product_name, variant_name, price, discounted_price, brand, category, feedback_count, rating_count, avg_rating, url, _image = row
        if not product_name or not brand:
            continue

        if discounted_price is not None and discounted_price > 0 and discounted_price < price:
            price_text = f"{discounted_price} EUR (on sale)"
        else:
            price_text = f"{price} EUR"

        url_text = f" URL: /product/show/{url}" if url else ""
        count_label = f"{feedback_count} reviews" if feedback_count else f"{rating_count} ratings"
        lines.append(
            f"{brand} {product_name} - {variant_name}: {price_text}, "
            f"rated {avg_rating}/5 by {count_label}.{url_text}"
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


def _ingest_top_ordered(collection) -> None:
    """Build a top-ordered products document and upsert it under a fixed ID."""

    rows = product_repository.fetch_top_ordered_products()

    if not rows:
        return

    lines: list[str] = []

    for row in rows:
        _, product_name, variant_name, price, discounted_price, brand, category, order_count, url, _image = row
        if not product_name or not brand:
            continue

        if discounted_price is not None and discounted_price > 0 and discounted_price < price:
            price_text = f"{discounted_price} EUR (on sale)"
        else:
            price_text = f"{price} EUR"

        url_text = f" URL: /product/show/{url}" if url else ""
        lines.append(
            f"{brand} {product_name} - {variant_name}: {price_text}, "
            f"ordered {order_count} times.{url_text}"
        )

    if not lines:
        return

    text = "Most purchased products at LevelUp Store. " + ". ".join(lines) + "."
    embedding = np.array(embed(text), dtype=np.float32)

    collection.upsert(
        ids=["top_ordered"],
        documents=[text],
        embeddings=cast(Embeddings, [embedding]),
    )

    print(f"Ingested top ordered: {len(lines)} products.")


def _ingest_out_of_stock(collection) -> None:
    rows = product_repository.fetch_out_of_stock_products()

    if not rows:
        return

    ids: list[str] = []
    documents: list[str] = []
    embeddings_list = []

    for row in rows:
        id_, product_name, variant_name, brand, category, url = row
        if not product_name or not brand:
            continue

        url_text = f" URL: /product/detail/{url}" if url else ""
        text = (
            f"{brand} {product_name} - {variant_name}. "
            f"Category: {category}. Status: out of stock.{url_text}"
        )

        ids.append(f"oos_{id_}")
        documents.append(text)
        embeddings_list.append(np.array(embed(text), dtype=np.float32))

        print(f"Embedded (OOS): {text[:80]}...")

    if ids:
        collection.upsert(ids=ids, documents=documents, embeddings=cast(Embeddings, embeddings_list))
        print(f"Ingested {len(ids)} out-of-stock products.")


def _ingest_documents() -> None:
    """Chunk and embed PDF documents (privacy policy, terms) into the documents collection."""
    if not _PDF_DIR.exists():
        print("PDF directory not found, skipping document ingestion.")
        return

    collection = get_documents_collection()
    pdfs = list(_PDF_DIR.glob("*.pdf"))

    if not pdfs:
        print("No PDF files found, skipping document ingestion.")
        return

    ids: list[str] = []
    documents: list[str] = []
    metadatas: Metadatas = []
    embeddings_list = []

    for pdf_path in pdfs:
        reader = PdfReader(str(pdf_path))
        source = pdf_path.stem  # e.g. "privacy-policy"

        for page_num, page in enumerate(reader.pages):
            text = page.extract_text()
            if not text or not text.strip():
                continue

            chunk = text.strip()
            chunk_id = f"{source}_p{page_num}"

            ids.append(chunk_id)
            documents.append(chunk)
            metadatas.append({"source": source, "page": page_num})
            embeddings_list.append(np.array(embed(chunk), dtype=np.float32))

            print(f"Embedded [{source} p.{page_num}]: {chunk[:60]}...")

    if ids:
        collection.upsert(
            ids=ids,
            documents=documents,
            metadatas=metadatas,
            embeddings=cast(Embeddings, embeddings_list),
        )
        print(f"Ingested {len(ids)} document chunks from {len(pdfs)} PDF(s).")


def _ingest_reviews(collection) -> None:
    rows = fetch_reviews()

    if not rows:
        print("No reviews found.")
        return

    existing = _get_existing_hashes(collection, id_prefix="review_")

    ids: list[str] = []
    documents: list[str] = []
    embeddings_list: list[np.ndarray] = []
    metadatas: list[dict] = []
    current_ids: set[str] = set()

    for row in rows:
        id_, product_name, variant_name, brand, body, rating, _, url = row
        if not body or not product_name or not brand:
            continue

        url_text = f" URL: /product/show/{url}" if url else ""
        text = (
            f"{brand} {product_name} - {variant_name}. "
            f"Customer review ({rating}/5): {body}.{url_text}"
        )

        doc_id = f"review_{id_}"
        current_ids.add(doc_id)
        content_hash = _hash(text)

        if existing.get(doc_id) == content_hash:
            continue

        ids.append(doc_id)
        documents.append(text)
        embeddings_list.append(np.array(embed(text), dtype=np.float32))
        metadatas.append({"hash": content_hash})

        print(f"Embedded (review): {text[:80]}...")

    if ids:
        collection.upsert(
            ids=ids,
            documents=documents,
            embeddings=cast(Embeddings, embeddings_list),
            metadatas=cast(Metadatas, metadatas),
        )

    stale = set(existing.keys()) - current_ids
    if stale:
        collection.delete(ids=list(stale))
        print(f"Deleted {len(stale)} stale review(s).")

    print(f"Reviews: {len(ids)} upserted, {len(stale)} deleted, {len(current_ids) - len(ids)} unchanged.")


def ingest() -> None:
    rows = product_repository.fetch_available_products()

    if not rows:
        print("No products found.")
        return

    collection = get_collection()

    _ingest_products(collection, rows)
    _ingest_out_of_stock(collection)
    _ingest_catalog_summary(collection)
    _ingest_top_reviewed(collection)
    _ingest_top_ordered(collection)
    _ingest_reviews(collection)
    _ingest_documents()

    print("")
    print("╔═════════════════════════════════════════════════╗")
    print("║           LEVELUP STORE - SETUP DONE            ║")
    print("╚═════════════════════════════════════════════════╝")
    print("🛒  E-commerce: LevelUp Store is ready")
    print("🤖  AI Assistant is ready")
    print("")


if __name__ == "__main__":
    ingest()

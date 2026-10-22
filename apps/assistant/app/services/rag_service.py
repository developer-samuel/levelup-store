import logging
from typing import cast

import chromadb
import chromadb.api
import httpx
import ollama
from chromadb.api.types import Metadata, PyEmbeddings

from app.config import CHROMA_COLLECTION_DOCUMENTS, CHROMA_COLLECTION_PRODUCTS, settings

_log = logging.getLogger(__name__)

# Module-level singletons created once, reused across requests
_chroma_client: chromadb.api.ClientAPI | None = None
_ollama_client: ollama.Client | None = None


def _get_chroma_client() -> chromadb.api.ClientAPI:
    global _chroma_client

    if _chroma_client is None:
        host = settings.chroma_host.replace("http://", "").replace("https://", "").split(":")[0]
        port = int(settings.chroma_host.split(":")[-1])
        _chroma_client = chromadb.HttpClient(host=host, port=port)

    return _chroma_client


def _get_ollama_client() -> ollama.Client:
    global _ollama_client

    if _ollama_client is None:
        _ollama_client = ollama.Client(host=settings.assistant_ollama_host)

    return _ollama_client


def get_collection() -> chromadb.Collection:
    return _get_chroma_client().get_or_create_collection(CHROMA_COLLECTION_PRODUCTS)


def get_documents_collection() -> chromadb.Collection:
    return _get_chroma_client().get_or_create_collection(CHROMA_COLLECTION_DOCUMENTS)


def reset_collection() -> chromadb.Collection:
    """Delete and recreate the ChromaDB collection to remove stale documents."""

    client = _get_chroma_client()
    client.delete_collection(CHROMA_COLLECTION_PRODUCTS)

    return client.get_or_create_collection(CHROMA_COLLECTION_PRODUCTS)


def _embed_via_api(text: str) -> list[float]:
    response = httpx.post(
        settings.assistant_embed_url,
        headers={"Authorization": f"Bearer {settings.assistant_api_key}"},
        json={"model": settings.assistant_embed_model, "input": text},
        timeout=30,
    )
    response.raise_for_status()
    return cast(list[float], response.json()["data"][0]["embedding"])


def embed(text: str) -> list[float]:
    if settings.assistant_type == "api":
        return _embed_via_api(text)

    response = _get_ollama_client().embeddings(
        model=settings.assistant_ollama_embed_model, prompt=text
    )

    return cast(list[float], response["embedding"])


def query(question: str, n_results: int = 5) -> str:
    try:
        collection = get_collection()
        embedding = embed(question)

        results = collection.query(
            query_embeddings=cast(PyEmbeddings, [embedding]),
            n_results=n_results,
            include=["documents"],
        )

        documents: list[str] = results["documents"][0] if results["documents"] else []

        return "\n".join(f"- {doc}" for doc in documents)
    except Exception:
        _log.exception("RAG product query failed")
        return ""


def query_documents(question: str, n_results: int = 3) -> str:
    try:
        collection = get_documents_collection()
        embedding = embed(question)

        results = collection.query(
            query_embeddings=cast(PyEmbeddings, [embedding]),
            n_results=n_results,
            include=["documents", "metadatas"],
        )

        documents: list[str] = results["documents"][0] if results["documents"] else []
        metadatas: list[Metadata] = results["metadatas"][0] if results["metadatas"] else []

        parts = []

        for doc, meta in zip(documents, metadatas):
            source = str(meta.get("source", ""))
            parts.append(f"[{source}]\n{doc}")

        return "\n\n".join(parts)
    except Exception:
        _log.exception("RAG documents query failed")
        return ""

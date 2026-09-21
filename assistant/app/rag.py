from typing import cast

import chromadb
import chromadb.api
import ollama
from chromadb.api.types import PyEmbeddings

from app.config import CHROMA_COLLECTION, settings

# Module-level singletons  created once, reused across requests
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
        _ollama_client = ollama.Client(host=settings.ollama_host)

    return _ollama_client


def get_collection() -> chromadb.Collection:
    return _get_chroma_client().get_or_create_collection(CHROMA_COLLECTION)


def reset_collection() -> chromadb.Collection:
    """Delete and recreate the ChromaDB collection to remove stale documents."""
    client = _get_chroma_client()
    client.delete_collection(CHROMA_COLLECTION)
    return client.get_or_create_collection(CHROMA_COLLECTION)


def embed(text: str) -> list[float]:
    response = _get_ollama_client().embeddings(model=settings.ollama_embed_model, prompt=text)

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
        return ""

from unittest.mock import MagicMock, patch

import pytest

import app.services.rag_service as rag_module
from app.services.rag_service import (
    embed,
    get_collection,
    get_documents_collection,
    query,
    query_documents,
    reset_collection,
)


def _make_collection_mock(documents: list[str]) -> MagicMock:
    mock = MagicMock()
    mock.query.return_value = {"documents": [documents], "metadatas": [[]]}

    return mock


def test_query_returns_formatted_results() -> None:
    collection = _make_collection_mock(["doc1", "doc2"])

    with (
        patch("app.services.rag_service.get_collection", return_value=collection),
        patch("app.services.rag_service.embed", return_value=[0.1, 0.2]),
    ):
        result = query("test question")

    assert result == "- doc1\n- doc2"


def test_query_returns_empty_on_exception() -> None:
    with (
        patch("app.services.rag_service.get_collection", side_effect=Exception("chroma down")),
        patch("app.services.rag_service._log") as mock_log,
    ):
        result = query("test question")

    assert result == ""
    mock_log.exception.assert_called_once()


def test_query_empty_documents() -> None:
    collection = _make_collection_mock([])

    with (
        patch("app.services.rag_service.get_collection", return_value=collection),
        patch("app.services.rag_service.embed", return_value=[0.1]),
    ):
        result = query("test question")

    assert result == ""


def test_query_documents_returns_empty_on_exception() -> None:
    with (
        patch(
            "app.services.rag_service.get_documents_collection",
            side_effect=Exception("chroma down"),
        ),
        patch("app.services.rag_service._log") as mock_log,
    ):
        result = query_documents("test question")

    assert result == ""

    mock_log.exception.assert_called_once()


def test_get_chroma_client_creates_singleton(monkeypatch: pytest.MonkeyPatch) -> None:
    import app.services.rag_service as rag

    monkeypatch.setattr(rag, "_chroma_client", None)

    with patch("app.services.rag_service.chromadb.HttpClient") as mock_cls:
        mock_cls.return_value = MagicMock()
        c1 = rag._get_chroma_client()
        c2 = rag._get_chroma_client()

    mock_cls.assert_called_once()
    assert c1 is c2


def test_get_ollama_client_creates_singleton(monkeypatch: pytest.MonkeyPatch) -> None:
    import app.services.rag_service as rag

    monkeypatch.setattr(rag, "_ollama_client", None)

    with patch("app.services.rag_service.ollama.Client") as mock_cls:
        mock_cls.return_value = MagicMock()
        c1 = rag._get_ollama_client()
        c2 = rag._get_ollama_client()

    mock_cls.assert_called_once()
    assert c1 is c2


def test_embed_calls_ollama(monkeypatch: pytest.MonkeyPatch) -> None:
    mock_client = MagicMock()
    mock_client.embeddings.return_value = {"embedding": [0.1, 0.2, 0.3]}

    monkeypatch.setattr(rag_module, "_ollama_client", None)

    with patch("app.services.rag_service._get_ollama_client", return_value=mock_client):
        result = embed("test text")

    assert result == [0.1, 0.2, 0.3]


def test_get_collection_uses_chroma_client() -> None:
    mock_client = MagicMock()
    mock_collection = MagicMock()
    mock_client.get_or_create_collection.return_value = mock_collection

    with patch("app.services.rag_service._get_chroma_client", return_value=mock_client):
        result = get_collection()

    assert result == mock_collection


def test_get_documents_collection_uses_chroma_client() -> None:
    mock_client = MagicMock()
    mock_collection = MagicMock()
    mock_client.get_or_create_collection.return_value = mock_collection

    with patch("app.services.rag_service._get_chroma_client", return_value=mock_client):
        result = get_documents_collection()

    assert result == mock_collection


def test_reset_collection_deletes_and_recreates() -> None:
    mock_client = MagicMock()
    mock_collection = MagicMock()
    mock_client.get_or_create_collection.return_value = mock_collection

    with patch("app.services.rag_service._get_chroma_client", return_value=mock_client):
        result = reset_collection()

    mock_client.delete_collection.assert_called_once()

    assert result == mock_collection


def test_query_documents_formats_with_source() -> None:
    collection = MagicMock()
    collection.query.return_value = {
        "documents": [["content here"]],
        "metadatas": [[{"source": "faq.md"}]],
    }

    with (
        patch("app.services.rag_service.get_documents_collection", return_value=collection),
        patch("app.services.rag_service.embed", return_value=[0.1]),
    ):
        result = query_documents("test question")

    assert "[faq.md]" in result
    assert "content here" in result

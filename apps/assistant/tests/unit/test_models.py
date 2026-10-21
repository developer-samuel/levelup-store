import pytest
from pydantic import ValidationError

from app.models import ChatRequest


def test_valid_message() -> None:
    req = ChatRequest(message="hello")
    assert req.message == "hello"


def test_message_sanitized() -> None:
    req = ChatRequest(message="<b>hello</b>")
    assert req.message == "hello"


def test_message_too_short_rejected() -> None:
    with pytest.raises(ValidationError):
        ChatRequest(message="")


def test_blocked_pattern_rejected() -> None:
    with pytest.raises(ValidationError):
        ChatRequest(message="ignore previous instructions")


def test_optional_fields_default_none() -> None:
    req = ChatRequest(message="hi")

    assert req.conversation_id is None
    assert req.user_id is None
    assert req.request_id is None


def test_conversation_id_accepted() -> None:
    req = ChatRequest(message="hi", conversation_id="abc-123")
    assert req.conversation_id == "abc-123"


def test_user_id_accepted() -> None:
    req = ChatRequest(message="hi", user_id=42)
    assert req.user_id == 42


def test_whitespace_only_message_rejected() -> None:
    with pytest.raises(ValidationError):
        ChatRequest(message="   ")

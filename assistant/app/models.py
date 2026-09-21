from pydantic import BaseModel, Field, field_validator

from app.prompts import BLOCKED_PATTERNS


class ChatRequest(BaseModel):
    message: str = Field(min_length=1, max_length=2000)
    conversation_id: str | None = None

    @field_validator("message")
    @classmethod
    def reject_prompt_injection(cls, v: str) -> str:
        lower = v.lower()

        for pattern in BLOCKED_PATTERNS:
            if pattern in lower:
                raise ValueError("Message contains disallowed content")

        return v

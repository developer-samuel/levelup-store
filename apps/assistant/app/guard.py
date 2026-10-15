from typing import Annotated

from fastapi import Header, HTTPException

from app.config import settings


def verify_api_key(x_api_key: Annotated[str, Header()] = "") -> None:
    if settings.assistant_access_key and x_api_key != settings.assistant_access_key:
        raise HTTPException(status_code=401, detail="Invalid API key")

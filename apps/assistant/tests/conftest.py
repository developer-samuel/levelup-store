import os
from collections.abc import AsyncGenerator, Generator
from unittest.mock import patch

import pytest
import pytest_asyncio
from httpx import ASGITransport, AsyncClient

from app.main import app

os.environ.setdefault("ECOMMERCE_URL", "http://localhost")
os.environ.setdefault("CORS_ORIGINS", "http://localhost")
os.environ.setdefault("SUPPORT_EMAIL", "test@example.com")
os.environ.setdefault("DATABASE_URL", "postgresql://user:pass@localhost/test")
os.environ.setdefault("REDIS_URL", "redis://localhost:6379/0")
os.environ.setdefault("ASSISTANT_ACCESS_KEY", "")


@pytest.fixture(autouse=True)
def disable_rate_limit() -> Generator[None, None, None]:
    with patch("app.middleware.rate_limiter.limiter.enabled", False, create=True):
        with patch("slowapi.Limiter._check_request_limit", return_value=None):
            yield


@pytest_asyncio.fixture
async def client() -> AsyncGenerator[AsyncClient, None]:
    with patch("app.guard.settings") as mock_settings:
        mock_settings.assistant_access_key = ""

        async with AsyncClient(transport=ASGITransport(app=app), base_url="http://test") as ac:
            yield ac

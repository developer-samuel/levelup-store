from unittest.mock import AsyncMock, patch

import pytest

from app.main import app, lifespan


@pytest.mark.asyncio
async def test_lifespan_calls_close_chat() -> None:
    mock_close = AsyncMock()

    with patch("app.main.close_chat", mock_close):
        async with lifespan(app):
            pass

    mock_close.assert_called_once()

from collections.abc import AsyncGenerator
from contextlib import asynccontextmanager

import sentry_sdk
from fastapi import FastAPI
from opentelemetry.instrumentation.fastapi import FastAPIInstrumentor
from slowapi.errors import RateLimitExceeded

from app.config import settings
from app.middleware.rate_limiter import limiter, rate_limit_handler
from app.middleware.security import setup_middleware
from app.routers import chat, chat_queue, health, info
from app.services.chat_service import close as close_chat
from app.telemetry import setup_telemetry

if settings.sentry_dsn:
    sentry_sdk.init(dsn=settings.sentry_dsn, send_default_pii=False)

setup_telemetry()


@asynccontextmanager
async def lifespan(_: FastAPI) -> AsyncGenerator[None, None]:
    yield
    await close_chat()


app = FastAPI(title="LevelUp Store Assistant", lifespan=lifespan)
app.state.limiter = limiter
app.add_exception_handler(RateLimitExceeded, rate_limit_handler)

setup_middleware(app)

app.include_router(chat.router)
app.include_router(chat_queue.router)
app.include_router(health.router)
app.include_router(info.router)

if settings.otel_exporter_otlp_endpoint:
    FastAPIInstrumentor.instrument_app(app)

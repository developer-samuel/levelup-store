import sentry_sdk
from fastapi import FastAPI
from opentelemetry.instrumentation.fastapi import FastAPIInstrumentor
from slowapi.errors import RateLimitExceeded

from app.config import settings
from app.middleware import setup_middleware
from app.rate_limiter import limiter, rate_limit_handler
from app.routers import chat, health, info
from app.telemetry import setup_telemetry

if settings.sentry_dsn:
    sentry_sdk.init(dsn=settings.sentry_dsn, send_default_pii=False)

setup_telemetry()

app = FastAPI(title="LevelUp Store Assistant")
app.state.limiter = limiter
app.add_exception_handler(RateLimitExceeded, rate_limit_handler)

setup_middleware(app)

app.include_router(chat.router)
app.include_router(health.router)
app.include_router(info.router)

if settings.otel_exporter_otlp_endpoint:
    FastAPIInstrumentor.instrument_app(app)

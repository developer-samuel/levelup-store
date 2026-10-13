from typing import cast

from fastapi import Request
from fastapi.responses import JSONResponse
from redis import Redis
from slowapi import Limiter
from slowapi.util import get_remote_address

from app.config import settings

limiter = Limiter(key_func=get_remote_address, storage_uri=settings.redis_url)


def _get_ttl(ip: str) -> int | None:
    try:
        r = Redis.from_url(settings.redis_url, decode_responses=True, socket_timeout=1)
        keys: list[str] = cast(list[str], r.keys(f"LIMITS:LIMITER/{ip}*"))

        if keys:
            ttl: int = cast(int, r.ttl(keys[0]))

            return ttl if ttl > 0 else None
    except Exception:
        pass
    return None


def rate_limit_handler(request: Request, _exc: Exception) -> JSONResponse:
    ip = request.client.host if request.client else ""
    ttl = _get_ttl(ip)

    content: dict[str, object] = {"success": False, "code": "rate_limit"}

    if ttl:
        content["retry_after"] = ttl

    return JSONResponse(status_code=429, content=content)

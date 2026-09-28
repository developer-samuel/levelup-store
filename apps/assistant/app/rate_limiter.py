from typing import cast

from fastapi import Request
from fastapi.responses import JSONResponse
from redis import Redis
from slowapi import Limiter
from slowapi.util import get_remote_address

from app.config import settings

limiter = Limiter(key_func=get_remote_address, storage_uri=settings.redis_url)


def _format_seconds(seconds: int) -> str:
    if seconds >= 3600:
        h = seconds // 3600
        m = (seconds % 3600) // 60
        base = f"{h} hour{'s' if h > 1 else ''}"

        return f"{base} {m} minute{'s' if m > 1 else ''}" if m else base
    if seconds >= 60:
        m = seconds // 60

        return f"{m} minute{'s' if m > 1 else ''}"
    return f"{seconds} second{'s' if seconds > 1 else ''}"


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

    if ttl:
        message = f"Rate limit exceeded. Please try again in {_format_seconds(ttl)}."
    else:
        message = "Rate limit exceeded. Please try again later."

    return JSONResponse(status_code=429, content={"success": False, "message": message})

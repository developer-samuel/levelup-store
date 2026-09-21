import asyncio
from typing import cast

import httpx
import psycopg2
from fastapi import APIRouter
from fastapi.responses import JSONResponse
from redis import Redis

from app.config import settings
from app.responses import success

router = APIRouter()


def _check_ollama() -> bool:
    try:
        with httpx.Client(timeout=2) as client:
            return client.get(f"{settings.ollama_host}/api/tags").is_success
    except Exception:
        return False


def _check_redis() -> bool:
    try:
        r = Redis.from_url(settings.redis_url, socket_timeout=1)
        return cast(bool, r.ping())
    except Exception:
        return False


def _check_chromadb() -> bool:
    try:
        with httpx.Client(timeout=2) as client:
            return client.get(f"{settings.chroma_host}/api/v2/heartbeat").is_success
    except Exception:
        return False


def _check_postgres() -> bool:
    try:
        conn = psycopg2.connect(settings.database_url, connect_timeout=2)
        conn.close()
        return True
    except Exception:
        return False


@router.get("/health")
async def health() -> JSONResponse:
    ollama, redis, chromadb, postgres = await asyncio.gather(
        asyncio.to_thread(_check_ollama),
        asyncio.to_thread(_check_redis),
        asyncio.to_thread(_check_chromadb),
        asyncio.to_thread(_check_postgres),
    )
    
    checks = {"ollama": ollama, "redis": redis, "chromadb": chromadb, "postgres": postgres}
    all_healthy = all(checks.values())

    return success(
        data={"status": "ok" if all_healthy else "degraded", "services": checks},
        code=200 if all_healthy else 503,
    )

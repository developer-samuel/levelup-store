from fastapi import APIRouter
from fastapi.responses import JSONResponse

from app.config import settings
from app.responses import success

router = APIRouter()


@router.get("/info")
async def info() -> JSONResponse:
    return success(data={"model": settings.ollama_model})

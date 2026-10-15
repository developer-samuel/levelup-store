from fastapi import APIRouter
from fastapi.responses import JSONResponse

from app.config import settings
from app.responses import success

router = APIRouter()


@router.get("/info")
async def info() -> JSONResponse:
    model = (
        settings.assistant_model
        if settings.assistant_type == "api"
        else settings.assistant_ollama_model
    )

    return success(data={"model": model, "ollama": settings.assistant_type == "ollama"})

from fastapi import APIRouter
from fastapi.responses import JSONResponse

from app.responses import success

router = APIRouter()


@router.get("/health")
async def health() -> JSONResponse:
    return success({"status": "ok"})

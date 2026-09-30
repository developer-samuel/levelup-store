from typing import Any

from fastapi.responses import JSONResponse


def success(data: Any = None, code: int = 200, message: str | None = None) -> JSONResponse:
    return JSONResponse(
        status_code=code,
        content={"success": True, "data": data, "message": message},
    )


def error(message: str, code: int = 400) -> JSONResponse:
    return JSONResponse(
        status_code=code,
        content={"success": False, "message": message},
    )

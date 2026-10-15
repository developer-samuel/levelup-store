import ollama

from app.adapters import api_adapter
from app.config import settings
from app.prompts.loader import load as _load

_PROMPT = _load("security", "guard")


async def is_attack(message: str) -> bool:
    prompt = _PROMPT.format(message=message)

    if settings.assistant_type == "api":
        answer = (await api_adapter.generate(prompt)).strip().upper()
    elif settings.assistant_type == "ollama":
        client = ollama.Client(host=settings.assistant_ollama_host)

        response = client.chat(
            model=settings.assistant_ollama_model,
            messages=[{"role": "user", "content": prompt}],
            options={"temperature": 0},
        )

        answer = str(response["message"]["content"]).strip().upper()
    else:
        return False

    return answer.startswith("YES")

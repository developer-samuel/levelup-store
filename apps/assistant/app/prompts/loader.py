import re
from pathlib import Path

_DIR = Path(__file__).parent / "templates"
_INCLUDE_RE = re.compile(r"\{\{include:([^}]+)\}\}")


def _resolve_includes(text: str) -> str:
    def _replace(m: re.Match[str]) -> str:
        return (_DIR / f"{m.group(1)}.md").read_text().strip()

    return _INCLUDE_RE.sub(_replace, text)


def load(folder: str, name: str, **kwargs: str) -> str:
    text = (_DIR / folder / f"{name}.md").read_text().strip()
    text = _resolve_includes(text)
    
    return text.format(**kwargs) if kwargs else text

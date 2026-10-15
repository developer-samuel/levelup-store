import re
import unicodedata

_HTML_TAG = re.compile(r"<[^>]+>")
_WHITESPACE = re.compile(r"\s+")
_MAX_LENGTH = 2000


def sanitize(text: str) -> str:
    text = unicodedata.normalize("NFKC", text)
    text = _HTML_TAG.sub("", text)
    text = _WHITESPACE.sub(" ", text)

    return text.strip()[:_MAX_LENGTH]

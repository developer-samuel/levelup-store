from app.prompts.blocked import BLOCKED_PATTERNS


def test_is_list_of_strings() -> None:
    assert isinstance(BLOCKED_PATTERNS, list)
    assert all(isinstance(p, str) for p in BLOCKED_PATTERNS)


def test_no_duplicates() -> None:
    assert len(BLOCKED_PATTERNS) == len(set(BLOCKED_PATTERNS))


def test_not_empty() -> None:
    assert len(BLOCKED_PATTERNS) > 0


def test_all_lowercase() -> None:
    assert all(p == p.lower() for p in BLOCKED_PATTERNS)

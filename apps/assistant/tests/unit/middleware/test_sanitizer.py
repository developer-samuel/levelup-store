from app.middleware.sanitizer import sanitize


def test_strips_html_tags() -> None:
    assert sanitize("<b>hello</b>") == "hello"


def test_collapses_whitespace() -> None:
    assert sanitize("hello   world") == "hello world"


def test_strips_leading_trailing_whitespace() -> None:
    assert sanitize("  hello  ") == "hello"


def test_normalizes_unicode() -> None:
    ligature_fi = "ﬁle"
    assert sanitize(ligature_fi) == "file"


def test_truncates_to_2000() -> None:
    long = "a" * 3000
    assert len(sanitize(long)) == 2000


def test_empty_string() -> None:
    assert sanitize("") == ""


def test_newlines_collapsed() -> None:
    assert sanitize("line1\n\nline2") == "line1 line2"

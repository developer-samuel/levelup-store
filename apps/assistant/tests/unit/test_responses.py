import json

from app.responses import error, success


def test_success_default() -> None:
    r = success(data={"key": "value"})

    assert r.status_code == 200

    body = r.body
    content = json.loads(body)

    assert content["success"] is True
    assert content["data"] == {"key": "value"}
    assert content["message"] is None


def test_success_custom_code() -> None:
    r = success(data={}, code=201)
    assert r.status_code == 201


def test_success_with_message() -> None:
    r = success(data=None, message="done")
    assert json.loads(r.body)["message"] == "done"


def test_error_default() -> None:
    r = error("something failed")

    assert r.status_code == 400

    content = json.loads(r.body)

    assert content["success"] is False
    assert content["message"] == "something failed"


def test_error_custom_code() -> None:
    r = error("not found", code=404)
    assert r.status_code == 404

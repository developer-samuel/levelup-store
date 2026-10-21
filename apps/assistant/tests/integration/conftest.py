from pathlib import Path

import psycopg2
import pytest
from dotenv import load_dotenv

import app.config as config_module
import app.database as db_module

_ENV_FILE = Path(__file__).parent.parent.parent / ".env.test"
load_dotenv(_ENV_FILE, override=True)


def pytest_runtest_setup(item: pytest.Item) -> None:
    if "integration" not in item.keywords:
        return

    if db_module._pool is not None:
        db_module._pool.closeall()
        db_module._pool = None

    new_settings = config_module.Settings()
    config_module.settings = new_settings
    db_module.settings = new_settings

    database_url = new_settings.database_url

    try:
        conn = psycopg2.connect(database_url, connect_timeout=2)
        conn.close()
    except Exception as e:
        pytest.skip(f"database not available - url={database_url!r} error={e}")

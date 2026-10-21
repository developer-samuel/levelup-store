"""
Scheduler - incrementally syncs data into ChromaDB to keep the assistant up to date.

Usage:
    python -m jobs.scheduler
"""

import logging
import time

from jobs.ingest import ingest

logging.basicConfig(level=logging.WARNING, format="%(asctime)s %(levelname)s %(message)s")
logger = logging.getLogger(__name__)

_INTERVAL = 15 * 60  # 15 minutes


if __name__ == "__main__":
    logger.warning(
        "Scheduler started - running incremental ingest every %d minutes",
        _INTERVAL // 60,
    )

    while True:
        try:
            ingest()
        except Exception as e:
            logger.error("Ingest failed: %s", e)

        time.sleep(_INTERVAL)

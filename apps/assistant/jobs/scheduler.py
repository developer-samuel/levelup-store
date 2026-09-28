"""
Scheduler - re-ingests data into ChromaDB every 15 minutes to keep the assistant up to date.

Usage:
    python -m jobs.scheduler
"""

import logging
import time

from app.rag import reset_collection
from jobs.ingest import ingest

logging.basicConfig(level=logging.WARNING, format="%(asctime)s %(levelname)s %(message)s")
logger = logging.getLogger(__name__)

_INTERVAL = 15 * 60  # 15 minutes


if __name__ == "__main__":
    logger.warning("Scheduler started - running ingest every %d minutes", _INTERVAL // 60)

    while True:
        try:
            reset_collection()
            ingest()
        except Exception as e:
            logger.error("Ingest failed: %s", e)
            
        time.sleep(_INTERVAL)

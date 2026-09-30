#!/bin/bash

# ────────────── Config ──────────────
BASE_DIR="$(dirname "$(dirname "$0")")/bootstrap"

CACHE_FILE="var/tools/pdepend/.pdepend.cache"
REPORT_SUMMARY_XML="var/tools/pdepend/reports/pdepend-summary.xml"
REPORT_JDEPEND_XML="var/tools/pdepend/reports/pdepend-jdepend.xml"
PDEPEND_CONFIG="pdepend.yaml"

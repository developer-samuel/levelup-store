#!/bin/bash
set -e

# ────────────── Clean old Deptrac folder ──────────────
source scripts/tools/common/directory/clean.sh
clean_directory "var/tools/deptrac"

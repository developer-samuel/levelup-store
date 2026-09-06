#!/bin/bash
set -e

# ────────────── Load common functions ──────────────
source scripts/tools/common/directory/create.sh

# ────────────── Ensure 'tools' directory in 'var' exists ──────────────
create_directory "var/tools"

# ────────────── Ensure PDepend tools directory exists ──────────────
create_directory "var/tools/pdepend"

# ────────────── Ensure PDepend reports directory exists ──────────────
create_directory "var/tools/pdepend/reports"

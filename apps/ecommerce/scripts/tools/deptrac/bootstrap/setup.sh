#!/bin/bash
set -e

# ────────────── Load common functions ──────────────
source scripts/tools/common/directory/create.sh

# ────────────── Ensure 'tools' directory in 'var' exists ──────────────
create_directory "var/tools"

# ────────────── Ensure Deptrac tools directory exists ──────────────
create_directory "var/tools/deptrac"

# ────────────── Ensure Deptrac reports directory exists ──────────────
create_directory "var/tools/deptrac/reports"

#!/bin/bash
set -e

# ────────────── Load common functions ──────────────
source scripts/tools/common/directory/create.sh

# ────────────── Ensure 'tools' directory in 'var' exists ──────────────
create_directory "var/tools"

# ────────────── Ensure PHPUnit tools directory exists ──────────────
create_directory "var/tools/php-unit"

# ────────────── Ensure PHPUnit HTML directory exists ──────────────
create_directory "var/tools/php-unit/html"

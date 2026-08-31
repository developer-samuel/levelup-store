#!/bin/bash
set -e

# ────────────── Load common functions ──────────────
source scripts/tools/common/directory/create.sh

# ────────────── Ensure 'tools' directory in 'var' exists ──────────────
create_directory "var/tools"

# ────────────── Ensure PHP-CS-Fixer tools directory exists ──────────────
create_directory "var/tools/php-cs-fixer"

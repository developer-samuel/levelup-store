#!/bin/bash
set -e
export DEBIAN_FRONTEND=noninteractive

echo "🔧 Updating packages and installing core dependencies..."
apt-get update -y

apt-get install -y --no-install-recommends \
    apt-transport-https \
    ca-certificates \
    gnupg2 \
    lsb-release \
    curl \
    wget \
    unzip \
    git \
    postgresql-client \
    libpq-dev \
    redis-tools \
    fontconfig \
    libfreetype6 \
    libjpeg62-turbo \
    libpng16-16 \
    xfonts-base \
    xfonts-75dpi \
    libxrender1 \
    libfontconfig1 \
    libx11-dev \
    libxtst6 \
    libxext6 \
    libssl3 \
    build-essential \
    libonig-dev \
    libzip-dev \
    zip \

apt-get clean

echo "✅ Core dependencies installed."

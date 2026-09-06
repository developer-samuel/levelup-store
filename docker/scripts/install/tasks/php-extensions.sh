#!/bin/bash
set -e

echo "🔨 Installing PHP extensions..."
docker-php-ext-install pdo pdo_pgsql zip

echo "📦 Installing PHP Redis extension..."
pecl install redis && docker-php-ext-enable redis

echo "📦 Installing PHP AMQP extension..."
apt-get install -y librabbitmq-dev
pecl install amqp && docker-php-ext-enable amqp

echo "📦 Installing PCOV (coverage driver)..."
pecl install pcov && docker-php-ext-enable pcov

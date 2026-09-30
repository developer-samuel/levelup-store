FROM composer:2 AS composer
FROM php:8.3-fpm

# ──────────────────────────────────
# 🎼 Composer
# ──────────────────────────────────
COPY --from=composer /usr/bin/composer /usr/local/bin/composer

# ──────────────────────────────────
# 🟦 PHP configuration
# ──────────────────────────────────
COPY config/php/php.ini /usr/local/etc/php/conf.d/opcache.ini
COPY config/php/www.conf /usr/local/etc/php-fpm.d/www.conf

# ──────────────────────────────────
# 📦 Install dependencies & fix permissions
# ──────────────────────────────────
COPY scripts/install/ /usr/local/bin/scripts/install/
RUN chmod +x /usr/local/bin/scripts/install/*.sh \
    && chmod +x /usr/local/bin/scripts/install/tasks/*.sh \
    && /usr/local/bin/scripts/install/dependencies.sh

# ──────────────────────────────────
# 🛠️ Helper scripts
# ──────────────────────────────────
COPY scripts/helpers/ /usr/local/bin/scripts/helpers/
RUN chmod +x /usr/local/bin/scripts/helpers/*.sh

# ──────────────────────────────────
# 🔐 Permissions scripts
# ──────────────────────────────────
COPY scripts/bootstrap/ /usr/local/bin/scripts/bootstrap/
RUN find /usr/local/bin/scripts/bootstrap/ -type f -name "*.sh" -exec chmod +x {} \; \
    && /usr/local/bin/scripts/bootstrap/permissions.sh

# ──────────────────────────────────
# 🌍 Working directory
# ──────────────────────────────────
WORKDIR /var/www/apps/ecommerce

RUN git config --system --add safe.directory /var/www \
    && git config --system --add safe.directory /var/www/apps/ecommerce

# ──────────────────────────────────
# ⚙️ Entrypoints & runtime scripts
# ──────────────────────────────────
COPY scripts/entrypoints/ /usr/local/bin/scripts/entrypoints/
RUN find /usr/local/bin/scripts/entrypoints/ -type f -name "*.sh" -exec chmod +x {} \;

COPY scripts/run.sh /usr/local/bin/scripts/run.sh
RUN chmod +x /usr/local/bin/scripts/run.sh

# ──────────────────────────────────
# 🚀 Set entrypoint & command
# ──────────────────────────────────
ENTRYPOINT ["/usr/local/bin/scripts/run.sh"]
CMD []

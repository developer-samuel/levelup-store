#!/bin/bash
set -e

# Ensure all runtime var/ subdirectories exist before setting permissions
echo "🛠️ Creating required directories..."
mkdir -p \
  /var/www/var/sessions \
  /var/www/var/cache \
  /var/www/var/log \
  /var/www/var/tmp \
  /var/www/var/tools

# www-data runs PHP-FPM - must own all of var/ to read/write at runtime
echo "🔧 Setting ownership and permissions for var/..."
chown -R www-data:www-data /var/www/var

# 775 dirs (group-writable), 664 files (group-writable, no world-write)
for dir in cache log sessions tmp; do
  find /var/www/var/${dir} -type d -exec chmod 775 {} \;
  find /var/www/var/${dir} -type f -exec chmod 664 {} \;
done

# tools: world-writable so both Docker (www-data) and host user can write
find /var/www/var/tools -type d -exec chmod 777 {} \;
find /var/www/var/tools -type f -executable -exec chmod 777 {} \;
find /var/www/var/tools -type f ! -executable -exec chmod 666 {} \;
chmod g+s /var/www/var/log

# 🔑 JWT keys - www-data needs read access
if [ -d /var/www/config/jwt ]; then
  chmod 644 /var/www/config/jwt/private.pem /var/www/config/jwt/public.pem 2>/dev/null || true
fi

# 📦 Frontend package files — readable by www-data, writable by host user only
for f in package.json package-lock.json pnpm-lock.yaml pnpm-workspace.yaml; do
  [ -f /var/www/$f ] && chmod 644 /var/www/$f
done

# 📢 Hot reload file
[ -f /var/www/public/hot ] && chown www-data:www-data /var/www/public/hot

# 📦 Frontend build assets directory
[ -d /var/www/public/build ] && chown -R www-data:www-data /var/www/public/build

# 📜 All project scripts - must be executable (only at runtime when /var/www is mounted)
if [ -d /var/www/scripts ]; then
  find /var/www/scripts -type f -name "*.sh" -exec chmod +x {} \;
fi

# 📜 Symfony bin/ executables
if [ -d /var/www/bin ]; then
  find /var/www/bin -type f -exec chmod +x {} \;
fi

# 📦 Composer vendor binaries
if [ -d /var/www/vendor/bin ]; then
  find /var/www/vendor/bin -type f -exec chmod +x {} \;
fi

echo "✅ Permissions and logging setup complete."

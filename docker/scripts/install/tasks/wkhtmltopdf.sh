#!/bin/bash
set -e

export DEBIAN_FRONTEND=noninteractive

echo "🖨️ Installing wkhtmltopdf (attempting to fetch prebuilt .deb)..."

WKDEB_TMP="/tmp/wkhtmltox.deb"
rm -f "${WKDEB_TMP}"

ARCH=$(dpkg --print-architecture)  # amd64 or arm64

if [ "${ARCH}" = "arm64" ]; then
  CANDIDATES=(
    "https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6.1-3/wkhtmltox_0.12.6.1-3.bookworm_arm64.deb"
    "https://deb.debian.org/debian/pool/main/w/wkhtmltopdf/wkhtmltopdf_0.12.6-2+b1_arm64.deb"
  )
else
  CANDIDATES=(
    "https://github.com/wkhtmltopdf/packaging/releases/download/0.12.6.1-3/wkhtmltox_0.12.6.1-3.bookworm_amd64.deb"
    "https://deb.debian.org/debian/pool/main/w/wkhtmltopdf/wkhtmltopdf_0.12.6-2+b1_amd64.deb"
  )
fi

DOWNLOAD_OK=0
for URL in "${CANDIDATES[@]}"; do
  echo "→ trying: ${URL}"
  if wget -q -O "${WKDEB_TMP}" "${URL}"; then
    echo "✔ downloaded from ${URL}"
    # try to install; if missing deps, apt will fix them below
    if dpkg -i "${WKDEB_TMP}" 2>/tmp/dpkg_install.log || true; then
      # dpkg may fail due to missing deps -> try to fix them
      apt-get -y -f install
      # re-run dpkg to make sure package configured
      dpkg -i "${WKDEB_TMP}" || true
    fi

    # verify binary present
    if [ -x /usr/local/bin/wkhtmltopdf ] || [ -x /usr/bin/wkhtmltopdf ]; then
      DOWNLOAD_OK=1
      break
    else
      echo "⚠ wkhtmltopdf binary not found after dpkg attempt (log in /tmp/dpkg_install.log). continuing fallback..."
      rm -f "${WKDEB_TMP}"
    fi
  else
    echo "✖ download failed for ${URL}"
  fi
done

if [ "${DOWNLOAD_OK}" -ne 1 ]; then
  echo "❌ ERROR: wkhtmltopdf download/install failed. See logs above."
  echo "Tried these URLs:"
  for u in "${CANDIDATES[@]}"; do echo "  - $u"; done
  exit 10
fi

# ensure executable permissions for whichever path got installed
if [ -f /usr/local/bin/wkhtmltopdf ]; then
  chmod +x /usr/local/bin/wkhtmltopdf || true
elif [ -f /usr/bin/wkhtmltopdf ]; then
  chmod +x /usr/bin/wkhtmltopdf || true
fi

# create a small wrapper to force headless/offscreen mode (safe default)
WRAPPER_PATH="/usr/local/bin/wkhtmltopdf-wrapper"
cat > "${WRAPPER_PATH}" <<'EOF'
#!/bin/bash
export QT_QPA_PLATFORM=offscreen
# Prefer installed binary in /usr/local/bin or /usr/bin
if [ -x /usr/local/bin/wkhtmltopdf ]; then
  exec /usr/local/bin/wkhtmltopdf "$@"
else
  exec /usr/bin/wkhtmltopdf "$@"
fi
EOF

chmod +x "${WRAPPER_PATH}" || true

# create friendly symlink safe executable (non-destructive)
ln -sf "${WRAPPER_PATH}" /usr/local/bin/wkhtmltopdf-safe || true

echo "✅ wkhtmltopdf installed and wrapper created."

# cleanup apt caches & temp files
rm -f "${WKDEB_TMP}"
rm -rf /var/lib/apt/lists/* /tmp/*

# quick smoke test (non-fatal): print version
if command -v wkhtmltopdf >/dev/null 2>&1; then
  echo "wkhtmltopdf version:"
  wkhtmltopdf --version || true
else
  echo "⚠ wkhtmltopdf not in PATH; check installation."
fi

echo "Wkhtmltopdf done."
FROM python:3.12-slim@sha256:02108f5d322dd89f1c9e552442c25acb0543dfdbc455693a5599624f20d9155d

WORKDIR /app

RUN addgroup --system app && adduser --system --ingroup app app

# System dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
        ca-certificates \
        libgomp1 \
        libpq5 \
    && rm -rf /var/lib/apt/lists/*

COPY --from=ghcr.io/astral-sh/uv:0.12.19@sha256:04d046b13e60d6bcec73cbc5e1cad25d680dea90c8573340950a0ac2d1aef424 /uv /usr/local/bin/uv

COPY pyproject.toml uv.lock ./
RUN uv sync --frozen --no-dev --no-cache

ENV PATH="/app/.venv/bin:$PATH"

COPY app/ ./app/
COPY jobs/ ./jobs/

COPY docker/scripts/ /usr/local/bin/scripts/
RUN chmod +x /usr/local/bin/scripts/*.sh

USER app

EXPOSE 8001

ENTRYPOINT ["/usr/local/bin/scripts/run.sh"]

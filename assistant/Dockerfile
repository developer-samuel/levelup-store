FROM python:3.12-slim

WORKDIR /app

RUN addgroup --system app && adduser --system --ingroup app app

# System dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
        ca-certificates \
        libgomp1 \
        libpq5 \
    && rm -rf /var/lib/apt/lists/*

COPY requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

COPY app/ ./app/
COPY jobs/ ./jobs/

COPY docker/scripts/ /usr/local/bin/scripts/
RUN chmod +x /usr/local/bin/scripts/*.sh

USER app

EXPOSE 8001

ENTRYPOINT ["/usr/local/bin/scripts/run.sh"]

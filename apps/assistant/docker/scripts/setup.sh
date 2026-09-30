#!/usr/bin/env bash
set -euo pipefail

# Pull Ollama models via Python client (idempotent - skips if already pulled)
python3 - <<EOF
import ollama, os, sys

host = os.environ.get("OLLAMA_HOST", "http://localhost:11434")
client = ollama.Client(host=host)

for model in [
    os.environ.get("OLLAMA_MODEL", "mistral:7b"),
    os.environ.get("OLLAMA_EMBED_MODEL", "nomic-embed-text"),
]:
    print(f"Pulling {model}...", flush=True)
    last_status = None
    for chunk in client.pull(model, stream=True):
        status = chunk.get("status", "")
        completed = chunk.get("completed", 0)
        total = chunk.get("total", 0)
        if status != last_status:
            if total:
                pct = int(completed / total * 100)
                print(f"  {status}: {pct}%", flush=True)
            else:
                print(f"  {status}", flush=True)
            last_status = status
    print(f"Done: {model}", flush=True)
EOF

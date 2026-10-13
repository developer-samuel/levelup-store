#!/usr/bin/env bash
set -euo pipefail

# Pull Ollama models only when running in ollama mode
if [ "${ASSISTANT_TYPE:-ollama}" != "ollama" ]; then
    exit 0
fi

python3 - <<EOF
import ollama, os, sys

host = os.environ.get("ASSISTANT_OLLAMA_HOST", "http://localhost:11434")
client = ollama.Client(host=host)

for model in [
    os.environ.get("ASSISTANT_OLLAMA_MODEL", "mistral:7b-instruct"),
    os.environ.get("ASSISTANT_OLLAMA_EMBED_MODEL", "nomic-embed-text"),
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

# LevelUp Store Chatbot

FastAPI chatbot service with Ollama + RAG.

## Prerequisites

- Python 3.12 + pip + venv

```bash
sudo apt install python3-pip python3.12-venv
```
- [Ollama](https://ollama.com) running locally with `qwen2.5:3b` pulled

```bash
sudo snap install ollama
ollama pull qwen2.5:3b
```

## Quick start

```bash
cp .env.example .env
make install
make run
```

API available at `http://localhost:8001`.  
Swagger docs at `http://localhost:8001/docs`.

## Environment variables

| Variable                    | Default                       | Description                            |
|-----------------------------|-------------------------------|----------------------------------------|
| `OLLAMA_HOST`               | `http://localhost:11434`      | Ollama service URL                     |
| `OLLAMA_MODEL`              | `qwen2.5:3b`                  | Model to use for generation            |
| `CORS_ORIGINS`              | `*`                           | Allowed CORS origins (comma-separated) |

## Commands

| Command               | Description                      |
|-----------------------|----------------------------------|
| `make install`        | Install dependencies             |
| `make run`            | Start dev server with hot reload |
| `make lint`           | Run ruff linter                  |
| `make format`         | Run ruff formatter               |
| `make typecheck`      | Run mypy type checker            |
| `make docker-build`   | Build Docker image               |
| `make docker-run`     | Run Docker container             |

## Endpoints

| Method   | Path      | Description                    |
|----------|-----------|--------------------------------|
| `POST`   | `/chat`   | Send message, receive SSE stream |
| `GET`    | `/health` | Health check                   |

from typing import Literal

from pydantic_settings import BaseSettings, SettingsConfigDict

CHROMA_COLLECTION_PRODUCTS = "products"
CHROMA_COLLECTION_DOCUMENTS = "documents"


class Settings(BaseSettings):
    model_config = SettingsConfigDict(env_file=".env", extra="ignore")

    # Application
    ecommerce_url: str
    cors_origins: str
    support_email: str

    # Database / Redis
    database_url: str
    redis_url: str

    # Assistant
    assistant_type: Literal["ollama", "api"] = "ollama"
    assistant_api_key: str = ""
    assistant_model: str = "gemini-3.5-flash-lite"
    assistant_fallback_model: str = "gemini-3.1-flash-lite"
    assistant_primary_limit: int = 20
    assistant_fallback_limit: int = 30
    assistant_limit_window: int = 18000  # 5 hours in seconds
    assistant_access_key: str = ""
    assistant_embed_url: str = ""
    assistant_embed_model: str = ""

    # Ollama
    assistant_ollama_host: str = "http://localhost:11434"
    assistant_ollama_model: str = "mistral:7b-instruct"
    assistant_ollama_embed_model: str = "nomic-embed-text"
    assistant_ollama_limit: int = 50

    # RAG
    chroma_host: str = "http://localhost:8010"

    # Conversation
    conversation_ttl_guest: int = 60 * 60 * 24  # 1 day
    conversation_ttl_user: int = 60 * 60 * 24 * 30  # 30 days

    # Messaging
    rabbitmq_url: str = ""

    # Storage
    minio_enabled: bool = False
    minio_public_url: str = ""
    minio_bucket: str = ""

    # Observability
    sentry_dsn: str = ""
    otel_service_name: str = "levelup-store-assistant"
    otel_exporter_otlp_endpoint: str = ""
    otel_exporter_otlp_protocol: str = "http/protobuf"


settings = Settings()

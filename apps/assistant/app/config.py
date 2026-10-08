from pydantic_settings import BaseSettings, SettingsConfigDict

CHROMA_COLLECTION_PRODUCTS = "products"


class Settings(BaseSettings):
    model_config = SettingsConfigDict(env_file=".env", extra="ignore")

    # service URLs - sensible defaults for local dev
    ollama_host: str = "http://localhost:11434"
    chroma_host: str = "http://localhost:8010"

    # model names - fixed defaults, unlikely to change per environment
    ollama_model: str = "llama3.2:1b"
    ollama_embed_model: str = "nomic-embed-text"

    # required - must be set in .env, no safe default
    cors_origins: str
    database_url: str
    redis_url: str
    support_email: str

    # optional
    rabbitmq_url: str = ""
    api_key: str = ""
    sentry_dsn: str = ""
    otel_service_name: str = "levelup-store-assistant"
    otel_exporter_otlp_endpoint: str = ""
    otel_exporter_otlp_protocol: str = "http/protobuf"


settings = Settings()

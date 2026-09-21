from pydantic_settings import BaseSettings, SettingsConfigDict

CHROMA_COLLECTION = "products"


class Settings(BaseSettings):
    model_config = SettingsConfigDict(env_file=".env", extra="ignore")

    # service URLs - sensible defaults for local dev
    ollama_host: str = "http://localhost:11434"
    chroma_host: str = "http://localhost:8010"

    # model names - fixed defaults, unlikely to change per environment
    ollama_model: str = "mistral:7b"
    ollama_embed_model: str = "nomic-embed-text"

    # required - must be set in .env, no safe default
    cors_origins: str
    database_url: str


settings = Settings()

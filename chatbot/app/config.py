from pydantic_settings import BaseSettings, SettingsConfigDict


class Settings(BaseSettings):
    model_config = SettingsConfigDict(env_file=".env")

    ollama_host: str = "http://localhost:11434"
    ollama_model: str = "qwen2.5:3b"
    cors_origins: str = "*"


settings = Settings()

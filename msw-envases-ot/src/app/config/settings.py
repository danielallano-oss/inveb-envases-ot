"""
Configuración del microservicio INVEB Envases OT.
Utiliza pydantic-settings para manejo de variables de entorno.
"""
from pydantic_settings import BaseSettings
from functools import lru_cache


class Settings(BaseSettings):
    """Configuración principal del microservicio."""

    # App
    APP_NAME: str = "INVEB Envases OT API"
    APP_VERSION: str = "1.0.0"
    DEBUG: bool = False
    ENVIRONMENT: str = "development"

    # Database MySQL (base de datos compartida)
    LARAVEL_MYSQL_HOST: str = "host.docker.internal"
    LARAVEL_MYSQL_PORT: int = 3307
    LARAVEL_MYSQL_USER: str = "root"
    LARAVEL_MYSQL_PASSWORD: str = "root"
    LARAVEL_MYSQL_DATABASE: str = "envases_ot"

    # JWT Configuration
    JWT_SECRET_KEY: str = "inveb-cascade-service-secret-key-2024"
    JWT_ALGORITHM: str = "HS256"
    JWT_EXPIRATION_HOURS: int = 24

    # CORS
    CORS_ORIGINS: list[str] = [
        "http://localhost:3000",
        "http://localhost:3001",
        "http://localhost:3002",
        "http://localhost:5173",
        "https://inveb-frontend-production.up.railway.app",
        "https://inveb-api-production.up.railway.app",
    ]

    # API
    API_PREFIX: str = "/api/v1"

    class Config:
        env_file = ".env"
        case_sensitive = True


@lru_cache()
def get_settings() -> Settings:
    """Retorna instancia cacheada de settings."""
    return Settings()

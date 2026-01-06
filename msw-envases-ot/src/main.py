"""
INVEB Envases OT - Microservicio Principal
API para gestión de Órdenes de Trabajo y reglas de cascada.

Stack: Python 3.12 + FastAPI + MySQL
Estándar: Monitor One
"""
from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from fastapi.staticfiles import StaticFiles
from contextlib import asynccontextmanager
from pathlib import Path
import os

from app.config import get_settings
from app.routers import ROUTERS

settings = get_settings()


@asynccontextmanager
async def lifespan(app: FastAPI):
    """Lifecycle management."""
    yield


app = FastAPI(
    title=settings.APP_NAME,
    description="""
    API de INVEB Envases para gestión de Órdenes de Trabajo.

    ## Funcionalidades

    * **Cascade Rules**: Reglas de habilitación/deshabilitación de campos
    * **Valid Combinations**: Combinaciones válidas de producto/impresión/FSC
    * **Validation Endpoints**: Validación en tiempo real del formulario

    ## Estándares

    Desarrollado siguiendo estándares Monitor One de Tecnoandina.
    """,
    version=settings.APP_VERSION,
    lifespan=lifespan,
    docs_url="/docs",
    redoc_url="/redoc"
)

# Configurar CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=settings.CORS_ORIGINS,
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Registrar routers
for router in ROUTERS:
    app.include_router(router, prefix=settings.API_PREFIX)

# Montar archivos estáticos (para servir archivos subidos)
FILES_DIR = Path("/app/files")
FILES_DIR.mkdir(parents=True, exist_ok=True)
app.mount("/files", StaticFiles(directory=str(FILES_DIR)), name="files")


@app.get("/")
def root():
    """Health check endpoint."""
    return {
        "service": settings.APP_NAME,
        "version": settings.APP_VERSION,
        "status": "healthy"
    }


@app.get("/health")
def health_check():
    """Health check para Kubernetes."""
    return {"status": "ok"}


if __name__ == "__main__":
    import uvicorn
    uvicorn.run(
        "main:app",
        host="0.0.0.0",
        port=8000,
        reload=settings.DEBUG
    )

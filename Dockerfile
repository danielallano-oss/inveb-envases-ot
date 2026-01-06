# INVEB Envases OT - Microservice Dockerfile for Railway
# Stack: Python 3.12 + FastAPI + SQLModel

FROM python:3.12-slim

# Metadata
LABEL maintainer="Tecnoandina"
LABEL description="INVEB Envases OT Microservice"
LABEL version="1.0.0"

# Set environment variables
ENV PYTHONDONTWRITEBYTECODE=1
ENV PYTHONUNBUFFERED=1
ENV PYTHONPATH=/app/src

WORKDIR /app

# Install system dependencies
RUN apt-get update && apt-get install -y --no-install-recommends \
    gcc \
    libpq-dev \
    curl \
    && rm -rf /var/lib/apt/lists/*

# Copy requirements first (Docker layer caching)
# Note: Copying from msw-envases-ot subdirectory
COPY msw-envases-ot/requirements.txt .
RUN pip install --no-cache-dir -r requirements.txt

# Copy source code from msw-envases-ot
COPY msw-envases-ot/src/ ./src/
COPY msw-envases-ot/alembic/ ./alembic/
COPY msw-envases-ot/alembic.ini .

# Expose port
EXPOSE 8000

# Health check
HEALTHCHECK --interval=30s --timeout=10s --start-period=5s --retries=3 \
    CMD curl -f http://localhost:8000/health || exit 1

# Run with uvicorn
CMD ["uvicorn", "src.main:app", "--host", "0.0.0.0", "--port", "8000"]

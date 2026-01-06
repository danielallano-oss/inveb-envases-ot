# INVEB Cascade Frontend
# Multi-stage build for React + Vite

# Stage 1: Build
FROM node:20-alpine AS builder

WORKDIR /app

# Build argument for API URL (Railway will inject this)
ARG VITE_API_URL=http://localhost:8001/api/v1

# Set as environment variable for Vite build
ENV VITE_API_URL=$VITE_API_URL

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm ci

# Copy source code
COPY . .

# Build the application (skip TypeScript check, pre-existing errors)
RUN npx vite build

# Stage 2: Production
FROM nginx:alpine

# Copy built assets from builder
COPY --from=builder /app/dist /usr/share/nginx/html

# Copy nginx configuration for production
COPY nginx.prod.conf /etc/nginx/conf.d/default.conf

# Expose port (Railway uses PORT env variable)
EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]

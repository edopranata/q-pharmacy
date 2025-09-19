# Frontend Deployment Documentation

## Overview

Dokumentasi ini menjelaskan proses deployment aplikasi frontend Q-Pharmacy yang dibangun dengan Vue.js 3 dan Quasar Framework. Panduan ini mencakup deployment untuk berbagai environment mulai dari development hingga production.

## Prerequisites

### System Requirements
- **Node.js**: >= 16.x
- **npm**: >= 8.x atau **yarn**: >= 1.22.x
- **Git**: Latest version
- **Docker**: >= 20.x (optional, untuk containerized deployment)

### Development Tools
- **Quasar CLI**: `npm install -g @quasar/cli`
- **Vue CLI**: `npm install -g @vue/cli` (optional)

## Environment Setup

### Development Environment

#### 1. Clone Repository
```bash
git clone <repository-url>
cd q-pharmacy/frontend/web
```

#### 2. Install Dependencies
```bash
# Using npm
npm install

# Using yarn
yarn install
```

#### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env.development

# Edit environment variables
vim .env.development
```

#### 4. Start Development Server
```bash
# Using Quasar CLI
quasar dev

# Using npm scripts
npm run dev

# Using yarn
yarn dev
```

### Staging Environment

#### 1. Environment Configuration
```bash
# .env.staging
VUE_APP_ENV=staging
VUE_APP_API_URL=https://api-staging.q-pharmacy.com
VUE_APP_WS_URL=wss://ws-staging.q-pharmacy.com
VUE_APP_SENTRY_DSN=your-sentry-dsn
VUE_APP_GOOGLE_ANALYTICS_ID=your-ga-id
```

#### 2. Build for Staging
```bash
# Build with staging environment
quasar build --mode staging

# Or using npm
npm run build:staging
```

### Production Environment

#### 1. Environment Configuration
```bash
# .env.production
VUE_APP_ENV=production
VUE_APP_API_URL=https://api.q-pharmacy.com
VUE_APP_WS_URL=wss://ws.q-pharmacy.com
VUE_APP_SENTRY_DSN=your-production-sentry-dsn
VUE_APP_GOOGLE_ANALYTICS_ID=your-production-ga-id
VUE_APP_CDN_URL=https://cdn.q-pharmacy.com
```

#### 2. Build for Production
```bash
# Build for production
quasar build

# Or using npm
npm run build

# Build with specific target
quasar build --target spa
```

## Deployment Strategies

### 1. Static Hosting (Recommended)

#### Netlify Deployment

**netlify.toml**
```toml
[build]
  publish = "dist/spa"
  command = "npm run build"

[build.environment]
  NODE_VERSION = "16"

[[redirects]]
  from = "/*"
  to = "/index.html"
  status = 200

[[headers]]
  for = "/static/*"
  [headers.values]
    Cache-Control = "public, max-age=31536000, immutable"

[[headers]]
  for = "*.js"
  [headers.values]
    Cache-Control = "public, max-age=31536000, immutable"

[[headers]]
  for = "*.css"
  [headers.values]
    Cache-Control = "public, max-age=31536000, immutable"
```

**Deployment Steps:**
```bash
# Install Netlify CLI
npm install -g netlify-cli

# Login to Netlify
netlify login

# Deploy to staging
netlify deploy --dir=dist/spa

# Deploy to production
netlify deploy --prod --dir=dist/spa
```

#### Vercel Deployment

**vercel.json**
```json
{
  "version": 2,
  "builds": [
    {
      "src": "package.json",
      "use": "@vercel/static-build",
      "config": {
        "distDir": "dist/spa"
      }
    }
  ],
  "routes": [
    {
      "src": "/static/(.*)",
      "headers": {
        "Cache-Control": "public, max-age=31536000, immutable"
      }
    },
    {
      "src": "/(.*)",
      "dest": "/index.html"
    }
  ]
}
```

**package.json scripts:**
```json
{
  "scripts": {
    "build": "quasar build",
    "vercel-build": "npm run build"
  }
}
```

### 2. Docker Deployment

#### Dockerfile
```dockerfile
# Build stage
FROM node:16-alpine as build-stage

WORKDIR /app

# Copy package files
COPY package*.json ./
RUN npm ci --only=production

# Copy source code
COPY . .

# Build application
RUN npm run build

# Production stage
FROM nginx:alpine as production-stage

# Copy built files
COPY --from=build-stage /app/dist/spa /usr/share/nginx/html

# Copy nginx configuration
COPY nginx.conf /etc/nginx/nginx.conf

EXPOSE 80

CMD ["nginx", "-g", "daemon off;"]
```

#### nginx.conf
```nginx
events {
    worker_connections 1024;
}

http {
    include       /etc/nginx/mime.types;
    default_type  application/octet-stream;
    
    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;
    
    server {
        listen 80;
        server_name localhost;
        root /usr/share/nginx/html;
        index index.html;
        
        # Security headers
        add_header X-Frame-Options "SAMEORIGIN" always;
        add_header X-Content-Type-Options "nosniff" always;
        add_header X-XSS-Protection "1; mode=block" always;
        add_header Referrer-Policy "strict-origin-when-cross-origin" always;
        
        # Cache static assets
        location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
            expires 1y;
            add_header Cache-Control "public, immutable";
        }
        
        # Handle client-side routing
        location / {
            try_files $uri $uri/ /index.html;
        }
        
        # API proxy (optional)
        location /api/ {
            proxy_pass http://backend:8000/api/;
            proxy_set_header Host $host;
            proxy_set_header X-Real-IP $remote_addr;
            proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
            proxy_set_header X-Forwarded-Proto $scheme;
        }
    }
}
```

#### Docker Compose
```yaml
# docker-compose.yml
version: '3.8'

services:
  frontend:
    build:
      context: .
      dockerfile: Dockerfile
    ports:
      - "80:80"
    environment:
      - NODE_ENV=production
    depends_on:
      - backend
    networks:
      - app-network

  backend:
    image: q-pharmacy-backend:latest
    ports:
      - "8000:8000"
    networks:
      - app-network

networks:
  app-network:
    driver: bridge
```

#### Build and Deploy
```bash
# Build Docker image
docker build -t q-pharmacy-frontend:latest .

# Run container
docker run -p 80:80 q-pharmacy-frontend:latest

# Using Docker Compose
docker-compose up -d
```

### 3. AWS S3 + CloudFront

#### S3 Bucket Setup
```bash
# Create S3 bucket
aws s3 mb s3://q-pharmacy-frontend

# Configure bucket for static website hosting
aws s3 website s3://q-pharmacy-frontend \
  --index-document index.html \
  --error-document index.html
```

#### CloudFront Distribution
```json
{
  "DistributionConfig": {
    "CallerReference": "q-pharmacy-frontend",
    "Origins": {
      "Quantity": 1,
      "Items": [
        {
          "Id": "S3-q-pharmacy-frontend",
          "DomainName": "q-pharmacy-frontend.s3.amazonaws.com",
          "S3OriginConfig": {
            "OriginAccessIdentity": ""
          }
        }
      ]
    },
    "DefaultCacheBehavior": {
      "TargetOriginId": "S3-q-pharmacy-frontend",
      "ViewerProtocolPolicy": "redirect-to-https",
      "Compress": true,
      "CachePolicyId": "managed-caching-optimized"
    },
    "Comment": "Q-Pharmacy Frontend Distribution",
    "Enabled": true
  }
}
```

#### Deployment Script
```bash
#!/bin/bash
# deploy-aws.sh

set -e

# Build application
npm run build

# Sync to S3
aws s3 sync dist/spa/ s3://q-pharmacy-frontend --delete

# Invalidate CloudFront cache
aws cloudfront create-invalidation \
  --distribution-id YOUR_DISTRIBUTION_ID \
  --paths "/*"

echo "Deployment completed successfully!"
```

## CI/CD Pipeline

### GitHub Actions

**.github/workflows/deploy.yml**
```yaml
name: Deploy Frontend

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup Node.js
      uses: actions/setup-node@v3
      with:
        node-version: '16'
        cache: 'npm'
        cache-dependency-path: frontend/web/package-lock.json
    
    - name: Install dependencies
      working-directory: frontend/web
      run: npm ci
    
    - name: Run tests
      working-directory: frontend/web
      run: npm run test:unit
    
    - name: Run linting
      working-directory: frontend/web
      run: npm run lint
    
    - name: Build application
      working-directory: frontend/web
      run: npm run build
  
  deploy-staging:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/develop'
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup Node.js
      uses: actions/setup-node@v3
      with:
        node-version: '16'
        cache: 'npm'
        cache-dependency-path: frontend/web/package-lock.json
    
    - name: Install dependencies
      working-directory: frontend/web
      run: npm ci
    
    - name: Build for staging
      working-directory: frontend/web
      run: npm run build:staging
      env:
        VUE_APP_API_URL: ${{ secrets.STAGING_API_URL }}
    
    - name: Deploy to Netlify
      uses: nwtgck/actions-netlify@v1.2
      with:
        publish-dir: './frontend/web/dist/spa'
        production-branch: develop
        github-token: ${{ secrets.GITHUB_TOKEN }}
        deploy-message: "Deploy from GitHub Actions"
      env:
        NETLIFY_AUTH_TOKEN: ${{ secrets.NETLIFY_AUTH_TOKEN }}
        NETLIFY_SITE_ID: ${{ secrets.NETLIFY_SITE_ID }}
  
  deploy-production:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Setup Node.js
      uses: actions/setup-node@v3
      with:
        node-version: '16'
        cache: 'npm'
        cache-dependency-path: frontend/web/package-lock.json
    
    - name: Install dependencies
      working-directory: frontend/web
      run: npm ci
    
    - name: Build for production
      working-directory: frontend/web
      run: npm run build
      env:
        VUE_APP_API_URL: ${{ secrets.PRODUCTION_API_URL }}
    
    - name: Deploy to AWS S3
      uses: aws-actions/configure-aws-credentials@v1
      with:
        aws-access-key-id: ${{ secrets.AWS_ACCESS_KEY_ID }}
        aws-secret-access-key: ${{ secrets.AWS_SECRET_ACCESS_KEY }}
        aws-region: us-east-1
    
    - name: Sync to S3
      working-directory: frontend/web
      run: |
        aws s3 sync dist/spa/ s3://${{ secrets.S3_BUCKET }} --delete
        aws cloudfront create-invalidation --distribution-id ${{ secrets.CLOUDFRONT_DISTRIBUTION_ID }} --paths "/*"
```

### GitLab CI/CD

**.gitlab-ci.yml**
```yaml
stages:
  - test
  - build
  - deploy

variables:
  NODE_VERSION: "16"

cache:
  paths:
    - frontend/web/node_modules/

test:
  stage: test
  image: node:$NODE_VERSION
  before_script:
    - cd frontend/web
    - npm ci
  script:
    - npm run test:unit
    - npm run lint
  artifacts:
    reports:
      junit: frontend/web/test-results.xml
      coverage: frontend/web/coverage/

build:
  stage: build
  image: node:$NODE_VERSION
  before_script:
    - cd frontend/web
    - npm ci
  script:
    - npm run build
  artifacts:
    paths:
      - frontend/web/dist/
    expire_in: 1 hour
  only:
    - main
    - develop

deploy:staging:
  stage: deploy
  image: node:$NODE_VERSION
  before_script:
    - npm install -g netlify-cli
  script:
    - cd frontend/web
    - netlify deploy --dir=dist/spa --site=$NETLIFY_SITE_ID --auth=$NETLIFY_AUTH_TOKEN
  environment:
    name: staging
    url: https://staging.q-pharmacy.com
  only:
    - develop

deploy:production:
  stage: deploy
  image: node:$NODE_VERSION
  before_script:
    - npm install -g netlify-cli
  script:
    - cd frontend/web
    - netlify deploy --prod --dir=dist/spa --site=$NETLIFY_SITE_ID --auth=$NETLIFY_AUTH_TOKEN
  environment:
    name: production
    url: https://q-pharmacy.com
  only:
    - main
  when: manual
```

## Monitoring and Health Checks

### Application Monitoring

```javascript
// src/utils/monitoring.js
import * as Sentry from '@sentry/vue'

export const initMonitoring = (app) => {
  if (process.env.VUE_APP_ENV === 'production') {
    Sentry.init({
      app,
      dsn: process.env.VUE_APP_SENTRY_DSN,
      environment: process.env.VUE_APP_ENV,
      tracesSampleRate: 0.1,
      beforeSend(event) {
        // Filter out development errors
        if (event.environment === 'development') {
          return null
        }
        return event
      }
    })
  }
}

// Performance monitoring
export const trackPerformance = () => {
  if ('performance' in window) {
    window.addEventListener('load', () => {
      const navigation = performance.getEntriesByType('navigation')[0]
      const loadTime = navigation.loadEventEnd - navigation.loadEventStart
      
      // Send to analytics
      if (window.gtag) {
        window.gtag('event', 'page_load_time', {
          custom_parameter: loadTime
        })
      }
    })
  }
}
```

### Health Check Endpoint

```javascript
// public/health.json
{
  "status": "healthy",
  "version": "1.0.0",
  "timestamp": "2024-01-01T00:00:00Z",
  "environment": "production"
}
```

### Uptime Monitoring

```yaml
# monitoring/uptime.yml
apiVersion: v1
kind: ConfigMap
metadata:
  name: uptime-config
data:
  config.yml: |
    checks:
      - name: "Q-Pharmacy Frontend"
        url: "https://q-pharmacy.com"
        method: "GET"
        expected_status: 200
        interval: 30s
        timeout: 10s
      
      - name: "Health Check"
        url: "https://q-pharmacy.com/health.json"
        method: "GET"
        expected_status: 200
        interval: 60s
```

## Security Considerations

### Content Security Policy

```html
<!-- public/index.html -->
<meta http-equiv="Content-Security-Policy" content="
  default-src 'self';
  script-src 'self' 'unsafe-inline' https://www.google-analytics.com;
  style-src 'self' 'unsafe-inline' https://fonts.googleapis.com;
  font-src 'self' https://fonts.gstatic.com;
  img-src 'self' data: https:;
  connect-src 'self' https://api.q-pharmacy.com wss://ws.q-pharmacy.com;
">
```

### Environment Variables Security

```bash
# Never commit these to version control
# Use CI/CD secrets instead

# .env.production (example - use CI/CD secrets)
VUE_APP_API_URL=https://api.q-pharmacy.com
VUE_APP_SENTRY_DSN=your-sentry-dsn
# Do not include sensitive keys in frontend builds
```

### Build Security

```javascript
// quasar.config.js
module.exports = {
  build: {
    // Remove console logs in production
    minify: true,
    
    // Source maps for debugging (disable in production)
    sourcemap: process.env.NODE_ENV !== 'production',
    
    // Environment variables filtering
    env: {
      // Only include safe environment variables
      API_URL: process.env.VUE_APP_API_URL,
      ENV: process.env.VUE_APP_ENV
      // Never include secrets or API keys
    }
  }
}
```

## Troubleshooting

### Common Issues

#### 1. Build Failures
```bash
# Clear cache and reinstall
rm -rf node_modules package-lock.json
npm install

# Clear Quasar cache
quasar clean

# Check Node.js version
node --version
npm --version
```

#### 2. Routing Issues
```nginx
# Ensure proper nginx configuration for SPA
location / {
    try_files $uri $uri/ /index.html;
}
```

#### 3. API Connection Issues
```javascript
// Check CORS configuration
// Verify API URL in environment variables
// Check network connectivity

// Debug API calls
console.log('API URL:', process.env.VUE_APP_API_URL)
```

#### 4. Performance Issues
```bash
# Analyze bundle size
npm run build -- --analyze

# Check for large dependencies
npm ls --depth=0

# Optimize images
# Use lazy loading
# Implement code splitting
```

### Debugging Tools

```javascript
// Development debugging
if (process.env.NODE_ENV === 'development') {
  // Vue DevTools
  window.__VUE_DEVTOOLS_GLOBAL_HOOK__ = true
  
  // Performance monitoring
  console.log('Performance entries:', performance.getEntries())
}
```

## Best Practices

### Deployment Checklist

- [ ] Environment variables configured
- [ ] Build process tested
- [ ] Tests passing
- [ ] Security headers configured
- [ ] HTTPS enabled
- [ ] CDN configured
- [ ] Monitoring setup
- [ ] Backup strategy in place
- [ ] Rollback plan prepared

### Performance Optimization

1. **Bundle Optimization**
   - Code splitting by routes
   - Tree shaking unused code
   - Minimize bundle size

2. **Caching Strategy**
   - Static asset caching
   - API response caching
   - Service worker implementation

3. **Image Optimization**
   - WebP format support
   - Lazy loading
   - Responsive images

### Security Best Practices

1. **Content Security Policy**
2. **HTTPS enforcement**
3. **Secure headers**
4. **Environment variable security**
5. **Dependency vulnerability scanning**

## Conclusion

Dokumentasi ini menyediakan panduan lengkap untuk deployment aplikasi frontend Q-Pharmacy. Ikuti best practices yang telah ditetapkan untuk memastikan deployment yang aman, reliable, dan performant.

Untuk bantuan lebih lanjut, hubungi tim DevOps atau lihat dokumentasi teknis lainnya.
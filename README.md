# News Aggregator Backend

A Laravel-based news aggregator that fetches articles from multiple sources (The Guardian, NewsAPI, NY Times) and provides a unified REST API with automatic background processing using Laravel Horizon.

## Table of Contents

-   [Features](#features)
-   [Requirements](#requirements)
-   [Installation](#installation)
-   [Configuration](#configuration)
-   [Usage](#usage)
-   [API Documentation](#api-documentation)
-   [Docker Services](#docker-services)

## Features

-   **Multi-source aggregation**: Fetches news from The Guardian, NewsAPI.org, and NY Times
-   **Unified API**: Single REST API for all news sources
-   **Background Processing**: Laravel Horizon for queue management
-   **Automatic Scheduling**: Fetches news every 15 minutes automatically
-   **Smart Caching**: Redis-based caching for performance
-   **Advanced Search**: Filter by keyword, source, category, author, and date range
-   **Docker Ready**: Complete Docker setup with all services

### Design Patterns Used

-   **Repository Pattern**: Separates data access logic
-   **Adapter Pattern**: Normalizes different API responses
-   **Service Layer**: Encapsulates business logic
-   **CQRS**: Separate Read and Write repositories

### Requirements

-   Docker & Docker Compose
-   PHP 8.4+
-   Redis
-   MySQL/PostgreSQL/SQLite
-   Composer

## Installation

### Quick Start

```bash
# Clone the repository
git clone https://github.com/LucasVSCS/news-aggregator.git
cd news-aggregator

# Create and edit the env file
cp .env.example .env

# Run automated setup
chmod +x setup.sh
./setup.sh
```

## Configuration

### Environment Variables

Edit your `.env` file:

```env
# Application
APP_NAME=NewsAggregator
APP_ENV=local
APP_URL=http://localhost:61000

# Database
DB_CONNECTION=sqlite
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=

# Redis (Docker service name)
REDIS_HOST=redis
REDIS_PORT=6379
REDIS_CLIENT=phpredis

# Queue & Cache
QUEUE_CONNECTION=redis
CACHE_STORE=redis
SESSION_DRIVER=redis

# News API Keys
GUARDIAN_API_KEY=your_guardian_api_key_here
NEWSAPI_API_KEY=your_newsapi_key_here
NYTIMES_API_KEY=your_nytimes_api_key_here
```

### Getting API Keys

#### The Guardian

1. Visit: https://open-platform.theguardian.com/access/
2. Register and get your API key
3. Free tier: ~5000 requests/day

#### NewsAPI.org

1. Visit: https://newsapi.org/register
2. Register (free plan: 100 requests/day)
3. Get your API key

#### New York Times

1. Visit: https://developer.nytimes.com/get-started
2. Create an app and get API key
3. Free tier: 500 requests/day

## Usage

### Fetching News

```bash
# Fetch from all sources
docker-compose exec php-fpm php artisan news:fetch

# Fetch from specific source
docker-compose exec php-fpm php artisan news:fetch --source=guardian
docker-compose exec php-fpm php artisan news:fetch --source=newsapi
docker-compose exec php-fpm php artisan news:fetch --source=nytimes
```

## API Documentation

Base URL: `http://localhost:61000/api/v1`

### Articles Endpoints

#### List All Articles

```bash
GET /articles
```

**Query Parameters:**

-   `keyword` (string): Search in title, description, content
-   `sources[]` (array): Filter by source IDs
-   `categories[]` (array): Filter by category IDs
-   `authors[]` (array): Filter by author names
-   `date_from` (date): Start date (YYYY-MM-DD)
-   `date_to` (date): End date (YYYY-MM-DD)
-   `per_page` (int): Items per page (default: 20, max: 100)

**Example:**

```bash
curl "http://localhost:61000/api/v1/articles?keyword=technology&sources[]=1&per_page=10"
```

**Response:**

```json
{
    "data": [
        {
            "id": 1,
            "title": "Article Title",
            "description": "Article description...",
            "content": "Full content...",
            "url": "https://source.com/article",
            "image_url": "https://source.com/image.jpg",
            "author": "John Doe",
            "published_at": "2024-11-09 10:00:00",
            "source": {
                "id": 1,
                "name": "The Guardian",
                "slug": "guardian"
            },
            "categories": [
                {
                    "id": 1,
                    "name": "Technology",
                    "slug": "technology"
                }
            ]
        }
    ],
    "meta": {
        "total": 150,
        "per_page": 20,
        "current_page": 1,
        "last_page": 8
    }
}
```

#### Get Single Article

```bash
GET /articles/{id}
```

#### Get Latest Articles

```bash
GET /articles/latest
```

Returns the 20 most recent articles.

#### Get Articles by Source

```bash
GET /articles/source/{sourceId}
```

#### Get Articles by Category

```bash
GET /articles/category/{categoryId}
```

#### Get Unique Authors

```bash
GET /articles/authors
```

Returns a list of all unique author names.

### Sources Endpoints

#### List All Sources

```bash
GET /sources
```

#### List Active Sources

```bash
GET /sources/active
```

#### Get Single Source

```bash
GET /sources/{id}
```

#### Create Source

```bash
POST /sources
Content-Type: application/json

{
  "name": "BBC News",
  "slug": "bbc",
  "url": "https://www.bbc.com/"
}
```

#### Update Source

```bash
PUT /sources/{id}
Content-Type: application/json

{
  "name": "Updated Name",
  "slug": "updated-slug",
  "url": "https://www.updated-url.com/"
}
```

#### Toggle Source Active Status

```bash
POST /sources/toggle-active/{id}
```

#### Delete Source

```bash
DELETE /sources/{id}
```

### Categories Endpoints

#### List All Categories

```bash
GET /categories
```

#### Get Single Category

```bash
GET /categories/{id}
```

### Example Queries

**Search for climate news from Guardian in the last week:**

```bash
curl "http://localhost:61000/api/v1/articles?keyword=climate&sources[]=1&date_from=2024-11-01"
```

**Get technology articles:**

```bash
curl "http://localhost:61000/api/v1/articles?categories[]=1"
```

**Complex search:**

```bash
curl "http://localhost:61000/api/v1/articles?keyword=AI&sources[]=1&sources[]=2&categories[]=1&date_from=2024-11-01&per_page=50"
```

## Docker Services

-   **webserver** - Nginx (port 61000)
-   **php-fpm** - Laravel application
-   **horizon** - Queue worker
-   **scheduler** - Cron jobs
-   **redis** - Cache and queues

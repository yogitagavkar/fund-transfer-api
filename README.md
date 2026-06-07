# 🏦 Fund Transfer API

## Project Overview

A secure REST API built with Laravel for transferring funds between accounts.

The solution focuses on:

* Transaction integrity
* Concurrent request handling
* Idempotent transfers
* Balance validation
* Redis integration
* Unit and Feature testing
* Docker support

Technology Stack:

* PHP 8.3
* Laravel 13.8
* MySQL 8
* Redis
* PHPUnit
* Docker

---

# Architecture

The application follows a layered architecture:

```text
Controller
    ↓
Request Validation
    ↓
DTO
    ↓
Service Layer
    ↓
Repository Layer
    ↓
Database
```

### Components

* Controllers: Handle HTTP requests
* Form Requests: Validation
* DTOs: Data transfer objects
* Services: Business logic
* Repositories: Database operations
* Models: Eloquent entities
* Resources: API responses
* Exceptions: Centralized error handling

---

# Setup Instructions

## Clone Repository

```bash
git clone <repository-url>
cd fund-transfer-api
```

## Install Dependencies

```bash
composer install
```

## Configure Environment

```bash
cp .env.example .env
```

Update database configuration:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=fund_transfer
DB_USERNAME=root
DB_PASSWORD=password
```

Generate application key:

```bash
php artisan key:generate
```

Create database:

```sql
CREATE DATABASE fund_transfer;
```

Run migrations and seeders:

```bash
php artisan migrate
php artisan db:seed
```

Start application:

```bash
php artisan serve
```

Application URL:

```text
http://127.0.0.1:8000
```

---

# Docker Instructions

Build containers:

```bash
docker compose build
```

Start containers:

```bash
docker compose up -d
```

Run migrations:

```bash
docker exec -it fund-transfer-app bash

php artisan migrate

php artisan db:seed
```

---

# API Endpoints

| Method | Endpoint                       |
| ------ | ------------------------------ |
| POST   | /api/v1/transfers              |
| GET    | /api/v1/accounts/{id}/balance  |
| GET    | /api/v1/transfers/{id}         |
| POST   | /api/v1/transfers/{id}/reverse |

---

# Sample Requests

## Create Transfer

```http
POST /api/v1/transfers
```

```json
{
    "from_account_id": 1,
    "to_account_id": 2,
    "amount": 100,
    "reference_id": "TXN001"
}
```

## Get Balance

```http
GET /api/v1/accounts/1/balance
```

---

# Sample Responses

## Successful Transfer

```json
{
    "success": true,
    "message": "Transfer completed successfully",
    "data": {
        "id": 1,
        "amount": 100
    }
}
```

## Insufficient Balance

```json
{
    "success": false,
    "message": "Insufficient balance"
}
```

---

# Testing

Run all tests:

```bash
php artisan test
```

Run Unit Tests:

```bash
php artisan test --testsuite=Unit
```

Run Feature Tests:

```bash
php artisan test --testsuite=Feature
```

Covered scenarios:

### Unit Tests

* Successful Transfer
* Idempotent Transfer
* Insufficient Balance
* Same Account Transfer
* Account Not Found
* Reversal Flow

### Feature Tests

* Transfer API
* Validation Failures
* Balance API
* Reversal API
* Idempotency API

---

# Redis Usage

Redis is used for:

* Application cache
* Session storage
* Queue support (future scalability)

Configuration:

```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

---

# Concurrency Handling

To prevent race conditions during transfers:

```php
lockForUpdate()
```

is used on both accounts within a database transaction.

This ensures:

* No double spending
* Consistent balances
* Safe concurrent transfers

---

# Idempotency Strategy

Transfers support a unique:

```text
reference_id
```

Before processing a transfer:

```php
Transaction::where(
    'reference_id',
    $referenceId
)->first();
```

If a transaction already exists, the existing record is returned instead of creating a duplicate transfer.

This prevents duplicate fund movements caused by retries or network failures.

---

## High Load Considerations

The application is designed to handle concurrent financial transactions safely and efficiently.

### Database Transactions

All fund transfers are executed within database transactions to guarantee atomicity.

```php
DB::beginTransaction();
...
DB::commit();
```

### Row-Level Locking

Accounts involved in a transfer are locked using:

```php
lockForUpdate()
```

This prevents concurrent balance modifications and eliminates race conditions.

### Redis Distributed Locking

To prevent duplicate concurrent processing of the same transfer request, Laravel Cache Locks backed by Redis are used.

Example:

```php
$lock = Cache::lock(
    "transfer_lock:{$referenceId}",
    30
);

if (! $lock->get()) {
    throw new InvalidTransferException(
        'Transfer already being processed'
    );
}
```

Benefits:

* Prevents duplicate processing
* Supports horizontally scaled environments
* Protects against concurrent retries
* Improves transaction safety

### API Rate Limiting

Rate limiting is implemented using Laravel RateLimiter.

Transfer endpoints:

* 20 requests per minute per user/IP

Balance endpoints:

* 100 requests per minute per user/IP

Benefits:

* Prevents abuse
* Protects infrastructure
* Mitigates brute-force attacks
* Improves overall platform stability

### Queue-Based Processing

Fund transfers are processed synchronously to ensure consistency.

Non-critical operations are processed asynchronously:

* Audit logging
* Notifications
* Analytics events

Queues are backed by Laravel Queue Workers.

Start worker:

```bash
php artisan queue:work
```

### Database Optimization

The application uses:

* Unique constraint on `reference_id`
* Indexed lookup columns
* Transaction-level locking
* Atomic balance updates

### Scalability Strategy

Future enhancements:

* Read replicas
* Horizontal scaling
* Event-driven architecture
* Kafka integration
* Transaction partitioning
* Monitoring with Prometheus and Grafana

------------------------------------
# Time Spent

Approximately 4-5 hours

---

# AI Tools Used

* ChatGPT
* GitHub Copilot

AI tools were used for architecture discussions, edge-case analysis, and test case generation. All code was reviewed, modified, and understood before implementation.

AI assistance was used for:
- Architecture discussion
- Concurrency handling approaches
- Test case generation
- Code review and validation
- Docker configuration guidance
# Flashsale-API

A Laravel API for managing flash sales, stock holds, orders, and payment webhooks with idempotency and concurrency safety.


## About Flashsale API

This API allows:
- Reserving products temporarily using "holds"
- Creating orders from holds
- Updating stock automatically after orders
- Handling payments via webhooks (idempotent and out-of-order safe)
- Monitoring jobs, requests, and cache using Laravel Telescope


## Installation

1. Clone repo: `git clone https://github.com/KarimZakaria/Flashsale-Api`
2. Install dependencies: `composer install`
3. Copy env: `cp .env.example .env` and configure DB + Redis
4. Generate app key: `php artisan key:generate`
5. Run migrations & seeders: `php artisan migrate --seed`
6. Run queue worker: `php artisan queue:work redis --tries=3 -vvvv`
7. Serve app: `php artisan serve`

## API Endpoints

### Get Products
GET /api/products/1    

### Create Hold
POST /api/holds
```json
{
  "product_id": 1,
  "quantity": 2,
  "user_id": 1
}
```

### Create Order
POST /api/orderds
```json
{
  "hold_id": 1
}
```

### Create Payment webhook
POST /api/payments/webhook
```json
{
  "payment_id": "TX12345",
  "status": "paid"  // or "failure"
}




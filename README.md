# About Carob Mailer

Transactional Email API Service built on top of laravel

## Installation

1. Clone the repo

``` git clone https://github.com/tuxonice/carob-mailer.git carob-mailer ```

``` cd carob-mailer ```

2. Install dependencies

``` composer install --no-dev```

3. Build frontend

``` npm install ```

``` npm run prod ```

4. Create .env file

``` cp .env.example .env ```

5. Generate application key

``` php artisan generate:key ```

6. Setup database and mail credentials in .env file


7. Setup webserver to point document root to public folder


## API Usage

### Endpoint

```
http://api.[domain]/mailer/send/
```

### Payload

```
{
    "from": {
        "name": "Acme Inc."
    },
    "to": {
        "name": "Jonh Doe",
        "email": "user@example.com"
    },
    "subject": "Email subject",
    "body": {
        "text": "Simplicity is the essence of happiness.",
        "html": "<i>Simplicity</i> is the essence of <b>happiness.</b>"
    }
}
```

### Curl example

````
curl --location --request POST 'http://api.carob-mailer.local/mailer/send' \
--header 'Accept: application/json' \
--header 'Authorization: Bearer 12|tgWanTyitSs3GqaaaqnmrLEpxr7wRUZY0VCRfLK' \
--header 'Content-Type: application/json' \
--data-raw '{
    "from": {
        "name": "Acme Inc."
    },
    "to": {
        "name": "Jonh Doe",
        "email": "user@example.com"
    },
    "subject": "Email subject",
    "body": {
        "text": "Simplicity is the essence of happiness.",
        "html": "<i>Simplicity</i> is the essence of <b>happiness.</b>"
    },
    "attachments": [
        {
            "base64Content": "VGhpcyBpcyBhIGJhc2UgNjQgc3RyaW5n",
            "originalFileName": "sample.txt"
        }   
    ]
}'
````

## Dashboard Usage

Go to ```http://[domain]```

1. Login or create a new account

2. Create new authentication api token to be used on api request

## Country-Based Access Restriction

Carob Mailer includes an IP address blocking middleware that can restrict API access based on the country of origin.

### Configuration

To enable country-based access restriction, add the following to your `.env` file:

```
ALLOW_COUNTRY_CODE=us
```

Replace `us` with the two-letter country code you want to allow. The comparison is case-insensitive.

### How It Works

- When configured, the middleware checks the origin country of each request using the IP-API service
- Only requests from the specified country are allowed
- Requests from other countries receive a 404 Not Found response
- If no country code is specified, all requests are allowed

### Middleware Registration

The middleware is already registered in the HTTP kernel. To apply it to specific routes, add it to your route definitions:

```php
Route::middleware(['ip.blocker'])->group(function () {
    // Protected routes
});
```


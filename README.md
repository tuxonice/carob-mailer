# About Carob Mailer

Transactional Email API Service built on top of laravel

## Installation

1. Clone the repo

``` git clone https://github.com/tuxonice/carob-mailer.git carob-mailer ```

``` cd carob-mailer ```

2. Install dependencies

``` vendor/bin/sail composer install --no-dev ```

3. Build frontend assets

``` vendor/bin/sail composer copy-assets ```

4. Create .env file

``` cp .env.example .env ```

5. Generate application key

``` php artisan generate:key ```

6. Setup database and mail credentials in .env file

7. Setup webserver to point document root to public folder

8. Setup Laravel scheduler (add `* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1` to crontab)


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

## Email Delivery

Emails are queued via Laravel jobs and processed by queue workers. A cron-based fallback command (`emails:send-pending`) runs every minute to process any pending emails if queue workers are unavailable or stopped.

The scheduler automatically handles both:
- Running `queue:work --stop-when-empty` every minute
- Running `emails:send-pending` every minute as a fallback

You can manually trigger the fallback command if needed:

```
php artisan emails:send-pending --limit=200
```

## Country-Based Access Restriction

Carob Mailer includes an IP address blocking middleware that can restrict API access based on the country of origin.

### Configuration

To enable country-based access restriction, add the following to your `.env` file:

```
ALLOW_COUNTRY_CODE=us
ALLOW_IPS="192.168.1.100,10.0.0.1"
```

Replace `us` with the two-letter country code you want to allow. The comparison is case-insensitive.

`ALLOW_IPS` accepts a comma-separated list of IP addresses that will bypass the country check. Leave it empty to disable IP-based allowlisting.

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


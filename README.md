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



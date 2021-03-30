#rest-api-request-failover-example

Simple client-server example how to handle failure connection between services via REST API.

---
This example implementation contains two guzzle middlewares:

FailedTransportMiddleware - store request when service response is incorrect. We can retry requests when we are sure, that the service is up and running. Default storage is in memory.

FailureDetectionMiddleware - circuit breaker pattern, see https://github.com/ackintosh/ganesha

##Run

Start server
```
symfony serve --port 8000
```

in .env & .env.test you can set different port. Default connection doesn't have SSL certificate configured.


Run command to see how it works
 ```
php bin/console action fail
 ```
There are 3 types of actions:
- `success` - always return status 200
- `fail` - always return status 500
- `unstable` - random from [`success`, `fail`] 
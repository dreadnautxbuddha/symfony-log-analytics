# Overview
This command reads through a growing log file and saves each row into the database that looks like this:
```text
USER-SERVICE - - [17/Aug/2018:09:21:53 +0000] "POST /users HTTP/1.1" 201
USER-SERVICE - - [17/Aug/2018:09:21:54 +0000] "POST /users HTTP/1.1" 400
INVOICE-SERVICE - - [17/Aug/2018:09:21:55 +0000] "POST /invoices HTTP/1.1" 201
USER-SERVICE - - [17/Aug/2018:09:21:56 +0000] "POST /users HTTP/1.1" 201
USER-SERVICE - - [17/Aug/2018:09:21:57 +0000] "POST /users HTTP/1.1" 201
INVOICE-SERVICE - - [17/Aug/2018:09:22:58 +0000] "POST /invoices HTTP/1.1" 201
```

There are a couple of API endpoints used to interact with the log entries. Refer to the [OpenAPI spec](../openapi.yaml) for more information.

# Console Command
This long-running console command will be responsible for importing log files that exist in the filesystem, and can be run like so:

```shell
php bin/console import:log:local <path>
```

This will start importing each one of the file to the database.

## Pagination
However, what if you want to only import specific lines? Luckily, you can instruct the command to only start importing from specific lines, as well as limit how many
you want to import:

```shell
php bin/console import:log:local <path> [--offset=<value>] [--limit=<value>]
```

## Performance
All database operations made by the importer are chunked, reducing the number of writes made to the database. By default, up to a maximum of `500` lines are inserted at a time
but you can change this by specifying the chunk size to the command like so:

```shell
php bin/console import:log:local <path> [--chunk-size=<value>]
```

### Memory Usage
On your local machine, you may encounter memory issues when running the command:

```shell
PHP Fatal error:  Allowed memory size of 134217728 bytes exhausted (tried to allocate 20480 bytes) in /app/vendor/monolog/monolog/src/Monolog/Formatter/NormalizerFormatter.php on line 227

Fatal error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 20480 bytes) in /app/vendor/monolog/monolog/src/Monolog/Formatter/NormalizerFormatter.php on line 227
PHP Fatal error:  Allowed memory size of 134217728 bytes exhausted (tried to allocate 32768 bytes) in /app/vendor/symfony/error-handler/Error/OutOfMemoryError.php on line 1

Fatal error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 32768 bytes) in /app/vendor/symfony/error-handler/Error/OutOfMemoryError.php on line 1
```

This has been a known issue and can be [fixed](https://github.com/symfony/monolog-bundle/issues/118#issuecomment-92449647) by adding the `--no-debug` flag to the command:

```shell
php bin/console import:log:local <path> [--no-debug]
```

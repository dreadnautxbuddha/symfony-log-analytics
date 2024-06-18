# Overview
This application reads through a growing log file and saves each row into the database that looks like this:
```text
USER-SERVICE - - [17/Aug/2018:09:21:53 +0000] "POST /users HTTP/1.1" 201
USER-SERVICE - - [17/Aug/2018:09:21:54 +0000] "POST /users HTTP/1.1" 400
INVOICE-SERVICE - - [17/Aug/2018:09:21:55 +0000] "POST /invoices HTTP/1.1" 201
USER-SERVICE - - [17/Aug/2018:09:21:56 +0000] "POST /users HTTP/1.1" 201
USER-SERVICE - - [17/Aug/2018:09:21:57 +0000] "POST /users HTTP/1.1" 201
INVOICE-SERVICE - - [17/Aug/2018:09:22:58 +0000] "POST /invoices HTTP/1.1" 201
```

A `GET /count` endpoint will be used to count the number of log messages using under a specified criteria. Refer to the OpenAPI spec for more information. 

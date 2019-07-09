# PHP call Kafka

## Setup

- Product is developing on PHP.7.1

- Setup rdkafka library:
```js
Download rdkafka

In Linux
librdkafka is available in source form and as precompiled binaries for Debian and Redhat based Linux distributions. Most users will want to use the precompiled binaries.
    
    For CentOS
    - sudo yum install librdkafka-devel

    For Ubuntu
    - sudo apt-get install librdkafka-dev

    For macOS
    - brew install librdkafka

In Window
Download binary <a href="https://pecl.php.net/package/rdkafka/3.0.5/windows">php_rdkafka</a>
Copy php_rdkafka.dll to C:\php\ext folder (environment), then change name to rdkafka.dll
Copy librdkafka.dll to C:\php folder (environment)
```

## Running

### Run source by command line

```js
php consumer.php
php consumer1.php

php producer.php
```
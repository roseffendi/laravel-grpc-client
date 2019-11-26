#
# Installing composer dependecies
#

FROM composer:1.9 as vendor

WORKDIR /app

COPY . .

RUN composer install --no-ansi --no-dev --no-interaction --no-plugins --no-progress --no-scripts --no-suggest --optimize-autoloader

#
# Compile proto file
#

FROM alpine:3.10.3 as grpc

RUN apk add --update --no-cache \
         --repository=http://dl-cdn.alpinelinux.org/alpine/edge/main \
         --repository=http://dl-cdn.alpinelinux.org/alpine/edge/community \
         grpc

WORKDIR /app

COPY --from=vendor /app .

RUN protoc --plugin=protoc-gen-grpc=/usr/bin/grpc_php_plugin --grpc_out=./generated  --php_out=./generated protos/**/*.proto

#
# Build app image
#

FROM php:7.2-fpm-alpine

RUN apk add --update --no-cache --virtual .build-deps \
        curl \
        autoconf \
        gcc \
        make \
        g++ \
        zlib-dev

RUN apk add --update --no-cache libstdc++

WORKDIR /var/www

RUN pecl install grpc protobuf

RUN docker-php-ext-install pdo_mysql bcmath

RUN docker-php-ext-enable grpc protobuf

COPY --from=grpc /app .

RUN apk del .build-deps
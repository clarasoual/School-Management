#!/bin/bash
php-fpm -D
sleep 3
nginx -g "daemon off;"
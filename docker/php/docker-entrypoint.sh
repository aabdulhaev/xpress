#!/usr/bin/env bash

composer update
php yii migrate --interactive=0

exec "$@"

#!/usr/bin/env sh
set -eu

envsubst '${COMPOSE_PROJECT_NAME}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

exec "$@"
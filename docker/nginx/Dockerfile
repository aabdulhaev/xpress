FROM nginx:latest

ARG NGINX_HOST=development
ARG NGINX_PORT=1060
ARG NGINX_PORT_SSL=1065

ENV NGINX_HOST $NGINX_HOST
ENV NGINX_PORT $NGINX_PORT
ENV NGINX_PORT_SSL $NGINX_PORT_SSL

COPY "$NGINX_HOST".conf.template /etc/nginx/conf.d/default.conf.template

COPY docker-entrypoint.sh /usr/local/bin/

ENTRYPOINT ["docker-entrypoint.sh"]

CMD ["nginx", "-g", "daemon off;"]
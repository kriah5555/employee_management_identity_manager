#!/bin/bash

envsubst '${IDENTITY_MANAGER_SERVER_NAME}' < /etc/nginx/nginx.conf.template > /etc/nginx/nginx.conf

# Start Nginx
exec nginx -g 'daemon off;'
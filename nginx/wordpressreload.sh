#!/bin/bash

ln -s /etc/nginx/sites-available/wordpress.conf /etc/nginx/sites-enabled/wordpress.conf
#exec nginx -s reload
nginx -s reload
#exec /usr/bin/supervisord -n -c /etc/supervisord.conf

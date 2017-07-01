#!/bin/sh

mkdir -p /var/lib/varnish/`hostname` && chown nobody /var/lib/varnish/`hostname`
varnishd -s malloc,${VARNISH_MEMORY} -a :80 -f /etc/varnish/wordpress.vcl
#varnishd -s malloc,${VARNISH_MEMORY} -a :80 -b ${VARNISH_BACKEND_ADDRESS}:${VARNISH_BACKEND_PORT} -f /etc/varnish/wordpress.vcl
sleep 1
varnishlog

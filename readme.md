How to Build nginx and Varnish images
nginx
1. cd nginx
2. docker build -t nginx:production .
3. cd .. 

varnish
1. cd varnish
2. docker build -t varnish:production .
3. cd ..

How to Run Stack deploy.
docker stack deploy -c docker-compose.yml wordpress

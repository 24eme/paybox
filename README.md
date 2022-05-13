# Pour lancer php en 5.4
```
podman build -t php-fpm-5.4 .
podman run --name php-fpm-5.4 -v $PWD:$PWD --network="host" localhost/php-fpm-5.4
```

Puis configurer nginx / apache pour passer le php sur le port 9000

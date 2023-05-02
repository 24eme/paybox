# Interface pour payer en ligne sur Paybox

## Installation

```
git clone <repo>
cd <dir>
composer install --no-dev
cp db-dist.ini db.ini
```

Configurer le db.ini en fonction de la base. Vérifier que le fichier phinx.php
est bien configurer et faire un : `vendor/bin/phinx migrate`

Il faut également modifier l'adresse mail dans `define.php`.

## Update

```
git pull
composer install
vendor/bin/phinx migrate
```

## Pour lancer php en 5.4
```
podman build -t php-fpm-5.4 .
podman run --name php-fpm-5.4 -v $PWD:$PWD --network="host" localhost/php-fpm-5.4
```

Puis configurer nginx / apache pour passer le php sur le port 9000

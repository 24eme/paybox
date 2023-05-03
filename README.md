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

## Migration vers PostgreSQL

#### Prérequis

[pgloader](https://pgloader.readthedocs.io/en/latest/index.html)

#### Migration

`pgloader ./db/convert-to-pgsql.load`

Si pgloader n'est pas packagé, l'alternative podman / docker fonctionne comme ceci :

`podman run --rm --network=host -it -v $PWD:$HOME dimitri/pgloader:latest pgloader $HOME/db/convert-to-pgsql.load`

Vérifier les infos de connexion des bases dans le script [convert-to-pgsql.load](./db/convert-to-pgsql.load)

## Pour lancer php en 5.4
```
podman build -t php-fpm-5.4 .
podman run --name php-fpm-5.4 -v $PWD:$PWD --network="host" localhost/php-fpm-5.4
```

Puis configurer nginx / apache pour passer le php sur le port 9000

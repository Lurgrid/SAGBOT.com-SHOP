#  <center>__Lemauvaiscoin__<center/>
Create on 06/05/2022

## 1 - Configuration du serveur MySQL

- Pour changer les valeurs des constantes utiliser sur le site vous devez modifier le fichier php [.mysql.php](./src/.mysql.php) _(./src/.mysql.php)_.

Ressemblant à ceci :

```php
  define("MYSQL_LOG", "Account");
  
  define("MYSQL_HOST", "Host");
  
  define("MYSQL_PWD", "Password");
  
  define("MYSQL_DB", "DataBase");
```

La constante __MYSQL_LOG__ est le login de votre compte MySQL de votre serveur.  <br />
La constante __MYSQL_HOST__ est l'adresse de votre serveur MySQL.  <br />
La constante __MYSQL_PWD__ est le mot de passe de votre compte MySQL. <br />
La constante __MYSQL_DB__ est la DataBase que le site doit utiliser.  <br />

- Pour le bon fonctionnement du site web, quelques valeurs doivent être changées.

La constante de la taille maximale des packets _(max_allowed_packet)_ doit au moins être égale à 9 Mo.

Pour le bon fonctionnement du site web les tables "account" et "classified" ne devrons pas être utilisé pas un autre programme connecté a la DataBase.
Vous n'aurez pas besoin de les crée au préalable la création se fera automatiquement par le site web s'il détecte qu'elles n'existent pas.

## 2 - La clef de chiffrement (RSA)

Les 2 fichiers [private.key](./src/.private.key) et [public.key](./src/.public.key) _(./src/.private.key && .public.key)_

Ces fichiers contiennent respectivement la clef privée et la clef publique de chiffrement des mots de passe qui servent a la création du  _Token[^1]_.

Ces fichiers sont des clefs [RSA][ref1] générées à partir de [OpenSSL][ref2] la clef privée est une clef de 2048 bit Token.

Ces fichiers sont obligatoires pour le bon fonctionnement du site web.

[ref1]: https://fr.wikipedia.org/wiki/Chiffrement_RSA
[ref2]: https://fr.wikipedia.org/wiki/OpenSSL
[^1]: Suite de caractères authentifiant l'utilisateur durant sa navigation sur le site web, le token se situe dans le cookie du navigateur.

## 3 - Les categories des annonces

Si vous voulez voir/modifier les catégories du site web, il suffit d'aller sur le fichier [.config.php](./src/.config.php) _(./src/.config.php)_.

Ressemblant à ceci :

```php
$category = [
    "Vacations",
    "Employment",
    "Vehicles",
    "Real Estate",
    "Fashion",
    "Home",
    "Multimedia",
    "Leisure",
    "Pets",
    "Professional Equipment",
    "Service",
    "Other"
]
```

## 4 - .htaccess

Le fichier [.htaccess](.htaccess) est le fichier de configuration du serveur (apache) celui présent pas défaut est très basique.

Il ressemble a ceci 
```html
Options -Indexes

<FilesMatch "^\.">
Order allow,deny
Deny from all
</FilesMatch>

ErrorDocument 403 /error/?code=403
ErrorDocument 404 /error/?code=404
ErrorDocument 500 /error/?code=500
```

La partie centrale avec les balises "FileMatch" interdit aux utilisateurs du site web d'accéder au fichier commençant par un "." _(pour des raisons de sécurité du site web)_

Les 3 dernières lignes permettent de dire au serveur que s'il y a une erreur du type 403, 404 ou 500 alors il doit renvoyer la page donnée comme second paramètre.

##  5 - La page d'Administration

Pour accéder a la page d'administration du site web il suffit d'aller sur [www.exemple.com/admin](./admin/index.php) _(ou exemple.com représente votre domaine et que le site web se trouve a la racine de votre serveur)_

Normalement quand vous vous rendrez sur cette page on vous demandera un mots de passe, le mots de passe par défaut est "Admin123@", si vous voulez le changer manuellement le mots de passe se trouve dans le fichier [.admin_config.json](./src/.admin_config.json) _(./src/.admin_config.json)_

Ressemblant à ceci :

```json
{ "password": "Admin123@" }
```

Bien sûr, ce n'est pas le seul moyen de le modifier pour cela, vous pouvez aller sur le profil du compte admin (en étant connecté) entrez le mot de passe actuel et mettre le nouveau mot de passe.

# <center> Et voilà vous savez tout pour mettre le site web en place <center/>



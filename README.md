# Panier-Malin-Amiens
Comparer les prix de divers produits entre plusieurs enseignes locales et ecommerces

## Documentation du Projet Panier Malin Amiens

### Pré-requis

1. **Docker** : pour gérer la base de données MySQL.
2. **Node.js** et **npm** : pour exécuter le code en JavaScript et gérer les dépendances.

### Lancer Docker :

```bash
docker-compose up -d

```

### Lancer le serveur :

```bash
docker exec -it panier-malin-amiens-app-1 php -S 0.0.0.0:8000 -t public/

```

### Pour commande symfony :

```bash
docker exec -it panier-malin-amiens-app-1 php bin/console make:

```

OU

```bash
docker exec -it panier-malin-amiens-app-1 php bin/console doctrine:


```

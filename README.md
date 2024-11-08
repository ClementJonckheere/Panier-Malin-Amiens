# Panier-Malin-Amiens
Comparer les prix de divers produits entre plusieurs enseignes locales et ecommerces

## Documentation du Projet Panier Malin Amiens

### Pré-requis

1. **Docker** : pour gérer la base de données MySQL.
2. **Node.js** et **npm** : pour exécuter le code en JavaScript et gérer les dépendances.

Lancer Docker :

docker-compose up —build -d

**(Si necessaire) Synchroniser le Modèle de Données** avec la base de données MySQL pour créer les tables :

```bash
node script/sync.js

```

### Vérification de la Connexion

Pour vérifier que la connexion et la table sont correctement configurées, vous pouvez vous connecter à MySQL via Docker :

```bash
docker exec -it panier_malin_db mysql -u root -p

```


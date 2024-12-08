# Projet Panier Malin
Panier Malin est une API développée en Symfony qui permet de gérer, comparer et manipuler des données produits entre différents supermarchés.

## 🚀 Fonctionnalités
- Lister les produits avec leurs prix par site.
- Comparer les prix d'un produit entre plusieurs sources.
- Ajouter, modifier ou supprimer un produit.
- API REST avec documentation Swagger.

## 📋 Prérequis
Outils nécessaires :
- Docker
- PHP
- Composer
- MySQL

## 🛠 Installation
1. Cloner le projet :
```
git clone https://github.com/votre-utilisateur/projet_panier_malin.git
cd projet_panier_malin
```

2. Démarrer les services Docker :
```
docker-compose up -d
```

3. Installer les dépendances :
```
docker-compose exec app composer install
```

4. Créer la base de donnée :
```
docker-compose exec app php bin/console doctrine:database:create
```

5. Executer les migrations :
```
docker-compose exec app php bin/console doctrine:migrations:migrate
```

##  🌐 Tester l'API
L'API est accessible sur http://localhost:8001.

### Routes principales :
Méthode	URL	Description
- GET	/api/produits	Lister tous les produits
- GET	/api/produits/{id}	Détails d'un produit
- POST	/api/produits	Ajouter un produit
- PUT	/api/produits/{id}	Mettre à jour un produit
- DELETE	/api/produits/{id}	Supprimer un produit
- GET	/api/comparaison/{type}	Comparer les prix d'un type de produit entre les sites
  
### Requêtes avec cURL :

1. Lister les produits :
```
curl -X GET http://localhost:8001/api/produits
```

2. Ajouter un produit :
```
curl -X POST http://localhost:8001/api/produits \
-H "Content-Type: application/json" \
-d '{"name":"Riz Basmati","price":2.99,"pricePerKg":5.98,"type":"riz","source":"bi1"}'
```

3. Comparer les prix pour un type de produit :
```
curl -X GET http://localhost:8001/api/comparaison/riz
```






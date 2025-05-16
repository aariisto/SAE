# 🚲 Vélib WebApp - Plateforme Web de Gestion des Stations Vélib

## 📱 À propos du projet

Vélib WebApp est une application web PHP développée qui permet aux utilisateurs de localiser et d'obtenir des informations sur les stations Vélib (vélos en libre-service) à Paris. L'application affiche une carte interactive avec toutes les stations disponibles, fournit des détails sur chaque station et permet aux utilisateurs de rechercher des stations spécifiques, de consulter leur historique de recherches et de gérer leurs réservations.

> **Configuration Docker** : La configuration Docker complète se trouve ici : [https://github.com/aariisto/Docker_Velib_WebApp](https://github.com/aariisto/Docker_Velib_WebApp)

> **Architecture du système** : Cette application web est constituée d'un frontend PHP et d'un backend mixte PHP/Python. Le backend Python sert de proxy pour récupérer et formater les données des stations Vélib, tandis que le backend PHP gère l'authentification, les recherches et les réservations.

## ✨ Fonctionnalités

- 🗺️ **Carte interactive** affichant toutes les stations Vélib
- 📍 **Géolocalisation** pour trouver les stations à proximité
- 🔍 **Recherche de stations** par nom ou adresse
- 📋 **Historique des recherches** pour chaque utilisateur
- 🔒 **Authentification sécurisée** des utilisateurs avec JWT
- 📱 **Interface utilisateur responsive** adaptée à tous les appareils
- 🚴 **Gestion des réservations** de vélos

## 🛠️ Technologies utilisées

- 🌐 &nbsp;**Frontend**
  ![HTML5](https://img.shields.io/badge/-HTML5-333333?style=flat&logo=html5)
  ![CSS3](https://img.shields.io/badge/-CSS3-333333?style=flat&logo=css3)
  ![JavaScript](https://img.shields.io/badge/-JavaScript-333333?style=flat&logo=javascript)
  ![Bootstrap](https://img.shields.io/badge/-Bootstrap-333333?style=flat&logo=bootstrap)

- ⚙️ &nbsp;**Backend**
  ![PHP](https://img.shields.io/badge/-PHP-333333?style=flat&logo=php)
  ![Python](https://img.shields.io/badge/-Python-333333?style=flat&logo=python)
  ![Apache](https://img.shields.io/badge/-Apache-333333?style=flat&logo=apache)
  ![Flask](https://img.shields.io/badge/-Flask-333333?style=flat&logo=flask)
  ![MySQL](https://img.shields.io/badge/-MySQL-333333?style=flat&logo=mysql)

- 🔐 &nbsp;**Authentification & Sécurité**
  ![JWT](https://img.shields.io/badge/-JWT-333333?style=flat&logo=json-web-tokens)
  ![Sessions PHP](https://img.shields.io/badge/-Sessions%20PHP-333333?style=flat&logo=php)
  ![CSRF Protection](https://img.shields.io/badge/-CSRF%20Protection-333333?style=flat&logo=security)

- 🗺️ &nbsp;**Cartographie**
  ![Leaflet](https://img.shields.io/badge/-Leaflet-333333?style=flat&logo=leaflet)
  ![Geocoding API](https://img.shields.io/badge/-Geocoding%20API-333333?style=flat&logo=googlemaps)

- 🌐 &nbsp;**API & Réseau**
  ![REST API](https://img.shields.io/badge/-REST%20API-333333?style=flat&logo=api)
  ![Vélib API](https://img.shields.io/badge/-V%C3%A9lib%20API-333333?style=flat&logo=biking)

- 🔧 &nbsp;**Outils de développement**
  ![Git](https://img.shields.io/badge/-Git-333333?style=flat&logo=git)
  ![Docker](https://img.shields.io/badge/-Docker-333333?style=flat&logo=docker)
  ![VS Code](https://img.shields.io/badge/-VS%20Code-333333?style=flat&logo=visual-studio-code&logoColor=007ACC)

## 📂 Structure du projet

```
Velib_WebApp/
├── html/                   # Interface utilisateur et contrôleurs frontend
│   ├── index.php           # Point d'entrée avec routage
│   ├── get_id.php          # Récupération des données utilisateur
│   ├── info.php            # Informations sur le système
│   ├── controller/         # Contrôleurs pour gérer les requêtes
│   │   ├── LogController.php     # Contrôleur d'authentification
│   │   └── PostGetController.php # Contrôleur des opérations CRUD
│   └── page/               # Pages web de l'application
│       ├── account_page.php      # Page du compte utilisateur
│       ├── home.php              # Page d'accueil avec carte
│       ├── login.php             # Page de connexion
│       ├── register.php          # Page d'inscription
│       ├── css/                  # Feuilles de style CSS
│       └── images/               # Images et ressources graphiques
├── private/                # Backend et logique métier
│   ├── app.py              # API Flask pour les données Vélib
│   ├── api_files/          # API endpoints pour les fonctionnalités
│   │   ├── get_reservation.php   # Récupération des réservations
│   │   ├── get_search.php        # Récupération de l'historique
│   │   ├── post_order.php        # Enregistrement des commandes
│   │   ├── post_search.php       # Enregistrement des recherches
│   │   └── remove_search.php     # Suppression des recherches
│   ├── functions/          # Fonctions utilitaires
│   │   ├── tokenCHECK.php        # Validation des tokens JWT
│   │   └── tokenGEN.php          # Génération des tokens JWT
│   ├── log_backend/        # Système d'authentification
│   │   ├── get_id.php            # Récupération de l'ID utilisateur
│   │   ├── login.php             # Traitement de la connexion
│   │   ├── register.php          # Traitement de l'inscription
│   │   └── session_destroy.php   # Déconnexion et fin de session
│   ├── Model/              # Modèle de données
│   │   └── Model.php             # Classe pour l'accès à la base de données
│   └── php-BIB/            # Bibliothèques PHP externes
│       └── vendor/               # Dépendances (Firebase JWT, etc.)
```

## 🚀 Installation et déploiement

### Prérequis

- [PHP](https://www.php.net/) (v7.4 ou supérieur)
- [Python](https://www.python.org/) (v3.8 ou supérieur)
- [MySQL](https://www.mysql.com/) ou [MariaDB](https://mariadb.org/)
- [Composer](https://getcomposer.org/) pour les dépendances PHP
- [Docker](https://www.docker.com/) et [Docker Compose](https://docs.docker.com/compose/)

### Déploiement avec Docker

Le projet dispose d'un environnement Docker qui configure automatiquement tous les services nécessaires (Apache, MySQL, API Flask).

1. **Cloner le dépôt Git contenant la configuration Docker**

   ```bash
   git clone https://github.com/aariisto/Docker_Velib_WebApp
   cd Docker_Velib_WebApp
   ```

2. **Configurer les variables d'environnement (optionnel)**

   Modifiez le fichier `.env` si vous souhaitez personnaliser les ports, mots de passe ou autres configurations.

3. **Lancer les conteneurs Docker**

   ```bash
   docker-compose up -d
   ```

Cette commande déploie automatiquement:

- Un serveur web Apache avec PHP
- Un serveur de base de données MySQL
- L'API Flask qui sert de proxy pour les données Vélib
- Les configurations réseau nécessaires

Le conteneur Docker exposera l'application sur le port 80 par défaut.

## 🔄 Fonctionnalités spéciales

### Authentification par token JWT

L'application utilise des tokens JWT (JSON Web Tokens) pour sécuriser l'authentification des utilisateurs. Chaque requête à l'API nécessite un token valide, ce qui assure que seuls les utilisateurs authentifiés peuvent accéder aux fonctionnalités protégées.

### Protection CSRF

Toutes les requêtes POST sont protégées contre les attaques CSRF (Cross-Site Request Forgery) grâce à un système de tokens CSRF généré côté serveur et vérifié lors de chaque requête.

### API Proxy pour les données Vélib

Un serveur Flask sert d'intermédiaire (proxy) pour récupérer les données des stations Vélib depuis l'API officielle, les formater correctement et les renvoyer au frontend. Cela permet de filtrer et d'optimiser les données avant de les présenter à l'utilisateur.

---

Fait avec ❤️ pour les utilisateurs de Vélib à Paris.

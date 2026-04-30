# DiscoveryLive

Site web permettant de découvrir et réserver des concerts à Bordeaux selon le style musical.

## Démo en ligne

https://discovery-live.onrender.com

## Prérequis

### Option 1 — XAMPP (local)
- XAMPP (Apache + MySQL + PHP 8.2)
- MongoDB Community Server
- Composer
- Un navigateur web

### Option 2 — Docker (local)
- Docker Desktop
- Un navigateur web

### Option 3 — Render (en ligne)
- Un compte GitHub
- Un compte Render

## Installation

### Option 1 — XAMPP

#### 1. Cloner le projet

```bash
git clone https://github.com/clarasoual/Discovery-Live.git
```

Ou télécharger le ZIP et le déposer dans :

```
/Applications/XAMPP/xamppfiles/htdocs/Discovery Live/
```

#### 2. Démarrer XAMPP
- Lancer **XAMPP Control Panel**
- Démarrer **Apache** et **MySQL**

#### 3. Créer la base de données
- Ouvrir **phpMyAdmin** : `http://localhost/phpmyadmin`
- Créer une base de données nommée `discovery_live`
- Importer le fichier `discovery_live.sql` fourni dans le projet

#### 4. Installer les dépendances Composer

```bash
cd "/Applications/XAMPP/xamppfiles/htdocs/Discovery Live"
composer install
```

#### 5. Démarrer MongoDB

```bash
mongod
```

#### 6. Lancer le site

```
http://localhost/Discovery%20Live/index.php
```

---

### Option 2 — Docker

#### 1. Cloner le projet

```bash
git clone https://github.com/clarasoual/Discovery-Live.git
cd Discovery-Live
```

#### 2. Lancer les conteneurs

```bash
docker-compose up --build
```

#### 3. Accéder aux services

| Service      | URL                    |
|--------------|------------------------|
| Site         | http://localhost:8080  |
| phpMyAdmin   | http://localhost:8081  |
| Mongo Express| http://localhost:8082  |

---

### Option 3 — Render

#### 1. Pusher le projet sur GitHub

#### 2. Créer un nouveau Web Service sur Render
- Connecter le dépôt GitHub
- Render détecte automatiquement le **Dockerfile**

#### 3. Créer une base de données PostgreSQL sur Render
- Aller dans **New > PostgreSQL**
- Récupérer les identifiants de connexion

#### 4. Configurer les variables d'environnement sur Render

| Variable  | Valeur          |
|-----------|-----------------|
| DB_HOST   | fourni par Render |
| DB_PORT   | 5432            |
| DB_NAME   | fourni par Render |
| DB_USER   | fourni par Render |
| DB_PASS   | fourni par Render |
| MONGO_HOST| localhost       |
| MONGO_PORT| 27017           |

#### 5. Déployer
- Render lance automatiquement le build et le déploiement
- Le site est accessible via l'URL publique fournie par Render : https://discovery-live.onrender.com

---

## Structure du projet

```
Discovery Live/
├── index.php               → Page d'accueil (4 prochains concerts)
├── concerts.php            → Liste des concerts avec filtrage dynamique
├── artiste.php             → Fiche artiste dynamique
├── contact.php             → Formulaire de contact (MongoDB)
├── admin.php               → Interface d'administration (messages MongoDB)
├── get_concerts.php        → Endpoint JSON pour le chargement asynchrone
├── decouvrir.html          → Page Découvrir avec playlists Spotify
├── connexion.php           → Connexion MySQL/PostgreSQL (PDO)
├── connexion_mongo.php     → Connexion MongoDB
├── concerts.css            → CSS page concerts
├── novelists.css           → CSS fiche artiste
├── decouvrir.css           → CSS page découvrir
├── index.css               → CSS page accueil
├── Dockerfile              → Image Docker PHP 8.2 + nginx
├── docker-compose.yml      → Orchestration des 5 services
├── nginx.conf              → Configuration nginx
├── start.sh                → Script de démarrage php-fpm + nginx
├── discovery_live.sql      → Script SQL de création et insertion
├── composer.json           → Dépendances PHP (mongodb/mongodb)
└── IMAGES/                 → Images du site
```

## Base de données

### MySQL / PostgreSQL
Le projet utilise **MySQL** en local et **PostgreSQL** sur Render, avec 2 tables :
- `concerts` — artistes, dates, prix, liens streaming
- `salles` — salles de concert de Bordeaux et sa métropole

### MongoDB
La base `discovery_live` contient une collection :
- `contacts` — messages envoyés via le formulaire de contact (structure variable selon le type : proposition, signalement, suggestion, autre)

## Fonctionnalités

- Affichage dynamique des 4 prochains concerts depuis la BDD
- Filtrage par date, genre, prix et type de salle
- Fiche artiste avec liens streaming (Spotify, YouTube, Apple Music, Deezer)
- Système "Voir plus / Voir moins" asynchrone (async/await)
- Formulaire de contact avec double validation JS + PHP
- Page d'administration protégée par session PHP
- Playlists Spotify intégrées par genre
- Ancres HTML entre la page Découvrir et la page Concerts

## Branches Git

- `main` — branche principale stable
- `vitrine` — développement front HTML/CSS
- `php-concerts` — intégration PHP/MySQL
- `nosql` — intégration MongoDB
- `formulaires` — développement des formulaires

# School Management

Site de gestion scolaire permettant de consulter et gérer les notes des élèves par classe.

## Démo en ligne

https://school-management-1zdt.onrender.com

## Prérequis

### Option 1 — XAMPP (local)
- XAMPP (Apache + MySQL + PHP 8.2)
- MongoDB Community Server
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
git clone https://github.com/clarasoual/School-Management.git
```

Ou télécharger le ZIP et le déposer dans :

```
/Applications/XAMPP/xamppfiles/htdocs/School-Management/
```

#### 2. Démarrer XAMPP
- Lancer **XAMPP Control Panel**
- Démarrer **Apache** et **MySQL**

#### 3. Créer la base de données
- Ouvrir **phpMyAdmin** : `http://localhost/phpmyadmin`
- Créer une base de données nommée `school_management`
- Importer le fichier `school_management.sql` fourni dans le projet

#### 4. Démarrer MongoDB

```bash
mongod
```

#### 5. Lancer le site

```
http://localhost/School-Management/index.php
```

---

### Option 2 — Docker

#### 1. Cloner le projet

```bash
git clone https://github.com/clarasoual/School-Management.git
cd School-Management
```

#### 2. Lancer les conteneurs

```bash
docker-compose up --build
```

#### 3. Accéder aux services

| Service       | URL                   |
|---------------|-----------------------|
| Site          | http://localhost:8083 |
| phpMyAdmin    | http://localhost:8084 |
| Mongo Express | http://localhost:8085 |

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

| Variable | Valeur            |
|----------|-------------------|
| DB_HOST  | fourni par Render |
| DB_PORT  | 5432              |
| DB_NAME  | fourni par Render |
| DB_USER  | fourni par Render |
| DB_PASS  | fourni par Render |

#### 5. Déployer
- Render lance automatiquement le build et le déploiement
- Le site est accessible via l'URL publique : https://school-management-1zdt.onrender.com

---

## Structure du projet

```
School-Management/
├── index.php              → Page d'accueil — liste des classes avec moyennes
├── eleves.php             → Liste des élèves par classe + ajout de notes par classe
├── notes.php              → Fiche élève — notes, ajout et suppression
├── connexion.php          → Connexion MySQL/PostgreSQL (PDO)
├── connexion_mongo.php    → Connexion MongoDB
├── index.css              → CSS page d'accueil
├── eleves.css             → CSS page élèves
├── nino.css               → CSS fiche élève
├── Dockerfile             → Image Docker PHP 8.2 + nginx
├── docker-compose.yml     → Orchestration des 5 services
├── nginx.conf             → Configuration nginx
├── start.sh               → Script de démarrage php-fpm + nginx
└── school_management.sql  → Script SQL de création et insertion des données
```

## Base de données

### MySQL / PostgreSQL
Le projet utilise **MySQL** en local et **PostgreSQL** sur Render, avec 5 tables reliées par clés étrangères :
- `professeurs` — informations sur les professeurs
- `classes` — classes reliées à leur professeur principal
- `eleves` — élèves reliés à leur classe
- `matieres` — matières enseignées
- `notes` — notes reliées aux élèves et aux matières

### MongoDB
La base `school_management` contient une collection :
- `logs` — logs d'actions (ajout et suppression de notes) avec timestamp automatique

## Fonctionnalités

- Consultation des classes avec leurs moyennes générales et professeurs principaux
- Navigation dynamique entre classes et élèves via URLSearchParams
- Filtrage des notes par matière avec calcul de moyenne asynchrone (async/await)
- Ajout de notes pour un élève individuel ou pour toute une classe
- Suppression de notes avec vérification d'appartenance
- Double validation des formulaires : côté client (JS) et côté serveur (PHP)
- Logs d'actions MongoDB à chaque ajout ou suppression de note

## Branches Git

- `main` — branche principale stable
- `vitrine` — développement front HTML/CSS/JS
- `connexion-bdd` — intégration PHP/MySQL
- `nosql` — intégration MongoDB

# School Management

Site de gestion scolaire permettant de consulter et gérer les notes des élèves par classe.

## Prérequis

- XAMPP (Apache + MySQL)
- PHP 8.0 ou supérieur
- Git

## Installation

### 1. Cloner le dépôt
```bash
cd /Applications/XAMPP/xamppfiles/htdocs
git clone https://github.com/clarasoual/School-Management.git
cd School-Management
```

### 2. Démarrer XAMPP

Lancer Apache et MySQL depuis le panneau de contrôle XAMPP.

### 3. Créer la base de données

- Ouvrir phpMyAdmin : http://localhost/phpmyadmin
- Créer une nouvelle base de données nommée `school_management`
- Importer le fichier `school_management.sql` via l'onglet **Importer**

### 4. Configurer la connexion

Vérifier que le fichier `connexion.php` contient les bons paramètres :
```php
$host = 'localhost';
$dbname = 'school_management';
$user = 'root';
$password = '';
```

### 5. Accéder au site

Ouvrir le navigateur et aller sur :
```
http://localhost/School-Management/index.php
```

## Structure du projet
```
School-Management/
├── connexion.php      # Connexion PDO à la base de données
├── index.php          # Page d'accueil — liste des classes
├── eleves.php         # Liste des élèves par classe + ajout de notes
├── notes.php          # Fiche élève — notes, ajout et suppression
├── index.css          # Styles page d'accueil
├── eleves.css         # Styles page élèves
├── nino.css           # Styles fiche élève
└── school_management.sql       # Script SQL de création et insertion des données
```

## Fonctionnalités

- Consultation des classes et de leurs moyennes générales
- Navigation dynamique entre classes et élèves
- Filtrage des notes par matière avec calcul de moyenne
- Ajout de notes pour un élève individuel ou pour toute une classe
- Suppression de notes individuelles
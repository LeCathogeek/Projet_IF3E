# Projet IF3E – Carpooling

## Membres de l'équipe
- À compléter avec les noms complets de l'équipe.

## Logiciel requis
- PHP 8.x
- SQLite (intégré à PHP)
- Un navigateur web moderne

## Installation
1. Placez le projet dans un dossier accessible par PHP.
2. Vérifiez que le dossier `sources/databases/` est bien présent.
3. Ouvrez un terminal dans le dossier du projet.
4. Lancez le serveur PHP :

```bash
php -S localhost:8000
```

5. Ouvrez votre navigateur sur :

```text
http://localhost:8000/sources/index.php
```

## Configuration de la base de données
Le projet utilise SQLite via le fichier :

```text
sources/databases/main_database.sq3
```

La structure de la base est décrite dans :
- `database/schema.sql`
- `database/data.sql`
- `database/queries.sql`

Le fichier `sources/db.php` crée automatiquement les tables si elles n'existent pas au premier accès.

## Comptes de démonstration
À compléter selon les comptes réellement créés dans la base.

Exemple :
- Email : admin@example.com
- Mot de passe : à définir

## Fonctionnalités implémentées
- Inscription d'un utilisateur
- Connexion / déconnexion
- Tableau de bord utilisateur
- Recherche de trajets
- Structure de base de données pour les utilisateurs, véhicules, trajets, réservations, paiements et avis

## Limites connues
- Le projet est encore en phase de base fonctionnelle.
- Les fonctionnalités métier avancées (réservation complète, paiement, admin, avis avancés) doivent être complétées.
- Les données de démonstration doivent être générées avec des mots de passe réellement hashés.

## Contribution de chaque membre
- À compléter par chaque membre de l'équipe.

## Démarrage rapide
```bash
php -S localhost:8000
```
Puis ouvrir :
```text
http://localhost:8000/sources/index.php
```

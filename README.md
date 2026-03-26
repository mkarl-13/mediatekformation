# Mediatekformation
Ce dépôt est une version améliorée de Mediatekformations disponible ici https://github.com/CNED-SLAM/mediatekformation. Ce dernier contient la présentation de l'applicaiton d'origine.

## Fonctionnalités ajoutées
### Nettoyage et optimisation du code
Le code existant a été rendu plus maintenable et nettoyé en suivant les recommandations de SonarLint (conventions de nommage, suppression des doublons, accolades, visibilité explicite des méthodes, etc).

### Nombre de formations par playlist
Une nouvelle colonne affiche le nombre de formations associées à chaque playlist, visible sur la page de liste des playlists ainsi que sur la page de détail d'une playlist. Le tri croissant et décroissant est disponible sur cette colonne.

### Back-office d'administration
Un espace d'administration complet a été développé, accessible via /admin, permettant de :
- **Gérer les formations** : lister, créer, modifier et supprimer des formations (avec protection CSRF et confirmation avant suppression)
- **Gérer les playlists** : lister, créer, modifier et supprimer des playlists (une playlist ne peut être supprimée que si elle est vide)
- **Gérer les catégories** : lister, ajouter et supprimer des catégories directement depuis la page de liste

### Authentification
L'accès au back-office est sécurisé par un système d'authentification. Un bouton de déconnexion est présent sur toutes les pages d'administration.

### Tests automatisés
Des tests fonctionnels ont été écrits pour toutes les pages de liste (front-office et back-office), couvrant les tris, les filtres et les opérations CRUD.

### Déploiement continu
Un workflow GitHub Actions a été mis en place pour déployer automatiquement le site à chaque push sur la branche principale, via FTP vers l'hébergeur o2switch.

### Sauvegarde automatique de la base de données
Un script de sauvegarde s'exécute automatiquement chaque jour à minuit via une tâche cron côté serveur.

## Installation et utilisation en local
### Prérequis
- PHP 8+
- Composer
- MySQL (ex. via WAMP, XAMPP ou MAMP)

### Étapes

```bash
# 1. Cloner le dépôt
git clone https://github.com/mkarl-13/mediatekformation.git
cd mediatekformation

# 2. Installer les dépendances
composer install

# 3. Configurer la base de données
# Créer un fichier .env.local à la racine et y renseigner :
DATABASE_URL="mysql://utilisateur:motdepasse@127.0.0.1:3306/mediatekformation"

# 4. Créer la base de données et importer les données
php bin/console doctrine:database:create
# Importer le fichier SQL fourni via phpMyAdmin ou en ligne de commande

# 5. Lancer le serveur de développement
symfony server:start
```

L'application est ensuite accessible à l'adresse `https://localhost:8000`.

## Tester l'application en ligne

L'application est déployée et accessible à l'adresse suivante :
**[https://app.sc3mika2559.universe.wf](#)**

## Lancer les tests automatisés

```bash
php bin/phpunit
```

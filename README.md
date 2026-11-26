# Dashboard To-Do — HTML / CSS / JS / PHP / MySQL

Application de gestion de tâches avec comptes utilisateurs, design "dashboard"
(sidebar corail, cartes de tâches, donuts de statut), catégories, priorités
et recherche — testée de bout en bout (PHP 8.3 + MariaDB).

## Arborescence

```
todo-app/
├── index.php                 # redirige vers dashboard.php ou login.php
├── login.php                  # connexion
├── register.php                # inscription
├── logout.php                  # déconnexion
├── dashboard.php                # Dashboard : To-Do du jour + donuts + tâches terminées
├── vital-task.php                # Tâches de priorité Haute
├── my-task.php                    # Toutes les tâches (filtres statut/catégorie/recherche)
├── task-categories.php             # Gestion des catégories
├── settings.php                     # Profil + changement de mot de passe
├── help.php                          # FAQ statique
├── includes/
│   ├── db.php                          # connexion PDO à MySQL
│   ├── session.php                      # require_login() / require_login_page()
│   ├── helpers.php                       # initiales, dates FR, icônes SVG, libellés
│   ├── layout_top.php                     # sidebar + topbar (inclus en haut de chaque page)
│   └── layout_bottom.php                   # fermeture + modal ajout/édition de tâche
├── api/
│   ├── tasks.php                            # CRUD tâches + ?stats=1 (donuts) + ?scope=today|vital|completed
│   ├── categories.php                        # CRUD catégories
│   ├── content.php                            # (bonus non affiché) suivi de contenu
│   └── moods.php                               # (bonus non affiché) humeur du jour
├── assets/
│   ├── css/style.css                            # thème dashboard (corail / cartes / donuts)
│   └── js/app.js                                 # AJAX + rendu dynamique (aucune dépendance)
└── sql/
    └── schema.sql                                  # base + tables exportables
```

## Installation (XAMPP / WAMP / MAMP / LAMP)

1. Copier `todo-app/` dans `htdocs/` (XAMPP) ou `www/` (WAMP/MAMP).
2. Importer `sql/schema.sql` :
   ```
   mysql -u root -p < sql/schema.sql
   ```
   ou via phpMyAdmin → Importer.
3. Vérifier les identifiants dans `includes/db.php` (host/user/mot de passe).
4. Démarrer Apache + MySQL, ouvrir `http://localhost/todo-app/`.
5. Créer un compte, se connecter, ajouter des tâches et catégories.

## Modèle de données

- `users` — comptes (mot de passe haché, email optionnel)
- `task_categories` — catégories propres à chaque utilisateur (nom + couleur)
- `todo` — tâches : titre, description, **priorité** (Faible/Modérée/Haute),
  **statut** (Non commencé/En cours/Terminé), catégorie, image (URL optionnelle),
  échéance, date de création, date de complétion (calculée automatiquement)
- `content_tracker`, `moods` — tables du bonus "suivi de contenu / mood board"
  livré précédemment ; conservées en base mais non affichées dans ce design
  (dis-moi où tu veux les intégrer si tu veux les garder visibles)

Toutes les tables métier ont `user_id` en clé étrangère `ON DELETE CASCADE` :
chaque compte ne voit et ne modifie que ses propres données.

## Routes de l'application (menu latéral)

| Route                  | Contenu                                                        |
|-------------------------|------------------------------------------------------------------|
| `dashboard.php`          | To-Do du jour, donuts "Task Status", dernières tâches terminées |
| `vital-task.php`          | Tâches de priorité Haute                                        |
| `my-task.php`               | Toutes les tâches — filtres statut/catégorie, recherche (topbar) |
| `task-categories.php`         | Création / suppression de catégories                            |
| `settings.php`                  | Mise à jour du profil et du mot de passe                        |
| `help.php`                        | FAQ                                                              |
| `logout.php`                        | Déconnexion                                                     |

## API (AJAX, JSON)

| Endpoint             | Méthodes               | Paramètres utiles                                      |
|------------------------|--------------------------|------------------------------------------------------------|
| `api/tasks.php`         | GET, POST, PUT, DELETE     | `?scope=today\|vital\|completed`, `?stats=1`, `?status=`, `?category_id=`, `?q=`, `?limit=` |
| `api/categories.php`      | GET, POST, DELETE            | —                                                              |

Toutes les routes API exigent une session active (401 sinon).

## Ce qui a été testé (bout en bout, PHP 8.3 + MariaDB 10.11)

- Import du schéma SQL sans erreur
- Inscription, connexion, déconnexion, protection des pages et des API
- CRUD complet des tâches (création, lecture filtrée, mise à jour de statut
  avec `completed_at` automatique, suppression)
- CRUD des catégories, association à une tâche
- Donuts de statut (`?stats=1`) avec pourcentages corrects
- Mise à jour du profil et du mot de passe depuis Settings
- Toutes les pages (200) et lint PHP (`php -l`) sans erreur sur tous les fichiers

## Notes de conception

- **Avatar** : initiales générées en CSS (pas d'upload de photo).
- **Image de tâche** : champ URL optionnel (pas d'upload serveur).
- Nécessite l'extension PHP `pdo_mysql` (incluse par défaut avec XAMPP/WAMP/MAMP).

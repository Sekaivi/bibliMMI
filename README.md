# BibliMMI

**BibliMMI** est une solution complète de gestion de bibliothèque développée avec le framework **PHP Laravel**. Elle permet de piloter l'ensemble des activités documentaires, de la gestion du catalogue à l'administration des usagers et de leurs emprunts, tout en offrant une interface de communication sécurisée pour des services tiers.

## Fonctionnalités Principales

### Gestion des Usagers
- Suivi complet des membres (coordonnées, date d'adhésion).
- Statut d'abonnement : gestion automatique des **usagers bloqués** ou actifs.
- Historique individuel des actions.

### Gestion du Catalogue (Ouvrages & Exemplaires)
- **Ouvrages** : Informations détaillées (Auteur, Éditeur, Date de parution, ISBN, catégorie).
- **Exemplaires** : Suivi granulaire par unité.
    - État physique (Neuf, Bon état, Abîmé, Perdu).
    - Statut de disponibilité (En rayon, Emprunté, En réparation).

### Emprunts et Retours
- Système de prêt et de rendu intuitif.
- Détection automatique des **emprunts en retard**.
- Calcul des durées de prêt.

### API REST Sécurisée
BibliMMI intègre une API permettant l'interconnexion avec un site externe (portail usager) :
- **Consultation** : Les usagers peuvent voir leurs emprunts en cours à distance.
- **Renouvellement** : Possibilité de prolonger un prêt via l'API.
- **Sécurité** : Authentification via **Token sécurisé** (Laravel Sanctum).
- **Test de connexion** : Endpoint dédié pour vérifier la validité des accès et la disponibilité du service.

## Stack Technique

- **Framework** : [Laravel 10/11](https://laravel.com/)
- **Langage** : PHP 8.x
- **Base de données** : MySQL / PostgreSQL
- **Authentification API** : Laravel Sanctum

## Installation

1. **Cloner le projet** :
   ```bash
   git clone [https://github.com/votre-compte/biblimmi.git](https://github.com/votre-compte/biblimmi.git)
   cd biblimmi
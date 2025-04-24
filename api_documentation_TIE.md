
# 🎭 API - Projet Théâtre TIE

Bienvenue dans la documentation de l'API du projet TIE (Théâtre Intergénérationnel Éducatif), une plateforme de gestion de cours et spectacles de théâtre pour enfants, adolescents et adultes.

## ✨ Objectif
Cette API permet :
- Aux visiteurs de consulter les événements (sans se connecter)
- Aux utilisateurs inscrits de réserver ou s’inscrire à un atelier
- Aux administrateurs de gérer le contenu (événements, médias, utilisateurs…)

## 👥 Rôles et accès
- `Invité` : accès en lecture seule (événements, médias…)
- `Utilisateur` (`ROLE_USER`) : peut réserver, s’inscrire à un atelier, consulter son profil
- `Administrateur` (`ROLE_ADMIN`) : peut gérer les événements, médias, catégories, utilisateurs, etc.

## 🧭 Utilisation recommandée
- Toutes les requêtes protégées nécessitent un token JWT (login via `/api/login`)
- Pour les tests, Swagger est disponible sur `/api` (penser à s’autoriser avec le token)
- Les relations entre entités sont au format `/api/{ressource}/{id}`

---


# Documentation de l'API - Projet Théâtre TIE

## Routes GET

### `GET /api/events`
- Accès : Invité, User, Admin
- Description : Liste tous les événements
- Réponse :
```json
[
  {
    "id": 16,
    "titre": "Spectacle de fin d’année",
    "date": "2025-06-13T20:00:00",
    "lieu": "Théâtre de la ville",
    "isVisible": true
  }
]
```

### `GET /api/media`
- Accès : Invité, User, Admin
- Description : Liste des images/vidéos

### `GET /api/categories`
- Accès : Invité, User, Admin
- Description : Liste des catégories de média

### `GET /api/me`
- Accès : User connecté
- Description : Donne les infos du user connecté

### `GET /api/enrollments`
- Accès : User connecté
- Description : Inscriptions aux ateliers

### `GET /api/reservations`
- Accès : User connecté
- Description : Réservations liées à un user


## Routes POST

### `POST /api/reservations`
- Accès : ROLE_USER
- Description : Créer une réservation
```json
{
  "nombrePlaces": 2,
  "dateReservation": "2025-06-14T14:00:00",
  "user": "/api/users/31",
  "event": "/api/events/16"
}
```

### `POST /api/enrollments`
- Accès : ROLE_USER
- Description : Inscription à un atelier
```json
{
  "groupe": "Enfants Débutants",
  "isActive": true,
  "anneeScolaire": "2024-2025",
  "user": "/api/users/31"
}
```

### `POST /api/media`
- Accès : ROLE_ADMIN
- Description : Ajouter un média (multipart/form-data)

### `POST /api/categories`
- Accès : ROLE_ADMIN
- Description : Ajouter une catégorie
```json
{
  "nom": "Galerie Spectacles"
}
```

### `POST /api/logs`
- Accès : ROLE_USER / interne
- Description : Journalisation d’activité
```json
{
  "action": "Réservation créée",
  "user": "/api/users/31"
}
```

## Routes PATCH

### `PATCH /api/users/{id}`
- Accès : ROLE_USER (sur son propre compte) ou ROLE_ADMIN
- Description : Modifier le profil utilisateur
- Exemple :
```json
{
  "prenom": "Sylvie",
  "nom": "Durand"
}
```

### `PATCH /api/events/{id}`
- Accès : ROLE_ADMIN
- Description : Modifier un événement
```json
{
  "titre": "Spectacle d’hiver",
  "isVisible": false
}
```


## Routes DELETE

### `DELETE /api/events/{id}`
- Accès : ROLE_ADMIN
- Description : Supprime un événement

### `DELETE /api/media/{id}`
- Accès : ROLE_ADMIN
- Description : Supprime un média (image/vidéo)

### `DELETE /api/enrollments/{id}`
- Accès : ROLE_USER ou ADMIN
- Description : Supprime une inscription à un atelier

### `DELETE /api/reservations/{id}`
- Accès : ROLE_USER ou ADMIN
- Description : Supprime une réservation


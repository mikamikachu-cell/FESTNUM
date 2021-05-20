

Les rôles utilisateurs de l'application : 
- super-admin : développeur, tous les privilèges
- admin : gérer toute l'application. Identique au super-admin, mais sans l'accès aux fonctionnalités critiques.
- rôles spécifiques à certaines fonctionnalités  par exemple, un role auteur pour publier, un post-admin pour créer/modifier/supprimer les articles, ...
On peut avoir un role back pour protéger l'accès au back-office et un role utilisateur de base.
Ici, on a un user-admin pour gérer les utilisateurs.

note : dans Symfony il y a le rôle ROLE_ALLOWED_TO_SWITCH pour utiliser un autre compte sans connaître son mdp.

Arrêt à 'Personnalisation du controleur'
mdp donné au super-admin : que vaille
Bien sûr, voici le tableau en Markdown sans les deux premières routes :

```markdown
| Route      | Nom             | Méthodes  | Action                                                                |
|------------|-----------------|-----------|-----------------------------------------------------------------------|
| /book/list | app_book_list   | GET       | Affiche la liste de tous les livres présents dans la base de données. |
| /book/{id} | app_book_detail | GET       | Affiche les détails d'un livre spécifique identifié par son `id`.     |
| /          | app_main        | GET       | Affiche la page principale avec une liste des livres.                 |
| /register  | app_register    | GET, POST | Permet à un utilisateur de s'inscrire en remplissant un formulaire.   |
| /login     | app_login       | GET       | Affiche la page de connexion avec un formulaire pour l'utilisateur.   |
| /logout    | app_logout      | GET       | Permet à un utilisateur de se déconnecter de l'application.           |
```
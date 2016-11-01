# tpweb
## /index.php
Cette page présente un formulaire de connexion proposant de "retenir" l'utilisateur pendant 1 mois.
La page appelle __/lib/display_users_list.php__, permettant d'afficher la liste des utilisateurs de la base triés par leur nom et prénom. Chaque ligne de la table propose un lien vers __view_profile.php__.
En fin de page se trouve un lien vers __inscription.php__.

## /inscription.php
Un formulaire permettant d'inscrire un nouvel utilisateur.
La validité de chaque champ est vérifiée à l'aide d'un script __/js/checkInscriptionForm.js__ lorsque l'utilisateur quitte le champ correspondant (onblur). Ce script vérifie également que le login utilisateur est disponible à l'aide du fichier __lib/is_login_used.php__. Lorsque la demande est envoyée, appelle __lib/register.php__ et si l'inscription a été effectuée correctement, redirige vers __/index.php__.

## /verif_login.php
Appelle __lib/get_user_infos.php__ permettant de récupérer les informations d'un utilisateur par son id, et affiche ces informations.

## /private/index.php
Identique à __/index.php__ hormis l'appel de __/lib/verif_login.php__ et de l'affichage du message de bienvenue ($prenom $nom).

## /private/modify_login.php
Une version de __/view_profile.php__ mais présentant un formulaire permettant de modifier les informations d'utilisateur. Le fichier __lib/verif_login.php__ est appelé en début de page. L'utilisateur connecté peut modifier son propre profil uniquement. Lorsque la demande de modification a été envoyée, appelle __lib/modify_profile.php__.

## /lib/connect_db.php
Une simple fonction renvoyant une connexion à la base de donnée.

## /lib/display_users_list.php
Affiche la liste des utilisateurs enregistrés dans la base de donnée triés par leur nom et prénom et proposant un lien permettant de consulter leur profil respectif. Si l'utilisateur est connecté, la ligne correspondant à son profil mène à __private/modify_profile.php__ au lieu de __/view_profile.php__.

## /lib/get_user_infos.php
Fonction utilisée dans __/view_profile.php__ et dans __lib/modify_profile.php__ pour récupérer les informations courantes de l'utilisateur.

## /lib/is_login_used.php
Affiche une page avec "true" si le login spécifié est pris par un utilisateur, "false" sinon. Utilisé dans __js/checkInscriptionForm.js__.

## /lib/modify_profile.php
Traite la demande de modification du profil utilisateur. Vérifie que les informations entrées sont correctes. Met à jour l'avatar si le login change (le nom de l'avatar étant "avatar_userLogin.type")... Modifier le mot de passe requiert de spécifier le mot de passe courant.  

## /lib/register.php
Traite la demande d'inscription. Si la demande réussi et si l'image a bien été copiée sur le serveur, renvoi vers __/index.php__.

## /lib/verif_login.php
4 cas possibles :
* une session existe, reprise simple de la session
* aucune session n'était en cours, mais un cookie existe => connexion à partir des informations du cookie
* aucune session et aucun cookie, mais une demande de connexion par formulaire a été émise
* aucun des 3 cas précédents => tentative d'accès à une ressource privée =>erreur => redirection vers __/index.php__
Dans le cookie est conservée l'id de l'utilisateur (aucune sécurité).
Dans la session sont conservés le nom, le prénom, le login et l'id de l'utilisateur.

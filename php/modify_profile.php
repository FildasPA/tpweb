<?php

//==============================================================================
//
// ■ Modify profile
// -- Objet : Traitement des modifications du profil utilisateur
// -- Par : Julien Delvaux & Julien Boge
// -- Dernière modification : 29.10.16
//
//==============================================================================
//
// ■ Liste des erreurs possibles :
// - Champs vides ou trop longs
// - Ancien mot de passe incorrect
// - Champ ancien mdp rempli et nouveau mdp vide, ou inversement
// - Pas de session ouverte
// - Nouveau login déjà pris par un autre utilisateur
//
//==============================================================================
include("display_globals.php"); // affiche le contenu des variables globales

//------------------------------------------------------------------------------
// Test input (pris sur w3schools)
// http://www.w3schools.com/php/showphp.asp?filename=demo_form_validation_escapechar
//------------------------------------------------------------------------------
// function test_input($data)
// {
//   $data = trim($data);             // Supprime les espaces de début et de fin
//   $data = stripslashes($data);     // Supprime les antislashs (\)
//   $data = htmlspecialchars($data); // Convertit les charactères spéciaux en HTML
//   return $data;
// }

//------------------------------------------------------------------------------
// * Copie l'avatar sur le serveur
//------------------------------------------------------------------------------
function copy_avatar($url,$dest_file)
{
	$dest_dir  = dirname(dirname($_SERVER['SCRIPT_FILENAME'])) . "/pictures/";
	$dest_file = $dest_dir . $dest_file;
	if(copy($url,$dest_file)) {
		return true;
	}	else {
		echo "<p style='color:red;'>L'image n'a pas pu être ajoutée...</p>";
		return false;
	}
}

//------------------------------------------------------------------------------
// * Vérifie si le login est pris par un autre utilisateur
//------------------------------------------------------------------------------
function is_login_used($conn,$login)
{
	try {
		$sql = "SELECT id
		        FROM personnes
		        WHERE login = :login
		        LIMIT 1";
		$user = $conn->prepare($sql);
		$user->bindParam(':login',$login,PDO::PARAM_STR,15);
		$user->execute();
		if(!$user || $user->rowCount() > 0) {
			return $user->fetch(PDO::FETCH_ASSOC);
		}
		return false;
	}	catch(Exception $e) {
		echo "<br/>" . $e->getMessage() . "<br/>";
		return true;
	}
}


//------------------------------------------------------------------------------
// * MAIN
// Vérification des données envoyées par le formulaire
// Récupère les informations transmises via le formulaire
//------------------------------------------------------------------------------

// Si ces informations sont vides ou invalides, la demande de modification est rejetée
// TODO: décomposer ces erreurs & afficher les messages d'erreur correspondants
// OU PAS? Affichage à l'aide javascript? => si js pas activé?
// if(empty($_REQUEST['name'])) {}
// if(empty($_REQUEST['firstname'])) {}
// if(empty($_REQUEST['login'])) {}
// if(empty($old_password) && !empty($new_password)) {}
// if(!empty($old_password) && empty($new_password)) {}

//------------------------------------------------------------------------------
// * Récolte les informations du formulaire
// Nouvelles informations
$new_user = array('name'      => test_input($_REQUEST['name']),
                  'firstname' => test_input($_REQUEST['firstname']),
                  'login'     => test_input($_REQUEST['login']),
                  'password'  => test_input($_REQUEST['new-password']));
// Confirmation de l'ancien mot de passe
$old_password = test_input($_REQUEST['old-password']);

//------------------------------------------------------------------------------
// * Vérification des données entrées dans le formulaire
if(empty($new_user['name']) ||
   empty($new_user['firstname']) ||
   empty($new_user['login']) ||
   (empty($old_password) && !empty($new_user['password'])) ||
   (!empty($old_password) && empty($new_user['password'])) ||
   strlen($new_user['login']) > 15 ||
   strlen($new_user['name']) > 15 ||
   strlen($new_user['firstname']) > 15 ||
   strlen($old_password) > 32 ||
   strlen($new_user['password']) > 32) {
	echo "<p>Erreur dans les données entrées (vide ou trop long)</p>";
	exit;
}

//------------------------------------------------------------------------------
// Connexion à la bdd
include_once("connect_db.php");
$conn = connect_db();
if(!$conn) {
	echo "<p style='color:red;'>Impossible de se connecter à la bdd</p>";
	exit;
}

//------------------------------------------------------------------------------
// Récolte des anciennes informations de l'utilisateur
if(!isset($_SESSION['id'])) {
	echo "<p style='color:red;'>Erreur: un invité ne peut modifier un profil utilisateur!</p>";
	exit;
}
$id = (int) $_SESSION['id'];
// $id = (int) test_input($_REQUEST['id']);
try {
	$sql = "SELECT nom,prenom,avatar,login,password
	        FROM personnes
	        WHERE id = :id
	        LIMIT 1";
	$old_user = $conn->prepare($sql);
	$old_user->bindParam(':id',$id,PDO::PARAM_INT);
	$old_user->execute();
	if(!$old_user || $old_user->rowCount() <= 0) {
		echo "<p>Erreur: aucun utilisateur ne correspond à cette id</p>";
		exit;
	}
	$old_user = $old_user->fetch(PDO::FETCH_ASSOC);
}	catch(Exception $e) {
	echo "<br/>" . $e->getMessage() . "<br/>";
	return NULL;
}

//------------------------------------------------------------------------------
// Ancien mot de passe incorrect
if(!empty($old_password)) {
	if($old_user['password'] != $old_password) {
		echo "<p>Erreur: l'ancien mot de passe ne correspond pas</p>";
		exit;
	}
}

//------------------------------------------------------------------------------
// Changement du login & champ avatar
if($old_user['login'] != $new_user['login']) {
	echo "<p>Changement du login...</p>";
	// Vérifie si le nouveau login est déjà pris
	$is_login_used = is_login_used($conn,$new_user['login']);
	if($is_login_used != false && $is_login_used != $id) {
		echo "<p>Erreur: login déjà pris</p>";
		exit;
	}
	// Si aucune nouvelle image n'a été envoyée, modifie l'adresse de l'ancien avatar
	if(empty($_FILES['avatar']['tmp_name'])) {
		$image_type = substr($old_user['avatar'],strrpos($old_user['avatar'],"."));
		$new_user['avatar'] = 'avatar_' . $new_user['login'] . $image_type;
		$dest_dir  = dirname(dirname($_SERVER['SCRIPT_FILENAME'])) . "/pictures/";
		echo "<p>Renommage de l'avatar... (" . $old_user['avatar'] . "," . $new_user['avatar'] . ") </p>";
		rename($dest_dir . $old_user['avatar'],$dest_dir . $new_user['avatar']);
	} else {

	}
} else {
	if(!empty($_FILES['avatar']['tmp_name'])) {
		$new_user['avatar'] = $old_user['avatar'];
		echo "<p>Pas de changement de login => pas de changement d'avatar</p>";
	}
}

if(empty($old_password) && empty($new_password)) {
	echo "<p>Pas de changement de mot de passe</p>";
	$new_user['password'] = $old_user['password'];
}

//------------------------------------------------------------------------------
// * Avatar
// L'image et le champ avatar doivent être modifiés si:
// - le login change
// - une autre image a été envoyée
//------------------------------------------------------------------------------
// Copie du nouvel avatar
// Suppression de l'ancienne image si le login a changé
if(!empty($_FILES['avatar']['tmp_name'])) {
	echo "<p>Nouvel avatar</p>";
	$dest_dir  = dirname(dirname($_SERVER['SCRIPT_FILENAME'])) . "/pictures/";
	$avatar_url  = $_FILES['avatar']['tmp_name'];
	$avatar_name = $_FILES['avatar']['name'];
	$image_type  = substr($avatar_name,strrpos($avatar_name,"."));
	$new_user['avatar'] = 'avatar_' . $new_user['login'] . $image_type;
	if($old_user['login'] != $new_user['login']) unlink($dest_dir . $old_user['avatar']);
	echo "<p>Changement de l'avatar: copie de l'image... (" . $avatar_url . "," . $dest_dir . $new_user['avatar'] . ")</p>";
	copy_avatar($avatar_url,"../pictures/" . $new_user['avatar']);
}

//------------------------------------------------------------------------------
// Applique les modifications à l'utilisateur
try {
	echo "<p><b>Applications des modifications...</b></p>";
	$sql = "UPDATE personnes
	        SET nom=:name,prenom=:firstname,avatar=:avatar,login=:login,password=:password
	        WHERE id=:id";
	$insert = $conn->prepare($sql);
	$insert->bindParam(':name',      $new_user['name'],      PDO::PARAM_STR,15);
	$insert->bindParam(':firstname', $new_user['firstname'], PDO::PARAM_STR,15);
	$insert->bindParam(':avatar',    $new_user['avatar'],    PDO::PARAM_STR,30);
	$insert->bindParam(':login',     $new_user['login'],     PDO::PARAM_STR,15);
	$insert->bindParam(':password',  $new_user['password'],  PDO::PARAM_STR,32);
	$insert->bindParam(':id',        $id,                    PDO::PARAM_INT);
	$insert->execute();
	echo "<p style='color:green;'>Modifications enregistrées!</p>";
}	catch(Exception $e) {
	echo "<br/>" . $e->getMessage() . "<br/>";
}

unset($old_user);
unset($new_user);


?>

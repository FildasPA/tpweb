<?php

//==============================================================================
//
// ■ Modify profile
// -- Objet : Traitement des modifications du profil utilisateur
// -- Par : Julien Delvaux & Julien Boge
// -- Dernière modification : 30.10.16
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

// include("display_globals.php"); // affiche le contenu des variables globales

//------------------------------------------------------------------------------
// * Vérifie si les informations sont remplies correctement
//------------------------------------------------------------------------------
function check_invalid_info(&$error_msg,&$error,$new_user,$old_password)
{
	if(empty($new_user['login'])) {
		$error_msg['login'] = "Ce champ doit être rempli";
		$error = true;
	}
	if(empty($new_user['name'])) {
		$error_msg['name'] = "Ce champ doit être rempli";
		$error = true;
	}
	if(empty($new_user['firstname'])) {
		$error_msg['firstname'] = "Ce champ doit être rempli";
		$error = true;
	}
	if(empty($old_password) && !empty($new_user['password'])) {
		$error_msg['old-password'] = "Pour changer de mot de passe, veuillez indiquer votre mot de passe actuel";
		$error = true;
	}
	if(strlen($new_user['login']) > 15) {
		$error_msg['login'] = "Le login ne peut dépasser 15 caractères";
		$error = true;
	}
	if(strlen($new_user['name']) > 15) {
		$error_msg['name'] = "Le nom ne peut dépasser 15 caractères";
		$error = true;
	}
	if(strlen($new_user['firstname']) > 15) {
		$error_msg['firstname'] = "Le prénom ne peut dépasser 15 caractères";
		$error = true;
	}
	if(strlen($new_user['password']) > 32) {
		$error_msg['old-password'] = "Le mot de passe ne peut dépasser 32 caractères";
		$error = true;
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
// * Copie l'avatar sur le serveur
//------------------------------------------------------------------------------
function copy_avatar($url,$dest_file)
{
	$dest_dir  = dirname(dirname($_SERVER['SCRIPT_FILENAME'])) . "/pictures/";
	$dest_file = $dest_dir . $dest_file;
	if(copy($url,$dest_file)) {
		return true;
	}	else {
		$error_msg['avatar'] = "L'image n'a pas pu être ajoutée...";
		return false;
	}
}

//------------------------------------------------------------------------------
// * Applique les modifications à l'utilisateur
//------------------------------------------------------------------------------
function alter_user($conn,$new_user,$id)
{
	try {
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
}

//------------------------------------------------------------------------------
// * Modifier l'utilisateur
// Sommaire :
// - connexion à la bdd
// - récupère les informations courantes de l'utilisateur
// - vérifie si le login est déjà pris
// - vérifie si l'ancien mot de passe correspond
// - gestion de l'avatar (image et champ)
// - si aucune erreur, appelle 'alter_user' et applique les modifications
//------------------------------------------------------------------------------
function modify_profile(&$new_user,$old_password)
{
	$error = false;
	//------------------------------------------------------------------------------
	// Connexion à la bdd
	include_once("connect_db.php");
	$conn = connect_db();
	if(!$conn) {
		echo "<p style='color:red;'>Impossible de se connecter à la bdd</p>";
		exit;
	}
	$id = (int) $_SESSION['id'];
	include_once("get_user_infos.php");
	$old_user = get_user_infos($id);

	//------------------------------------------------------------------------------
	// Vérifie si le nouveau login est déjà pris
	if($old_user['login'] != $new_user['login']) {
		$is_login_used = is_login_used($conn,$new_user['login']);
		if($is_login_used != false && $is_login_used != $id) {
			$error_msg['login'] = "Ce pseudo est déjà pris!";
			$error = true;
		}
	}

	//------------------------------------------------------------------------------
	// ▼ Mot de passe
	//------------------------------------------------------------------------------
	// Ancien mot de passe incorrect
	if(!empty($old_password) && $old_user['password'] != $old_password) {
		$error_msg['old-password'] = "L'ancien mot de passe ne correspond pas!";
		$error = true;
	}
	//------------------------------------------------------------------------------
	// Aucun changement de mot de passe
	else if(empty($old_password) && empty($new_password)) {
		$new_user['password'] = $old_user['password'];
	}

	//------------------------------------------------------------------------------
	// ▼ Avatar
	// 3 cas:
	// 1. aucune nouvelle image n'a été envoyée ET le login ne change pas :
	//     - aucune modification à appliquer
	// 2. aucune nouvelle image n'a été envoyée ET le login change :
	//     - mettre à jour le nom de l'image
	//     - mettre à jour le champ avatar
	// 3. une nouvelle image a été envoyée:
	//     - supprimer l'ancienne image
	//     - copier la nouvelle image
	//     - mettre à jour le champ avatar (login modifié ET/OU extension modifée)
	//------------------------------------------------------------------------------
	// * 1. Aucune nouvelle image n'a été envoyée ET le login ne change pas
	if(empty($_FILES['avatar']['tmp_name']) && $old_user['login'] == $new_user['login']) {
		$new_user['avatar'] = $old_user['avatar'];
	}
	//------------------------------------------------------------------------------
	// * 2. Aucune nouvelle image n'a été envoyée ET le login change
	else if(empty($_FILES['avatar']['tmp_name']) && $old_user['login'] != $new_user['login']) {
		$image_type         = substr($old_user['avatar'],strrpos($old_user['avatar'],"."));
		$new_user['avatar'] = 'avatar_' . $new_user['login'] . $image_type;
		rename($dest_dir . $old_user['avatar'],$dest_dir . $new_user['avatar']);
	}
	//------------------------------------------------------------------------------
	// * 3. Une nouvelle image a été envoyée
	else {
		unlink("../pictures/" . $old_user['avatar']);
		$avatar_url  = $_FILES['avatar']['tmp_name'];
		$avatar_name = $_FILES['avatar']['name'];
		$image_type  = substr($avatar_name,strrpos($avatar_name,"."));
		$new_user['avatar'] = 'avatar_' . $new_user['login'] . $image_type;
		copy_avatar($avatar_url,"../pictures/" . $new_user['avatar']);
	}

	//------------------------------------------------------------------------------
	// Applique les modifications à l'utilisateur
	if($error === false) alter_user($conn,$new_user,$id);
}


//------------------------------------------------------------------------------
// ▼ MAIN
//------------------------------------------------------------------------------
if(!isset($_SESSION['id'])) {
	echo "<p style='color:red;'>Erreur: un invité ne peut modifier un profil utilisateur!</p>";
	exit;
}
// Nouvelles informations
$new_user = array('name'      => test_input($_REQUEST['name']),
                  'firstname' => test_input($_REQUEST['firstname']),
                  'login'     => test_input($_REQUEST['login']),
                  'password'  => test_input($_REQUEST['new-password']));
// Confirmation de l'ancien mot de passe
$old_password = test_input($_REQUEST['old-password']);
$error = false;
// * Vérification des données entrées dans le formulaire
check_invalid_info($error_msg,$error,$new_user,$old_password);
if($error === false) modify_profile($new_user,$old_password);

unset($new_user);
unset($old_password);


?>

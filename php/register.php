<?php

// include("php/display_globals.php"); // affiche le contenu des variables globales

//------------------------------------------------------------------------------
// Test input (pris sur w3schools)
// http://www.w3schools.com/php/showphp.asp?filename=demo_form_validation_escapechar
//------------------------------------------------------------------------------
function test_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

//------------------------------------------------------------------------------
// * Copie l'avatar vers le serveur
//------------------------------------------------------------------------------
function copy_avatar($url,$dest_file)
{
	$dest_dir  = dirname(__FILE__) . "/pictures/";
	$dest_file = $dest_dir . $dest_file;
	try {
		copy($url,$dest_file);
	} catch(Exception $e) {
		echo "<p style='color:red;'>L'image n'a pas pu être ajoutée...</p>";
	}
}

//------------------------------------------------------------------------------
// * Vérifie si les informations sont correctes
//------------------------------------------------------------------------------
function check_user_info($user,$avatar_url)
{
	return !(empty($user['login']) ||
	         empty($user['password']) ||
	         empty($user['name']) ||
	         empty($user['firstname']) ||
	         empty($user['avatar']) ||
	         empty($avatar_url) ||
	         strlen($user['login']) > 15 ||
	         strlen($user['password']) > 32 ||
	         strlen($user['name']) > 15 ||
	         strlen($user['firstname']) > 15);
}

//------------------------------------------------------------------------------
// * Vérifie si un utilisateur avec le même nom et le même prénom existe déjà
// Renvoie vrai si la requête échoue
//------------------------------------------------------------------------------
function user_already_exists($conn,$user)
{
	$stmt = query($conn,"SELECT id FROM personnes WHERE login='$user[login]'");
	if(!$stmt || $stmt->rowCount() > 0) {
		return true;
	}
	return false;
}

//------------------------------------------------------------------------------
// * Ajoute l'utilisateur à la base de données
//------------------------------------------------------------------------------
function insert_user($conn,$user)
{
	if(user_already_exists($conn,$user)) {
		echo "<p style='color:red;'>Un autre utilisateur avec le même pseudo existe déjà!</p>";
		return false;
	}
	$insert = query($conn,"INSERT INTO personnes (nom,prenom,avatar,login,password)
	                VALUES ('$user[name]','$user[firstname]','$user[avatar]','$user[login]','$user[password]')");
	if(!$insert) {
		echo "<p style='color:red;'>Erreur: l'utilisateur n'a pas pu être ajouté!</p>";
		return false;
	}
	return true;
}

//------------------------------------------------------------------------------
// * Inscription
// Créer l'utilisateur dans la bdd si les informations sont valides
// Si l'opération réussit, copie l'image de l'avatar
//------------------------------------------------------------------------------
function register($user,$avatar_url)
{
	if(!check_user_info($user,$avatar_url)) return false;
	include_once("php/connect_db.php");
	$conn = connect_db();
	if(!$conn) {
		echo "<p style='color:red;'>Impossible de se connecter à la bdd</p>";
		return false;
	}
	include_once("php/query.php");
	if(insert_user($conn,$user)) {
		copy_avatar($avatar_url,$user['avatar']);
		$conn = "";
		return true;
	}
	$conn = "";
	return false;
}

//------------------------------------------------------------------------------
// * MAIN
// Récupère les informations transmises via le formulaire
//------------------------------------------------------------------------------
$user = array('name'=>'','firstname'=>'','login'=>'','password'=>'');

$is_info_set = (isset($_REQUEST['name']) &&
                isset($_REQUEST['firstname']) &&
                isset($_REQUEST['login']) &&
                isset($_REQUEST['password']) &&
                isset($_FILES['avatar']));

if($is_info_set) {
	// Informations générales
	$user = array('name'      => test_input($_REQUEST['name']),
	              'firstname' => test_input($_REQUEST['firstname']),
	              'login'     => test_input($_REQUEST['login']),
	              'password'  => test_input($_REQUEST['password']));

	// URL de l'image (serveur & upload)
	$avatar_url     = $_FILES['avatar']['tmp_name'];
	$avatar_name    = $_FILES['avatar']['name'];
	$image_type     = substr($avatar_name,strpos($avatar_name,"."));
	$user['avatar'] = 'avatar_' . $user['login'] . $image_type;

	// Inscription
	// Redirige l'utilisateur sur l'index si l'opération réussit
	if(register($user,$avatar_url)) {
		echo "<p style='color:green;'>Utilisateur enregistré!</p>";
		echo "<p>Redirection vers l'<a href='index.php'>index</a>...</p>";
		header('refresh:5;url=index.php');
		exit();
	}

	unset($user['password']);
	unset($avatar_name);
	unset($image_type);
	unset($avatar_url);
}


?>

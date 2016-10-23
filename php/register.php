<?php

//==============================================================================
//
// ■ Register
// -- Objet : Traitement des données du formulaire d'inscription
// -- Par : Julien Delvaux & Julien Boge
// -- Dernière modification : 23.10.16
//==============================================================================


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
// * Vérifie si les informations sont correctes (vide & longueur)
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
// * Copie l'avatar sur le serveur
//------------------------------------------------------------------------------
function copy_avatar($url,$dest_file)
{
	$dest_dir  = dirname($_SERVER['SCRIPT_FILENAME']) . "/pictures/";
	$dest_file = $dest_dir . $dest_file;
	if(copy($url,$dest_file)) {
		return true;
	}	else {
		echo "<p style='color:red;'>L'image n'a pas pu être ajoutée...</p>";
		return false;
	}
}

//------------------------------------------------------------------------------
// * Vérifie si le login est déjà pris
//------------------------------------------------------------------------------
function user_login_used($conn,$login)
{
	try {
		$user_exists = $conn->prepare("SELECT login
	                                 FROM personnes
	                                 WHERE login = :login");
		$user_exists->bindParam(':login',$user['login'],PDO::PARAM_STR,15);
		$user_exists->execute();
		if(!$user_exists || $user_exists->rowCount() > 0) {
			echo "<p style='color:red;'>Un autre utilisateur avec le même pseudo existe déjà!</p>";
			return true;
		}
		return false;
	}	catch(Exception $e) {
		echo "<br/>" . $e->getMessage() . "<br/>";
		return true;
	}
}


//------------------------------------------------------------------------------
// * Ajoute l'utilisateur à la base de données
//------------------------------------------------------------------------------
function insert_user($conn,$user)
{
	try { // Insère l'utilisateur
		$insert = $conn->prepare("INSERT INTO personnes (nom,prenom,avatar,login,password)
	                            VALUES (:name,:firstname,:avatar,:login,:password)");
		$insert->bindParam(':name',      $user['name'],      PDO::PARAM_STR,15);
		$insert->bindParam(':firstname', $user['firstname'], PDO::PARAM_STR,15);
		$insert->bindParam(':avatar',    $user['avatar'],    PDO::PARAM_STR,30);
		$insert->bindParam(':login',     $user['login'],     PDO::PARAM_STR,15);
		$insert->bindParam(':password',  $user['password'],  PDO::PARAM_STR,32);
		$insert->execute();
		return true;
	}	catch(Exception $e) {
		echo "<br/>" . $e->getMessage() . "<br/>";
		return false;
	}
}

//------------------------------------------------------------------------------
// * Inscription
// Créer l'utilisateur dans la bdd si les informations sont valides
// Si l'opération réussit, copie l'image de l'avatar
//------------------------------------------------------------------------------
function register($user,$avatar_url)
{

	// Vérifie les infos
	if(!check_user_info($user,$avatar_url)) return false;

	// Connexion bdd
	include_once("php/connect_db.php");
	$conn = connect_db();
	if(!$conn) {
		echo "<p style='color:red;'>Impossible de se connecter à la bdd</p>";
		return false;
	}

	return (!user_login_used($conn,$user['login']) &&
	        insert_user($conn,$user,$avatar_url) &&
		      copy_avatar($avatar_url,$user['avatar']));
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
	$image_type     = substr($avatar_name,strrpos($avatar_name,"."));
	$user['avatar'] = 'avatar_' . $user['login'] . $image_type;

	// Inscription
	// Redirige l'utilisateur sur l'index si l'opération réussit
	if(register($user,$avatar_url)) {
		echo "<p style='color:green;'>Utilisateur enregistré!</p>";
		echo "<p>Redirection vers l'<a href='index.php'>index</a>...</p>";
		header('refresh:5;url=index.php');
		exit;
	}

	unset($user['password']);
	unset($avatar_name);
	unset($image_type);
	unset($avatar_url);

}


?>

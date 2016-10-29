<?php

//------------------------------------------------------------------------------
// Test input (pris sur w3schools)
// http://www.w3schools.com/php/showphp.asp?filename=demo_form_validation_escapechar
//------------------------------------------------------------------------------
function test_input($data)
{
  $data = trim($data);             // Supprime les espaces de début et de fin
  $data = stripslashes($data);     // Supprime les antislashs (\)
  $data = htmlspecialchars($data); // Convertit les charactères spéciaux en HTML
  return $data;
}

var_dump($_REQUEST); echo "<br>"; echo "<br>";
var_dump($_SERVER);  echo "<br>"; echo "<br>";
var_dump($_COOKIE);  echo "<br>"; echo "<br>";

if(!isset($_SESSION['id']) && isset($_COOKIE['remember-user'])) {
	$id = $_COOKIE['remember-user'];
	//------------------------------------------------------------------------------
	// Connexion à la bdd
	include_once("../php/connect_db.php");
	$conn = connect_db();
	if(!$conn) {
		echo "<p style='color:red;'>Impossible de se connecter à la bdd</p>";
		exit;
	}
	//------------------------------------------------------------------------------
	// Récupération des informations de l'utilisateur
	try {
		$sql = "SELECT login,nom,prenom,avatar
		        FROM personnes
		        WHERE id=:id
		        LIMIT 1";
		$user = $conn->prepare($sql);
		$user->bindParam(':id',$id,PDO::PARAM_INT);
		$user->execute();
		if($user === false || $user->rowCount() <= 0) {
			echo "<p>Erreur: cookie incorrect?</p>";
			echo "<p>Redirection vers l'<a href='../index.php'>index</a>...</p>";
			header('refresh:5;url=../index.php');
			exit;
		}
		$user = $user->fetch(PDO::FETCH_ASSOC);
	}	catch(Exception $e) {
		echo "<br/>" . $e->getMessage() . "<br/>";
		exit;
	}
	//------------------------------------------------------------------------------
	// Connexion réussie
	session_start();
	$_SESSION['id']        = (int) $user['id'];
	$_SESSION['login']     = $login;
	$_SESSION['name']      = $user['nom'];
	$_SESSION['firstname'] = $user['prenom'];
} else if($_SERVER['REQUEST_METHOD'] != "POST") {
	// Vérification
	session_start();
	if(!isset($_SESSION['id'])) {
		echo "<p>Erreur: vous n'êtes pas connecté!<p>";
		echo "<p>Redirection vers l'<a href='../index.php'>index</a>...</p>";
		header('refresh:5;url=../index.php');
		exit;
	}
	// var_dump($_SESSION); echo "<br>";
} else if($_SERVER['REQUEST_METHOD'] == "POST") {
	// Demande de connexion
	$login    = test_input($_REQUEST['login']);
	$password = test_input($_REQUEST['password']);

	//------------------------------------------------------------------------------
	// Connexion à la bdd
	include_once("../php/connect_db.php");
	$conn = connect_db();
	if(!$conn) {
		echo "<p style='color:red;'>Impossible de se connecter à la bdd</p>";
		exit;
	}

	//------------------------------------------------------------------------------
	// Récupération des informations de l'utilisateur
	try {
		$sql = "SELECT id,nom,prenom,avatar
		        FROM personnes
		        WHERE login=:login AND password=:password
		        LIMIT 1";
		$user = $conn->prepare($sql);
		$user->bindParam(':login',   $login,   PDO::PARAM_STR,15);
		$user->bindParam(':password',$password,PDO::PARAM_STR,32);
		$user->execute();
		if($user === false || $user->rowCount() <= 0) {
			echo "<p>Erreur: login ou mot de passe incorrect</p>";
			echo "<p>Redirection vers l'<a href='../index.php'>index</a>...</p>";
			header('refresh:5;url=../index.php');
			exit;
		}
		$user = $user->fetch(PDO::FETCH_ASSOC);
	}	catch(Exception $e) {
		echo "<br/>" . $e->getMessage() . "<br/>";
		exit;
	}

	//------------------------------------------------------------------------------
	// Connexion réussie
	session_start();
	$_SESSION['id']        = (int) $user['id'];
	$_SESSION['login']     = $login;
	$_SESSION['name']      = $user['nom'];
	$_SESSION['firstname'] = $user['prenom'];

	//------------------------------------------------------------------------------
	// Création du cookie (si demandé)
	if($_REQUEST['remember-me'] == "on") {
		setcookie("remember-user",$user['id'],time() + (3600 * 24 * 7 * 31));
	}

	//------------------------------------------------------------------------------
	// Redirection vers private/index
	echo "<p>Connexion...<p>";
	echo "<p>Redirection vers l'<a href='index.php'>index</a>...</p>";
	header('refresh:5;url=index.php');
}


?>

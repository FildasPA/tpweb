<?php

//==============================================================================
//
// ■ Verif login
// -- Objet : Connexion par formulaire, cookie ou simple reprise de session
// -- Par : Julien Delvaux & Julien Boge
// -- Dernière modification : 29.10.16
//
//==============================================================================
//
// ▼ 4 cas possibles :
// - une session existe, reprise simple de la session
// - aucune session n'était en cours, mais un cookie existe
// - aucune session et aucun cookie, mais une demande de connexion par
//    formulaire a été émise
// - aucun des 3 cas précédents => tentative d'accès à une ressource privée
//    => erreur => redirection
//
//==============================================================================


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


session_start(); // ouverture/reprise de session simple

// echo "REQUEST:<br>"; var_dump($_REQUEST); echo "<br>"; echo "<br>";
// echo "SERVER:<br>";  var_dump($_SERVER);  echo "<br>"; echo "<br>";
// echo "SESSION:<br>"; var_dump($_SESSION); echo "<br>"; echo "<br>";
// echo "COOKIE1:<br>"; var_dump($_COOKIE);  echo "<br>"; echo "<br>";
// setcookie("remember-user","",time()-3600);
// echo "COOKIE2:<br>"; var_dump($_COOKIE);  echo "<br>"; echo "<br>";

//------------------------------------------------------------------------------
// Connexion à la bdd
include_once("connect_db.php");
$conn = connect_db();
if(!$conn) {
	echo "<p style='color:red;'>Impossible de se connecter à la bdd</p>";
	exit;
}


//------------------------------------------------------------------------------
// Pas de session, mais demande de connexion (formulaire)
//------------------------------------------------------------------------------
if($_SERVER['REQUEST_METHOD'] == "POST" && $_REQUEST["form-name"] == "login") {

	//------------------------------------------------------------------------------
	// Récupération des informations
	$login    = test_input($_REQUEST['login']);
	$password = test_input($_REQUEST['password']);

	//------------------------------------------------------------------------------
	// Récupération des informations de l'utilisateur s'il existe
	try {
		$sql = "SELECT id,nom,prenom,password
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
	// Connexion réussie => début de session
	$_SESSION['id']        = (int) $user['id'];
	$_SESSION['login']     = $login;
	$_SESSION['name']      = $user['nom'];
	$_SESSION['firstname'] = $user['prenom'];

	//------------------------------------------------------------------------------
	// Création du cookie (si demandé) => conserve l'id et le mot de passe
	if($_REQUEST['remember-me'] == "on") {
		setcookie("id",      $user['id'],      time() + (3600 * 24 * 7 * 31));
		setcookie("password",$user['password'],time() + (3600 * 24 * 7 * 31));
	}
}

//------------------------------------------------------------------------------
// Pas de session en cours, mais cookie existe
//------------------------------------------------------------------------------
else if(!isset($_SESSION['id']) && isset($_COOKIE['remember-user'])) {

	//------------------------------------------------------------------------------
	// Récupération des informations de l'utilisateur
	try {
		$sql = "SELECT login,nom,prenom
		        FROM personnes
		        WHERE id=:id AND password=:password
		        LIMIT 1";
		$user = $conn->prepare($sql);
		$user->bindParam(':id',      $_COOKIE['id'],      PDO::PARAM_INT);
		$user->bindParam(':password',$_COOKIE['password'],PDO::PARAM_STR,32);
		$user->execute();
		if($user === false || $user->rowCount() == 0) {
			echo "<p>Erreur: informations du cookie incorrectes</p>";
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
	$_SESSION['id']        = (int) $_COOKIE['id'];
	$_SESSION['login']     = $user['login'];
	$_SESSION['name']      = $user['nom'];
	$_SESSION['firstname'] = $user['prenom'];
}

//------------------------------------------------------------------------------
// Reprise simple de la session
else if(isset($_SESSION['id'])) {
	// Aucune erreur à signaler
}

//------------------------------------------------------------------------------
// Pas de session en cours, pas de cookie valide, ni demande de connexion par
// formulaire => erreur, redirection vers l'index publique
//------------------------------------------------------------------------------
else {
	unset($conn);
	echo "<p>Erreur: vous n'êtes pas connecté!<p>";
	echo "<p>Redirection vers l'<a href='../index.php'>index</a>...</p>";
	header('refresh:5;url=../index.php');
	exit;
}

?>

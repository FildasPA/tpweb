<!doctype html>
<html lang="fr">
<head>
  <title>Inscription</title>
	<meta charset="utf-8" />
	<meta name="author" content="Delvaux Julien, id:1500182, Boge Julien, id:1502198"/>
	<meta name="description" content="Inscription utilisateur" />
  <link rel="stylesheet" href="css/main.css">

  <script type="text/javascript" src="js/checkInscriptionForm.js"></script>
</head>
<body>


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
	// Le redirige sur l'index si l'inscription a réussie
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
	// * Créer l'utilisateur dans la bdd
	// Si les informations entrées sont invalides, ne fait rien
	//------------------------------------------------------------------------------
	function create_user($user,$avatar_url)
	{
		if(!check_user_info($user,$avatar_url)) return false;
		include_once("php/connect_db.php");
		$conn = connect_db();
		if(!$conn) {
			echo "<p style='color:red;'>Impossible de se connecter à la bdd</p>";
			return;
		}
		include_once("php/query.php");
		if(insert_user($conn,$user)) {
			copy_avatar($avatar_url,$user['avatar']);
			echo "<p style='color:green;'>Utilisateur enregistré!</p>";
			echo "<p>Redirection vers l'<a href='index.php'>index</a>...</p>";
			header('refresh:5;url=index.php');
			$conn = "";
			exit();
		}
		$conn = "";
	}

	//------------------------------------------------------------------------------
	// * MAIN
	// Récupère les informations transmises via le formulaire
	//------------------------------------------------------------------------------
	// Informations générales
	$user = array('name'      => test_input($_REQUEST['name']),
	              'firstname' => test_input($_REQUEST['firstname']),
	              'login'     => test_input($_REQUEST['login']),
	              'password'  => test_input($_REQUEST['password']));

	// URL de l'image (serveur & upload)
	$avatar_url = $_FILES['avatar']['tmp_name'];
	$avatar_name = $_FILES['avatar']['name'];
	$image_type = substr($avatar_name,strpos($avatar_name,"."));
	$user['avatar'] = 'avatar_' . test_input($_REQUEST['login']) . $image_type;

	// Création de l'utilisateur
	create_user($user,$avatar_url);

	unset($user['password']);
	unset($avatar_name);
	unset($image_type);
	unset($avatar_url);


	?>


	<br><br>
	<form id="form-inscription" name="inscription" method="POST" action="inscription.php" enctype="multipart/form-data">
		<label id="inscription-label">Inscription</label>
		<div id="login-form-element">
			<label>Pseudo</label>
			<input id="login" name="login" type="text/html" placeholder="Pseudo" onblur="checkLogin()" value="<?php echo $user['login']; ?>">
			<div id="error-login" class="error-message"></div>
		</div>
		<div id="password-form-element">
			<label>Mot de passe</label>
			<input id="password" name="password" type="password" placeholder="Mot de passe" onblur="checkPassword()">
			<div id="error-password" class="error-message"></div>
		</div>
		<div id="name-form-element">
			<label>Identité</label>
			<input id="firstname" name="firstname" type="text/html" placeholder="Prénom" onblur="checkFirstname()" value="<?php echo $user['firstname']; ?>">
			<input id="name" name="name" type="text/html" placeholder="Nom" onblur="checkName()" value="<?php echo $user['name']; ?>">
			<div id="error-firstname" class="error-message"></div>
			<div id="error-name" class="error-message"></div>
		</div>
		<div id="avatar-form-element">
			<label>Avatar</label>
			<input id="max-file-size" name="max-file-size" type="hidden" value="1000000" />
			<input id="avatar" name="avatar" type="file">
			<div id="error-avatar" class="error-message"></div>
		</div>
		<div id="submit-element">
			<input id="submit-button" class="button" name="valider" value="Inscription" onclick="sendForm()" onkeypress="sendForm()">
		</div>
	</form>

	<?php include("includes/bottom.php");	?>

</body>
</html>

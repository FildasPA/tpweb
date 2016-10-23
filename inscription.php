<!doctype html>
<html lang="fr">
<head>
  <title>Inscription</title>
	<meta charset="utf-8" />
	<meta name="author" content="Delvaux Julien, id:1500182, Boge Julien, id:1502198"/>
	<meta name="description" content="Inscription utilisateur" />
  <link rel="stylesheet" href="css/main.css">
</head>
<body onload="checkFormOnLoad()">

	<?php include("php/register.php"); ?>

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

  <script type="text/javascript" src="js/checkInscriptionForm.js"></script>

</body>
</html>

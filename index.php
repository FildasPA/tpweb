<!doctype html>
<html lang="fr">
<head>
	<title>Index - Liste des utilisateurs</title>
	<meta charset="utf-8" />
	<meta name="author" content="Delvaux Julien, id:1500182, Boge Julien, id:1502198"/>
	<meta name="description" content="Index du site / Liste des utilisateurs enregistrés" />
  <link rel="stylesheet" href="css/main.css">
</head>
	<body>

		<div id="main-container">
			<form id="form-login" name="login" method="POST" action="private/index.php" enctype="multipart/form-data">
				<h3>Se connecter</h3>
				<div id="login-form-element">
					<label>Pseudo</label>
					<input name="login" type="text" placeholder="Pseudo">
				</div>
				<div id="password-form-element">
					<label>Mot de passe</label>
					<input name="password" type="password" placeholder="Password">
				</div>
				<div id="">
				<input type="checkbox" name="remember-me" style="width:auto !important;display:inline-block;">
					<label style="width:auto !important;display:inline-block;">Rester connecté</label>
				</div>
				<div id="submit-element">
					<input id="submit-button" class="button" type="submit" name="valider" value="Connexion">
				</div>
			</form>

			<?php include("php/display_users_list.php"); ?>
			<?php include("includes/footer.php"); ?>
		</div>

	</body>
</html>

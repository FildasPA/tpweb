<?php

include("../php/verif_login.php");

if(!isset($_SESSION)) {
	//------------------------------------------------------------------------------
	// Redirection vers private/index
	echo "<p>Vous devez être connecté pour accéder à cette page!<p>";
	echo "<p>Redirection vers l'<a href='index.php'>index</a>...</p>";
	header('refresh:5;url=index.php');
	exit;
}

?>

<!doctype html>
<html lang="fr">
<head>
	<title>Modifier profil utilisateur</title>
	<meta charset="utf-8" />
	<meta name="author" content="Delvaux Julien, id:1500182, Boge Julien, id:1502198"/>
	<meta name="description" content="Modifier un profil utilisateur" />
  <link rel="stylesheet" href="../css/main.css">
</head>

	<body>
		<div id="main-container">
		<?php

		// var_dump($_REQUEST); echo "<br>";

		// Traitement des modifications du profil
		if($_SERVER['REQUEST_METHOD'] == "POST") {
			include("../php/modify_profile.php");
		}

		// Récolter informations utilisateur
		include("../php/get_user_infos.php");
		$id   = (int) $_SESSION['id'];
		$user = get_user_infos($id);
		if(!$user) { // Si l'utilisateur n'existe pas, affiche l'erreur et le footer
			echo "<p>L'id $id ne correspond à aucun utilisateur.</p>";
			include("../includes/footer.php");
			exit;
		}

		?>

		<form id="form-modify-profile" name="modify-profile" method="POST" action="modify_profile.php" enctype="multipart/form-data">
			<div id="user-profile">
				<h3 style="text-align:left;">Profil de <?php echo $user['login']; ?></h3>
				<div id="user-info-block">
					<div class="info-element">
						<div class="info-label">Login:</div>
						<div id="user-login">
							<input id="login" name="login" type="text/html" value="<?php echo $user['login']; ?>">
							<div id="error-login" class="error-message"></div>
						</div>
					</div>
					<div class="info-element">
						<div class="info-label">Modifier le mot de passe:</div>
						<div id="user-login">
							<input id="password" name="old-password" type="password" placeholder="Ancien mot de passe" onblur="checkPassword()" style="margin-bottom: 10px;"><br>
							<input id="password" name="new-password" type="password" placeholder="Nouveau mot de passe" onblur="checkPassword()">
							<div id="error-password" class="error-message"></div>
						</div>
					</div>
					<div class="info-element">
						<div class="info-label">Prénom:</div>
						<div id="user-firstname">
							<input id="firstname" name="firstname" type="text/html" value="<?php echo $user['prenom']; ?>">
							<div id="error-firstname" class="error-message"></div>
						</div>
					</div>
					<div class="info-element">
						<div class="info-label">Nom:</div>
						<div id="user-name">
							<input id="name" name="name" type="text/html" value="<?php echo $user['nom']; ?>">
							<div id="error-name" class="error-message"></div>
						</div>
					</div>
					<div class="info-element" style="overflow:auto;">
						<div class="info-label">Avatar:</div>
						<img id="user-avatar-image" src="../pictures/<?php echo $user['avatar']; ?>" style="float:left;">
						<input id="max-file-size" name="max-file-size" type="hidden" value="1000000" />
						<input id="avatar" name="avatar" type="file" style="float:left;margin-left: 15px;margin-top: 25px;">
						<div id="error-avatar" class="error-message"></div>
					</div>
					<input name="id" type="hidden" value="<?php echo $id; ?>">
					<div id="submit-element">
						<input id="submit-button" class="button" name="valider" type="submit" value="Modifier profil">
					</div>
				</div>
			</div>
		</form>

		<?php include("../includes/footer.php");	?>

		</div>
	</body>
</html>

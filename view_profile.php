<?php
	session_start();
	if(isset($_SESSION['id'])) {
		include("lib/verif_login.php");
	}
?>

<!doctype html>
<html lang="fr">
<head>
	<title>Voir un profil utilisateur</title>
	<meta charset="utf-8" />
	<meta name="author" content="Delvaux Julien, id:1500182, Boge Julien, id:1502198"/>
	<meta name="description" content="Voir un profil utilisateur" />
  <link rel="stylesheet" href="css/main.css">
</head>

	<body>
		<div id="main-container">

		<?php

		// Obtenir les informations utilisateur
		include("lib/get_user_infos.php");
		$id   = (int) $_REQUEST['id'];
		$user = get_user_infos($id);
		$user['password'] = ''; // sécurité?

		// Si aucune info (utilisateur inexistant?) affiche l'erreur et le footer
		if($user === false) {
			echo "<p>L'id $id ne correspond à aucun utilisateur.</p>";
			include("includes/footer.php");
			exit;
		}

		?>

		<div id="user-profile">
			<h3>Profil de <?php echo $user['login']; ?></h3>
			<div id="user-info-block">
				<div class="info-element">
					<div class="info-label">Prénom:</div>
					<div id="user-firstname"><?php echo $user['prenom']; ?></div>
				</div>
				<div class="info-element">
					<div class="info-label">Nom:</div>
					<div id="user-name"><?php echo $user['nom']; ?></div>
				</div>
				<div class="info-element">
					<div class="info-label">Avatar:</div>
					<img id="user-avatar-image" src="pictures/<?php echo $user['avatar']; ?>">
				</div>
			</div>
		</div>

		<?php
		// Affichage du footer correct (retour à l'index publique ou privé)
		if(isset($_SESSION['id'])) {
			echo "<div id=\"footer\">";
			echo "<a href=\"private/index.php\">Index</a>";
			echo "</div>";
		} else {
			include("includes/footer.php");
		}
		?>

		</div>
	</body>
</html>

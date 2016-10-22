<!doctype html>
<html lang="fr">
<head>
	<title>Liste des utilisateurs</title>
	<meta charset="utf-8" />
	<meta name="author" content="Delvaux Julien, id:1500182, Boge Julien, id:1502198"/>
	<meta name="description" content="Liste des utilisateurs enregistrés" />
  <link rel="stylesheet" href="css/main.css">
</head>
	<body>

		<form name="login" method="POST" action="connexion.php" enctype="multipart/form-data">
			<label><b>Se connecter : </b></label><br><br>
			<input name="pseudo"    type="text/html" placeholder="pseudo">    <br>
			<input name="password" type="password" placeholder="Mot de passe"><br><br>
			<input class="button" type="submit" name="valider" value="Connexion">
		</form>


		<?php
			include_once("php/connect_db.php");
			$conn = connect_db();
			if(!$conn) {
				echo "<p style='color:red;'>Impossible de se connecter à la bdd</p>";
				return;
			}
			include_once("php/query.php");
			display_users($conn);
			$conn = "";

			// Afficher les utilisateurs
			function display_users($conn)
			{
				// Obtenir la liste des utilisateurs
				$users = query($conn,"SELECT * FROM personnes;");
				if(!$users) {
					echo "<p>La requête demandant la liste des utilisateurs a échoué</p>";
					return;
				}
				// Afficher la liste
				echo "<br/>";
				echo "<br/>";
				echo "<h4>Liste des utilisateurs inscrits:</h4>";
				echo "<br/>";
				echo "<table>";
				echo "<tr>";
				echo "<th>Nom</th>";
				echo "<th>Prénom</th>";
				echo "</tr>";
				while($user = $users->fetch(PDO::FETCH_ASSOC)) {
					echo "<tr class='user' title=\"Voir le profil\" onclick=\"window.document.location='view_profile.php?id=" . $user['id'] . "';\">";
					echo "<td>" . $user['nom']    . "</td>";
					echo "<td>" . $user['prenom'] . "</td>";
					echo "</tr>";
				}
				echo "</table>";
			}

			// javascript page précédente: document.referrer
		?>

		<?php include("includes/bottom.php");	?>
	</body>
</html>

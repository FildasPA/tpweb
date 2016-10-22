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
		<?php
			include_once("php/connect_db.php");
			$conn = connect_db();
			if(!$conn) {
				echo "<p style='color:red;'>Impossible de se connecter à la bdd</p>";
				return;
			}
			include_once("php/query.php");


			//------------------------------------------------------------------------------
			// * Retourne l'utilisateur correspondant à l'id envoyée en paramètre
			//------------------------------------------------------------------------------
			function get_user($conn,$id) {
				$user = query($conn,"SELECT * FROM personnes WHERE id = $id;");
				if(!$user) {
					echo "<p>Aucun utilisateur avec l'id $id n'a été trouvé.</p>";
					return;
				}
				$user = $user->fetch(PDO::FETCH_ASSOC);
				return $user;
			}

			//------------------------------------------------------------------------------
			// * Formulaire permettant de modifier un utilisateur envoyé en paramètre
			//------------------------------------------------------------------------------
			function modify_user($conn,$user) {
				echo "<br/>";
				echo "<br/>";
				echo "<h4>Profil de " . $user['login'] . ":</h4>";
				echo "<br/>";
				echo "<table>";
				echo "<tr>";
				echo "<th>Id</th>";
				echo "<th>Login</th>";
				echo "<th>Nom</th>";
				echo "<th>Prénom</th>";
				echo "<th>Adresse avatar</th>";
				echo "</tr>";
				echo "<tr>";
				echo "<td>" . $user['id']     . "</td>";
				echo "<td>" . $user['login'] . "</td>";
				echo "<td> ". $user['nom'] . "</td>";
				echo "<td>" . $user['prenom'] . "</td>";
				echo "<td style=\"width:200px\">" . $user['avatar'] . "</td>";
				echo "</tr>";
				echo "</table>";
			}

			$id   = $_REQUEST['id'];
			$user = get_user($conn,$id);
			modify_user($conn,$user);

			$conn = "";

		?>

		<?php include("includes/bottom.php");	?>
	</body>
</html>

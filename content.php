<!DOCTYPE html>
	<head>
		<meta charset="utf-8" />
		<meta name="author" content="Delvaux Julien, id:1500182"/>
		<meta name="description" content="Affichage de la liste des utilisateurs enregistrés dans la BDD, et suppression d'un utilisateur." />
	</head>
	<body>
		<div id="php">
			<form name="Delete" method="POST" action="content.php" enctype="multipart/form-data">
				<label><b>Suppression : </b></label>
				<br/>
				<br/>
				<input name="id" type="text/html" placeholder="ID">
				<br/>
				<input class="button" type="submit" name="valider" value="Supprimer">
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

				// Supprimer utilisateur
				if(isset($_REQUEST['id'])) {
					$id  = $_REQUEST['id'];
					$del = $conn->query("DELETE FROM personnes WHERE id='$id'");
					echo "<p style='color:green;'>Supprimé!</p>";
				}

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
					echo "<b>Liste des utilisateurs inscrits:</b><br/>";
					echo "<br/>";

					echo "<table>";
					echo "<tr>";
					echo "<th>Id</th>";
					echo "<th>Nom</th>";
					echo "<th>Prénom</th>";
					echo "<th>Adresse avatar</th>";
					echo "</tr>";
					while($user = $users->fetch(PDO::FETCH_ASSOC)) {
						echo "<tr id='$user[id]'>";
						echo "<td>" . $user['id'] . "</td>";
						echo "<td>" . $user['nom'] . "</td>";
						echo "<td>" . $user['prenom'] . "</td>";
						echo "<td>" . $user['avatar'] . "</td>";
						echo '<td><input class="button" name="valider" value="Supprimer"></td>';
						echo "</tr>";
						echo "<br/>";
					}
					echo "</table>";
				}

			?>


			<br/>
			<a href="index.php">Index</a>
		</div>
	</body>
</html>

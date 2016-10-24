<?php

//==============================================================================
//
// ■ Display users list
// -- Objet : Affiche la liste des utilisateurs enregistrés dans la bdd
// -- Par : Julien Delvaux & Julien Boge
// -- Dernière modification : 23.10.16
//
//==============================================================================

function display_users()
{
	// Connexion bdd
	include_once("connect_db.php");
	$conn = connect_db();
	if(!$conn) {
		echo "<p style='color:red;'>Impossible de se connecter à la bdd</p>";
		return;
	}

	try {
		// Obtenir la liste des utilisateurs (100 max, à partir du début)
		$sql = "SELECT id,login
	          FROM personnes
	          LIMIT 100";
		$users = $conn->prepare($sql);
		$users->execute();
		if(!$users) {
			echo "<p>La requête demandant la liste des utilisateurs a échoué</p>";
			return;
		}

		// Afficher la liste des utilisateurs
		echo "<h4>Liste des utilisateurs inscrits:</h4>";
		if($users->rowCount() <= 0) {
			echo "<p>Aucun utilisateur enregistré</p>";
			return;
		}
		echo "<table>";
		echo "<tr>";
		// echo "<th>Nom</th>";
		// echo "<th>Prénom</th>";
		echo "<th>Pseudo</th>";
		echo "</tr>";
		while($user = $users->fetch(PDO::FETCH_ASSOC)) {
			echo "<tr class='user' title=\"Voir le profil\" onclick=\"window.document.location='view_profile.php?id=" . $user['id'] . "	';\">";
			echo "<td>" . $user['login']    . "</td>";
			// echo "<td>" . $user['nom']    . "</td>";
			// echo "<td>" . $user['prenom'] . "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}	catch(Exception $e) {
		echo "<br/>" . $e->getMessage() . "<br/>";
		return;
	}
}

display_users();

?>

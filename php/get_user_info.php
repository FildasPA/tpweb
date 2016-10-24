<?php

//==============================================================================
//
// ■ Get user profile
// -- Objet : Renvoie les informations de l'utilisateur correspondant à l'id envoyée
// -- Par : Julien Delvaux & Julien Boge
// -- Dernière modification : 23.10.16
//
//==============================================================================

function get_user_info($id) {

	// Connexion bdd
	include_once("connect_db.php");
	$conn = connect_db();
	if(!$conn) {
		echo "<p style='color:red;'>Impossible de se connecter à la bdd</p>";
		return NULL;
	}

	try {
		$sql = "SELECT nom,prenom,avatar,login
		        FROM personnes
		        WHERE id = :id";
		$user = $conn->prepare($sql);
		$user->bindParam(':id',$id,PDO::PARAM_INT);
		$user->execute();
		if(!$user || $user->rowCount() <= 0) return NULL;
		return $user->fetch(PDO::FETCH_ASSOC);
	}	catch(Exception $e) {
		echo "<br/>" . $e->getMessage() . "<br/>";
		return NULL;
	}
}

?>

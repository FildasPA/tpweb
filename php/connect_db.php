<?php

//------------------------------------------------------------------------------
// * Créer & retourne un objet PDO connecté à la base de données
//------------------------------------------------------------------------------
function connect_db() {
	$user = "etd";
	$pwd  = "123";

	try {
		$conn = new PDO("pgsql:host=localhost;dbname=ceri",$user,$pwd);
		$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
	} catch(PDOException $e) {
		$conn = "";
		echo "<br/>" . $e->getMessage() . "<br/>";
		return false;
	}
	return $conn;
}

?>

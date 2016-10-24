<?php

//------------------------------------------------------------------------------
// * Vérifie si le login se trouve déjà dans la bdd
// Affiche "true" si oui, "false" sinon
// Utilisé pour la vérification du login en AJAX.
//------------------------------------------------------------------------------
$login = "";
if(isset($_REQUEST['login'])) $login = $_REQUEST['login'];
else exit;

include_once("connect_db.php");
$conn = connect_db();
if(!$conn) {
	// echo "<p style='color:red;'>Impossible de se connecter à la bdd</p>";
	exit;
}
try {
	$stmt = $conn->prepare("SELECT login
	                        FROM personnes
	                        WHERE login = :login");
	$stmt->bindParam(':login',$login,PDO::PARAM_STR,15);
	$stmt->execute();
	if($stmt->rowCount() > 0) {
		echo "true";
	} else {
		echo "false";
	}
} catch(Exception $e) {
	echo "<br/>" . $e->getMessage() . "<br/>";
	echo "false";
}

?>

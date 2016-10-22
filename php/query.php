<?php

//------------------------------------------------------------------------------
// * Exécute une requête
// TODO: séparer requête et variables
//------------------------------------------------------------------------------
function query($conn,$sql) {
	try {
		$stmt = $conn->prepare($sql);
		$stmt->execute();
	}	catch(Exception $e) {
		echo "<br/>" . $e->getMessage() . "<br/>";
		return false;
	}
	return $stmt;
}

?>

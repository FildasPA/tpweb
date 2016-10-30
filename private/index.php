<?php

include("../lib/verif_login.php");

?>

<!doctype html>
<html lang="fr">
<head>
	<title>Index - Liste des utilisateurs (v. privée)</title>
	<meta charset="utf-8" />
	<meta name="author" content="Delvaux Julien, id:1500182, Boge Julien, id:1502198"/>
	<meta name="description" content="Index du site / Liste des utilisateurs enregistrés / Version privée" />
  <link rel="stylesheet" href="../css/main.css">
</head>
	<body>

		<div id="main-container">
			<p>Bonjour <?php echo $_SESSION['firstname'] . " " . $_SESSION['name']; ?>!</p>
			<h4>Liste des utilisateurs inscrits</h4>
			<?php	include("../lib/display_users_list.php");	?>
			<div id="footer">
				<a href="index.php">Index</a>
			</div>
		</div>

	</body>
</html>

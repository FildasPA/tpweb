
<div id="footer">
<?php
	if(!isset($_SESSION)) {
		echo "<a href=\"inscription.php\">S'incrire</a><br/>";
	} else  {
		echo "<a href=\"index.php\">Index</a>";
		// echo "<a href=\"private/index.php\">Index</a>";
	}
?>
</div>

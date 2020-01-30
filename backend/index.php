<?php
	$page = 'index';
	require('header.php');

	// Phrase, wenn Passwort zurückgesetzt wurde
	if ( isset($_SESSION["first"]) ) {
		echo $_SESSION["first"];
	}
?>

<div class='row'>
	<div class="col-12">
		<div class="jumbotron">
			<h1>Hallo.</h1>
			<p class="lead">Herzlich willkommen im Backend der <?php echo $institut; ?>.</p>
		</div>
	</div>
</div>

<?php
	require('footer.php');
?>
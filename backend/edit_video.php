<?php
	//Datenbank einlesen
	require_once('../connection.inc.php');
	require_once('../function.inc.php');

	//Rubrik-ID
	$rub_id 	= $_GET['rub_id'];

	//Rubrik-Daten
	$sql_rubrik 	= sql_select_where('all', 'rubrik', array('rub_institut', 'rub_id'), array($institut_id, $rub_id), '', '');
	$row_rubrik 	= mysqli_fetch_assoc($sql_rubrik);
	$category_titel = strtolower(str_replace(' ', '-', $row_rubrik['rub_name']));

	require('header.php');

	$alert 			= '';
	$schluessel 	= 'video';

	// Tabelle auslesen
	$video_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_rubrik', 'txt_schluessel'), array($institut_id, $rub_id, $schluessel), '', '');

	if ( mysqli_num_rows($video_sql) == 1 ) {
		$video_row 				= mysqli_fetch_assoc($video_sql);

		// Variablen vergeben
		$txt_titel 				= $video_row['txt_titel'];
		$txt_einleitung 		= $video_row['txt_einleitung'];
		$txt_einleitung 		= str_replace('<br />', '', $txt_einleitung);
		$txt_einleitung 		= str_replace('\r\n', '', $txt_einleitung);
		$txt_einleitung 		= stripslashes($txt_einleitung);
		$txt_auszug 			= $video_row['txt_auszug'];
		$txt_auszug 			= str_replace('<br />', '', $txt_auszug);
		$txt_auszug 			= str_replace('\r\n', '', $txt_auszug);
		$txt_auszug 			= stripslashes($txt_auszug);
		$txt_beitrag 			= $video_row['txt_beitrag'];
		$txt_beitrag 			= str_replace('<br />', '', $txt_beitrag);
		$txt_beitrag 			= str_replace('\r\n', '', $txt_beitrag);
		$txt_beitrag 			= stripslashes($txt_beitrag);
		$txt_conversion_ziel 	= $video_row['txt_conversion_ziel'];
		$txt_conversion_titel 	= $video_row['txt_conversion_titel'];

	} else {
		$txt_titel 				= '';
		$txt_einleitung 		= '';
		$txt_auszug 			= '';
		$txt_beitrag 			= '';
		$txt_conversion_ziel 	= '';
		$txt_conversion_titel 	= '';
	}

	// speichern
	if (isset($_POST['submit'])) {
		// einlesen der im Formular angegebenen Werte
		$txt_titel 				= mysqli_real_escape_string($db, $_POST['txt_titel']);
		$txt_einleitung 		= mysqli_real_escape_string($db, $_POST['txt_einleitung']);
		$txt_einleitung 		= nl2br($txt_einleitung);
		$txt_auszug 			= mysqli_real_escape_string($db, $_POST['txt_auszug']);
		$txt_auszug 			= nl2br($txt_auszug);
		$txt_beitrag 			= mysqli_real_escape_string($db, $_POST['txt_beitrag']);
		$txt_beitrag 			= nl2br($txt_beitrag);
		$txt_conversion_ziel 	= mysqli_real_escape_string($db, $_POST['txt_conversion_ziel']);
		$txt_conversion_titel 	= mysqli_real_escape_string($db, $_POST['txt_conversion_titel']);

		// Variablen für Fehlerprüfung – Wenn kein Fehler vorhanden ist: fehler=0
		$fehler 				= 0;

		if (mysqli_num_rows($video_sql) == 1) {
			$video_update = sql_update('texte', array('txt_titel', 'txt_einleitung', 'txt_auszug', 'txt_beitrag', 'txt_conversion_titel', 'txt_conversion_ziel', 'chg_user'), array($txt_titel, $txt_einleitung, $txt_auszug, $txt_beitrag, $txt_conversion_titel, $txt_conversion_ziel, $user_email), array('txt_institut', 'txt_rubrik', 'txt_schluessel'), array($institut_id, $rub_id, $schluessel));
		} else {
			$video_update = sql_insert('texte', array('txt_institut', 'txt_rubrik', 'txt_schluessel', 'txt_titel', 'txt_einleitung', 'txt_auszug', 'txt_beitrag', 'txt_conversion_titel', 'txt_conversion_ziel', 'new_user', 'chg_user'), array($institut_id, $rub_id, $schluessel, $txt_titel, $txt_einleitung, $txt_auszug, $txt_beitrag, $txt_conversion_titel, $txt_conversion_ziel, $user_email, $user_email));
		}
		if ($video_update == true) {
			$alert 			.= '<div class="alert alert-success" role="alert">Das Video für die Rubrik <b>'.$row_rubrik['rub_name'].'</b> wurde erfolgreich gespeichert.</div>';
			$txt_einleitung 		= str_replace('<br />', '', $txt_einleitung);
			$txt_auszug 			= str_replace('<br />', '', $txt_auszug);
			$txt_beitrag 			= str_replace('<br />', '', $txt_beitrag);
			$txt_einleitung 		= $_POST['txt_einleitung'];
			$txt_auszug 			= $_POST['txt_auszug'];
			$txt_beitrag 			= $_POST['txt_beitrag'];
			$txt_conversion_ziel 	= $_POST['txt_conversion_ziel'];
			$txt_conversion_titel 	= $_POST['txt_conversion_titel'];
		} else {
			$fehler++;
			$alert   		.= '<div class="alert alert-danger alert-dismissible fade in" role="alert"><b>Fehler!</b> Das Video für die Rubrik <b>'.$row_rubrik['rub_name'].'</b> konnte nicht gespeichert werden.</div>';
			$txt_einleitung 		= str_replace('<br />', '', $txt_einleitung);
			$txt_auszug 			= str_replace('<br />', '', $txt_auszug);
			$txt_beitrag 			= str_replace('<br />', '', $txt_beitrag);
			$txt_einleitung 		= $_POST['txt_einleitung'];
			$txt_auszug 			= $_POST['txt_auszug'];
			$txt_beitrag 			= $_POST['txt_beitrag'];
			$txt_conversion_ziel 	= $_POST['txt_conversion_ziel'];
			$txt_conversion_titel 	= $_POST['txt_conversion_titel'];
		}
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item"><?php echo $row_rubrik['rub_name']; ?></li>
	<li class="breadcrumb-item active">Video bearbeiten</li>
</ol>

<div class="jumbotron">
	<h1>Video bearbeiten</h1>
	<p class="lead">Hier haben Sie die Möglichkeit ein Video für die Rubrik <b><?php echo $row_rubrik['rub_name']; ?></b> zu zufügen/aktualisieren.</p>
</div><!-- /.jumbotron -->

<form action="" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12">
			<?php echo $alert; ?>
		</div>
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-video" aria-hidden="true"></i> &nbsp; Video für die Rubrik <b><?php echo $row_rubrik['rub_name']; ?></b>
				</div>
				<div class="card-body">
					<div class="form-group">
						<label for="txt_auszug">URL zum YouTube-Video:</label>
						<p class="help-block">Geben Sie die URL zum Video an.</p>
						<input type="text" class="form-control" placeholder="URL" name="txt_auszug" value="<?php echo $txt_auszug; ?>">
					</div>
					<div class="form-group">
						<label for="txt_titel">Headline:</label>
						<p class="help-block">Geben Sie eine Headline zum Video an.</p>
						<input type="text" class="form-control" placeholder="Headline" name="txt_titel" value="<?php echo $txt_titel; ?>">
					</div>
					<div class="form-group">
						<label for="txt_einleitung">Einleitung:</label>
						<p class="help-block">Verfassen Sie eine kurze Einleitung/Subline.</p>
						<textarea class="form-control" rows="2" name="txt_einleitung" id="txt_einleitung"><?php echo $txt_einleitung; ?></textarea>
					</div>
					<div class="form-group">
						<label for="txt_beitrag">Text:</label>
						<p class="help-block">Verfassen Sie einen Beitrag.</p>
						<textarea class="form-control" rows="5" name="txt_beitrag" id="txt_beitrag"><?php echo $txt_beitrag; ?></textarea>
					</div>
					<div class="form-group">
						<label for="txt_conversion_ziel">Conversion-Ziel:</label>
						<p class="help-block">Geben Sie die URL zum Conversion-Ziel an.</p>
						<input type="text" class="form-control" placeholder="URL" name="txt_conversion_ziel" value="<?php echo $txt_conversion_ziel; ?>" required>
					</div>

					<div class="form-group">
						<label for="txt_conversion_titel">Button-Bezeichnung:</label>
						<p class="help-block">Geben Sie eine Button-Bezeichnung zum Conversion-Ziel an.</p>
						<input type="text" class="form-control" placeholder="Button-Bezeichnung" name="txt_conversion_titel" value="<?php echo $txt_conversion_titel; ?>" required>
					</div>
				</div>
				<!-- Submit Button -->
				<div class="card-footer pb-0">
					<div class="form-group">
						<button type="submit" class="btn btn-lg btn-success" name="submit">speichern</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<?php
	include('footer.php');
?>

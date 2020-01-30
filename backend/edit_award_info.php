<?php
	$page 			= 'edit_award_info';

	require('header.php');

	$schluessel 	= 'award';
	$alert 		 	= '';

	// Tabelle auslesen
	$award_sql 	= sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel'), array($institut_id, $schluessel), '', '');

	if (mysqli_num_rows($award_sql) == 1) {
		$award_row 			= mysqli_fetch_assoc($award_sql);

		// Variablen vergeben
		$txt_titel 				= $award_row['txt_titel'];
		$txt_einleitung 		= $award_row['txt_einleitung'];
		$txt_einleitung 		= str_replace('<br />', '', $txt_einleitung);
		$txt_einleitung 		= str_replace('\r\n', '', $txt_einleitung);
		$txt_einleitung 		= stripslashes($txt_einleitung);
		$txt_conversion_ziel 	= $award_row['txt_conversion_ziel'];
		$txt_conversion_titel 	= $award_row['txt_conversion_titel'];
		
	} else {
		$txt_titel 				= '';
		$txt_einleitung 		= '';
		$txt_conversion_ziel 	= '';
	}

	// speichern
	if (isset($_POST['submit'])) {
		// einlesen der im Formular angegebenen Werte
		$txt_titel 				= mysqli_real_escape_string($db, $_POST['txt_titel']);
		$txt_einleitung 		= mysqli_real_escape_string($db, $_POST['txt_einleitung']);
		$txt_einleitung 		= nl2br($txt_einleitung);
		$txt_conversion_ziel 	= mysqli_real_escape_string($db, $_POST['txt_conversion_ziel']);
		$txt_conversion_titel 	= mysqli_real_escape_string($db, $_POST['txt_conversion_titel']);

		// Variablen für Fehlerprüfung – Wenn kein Fehler vorhanden ist: fehler=0
		$fehler 				= 0;

		if ($fehler == 0) {
			if (mysqli_num_rows($award_sql) == 1) {
				$award_update 	= sql_update('texte', array('txt_titel', 'txt_einleitung', 'txt_conversion_titel', 'txt_conversion_ziel', 'chg_user'), array($txt_titel, $txt_einleitung, $txt_conversion_titel, $txt_conversion_ziel, $user_email), array('txt_institut', 'txt_schluessel'), array($institut_id, $schluessel));
			} else {
				$award_update 	= sql_insert('texte', array('txt_institut', 'txt_schluessel', 'txt_titel', 'txt_einleitung', 'txt_conversion_titel', 'txt_conversion_ziel', 'new_user', 'chg_user'), array($institut_id, $schluessel, $txt_titel, $txt_einleitung, $txt_conversion_titel, $txt_conversion_ziel, $user_email, $user_email));
			}
		}

		if ($award_update == true) {
			$alert 		 			.= '<div class="alert alert-success" role="alert">Die Angaben wurden erfolgreich gespeichert.</div>';
			$txt_einleitung 		= str_replace('<br />', '', $txt_einleitung);
			$txt_einleitung 		= $_POST['txt_einleitung'];
			$txt_conversion_ziel 	= $_POST['txt_conversion_ziel'];
			$txt_conversion_titel 	= $_POST['txt_conversion_titel'];
		} else {
			$fehler++;
			$alert 				   .= '<div class="alert alert-danger alert-dismissible fade in" role="alert"><b>Fehler!</b> Die Angaben konnten nicht gespeichert werden.</div>';
			$txt_einleitung 		= str_replace('<br />', '', $txt_einleitung);
			$txt_einleitung 		= $_POST['txt_einleitung'];
			$txt_conversion_ziel 	= $_POST['txt_conversion_ziel'];
			$txt_conversion_titel 	= $_POST['txt_conversion_titel'];
		}
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="award.php">Auszeichnungen</a></li>
	<li class="breadcrumb-item active">Auszeichnungsangaben bearbeiten</li>
</ol>

<div class="jumbotron">
	<h1>Auszeichnungsangaben bearbeiten</h1>
	<p class="lead">Hier haben Sie die Möglichkeit die Angaben zu den Auszeichnungen zu bearbeiten.</p>
</div><!-- /.jumbotron -->

<form action="" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12">
			<?php echo $alert; ?>
		</div>

		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-home" aria-hidden="true"></i> &nbsp; Angaben
				</div>
				<div class="card-body">
					<div class="form-group">
						<label for="txt_titel">Headline:</label>
						<p class="help-block">Geben Sie eine Headline an.</p>
						<input type="text" class="form-control" placeholder="Headline" name="txt_titel" value="<?php echo $txt_titel; ?>" required>
					</div>

					<div class="form-group">
						<label for="txt_einleitung">Einleitung:</label>
						<p class="help-block">Verfassen Sie eine kurze Einleitung/Subline.</p>
						<textarea class="form-control" rows="5" name="txt_einleitung" id="txt_einleitung"><?php echo $txt_einleitung; ?></textarea>
					</div>

					<div class="form-group">
						<label for="txt_conversion_ziel">Link zum Conversion-Ziel:</label>
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

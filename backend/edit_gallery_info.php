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

	$schluessel 	= 'galerie';
	$alert 		 	= '';

	// Tabelle auslesen
	$galerie_sql 	= sql_select_where('all', 'texte', array('txt_institut', 'txt_rubrik', 'txt_schluessel'), array($institut_id, $rub_id, $schluessel), '', '');

	if (mysqli_num_rows($galerie_sql) == 1) {
		$galerie_row 			= mysqli_fetch_assoc($galerie_sql);

		// Variablen vergeben
		$txt_titel 				= $galerie_row['txt_titel'];
		$txt_einleitung 		= $galerie_row['txt_einleitung'];
		$txt_einleitung 		= str_replace('<br />', '', $txt_einleitung);
		$txt_einleitung 		= str_replace('\r\n', '', $txt_einleitung);
		$txt_einleitung 		= stripslashes($txt_einleitung);
		$txt_conversion_ziel 	= $galerie_row['txt_conversion_ziel'];
		$txt_conversion_titel 	= $galerie_row['txt_conversion_titel'];
		
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
			if (mysqli_num_rows($galerie_sql) == 1) {
				$galerie_update 	= sql_update('texte', array('txt_titel', 'txt_einleitung', 'txt_conversion_titel', 'txt_conversion_ziel', 'chg_user'), array($txt_titel, $txt_einleitung, $txt_conversion_titel, $txt_conversion_ziel, $user_email), array('txt_institut', 'txt_rubrik', 'txt_schluessel'), array($institut_id, $rub_id, $schluessel));
			} else {
				$galerie_update 	= sql_insert('texte', array('txt_institut', 'txt_rubrik', 'txt_schluessel', 'txt_titel', 'txt_einleitung', 'txt_conversion_titel', 'txt_conversion_ziel', 'new_user', 'chg_user'), array($institut_id, $rub_id, $schluessel, $txt_titel, $txt_einleitung, $txt_conversion_titel, $txt_conversion_ziel, $user_email, $user_email));
			}
		}

		if ($galerie_update == true) {
			$alert 		 			.= '<div class="alert alert-success" role="alert">Die Angaben zur Galerie wurden erfolgreich gespeichert.</div>';
			$txt_einleitung 		= str_replace('<br />', '', $txt_einleitung);
			$txt_einleitung 		= $_POST['txt_einleitung'];
			$txt_conversion_ziel 	= $_POST['txt_conversion_ziel'];
			$txt_conversion_titel 	= $_POST['txt_conversion_titel'];
		} else {
			$fehler++;
			$alert 				   .= '<div class="alert alert-danger alert-dismissible fade in" role="alert"><b>Fehler!</b> Die Angaben zur Galerie konnten nicht gespeichert werden.</div>';
			$txt_einleitung 		= str_replace('<br />', '', $txt_einleitung);
			$txt_einleitung 		= $_POST['txt_einleitung'];
			$txt_conversion_ziel 	= $_POST['txt_conversion_ziel'];
			$txt_conversion_titel 	= $_POST['txt_conversion_titel'];
		}
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item"><?php echo $row_rubrik['rub_name']; ?></li>
	<li class="breadcrumb-item"><a href="gallery.php?rub_id=<?php echo $rub_id; ?>">Galeriebilder</a></li>
	<li class="breadcrumb-item active">Galerie bearbeiten</li>
</ol>

<div class="jumbotron">
	<h1>Galerie bearbeiten</h1>
	<p class="lead">Hier haben Sie die Möglichkeit die Angaben zur Galerie zu bearbeiten.</p>
</div><!-- /.jumbotron -->

<form action="" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12">
			<?php echo $alert; ?>
		</div>

		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-home" aria-hidden="true"></i> &nbsp; Galerieangaben
				</div>
				<div class="card-body">
					<div class="form-group">
						<label for="txt_titel">Headline:</label>
						<p class="help-block">Geben Sie eine Headline zur Galerie an.</p>
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

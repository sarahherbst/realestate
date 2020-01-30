<?php
	$page 	= 'karriere';
	require('header.php');

	$fehlerangabe 	= '';
	$schluessel 	= 'karriere';
	$img_schluessel = 'karriere-header';

	// Tabelle auslesen
	$txt_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel'), array($institut_id, $schluessel), '', '');

	if ( mysqli_num_rows($txt_sql) == 1 ) {
		$txt_row = mysqli_fetch_object($txt_sql);

		// Variablen vergeben
		$txt_titel 				= $txt_row->txt_titel;
		$txt_einleitung 		= $txt_row->txt_einleitung;
		$txt_einleitung 		= str_replace('<br />', '', $txt_einleitung);
		$txt_einleitung 		= str_replace('\r\n', '', $txt_einleitung);
		$txt_einleitung 		= stripslashes($txt_einleitung);
		$txt_conversion_ziel 	= $txt_row->txt_conversion_ziel;
		$txt_conversion_titel 	= $txt_row->txt_conversion_titel;

		// Bild auslesen
		$img_sql 				= sql_select_where('all', 'images', array('img_institut', 'img_schluessel'), array($institut_id, $img_schluessel), '', '');
		$img_row 				= mysqli_fetch_object($img_sql);
		$img_thumb 				= $img_row->img_thumb;
		$img_titel 				= $img_row->img_titel;
		$img_beschreibung 		= $img_row->img_beschreibung;
		$img_beschreibung 		= str_replace('<br />', '', $img_beschreibung);
		$img_beschreibung 		= str_replace('\r\n', '', $img_beschreibung);
		$img_beschreibung 		= stripslashes($img_beschreibung);

	} else {
		$txt_titel 				= '';
		$txt_einleitung 		= '';
		$txt_conversion_ziel 	= '';
		$txt_conversion_titel 	= '';

		$img_titel 				= '';
		$img_beschreibung		= '';
	}

	// speichern
	if (isset($_POST['submit'])) {
		// einlesen der im Formular angegebenen Werte
		$txt_titel 				= mysqli_real_escape_string($db, $_POST['txt_titel']);
		$txt_einleitung 		= mysqli_real_escape_string($db, $_POST['txt_einleitung']);
		$txt_einleitung 		= nl2br($txt_einleitung);
		$txt_conversion_ziel 	= mysqli_real_escape_string($db, $_POST['txt_conversion_ziel']);
		$txt_conversion_titel 	= mysqli_real_escape_string($db, $_POST['txt_conversion_titel']);

		$img_titel 				= mysqli_real_escape_string($db, $_POST['img_titel']);
		$img_beschreibung 		= mysqli_real_escape_string($db, $_POST['img_beschreibung']);
		$img_beschreibung 		= nl2br($img_beschreibung);

		// Variablen für Fehlerprüfung – Wenn kein Fehler vorhanden ist: fehler=0
		$fehler 				= 0;

		//Start Upload Bild
		if (!empty($_FILES['motiv']['tmp_name'])) {
			$upload_folder 			= '../img/'; //Das Upload-Verzeichnis
			$thumbnail_folder 	= '../img/'; //Das Upload-Verzeichnis
			$filename 				= pathinfo($_FILES['motiv']['name'], PATHINFO_FILENAME);
			$extension 				= strtolower(pathinfo($_FILES['motiv']['name'], PATHINFO_EXTENSION));

			//Überprüfung der Dateiendung
			$allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
			if (!in_array($extension, $allowed_extensions)) {
				$fehler++;
				$fehlerangabe .= '<div class="alert alert-danger" role="alert">Ungültige Dateiendung. Nur png, jpg, jpeg und gif-Dateien sind erlaubt.</div>';
			}

			//Überprüfung der Dateigröße
			$max_size = 500*1024; //500 KB
			if ($_FILES['motiv']['size'] > $max_size) {
				$fehler++;
				$fehlerangabe .= '<div class="alert alert-danger" role="alert">Bitte keine Dateien größer als 500kb hochladen</div>';
			}

			//Überprüfung dass das Bild keine Fehler enthält
			if (function_exists('exif_imagetype')) { //Die exif_imagetype-Funktion erfordert die exif-Erweiterung auf dem Server
				$allowed_types = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
				$detected_type = exif_imagetype($_FILES['motiv']['tmp_name']);

				if (!in_array($detected_type, $allowed_types)) {
					$fehler++;
					$fehlerangabe .= '<div class="alert alert-danger" role="alert">Nur der Upload von Bilddateien (png, jpg, jpeg und gif-Dateien) ist gestattet.</div>';
				}
			}

			//Pfad zum Upload
			$new_path = $upload_folder.$filename.'.'.$extension;

			//Neuer Dateiname falls die Datei bereits existiert
			if (file_exists($new_path)) { //Falls Datei existiert, hänge eine Zahl an den Dateinamen
				$img_id = 1;
				do {
					$new_path = $upload_folder.$filename.'_'.$img_id.'.'.$extension;
					$img_id++;
				}
				while(file_exists($new_path));
			}

			if ($fehler == 0) {
				//Alles okay, verschiebe Datei an neuen Pfad
				move_uploaded_file($_FILES['motiv']['tmp_name'], $new_path);
				$motiv = $new_path;
				
				//Thumbnail erstellen
				$thumbnail = imgresize($motiv, $thumbnail_folder);

				$motiv = str_replace('../', '', $motiv);
				$thumbnail = str_replace('../', '', $thumbnail);
			}

			$plusimg = 'img_bild = "$motiv", img_thumb = "$thumbnail", ';
		} else {
			$plusimg = '';
		}

		if ($fehler == 0) {
			if ( mysqli_num_rows($txt_sql) == 1 ) {
				$txt_update 	= sql_update('texte', array('txt_titel', 'txt_einleitung', 'txt_conversion_titel', 'txt_conversion_ziel', 'chg_user'), array($txt_titel, $txt_einleitung, $txt_conversion_titel, $txt_conversion_ziel, $user_email), array('txt_institut', 'txt_schluessel'), array($institut_id, $schluessel));
			} else {
				$txt_update 	= sql_insert('texte', array('txt_institut', 'txt_schluessel', 'txt_titel', 'txt_einleitung', 'txt_conversion_titel', 'txt_conversion_ziel', 'new_user', 'chg_user'), array($institut_id, $schluessel, $txt_titel, $txt_einleitung, $txt_conversion_titel, $txt_conversion_ziel, $user_email, $user_email));
			}
		}

		if ($txt_update == true) {
			$fehlerangabe .= '<div class="alert alert-success" role="alert">Die Angaben zur Karriereseite wurden erfolgreich gespeichert.</div>';
			$txt_einleitung 		= str_replace('<br />', '', $txt_einleitung);
			$txt_conversion_ziel 	= str_replace('<br />', '', $txt_conversion_ziel);
			$txt_einleitung 		= $_POST['txt_einleitung'];
			$txt_conversion_ziel 	= $_POST['txt_conversion_ziel'];
			$txt_conversion_titel 	= $_POST['txt_conversion_titel'];
		} else {
			$fehler++;
			$fehlerangabe   .= '<div class="alert alert-danger alert-dismissible fade in" role="alert"><b>Fehler!</b> Die Angaben zur Karriereseite konnten nicht gespeichert werden.</div>';
			$txt_einleitung 		= str_replace('<br />', '', $txt_einleitung);
			$txt_conversion_ziel 	= str_replace('<br />', '', $txt_conversion_ziel);
			$txt_einleitung 		= $_POST['txt_einleitung'];
			$txt_conversion_ziel 	= $_POST['txt_conversion_ziel'];
			$txt_conversion_titel 	= $_POST['txt_conversion_titel'];
		}

		if ($fehler == 0) {
			if ($plusimg != '') {
				$img_sql 	= sql_select_where('all', 'images', array('img_institut', 'img_schluessel'), array($institut_id, $img_schluessel), '', '');

				if ( mysqli_num_rows($img_sql) == 1 ) {
					$image_upload 	= sql_update('images', array('img_bild', 'img_thumb', 'img_titel', 'img_beschreibung', 'chg_user'), array($motiv, $thumbnail, $img_titel, $img_beschreibung, $user_email), array('img_institut', 'img_schluessel'), array($institut_id, $img_schluessel));
				} else {
					$image_upload 	= sql_insert('images', array('img_institut', 'img_schluessel', 'img_bild', 'img_thumb', 'img_titel', 'img_beschreibung', 'new_user', 'chg_user'), array($institut_id, $img_schluessel, $motiv, $thumbnail, $img_titel, $img_beschreibung, $user_email, $user_email));
				}

				if ($image_upload == true) {
					$fehlerangabe .= '<div class="alert alert-success" role="alert">Das Bild wurde erfolgreich gespeichert.</div>';
					$img_beschreibung = str_replace('<br />', '', $img_beschreibung);
					$img_beschreibung = $_POST['img_beschreibung'];
					$img_thumb 			= $thumbnail;
				} else {
					$fehler++;
					$fehlerangabe .= '<div class="alert alert-danger" role="alert"><b>Fehler!</b> Das Bild konnte nicht gespeichert werden. Bitte kontaktieren Sie den Websiteadministrator!</div>';
					$img_beschreibung = str_replace('<br />', '', $img_beschreibung);
					$img_beschreibung = $_POST['img_beschreibung'];
				}
			}
		}
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item active">Karriere</li>
	<li class="breadcrumb-item active">Karriereangaben bearbeiten</li>
</ol>

<div class="jumbotron">
	<h1>Karriereangaben bearbeiten</h1>
	<p class="lead">Hier haben Sie die Möglichkeit die Angaben zur Karriereseite zu bearbeiten.</p>
</div><!-- /.jumbotron -->

<form action="" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-home" aria-hidden="true"></i> &nbsp; Karriereangaben
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<?php echo $fehlerangabe; ?>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="txt_titel">Headline:</label>
								<p class="help-block">Geben Sie eine Headline zur Karriereseite an.</p>
								<input type="text" class="form-control" placeholder="Headline" name="txt_titel" value="<?php echo $txt_titel; ?>" required>
							</div>

							<div class="form-group">
								<label for="txt_einleitung">Einleitung:</label>
								<p class="help-block">Verfassen Sie eine kurze Einleitung.</p>
								<textarea class="form-control" rows="5" name="txt_einleitung" id="txt_einleitung"><?php echo $txt_einleitung; ?></textarea>
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
						<div class="col-md-6">
							<div class="form-group">
								<?php if ($img_thumb != '') { ?>
									<label>Aktuelles Headerbild:</label>
									<div class="row">
										<div class="col-md-6">
											<img src="../<?php echo $img_thumb; ?>" alt="Headerbild" class="img-thumbnail" width="250">
										</div>
									</div>
									<br>
								<?php } ?>
							
								<label for="motiv">Headerbild auswählen:</label>
								<p class="help-block">Laden Sie ein Bild zur Seite hoch.</p>
								<div class="input-group">
									<label class="input-group-btn mb-0">
										<span class="btn btn-primary">
											Durchsuchen &hellip; <input type="file" name="motiv" id="motiv" style="display: none;">
										</span>
									</label>
									<input type="text" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="img_titel">Bildtitel:</label>
								<p class="help-block">Geben Sie einen Bildtitel an.</p>
								<input type="text" class="form-control" placeholder="Bildtitel" name="img_titel" value="<?php echo $img_titel; ?>">
							</div>
							<div class="form-group">
								<label for="img_beschreibung">Bildbeschreibung:</label>
								<p class="help-block">Geben Sie eine Bildbeschreibung an.</p>
								<input type="text" class="form-control" placeholder="Bildbeschreibung" name="img_beschreibung" value="<?php echo $img_beschreibung; ?>">
							</div>
						</div>
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

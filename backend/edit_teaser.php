<?php
	//Datenbank einlesen
	require_once('../connection.inc.php');
	require_once('../function.inc.php');

	//Rubrik-ID
	$rub_id 	= $_GET['rub_id'];

	//Rubrik-Daten
	$sql_rubrik 			= sql_select_where('all', 'rubrik', array('rub_institut', 'rub_id'), array($institut_id, $rub_id), '', '');
	$row_rubrik 			= mysqli_fetch_assoc($sql_rubrik);
	$category_titel 		= strtolower(str_replace(' ', '-', $row_rubrik['rub_name']));

	require('header.php');

	$alert 						= '';
	$schluessel 				= 'teaser';
	$cutout_schluessel 			= 'teaser-cutout';
	$header_schluessel 			= 'teaser-header';

	// Tabelle auslesen
	$teaser_sql 				= sql_select_where('all', 'texte', array('txt_institut', 'txt_rubrik', 'txt_schluessel'), array($institut_id, $rub_id, $schluessel), '', '');

	if ( mysqli_num_rows($teaser_sql) == 1 ) {
		$teaser_row 			= mysqli_fetch_assoc($teaser_sql);

		// Variablen vergeben
		$teaser 				= $teaser_row['txt_beitrag'];
		$teaser 				= str_replace('<br />', '', $teaser);
		$teaser 				= str_replace('\r\n', '', $teaser);
		$teaser 				= stripslashes($teaser);

		$txt_titel 				= $teaser_row['txt_titel'];
		$txt_einleitung 		= $teaser_row['txt_einleitung'];
		$txt_einleitung 		= str_replace('<br />', '', $txt_einleitung);
		$txt_einleitung 		= str_replace('\r\n', '', $txt_einleitung);
		$txt_einleitung 		= stripslashes($txt_einleitung);
		$txt_conversion_ziel 	= $teaser_row['txt_conversion_ziel'];
		$txt_conversion_titel 	= $teaser_row['txt_conversion_titel'];
	} else {
		$teaser 				= '';
		$txt_titel 				= '';
		$txt_einleitung 		= '';
		$txt_conversion_ziel 	= '';
		$txt_conversion_titel 	= '';
	}

	// Bild laden
	$header_sql 				= sql_select_where('all', 'images', array('img_institut', 'img_rubrik', 'img_schluessel'), array($institut_id, $rub_id, $header_schluessel), '', '');
	$header_row 				= mysqli_fetch_assoc($header_sql);
	$header_thumb 				= $header_row['img_thumb'];
	$header_titel 				= $header_row['img_titel'];
	$header_beschreibung 		= $header_row['img_beschreibung'];
	$header_beschreibung 		= str_replace('<br />', '', $header_beschreibung);

	// Bild laden
	$cutout_sql 				= sql_select_where('all', 'images', array('img_institut', 'img_rubrik', 'img_schluessel'), array($institut_id, $rub_id, $cutout_schluessel), '', '');
	$cutout_row 				= mysqli_fetch_assoc($cutout_sql);
	$cutout_thumb 				= $cutout_row['img_bild'];
	$cutout_beschreibung 		= $cutout_row['img_beschreibung'];

	// speichern
	if (isset($_POST['submit'])) {
		/*einlesen der im Formular angegebenen Werte*/
		$teaser 				= $_POST['teaser'];
		$teaser 				= nl2br($teaser);
		$txt_titel 				= $_POST['txt_titel'];
		$txt_einleitung 		= $_POST['txt_einleitung'];
		$txt_einleitung  		= nl2br($txt_einleitung );
		$txt_conversion_ziel 	= mysqli_real_escape_string($db, $_POST['txt_conversion_ziel']);
		$txt_conversion_titel 	= mysqli_real_escape_string($db, $_POST['txt_conversion_titel']);

		$header_titel 			= mysqli_real_escape_string($db, $_POST['header_titel']);
		$header_beschreibung 	= mysqli_real_escape_string($db, $_POST['header_beschreibung']);
		$header_beschreibung 	= nl2br($header_beschreibung);

		$cutout_beschreibung 	= mysqli_real_escape_string($db, $_POST['cutout_beschreibung']);

		// Variablen für Fehlerprüfung – Wenn kein Fehler vorhanden ist: fehler=0
		$fehler 				= 0;

		// Daten in die Datenbank eintragen
		if ( mysqli_num_rows($teaser_sql) == 1 ) {
			$speichern = sql_update('texte', array('txt_titel', 'txt_einleitung', 'txt_beitrag', 'txt_conversion_titel', 'txt_conversion_ziel', 'chg_user'), array($txt_titel, $txt_einleitung, $teaser, $txt_conversion_titel, $txt_conversion_ziel, $user_email), array('txt_institut', 'txt_rubrik', 'txt_schluessel'), array($institut_id, $rub_id, $schluessel));
		} else {
			$speichern = sql_insert('texte', array('txt_institut', 'txt_rubrik', 'txt_schluessel', 'txt_titel', 'txt_einleitung', 'txt_beitrag', 'txt_conversion_titel', 'txt_conversion_ziel', 'new_user', 'chg_user'), array($institut_id, $rub_id, $schluessel, $txt_titel, $txt_einleitung, $teaser, $txt_conversion_titel, $txt_conversion_ziel, $user_email, $user_email));
		}
		// auf Erfolg prüfen
		if ($speichern == true) {
			$alert 			.= '<div class="alert alert-success" role="alert">Der Teaser <b>'.$row_rubrik['rub_name'].'</b> wurde erfolgreich geändert.</div>';
			$teaser 				= str_replace('<br />', '', $teaser);
			$teaser 				= $_POST['teaser'];
			$txt_titel 				= $_POST['txt_titel'];
			$txt_einleitung 		= $_POST['txt_einleitung'];
			$txt_conversion_ziel 	= $_POST['txt_conversion_ziel'];
			$txt_conversion_titel 	= $_POST['txt_conversion_titel'];
		} else {
			$fehler++;
			$alert   .= '<div class="alert alert-danger alert-dismissible fade in" role="alert"><b>Fehler!</b> Der Teaser <b>'.$row_rubrik['rub_name'].'</b> konnte nicht gespeichert werden.</div>';
		}

		// Headerbild hinzufügen
		if ($fehler == 0) {
			//Start Upload Bild
			if ($_FILES['motiv']['error'] <= 0) {
				$alert_upload 		= '';

				$upload_folder 		= '../img/artikel/'.$rub_id.'/'; 		// temporäres Upload-Verzeichnis
				$large_folder 		= '../img/artikel/'.$rub_id.'/large/';	// Verzeichnis große Auflösung
				$thumbnail_folder 	= '../img/artikel/'.$rub_id.'/thumb/'; 	// Verzeichnis Thumbnail

				// prüfen, ob Verzeichnis vorhanden ist, ansonsten erstellen
				if (!is_dir($upload_folder)) {
					if (!mkdir($upload_folder, 0777, true)) {
						$fehler++;
						$alert .= '<div class="alert alert-danger" role="alert">Der Ordner für das Headerbild konnte nicht erstellt werden.</div>';
					}
				}
				if (!is_dir($large_folder)) {
					if (!mkdir($large_folder, 0777, true)) {
						$fehler++;
						$alert .= '<div class="alert alert-danger" role="alert">Der Ordner für das Headerbild konnte nicht erstellt werden.</div>';
					}
				}
				if (!is_dir($thumbnail_folder)) {
					if (!mkdir($thumbnail_folder, 0777, true)) {
						$fehler++;
						$alert .= '<div class="alert alert-danger" role="alert">Der Ordner für das Headerbild konnte nicht erstellt werden.</div>';
					}
				}

				$filename 			= pathinfo($_FILES['motiv']['name'], PATHINFO_FILENAME);
				$extension 			= strtolower(pathinfo($_FILES['motiv']['name'], PATHINFO_EXTENSION));

				//Überprüfung der Dateiendung
				$allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
				if (!in_array($extension, $allowed_extensions)) {
					$fehler++;
					$alert .= '<div class="alert alert-danger" role="alert">Ungültige Dateiendung. Nur png, jpg, jpeg und gif-Dateien sind erlaubt.</div>';
				}

				//Überprüfung der Dateigröße
				$max_size = 500*1024; //500 KB
				if ($_FILES['motiv']['size'] > $max_size) {
					$fehler++;
					$alert .= '<div class="alert alert-danger" role="alert">Bitte keine Dateien größer als 500kb hochladen</div>';
				}

				//Überprüfung dass das Bild keine Fehler enthält
				if (function_exists('exif_imagetype')) { //Die exif_imagetype-Funktion erfordert die exif-Erweiterung auf dem Server
					$allowed_types = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
					$detected_type = exif_imagetype($_FILES['motiv']['tmp_name']);

					if (!in_array($detected_type, $allowed_types)) {
						$fehler++;
						$alert .= '<div class="alert alert-danger" role="alert">Nur der Upload von Bilddateien (png, jpg, jpeg und gif-Dateien) ist gestattet.</div>';
					}
				}

				// Überprüfung, ob Dateiname bereits existiert
				// Pfad zum Upload
				$current_path 	= $upload_folder.$filename.'.'.$extension;
				$new_path 		= $large_folder.$filename.'.'.$extension;
				// Falls Datei existiert, hänge eine Zahl an den Dateinamen
				if (file_exists($new_path)) { 
					$img_id = 1;
					do {

						$current_path 	= $upload_folder.$filename.'_'.$img_id.'.'.$extension;
						$new_path 		= $large_folder.$filename.'_'.$img_id.'.'.$extension;

						$img_id++;
					}
					while(file_exists($new_path));
				}

				if ($fehler == 0) {
					//Alles okay, verschiebe Datei an neuen Pfad
					move_uploaded_file($_FILES['motiv']['tmp_name'], $current_path);
					$motiv = $current_path;

					// Bild resizen
					if ($motiv !== '') {
						$large = imgresize($motiv, $large_folder);
						if ($large == '' || $large == false) {
							$fehler++;
							$alert_upload .= 'Das Bild '.$_FILES['motiv']['name'].' konnte nicht hinzugefügt werden. ';
						} else {
							$large = str_replace('../', '', $large);
							$alert_upload .= 'Das Bild '.$_FILES['motiv']['name'].' wurde erfolgreich hochgeladen. ';
						}
					} else {
						$fehler++;
						$alert_upload .= 'Das Bild '.$_FILES['motiv']['name'].' konnte nicht hochgeladen werden. ';
					}

					// Thumbnail erstellen & Datenbankeintrag
					if ($fehler == 0) {
						$thumbnail = imgthumb($motiv, $thumbnail_folder);
						if ($thumbnail == '' || $thumbnail == false) {
							$fehler++;
							$alert_upload .= 'Das Thumbnail zum Bild '.$_FILES['motiv']['name'].' konnte nicht hinzugefügt werden. ';
						} else {
							$thumbnail 	= str_replace('../', '', $thumbnail);
							$alert_upload .= 'Das Thumbnail zum Bild '.$_FILES['motiv']['name'].' wurde erfolgreich hochgeladen. ';

							// Datenbankeintrag
							if ( mysqli_num_rows($header_sql) == 1 ) {
								$image_upload 	= sql_update('images', array('img_bild', 'img_thumb', 'img_titel', 'img_beschreibung', 'chg_user'), array($large, $thumbnail, $header_titel, $header_beschreibung, $user_email), array('img_institut', 'img_rubrik', 'img_schluessel'), array($institut_id, $rub_id, $header_schluessel));
							} else {
								$image_upload 	= sql_insert('images', array('img_institut', 'img_rubrik', 'img_schluessel', 'img_bild', 'img_thumb', 'img_titel', 'img_beschreibung', 'new_user', 'chg_user'), array($institut_id, $rub_id, $header_schluessel, $large, $thumbnail, $header_titel, $header_beschreibung, $user_email, $user_email));
							}

							if ($image_upload == true) {
								$header_beschreibung 	= str_replace('<br />', '', $header_beschreibung);
								$header_beschreibung 	= $_POST['img_beschreibung'];
								$header_thumb 			= $thumbnail;
							} else {
								$fehler++;
								$alert_upload .= '<b>Fehler!</b> Das Bild konnte nicht in die Datenbank eingetragen werden. Bitte kontaktieren Sie den Websiteadministrator! ';
								$header_beschreibung = str_replace('<br />', '', $header_beschreibung);
								$header_beschreibung = $_POST['img_beschreibung'];
							}
						}
					}

					if ($fehler == 0) {
						$alert .= '<div class="alert alert-success" role="alert">'.$alert_upload.'</div>';

						// vorübergehendes Bild löschen
						unlink($motiv);
					} else {
						$alert .= '<div class="alert alert-danger" role="alert">'.$alert_upload.'</div>';
					}
				}
			} else {
				// Daten zum Bild in die Datenbank eintragen
				if ( mysqli_num_rows($header_sql) == 1 ) {
					$image_upload 	= sql_update('images', array('img_titel', 'img_beschreibung', 'chg_user'), array($header_titel, $header_beschreibung, $user_email), array('img_institut', 'img_rubrik', 'img_schluessel'), array($institut_id, $rub_id, $header_schluessel));
				} else {
					$image_upload 	= sql_insert('images', array('img_institut', 'img_rubrik', 'img_schluessel', 'img_titel', 'img_beschreibung', 'new_user', 'chg_user'), array($institut_id, $rub_id, $header_schluessel, $header_titel, $header_beschreibung, $user_email, $user_email));
				}
				// auf Erfolg prüfen
				if ($image_upload == true) {
					$alert .= '<div class="alert alert-success" role="alert">Die Angaben zum Bild wurden erfolgreich gespeichert.</div>';
					$header_beschreibung = str_replace('<br />', '', $header_beschreibung);
					$header_beschreibung = $_POST['img_beschreibung'];
				} else {
					$fehler++;
					$alert .= '<div class="alert alert-danger" role="alert"><b>Fehler!</b> Die Angaben zum Bild konnten nicht gespeichert werden. Bitte kontaktieren Sie den Websiteadministrator!</div>';
					$header_beschreibung = str_replace('<br />', '', $header_beschreibung);
					$header_beschreibung = $_POST['img_beschreibung'];
				}
			}
		}

		// freistellerbild hinzufügen
		if ($fehler == 0) {
			//Start Upload Bild
			if ($_FILES['cutout']['error'] <= 0) {
				$alert_upload 		= '';

				$upload_folder 		= '../img/'; 		// temporäres Upload-Verzeichnis
				// prüfen, ob Verzeichnis vorhanden ist, ansonsten erstellen
				if (!is_dir($upload_folder)) {
					if (!mkdir($upload_folder, 0777, true)) {
						$fehler++;
						$alert .= '<div class="alert alert-danger" role="alert">Der Ordner für das Teaserbild konnte nicht erstellt werden.</div>';
					}
				}

				$filename 			= pathinfo($_FILES['cutout']['name'], PATHINFO_FILENAME);
				$extension 			= strtolower(pathinfo($_FILES['cutout']['name'], PATHINFO_EXTENSION));

				//Überprüfung der Dateiendung
				$allowed_extensions = array('png', 'jpg', 'jpeg', 'gif');
				if (!in_array($extension, $allowed_extensions)) {
					$fehler++;
					$alert .= '<div class="alert alert-danger" role="alert">Ungültige Dateiendung. Nur png, jpg, jpeg und gif-Dateien sind erlaubt.</div>';
				}

				//Überprüfung der Dateigröße
				$max_size = 500*1024; //500 KB
				if ($_FILES['cutout']['size'] > $max_size) {
					$fehler++;
					$alert .= '<div class="alert alert-danger" role="alert">Bitte keine Dateien größer als 500kb hochladen</div>';
				}

				//Überprüfung dass das Bild keine Fehler enthält
				if (function_exists('exif_imagetype')) { //Die exif_imagetype-Funktion erfordert die exif-Erweiterung auf dem Server
					$allowed_types = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
					$detected_type = exif_imagetype($_FILES['cutout']['tmp_name']);

					if (!in_array($detected_type, $allowed_types)) {
						$fehler++;
						$alert .= '<div class="alert alert-danger" role="alert">Nur der Upload von Bilddateien (png, jpg, jpeg und gif-Dateien) ist gestattet.</div>';
					}
				}

				// Überprüfung, ob Dateiname bereits existiert
				// Pfad zum Upload
				$current_path 	= $upload_folder.$filename.'.'.$extension;
				// Falls Datei existiert, hänge eine Zahl an den Dateinamen
				if (file_exists($current_path)) { 
					$img_id = 1;
					do {

						$current_path 	= $upload_folder.$filename.'_'.$img_id.'.'.$extension;

						$img_id++;
					}
					while(file_exists($current_path));
				}

				if ($fehler == 0) {
					//Alles okay, verschiebe Datei an neuen Pfad
					move_uploaded_file($_FILES['cutout']['tmp_name'], $current_path);
					$motiv 				= $current_path;

					// Eintrag in die Datenbank
					if ($motiv !== '') {
						$motiv = str_replace('../', '', $motiv);
						// Datenbankeintrag
						if ( mysqli_num_rows($cutout_sql) == 1 ) {
							$image_upload 	= sql_update('images', array('img_bild', 'img_titel', 'img_beschreibung', 'chg_user'), array($motiv, $row_rubrik['rub_name'], $cutout_beschreibung, $user_email), array('img_institut', 'img_rubrik', 'img_schluessel'), array($institut_id, $rub_id, $cutout_schluessel));
						} else {
							$image_upload 	= sql_insert('images', array('img_institut', 'img_rubrik', 'img_schluessel', 'img_bild', 'img_titel', 'img_beschreibung', 'new_user', 'chg_user'), array($institut_id, $rub_id, $cutout_schluessel, $motiv, $row_rubrik['rub_name'], $cutout_beschreibung, $user_email, $user_email));
						}

						if ($image_upload == true) {
							$alert .= '<div class="alert alert-success" role="alert">Das Teaserbild zur Rubrik '.$row_rubrik['rub_name'].' wurde erfolgreich hinzugefügt.</div>';
							$cutout_thumb 	= $motiv;
						} else {
							$fehler++;
							$alert .= '<div class="alert alert-danger" role="alert"><b>Fehler!</b> Das Teaserbild konnte nicht in die Datenbank eingetragen werden. Bitte kontaktieren Sie den Websiteadministrator!</div>';
						}
					} else {
						$fehler++;
						$alert .= '<div class="alert alert-danger" role="alert">Das Teaserbild '.$_FILES['cutout']['name'].' konnte nicht hochgeladen werden.</div>';
					}
				}
			}
		}
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item"><?php echo $row_rubrik['rub_name']; ?></li>
	<li class="breadcrumb-item active">Teaser bearbeiten</li>
</ol>

<div class="jumbotron">
	<h1>Teaser bearbeiten</h1>
	<p class="lead">Hier haben Sie die Möglichkeit den Teaser <b><?php echo $row_rubrik['rub_name']; ?></b> zu bearbeiten.</p>
</div><!-- /.jumbotron -->

<form action="" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12">
			<?php echo $alert; ?>
		</div>

		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-file" aria-hidden="true"></i> &nbsp; Teaser für die Rubrik <b><?php echo $row_rubrik['rub_name']; ?></b>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="teaser">Text:</label>
								<textarea class="form-control" rows="5" name="teaser" id="teaser"><?php echo $teaser; ?></textarea>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<?php if ($cutout_thumb != '') { ?>
									<label>Aktuelles Teaserbild:</label>
									<div class="row">
										<div class="col-md-6">
											<img src="../<?php echo $cutout_thumb; ?>" alt="Freisteller" class="img-responsive thumbnail">
										</div>
									</div>
									<br>
								<?php } ?>
							
								<label for="cutout">Teaserbild auswählen:</label>
								<p class="help-block">Laden Sie ein Freistellerbild für den Rubriksteaser hoch.</p>
								<div class="input-group">
									<label class="input-group-btn mb-0">
										<span class="btn btn-primary">
											Durchsuchen &hellip; <input type="file" name="cutout" id="cutout" style="display: none;">
										</span>
									</label>
									<input type="text" class="form-control" readonly>
								</div>
							</div>
							<div class="form-group">
								<label for="cutout_beschreibung">CSS-Klasse:</label>
								<p class="help-block">Geben Sie eine CSS-Klasse zur Gestaltung des Teaserbildes an.</p>
								<input type="text" class="form-control" placeholder="product-img-haus-verkaufen" name="cutout_beschreibung" value="<?php echo $cutout_beschreibung; ?>" required>
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

		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-file" aria-hidden="true"></i> &nbsp; Einleitung für die Rubrik <b><?php echo $row_rubrik['rub_name']; ?></b>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="txt_titel">Headline:</label>
								<p class="help-block">Geben Sie eine Headline für die Einleitung der Rubrikseite an.</p>
								<input type="text" class="form-control" placeholder="Headline" name="txt_titel" value="<?php echo $txt_titel; ?>" required>
							</div>
							<div class="form-group">
								<label for="txt_einleitung">Einleitung:</label>
								<p class="help-block">Verfassen Sie eine kurze Einleitung für die Rubrikseite.</p>
								<input type="button" class="btn btn-sm btn-info mb-2" value="Liste einfügen" onclick="insertAtCursor('txt_einleitung', '<ul>\n<li>Ich bin ein Listenpunkt</li>\n<li>Ich bin noch ein Listenpunkt</li>\n</ul>')" />&nbsp;
								<textarea class="form-control" rows="5" name="txt_einleitung" id="txt_einleitung" required><?php echo $txt_einleitung; ?></textarea>
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
								<?php if ($header_thumb != '') { ?>
									<label>Aktuelles Headerbild:</label>
									<div class="row">
										<div class="col-md-6">
											<img src="../<?php echo $header_thumb; ?>" alt="Headerbild" class="img-responsive thumbnail">
										</div>
									</div>
									<br>
								<?php } ?>
							
								<label for="motiv">Headerbild auswählen:</label>
								<p class="help-block">Laden Sie ein Bild zum Beitrag hoch.</p>
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
								<label for="header_titel">Bildtitel:</label>
								<p class="help-block">Geben Sie einen Bildtitel an.</p>
								<input type="text" class="form-control" placeholder="Bildtitel" name="header_titel" value="<?php echo $header_titel; ?>">
							</div>
							<div class="form-group">
								<label for="header_beschreibung">Bildbeschreibung:</label>
								<p class="help-block">Geben Sie eine Bildbeschreibung an.</p>
								<input type="text" class="form-control" placeholder="Bildbeschreibung" name="header_beschreibung" value="<?php echo $header_beschreibung; ?>">
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

						
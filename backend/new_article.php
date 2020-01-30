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

	$fehlerangabe 	= '';
	$schluessel 	= 'artikel';

	$txt_alias 				= '';
	$txt_titel 				= '';
	$txt_einleitung 		= '';
	$txt_auszug 			= '';
	$txt_beitrag 			= '';
	$img_titel				= '';
	$img_beschreibung 		= '';
	$img_conversion_ziel 	= '';
	$img_conversion_titel 	= '';

	// speichern
	if (isset($_POST['submit'])) {
		// einlesen der im Formular angegebenen Werte
		if (isset($_POST['txt_alias']) && $_POST['txt_alias'] == 'extern') {
			$txt_alias 			= mysqli_real_escape_string($db, $_POST['txt_alias']);
		} else {
			$txt_alias 			= '';
		}
		$txt_titel 				= mysqli_real_escape_string($db, $_POST['txt_titel']);
		$txt_einleitung 		= mysqli_real_escape_string($db, $_POST['txt_einleitung']);
		$txt_einleitung 		= nl2br($txt_einleitung);
		$txt_auszug 			= mysqli_real_escape_string($db, $_POST['txt_auszug']);
		$txt_auszug 			= nl2br($txt_auszug);
		$txt_beitrag 			= mysqli_real_escape_string($db, $_POST['txt_beitrag']);
		$txt_beitrag 			= nl2br($txt_beitrag);

		$img_titel 				= mysqli_real_escape_string($db, $_POST['img_titel']);
		$img_beschreibung 		= mysqli_real_escape_string($db, $_POST['img_beschreibung']);
		$img_beschreibung 		= nl2br($img_beschreibung);
		$txt_conversion_ziel 	= mysqli_real_escape_string($db, $_POST['txt_conversion_ziel']);
		$txt_conversion_titel 	= mysqli_real_escape_string($db, $_POST['txt_conversion_titel']);

		// Variablen für Fehlerprüfung – Wenn kein Fehler vorhanden ist: fehler=0
		$fehler 				= 0;

		if ($fehler == 0) {
			$speichern 			= sql_insert('texte', array('txt_institut', 'txt_rubrik', 'txt_schluessel', 'txt_titel', 'txt_alias', 'txt_einleitung', 'txt_auszug', 'txt_beitrag', 'txt_conversion_titel', 'txt_conversion_ziel', 'new_user', 'chg_user'), array($institut_id, $rub_id, $schluessel, $txt_titel, $txt_alias, $txt_einleitung, $txt_auszug, $txt_beitrag, $txt_conversion_titel, $txt_conversion_ziel, $user_email, $user_email));
		}

		if ($speichern == true) {
			$fehlerangabe 		.= '<div class="alert alert-success" role="alert">Der Artikel für die Rubrik <b>'.$row_rubrik['rub_name'].'</b> wurde erfolgreich hinzugefügt.</div>';
			$txt_einleitung 	= str_replace('<br />', '', $txt_einleitung);
			$txt_auszug 		= str_replace('<br />', '', $txt_auszug);
			$txt_beitrag 		= str_replace('<br />', '', $txt_beitrag);
		} else {
			$fehler++;
			$fehlerangabe   	.= '<div class="alert alert-danger alert-dismissible fade in" role="alert"><b>Fehler!</b> Der Artikel für die Rubrik <b>'.$row_rubrik['rub_name'].'</b> konnte nicht hinzugefügt werden.</div>';
			$txt_einleitung 	= str_replace('<br />', '', $txt_einleitung);
			$txt_auszug 		= str_replace('<br />', '', $txt_auszug);
			$txt_beitrag 		= str_replace('<br />', '', $txt_beitrag);
		}

		//Bild hinzufügen
		if ($fehler == 0) {
			//Start Upload Bild
			if ($_FILES['motiv']['error'] <= 0) {
				$alert_upload 			= '';
				
				$upload_folder 			= '../img/artikel/'.$rub_id.'/'; 		//Das Upload-Verzeichnis
				$large_folder 			= '../img/artikel/'.$rub_id.'/large/'; 	//Das Upload-Verzeichnis
				$thumbnail_folder 		= '../img/artikel/'.$rub_id.'/thumb/'; 	//Das Upload-Verzeichnis
				// prüfen, ob Verzeichnis vorhanden ist, ansonsten erstellen
				if (!is_dir($upload_folder)) {
					if (!mkdir($upload_folder, 0777, true)) {
						$fehler++;
						$alert .= '<div class="alert alert-danger" role="alert">Der Ordner für das Bild konnte nicht erstellt werden.</div>';
					}
				}
				if (!is_dir($large_folder)) {
					if (!mkdir($large_folder, 0777, true)) {
						$fehler++;
						$alert .= '<div class="alert alert-danger" role="alert">Der Ordner für das Bild konnte nicht erstellt werden.</div>';
					}
				}
				if (!is_dir($thumbnail_folder)) {
					if (!mkdir($thumbnail_folder, 0777, true)) {
						$fehler++;
						$alert .= '<div class="alert alert-danger" role="alert">Der Ordner für das Bild konnte nicht erstellt werden.</div>';
					}
				}

				$filename 				= pathinfo($_FILES['motiv']['name'], PATHINFO_FILENAME);
				$extension 				= strtolower(pathinfo($_FILES['motiv']['name'], PATHINFO_EXTENSION));

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
							$article_sql 	= sql_select_where('txt_id', 'texte', array('txt_institut', 'txt_rubrik',  'txt_schluessel', 'txt_titel', 'txt_alias'), array($institut_id, $rub_id, $schluessel, $txt_titel, $txt_alias), '', '' );
							$article_row 	= mysqli_fetch_assoc($article_sql);
							$txt_id			= $article_row['txt_id'];

							$image_upload 	= sql_insert('images', array('img_institut', 'img_rubrik', 'img_schluessel', 'img_item_id', 'img_bild', 'img_thumb', 'img_titel', 'img_beschreibung', 'new_user', 'chg_user'), array($institut_id, $rub_id, $schluessel, $txt_id, $large, $thumbnail, $img_titel, $img_beschreibung, $user_email, $user_email));
							
							if ($image_upload == true) {
								$img_titel				= '';
								$img_beschreibung 		= '';
								$img_conversion_ziel 	= '';
								$img_conversion_titel 	= '';
								$img_bild 				= '';
							} else {
								$fehler++;
								$alert_upload .= '<b>Fehler!</b> Das Bild konnte nicht in die Datenbank eingetragen werden. Bitte kontaktieren Sie den Websiteadministrator! ';
								$img_beschreibung = str_replace('<br />', '', $img_beschreibung);
								$img_beschreibung = $_POST['img_beschreibung'];
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
			}
		}

		// Variablen zurücksetzen
		if ($fehler == 0) {
			$txt_alias 				= '';
			$txt_titel 				= '';
			$txt_einleitung 		= '';
			$txt_auszug 			= '';
			$txt_beitrag 			= '';
			$img_titel				= '';
			$img_beschreibung 		= '';
			$img_conversion_ziel 	= '';
			$img_conversion_titel 	= '';
		}
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item"><?php echo $row_rubrik['rub_name']; ?></li>
	<li class="breadcrumb-item"><a href="article.php?rub_id=<?php echo $rub_id; ?>">Artikel</a></li>
	<li class="breadcrumb-item active">Artikel hinzufügen</li>
</ol>

<div class="jumbotron">
	<h1>Artikel hinzufügen</h1>
	<p class="lead">Hier haben Sie die Möglichkeit einen Artikel für die Rubrik <b><?php echo $row_rubrik['rub_name']; ?></b> zu verfassen.</p>
</div><!-- /.jumbotron -->

<form action="" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-file" aria-hidden="true"></i> &nbsp; Artikel für die Rubrik <b><?php echo $row_rubrik['rub_name']; ?></b>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<?php echo $fehlerangabe; ?>
						</div>
						<div class="col-md-6">
							<div class="form-group form-check">
								<input type="checkbox" class="form-check-input" id="txt_alias" name="txt_alias" value="extern" <?php echo (isset($txt_alias) && $txt_alias == 'extern' ? 'checked' : '' ) ?>>
								<label class="form-check-label" for="txt_alias">Externer Link</label>
								<small id="txt_aliasHelp" class="form-text text-muted">Für einen externen Artikellink setzen Sie einen Haken und geben Sie den Link als Conversion an.</small>
							</div>
							<div class="form-group">
								<label for="txt_titel">Headline:</label>
								<p class="help-block">Geben Sie eine Headline zum Artikel an.</p>
								<input type="text" class="form-control" placeholder="Headline" name="txt_titel" value="<?php echo $txt_titel; ?>" required>
							</div>
							<div class="form-group">
								<label for="txt_einleitung">Einleitung:</label>
								<p class="help-block">Verfassen Sie eine kurze Einleitung/Subline.</p>
								<textarea class="form-control" rows="5" name="txt_einleitung" id="txt_einleitung"><?php echo $txt_einleitung; ?></textarea>
							</div>
							<div class="form-group">
								<label for="txt_beitrag">Text:</label>
								<p class="help-block">Verfassen Sie einen Beitrag.</p>
								<textarea class="form-control" rows="25" name="txt_beitrag" id="txt_beitrag"><?php echo $txt_beitrag; ?></textarea>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label for="txt_auszug">Auszug:</label>
								<p class="help-block">Geben Sie einen kurzen Auszug Ihres Beitrages an.</p>
								<textarea class="form-control" rows="5" name="txt_auszug" id="txt_auszug" required><?php echo $txt_auszug; ?></textarea>
							</div>

							<div class="form-group">
								<?php if ($img_bild != '') { ?>
									<label>Aktuelles Beitragsbild:</label>
									<div class="row">
										<div class="col-md-6">
											<img src="../<?php echo $img_bild; ?>" alt="Beitragsbild" class="img-responsive thumbnail">
										</div>
									</div>
									<br>
								<?php } ?>
							
								<label for="motiv">Beitragsbild auswählen:</label>
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
								<label for="img_titel">Bildtitel:</label>
								<p class="help-block">Geben Sie einen Bildtitel an.</p>
								<input type="text" class="form-control" placeholder="Bildtitel" name="img_titel" value="<?php echo $img_titel; ?>">
							</div>
							<div class="form-group">
								<label for="img_beschreibung">Bildbeschreibung:</label>
								<p class="help-block">Geben Sie eine Bildbeschreibung an.</p>
								<input type="text" class="form-control" placeholder="Bildbeschreibung" name="img_beschreibung" value="<?php echo $img_beschreibung; ?>">
							</div>

							<div class="form-group">
								<label for="txt_conversion_ziel">Conversion-Ziel:</label>
								<p class="help-block">Geben Sie die URL zum Conversion-Ziel an.</p>
								<input type="text" class="form-control" placeholder="URL" name="txt_conversion_ziel" value="<?php echo $txt_conversion_ziel; ?>">
							</div>

							<div class="form-group">
								<label for="txt_conversion_titel">Button-Bezeichnung:</label>
								<p class="help-block">Geben Sie eine Button-Bezeichnung zum Conversion-Ziel an.</p>
								<input type="text" class="form-control" placeholder="Button-Bezeichnung" name="txt_conversion_titel" value="<?php echo $txt_conversion_titel; ?>">
							</div>
						</div>

					</div>
				</div>
				<!-- Submit Button -->
				<div class="card-footer pb-0">
					<div class="form-group">
						<button type="submit" class="btn btn-lg btn-success" name="submit">hinzufügen</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>

<?php
	include('footer.php');
?>

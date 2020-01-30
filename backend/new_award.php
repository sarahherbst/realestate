<?php
	$page 				= 'new_award';

	require('header.php');

	$alert 				= '';
	$schluessel 		= 'award';

	$img_titel 			= '';
	$img_url			= '';
	$img_beschreibung 	= '';

	// Auszeichnung erstellen
	if (isset($_POST['submit'])) {
		// Variablen für Fehlerprüfung – Wenn kein Fehler vorhanden ist: fehler = 0
		$fehler 			= 0;
		
		$img_titel 			= mysqli_real_escape_string($db, $_POST['img_titel']);
		$img_url 			= mysqli_real_escape_string($db, $_POST['img_url']);
		$img_beschreibung 	= mysqli_real_escape_string($db, $_POST['img_beschreibung']);
		$img_beschreibung 	= nl2br($img_beschreibung);

		if ($_FILES['motiv']['error'] <= 0) {
			$upload_folder 		= '../img/award/'; 		//Das Upload-Verzeichnis
			$large_folder 		= '../img/award/large/'; //Das Upload-Verzeichnis
			$thumbnail_folder 	= '../img/award/thumb/'; //Das Upload-Verzeichnis
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

			$filename 	= pathinfo($_FILES['motiv']['name'], PATHINFO_FILENAME);
			$extension 	= strtolower(pathinfo($_FILES['motiv']['name'], PATHINFO_EXTENSION));

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
						$image_upload 	= sql_insert('images', array('img_institut', 'img_schluessel', 'img_bild', 'img_thumb', 'img_titel', 'img_url', 'img_beschreibung', 'new_user', 'chg_user'), array($institut_id, $schluessel, $large, $thumbnail, $img_titel, $img_url, $img_beschreibung, $user_email, $user_email));

						if ($image_upload == true) {
							$img_titel			= '';
							$img_beschreibung 	= '';
							$img_thumb 			= '';
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
		} else {
			$alert .= '<div class="alert alert-danger" role="alert"><b>Fehler!</b> Es wurde kein Bild zum Upload ausgewählt.</div>';
		}
	}
?>

<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="award.php">Auszeichnungen</a></li>
	<li class="breadcrumb-item active">Auszeichnung hinzufügen</li>
</ol>


<div class="jumbotron">
	<h1>Auszeichnung hinzufügen</h1>
	<p class="lead">Hier können Sie eine weitere Auszeichnung hinzufügen.</p>
</div>


<form action="" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12">
			<?php echo $alert; ?>
		</div>
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-image" aria-hidden="true"></i> &nbsp; Auszeichnung</b>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="img_titel">Titel:</label>
								<p class="help-block">Geben Sie eine Titel zum Bild an.</p>
								<input type="text" class="form-control" placeholder="Titel" name="img_titel" value="<?php echo $img_titel; ?>" required>
							</div>
							<div class="form-group">
								<label for="img_beschreibung">Beschreibung:</label>
								<p class="help-block">Verfassen Sie optional eine ausführliche Beschreibung die in der Lightbox angezeigt wird.</p>
								<textarea class="form-control" name="img_beschreibung" id="img_beschreibung" rows="10" placeholder="Ihre Beschreibung"><?php echo $img_beschreibung; ?></textarea>
							</div>
							<div class="form-group">
								<label for="img_url">URL:</label>
								<p class="help-block">Geben Sie hier den Link an.</p>
								<input type="text" class="form-control" placeholder="URL" name="img_url" value="<?php echo $img_url; ?>" required>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<?php if ($img_thumb != '') { ?>
									<label>Aktuelles Bild:</label>
									<div class="row">
										<div class="col-md-6">
											<img src="../<?php echo $img_thumb; ?>" alt="Bild" class="img-thumbnail" width="200">
										</div>
									</div>
									<br>
								<?php } ?>

								<label for="motiv">Bild auswählen:</label>
								<p class="help-block">Laden Sie ein Bild hoch.</p>
								<div class="input-group">
									<label class="input-group-btn mb-0">
										<span class="btn btn-primary">
											Durchsuchen&hellip; <input type="file" name="motiv" id="motiv" style="display: none;" required>
										</span>
									</label>
									<input type="text" class="form-control" readonly>
								</div>
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

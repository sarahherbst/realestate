<?php
	$page = 'edit_beratergruppe';
	require('header.php');

	$alert 					= '';
	$begr_id 				= $_GET['begr_id'];
	$schluessel 			= 'beratergruppe';

	$begr_sql 				= sql_select_where('all', 'beratergruppe', 'begr_id', $begr_id, '', '');
	$begr_row 				= mysqli_fetch_assoc($begr_sql);
	$begr_name 				= $begr_row['begr_name'];
	$begr_rubrik 			= $begr_row['begr_rubrik'];

	$img_sql 				= sql_select_where('all', 'images', array('img_schluessel', 'img_item_id'), array($schluessel, $begr_id), '', '');
	$img_row 				= mysqli_fetch_assoc($img_sql);
	$img_thumb 				= $img_row['img_thumb'];
	$img_beschreibung 		= $img_row['img_beschreibung'];

	if (isset($_POST['submit'])) {
		//einlesen der im Formular angegebenen Werte*/
		$begr_name 						= mysqli_real_escape_string($db, $_POST['begr_name']);
		$begr_rubrik 					= $_POST['begr_rubrik'];
		if (is_array($begr_rubrik)) {
			$begr_rubrik 				= implode(',', $begr_rubrik);
			$begr_rubrik 				= mysqli_escape_string($db, $begr_rubrik);
		}
		$img_beschreibung 				= mysqli_real_escape_string($db, $_POST['img_beschreibung']);
		
		//Variablen für Fehlerprüfung
		$fehler 						= 0;

		//Beratergruppe in die Datenbank eintragen
		$begr_update 					= sql_update('beratergruppe', array('begr_name', 'begr_rubrik', 'chg_user'), array($begr_name, $begr_rubrik, $user_email), 'begr_id', $begr_id);
		if ($begr_update == true) {
			$alert .= "<div class='alert alert-success alert-dismissible' role='alert'><button class='close' type='button' data-dismiss='alert' aria-label='Schließen'><span aria-hidden='true'>×</span></button>Das Team wurde erfolgreich bearbeitet.</div>";
		} else {
			$fehler++;
			$alert .= "<div class='alert alert-danger alert-dismissible' role='alert'><button class='close' type='button' data-dismiss='alert' aria-label='Schließen'><span aria-hidden='true'>×</span></button>Fehler! Das Team konnte leider nicht bearbeitet werden.</div>";
		}

		//Start Upload Bild
		if ($_FILES['motiv']['error'] <= 0) {
			$upload_folder 			= '../img/berater/'; //Das Upload-Verzeichnis
			$large_folder 			= '../img/berater/large/'; //Das Upload-Verzeichnis
			$thumbnail_folder 		= '../img/berater/thumb/'; //Das Upload-Verzeichnis
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
			$allowed_extensions 	= array('png', 'jpg', 'jpeg', 'gif');
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
						if ( mysqli_num_rows($img_sql) == 1 ) {
							$image_upload 	= sql_update('images', array('img_bild', 'img_thumb', 'img_beschreibung', 'chg_user'), array($large, $thumbnail, $img_beschreibung, $user_email), array('img_institut', 'img_schluessel', 'img_item_id'), array($institut_id, $schluessel, $begr_id));
						} else {
							$image_upload 	= sql_insert('images', array('img_institut', 'img_schluessel', 'img_item_id', 'img_bild', 'img_thumb', 'img_beschreibung', 'new_user', 'chg_user'), array($institut_id, $schluessel, $begr_id, $large, $thumbnail, $img_beschreibung, $user_email, $user_email));
						}

						if ($image_upload == true) {
							$img_beschreibung 	= str_replace('<br />', '', $img_beschreibung);
							$img_beschreibung 	= $_POST['img_beschreibung'];
							$img_thumb 			= $thumbnail;
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
			// Datenbankeintrag
			if ( mysqli_num_rows($img_sql) == 1 ) {
				$image_upload 		= sql_update('images', array('img_beschreibung', 'chg_user'), array($img_beschreibung, $user_email), array('img_institut', 'img_schluessel', 'img_item_id'), array($institut_id, $schluessel, $begr_id));
			} else {
				$image_upload 		= sql_insert('images', array('img_institut', 'img_schluessel', 'img_item_id', 'img_beschreibung', 'new_user', 'chg_user'), array($institut_id, $schluessel, $begr_id, $img_beschreibung, $user_email, $user_email));
			}
			if ($image_upload == true) {
				$img_beschreibung 	= str_replace('<br />', '', $img_beschreibung);
				$img_beschreibung 	= $_POST['img_beschreibung'];
			} else {
				$fehler++;
				$alert .= '<div class="alert alert-danger" role="alert"><b>Fehler!</b> Das Bild konnte nicht in die Datenbank eingetragen werden. Bitte kontaktieren Sie den Websiteadministrator!</div>';
				$img_beschreibung 	= str_replace('<br />', '', $img_beschreibung);
				$img_beschreibung 	= $_POST['img_beschreibung'];
			}
		}
	}
?>

<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item">Berater</li>
	<li class="breadcrumb-item"><a href="beratergruppe.php">Teams</a></li>
	<li class="breadcrumb-item active">Team bearbeiten</li>
</ol>

<div class="jumbotron">
	<h1>Team bearbeiten</h1>
	<p class="lead">Hier können Sie das ausgewählte Team bearbeiten.</p>
</div><!-- /.jumbotron -->

<form action="" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12">
			<?php echo $alert; ?>
		</div>
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-user-o" aria-hidden="true"></i> &nbsp; Teamangaben
				</div>
				<div class="card-body">
					<div class="form-group">
						<label for="begr_name">Bezeichnung:</label>
						<input type="text" class="form-control" name="begr_name" id="begr_name" placeholder="Bezeichnung" value="<?php echo $begr_name; ?>">
					</div>
								
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
						<p class="help-block">Laden Sie ein Bild des Beraters hoch.</p>
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
						<label for="img_beschreibung">Bildbeschreibung:</label>
						<p class="help-block">Geben Sie eine Bildbeschreibung an.</p>
						<input type="text" class="form-control" placeholder="Bildbeschreibung" name="img_beschreibung" value="<?php echo $img_beschreibung; ?>">
					</div>

					<div class="form-group">
						<label for="begr_rubrik">Darstellung im Ansprechpartner-Karussel:</label>
						<select multiple class="custom-select custom-script" size="5" name="begr_rubrik[]" id="begr_rubrik" required>
							<option value="0" disabled>Bitte wählen Sie einen oder mehrere Bereiche</option>
							<option value="index" <?php echo (strpos($begr_rubrik, 'index') !== false) ? 'selected' : '' ?>>Startseite</option>
							<option value="impressum" <?php echo (strpos($begr_rubrik, 'impressum') !== false) ? 'selected' : '' ?>>Impressum</option>
							<option value="datenschutz" <?php echo (strpos($begr_rubrik, 'datenschutz') !== false) ? 'selected' : '' ?>>Datenschutz</option>
							<option value="ansprechpartner" <?php echo (strpos($begr_rubrik, 'ansprechpartner') !== false) ? 'selected' : '' ?>>Ansprechpartner</option>
							<option value="form_kontakt" <?php echo (strpos($begr_rubrik, 'form_kontakt') !== false) ? 'selected' : '' ?>>Kontaktformular</option>
							<?php $rub_sql = sql_select_where('all', 'rubrik', array('rub_status'), array('1'), '', ''); ?>
							<?php while ($rub_row = mysqli_fetch_assoc($rub_sql)) { ?>
								<option value="rub_<?php echo $rub_row['rub_id']; ?>" <?php echo (strpos($begr_rubrik, 'rub_'.$rub_row['rub_id']) !== false) ? 'selected' : '' ?>><?php echo $rub_row['rub_name']; ?></option>
							<?php } ?>
						</select>
						<small id="begr_rubrik_help" class="form-text text-muted">Geben Sie an, in welchen Bereichen die Ansprechpartner im Karussel gezeigt werden sollen.</small>
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

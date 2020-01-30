<?php
	$page 	= 'immo';
	require('header.php');

	$fehlerangabe 	= '';
	$fehler 		= 0;
	$schluessel 	= 'immobilientool';
	$alias 			= 'gameplay';

	// Tabelle auslesen
	$immotool_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel', 'txt_alias'), array($institut_id, $schluessel, $alias), '', '');

	if ( mysqli_num_rows($immotool_sql) == 1 ) {
		$immotool_row = mysqli_fetch_assoc($immotool_sql);

		// Variablen vergeben
		$txt_titel 			= $immotool_row['txt_titel'];
		$txt_einleitung 	= $immotool_row['txt_einleitung'];
		$txt_einleitung 	= str_replace('<br />', '', $txt_einleitung);
		$txt_einleitung 	= str_replace('\r\n', '', $txt_einleitung);
		$txt_einleitung 	= stripslashes($txt_einleitung);
		$imm_rubrik 		= $immotool_row['txt_conversion_titel'];
		$txt_auszug 		= $immotool_row['txt_auszug']; // Positive Auswertung
		$txt_auszug 		= str_replace('<br />', '', $txt_auszug);
		$txt_auszug 		= str_replace('\r\n', '', $txt_auszug);
		$txt_auszug 		= stripslashes($txt_auszug);
		$txt_beitrag 		= $immotool_row['txt_beitrag']; // negative Auswertung
		$txt_beitrag 		= str_replace('<br />', '', $txt_beitrag);
		$txt_beitrag 		= str_replace('\r\n', '', $txt_beitrag);
		$txt_beitrag 		= stripslashes($txt_beitrag);
	} else {
		$txt_titel 			= '';
		$txt_einleitung 	= '';
		$txt_auszug 		= ''; //Positive Auswertung
		$txt_beitrag 		= ''; //Negative Auswertung
	}

	// speichern
	if (isset($_POST['submit'])) {
		// einlesen der im Formular angegebenen Werte
		$txt_titel 			= mysqli_real_escape_string($db, $_POST['txt_titel']);
		$txt_einleitung 	= mysqli_real_escape_string($db, $_POST['txt_einleitung']);
		$txt_einleitung 	= nl2br($txt_einleitung);

		$imm_rubrik 					= $_POST['imm_rubrik'];
		if (is_array($imm_rubrik)) {
			$imm_rubrik 				= implode(',', $imm_rubrik);
			$imm_rubrik 				= mysqli_escape_string($db, $imm_rubrik);
		}

		$txt_auszug 		= mysqli_real_escape_string($db, $_POST['txt_auszug']); //positive Auswertung
		$txt_auszug 		= nl2br($txt_auszug);
		$txt_beitrag 		= mysqli_real_escape_string($db, $_POST['txt_beitrag']); //negative Auswertung
		$txt_beitrag 		= nl2br($txt_beitrag);

		if ($fehler == 0) {
			if ( mysqli_num_rows($immotool_sql) == 1 ) {
				$immotool_update = sql_update('texte', array('txt_titel', 'txt_einleitung', 'txt_auszug', 'txt_beitrag', 'txt_conversion_titel', 'chg_user'), array($txt_titel, $txt_einleitung, $txt_auszug, $txt_beitrag, $imm_rubrik, $user_email), array('txt_institut', 'txt_schluessel', 'txt_alias'), array($institut_id, $schluessel, $alias));
			} else {
				$immotool_update = sql_insert('texte', array('txt_institut', 'txt_schluessel', 'txt_alias', 'txt_titel', 'txt_einleitung', 'txt_auszug', 'txt_beitrag', 'txt_conversion_titel', 'new_user', 'chg_user'), array($institut_id, $schluessel, $alias, $txt_titel, $txt_einleitung, $txt_auszug, $txt_beitrag, $imm_rubrik, $user_email, $user_email));
			}
		}

		if ($immotool_update == true) {
			$fehlerangabe .= '<div class="alert alert-success" role="alert">Die Angaben zum Immobilientool wurden erfolgreich gespeichert.</div>';
			$txt_einleitung = $_POST['txt_einleitung'];
			$txt_auszug 	= $_POST['txt_auszug'];
			$txt_beitrag 	= $_POST['txt_beitrag'];
		} else {
			$fehler++;
			$fehlerangabe   .= '<div class="alert alert-danger alert-dismissible fade in" role="alert"><b>Fehler!</b> Die Angaben zum Immobilientool konnten nicht gespeichert werden.</div>';
			$txt_einleitung = $_POST['txt_einleitung'];
			$txt_auszug 	= $_POST['txt_auszug'];
			$txt_beitrag 	= $_POST['txt_beitrag'];
		}
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item active">Immobilien</li>
	<li class="breadcrumb-item active">Immobilientool bearbeiten</li>
</ol>

<div class="jumbotron">
	<h1>Immobilientool bearbeiten</h1>
	<p class="lead">Hier haben Sie die Möglichkeit die Angaben zum Immobilientool zu bearbeiten.</p>
</div><!-- /.jumbotron -->

<form action="" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-home" aria-hidden="true"></i> &nbsp; Immobilientoolangaben
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<?php echo $fehlerangabe; ?>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="txt_titel">Headline:</label>
								<input type="text" class="form-control" placeholder="Headline" name="txt_titel" value="<?php echo $txt_titel; ?>" required>
								<small class="text-muted">Geben Sie eine Headline zum Immobilientool an.</small>
							</div>

							<div class="form-group">
								<label for="txt_einleitung">Einleitung:</label>
								<textarea class="form-control" rows="4" name="txt_einleitung" id="txt_einleitung"><?php echo $txt_einleitung; ?></textarea>
								<small class="text-muted">Verfassen Sie eine kurze Einleitung/Subline.</small>
							</div>

							<div class="form-group">
								<label for="imm_rubrik">Darstellung auf der Webseite:</label>
								<select multiple class="custom-select custom-script" size="5" name="imm_rubrik[]" id="imm_rubrik" required>
									<option value="0" disabled>Bitte wählen Sie einen oder mehrere Bereiche</option>
									<?php $rub_sql = sql_select_where('all', 'rubrik', array('rub_status'), array('1'), '', ''); ?>
									<?php while ($rub_row = mysqli_fetch_assoc($rub_sql)) { ?>
										<option value="rub_<?php echo $rub_row['rub_id']; ?>" <?php echo (strpos($imm_rubrik, 'rub_'.$rub_row['rub_id']) !== false) ? 'selected' : '' ?>><?php echo $rub_row['rub_name']; ?></option>
									<?php } ?>
								</select>
								<small id="begr_rubrik_help" class="form-text text-muted">Geben Sie an, in welchen Bereichen das Immobilien-Tool gezeigt werden sollen.</small>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="txt_auszug">Positive Auswertung:</label>
								<textarea class="form-control" rows="6" name="txt_auszug" id="txt_auszug"><?php echo $txt_auszug; ?></textarea>
								<small class="text-muted">Verfassen Sie eine kurze Auswertung bei Angabe der korrekten Lösung.</small>
							</div>
							<div class="form-group">
								<label for="txt_beitrag">Negative Auswertung:</label>
								<textarea class="form-control" rows="6" name="txt_beitrag" id="txt_beitrag"><?php echo $txt_beitrag; ?></textarea>
								<small class="text-muted">Verfassen Sie eine kurze Auswertung bei einer Falschangabe.</small>
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

<?php
	$page 	= 'immobilienliste';
	require('header.php');

	$fehlerangabe 	= '';
	$schluessel 	= 'immobilienliste';

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
		$txt_rubrik 			= $txt_row->txt_rubrik;

	} else {
		$txt_titel 				= '';
		$txt_einleitung 		= '';
	}

	// speichern
	if (isset($_POST['submit'])) {
		// einlesen der im Formular angegebenen Werte
		$txt_titel 				= mysqli_real_escape_string($db, $_POST['txt_titel']);
		$txt_einleitung 		= mysqli_real_escape_string($db, $_POST['txt_einleitung']);
		$txt_einleitung 		= nl2br($txt_einleitung);
		$txt_rubrik 			= mysqli_real_escape_string($db, $_POST['txt_rubrik']);

		// Variablen für Fehlerprüfung – Wenn kein Fehler vorhanden ist: fehler=0
		$fehler 				= 0;

		if ($fehler == 0) {
			if ( mysqli_num_rows($txt_sql) == 1 ) {
				$txt_update 	= sql_update('texte', array('txt_titel', 'txt_einleitung', 'txt_rubrik', 'chg_user'), array($txt_titel, $txt_einleitung, $txt_rubrik, $user_email), array('txt_institut', 'txt_schluessel'), array($institut_id, $schluessel));
			} else {
				$txt_update 	= sql_insert('texte', array('txt_institut', 'txt_schluessel', 'txt_titel', 'txt_einleitung', 'txt_rubrik', 'new_user', 'chg_user'), array($institut_id, $schluessel, $txt_titel, $txt_einleitung, $txt_rubrik, $user_email, $user_email));
			}
		}

		if ($txt_update == true) {
			$fehlerangabe .= '<div class="alert alert-success" role="alert">Die Angaben zur Immobilienliste wurden erfolgreich gespeichert.</div>';
			$txt_einleitung 		= str_replace('<br />', '', $txt_einleitung);
			$txt_einleitung 		= $_POST['txt_einleitung'];
		} else {
			$fehler++;
			$fehlerangabe   .= '<div class="alert alert-danger alert-dismissible fade in" role="alert"><b>Fehler!</b> Die Angaben zur Immobilienliste konnten nicht gespeichert werden.</div>';
			$txt_einleitung 		= str_replace('<br />', '', $txt_einleitung);
			$txt_einleitung 		= $_POST['txt_einleitung'];
		}
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item active">Immobilienliste</li>
	<li class="breadcrumb-item active">Angaben zur Immobilienliste bearbeiten</li>
</ol>

<div class="jumbotron">
	<h1>Angaben zur Immobilienliste bearbeiten</h1>
	<p class="lead">Hier haben Sie die Möglichkeit die Angaben zur Immobilienliste zu bearbeiten.</p>
</div><!-- /.jumbotron -->

<form action="" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-home" aria-hidden="true"></i> &nbsp; Angaben
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<?php echo $fehlerangabe; ?>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label for="txt_rubrik">Wählen Sie eine Kategorie, in der das Tool erscheinen soll.</label>
								<select class="form-control" id="txt_rubrik" name="txt_rubrik">
									<?php
										$sql_category = sql_select_where('all', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
										while ($row_category = mysqli_fetch_assoc($sql_category)) {
											$category_name 	= str_replace(' ', '-', $row_category['rub_name']);
											$category_ul 	= str_replace('&', '', $row_category['rub_name']);
									?>
										<option value="<?php echo $row_category['rub_id']; ?>" <?php if($row_category['rub_id'] == $txt_rubrik) : echo 'selected'; endif; ?>><?php echo $row_category['rub_name']; ?></option>
									<?php } ?>
									<option value="0" <?php if($txt_rubrik == '0' || $txt_rubrik == '') : echo 'selected'; endif; ?>>Keine</option>
								</select>
							</div>

							<div class="form-group">
								<label for="txt_titel">Headline:</label>
								<p class="help-block">Geben Sie eine Headline zur Immobilienliste an.</p>
								<input type="text" class="form-control" placeholder="Headline" name="txt_titel" value="<?php echo $txt_titel; ?>" required>
							</div>

							<div class="form-group">
								<label for="txt_einleitung">Einleitung:</label>
								<p class="help-block">Verfassen Sie eine kurze Einleitung.</p>
								<textarea class="form-control" rows="5" name="txt_einleitung" id="txt_einleitung"><?php echo $txt_einleitung; ?></textarea>
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

<?php
	$page = 'edit_impressum';
	require('header.php');

	$fehlerangabe = '';

	// Tabelle auslesen
	$impressum_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel'), array($institut_id, 'impressum'), '', '');

	if ( mysqli_num_rows($impressum_sql) == 1 ) {
		$impressum_row = mysqli_fetch_object($impressum_sql);

		// Variablen vergeben
		$impressum = $impressum_row->txt_beitrag;
		$impressum = str_replace('<br />', '', $impressum);
	} else {
		$impressum = '';
	}

	// speichern
	if (isset($_POST['submit'])) {
		/*einlesen der im Formular angegebenen Werte*/
		$impressum = $_POST['impressum'];
		$impressum = nl2br($impressum);

		// Variablen für Fehlerprüfung – Wenn kein Fehler vorhanden ist: fehler=0
		$fehler = 0;

		if ( mysqli_num_rows($impressum_sql) == 1 ) {
			$aktualisierung = "UPDATE texte SET txt_beitrag = '$impressum', chg_user = '$user', chg_time = curtime(), chg_date = curdate() WHERE txt_institut = '$institut_id' AND txt_schluessel = 'impressum'";
			$speichern 		= mysqli_query($db,$aktualisierung);
		} else {
			$speichern 		= sql_insert('texte', array('txt_institut', 'txt_schluessel', 'txt_beitrag', 'new_user', 'chg_user'), array($institut_id, 'impressum', $impressum, $user_email, $user_email));
		}
		if ($speichern == true) {
			$fehlerangabe .= "<div class='alert alert-success' role='alert'>Das Impressum wurde erfolgreich geändert.</div>";
			$impressum = str_replace('<br />', '', $impressum);
			$impressum = $_POST['impressum'];
		}
		else {
			$fehler = 1;
			$fehlerangabe   .= "<div class='alert alert-danger alert-dismissible fade in' role='alert'><b>Fehler!</b> Das Impressum konnte nicht gespeichert werden.</div>";
		}
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item active">Impressum</li>
</ol>

<div class="jumbotron">
	<h1>Impressum bearbeiten</h1>
	<p class="lead">Hier haben Sie die Möglichkeit das Impressum zu bearbeiten.</p>
</div><!-- /.jumbotron -->

<form action="" method="post">
	<div class="row">
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-file" aria-hidden="true"></i> &nbsp; Impressum
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<?php echo $fehlerangabe; ?>
						</div>
						<div class="col-md-12">
							<div class="form-group">
								<label for="impressum">Text:</label>
								<textarea class="form-control" rows="25" name="impressum" id="impressum"><?php echo $impressum; ?></textarea>
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

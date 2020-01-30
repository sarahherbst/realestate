<?php
	$page = 'edit_mailfooter';
	require('header.php');

	$fehlerangabe = '';

	// Tabelle auslesen
	$mailfooter_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel'), array($institut_id, 'mailfooter'), '', '');

	if ( mysqli_num_rows($mailfooter_sql) == 1 ) {
		$mailfooter_row = mysqli_fetch_object($mailfooter_sql);

		// Variablen vergeben
		$mailfooter = $mailfooter_row->txt_beitrag;
		$mailfooter = str_replace('<br />', '', $mailfooter);
	} else {
		$mailfooter = '';
	}

	// speichern
	if (isset($_POST['submit'])) {
		/*einlesen der im Formular angegebenen Werte*/
		$mailfooter = $_POST['mailfooter'];
		$mailfooter = nl2br($mailfooter);

		// Variablen für Fehlerprüfung – Wenn kein Fehler vorhanden ist: fehler=0
		$fehler = 0;

		if ( mysqli_num_rows($mailfooter_sql) == 1 ) {
			$aktualisierung = "UPDATE texte SET txt_beitrag = '$mailfooter', chg_user = '$user', chg_time = curtime(), chg_date = curdate() WHERE txt_institut = '$institut_id' AND txt_schluessel = 'mailfooter'";
			$speichern 		= mysqli_query($db,$aktualisierung);
		} else {
			$speichern 		= sql_insert('texte', array('txt_institut', 'txt_schluessel', 'txt_beitrag', 'new_user','chg_user'), array($institut_id, 'mailfooter', $mailfooter, $user_email, $user_email));
		}
		if ($speichern == true) {
			$fehlerangabe .= "<div class='alert alert-success' role='alert'>Der E-Mail Footer wurde erfolgreich geändert.</div>";
			$mailfooter = str_replace('<br />', '', $mailfooter);
			$mailfooter = $_POST['mailfooter'];
		}
		else {
			$fehler = 1;
			$fehlerangabe   .= "<div class='alert alert-danger alert-dismissible fade in' role='alert'><b>Fehler!</b> Der E-Mail Footer konnte nicht gespeichert werden.</div>";
		}
	}
?>
<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item active">E-Mail Footer</li>
</ol>

<div class="jumbotron">
	<h1>E-Mail Footer bearbeiten</h1>
	<p class="lead">Hier haben Sie die Möglichkeit den E-Mail Footer zu bearbeiten.</p>
</div><!-- /.jumbotron -->

<form action="" method="post">
	<div class="row">
		<div class="col-md-12">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-file" aria-hidden="true"></i> &nbsp; E-Mail Footer
				</div>
				<div class="card-body">
					<div class="row">
						<?php echo $fehlerangabe; ?>
						<div class="form-group col-md-12">
							<label for="mailfooter">Text:</label>
							<textarea class="form-control" rows="25" name="mailfooter" id="mailfooter"><?php echo $mailfooter; ?></textarea>
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

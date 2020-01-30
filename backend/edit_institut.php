<?php
	$page = 'edit_institut';
	require_once('../connection.inc.php');
	require_once('../function.inc.php');

	require('header.php');

	$fehlerangabe 	= '';

	//Institut ändern
	if (isset($_POST['speichern'])) {
		// einlesen der im Formular angegebenen Werte
		$institut 		= mysqli_real_escape_string($db, $_POST['institut']);
		$sitetitel 		= mysqli_real_escape_string($db, $_POST['sitetitel']);
		$keywords 		= mysqli_real_escape_string($db, $_POST['keywords']);
		$description 	= mysqli_real_escape_string($db, $_POST['description']);
		$institut_mail 	= mysqli_real_escape_string($db, $_POST['institut_mail']);
		$institut_url 	= mysqli_real_escape_string($db, $_POST['institut_url']);
		$email_von 		= mysqli_real_escape_string($db, $_POST['email_von']);
		$email_zu 		= mysqli_real_escape_string($db, $_POST['email_zu']);
		$email_kopie 	= mysqli_real_escape_string($db, $_POST['email_kopie']);
		$microsite_url	= mysqli_real_escape_string($db, $_POST['microsite_url']);
		$fb_title 		= mysqli_real_escape_string($db, $_POST['fb_title']);
		$fb_description = mysqli_real_escape_string($db, $_POST['fb_description']);
		$fb_url 		= mysqli_real_escape_string($db, $_POST['fb_url']);
		$yt_url 		= mysqli_real_escape_string($db, $_POST['yt_url']);
		$smtp_server 	= mysqli_real_escape_string($db, $_POST['smtp_server']);
		$smtp_user 		= mysqli_real_escape_string($db, $_POST['smtp_user']);
		$smtp_passwort 	= mysqli_real_escape_string($db, $_POST['smtp_passwort']);
		$smtp_port 		= mysqli_real_escape_string($db, $_POST['smtp_port']);

		// Variablen für Fehlerprüfung – Wenn kein Fehler vorhanden ist: fehler = 0
		$fehler 		= 0;

		$arraySelectors = array('ins_institut', 'ins_sitetitel', 'ins_keywords', 'ins_description', 'ins_institut_mail', 'ins_institut_url', 'ins_email_von', 'ins_email_zu', 'ins_email_kopie', 'ins_microsite_url', 'ins_fb_title', 'ins_fb_description', 'ins_fb_url', 'ins_yt_url', 'ins_smtp_server', 'ins_smtp_user', 'ins_smtp_passwort', 'ins_smtp_port', 'chg_user', 'chg_time', 'chg_date');
		$arrayValues = array($institut, $sitetitel, $keywords, $description, $institut_mail, $institut_url, $email_von, $email_zu, $email_kopie, $microsite_url, $fb_title, $fb_description, $fb_url, $yt_url, $smtp_server, $smtp_user, $smtp_passwort, $smtp_port, $user_email, 'curtime()', 'curdate()');

		//print_r(array_values($arraySelectors));
		//print_r(array_values($arrayValues));

		// UPDATE Institut
		$p_where = "ins_id = '$institut_id'";
		$upd  = "ins_institut = '$institut',";
		$upd .= "ins_sitetitel = '$sitetitel',";
		$upd .= "ins_keywords = '$keywords',";
		$upd .= "ins_description = '$description',";
		$upd .= "ins_institut_mail = '$institut_mail',";
		$upd .= "ins_institut_url = '$institut_url',";
		$upd .= "ins_email_von = '$email_von',";
		$upd .= "ins_email_zu = '$email_zu',";
		$upd .= "ins_email_kopie = '$email_kopie',";
		$upd .= "ins_microsite_url = '$microsite_url',";
		$upd .= "ins_fb_title = '$fb_title',";
		$upd .= "ins_fb_description = '$fb_description',";
		$upd .= "ins_fb_url = '$fb_url',";
		$upd .= "ins_yt_url = '$yt_url',";
		$upd .= "ins_smtp_server = '$smtp_server',";
		$upd .= "ins_smtp_user = '$smtp_user',";
		$upd .= "ins_smtp_passwort= '$smtp_passwort',";
		$upd .= "ins_smtp_port = '$smtp_port'";
		include ("db/db_chg_upd.php") ;
		$result = mysqli_query($db, "UPDATE institut set $upd where $p_where" );

		if ($result == true) {
			$fehlerangabe .= '<div class="alert alert-success" role="alert">Die Daten wurden erfolgreich geändert.</div>';
		} else {
			$fehler++;
			$fehlerangabe .= '<div class="alert alert-danger alert-dismissible" role="alert"><b>Fehler!</b> Die Daten konnten leider nicht geändert werden.</div>';
		}
	}
?>

<!-- Breadcrumbs -->
<ol class="breadcrumb">
	<li class="breadcrumb-item active">Institut</li>
</ol>

<!-- Einleitung -->
<div class="jumbotron">
	<h1>Institut</h1>
	<p class="lead">Hier haben Sie die Möglichkeit die Institutsdaten zu bearbeiten.</p>
</div><!-- /.jumbotron -->

<!-- Eingabe-Formular -->
<?php echo $fehlerangabe; ?>
<form action="" method="post" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-6">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-cog" aria-hidden="true"></i> &nbsp; Allgemein
				</div>
				<div class="card-body">
					<div class="form-group">
						<label for="sitetitel">SEO – Seitentitel:</label>
						<input type="text" class="form-control" name="sitetitel" id="sitetitel" value="<?php echo $sitetitel; ?>" required>
					</div>
					<div class="form-group">
						<label for="microsite_url">URL zur Microsite:</label>
						<input type="text" class="form-control" name="microsite_url" id="microsite_url" value="<?php echo $microsite_url; ?>" required>
					</div>
					<div class="form-group">
						<label for="keywords">Keywords:</label>
						<input type="text" class="form-control" name="keywords" id="keywords" value="<?php echo $keywords; ?>">
					</div>
					<div class="form-group">
						<label for="description">SEO – Beschreibung:</label>
						<input type="text" class="form-control" name="description" id="description" value="<?php echo $description; ?>">
					</div>
				</div>
			</div>

			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-envelope" aria-hidden="true"></i> &nbsp; E-Mail
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label for="email_zu">Posteingangs-E-Mail:</label>
								<input type="email" class="form-control" name="email_zu" id="email_zu" value="<?php echo $email_zu; ?>" required>
							</div>
							<div class="form-group">
								<label for="email_von">Postausgangs-E-Mail:</label>
								<input type="email" class="form-control" name="email_von" id="email_von" value="<?php echo $email_von; ?>" required>
							</div>
							<div class="form-group">
								<label for="email_kopie">E-Mail für Kopie:</label>
								<input type="email" class="form-control" name="email_kopie" id="email_kopie" value="<?php echo $email_kopie; ?>">
							</div>
						</div>
						<div class="col-md-6">
							<div class="row">
								<div class="form-group col-md-2">
									<label for="smtp_port">Port:</label>
									<input type="text" class="form-control" name="smtp_port" id="smtp_port" value="<?php echo $smtp_port; ?>" required>
								</div>
								<div class="form-group col-md-10">
									<label for="smtp_server">SMTP Server:</label>
									<input type="text" class="form-control" name="smtp_server" id="smtp_server" value="<?php echo $smtp_server; ?>" required>
								</div>
							</div>
							<div class="form-group">
								<label for="smtp_user">SMTP User:</label>
								<input type="text" class="form-control" name="smtp_user" id="smtp_user" value="<?php echo $smtp_user; ?>" required>
							</div>
							<div class="form-group">
								<label for="smtp_passwort">SMTP Passwort:</label>
								<input type="password" class="form-control" name="smtp_passwort" id="smtp_passwort" value="<?php echo $smtp_passwort; ?>" required>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-university" aria-hidden="true"></i> &nbsp; Institut
				</div>
				<div class="card-body">
					<div class="form-group">
						<label for="institut">Institut:</label>
						<input type="text" class="form-control" name="institut" id="institut" value="<?php echo $institut; ?>" required>
					</div>
					<div class="form-group">
						<label for="institut_url">URL zum Institut:</label>
						<input type="text" class="form-control" name="institut_url" id="institut_url" value="<?php echo $institut_url; ?>" required>
					</div>
					<div class="form-group">
						<label for="institut_mail">E-Mail vom Institut:</label>
						<input type="text" class="form-control" name="institut_mail" id="institut_mail" value="<?php echo $institut_mail; ?>" required>
					</div>
				</div>
			</div>

			<div class="card mb-3">
				<div class="card-header">
					<i class="fa fa-facebook-official" aria-hidden="true"></i> &nbsp; Social Media
				</div>
				<div class="card-body">
					<div class="form-group">
						<label for="fb_title">Titel für Sharebeitrag:</label>
						<input type="text" class="form-control" name="fb_title" id="fb_title" value="<?php echo $fb_title; ?>">
					</div>
					<div class="form-group">
						<label for="fb_description">Beschreibung für Sharebeitrag:</label>
						<input type="text" class="form-control" name="fb_description" id="fb_description" value="<?php echo $fb_description; ?>">
					</div>
					<div class="form-group">
						<label for="fb_url">URL zur Facebook-Seite:</label>
						<input type="text" class="form-control" name="fb_url" id="fb_url" value="<?php echo $fb_url; ?>">
					</div>
					<div class="form-group">
						<label for="yt_url">URL zum YouTube-Channel:</label>
						<input type="text" class="form-control" name="yt_url" id="yt_url" value="<?php echo $yt_url; ?>">
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="col text-right pb-3">
		<button type="submit" class="btn btn-success btn-lg" name="speichern" value="Daten speichern">Speichern!</button>
	</div>
</form>

<?php
	include("footer.php");
?>

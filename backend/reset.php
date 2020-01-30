<?php
	session_start();
	header('Content-Type: text/html; charset=utf-8');
	//Datenbank einlesen
	require('../connection.inc.php');
	require('../function.inc.php');

	// Institutsdaten einlesen
	$institut_sql 	= sql_select_where('all', 'institut', 'ins_id', $institut_id, '', '');
	$institut_row 	= mysqli_fetch_object($institut_sql);
	$institut 		= $institut_row->ins_institut;
	$sitetitel 		= $institut_row->ins_sitetitel;
	$institut_mail 	= $institut_row->ins_institut_mail;
	$institut_url 	= $institut_row->ins_institut_url;
	$email_von 		= $institut_row->ins_email_von;
	$email_zu 		= $institut_row->ins_email_zu;
	$email_kopie 	= $institut_row->ins_email_kopie;
	$microsite_url	= $institut_row->ins_microsite_url;
	$fb_title 		= $institut_row->ins_fb_title;
	$fb_description = $institut_row->ins_fb_description;
	$fb_url 		= $institut_row->ins_fb_url;
	$yt_url 		= $institut_row->ins_yt_url;
	$smtp_server 	= $institut_row->ins_smtp_server;
	$smtp_user 		= $institut_row->ins_smtp_user;
	$smtp_passwort 	= $institut_row->ins_smtp_passwort;
	$smtp_port 		= $institut_row->ins_smtp_port;

	// Sessions
	$_SESSION['login'] 			= '';
	$_SESSION['user_email'] 	= '';
	$_SESSION['user_access'] 	= '';
	$_SESSION['user_id'] 		= '';

	// GET Angaben
	$user_id = $_GET['id'];
	$regcode = $_GET['regcode'];

	$fehler 		= 0;
	$fehlerangabe 	= '';

	// Benutzer einlesen
	$user_sql 	= sql_select_where('all', 'user', array('use_id', 'use_regcode'), array($user_id, $regcode), '', '');
	$user_row 	= mysqli_fetch_object($user_sql);
	$user_email = $user_row->use_email;
	$access 	= $user_row->use_access;

	// zurücksetzen & einloggen
	if (isset($_POST['zuruecksetzen'])) {
		// Formularangaben einlesen
		$pw 				= md5($_POST['passwort']);
		$pw_wiederholung 	= md5($_POST['wiederholung']);

		// Prüfen ob Passwort übereinstimmt
		if ($pw !== '' AND $pw == $pw_wiederholung) {
			// Update Datenbank
			$user_update = sql_update('user', array('use_passwort', 'use_status', 'chg_user'), array($pw, '1', $user_email), 'use_id', $user_id);

			// Prüfen, ob Datensatz aktualisiert wurde
			if ($user_update == true) {
				// Sessions setzen
				$_SESSION['login'] 			= 'ok';
				$_SESSION['user_email'] 	= $user_email;
				$_SESSION['user_access'] 	= $access;
				$_SESSION['user_id'] 		= $user_id;
				$_SESSION['first'] 	= '<div class="alert alert-success alert-dismissible" role="alert"><button class="close" type="button" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">×</span></button>Ihr Passwort wurde erfolgreich geändert!</div>';

				header('Location: index.php');
			} else {
				$fehlerangabe .= '<div class="alert alert-danger alert-dismissible" role="alert"><button class="close" type="button" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">×</span></button><b>Fehler!</b> Das Passwort konnte leider nicht geändert werden. Bitte kontaktieren Sie den Websiteadministrator!</div>';
			}
		} else {
			$fehler++;
			$fehlerangabe .= '<div class="alert alert-danger alert-dismissible" role="alert"><button class="close" type="button" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">×</span></button><b>Fehler!</b> Ihre Eingaben müssen übereinstimmen.</div>';
		}
	}
?>
<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<meta name="description" content="<?php echo $sitetitel; ?> <?php echo $institut; ?>">
		<meta name="author" content="FFE media">
		<title><?php echo $institut; ?> &ndash; <?php echo $sitetitel; ?></title>

		<link type="text/css" rel="stylesheet" href="/vendor/bootstrap/css/bootstrap.min.css">
		<link type="text/css" rel="stylesheet" href="/vendor/font-awesome/css/font-awesome.min.css">
		<link type="text/css" rel="stylesheet" href="/css/sb-admin.css">

		<link rel="apple-touch-icon" sizes="57x57" href="favicon/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="favicon/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="favicon/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="favicon/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="favicon/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="favicon/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="favicon/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="favicon/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="favicon/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192"  href="favicon/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="favicon/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="favicon/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="favicon/favicon-16x16.png">
		<link rel="manifest" href="favicon/manifest.json">
		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="favicon/ms-icon-144x144.png">
		<meta name="theme-color" content="#ffffff">
	</head>

	<body class="bg-dark">

		<div class="container">
			<div class="card card-login mx-auto mt-5">
				<div class="card-header">
					Passwort zurücksetzen
				</div>
				<div class="card-body">
				<?php if (mysqli_num_rows($user_sql) !== 1) { ?>
					<div class="alert alert-danger alert-dismissible fade in" role="alert">
						<button class="close" type="button" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">×</span></button>
						<b>Fehler!</b> Der Link ist ungültig.
					</div>
				<?php } else { ?>
						<?php echo $fehlerangabe; ?>
						<form action="" method="post">
							<div class="form-group">
								<label for="passwort">Passwort</label>
								<input type="password" class="form-control" name="passwort" placeholder="Passwort" required>
							</div>
							<div class="form-group">
								<label for="wiederholung">Wiederholung</label>
								<input type="password" class="form-control" name="wiederholung" placeholder="Passwortwiederholung" required>
							</div>
							<button type="submit" id="zuruecksetzen" name="zuruecksetzen" value="Zurücksetzen" class="btn btn-primary btn-block">Zurücksetzen</button>
						</form>
				<?php } ?>
				</div>
			</div>
		</div>

		<!-- Bootstrap core JavaScript -->
		<script src="vendor/jquery/jquery.min.js"></script>
		<script src="vendor/popper/popper.min.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	</body>
</html>

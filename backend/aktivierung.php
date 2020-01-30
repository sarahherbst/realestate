<?php
	session_start();
	header('Content-Type: text/html; charset=utf-8');
	//Datenbank einlesen
	require('../connection.inc.php');
	require('../function.inc.php');

	//Institutdaten einlesen
	$institut_sql 		= sql_select_where('all', 'institut', 'ins_id', $institut_id, '', '');
	$institut_row 		= mysqli_fetch_object($institut_sql);
	$institut 			= $institut_row->wet_institut;
	$sitetitel 			= $institut_row->ins_sitetitel;
	$keywords 			= $institut_row->wet_keywords;
	$microsite_url		= $institut_row->ins_microsite_url;

	//GET Angaben
	$user_id 			= $_GET['id'];
	$user_regcode 		= $_GET['regcode'];

	$fehler 			= 0;
	$fehlerangabe 		= "";

	// Benutzer einlesen
	$user_sql 			= sql_select_where('all', 'user', array('use_id', 'use_regcode', 'use_status'), array($user_id, $user_regcode, '9'), '', '');
	$user_row 			= mysqli_fetch_object($user_sql);
	$user_access 		= $user_row->use_access;
	$user_email 		= $user_row->use_email;

	//Aktivierung & Login
	if (isset($_POST['aktivierung'])) {
		// Formularangaben einlesen
		$pw 				= md5($_POST['passwort']);
		$pw_wiederholung 	= md5($_POST['wiederholung']);

		// Prüfen ob Passwort übereinstimmt
		if ($pw != '' AND $pw == $pw_wiederholung) {
			// Update Datenbank
			$aktivierung_sql = 'UPDATE user SET use_passwort = "$pw", use_status = "1", chg_user = "$user_email", chg_time = curtime(), chg_date = curdate() WHERE use_id = "$user_id" ';
			$aktivierung_res = mysqli_query($db,$aktivierung_sql);

			// Prüfen, ob Datensatz aktualisiert wurde
			if ($aktivierung_res == true) {
				// Sessions setzen
				$_SESSION['login'] 			= 'ok';
				$_SESSION['user_email'] 	= $user_email;
				$_SESSION['user_access'] 	= $user_access;
				$_SESSION['user_id'] 		= $user_id;
				$_SESSION['first'] 	= '<div class="alert alert-success alert-dismissible fade in" role="alert"><button class="close" type="button" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">×</span></button>Ihr Benutzeraccount wurde erfolgreich aktiviert! Sie können sich in Zukunft mit Ihrer E-Mail-Adresse und Ihrem soeben angelegten Passwort unter '.$microsite_url.' einloggen.</div>';

				header('Location: index.php');
			} else {
				$fehler++;
				$fehlerangabe .= '<div class="alert alert-danger alert-dismissible fade in" role="alert"><button class="close" type="button" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">×</span></button><b>Fehler!</b> Der Benutzeraccount konnte leider nicht aktiviert werden. Bitte kontaktieren Sie den Websiteadministrator!</div>';
			}
		} else {
			$fehler++;
			$fehlerangabe .= '<div class="alert alert-danger alert-dismissible fade in" role="alert"><button class="close" type="button" data-dismiss="alert" aria-label="Schließen"><span aria-hidden="true">×</span></button><b>Fehler!</b> Die Passwörter müssen übereinstimmen.</div>';
		}
	}
?>

<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<meta name='description' content='<?php echo $sitetitel; ?> <?php echo $institut; ?>'>
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
					Account-Aktivierung
				</div>
				<div class='card-body'>
					<?php
						// Abfrage ob bereits aktiv
						if (mysqli_num_rows($user_sql) != 1) {
					?>
						<div class="alert alert-danger alert-dismissible" role="alert"><b>Fehler!</b> Der Aktivierungslink ist ungültig.</div>
					<?php } else { ?>
						<form class="form-signin" action="" method="post">
							<h2 class="form-signin-heading">Bitte erstellen Sie zur Aktivierung Ihres Benutzeraccounts ein Passwort!</h2>
							<?php echo $fehlerangabe; ?>
							<label class="sr-only" for="passwort">Passwort</label>
							<input type="password" placeholder="Passwort" class="form-control" id="passwort" name="passwort" required>

							<label class="sr-only" for="passwort">Wiederholung</label>
							<input type="password" placeholder="Wiederholung" class="form-control" id="wiederholung" name="wiederholung" required>
							<br><br>
							<button type="submit" class="btn btn-lg btn-primary btn-block" name="aktivierung">Jetzt aktivieren!</button>
						</form>
					<?php }	?>
				</div>
			</div>
		</div>

		<!-- Bootstrap core JavaScript -->
		<script type="text/javascript" src="vendor/jquery/jquery.min.js" defer></script>
		<script type="text/javascript" src="vendor/popper/popper.min.js" defer></script>
		<script type="text/javascript" src="vendor/bootstrap/js/bootstrap.min.js" defer></script>
	</body>
</html>

<?php
	// DB-Connection schließen:
	mysqli_close($db);
?>
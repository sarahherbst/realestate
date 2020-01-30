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

	$fehler 		= 0;
	$fehlerangabe 	= '';
	$pw_vergessen 	= '';

	if ( !isset($user_email) ) {
		$user_email = '';
	}

	// Login
	if ( isset($_POST['login']) ) {
		// E-Mail einlesen
		$user_email = $_POST['email'];

		// Passworteingabe abgleichen
		if ( !isset($_POST['passwort']) || $_POST['passwort'] == '') {
			$pw_vergessen =	'<button type="submit" id="reset_passwort" name="reset_passwort" value="Passwort vergessen?" class="btn btn-primary btn-block">Passwort zurücksetzen</button>';
		} else {
			// Passwort einlesen
			$pw = md5($_POST['passwort']);

			$user_sql = sql_select_where('all', 'user', array('use_email', 'use_passwort', 'use_status'), array($user_email, $pw, '1'), '', '');
			$user_row = mysqli_fetch_object($user_sql);

			if (mysqli_num_rows($user_sql) == 0) {
				$fehler++;
				$fehlerangabe .= '<div class="alert alert-danger" role="alert"><b>Login fehlgeschlagen!</b> E-Mail oder Passwort ist ungültig.</div>';
				$pw_vergessen =	'<button type="submit" id="reset_passwort" name="reset_passwort" value="Passwort vergessen?" class="btn btn-primary btn-block">Passwort zurücksetzen</button>';
			} else {
				$user_access 				= $user_row->use_access;
				$user_id 					= $user_row->use_id;
				$_SESSION['login'] 			= 'ok';
				$_SESSION['user_email'] 	= $user_email;
				$_SESSION['user_access'] 	= $user_access;
				$_SESSION['user_id']		= $user_id;

				header('Location: index.php');
			}
		}
	} elseif ( isset($_POST['reset_passwort']) ) {
		// Passwort zurücksetzen
		// einlesen der im Formular angegebenen Werte
		$user_email = $_POST['email'];
		$user_sql 	= sql_select_where('all', 'user', array('use_institut', 'use_email'), array($institut_id, $user_email), '', '');
		$user_row 	= mysqli_fetch_object($user_sql);

		if (mysqli_num_rows($user_sql) == 0) {
			$fehler++;
			$fehlerangabe   .= '<div class="alert alert-danger" role="alert">Ein Benutzer mit dieser E-Mail-Adresse existiert nicht.</div>';
		} else {
			//User-ID etc. abfragen für Registrierungsmail
			$user_id 		= $user_row->use_id;
			$user_vorname 	= $user_row->use_vorname;
			$user_nachname 	= $user_row->use_nachname;
			$user_regcode 	= rand(1, 99999999);

			$user_update 	= sql_update('user', array('use_regcode', 'chg_user'), array($user_regcode, $user_email), 'use_institut', $institut_id);
			$mailfooter_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel'), array($institut_id, 'mailfooter'), '', '');
			$mailfooter_row = mysqli_fetch_object($mailfooter_sql);
			$mailfooter 	= $mailfooter_row->txt_text;

			//Registrierungsmail mit Aktivierungslink versenden
			// Include PHPMailer class
			require('phpmailer/PHPMailerAutoload.php');

			//Setup PHPMailer
			$mail 				= new PHPMailer;
			$mail->setLanguage('de', 'phpmailer/language/');
			$mail->CharSet 		='UTF-8';
			//$mail ->SMTPDebug = 3; 					// Enable verbose debug output
			$mail->isSMTP(); 						// Set mailer to use SMTP
			$mail->Host 		= $smtp_server; 	// Specify main and backup SMTP servers
			$mail->SMTPOptions 	= array(
				'ssl' => array(
					'verify_peer' => false,
					'verify_peer_name' => false,
					'allow_self_signed' => true
				)
			);
			$mail->SMTPAuth 	= true; 			// Enable SMTP authentication
			$mail->Username 	= $smtp_user; 		// SMTP username
			$mail->Password 	= $smtp_passwort; 	// SMTP password
			$mail->SMTPSecure 	= 'ssl'; 			// Enable TLS encryption, `ssl` also accepted
			$mail->Port 		= $smtp_port; 		// TCP port to connect to
			$mail->isHTML(true);					// Set email format to html

			//Absender
			$mail->SetFrom($email_von, $institut);
			$mail->Sender 		= ($email_von);
			$mail->addReplyTo($email_zu, $institut);

			//Empfänger
			$name_empfaenger 	= $user_vorname.' '.$user_nachname;
			$mail->addAddress($user_email, $name_empfaenger);

			//Betreff
			$mail->Subject 		= 'Passwort zurücksetzen';

			//Nachricht
			$mail->Body    		= '<p style="color:#333;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.3;">Sehr geehrte(r) '.$user_vorname.' '.$user_nachname.', </p>';
			$mail->Body 		.= '<p style="color:#333;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.3;">';
			$mail->Body 		.= 'bitte benutzen Sie diesen <a href="'.$microsite_url.'/reset.php?id='.$user_id.'&regcode='.$user_regcode.'">Link</a> ';
			$mail->Body 		.= 'f&uuml;r die Eingabe eines neuen Passwortes f&uuml;r Ihren Zugang zum '.$sitetitel.'.</p>';
			$mail->Body 		.= '<p style="color:#333;font-family:Helvetica,Arial,sans-serif;font-weight:400;line-height:1.3;">'.$mailfooter.'</p>';

			$mail->AltBody 		= 'Sehr geehrte(r) '.$user_vorname.' '.$user_nachname.', \r\n bitte benutzen Sie folgenden Link für die Eingabe eines neuen Passwortes für Ihren Zugang zum '.$sitetitel;
			$mail->AltBody 		.= "\r\n \r\n";
			$mail->AltBody 		.= 'Link zur Erstellung eines neuen Passwortes: https://'.$microsite_url.'/reset.php?id='.$user_id.'&regcode='.$user_regcode;
			$mail->AltBody 		.= "\r\n \r\n";
			$mail->AltBody 		.= '_______________________';
			$mail->AltBody 		.= "\r\n";
			$mailfooter 		= str_replace('<br />', '\r\n', $mailfooter);
			$mailfooter 		= str_replace('<hr>', '', $mailfooter);
			$mail->AltBody 		.= $mailfooter;
			
			//E-Mail versenden
			if( !$mail->Send() ) {
				$fehler++;
				$fehlerangabe  .= '<div class="alert alert-danger" role="alert">Fehler! Es konnte leider keine Mail versendet werden. Bitte kontaktieren Sie den Websiteadministrator! <br>'.$mail->ErrorInfo.'</div>';
				$pw_vergessen 	= '<button type="submit" id="reset_passwort" name="reset_passwort" value="Passwort vergessen?" class="btn btn-primary btn-block">Passwort zurücksetzen</button>';
			} else {
				$fehlerangabe  .= '<div class="alert alert-success" role="alert">Es wurde Ihnen eine Mail zur Erstellung eines neuen Passwortes zugesandt.</div>';
				$pw_vergessen 	= '<button type="submit" id="reset_passwort" name="reset_passwort" value="Passwort vergessen?" class="btn btn-primary btn-block">Passwort zurücksetzen</button>';
			}
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
					Login
				</div>
				<div class="card-body">
					<?php echo $fehlerangabe; ?>
					<form action="" method="post">
						<div class="form-group">
							<label for="email">E-Mail</label>
							<input type="email" class="form-control" name="email" aria-describedby="emailHelp" placeholder="E-Mail Adresse" value="<?php echo $user_email; ?>" required>
						</div>
						<div class="form-group">
							<label for="passwort">Passwort</label>
							<input type="password" class="form-control" name="passwort" placeholder="Passwort">
						</div>
						<button type="submit" id="login" name="login" value="Login" class="btn btn-primary btn-block">Login</button>
						<?php echo $pw_vergessen; ?>
					</form>
				</div>
			</div>
		</div>

		<!-- Bootstrap core JavaScript -->
		<script src="vendor/jquery/jquery.min.js"></script>
		<script src="vendor/popper/popper.min.js"></script>
		<script src="vendor/bootstrap/js/bootstrap.min.js"></script>
	</body>
</html>

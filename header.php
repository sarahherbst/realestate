<?php 

	include('track.inc.php'); 
	header('Content-Type:text/html;charset=utf8');

?>

<!DOCTYPE html>
<html lang="de">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

		<meta name="description" content="<?php echo $description; ?>">
		<meta name="author" content="<?php echo $institut; ?> und FFE media">
		<meta name="keywords" content="<?php echo $keywords; ?>">

		<meta property="og:url" content="<?php echo $microsite_url; ?>" />
		<meta property="og:image" content="<?php echo $microsite_url; ?>/img/facebook.png" />
		<meta property="og:title" content="<?php echo $fb_title; ?>" />
		<meta property="og:description" content="<?php echo $fb_description; ?>" />

		<link rel="apple-touch-icon" sizes="57x57" href="/favicon/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="/favicon/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="/favicon/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="/favicon/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="/favicon/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="/favicon/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="/favicon/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="/favicon/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192"  href="/favicon/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="/favicon/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="/favicon/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="/favicon/favicon-16x16.png">
		<link rel="manifest" href="/favicon/manifest.json">
		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
		<meta name="theme-color" content="#ffffff">

		<!-- Bootstrap CSS -->
		<link type="text/css" rel="stylesheet" href="/css/bootstrap.css">
		<link type="text/css" rel="stylesheet" href="/css/style.css">
		<link type="text/css" rel="stylesheet" href="/open-iconic/font/css/open-iconic-bootstrap.css">
		
		<title><?php echo $sitetitel; ?></title>
	</head>

	<body class="bg-lightgray-gradient">

		<!-- Navigation Desktop -->
		<nav class="menu d-none d-md-block">
			<div class="hamburger">
				<div class="hamburger-line"></div>
				<div class="hamburger-line"></div>
				<div class="hamburger-line"></div>
			</div>
			<div class="menu-inner">
				<ul class="menu-container">
					<li>
						<a class="navbar-brand" href="<?php echo $microsite_url; ?>" onclick="trackMenu(this,'Startseite');">
							<img src="/img/logo-positiv.svg" width="150" height="48" alt="Logo <?php echo $institut; ?>" title="<?php echo $institut; ?>">
						</a>
					</li>
					<li class="menu-item <?php echo ($page  == 'index' ? 'active' : ''); ?>">
						<a class="menu-link" href="<?php echo $microsite_url; ?>" onclick="trackMenu(this,'Startseite');" title="Start">
							Start <?php echo ($page  == 'index' ? '<span class="sr-only">(aktuell)</span>' : ''); ?>
						</a>
					</li>

					<?php
						$sql_category = sql_select_where('all', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
						while ($row_category = mysqli_fetch_assoc($sql_category)) {
					?>
					<li class="menu-item <?php echo ($page  == 'rub_'.$row_category['rub_id'] ? 'active' : ''); ?>">
						<a class="menu-link" href="/rubrik.php?rub_id=<?php echo $row_category['rub_id']; ?>" onclick="trackMenu(this,'<?php echo $row_category['rub_name']; ?>');" title="<?php echo $row_category['rub_name']; ?>">
							<?php echo $row_category['rub_name']; ?> <?php echo ($page  == 'rub_'.$row_category['rub_id'] ? '<span class="sr-only">(aktuell)</span>' : ''); ?>
						</a>
					</li>
					<?php } ?>

					<li class="menu-item">
						<a class="menu-link" href="<?php echo $microsite_url; ?>/files/Immobilienmarktbericht-2019.pdf" onclick="trackMenu(this,'Marktbericht 2019');" title="Marktbericht 2019" target="_blank">Marktbericht 2019</a>
					</li>

					<li class="menu-item <?php echo ($page  == 'ansprechpartner' ? 'active' : ''); ?>">
						<a class="menu-link" href="<?php echo $microsite_url; ?>/ansprechpartner.php" onclick="trackMenu(this,'Ansprechpartner');" title="Ansprechpartner">
							Ansprechpartner <?php echo ($page  == 'ansprechpartner' ? '<span class="sr-only">(aktuell)</span>' : ''); ?>
						</a>
					</li>
				</ul>
			</div>

			<svg version="1.1" class="blob" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
				<path class="blob-path" d="M60,500H0V0h60c0,0,20,172,20,250S60,900,60,500z"></path>
			</svg>
		</nav>

		<nav class="mobilemenu d-block d-md-none">
			<div class="mobilemenu-hamburger">
				<span class="mobilemenu-hamburger-line-top"></span>
				<span class="mobilemenu-hamburger-line-middle"></span>
				<span class="mobilemenu-hamburger-line-bottom"></span>
			</div>

			<div class="mobilemenu-inner">
				<ul class="menu-container">
					<li>
						<a class="navbar-brand" href="<?php echo $microsite_url; ?>" onclick="trackMenu(this,'Startseite');">
							<img src="/img/logo-positiv.svg"  width="150" height="48" alt="Logo <?php echo $institut; ?>" title="<?php echo $institut; ?>">
						</a>
					</li>
					<li class="menu-item <?php echo ($page  == 'index' ? 'active' : ''); ?>">
						<a class="menu-link" href="<?php echo $microsite_url; ?>" onclick="trackMenu(this,'Startseite');" title="Start">
							Start <?php echo ($page  == 'index' ? '<span class="sr-only">(aktuell)</span>' : ''); ?>
						</a>
					</li>

					<?php
						$sql_category = sql_select_where('all', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
						while ($row_category = mysqli_fetch_assoc($sql_category)) {
					?>
					<li class="menu-item <?php echo ($page  == 'rub_'.$row_category['rub_id'] ? 'active' : ''); ?>">
						<a class="menu-link" href="<?php echo $microsite_url; ?>/rubrik.php?rub_id=<?php echo $row_category['rub_id']; ?>" onclick="trackMenu(this,'<?php echo $row_category['rub_name']; ?>');" title="<?php echo $row_category['rub_name']; ?>">
							<?php echo $row_category['rub_name']; ?> <?php echo ($page  == 'rub_'.$row_category['rub_id'] ? '<span class="sr-only">(aktuell)</span>' : ''); ?>
						</a>
					</li>
					<?php } ?>

					<li class="menu-item">
						<a class="menu-link" href="<?php echo $microsite_url; ?>/files/Immobilienmarktbericht-2019.pdf" onclick="trackMenu(this,'Marktbericht 2019');" title="Marktbericht 2019" target="_blank">Marktbericht 2019</a>
					</li>

					<li class="menu-item <?php echo ($page  == 'ansprechpartner' ? 'active' : ''); ?>">
						<a class="menu-link" href="<?php echo $microsite_url; ?>/ansprechpartner.php" onclick="trackMenu(this,'Ansprechpartner');" title="Ansprechpartner">
							Ansprechpartner <?php echo ($page  == 'ansprechpartner' ? '<span class="sr-only">(aktuell)</span>' : ''); ?>
						</a>
					</li>
				</ul>
			</div>
			<!-- Kontaktbuttons mobil -->
			<div class="kontaktbuttons">
				<div>
					<a href="tel:07243 94746666" class="kontaktbtn"><img src="/img/tel.svg" alt="Rufen Sie uns an"></a>
					<div class="kontakbuttons_info">Rufen Sie uns unter 07243 94746666 an.</div>
				</div>
				<div>
					<a href="mailto:<?php echo $institut_mail; ?>" class="kontaktbtn"><img src="/img/mail.svg" alt="Schreiben Sie uns"></a>
					<div class="kontakbuttons_info"><span>Schreiben Sie uns unter <a href="mailto:<?php echo $institut_mail; ?>" title="Schreiben Sie uns"><?php echo $institut_mail; ?></a>.</span></div>
				</div>
			</div>
		</nav>
			
		<!-- Kontaktbuttons Desktop -->
		<div class="kontaktbuttons d-none d-md-block">
			<div>
				<a href="tel:07243 94746666" class="kontaktbtn"><img src="/img/tel.svg" alt="Rufen Sie uns an"></a>
				<div class="kontakbuttons_info">Rufen Sie uns unter 07243 94746666 an.</div>
			</div>
			<div>
				<a href="mailto:<?php echo $institut_mail; ?>" class="kontaktbtn"><img src="/img/mail.svg" alt="Schreiben Sie uns"></a>
				<div class="kontakbuttons_info"><span>Schreiben Sie uns unter <a href="mailto:<?php echo $institut_mail; ?>" title="Schreiben Sie uns"><?php echo $institut_mail; ?></a>.</span></div>
			</div>
		</div>

		<!-- Logo -->
		<nav class="navbar navbar-dark container-fluid bg-white pb-0 pb-sm-2" id="navigationbar">
			<a class="navbar-brand my-1 ml-3 p-0" href="<?php echo $microsite_url; ?>" onclick="trackMenu(this,'Startseite');">
				<img src="/img/logo-positiv.svg"  width="260" height="90" alt="Logo <?php echo $institut; ?>" title="<?php echo $institut; ?>">
			</a>
		</nav>
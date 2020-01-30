<?php
	session_start();
	header('Content-Type: text/html; charset=utf-8');
	
	// Sessions
	if ($_SESSION['login'] !== 'ok') {
		header('Location: login.php');
	}
	$user_email 	= $_SESSION['user_email'];
	$user_access 	= $_SESSION['user_access'];
	$user_id 		= $_SESSION['user_id'];

	//Datenbank einlesen
	require_once('../connection.inc.php');
	require_once('../function.inc.php');

	// Institutsdaten einlesen
	$institut_sql 	= sql_select_where('', 'institut', 'ins_id', $institut_id, '', '');
	$institut_row 	= mysqli_fetch_object($institut_sql);
	$institut 		= $institut_row->ins_institut;
	$sitetitel 		= $institut_row->ins_sitetitel;
	$keywords 		= $institut_row->ins_keywords;
	$description 	= $institut_row->ins_description;
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

		<!-- Bootstrap core CSS-->
		<link href="vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
		<!-- Custom fonts for this template-->
		<link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
		<!-- Page level plugin CSS-->
		<link href="vendor/datatables/dataTables.bootstrap4.css" rel="stylesheet">
		<!-- Custom styles for this template-->
		<link href="css/sb-admin.min.css" rel="stylesheet">

		<link rel="apple-touch-icon" sizes="57x57" href="../favicon/apple-icon-57x57.png">
		<link rel="apple-touch-icon" sizes="60x60" href="../favicon/apple-icon-60x60.png">
		<link rel="apple-touch-icon" sizes="72x72" href="../favicon/apple-icon-72x72.png">
		<link rel="apple-touch-icon" sizes="76x76" href="../favicon/apple-icon-76x76.png">
		<link rel="apple-touch-icon" sizes="114x114" href="../favicon/apple-icon-114x114.png">
		<link rel="apple-touch-icon" sizes="120x120" href="../favicon/apple-icon-120x120.png">
		<link rel="apple-touch-icon" sizes="144x144" href="../favicon/apple-icon-144x144.png">
		<link rel="apple-touch-icon" sizes="152x152" href="../favicon/apple-icon-152x152.png">
		<link rel="apple-touch-icon" sizes="180x180" href="../favicon/apple-icon-180x180.png">
		<link rel="icon" type="image/png" sizes="192x192"  href="../favicon/android-icon-192x192.png">
		<link rel="icon" type="image/png" sizes="32x32" href="../favicon/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="96x96" href="../favicon/favicon-96x96.png">
		<link rel="icon" type="image/png" sizes="16x16" href="../favicon/favicon-16x16.png">
		<link rel="manifest" href="../favicon/manifest.json">
		<meta name="msapplication-TileColor" content="#ffffff">
		<meta name="msapplication-TileImage" content="../favicon/ms-icon-144x144.png">
		<meta name="theme-color" content="#ffffff">

		<!--Load the AJAX API-->
		<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
	</head>

	<body id="page-top">

		<nav class="navbar navbar-expand navbar-dark bg-dark static-top">
			<a class="navbar-brand mr-1" href="index.php"><?php echo $institut; ?> &ndash; Backend</a>
			<button class="btn btn-link btn-sm text-white order-1 order-sm-0" id="sidebarToggle" href="#">
				<i class="fas fa-bars"></i>
			</button>

			<!-- Navbar -->
			<ul class="navbar-nav ml-auto">
				<li class="nav-item dropdown no-arrow">
					<a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-user-circle fa-fw"></i>
					</a>
					<div class="dropdown-menu dropdown-menu-right" aria-labelledby="userDropdown">
						<a class="dropdown-item" href="edit_user.php?use_id=<?php echo $user_id; ?>">Profil bearbeiten</a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="#" data-toggle="modal" data-target="#logoutModal">Logout</a>
					</div>
				</li>
			</ul>
		</nav>

		<div id="wrapper">

			<!-- Sidebar -->
			<ul class="sidebar navbar-nav">
				<li class="nav-item <?php echo ($page  == 'index' ? 'active' : ''); ?>">
					<a class="nav-link" href="index.php">
						<i class="fas fa-fw fa-tachometer-alt"></i>
						<span>Dashboard</span>
					</a>
				</li>
				
				<?php if($user_access == 'superadmin') { ?>
					<li class="nav-item <?php echo ($page  == 'edit_institut' ? 'active' : ''); ?>">
						<a class="nav-link" href="edit_institut.php">
							<i class="fas fa-fw fa-university"></i>
							<span>Institut</span>
						</a>
					</li>
				
					<li class="nav-item dropdown <?php echo (strpos($page,'user') !== false ? 'show' : ''); ?>">
						<a class="nav-link dropdown-toggle" href="#" id="pagesDropdownUser" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fas fa-fw fa-users"></i>
							<span>Benutzer</span>
						</a>
						<div class="dropdown-menu <?php echo (strpos($page,'user') !== false ? 'show' : ''); ?>" aria-labelledby="pagesDropdownUser">
							<h6 class="dropdown-header">Benutzer</h6>
							<a class="dropdown-item" href="user.php">bearbeiten</a>
							<a class="dropdown-item" href="new_user.php">hinzufügen</a>
						</div>
					</li>

					<li class="nav-item dropdown <?php echo (strpos($page,'rubrik') !== false ? 'show' : ''); ?>">
						<a class="nav-link dropdown-toggle" href="#" id="pagesDropdownRubrik" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fas fa-fw fa-folder-plus"></i>
							<span>Rubriken</span>
						</a>
						<div class="dropdown-menu <?php echo (strpos($page,'rubrik') !== false ? 'show' : ''); ?>" aria-labelledby="pagesDropdownRubrik">
							<h6 class="dropdown-header">Rubriken</h6>
							<a class="dropdown-item" href="category.php">bearbeiten</a>
							<a class="dropdown-item" href="new_category.php">hinzufügen</a>
						</div>
					</li>
				<?php } ?>

				<?php
					$sql_category = sql_select_where('all', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
					while ($row_category = mysqli_fetch_assoc($sql_category)) {
						$category_name 	= str_replace(' ', '-', $row_category['rub_name']);
						$category_ul 	= str_replace('&', '', $row_category['rub_name']);
				?>
					<li class="nav-item dropdown <?php echo ($category_titel == strtolower($category_name) ? 'show' : ''); ?>">
						<a class="nav-link dropdown-toggle" href="#" id="pagesDropdown<?php echo strtolower($category_ul); ?>" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fas fa-fw fa-folder-open"></i>
							<span><?php echo $row_category['rub_name']; ?></span>
						</a>
						<div class="dropdown-menu <?php echo ($category_titel == strtolower($category_name) ? 'show' : ''); ?>" aria-labelledby="pagesDropdown<?php echo strtolower($category_ul); ?>">
							<?php if($user_access == 'superadmin') { ?>
								<h6 class="dropdown-header">Teaser</h6>
								<a class="dropdown-item" href="edit_teaser.php?rub_id=<?php echo $row_category['rub_id']; ?>">bearbeiten</a>
								<div class="dropdown-divider"></div>
							<?php } ?>

							<h6 class="dropdown-header">Artikel</h6>
							<a class="dropdown-item" href="article.php?rub_id=<?php echo $row_category['rub_id']; ?>">bearbeiten</a>
							<a class="dropdown-item" href="new_article.php?rub_id=<?php echo $row_category['rub_id']; ?>">hinzufügen</a>

							<?php if($user_access == 'superadmin') { ?>
								<div class="dropdown-divider"></div>
								<h6 class="dropdown-header">Galeriebilder</h6>
								<a class="dropdown-item" href="gallery.php?rub_id=<?php echo $row_category['rub_id']; ?>">bearbeiten</a>
								<a class="dropdown-item" href="new_gallery.php?rub_id=<?php echo $row_category['rub_id']; ?>">hinzufügen</a>
								<div class="dropdown-divider"></div>

								<h6 class="dropdown-header">Video</h6>
								<a class="dropdown-item" href="edit_video.php?rub_id=<?php echo $row_category['rub_id']; ?>">bearbeiten</a>
								<div class="dropdown-divider"></div>

								<h6 class="dropdown-header">Immobilienliste</h6>
								<a class="dropdown-item" href="edit_immolist.php?rub_id=<?php echo $row_category['rub_id']; ?>">bearbeiten</a>
								<div class="dropdown-divider"></div>

								<h6 class="dropdown-header">Checkliste</h6>
								<a class="dropdown-item" href="checklist.php?rub_id=<?php echo $row_category['rub_id']; ?>">bearbeiten</a>
								<a class="dropdown-item" href="new_checkpoint.php?rub_id=<?php echo $row_category['rub_id']; ?>">Punkt hinzufügen</a>
								<div class="dropdown-divider"></div>
								
								<h6 class="dropdown-header">Testimonial</h6>
								<a class="dropdown-item" href="testimonial.php?rub_id=<?php echo $row_category['rub_id']; ?>">bearbeiten</a>
								<a class="dropdown-item" href="new_testimonial.php?rub_id=<?php echo $row_category['rub_id']; ?>">hinzufügen</a>
							<?php } ?>
						</div>
					</li>
				<?php } ?>

				<li class="nav-item dropdown <?php echo (strpos($page,'berater') !== false ? 'show' : ''); ?>">
					<a class="nav-link dropdown-toggle" href="#" id="pagesDropdownBerater" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-fw fa-address-book"></i>
						<span>Berater</span>
					</a>
					<div class="dropdown-menu <?php echo (strpos($page,'berater') !== false ? 'show' : ''); ?>" aria-labelledby="pagesDropdownBerater">
						<h6 class="dropdown-header">Teams</h6>
						<a class="dropdown-item" href="beratergruppe.php">bearbeiten</a>
						<a class="dropdown-item" href="new_beratergruppe.php">hinzufügen</a>
						<div class="dropdown-divider"></div>

						<h6 class="dropdown-header">Berater</h6>
						<a class="dropdown-item" href="berater.php">bearbeiten</a>
						<a class="dropdown-item" href="new_berater.php">hinzufügen</a>
					</div>
				</li>

				<li class="nav-item dropdown <?php echo (strpos($page,'statements') !== false ? 'show' : ''); ?>">
					<a class="nav-link dropdown-toggle" href="#" id="pagesDropdownStatements" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<i class="fas fa-fw fa-images"></i>
						<span>Kundenprojekte</span>
					</a>
					<div class="dropdown-menu <?php echo (strpos($page,'statements') !== false ? 'show' : ''); ?>" aria-labelledby="pagesDropdownStatements">
						<h6 class="dropdown-header">Kundenprojekte</h6>
						<a class="dropdown-item" href="statements.php">bearbeiten</a>
						<a class="dropdown-item" href="new_statement.php">hinzufügen</a>
					</div>
				</li>
				<?php if($user_access == 'superadmin') { ?>
					<li class="nav-item dropdown <?php echo (strpos($page,'award') !== false ? 'show' : ''); ?>">
						<a class="nav-link dropdown-toggle" href="#" id="pagesDropdownAuszeichnungen" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fas fa-fw fa-award"></i>
							<span>Auszeichnungen</span>
						</a>
						<div class="dropdown-menu <?php echo (strpos($page,'award') !== false ? 'show' : ''); ?>" aria-labelledby="pagesDropdownAuszeichnungen">
							<h6 class="dropdown-header">Auszeichnungen</h6>
							<a class="dropdown-item" href="award.php">bearbeiten</a>
							<a class="dropdown-item" href="new_award.php">hinzufügen</a>
						</div>
					</li>

					<li class="nav-item <?php echo ($page == 'objektbewertung' ? 'active' : ''); ?>">
						<a class="nav-link" href="edit_objektbewertung.php">
							<i class="fas fa-fw fa-home"></i>
							<span>Objektbewertungstool</span>
						</a>
					</li>

					<li class="nav-item <?php echo ($page == 'immobilienliste' ? 'active' : ''); ?>">
						<a class="nav-link" href="edit_immobilienliste.php">
							<i class="fas fa-fw fa-list-alt"></i>
							<span>Immobilienliste</span>
						</a>
					</li>

					<li class="nav-item <?php echo ($page == 'investitionstool' ? 'active' : ''); ?>">
						<a class="nav-link" href="edit_investitionstool.php">
							<i class="fas fa-fw fa-list-alt"></i>
							<span>Investitionstool</span>
						</a>
					</li>

					<li class="nav-item <?php echo ($page == 'kaeuferfinder' ? 'active' : ''); ?>">
						<a class="nav-link" href="edit_kaeuferfinder.php">
							<i class="fas fa-fw fa-search"></i>
							<span>Käuferfinder</span>
						</a>
					</li>

					<li class="nav-item <?php echo ($page == 'kontakt' ? 'active' : ''); ?>">
						<a class="nav-link" href="edit_kontakt.php">
							<i class="fas fa-fw fa-envelope"></i>
							<span>Kontakt</span>
						</a>
					</li>
				<?php } ?>

				<li class="nav-item <?php echo ($page == 'karriere' ? 'active' : ''); ?>">
					<a class="nav-link" href="edit_karriere.php">
						<i class="fas fa-fw fa-briefcase"></i>
						<span>Karriere</span>
					</a>
				</li>

				<li class="nav-item <?php echo ($page == 'edit_impressum' ? 'active' : ''); ?>">
					<a class="nav-link" href="edit_impressum.php">
						<i class="fas fa-fw fa-file"></i>
						<span>Impressum</span>
					</a>
				</li>

				<li class="nav-item <?php echo ($page == 'edit_datenschutz' ? 'active' : ''); ?>">
					<a class="nav-link" href="edit_datenschutz.php">
						<i class="fas fa-fw fa-file"></i>
						<span>Datenschutz</span>
					</a>
				</li>
				
				<?php if($user_access == 'superadmin') { ?>
					<li class="nav-item <?php echo ($page == 'edit_mailfooter' ? 'active' : ''); ?>">
						<a class="nav-link" href="edit_mailfooter.php">
							<i class="fas fa-fw fa-file"></i>
							<span>E-Mail Footer</span>
						</a>
					</li>
				<?php } ?>

				<li class="nav-item <?php echo ($page == 'statistik' ? 'active' : ''); ?>">
					<a class="nav-link" href="statistik.php">
						<i class="fas fa-fw fa-chart-area"></i>
						<span>Statistik</span>
					</a>
				</li>
			</ul>

			<div id="content-wrapper">

				<div class="container-fluid">
<?php
	$page 		= 'ansprechpartner';
	$ite_rubrik = 'Ansprechpartner';

	require('connection.inc.php');
	require('function.inc.php');
	require('data.inc.php');

	$txt_sql 	= sql_select_where('all', 'texte', array('txt_status', 'txt_schluessel'), array('1', 'berater'), '', '');
	$txt_row 	= mysqli_fetch_assoc($txt_sql);
	$img_sql 	= sql_select_where('all', 'images', array('img_status', 'img_schluessel'), array('1', 'berater-header'), '', '');
	$img_row 	= mysqli_fetch_assoc($img_sql);

	include('header.php');
?>
		<section class="bg-white first-section hero-wide">
			<!-- Header -->
			<div class="rubrik-hero">
				<div class="rubrik-hero-img">
					<img class="rubrik-hero-img-inner" src="/<?php echo $img_row['img_bild']; ?>" alt="<?php echo $img_row['img_titel']; ?>">
				</div>

				<div class="rubrik-hero-pulled">
					<div class="rubrik-hero-pulled-inner">
						<div class="rubrik-hero-card">
							<h2 class="rubrik-hero-card-subtitle text-black-50"><?php echo $txt_row['txt_titel']; ?></h2>
							<h1 class="rubrik-hero-card-title display-4 text-primary"><?php echo $ite_rubrik; ?></h1>
						</div>
					</div>
				</div>
			</div>
		</section>
		
		<!-- Einleitung -->
		<section class="pb-5 bg-white">
			<div class="container">
				<div class="card-deck">
					<div class="einleitung-card bg-blue-gradient shadow p-3 text-light ml-3 mr-3 mb-5">
						<div class="card-body">
							<h2 class="card-title mt-2 mb-4"><?php echo $txt_row['txt_titel']; ?></h2>
							<p class="card-text"><?php echo $txt_row['txt_einleitung']; ?></p>
						</div>
						<div class="card-footer">
							<a href="<?php echo $txt_row['txt_conversion_ziel']; ?>" class="btn btn-lg btn-outline-light text-uppercase shadow-none" title="<?php echo $txt_row['txt_conversion_titel']; ?>" target="<?php echo (strpos($txt_row['txt_conversion_ziel'],'form_') !== false ? '' : '_blank') ?>"><?php echo $txt_row['txt_conversion_titel']; ?></a>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- Ansprechpartner je Team-->
		<?php 
			$begr_sql = sql_select_where('all', 'beratergruppe', array('begr_institut', 'begr_status'), array($institut_id, '1'), '', ''); 
			while ($begr_row = mysqli_fetch_assoc($begr_sql)) {
		?>
		<section class="mb-5 pb-5 bg-white">
			<div class="container-fluid">
				<div class="row align-items-top">
					<?php $teambild_sql = sql_select_where('all', 'images', array('img_status', 'img_schluessel'), array('1', 'beratergruppe'), '', '');?>
					<?php 
						if (mysqli_num_rows($teambild_sql) >= 1) {
							$teambild_row = mysqli_fetch_assoc($teambild_sql);
						}
					?>
					<div class="col-12 col-md-11 py-3 bg-white px-md-5"> 
						<div class="row mb-5">
							<div class="col-12">
								<h2 class="font-weight-light text-primary"><?php echo $begr_row['begr_name']; ?></h2>
							</div>
						</div>
						<div class="row">
							<?php $berater_sql = sql_select_where('all', 'berater', array('ber_status', 'ber_gruppe'), array('1', $begr_row['begr_id']), '', '');  ?>
							<?php while ($berater_row = mysqli_fetch_assoc($berater_sql)) {  ?>
							<div class="col-12 col-lg-6 col-xl-4 py-3 <?php if($berater_row['ber_vorname'] == 'Ingo') { echo 'mr-xl-3'; } ?>">
								<div class="row align-items-top">
									<?php
										$profilbild_sql = sql_select_where('all', 'images', array('img_schluessel', 'img_item_id'), array('berater', $berater_row['ber_id']), '', '');
										if (mysqli_num_rows($profilbild_sql) >= 1) {
											$profilbild_row = mysqli_fetch_assoc($profilbild_sql);
									?>
										<?php if(!$profilbild_row['img_bild'] == '') { ?>
											<div class="col-4">
												<div class="rounded-circle" style="background-image:url('/<?php echo $profilbild_row['img_bild']; ?>')"></div>
											</div>
										<?php } ?>
									<?php } ?>
									<div class="<?php echo (mysqli_num_rows($profilbild_sql) >= 1 && isset($profilbild_row['img_bild']) && $profilbild_row['img_bild'] != '' ? 'col-8' : 'col-12'); ?>">
										<strong class="mb-2 text-primary"><?php echo $berater_row['ber_position']; ?></strong><br>
										<span class="h4 text-weight-light"><?php echo $berater_row['ber_vorname']; ?> <?php echo $berater_row['ber_nachname']; ?></span>
										<?php if(!$berater_row['ber_filiale'] == '') { ?>
											<br><small><?php echo $berater_row['ber_filiale']; ?></small>
										<?php } ?>
										<p class="mt-2">
											<span class="font-weight-light text-uppercase text-primary">Telefon:</span> <a href="tel:<?php echo $berater_row['ber_tel']; ?>"  onclick="trackItem(this,'Telefon', 'Ansprechpartner <?php echo $berater_row['ber_vorname']; ?> <?php echo $berater_row['ber_nachname']; ?>');" title="Telefonnummer"><?php echo $berater_row['ber_tel']; ?></a><br>
											<span class="font-weight-light text-uppercase text-primary">Mail:</span> <a href="mailto:<?php echo $berater_row['ber_email']; ?>"  onclick="trackItem(this,'E-Mail', 'Ansprechpartner <?php echo $berater_row['ber_vorname']; ?> <?php echo $berater_row['ber_nachname']; ?>');" class="wordbreak" title="Mail-Adresse"><?php echo $berater_row['ber_email']; ?></a>
										</p>
									</div>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>
					<?php if ($teambild_row['img_bild'] == '') { ?>
					<div class="col-12 col-lg-6 col-xl-4 text-light">
						<div class="row">
							<img src="<?php echo $teambild_row['img_bild']; ?>" alt="<?php echo $teambild_row['img_beschreibung']; ?>" class="img-fluid shadow-lg">
						</div>
					</div>
					<?php } ?>
				</div>
			</div>
		</section>
		<?php } ?>

<?php
	include('footer.php');
?>
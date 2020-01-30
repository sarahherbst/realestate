<?php
	$rub_id 		= $_GET['rub_id'];
	$page 			= 'rub_'.$rub_id;

	require('connection.inc.php');
	require('function.inc.php');
	require('data.inc.php');

	// Rubrikname auslesen
	$rubrik_sql 	= sql_select_where('all', 'rubrik', array('rub_institut', 'rub_id'), array($institut_id, $rub_id), '', '');
	$rubrik_row 	= mysqli_fetch_assoc($rubrik_sql);
	$ite_rubrik 	= $rubrik_row['rub_name'];

	if ($rubrik_row['rub_title'] !== '') {
		$sitetitel 	= $rubrik_row['rub_title'];
	}
	if ($rubrik_row['rub_description'] !== '') {
		$description 	= $rubrik_row['rub_description'];
	}

	// CSS Angaben auslesen
	$text_color		= $rubrik_row['rub_css_txt'];
	$bg_color 		= $rubrik_row['rub_css_bg'];

	// Session für Immobilientool
	$immotool_page_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel', 'txt_alias', 'txt_rubrik', 'txt_status'), array($institut_id, 'immobilientool', 'gameplay', $rub_id, '1'), '', '');

	if (mysqli_num_rows($immotool_page_sql) >= 1) {
		session_start();
	}

	$einleitung_sql = sql_select_where('txt_titel, txt_einleitung', 'texte', array('txt_status', 'txt_rubrik', 'txt_schluessel'), array('1', $rub_id, 'teaser'), '', '');
	$einleitung_row = mysqli_fetch_assoc($einleitung_sql);

	include('header.php');
?>
		<?php $artikel_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_status', 'txt_rubrik', 'txt_schluessel'), array($institut_id, '1', $rub_id, 'artikel'), 'new_date', 'DESC'); ?>

		<section class="bg-white first-section">

			<!-- Header -->
			<div class="rubrik-hero">
				<div class="rubrik-hero-img">
					<?php
						$testimonial_sql 	= sql_select_testimonial_where('all', 'texte', array('txt_institut', 'txt_status', 'txt_rubrik', 'txt_schluessel'), array($institut_id ,'1', $rub_id, 'testimonial'), '', '');
						$testimonial_row 	= mysqli_fetch_assoc($testimonial_sql);

						$header_sql 		= sql_select_where('all', 'images', array('img_institut', 'img_status',  'img_rubrik', 'img_schluessel'), array($institut_id, '1', $rub_id, 'teaser-header'), '', '');
						$header_num 		= mysqli_num_rows($header_sql);
						if ($header_num >= 1) {
							$img_row 			= mysqli_fetch_assoc($header_sql);
						} else {
							$img_sql 			= sql_select_where('all', 'images', array('img_status', 'img_rubrik', 'img_schluessel', 'img_item_id'), array('1', $rub_id, 'testimonial', $testimonial_row['txt_id']), '', '');
							$img_row 			= mysqli_fetch_assoc($img_sql);
						}
					?>
					<img class="rubrik-hero-img-inner" src="/<?php echo $img_row['img_bild']; ?>" alt="<?php echo $img_row['img_titel']; ?>">
				</div>

				<div class="rubrik-hero-pulled">
					<div class="rubrik-hero-pulled-inner">
						<div class="rubrik-hero-card">
							<h2 class="rubrik-hero-card-subtitle text-black-50"><?php echo $einleitung_row['txt_titel']; ?></h2>
							<h1 class="rubrik-hero-card-title display-4 text-<?php echo $text_color; ?>"><?php echo $rubrik_row['rub_name']; ?></h1>
							<br>
							<?php if ($einleitung_row['txt_conversion_ziel'] == true) { ?>
								<?php $immotool_page_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel', 'txt_alias', 'txt_rubrik', 'txt_status'), array($institut_id, 'objektbewertung', 'gameplay', $rub_id, '1'), '', ''); ?>
								<?php if (mysqli_num_rows($immotool_page_sql) >= 1) { ?>
									<a href="#hausbewertungstool" onclick="trackItem(this,'Header', 'Conversion Header');" class="btn btn-<?php echo $text_color; ?>" title="<?php echo $einleitung_row['txt_conversion_titel']; ?>"><?php echo $einleitung_row['txt_conversion_titel']; ?></a>
								<?php } else { ?>
									<a href="/<?php echo $einleitung_row['txt_conversion_ziel']; ?>" onclick="trackItem(this,'Header', 'Conversion Header');" class="btn btn-<?php echo $text_color; ?>" title="<?php echo $einleitung_row['txt_conversion_titel']; ?>"  target="<?php echo (strpos($einleitung_row['txt_conversion_ziel'],'form_') !== false ? '' : '_blank'); ?>"><?php echo $einleitung_row['txt_conversion_titel']; ?></a>
								<?php } ?>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- Einleitungsbox -->
		<section class="bg-white" id="einleitung">
			<div class="container">
				<div class="card-deck">
					<div class="einleitung-card bg-<?php echo $bg_color; ?> shadow p-3 text-light ml-md-3 mr-md-3 mb-5 w-100">
						<div class="row">
							<div class="col-12 col-md-8">
								<div class="card-body">
									<h2 class="card-title mt-2 mb-4"><?php echo $einleitung_row['txt_titel']; ?></h2>
									<div class="card-text"><?php echo $einleitung_row['txt_einleitung']; ?></div>
								</div>
								<div class="card-footer">
									<?php if ($einleitung_row['txt_conversion_ziel'] == true) { ?>
										<?php if (mysqli_num_rows($immotool_page_sql) >= 1) { ?>
											<a href="#hausbewertungstool" onclick="trackItem(this,'Einleitung', 'Conversion Einleitung');" class="btn btn-lg btn-outline-light text-uppercase shadow-none" title="<?php echo $einleitung_row['txt_conversion_titel']; ?>"><?php echo $einleitung_row['txt_conversion_titel']; ?></a>
										<?php } else { ?>
											<a href="/<?php echo $einleitung_row['txt_conversion_ziel']; ?>" onclick="trackItem(this,'Einleitung', 'Conversion Einleitung');" class="btn btn-lg btn-outline-light text-uppercase shadow-none" title="<?php echo $einleitung_row['txt_conversion_titel']; ?>"  target="<?php echo (strpos($einleitung_row['txt_conversion_ziel'],'form_') !== false ? '' : '_blank'); ?>"><?php echo $einleitung_row['txt_conversion_titel']; ?></a>
										<?php } ?>
									<?php } ?>
								</div>
							</div>
							<?php $teaser_img_sql = sql_select_where('img_bild', 'images', array('img_institut', 'img_rubrik', 'img_schluessel'), array($institut_id, $rubrik_row['rub_id'], 'teaser-cutout'), '', ''); ?>
							<?php $teaser_img_row = mysqli_fetch_assoc($teaser_img_sql); ?>
							<div class="col-12 col-md-4">
								<img src="/<?php echo $teaser_img_row['img_bild']; ?>" class="img-einleitung-rubrik d-none d-md-block">
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- Video -->
		<?php
			$video_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_status', 'txt_rubrik', 'txt_schluessel'), array($institut_id, '1', $rub_id,'video'), 'txt_titel', 'ASC');
			if ( mysqli_num_rows($video_sql) >= 1 ) {
		?>
		<?php while ($video_row = mysqli_fetch_assoc($video_sql)) { ?>
		<section class="mb-5 bg-<?php echo $bg_color; ?> container-fluid" id="video">
			<div class="row">
				<div class="col-12 col-md-6 col-lg-4">
					<div class="row justify-content-center align-items-center">
						<div class="col-12 col-md-12 py-5 text-light text-center">
								<span class="display-4"><?php echo $video_row['txt_titel']; ?></span>
								<p><?php echo $video_row['txt_einleitung']; ?></p>
								<a href="<?php echo $video_row['txt_conversion_ziel']; ?>" onclick="trackItem(this,'Video', 'Conversion Video');" class="btn btn-outline-light text-uppercase shadow-none" title="<?php echo $video_row['txt_conversion_titel']; ?>"  target="<?php echo (strpos($video_row['txt_conversion_ziel'],'form_') !== false ? '' : '_blank'); ?>"><?php echo $video_row['txt_conversion_titel']; ?></a>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-8 bg-dark">
					<div class="row">
						<section class="video-hero d-flex flex-grow-1">
							<div class="video-hero-bg d-flex flex-grow-1">
								<div class="row flex-grow-1 vcenter">
									<div class="col-md-12 vcenter">
										<a class="video-watch" id="play-button" href="#"><span class="oi oi-play-circle"></span> &nbsp;Video abspielen!</a>
									</div>
								</div>
							</div>
							<div class="video-video-wrapper">
								<a class="video-close video-video-close" id="pause-button" href="#"></a>
								<div class="video-youtube-wrapper">
									<div class="video-container">
										<iframe id="youtube-video" src="/<?php echo $video_row['txt_auszug']; ?>" frameborder="0" allowfullscreen=""></iframe>
									</div>
								</div>
							</div>
						</section>
					</div>
				</div>
			</div>
		</section>
		<?php } ?>
		<?php } ?>

		<!-- Galerie -->
		<?php
			$img_sql 		= sql_select_where('all', 'images', array('img_institut', 'img_status', 'img_rubrik', 'img_schluessel'), array($institut_id, '1', $rub_id, 'galerie'), 'img_titel', 'ASC');
			$galerie_sql 	= sql_select_where('all', 'texte', array('txt_institut', 'txt_status', 'txt_rubrik', 'txt_schluessel'), array($institut_id, '1', $rub_id, 'galerie'), '', '');
			$galerie_row 	= mysqli_fetch_assoc($galerie_sql);

			if (mysqli_num_rows($img_sql) >= 1) {
				$thumb_item 	= 1;
				$large_item 	= 1;
		?>
		<div id="top"></div>
		<section class="gallery pt-5 p-0 p-md-5" id="galerie">
			<div class="container-fluid">
				<div class="row align-items-xl-center">
					<!-- Einleitung -->
					<div class="col-12 col-md-6 col-lg-5 col-xl-3 mb-3 px-0 px-md-1">

						<div class="card einleitung-card bg-dark shadow p-3 text-light text-center">
							<div class="card-body">
								<h2 class="card-title display-4 font-weight-light mt-2 mb-4"><?php echo $galerie_row['txt_titel']; ?></h2>
								<p class="card-text"><?php echo $galerie_row['txt_einleitung']; ?></p>
							</div>
							<div class="card-footer">
								<?php if ($galerie_row['txt_conversion_ziel'] == true) { ?>
								<a href="<?php echo $galerie_row['txt_conversion_ziel']; ?>" onclick="trackItem(this,'Galerie', 'Conversion Galerie');" class="btn btn-outline-light text-uppercase shadow-none" title="<?php echo $galerie_row['txt_conversion_titel']; ?>"  target="<?php echo (strpos($galerie_row['txt_conversion_ziel'],'form_') !== false ? '' : '_blank'); ?>"><?php echo $galerie_row['txt_conversion_titel']; ?></a>
								<?php } ?>
							</div>
						</div>
					</div>
					<!-- Galeriebilder -->
					<div class="col-12 col-md-6 col-lg-7 col-xl-9">
						<ul class="row justify-content-center">
							<?php while ($img_row = mysqli_fetch_assoc($img_sql)) { ?>
								<li class="col-6 col-sm-4 col-md-6 col-lg-6 col-xl-3 mb-3">
									<a href="#item<?php echo $thumb_item; ?>" class="bg-white shadow">
										<img src="/<?php echo $img_row['img_thumb']; ?>" class="w-100" alt="<?php echo $img_row['img_titel']; ?>">
										<p class="m-4 text-uppercase text-center d-none d-md-block"><?php echo $img_row['img_titel']; ?></p>
									</a>
								</li>
								<?php $thumb_item++; ?>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>

			<!-- Lightbox -->
			<?php
				$img_sql 		= sql_select_where('all', 'images', array('img_institut', 'img_status', 'img_rubrik', 'img_schluessel'), array($institut_id, '1', $rub_id, 'galerie'), 'img_titel', 'ASC');
				while ($img_row = mysqli_fetch_assoc($img_sql)) {
			?>
				<div id="item<?php echo $large_item; ?>" class="port">
					<div class="row">
						<div class="col-12 col-md-6">
							<a href="#" class="gallery-close"></a>
							<img src="/<?php echo $img_row['img_thumb']; ?>" alt="<?php echo $img_row['img_titel']; ?>">
						</div>
						<div class="col-12 col-md-6 description">
							<h1 class="text-<?php echo $text_color; ?> font-weight-light pt-4 pt-md-0 pb-2 pb-md-0"><?php echo $img_row['img_titel']; ?></h1>
							<p class="text-black-50"><?php echo $img_row['img_beschreibung']; ?></p>
							<?php if ($galerie_row['txt_conversion_ziel'] == true) { ?>
							<a href="<?php echo $galerie_row['txt_conversion_ziel']; ?>" onclick="trackItem(this,'Galerie', 'Conversion Galerie');" class="btn btn-outline-<?php echo $text_color; ?> text-uppercase shadow-none mt-4 mt-md-0" title="<?php echo $galerie_row['txt_conversion_titel']; ?>" target="<?php echo (strpos($galerie_row['txt_conversion_ziel'],'form_') !== false ? '' : '_blank'); ?>"><?php echo $galerie_row['txt_conversion_titel']; ?></a>
							<?php } ?>
						</div>
					</div> <!-- / row -->
				</div> <!-- / Item -->
				<?php $large_item++; ?>
			<?php } ?>

		</section> <!-- / projects -->
		<?php } ?>

		<!-- Checkliste -->
		<?php
			$checklist_sql 	= sql_select_where('txt_titel, txt_einleitung', 'texte', array('txt_institut', 'txt_status', 'txt_rubrik', 'txt_schluessel', 'txt_alias'), array($institut_id, '1', $rub_id, 'checkliste', 'checklist'), '', '');
			$checkpoint_sql = sql_select_where('txt_titel, txt_einleitung', 'texte', array('txt_institut', 'txt_status', 'txt_rubrik', 'txt_schluessel', 'txt_alias'), array($institut_id, '1', $rub_id, 'checkliste', 'checkpoint'), '', '');

			if ( mysqli_num_rows($checkpoint_sql) >= 1 ) {
				$list_item 	= 1;
		?>
		<section class="checkliste bg-<?php echo $bg_color; ?> container-fluid" id="checkliste">
			<div class="row">
				<div class="col-12 col-md-6 col-lg-4">
					<div class="row justify-content-center align-items-center">
						<div class="col-12 col-md-12 py-5 text-light text-center">
								<?php $checklist_row = mysqli_fetch_assoc($checklist_sql); ?>
								<span class="display-4"><?php echo $checklist_row['txt_titel']; ?></span>
								<p><?php echo $checklist_row['txt_einleitung']; ?></p>
								<?php if ($checklist_row['txt_auszug'] == true) { ?>
									<a href="<?php echo $checklist_row['txt_auszug']; ?>" onclick="trackItem(this,'Checkliste', 'Conversion Checkliste');" class="btn btn-outline-light text-uppercase shadow-none" title="<?php echo $checklist_row['txt_beitrag']; ?>" target="<?php echo (strpos($checklist_row['txt_auszug'],'form_') !== false ? '' : '_blank'); ?>"><?php echo $checklist_row['txt_beitrag']; ?></a>
								<?php } ?>
						</div>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-8 py-sm-5 bg-dark">
					<div class="row justify-content-center">
						<div class="col-12 col-md-8 bg-white shadow">
							<ul class="list">
								<li class="list-item list-title h3 text-weight-light text-<?php echo $text_color; ?>">Checkliste</li>
								<?php while ($checkpoint_row = mysqli_fetch_assoc($checkpoint_sql)) { ?>
								<li class="list-item">
									<div class="list-item-container d-flex justify-content-between">
										<div class="list-item-check">
											<span class="oi oi-check"></span>
										</div>
										<div class="list-item-collapse-title flex-grow-1" data-toggle="collapse" data-target="#collapseList<?php echo $list_item; ?>" aria-expanded="true" aria-controls="collapseList<?php echo $list_item; ?>">
											<div class="list-item-title ">
												<span class="list-item-title-strikethrough"></span>
												<?php echo $checkpoint_row['txt_titel']; ?>
											</div>
										</div>
										<button class="list-item-collapse-btn" type="button" data-toggle="collapse" data-target="#collapseList<?php echo $list_item; ?>" aria-expanded="true" aria-controls="collapseList<?php echo $list_item; ?>">
											<span class="list-item-collapse"></span>
										</button>
									</div>

									<div id="collapseList<?php echo $list_item; ?>" class="collapse col-12" aria-labelledby="heading<?php echo $list_item; ?>">
										<div class="card-body text-black-50">
											<?php echo $checkpoint_row['txt_einleitung']; ?>
										</div>
									</div>
								</li>
								<?php $list_item++; ?>
								<?php } ?>

								<?php if ($checklist_row['txt_conversion_titel'] == true) { ?>
									<li class="list-item text-<?php echo $text_color; ?>"><a href="<?php echo $checklist_row['txt_conversion_ziel']; ?>" class="btn btn-outline-<?php echo $text_color; ?> text-uppercase shadow-none" title="<?php echo $checklist_row['txt_conversion_titel']; ?>" target="<?php echo (strpos($checklist_row['txt_conversion_ziel'],'form_') !== false ? '' : '_blank'); ?>"><span class="oi oi-data-transfer-download"></span> <?php echo $checklist_row['txt_conversion_titel']; ?></a></li>
								<?php } ?>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</section>
		<?php } ?>

		<!-- Immobilientool -->
		<?php if (mysqli_num_rows($immotool_page_sql) >= 1) { ?>
		<?php
			include('objektbewertung.php');
		?>
		<?php } ?>

		<!-- Immobilienliste -->
		<?php $immoliste_page_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel', 'txt_alias', 'txt_rubrik', 'txt_status'), array($institut_id, 'immobilienliste', 'gameplay', $rub_id, '1'), '', ''); ?>
		<?php if (mysqli_num_rows($immoliste_page_sql) >= 1) { ?>
		<?php
			include('immobilienliste.php');
		?>
		<?php } ?>
		
		<!-- Investitionsvolumen berechnen -->
		<?php 	$investitionstool_page_sql 	= sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel', 'txt_alias', 'txt_rubrik', 'txt_status'), array($institut_id, 'investitionstool', 'gameplay', $rub_id, '1'), '', ''); 
				$investitionstool_page_row	= mysqli_fetch_assoc($investitionstool_page_sql);
		?>
		<?php if (mysqli_num_rows($investitionstool_page_sql) >= 1) { ?>
			<section class="pb-5 bg-white d-flex flex-column">
				<div class="text-center">
					<h2 class="card-title display-4 font-weight-light mt-2 mb-4"><?php echo $investitionstool_page_row['txt_titel']; ?></h2>
					<p class="card-text mb-5"><?php echo $investitionstool_page_row['txt_einleitung']; ?></p>
				</div>
				<iframe height="600" style="height:600px" src="https://agreetouch.fiducia.de/beraterapp/ebanking/?layout=webcenter&amp;mid=investitionsvolumen" scrolling="no">
					<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
				</iframe>
			</section>
		<?php } ?>

		<!-- Käuferfinder -->
		<?php $kaeuferfinder_sql 	= sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel', 'txt_alias', 'txt_rubrik', 'txt_status'), array($institut_id, 'kaeuferfinder', 'gameplay', $rub_id, '1'), '', ''); 
				$kaeuferfinder_row	= mysqli_fetch_assoc($kaeuferfinder_sql);
		?>
		<?php if (mysqli_num_rows($kaeuferfinder_sql) >= 1) { ?>
			<?php include('kaeuferfinder.php'); ?>
		<?php } ?>

		<!-- Artikel -->
		<section class="bg-white" id="artikel">
			<div class="container">
				<div class="card-deck">
				<?php while ($artikel_row = mysqli_fetch_assoc($artikel_sql)) { ?>
					<div class="card article-card shadow">
						<?php
							$artikel_id 	= $artikel_row['txt_id'];
							$art_img_sql 	= sql_select_where('img_bild, img_titel', 'images', array('img_institut', 'img_rubrik', 'img_schluessel', 'img_item_id', 'img_status'), array($institut_id, $rub_id, 'artikel', $artikel_id, '1'), '', '');
							if ($art_img_row 	= mysqli_fetch_assoc($art_img_sql)) {
						?>
						<img class="card-img-top" src="/<?php echo $art_img_row['img_bild']; ?>" alt="<?php echo $art_img_row['img_titel']; ?>">
						<?php } ?>
						<div class="card-body">
							<span class="card-rubrik text-<?php echo $text_color; ?>"><?php echo $rubrik_row['rub_name']; ?></span>
							<h5 class="card-title mt-2 mb-4"><a href="/artikel.php?art_id=<?php echo $artikel_row['txt_id']; ?>&rub_id=<?php echo $rub_id; ?>" onclick="trackItem(this,'<?php echo $artikel_row['txt_id']; ?>', 'Artikel');" title="Lesen Sie den gesamten Artikel"><?php echo $artikel_row['txt_titel']; ?></a></h5>
							<p class="card-text"><?php echo $artikel_row['txt_auszug']; ?></p>
						</div>
						<div class="card-footer text-right">
							<?php if ($artikel_row['txt_alias'] == 'extern') { ?>
								<a href="<?php echo $artikel_row['txt_conversion_ziel']; ?>" target="_blank" onclick="trackItem(this,'<?php echo $artikel_row['txt_id']; ?>', 'Artikel');" class="btn btn-sm btn-<?php echo $text_color; ?> text-uppercase" title="Lesen Sie den gesamten Artikel"><?php echo $artikel_row['txt_conversion_titel']; ?></a>
							<?php } else { ?>
								<a href="/artikel/<?php echo validString($rubrik_row['rub_name']); ?>/<?php echo $rub_id; ?>/<?php echo validString($artikel_row['txt_titel']); ?>/<?php echo $artikel_row['txt_id']; ?>/" onclick="trackItem(this,'<?php echo $artikel_row['txt_id']; ?>', 'Artikel');" class="btn btn-sm btn-<?php echo $text_color; ?> text-uppercase" title="Lesen Sie den gesamten Artikel">Mehr lesen</a>
							<?php } ?>
						</div>
					</div>
				<?php } ?>
				</div>
			</div>
		</section>

<?php
	include('footer.php');
?>

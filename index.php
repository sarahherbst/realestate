<?php
	$page 		= 'index';
	$ite_rubrik = 'Startseite';

	require('connection.inc.php');
	require('function.inc.php');
	require('data.inc.php');
	include('header.php');
?>

<!-- Headbereich Logo & Teaser -->
<section class="bg-lightblue-gradient pt-5">

	<!-- Teaser < MD (Smartphone & Tablet) -->
	<section class="teaser-container pt-5 pt-sm-3 pb-0 pb-sm-2 d-block d-md-none" id="teaser-swipe-area">
		<div class="teaser-wrapper mt-5 pt-5">
			<div class="teaser-card-container" id="position-container">
				<!-- Teaser -->
				<?php
					$rubrik_sql = sql_select_where('all', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
					$array_category = array();
					while ($rubrik_row = mysqli_fetch_assoc($rubrik_sql)) {
				?>
				<?php array_push($array_category, array($rubrik_row['rub_id'], $rubrik_row['rub_name'])); ?>
				<div class="teaser-perspective">
					<div class="card teaser-card bg-<?php echo $rubrik_row['rub_css_bg']; ?> box-shadow border-0 overflow-hidden py-2 text-center text-md-left">
						<div class="row">
							<div class="col-md-6 z-index-5">
								<div class="card-body">
									<div class="h5 font-weight-light text-light">
										<?php $teaser_sql = sql_select_where('txt_beitrag', 'texte', array('txt_institut', 'txt_rubrik', 'txt_schluessel'), array($institut_id, $rubrik_row['rub_id'], 'teaser'), '', ''); ?>
										<?php $teaser_row = mysqli_fetch_assoc($teaser_sql); ?>
										<?php echo $teaser_row['txt_beitrag']; ?>
									</div>
								</div>
								<!-- Button zur Themenseite -->
								<div class="card-footer border-0 bg-transparent">
									<?php $kaeuferfinder_sql 	= sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel', 'txt_alias', 'txt_rubrik', 'txt_status'), array($institut_id, 'kaeuferfinder', 'gameplay', $rubrik_row['rub_id'], '1'), '', ''); 
											$kaeuferfinder_row	= mysqli_fetch_assoc($kaeuferfinder_sql);
									?>
									<?php if (mysqli_num_rows($kaeuferfinder_sql) >= 1) { ?>
										<div class="d-flex justify-content-center">
											<a href="/rubrik/<?php echo validString($rubrik_row['rub_name']); ?>/<?php echo $rubrik_row['rub_id']; ?>/" onclick="trackMenu(this,'<?php echo $rubrik_row['rub_name']; ?>');" class="btn btn-light text-uppercase text-<?php echo $rubrik_row['rub_css_txt']; ?> mr-2 px-2">
												<?php echo $rubrik_row['rub_name']; ?>
											</a>
											<a href="/rubrik/<?php echo validString($rubrik_row['rub_name']); ?>/<?php echo $rubrik_row['rub_id']; ?>/#kaeuferfinder" onclick="trackMenu(this,'<?php echo $rubrik_row['rub_name']; ?>');" class="btn btn-light text-uppercase text-<?php echo $rubrik_row['rub_css_txt']; ?> mr-2 px-2">
												Käufer&nbsp;finden
											</a>
										</div>
									<?php } else { ?>
										<a href="/rubrik/<?php echo validString($rubrik_row['rub_name']); ?>/<?php echo $rubrik_row['rub_id']; ?>/" onclick="trackMenu(this,'<?php echo $rubrik_row['rub_name']; ?>');" class="btn btn-light text-uppercase text-<?php echo $rubrik_row['rub_css_txt']; ?>">
											<?php echo $rubrik_row['rub_name']; ?>
										</a>
									<?php } ?>
								</div>
							</div>
							<!-- Teaserbild -->
							<?php $teaser_img_sql = sql_select_where('img_bild', 'images', array('img_institut', 'img_rubrik', 'img_schluessel'), array($institut_id, $rubrik_row['rub_id'], 'teaser-cutout'), '', ''); ?>
							<?php $teaser_img_row = mysqli_fetch_assoc($teaser_img_sql); ?>
							<div class="col-md-6 z-index-5 order-first order-md-last">
								<img src="<?php echo $teaser_img_row['img_bild']; ?>" class="<?php echo $teaser_img_row['img_beschreibung']; ?>" alt="<?php echo $rubrik_row['rub_name']; ?>">
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
				<!-- ENDE Teaser -->
			</div>
			<div id="swipeArea"></div>
		</div>
	</section>

	<!-- Teaser > SM (größere Tablets und Desktop)-->
	<div class="container pt-5 pb-4 px-md-0 d-none d-md-block">
		<div class="card-deck-teaser mt-5 pt-5">
			<!-- Teaser -->
			<?php
				$rubrik_sql = sql_select_where('all', 'rubrik', array('rub_institut', 'rub_status'), array($institut_id, '1'), '', '');
				while ($rubrik_row = mysqli_fetch_assoc($rubrik_sql)) {
			?>
			<div class="card teaser-card bg-<?php echo $rubrik_row['rub_css_bg']; ?> box-shadow border-0 overflow-hidden py-2 text-center text-lg-left">
				<div class="row">
					<div class="col-lg-6 z-index-5">
						<div class="card-body">
							<div class="h5 font-weight-light text-light">
								<?php $teaser_sql = sql_select_where('txt_beitrag', 'texte', array('txt_institut', 'txt_rubrik', 'txt_schluessel'), array($institut_id, $rubrik_row['rub_id'], 'teaser'), '', ''); ?>
								<?php $teaser_row = mysqli_fetch_assoc($teaser_sql); ?>
								<?php echo $teaser_row['txt_beitrag']; ?>
							</div>
						</div>
						<!-- Button zur Themenseite -->
						<div class="card-footer border-0 bg-transparent">
							<?php $kaeuferfinder_sql 	= sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel', 'txt_alias', 'txt_rubrik', 'txt_status'), array($institut_id, 'kaeuferfinder', 'gameplay', $rubrik_row['rub_id'], '1'), '', ''); 
											$kaeuferfinder_row	= mysqli_fetch_assoc($kaeuferfinder_sql);
							?>
							<?php if (mysqli_num_rows($kaeuferfinder_sql) >= 1) { ?>
								<div class="d-flex">
									<a href="/rubrik/<?php echo validString($rubrik_row['rub_name']); ?>/<?php echo $rubrik_row['rub_id']; ?>/" onclick="trackMenu(this,'<?php echo $rubrik_row['rub_name']; ?>');" class="btn btn-light text-uppercase text-<?php echo $rubrik_row['rub_css_txt']; ?> mr-2">
										<?php echo $rubrik_row['rub_name']; ?>
									</a>
									<a href="/rubrik/<?php echo validString($rubrik_row['rub_name']); ?>/<?php echo $rubrik_row['rub_id']; ?>/#kaeuferfinder" onclick="trackMenu(this,'<?php echo $rubrik_row['rub_name']; ?>');" class="btn btn-light text-uppercase text-<?php echo $rubrik_row['rub_css_txt']; ?> mr-2">
										Käufer&nbsp;finden
									</a>
								</div>
							<?php } else { ?>
								<a href="/rubrik/<?php echo validString($rubrik_row['rub_name']); ?>/<?php echo $rubrik_row['rub_id']; ?>/" onclick="trackMenu(this,'<?php echo $rubrik_row['rub_name']; ?>');" class="btn btn-light text-uppercase text-<?php echo $rubrik_row['rub_css_txt']; ?>">
									<?php echo $rubrik_row['rub_name']; ?>
								</a>
							<?php } ?>
						</div>
					</div>
					<!-- Teaserbild -->
					<?php $teaser_img_sql = sql_select_where('img_bild', 'images', array('img_institut', 'img_rubrik', 'img_schluessel'), array($institut_id, $rubrik_row['rub_id'], 'teaser-cutout'), '', ''); ?>
					<?php $teaser_img_row = mysqli_fetch_assoc($teaser_img_sql); ?>
					<div class="col-lg-6 z-index-5 order-first order-lg-last">
						<img src="<?php echo $teaser_img_row['img_bild']; ?>" class="<?php echo $teaser_img_row['img_beschreibung']; ?>" alt="<?php echo $rubrik_row['rub_name']; ?>">
					</div>
				</div>
			</div>
			<?php } ?>
			<!-- ENDE Teaser -->
		</div>
	</div>
</section>

<!-- Testimonials -->
<?php 
	$testimonial_sql = mysqli_query($db, "SELECT * FROM texte LEFT JOIN images ON texte.txt_id = images.img_item_id WHERE txt_status = 1 AND txt_schluessel = 'testimonial'  AND img_schluessel = 'testimonial' ORDER BY RAND() LIMIT 1"); 

	$testimonial_row = mysqli_fetch_assoc($testimonial_sql);

	if ( mysqli_num_rows($testimonial_sql) == true && mysqli_num_rows($testimonial_sql) >= 1 ) { ?>
		<section class="bg-white" id="testimonial">
			<div class="container-fluid">
				<div class="row bg-white">
					<div class="col-12 col-lg-6 col-img d-flex align-content-center justify-content-center flex-wrap">
						<div class="col-img-frame box-shadow box-shadow-md-0">
							<img src="<?php echo $testimonial_row['img_bild']; ?>" alt="<?php echo $testimonial_row['img_beschreibung']; ?>">
						</div>
					</div>
					<div class="col-12 col-lg-6 align-self-lg-center p-5 text-center text-md-left">
						<h5 class="text-primary text-weight-light"><?php echo $testimonial_row['txt_titel']; ?></h5>
						<p><?php echo $testimonial_row['txt_auszug']; ?></p>
					</div>
				</div>
			</div>
		</section>
<?php }	?>

<!-- Auszeichnungen-->
<?php $award_sql = sql_select_where('all', 'texte', array('txt_schluessel', 'txt_status'), array('award', '1'), '', ''); ?>
<?php if ( mysqli_num_rows($award_sql) == true && mysqli_num_rows($award_sql) >= 1 ) { ?>
	<?php $award_row = mysqli_fetch_assoc($award_sql); ?>
	<section>
		<div class="container-fluid mt-5 mb-5">
			<div class="row">
				<div class="col-sm-12 col-lg-6 d-flex align-content-center flex-wrap text-lg-left mb-4 mb-lg-0">
					<h2 class="font-weight-light text-primary text-center w-100"><?php echo $award_row['txt_titel'] ?></h2>
				</div>
				<div class="col-sm-12 col-lg-6 mb-5 mb-md-0 pl-5 text-center">
					<div class="row row-auszeichnung ml-0">
						<?php $img_sql = sql_select_where('all', 'images', array('img_schluessel', 'img_status'), array('award', '1'), '', ''); ?>
						<?php while ($img_row = mysqli_fetch_assoc($img_sql)) { ?>
							<div class="auszeichnung col-3 col-sm-1">
								<?php if(!$img_row['img_url'] == '') : echo '<a href="'.$img_row['img_url'].'" target="_blank">'; endif; ?><img src="/<?php echo $img_row['img_bild'] ?>" class="img-fluid rounded box-shadow" alt="<?php echo $img_row['img_titel'] ?>"><?php if(!$img_row['img_url'] == '') : echo '</a>'; endif; ?>
							</div>
						<?php } ?>
						<div class="col-6 col-sm-4 ml-4 px-0 mt-4 mt-sm-2 mt-md-0 overflow-hidden iframe-badge"><iframe style="overflow: hidden;" src="https://widget.immobilienscout24.de/anbieter/bewertung/07a646325288bf7395b747bff9a6fec3" frameborder="0" marginwidth="0" marginheight="0"></iframe></div>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php }	?>

<?php
	include('footer.php');
?>
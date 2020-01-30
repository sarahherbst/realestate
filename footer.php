		<!-- Berater -->
		<?php $stm_sql = sql_select_where('all', 'statements', 'stm_status', '1', '', ''); ?>
		<?php if (mysqli_num_rows($stm_sql) >= 1) { ?>
		<section class="mb-0 mb-sm-5 pt-5 px-0 px-md-5 position-relative">
			<div class="container pb-5 mb-0 mb-sm-5 px-0 overflow-hidden">
				<div id="carousel-consultant" class="carousel-consultant-wrap carousel slide" data-ride="carousel" data-interval="3000">
					<div class="carousel-inner row w-100 mx-auto  justify-content-center" role="listbox">
						<?php $stm_num = 1; ?>
						<?php while ($stm_row = mysqli_fetch_assoc($stm_sql)) { ?>
							<div class="carousel-item col-12 col-md-8 col-lg-6 <?php echo ($stm_num == 1 ? 'active' : ''); ?>">
								<div class="consultantcard mx-2">
									<div class="row justify-content-center">
										<?php $profilbild_sql = sql_select_where('all', 'images', array('img_schluessel', 'img_item_id'), array('statement', $stm_row['stm_id']), '', ''); ?>
										<div class="<?php echo (mysqli_num_rows($profilbild_sql) >= 1 ? 'consultantcard-wrap' : 'consultantcard-text-wrap'); ?> col-12 bg-lightblue-gradient">
											<div class="consultantcard-container <?php echo (mysqli_num_rows($profilbild_sql) >= 1 ? '' : 'justify-content-center'); ?>">
												<?php
													if (mysqli_num_rows($profilbild_sql) >= 1) {
														$profilbild_row = mysqli_fetch_assoc($profilbild_sql);
												?>
												<div class="consultantcard-img" style="background-image:url('/<?php echo $profilbild_row['img_bild']; ?>')"></div>
												<?php } ?>
												<div class="consultantcard-body text-light <?php echo (mysqli_num_rows($profilbild_sql) >= 1 ? '' : 'justify-content-center'); ?>">
													<h3 class="<?php echo (mysqli_num_rows($profilbild_sql) >= 1 ? '' : 'text-center'); ?> consultantcard-position"><?php echo $stm_row['stm_title']; ?></h3>
													<p class="<?php echo (mysqli_num_rows($profilbild_sql) >= 1 ? '' : 'text-center'); ?> consultantcard-text"><?php echo $stm_row['stm_text']; ?></p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php $stm_num++; ?>
						<?php } ?>
					</div>
				</div>
			</div>
			<a class="carousel-control-prev carousel-consultant-prev" href="#carousel-consultant" role="button" data-slide="prev">
				<span class="carousel-control-prev-icon" aria-hidden="true"></span>
				<span class="sr-only">Zurück</span>
			</a>
			<a class="carousel-control-next carousel-consultant-next" href="#carousel-consultant" role="button" data-slide="next">
				<span class="carousel-control-next-icon" aria-hidden="true"></span>
				<span class="sr-only">vorwärts</span>
			</a>
		</section>
		<?php } ?>

		<?php $immotool_page_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel', 'txt_alias', 'txt_rubrik', 'txt_status'), array($institut_id, 'objektbewertung', 'gameplay', $rub_id, '1'), '', ''); ?>
		<?php if (mysqli_num_rows($immotool_page_sql) >= 1) { ?>
			<div class="modal fade" id="modalImmobilienbewertung" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-dialog-centered modal-md border-0" role="document">
					<div class="modal-content border-0 shadow-lg">
						<div class="modal-header border-0">
							<button type="button" class="btn btn-orange" data-dismiss="modal" aria-label="Schließen" id="modal-close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body border-0 text-center">
							<div class="icon-tag">
							</div>
							<h2 class="modal-title font-weight-light"></h2>
							<div class="modal-text">
								<div class="row">
									<div class="col-12">
										<div class="spinner"></div>
										<h3 class="font-weight-light text-center text-primary mb-3">Einen Moment ...</h3>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer border-0">
							<button type="button" class="btn btn-secondary" data-dismiss="modal">Zurück zur Webseite</button>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
		
		<?php if ($page == 'immobilienobjekt' || $page == 'immobilienobjekt2') { ?>
			<div class="modal fade" id="immo_kontakformular" tabindex="-1" role="dialog" aria-labelledby="immo_kontaktformularLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header bg-blue-gradient text-light">
							<div>
								<small>Kontakt zum Objekt <?php echo $objekt_row['obj_objektnr_extern']; ?></small>
								<h5 class="modal-title" id="immo_kontaktformularLabel"><?php echo $objekt_row['obj_titel']; ?></h5>
							</div>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body" id="immobilienobjekt_form_body">
							<form action="" method="post" id="immobilienobjekt_form">
								<div class="row">
									<div class="col-12">
										<h5 class="font-weight-light text-<?php echo $text_color; ?> mb-3">Kontaktdaten</h5>
									</div>
								</div>

								<!-- Create fields for the honeypot -->
								<label class="nono" for="kd_firstname">Firstname</label>
								<input name="kd_firstname" type="text" id="kd_firstname" class="nono" autocomplete="off" placeholder="Firstname">
								<!-- honeypot fields end -->

								<!-- Vor- & Nachname -->
								<div class="form-row">
									<div class="form-group col-sm-6">
										<label class="sr-only" for="kd_vorname">Vorname</label>
										<input type="text" class="form-control" name="kd_vorname" id="kd_vorname" value="" title="Bitte geben Sie Ihren Vornamen an" autocomplete="given-name" placeholder="Vorname" required>
									</div>
									<div class="form-group col-sm-6">
										<label class="sr-only" for="kd_nachname">Nachname</label>
										<input type="text" class="form-control" name="kd_nachname" id="kd_nachname" value="" title="Bitte geben Sie Ihren Nachnamen an" autocomplete="gfamily-name" placeholder="Nachname" required>
									</div>
								</div>

								<!-- E-Mail -->
								<div class="form-row">
									<div class="form-group col-sm-12">
										<label class="sr-only" for="kd_email">E-Mail</label>
										<input type="email" class="form-control" name="kd_email" id="kd_email" value="" placeholder="E-Mail" autocomplete="email" required>
									</div>
								</div>

								<!-- Telefon -->
								<div class="form-row">
									<div class="form-group col-sm-12">
										<label class="sr-only" for="kd_tel">Telefon</label>
										<input type="tel" class="form-control" name="kd_tel" id="kd_tel" value="" title="Bitte geben Sie Ihre Telefonnummer an" autocomplete="tel" placeholder="Telefonnummer (optional)">
									</div>
								</div>

								<!-- Bemerkungen -->
								<div class="form-row">
									<div class="form-group col-12">
										<label for="kd_bemerkung" class="sr-only">Bemerkungen</label>
										<textarea class="form-control" rows="3" id="kd_bemerkung" name="kd_bemerkung" placeholder="Hier können Sie optional weitere Informationen verfassen."></textarea>
									</div>
								</div>
								
								<!-- Datenschutz -->
								<div class="form-row mb-3">
									<div class="form-group col-12">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" name="kd_datenschutz" id="kd_datenschutz" value="Datenschutzbestimmungen"> 
											<label class="custom-control-label" for="kd_datenschutz">
												<small>Ich habe die <a href="<?php echo $microsite_url; ?>/datenschutz.php" onclick="trackMenu(this,'Datenschutz');" target="_blank">Datenschutzbestimmungen</a> gelesen und akzeptiere diese.</small>
											</label>
										</div>
									</div>
								</div>

								<input type="hidden" id="obj_id" name="obj_id" value="<?php echo $obj_id; ?>">
								<input type="hidden" id="obj_objektnr_extern" name="obj_objektnr_extern" value="<?php echo $objekt_row['obj_objektnr_extern']; ?>">
								<input type="hidden" id="obj_titel" name="obj_titel" value="<?php echo $objekt_row['obj_titel']; ?>">
								<input type="hidden" id="obj_art" name="obj_art" value="<?php echo $objekt_row['obj_art']; ?>">
								<input type="hidden" id="obj_unterart" name="obj_unterart" value="<?php echo $objekt_row['obj_unterart']; ?>">
								<input type="hidden" id="obj_vermarktungsart" name="obj_vermarktungsart" value="<?php echo $objekt_row['obj_vermarktungsart']; ?>">

								<!-- Absenden -->
								<div class="form-row">
									<div class="form-group col-sm-12 text-right">
										<button type="submit" class="btn btn-<?php echo $text_color; ?>" name="sendform" id="sendform" value="Jetzt absenden!">Jetzt absenden!</button>
									</div>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>
		
		<?php if (mysqli_num_rows($kaeuferfinder_sql) >= 1) { ?>
			<div class="modal fade" id="kaeuferfinderModal" tabindex="-1" role="dialog" aria-labelledby="kaeuferfinderModalLabel" aria-hidden="true">
				<div class="modal-dialog modal-dialog-centered" role="document">
					<div class="modal-content">
						<div class="modal-header bg-blue-gradient text-light">
							<div>
								<small>Kontakt aufnehmen</small>
								<h5 class="modal-title" id="kaeuferfinderModalLabel"></h5>
							</div>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
								<span aria-hidden="true">&times;</span>
							</button>
						</div>
						<div class="modal-body">
							<form action="" method="post" id="kaeuferfinder-kontakt">

								<!-- Vor- & Nachname -->
								<div class="form-row">
									<div class="form-group col-sm-6">
										<label class="sr-only" for="kf_vorname">Vorname</label>
										<input type="text" class="form-control" name="kf_vorname" id="kf_vorname" value="" title="Bitte geben Sie Ihren Vornamen an" autocomplete="given-name" placeholder="Vorname" required>
									</div>
									<div class="form-group col-sm-6">
										<label class="sr-only" for="kf_nachname">Nachname</label>
										<input type="text" class="form-control" name="kf_nachname" id="kf_nachname" value="" title="Bitte geben Sie Ihren Nachnamen an" autocomplete="gfamily-name" placeholder="Nachname" required>
									</div>
								</div>

								<!-- E-Mail -->
								<div class="form-row">
									<div class="form-group col-sm-12">
										<label class="sr-only" for="kf_email">E-Mail</label>
										<input type="email" class="form-control" name="kf_email" id="kf_email" value="" placeholder="E-Mail" autocomplete="email" required>
									</div>
								</div>

								<!-- Telefon -->
								<div class="form-row">
									<div class="form-group col-sm-12">
										<label class="sr-only" for="kf_tel">Telefon</label>
										<input type="tel" class="form-control" name="kf_tel" id="kf_tel" value="" title="Bitte geben Sie Ihre Telefonnummer an" autocomplete="tel" placeholder="Telefonnummer (optional)">
									</div>
								</div>

								<!-- Bemerkungen -->
								<div class="form-row">
									<div class="form-group col-12">
										<label for="kf_bemerkung" class="sr-only">Bemerkungen</label>
										<textarea class="form-control" rows="3" id="kf_bemerkung" name="kf_bemerkung" placeholder="Hier können Sie optional weitere Bemerkungen verfassen."></textarea>
									</div>
								</div>
								
								<!-- Datenschutz -->
								<div class="form-row mb-3">
									<div class="form-group col-12">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" name="kf_datenschutz" id="kf_datenschutz" value="Datenschutzbestimmungen" required> 
											<label class="custom-control-label" for="kf_datenschutz">
												<small>Ich habe die <a href="<?php echo $microsite_url; ?>/datenschutz.php" onclick="trackMenu(this,'Datenschutz');" target="_blank">Datenschutzbestimmungen</a> gelesen und akzeptiere diese.</small>
											</label>
										</div>
									</div>
								</div>

								<input type="hidden" id="kf_id" name="kf_id" value="">

								<!-- Absenden -->
								<div class="form-row">
									<div class="form-group col-sm-12 text-right">
										<button type="button" class="btn btn-<?php echo $text_color; ?>" name="kf_send" id="kf_send" value="Jetzt absenden!">Jetzt absenden!</button>
									</div>
								</div>
							</form>
							<div class="modal-text"></div>
						</div>
					</div>
				</div>
			</div>
		<?php } ?>

		<div class="modal fade" id="modalRechtlicheHinweise" tabindex="-1" role="dialog" aria-labelledby="modalRechtlicheHinweiseTitle" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header border-0">
						<h5 class="modal-title" id="modalRechtlicheHinweiseTitle">Rechtliche Hinweise</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body border-0">
						<a href="/files/Widerrufsbelehrung_mit_Erkaerung_als_Anlage_zum_Maklerauftrag.pdf" target="_blank" title="Widerrufsbelehrung">Widerrufsbelehrung</a>
					</div>
					<div class="modal-footer border-0">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">schließen</button>
					</div>
				</div>
			</div>
		</div>

		<footer>
			<ul class="nav justify-content-center my-3">
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $microsite_url; ?>/impressum.php" onclick="trackMenu(this,'Impressum');">Impressum</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $microsite_url; ?>/datenschutz.php" onclick="trackMenu(this,'Datenschutz');">Datenschutz</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $microsite_url; ?>/kontakt.php">Kontakt</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="" data-toggle="modal" data-target="#modalRechtlicheHinweise">Rechtliche Hinweise</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $microsite_url; ?>/karriere.php">Karriere</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" href="<?php echo $fb_url; ?>" target="_blank">
						<img src="/img/facebook-logo.svg" alt="Facebookseite" title="Facebookseite" height="22" style="margin-top: -5px;">
					</a>
				</li>
			</ul>
		</footer>

		<script type="text/javascript" src="/js/jquery-3.3.1.min.js" defer></script>
		<script type="text/javascript" src="/js/jquery.touchSwipe.min.js" defer></script>
		<script type="text/javascript" src="/js/bootstrap.bundle.min.js" defer></script>
		<script type="text/javascript" src="/js/scripts.js" defer></script>
		<script type="text/javascript" src="https://76360033.flowfact-webparts.netjs/iframeResizer.js" defer></script>
		<script type="text/javascript" src="/js/iframe.js" defer></script>

		<?php if ($page == 'index') { ?>
		<script src='https://hammerjs.github.io/dist/hammer.js' defer></script>
		<script type="text/javascript" src="/js/teaser.js" defer></script>
		<?php } ?>

		<?php include('cookieconsent.php'); ?>

		<?php if (mysqli_num_rows($immotool_page_sql) >= 1) { 
			// Objektbewertungstool
			include 'js/objektbewertung_script.php';
		} ?>

		<?php if ($page == 'immobilienobjekt') {
			// Immobilientool
			include 'js/immobilienobjekt_script.php';
		} ?>

		<?php if (mysqli_num_rows($kaeuferfinder_sql) >= 1) { ?>
			<!-- Käuferfinder -->
			<script type="text/javascript" src="/js/kaeuferfinder_script.js" defer></script>
		<?php } ?>

		<script type="text/javascript">
			var ite_rubrik = "<?php echo $ite_rubrik; ?>";
			function trackItem(obj,ite_position,ite_name) {
				var ite_url = $(obj).attr('href');

				$.ajax({
					async: false,
					url: "insert_item_tracking.php",
					type: "POST",
					data: {'ite_name' : ite_name, 'ite_rubrik' : ite_rubrik, 'ite_position' : ite_position, 'ite_url' : ite_url},
					error: function(req, err){ console.log('TrackItem konnte nicht ausgeführt werden.'); }
				});

			};

			function trackMenu(obj,men_name) {
				var men_url = $(obj).attr('href');

				$.ajax({
					async: false,
					url: "insert_menu_tracking.php",
					type: "POST",
					data: {'men_seite' : men_name, 'men_url' : men_url},
					error: function(req, err){ console.log('TrackMenu konnte nicht ausgeführt werden.'); }
				});
			};
		</script>
	</body>
</html>

<?php
	// DB-Connection schließen:
	mysqli_close($db);
?>
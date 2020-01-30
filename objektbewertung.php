<section class="hausbewertungstool pt-5 p-0 p-md-5" id="hausbewertungstool">
	<div class="container">
		<div class="row align-items-xl-center">
			<div class="col-12 p-3 text-center">
				<div class="card-body">
					<?php
						$schluessel 	= 'objektbewertung';
						$objektbewertung_sql 	= sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel'), array($institut_id, $schluessel), '', '');
						$objektbewertung_row 	= mysqli_fetch_assoc($objektbewertung_sql);
					?>
					<h2 class="card-title display-4 font-weight-light mt-2 mb-4"><?php echo $objektbewertung_row['txt_titel']; ?></h2>
					<p class="card-text"><?php echo $objektbewertung_row['txt_einleitung']; ?></p>
				</div>
			</div>
		</div>
		<div class="row">
			<!-- Bewertungstool -->
			<div class="col-12 bg-white shadow py-5">
				<div class="row justify-content-center">
					<div class="col-12">
						<div class="alert text-left">
							<div class="processsteps">
								<div class="processsteps-row">
									<div class="processsteps-step">
										<button type="button" class="p-0 btn btn-xl btn-primary  btn-circle" disabled="disabled">1</button>
									</div>
									<div class="processsteps-step">
										<button type="button" class="p-0 btn btn-xl btn-circle" disabled="disabled">2</button>
									</div>
									<div class="processsteps-step">
										<button type="button" class="p-0 btn btn-xl btn-circle" disabled="disabled">3</button>
									</div>
									<div class="processsteps-step">
										<button type="button" class="p-0 btn btn-xl btn-circle" disabled="disabled">4</button>
									</div>
								</div>
							</div>
						</div>
						<div class="card-body">
							<form action="" method="POST" id="immobewertung" novalidate>
								<!-- SECTION 1 -->
								<div id="step-1">
									<h5 class="card-title text-center">Art der Immobilie</h5>
									<div class="form-row align-items-center mt-4">
										<div class="col text-center my-2">
											<div class="custom-control custom-radio img-checkbox">
												<input type="radio" class="custom-control-input" value="Einfamilienhaus" name="im_immotyp" id="einfamilienhaus" required>
												<label class="custom-control-label text-center" for="einfamilienhaus">Einfamilienhaus</label>
											</div>
										</div>
										<div class="col text-center my-2">
											<div class="custom-control custom-radio img-checkbox">
												<input type="radio" class="custom-control-input" value="Wohnung" name="im_immotyp" id="wohnung" required>
												<label class="custom-control-label text-center" for="wohnung">Wohnung</label>
											</div>
										</div>
										<div class="col text-center my-2">
											<div class="custom-control custom-radio img-checkbox">
												<input type="radio" class="custom-control-input" value="Mehrfamilienhaus" name="im_immotyp" id="mehrfamilienhaus" required>
												<label class="custom-control-label text-center" for="mehrfamilienhaus">Mehrfamilienhaus</label>
											</div>
										</div>
									</div>
									<div id="showhaustyp">
									</div>
								</div>

								<!-- SECTION 2 -->
								<div id="step-2" class="d-none">
									<h5 class="card-title text-center">Flächenangaben</h5>
									<p class="text-center w-100 mb-2">Bitte geben Sie alle Flächen in m², ohne Nachkommastellen und ohne Leer- oder Trennzeichen an.</p>
									<div class="form-row align-items-center mt-4 mb-3">
										<div class="form-group col-sm-12">
											<label class="sr-only" for="im_wohnflaeche">Wohnfläche</label>
											<input type="number" step="1" class="form-control" name="im_wohnflaeche" id="im_wohnflaeche" value="<?php echo $im_wohnflaeche; ?>" title="Bitte geben Sie die Wohnfläche an." placeholder="Wohnfläche" min="1" max="10000" maxlength="5" required>
										</div>
										<div class="form-group col-sm-12">
											<label class="sr-only" for="im_grundstuecksflaeche">Grundstücksfläche</label>
											<input type="number" step="1" class="form-control" name="im_grundstuecksflaeche" id="im_grundstuecksflaeche" value="<?php echo $im_grundstuecksflaeche; ?>" title="Bitte geben Sie die Grundstücksfläche an." placeholder="Grundstücksfläche" min="1" max="10000" required>
										</div>
									</div>
									<h5 class="card-title text-center">Baujahr</h5>
									<p class="text-center w-100 mb-2">Bitte geben Sie ein Baujahr ab 1800 an.</p>
									<div class="form-row align-items-center mt-3">
										<div class="form-group col-sm-12">
											<label class="sr-only" for="im_baujahr">Baujahr</label>
											<input type="number" step="1" class="form-control" name="im_baujahr" id="im_baujahr" value="<?php echo $im_baujahr; ?>" title="Bitte geben Sie das Baujahr an." placeholder="Baujahr" min="1800" max="<?php echo date('Y'); ?>" required>
										</div>
									</div>
								</div>

								<!-- SECTION 3 -->
								<div id="step-3" class="d-none">
									<h5 class="card-title text-center">Adresse der Immobilie</h5>
									<div class="form-row align-items-center mt-4">
										<div class="form-group col-12">
											<label class="sr-only" for="im_strasse">Straße, Hausnr.</label>
											<input type="text" name="im_strasse" id="im_strasse" class="form-control" placeholder="Straße, Hausnr." value="" pattern=".*([a-zA-Z]){1,}.*([a-zA-Z]){1,}.*" required>
										</div>
									</div>
									<div class="form-row align-items-center">
										<div class="form-group col-sm-4">
											<label class="sr-only" for="im_plz">PLZ</label>
											<input type="number" name="im_plz" id="im_plz" class="form-control" placeholder="PLZ" value="" min="01001" max="99999" required>
										</div>
										<div class="form-group col-sm-8">
											<label class="sr-only" for="im_ort">Ort</label>
											<input type="text" name="im_ort" id="im_ort" class="form-control" placeholder="Ort" value="" pattern=".*([A-Za-z_äÄöÖüÜß-]){2,}.*" required>
										</div>
									</div>
								</div>

								<!-- SECTION 4 -->
								<div id="step-4" class="d-none">
									<h5 class="card-title text-center" id="responsetitle">Kontakt</h5>
									<p class="text-center w-100 mb-2" id="alert">Um Ihnen die Ergebnisse unserer Analyse zukommen zu lassen, benötigen wir Ihre Kontaktdaten.</p>
									<div class="form-row align-items-center mt-4">
										<div class="form-group col-sm-6">
											<label class="sr-only" for="im_vorname">Vorname</label>
											<input type="text" class="form-control" name="im_vorname" id="im_vorname" value="" title="Bitte geben Sie Ihren Vornamen an" autocomplete="given-name" placeholder="Vorname" required>
										</div>
										<div class="form-group col-sm-6">
											<label class="sr-only" for="im_nachname">Nachname</label>
											<input type="text" class="form-control" name="im_nachname" id="im_nachname" value="" title="Bitte geben Sie Ihren Nachnamen an" autocomplete="gfamily-name" placeholder="Nachname" required>
										</div>
									</div>
									<div class="form-row align-items-center">
										<div class="form-group col-sm-6">
											<label class="sr-only" for="im_email">E-Mail</label>
											<input type="email" class="form-control" name="im_email" id="im_email" value="" placeholder="E-Mail" autocomplete="email" required>
										</div>
										<div class="form-group col-sm-6">
											<label class="sr-only" for="im_tel">Telefon (optional)</label>
											<input type="tel" class="form-control" name="im_tel" id="im_tel" value="" title="Bitte geben Sie Ihre Telefonnummer an" pattern=".*[0-9\-\+\s\(\)].*" autocomplete="tel" placeholder="Telefonnummer (optional)">
										</div>
									</div>
									<div class="form-row align-items-center">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" name="im_kontakt" id="im_kontakt" value="Kontakt"> 
											<label class="custom-control-label" for="im_kontakt">
												<small>Ich möchte zu möglichen Angeboten von der <?php echo $institut; ?> kontaktiert werden.</small>
											</label>
										</div>
									</div>
									<div class="form-row align-items-center">
										<div class="custom-control custom-checkbox">
											<input type="checkbox" class="custom-control-input" name="im_datenschutz" id="im_datenschutz" value="Datenschutzbestimmungen" required> 
											<label class="custom-control-label" for="im_datenschutz">
												<small>Ich habe die <a href="<?php echo $institut_url; ?>/datenschutz.php" onclick="trackMenu(this,'Datenschutz');" target="_blank">Datenschutzbestimmungen</a> gelesen und akzeptiere diese.</small>
											</label>
										</div>
									</div>
								</div>
							</form>
						</div>
						<div class="card-footer d-flex justify-content-between pb-0 mt-3">
							<button type="submit" class="btn btn-sm btn-primary text-uppercase disabled" title="Zurück" name="im_zurueck" id="im_zurueck">Zurück</button>
							<button type="submit" class="btn btn-sm btn-primary text-uppercase" title="Weiter" name="im_weiter" id="im_weiter">Weiter</button>
						</div>
					</div>
				</div>

				<div class="overlay d-none">
					<div class="row">
						<div class="col-12">
							<div class="spinner"></div>
							<h3 class="font-weight-light text-center text-primary mb-3">Daten werden berechnet ...</h3>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="kaueferfinder bg-white pt-2 pb-5" id="kaeuferfinder">
	<div class="container">
		<div class="row w-100 mx-0">
			<!-- Bewertungstool -->
			<div class="col-12 bg-white shadow py-3">
				<div class="row justify-content-center">
					<div class="col-12">
						<div class="card-body">
							<form action="" method="POST" novalidate id="kf-search" class="position-relative mx-md-5">
								<div>
									<h2 class="card-title mb-4 text-center"><?php echo $kaeuferfinder_row['txt_titel']; ?></h2>
									<p class="card-text text-center px-md-5"><?php echo $kaeuferfinder_row['txt_einleitung']; ?></p>
								</div>

								<div class="form-row align-items-center mt-5">
									<div class="col-12 col-md-6">
										<div class="form-group">
											<label class="sr-only" for="kf_objektart">Objektart</label>
											<select class="form-control" name="kf_objektart" id="kf_objektart" title="Bitte geben Sie die Objektart an." placeholder="Objektart" required>
												<option value="">Objektart</option>
												<option value="Wohnung">Wohnung</option>
												<option value="Haus">Haus</option>
												<option value="Grundstück">Grundstück</option>
												<option value="Anlageobjekt">Anlageobjekt</option>
											</select>
										</div>
									</div>
									<div class="col-12 col-md-6">
										<div class="form-group">
											<label class="sr-only" for="kf_ort">Ort</label>
											<select class="form-control" name="kf_ort" id="kf_ort" title="Bitte geben Sie den Ort des Objekts an." placeholder="Ort" required>
												<option value="">Ort</option>
													<option value="" disabled></option>

													<option value="Ettlingen" class="text-primary font-weight-bold">Ettlingen</option>
													<option value="" disabled></option>

													<option value="3" class="text-primary font-weight-bold">Karlsruhe, Stadt</option>
													<!-- <option value="Beiertheim-Bulach">Beiertheim-Bulach</option>
													<option value="Bergwald">Bergwald</option>
													<option value="Daxlanden">Daxlanden</option>
													<option value="Durlach">Durlach</option>
													<option value="Grötzingen">Grötzingen</option>
													<option value="Grünwettersbach">Grünwettersbach</option>
													<option value="Grünwinkel">Grünwinkel</option>
													<option value="Hagsfeld">Hagsfeld</option>
													<option value="Hohenwettersbach">Hohenwettersbach</option>
													<option value="Innenstadt">Innenstadt</option>
													<option value="Innenstadt-Ost">Innenstadt-Ost</option>
													<option value="Innenstadt-West">Innenstadt-West</option>
													<option value="Knielingen">Knielingen</option>
													<option value="Maxau">Maxau</option>
													<option value="Mühlburg">Mühlburg</option>
													<option value="Neureut">Neureut</option>
													<option value="Nordstadt">Nordstadt</option>
													<option value="Nordweststadt">Nordweststadt</option>
													<option value="Oberreut">Oberreut</option>
													<option value="Oststadt">Oststadt</option>
													<option value="Palmbach">Palmbach</option>
													<option value="Rintheim">Rintheim</option>
													<option value="Rüppurr">Rüppurr</option>
													<option value="Stupferich">Stupferich</option>
													<option value="Südstadt">Südstadt</option>
													<option value="Südweststadt">Südweststadt</option>
													<option value="Waldstadt">Waldstadt</option>
													<option value="Weiherfeld-Dammerstock">Weiherfeld-Dammerstock</option>
													<option value="Weststadt">Weststadt</option>
													<option value="Wolfartsweier">Wolfartsweier</option> -->
													<option value="" disabled></option>

													<option value="2" class="text-primary font-weight-bold">Landkreis Karlsruhe</option>
													<option value="Bad Schönborn">Bad Schönborn</option>
													<option value="Bretten">Bretten</option>
													<option value="Bruchsal">Bruchsal</option>
													<option value="Dettenheim">Dettenheim</option>
													<option value="Eggenstein-Leopoldshafen">Eggenstein-Leopoldshafen</option>
													<option value="Ettlingen">Ettlingen</option>
													<option value="Forst">Forst</option>
													<option value="Gondelsheim">Gondelsheim</option>
													<option value="Graben-Neudorf">Graben-Neudorf</option>
													<option value="Hambrücken">Hambrücken</option>
													<option value="Karlsbad">Karlsbad</option>
													<option value="Karlsdorf-Neuthard">Karlsdorf-Neuthard</option>
													<option value="Kraichtal">Kraichtal</option>
													<option value="Kronau">Kronau</option>
													<option value="Kürnbach">Kürnbach</option>
													<option value="Linkenheim-Hochstetten">Linkenheim-Hochstetten</option>
													<option value="Malsch">Malsch</option>
													<option value="Marxzell">Marxzell</option>
													<option value="Oberderdingen">Oberderdingen</option>
													<option value="Oberhausen-Rheinhausen">Oberhausen-Rheinhausen</option>
													<option value="Östringen">Östringen</option>
													<option value="Pfinztal">Pfinztal</option>
													<option value="Philippsburg">Philippsburg</option>
													<option value="Rheinstetten">Rheinstetten</option>
													<option value="Stutensee">Stutensee</option>
													<option value="Sulzfeld">Sulzfeld</option>
													<option value="Ubstadt-Weiher">Ubstadt-Weiher</option>
													<option value="Waghäusel">Waghäusel</option>
													<option value="Waldbronn">Waldbronn</option>
													<option value="Walzbachtal">Walzbachtal</option>
													<option value="Weingarten">Weingarten</option>
													<option value="Zaisenhausen">Zaisenhausen</option>
													<option value="" disabled></option>

													<option value="1" class="text-primary font-weight-bold">Landkreis Calw</option>
													<option value="Bad Herrenalb">Herrenalb</option>
													<option value="" disabled></option>

											</select>
										</div>
									</div>
									<div class="col-12 col-md-4">
										<div class="form-group">
											<label class="sr-only" for="kf_preis">Preis in €</label>
											<input type="number" class="form-control" name="kf_preis" id="kf_preis" title="Bitte geben Sie den Preis in Euro an. Nutzen Sie dazu nur Zahlen oder Kommata." placeholder="Preis in €" pattern="^[0-9,]*$" step="any">
										</div>
									</div>
									<div class="col-12 col-md-4">
										<div class="form-group">
											<label class="sr-only" for="kf_wohnflaeche">Wohnfläche</label>
											<input type="number" class="form-control" name="kf_wohnflaeche" id="kf_wohnflaeche" title="Bitte geben Sie den Wohnfläche in m² an. Nutzen Sie dazu nur Zahlen oder Kommata." placeholder="Wohnfläche in m²" pattern="^[0-9,]*$" step="any" required>
										</div>
									</div>
									<div class="col-12 col-md-4">
										<div class="form-group">
											<label class="sr-only" for="kf_zimmer">Zimmeranzahl</label>
											<input type="number" class="form-control" name="kf_zimmer" id="kf_zimmer" title="Bitte geben Sie die Anzahl der Zimmer an. Nutzen Sie dazu nur Zahlen oder Kommata." placeholder="Zimmeranzahl" pattern="^[0-9,]*$" step="any" required>
										</div>
									</div>
								</div>

								<div class="form-row mt-3 justify-content-center">
									<btn href="" class="btn btn-lg btn-primary text-uppercase shadow-none btn-submit" title="Käufer finden!">Käufer finden!</btn>
								</div>

								<div class="overlay d-none">
									<div class="row">
										<div class="col-12">
											<div class="spinner"></div>
											<h3 class="font-weight-light text-center text-primary mb-3">Daten werden berechnet ...</h3>
										</div>
									</div>
								</div>
							</form>

							<div id="kf-result" class="mt-5 mb-3 mx-md-5 d-none">
								<hr>
								<h5 class="mt-5 mb-4 text-center kf-result-title"></h5>
								<div class="kf-resultcards">

								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
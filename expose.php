<?php
	$obj_id 		= $_GET['obj_id'];

	$page					= 'immobilienobjekt';
	$schluessel				= 'immobilienobjekt';

	require('connection.inc.php');
	require('function.inc.php');
	require('data.inc.php');

	$objekt_sql 	= sql_select_where('all', 'objekte', 'obj_id', $obj_id, '', '');
	$objekt_row 	= mysqli_fetch_assoc($objekt_sql);

	$titel_img_sql 	= sql_select_where('all', 'images', array('img_item_id', 'img_titel'), array($obj_id, 'TITELBILD'), '', '');
	$titel_img_row 	= mysqli_fetch_assoc($titel_img_sql);
	$img_sql 		= sql_select_where('all', 'images', array('img_item_id', 'img_titel'), array($obj_id, 'BILD'), '', '');

	// CSS Styles
	$text_color		= 'primary';
	$bg_color 		= 'bg-blue-gradient';

	include('header.php');
	
?>

<!-- Header (Logo, Überschrift, Einleitung) -->
<section class="<?php echo $bg_color; ?> first-section">
	<!-- Überschrift & Einleitung-->
	<div class="container py-4">
		<span class="text-uppercase text-light"><?php echo $objekt_row['obj_art']; ?></span>
		<h1 class="display-4 text-light"><?php echo $objekt_row['obj_titel']; ?></h1>
	</div>
</section>

<!-- Inhalt -->
<div class="bg-white container p-5 mb-5 immobilie">
	<div class="row">
		<div class="col-md-6 mb-4 mb-md-0">
			<div id="objekt_carousel" class="carousel slide box-shadow" data-ride="carousel">
				<div class="carousel-inner">
					<?php if(!$titel_img_row['img_bild'] == '') { ?>
						<div class="carousel-item active" style="background-image: url('/<?php echo $titel_img_row['img_bild']; ?>');"></div>
					<?php } ?>
					<?php 
					$i = 1;
					while($img_row = mysqli_fetch_assoc($img_sql)) { ?>
						<div class="carousel-item <?php if($titel_img_row['img_bild'] == '' && $i == '1') { echo 'active'; } ?>" style="background-image: url('/<?php echo $img_row['img_bild']; ?>');"></div>
					<?php $i++; } ?>
				</div>
				<a class="carousel-control-prev" href="#objekt_carousel" role="button" data-slide="prev">
					<span class="carousel-control-prev-icon" aria-hidden="true"></span>
					<span class="sr-only">Previous</span>
				</a>
				<a class="carousel-control-next" href="#objekt_carousel" role="button" data-slide="next">
					<span class="carousel-control-next-icon" aria-hidden="true"></span>
					<span class="sr-only">Next</span>
				</a>
			</div>
			<!-- <img src="/img/artikel/1/large/kapitalanlage.jpg" class="box-shadow" alt="Immoname" title="Immoname"> -->
		</div>
		<div class="col-md-6">
			<!-- Wohnung zur Miete/Pacht/Leasing -->
			<?php if($objekt_row['obj_art'] == 'Wohnung' && $objekt_row['obj_nutzungsart'] == 'Wohnen' && ($objekt_row['obj_vermarktungsart'] == 'Miete/Pacht' || $objekt_row['obj_vermarktungsart'] == 'Leasing')) : ?>
				<table>
					<tbody>
						<tr>
							<td>Objektnummer</td>
							<td><?php echo $objekt_row['obj_objektnr_extern']; ?></td>
						</tr>
						<?php if(!$objekt_row['obj_unterart'] == '') : ?>
							<tr>
								<td>Objektkategorie</td>
								<td><?php echo $objekt_row['obj_unterart']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_vermarktungsart'] == '') : ?>
							<tr>
								<td>Vermarktungsart</td>
								<td><?php echo $objekt_row['obj_vermarktungsart']; ?></td>
							</tr>
						<?php endif; ?>
						<tr>
							<td>Objektanschrift</td>
							<td>
								<?php echo($objekt_row['obj_adresse_freigeben'] == '1' ? $objekt_row['obj_strasse'].' '.$objekt_row['obj_hausnummer'].'<br>' : ''); ?>
								<?php echo $objekt_row['obj_plz'].' '.$objekt_row['obj_ort']; ?>	
							</td>
						</tr>
						<?php if(!$objekt_row['obj_baujahr'] == '') : ?>
							<tr>
								<td>Baujahr</td>
								<td><?php echo $objekt_row['obj_baujahr']; ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_wohnflaeche'] == '') : ?>
							<tr>
								<td>Wohnfläche</td>
								<td>ca. <?php echo $objekt_row['obj_wohnflaeche']; ?> m²</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_nutzflaeche'] == '') : ?>
							<tr>
								<td>Nutzfläche</td>
								<td><?php echo $objekt_row['obj_nutzflaeche']; ?> m²</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_grundstuecksflaeche'] == '') : ?>
							<tr>
								<td>Grundstücksfläche</td>
								<td><?php echo $objekt_row['obj_grundstuecksflaeche']; ?> m²</td>
							</tr>
						<?php endif; ?>

					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_verfuegbar_ab'] == '') : ?>
							<tr>
								<td>Bezug ab</td>
								<td><?php echo $objekt_row['obj_verfuegbar_ab']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_zustand'] == '') : ?>
							<tr>
								<td>Zustand</td>
								<td><?php echo $objekt_row['obj_zustand']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_etage'] == '') : ?>
							<tr>
								<td>Etage</td>
								<td><?php echo $objekt_row['obj_etage']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_anzahl_zimmer'] == '') : ?>
							<tr>
								<td>Zimmer</td>
								<td><?php echo $objekt_row['obj_anzahl_zimmer']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_anzahl_schlafzimmer'] == '') : ?>
							<tr>
								<td>Anzahl Schlafzimmer</td>
								<td><?php echo $objekt_row['obj_anzahl_schlafzimmer']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_anzahl_badezimmer'] == '') : ?>
							<tr>
								<td>Anzahl Badezimmer</td>
								<td><?php echo $objekt_row['obj_anzahl_badezimmer']; ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_epart'] == '') : ?>
							<tr>
								<td>Energieausweistyp</td>
								<td><?php echo $objekt_row['obj_epart']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_endenergiebedarf'] == '' || !$objekt_row['obj_heizwert'] == '') : ?>
							<tr>
								<td>Kennwert</td>
								<td><?php echo ($objekt_row['obj_endenergiebedarf'] == '' ? $objekt_row['obj_heizwert'] : $objekt_row['obj_endenergiebedarf']); ?> kWh/(m²*a)</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_mitwarmwasser'] == '') : ?>
							<tr>
								<td>Energieverbrauch für Warmwasser enthalten</td>
								<td><?php echo($objekt_row['obj_mitwarmwasser'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_kaltmiete'] == '') : ?>
							<tr>
								<td>Kaltmiete mtl.</td>
								<td><?php echo number_format($objekt_row['obj_kaltmiete'],2,',','.'); ?> €</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_nebenkosten'] == '') : ?>
							<tr>
								<td>Nebenkosten</td>
								<td><?php echo number_format($objekt_row['obj_nebenkosten'],2,',','.'); ?> €</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_warmmiete'] == '') : ?>
							<tr>
								<td>Monatsmiete inkl. NK</td>
								<td><?php echo number_format($objekt_row['obj_warmmiete'],2,',','.'); ?> €</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_provisionspflichtig'] == '' || !$objekt_row['obj_aussen_courtage'] == '') : ?>
							<tr>
								<td>Provision</td>
								<td>
									<?php if($objekt_row['obj_provisionspflichtig'] == '0') : echo 'provisionsfrei';
										elseif (!$objekt_row['obj_aussen_courtage'] == '') : echo $objekt_row['obj_aussen_courtage'];
										else : echo 'provisionspflichtig'; endif; ?>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_heizung'] == '') : ?>
							<tr>
								<td>Heizungsart</td>
								<td><?php echo $objekt_row['obj_heizung']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_befeuerung'] == '') : ?>
							<tr>
								<td>Befeuerungsart</td>
								<td><?php echo $objekt_row['obj_befeuerung']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_fahrstuhl'] == '' && $objekt_row['obj_fahrstuhl'] !== '9') : ?>
							<tr>
								<td>Fahrstuhl</td>
								<td><?php echo($objekt_row['obj_fahrstuhl'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_haustiere'] == '' && $objekt_row['obj_haustiere'] !== '9') : ?>
							<tr>
								<td>Haustiere</td>
								<td><?php echo($objekt_row['obj_haustiere'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_balkon_terrasse'] == '' && $objekt_row['obj_balkon_terrasse'] !== '9') : ?>
							<tr>
								<td>Balkon/Terrasse</td>
								<td><?php echo($objekt_row['obj_balkon_terrasse'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_gartennutzung'] == '' && $objekt_row['obj_gartennutzung'] !== '9') : ?>
							<tr>
								<td>Gartenmitbenutzung</td>
								<td><?php echo($objekt_row['obj_gartennutzung'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_kueche'] == '' && $objekt_row['obj_kueche'] !== '9') : ?>
							<tr>
								<td>Einbauküche</td>
								<td><?php echo($objekt_row['obj_kueche'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_wbs_sozialwohnung'] == '' && $objekt_row['obj_wbs_sozialwohnung'] !== '9') : ?>
							<tr>
								<td>Förderung</td>
								<td><?php echo($objekt_row['obj_wbs_sozialwohnung'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_stellplatzart'] == '') : ?>
							<tr>
								<td>Parkplatz/Stellplatz</td>
								<td><?php echo $objekt_row['obj_stellplatzart']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_stp_sonstige'] == '') : ?>
							<tr>
								<td>Stellplatzmiete</td>
								<td><?php echo $objekt_row['obj_stp_sonstige']; ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<!-- Haus zur Miete/Pacht/Leasing -->
			<?php if($objekt_row['obj_art'] == 'Haus' && $objekt_row['obj_nutzungsart'] == 'Wohnen' && ($objekt_row['obj_vermarktungsart'] == 'Miete/Pacht' || $objekt_row['obj_vermarktungsart'] == 'Leasing')) : ?>
				<table>
					<tbody>
						<tr>
							<td>Objektnummer</td>
							<td><?php echo $objekt_row['obj_objektnr_extern']; ?></td>
						</tr>
						<?php if(!$objekt_row['obj_unterart'] == '') : ?>
							<tr>
								<td>Objektkategorie</td>
								<td><?php echo $objekt_row['obj_unterart']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_vermarktungsart'] == '') : ?>
							<tr>
								<td>Vermarktungsart</td>
								<td><?php echo $objekt_row['obj_vermarktungsart']; ?></td>
							</tr>
						<?php endif; ?>
						<tr>
							<td>Objektanschrift</td>
							<td>
								<?php echo($objekt_row['obj_adresse_freigeben'] == '1' ? $objekt_row['obj_strasse'].' '.$objekt_row['obj_hausnummer'].'<br>' : ''); ?>
								<?php echo $objekt_row['obj_plz'].' '.$objekt_row['obj_ort']; ?>	
							</td>
						</tr>
						<?php if(!$objekt_row['obj_baujahr'] == '') : ?>
							<tr>
								<td>Baujahr</td>
								<td><?php echo $objekt_row['obj_baujahr']; ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_nutzflaeche'] == '') : ?>
							<tr>
								<td>Nutzfläche</td>
								<td><?php echo $objekt_row['obj_nutzflaeche']; ?> m²</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_grundstuecksflaeche'] == '') : ?>
							<tr>
								<td>Grundstücksfläche</td>
								<td><?php echo $objekt_row['obj_grundstuecksflaeche']; ?> m²</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_verfuegbar_ab'] == '') : ?>
							<tr>
								<td>Bezug ab</td>
								<td><?php echo $objekt_row['obj_verfuegbar_ab']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_zustand'] == '') : ?>
							<tr>
								<td>Zustand</td>
								<td><?php echo $objekt_row['obj_zustand']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_etage'] == '') : ?>
							<tr>
								<td>Etagenzahl</td>
								<td><?php echo $objekt_row['obj_etage']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_anzahl_zimmer'] == '') : ?>
							<tr>
								<td>Zimmer</td>
								<td><?php echo $objekt_row['obj_anzahl_zimmer']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_anzahl_schlafzimmer'] == '') : ?>
							<tr>
								<td>Anzahl Schlafzimmer</td>
								<td><?php echo $objekt_row['obj_anzahl_schlafzimmer']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_anzahl_badezimmer'] == '') : ?>
							<tr>
								<td>Anzahl Badezimmer</td>
								<td><?php echo $objekt_row['obj_anzahl_badezimmer']; ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_epart'] == '') : ?>
							<tr>
								<td>Energieausweistyp</td>
								<td><?php echo $objekt_row['obj_epart']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_endenergiebedarf'] == '' || !$objekt_row['obj_heizwert'] == '') : ?>
							<tr>
								<td>Kennwert</td>
								<td><?php echo ($objekt_row['obj_endenergiebedarf'] == '' ? $objekt_row['obj_heizwert'] : $objekt_row['obj_endenergiebedarf']); ?> kWh/(m²*a)</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_mitwarmwasser'] == '') : ?>
							<tr>
								<td>Energieverbrauch für Warmwasser enthalten</td>
								<td><?php echo($objekt_row['obj_mitwarmwasser'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_kaltmiete'] == '') : ?>
							<tr>
								<td>Kaltmiete mtl.</td>
								<td><?php echo number_format($objekt_row['obj_kaltmiete'],2,',','.'); ?> €</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_nebenkosten'] == '') : ?>
							<tr>
								<td>Nebenkosten</td>
								<td><?php echo number_format($objekt_row['obj_nebenkosten'],2,',','.'); ?> €</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_warmmiete'] == '') : ?>
							<tr>
								<td>Monatsmiete inkl. NK</td>
								<td><?php echo number_format($objekt_row['obj_warmmiete'],2,',','.'); ?> €</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_provisionspflichtig'] == '' || !$objekt_row['obj_aussen_courtage'] == '') : ?>
							<tr>
								<td>Provision</td>
								<td>
									<?php if($objekt_row['obj_provisionspflichtig'] == '0') : echo 'provisionsfrei';
										elseif (!$objekt_row['obj_aussen_courtage'] == '') : echo $objekt_row['obj_aussen_courtage'];
										else : echo 'provisionspflichtig'; endif; ?>
								</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_heizung'] == '') : ?>
							<tr>
								<td>Heizungsart</td>
								<td><?php echo $objekt_row['obj_heizung']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_befeuerung'] == '') : ?>
							<tr>
								<td>Befeuerungsart</td>
								<td><?php echo $objekt_row['obj_befeuerung']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_fahrstuhl'] == '' && $objekt_row['obj_fahrstuhl'] !== '9') : ?>
							<tr>
								<td>Fahrstuhl</td>
								<td><?php echo($objekt_row['obj_fahrstuhl'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_haustiere'] == '' && $objekt_row['obj_haustiere'] !== '9') : ?>
							<tr>
								<td>Haustiere</td>
								<td><?php echo($objekt_row['obj_haustiere'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_balkon_terrasse'] == '' && $objekt_row['obj_balkon_terrasse'] !== '9') : ?>
							<tr>
								<td>Balkon/Terrasse</td>
								<td><?php echo($objekt_row['obj_balkon_terrasse'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_gartennutzung'] == '' && $objekt_row['obj_gartennutzung'] !== '9') : ?>
							<tr>
								<td>Gartenmitbenutzung</td>
								<td><?php echo($objekt_row['obj_gartennutzung'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_kueche'] == '' && $objekt_row['obj_kueche'] !== '9') : ?>
							<tr>
								<td>Einbauküche</td>
								<td><?php echo($objekt_row['obj_kueche'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_wbs_sozialwohnung'] == '' && $objekt_row['obj_wbs_sozialwohnung'] !== '9') : ?>
							<tr>
								<td>Förderung</td>
								<td><?php echo($objekt_row['obj_wbs_sozialwohnung'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_anzahl_stellplaetze'] == '') : ?>
							<tr>
								<td>Anzahl Garage/Stellplätze</td>
								<td><?php echo $objekt_row['obj_anzahl_stellplaetze']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_stp_sonstige'] == '') : ?>
							<tr>
								<td>Stellplatzmiete</td>
								<td><?php echo $objekt_row['obj_stp_sonstige']; ?> €</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<!-- Wohnung zum Kauf/Erbpacht -->
			<?php if($objekt_row['obj_art'] == 'Wohnung' && ($objekt_row['obj_nutzungsart'] == 'Wohnen' || $objekt_row['obj_nutzungsart'] == 'Anlage') && ($objekt_row['obj_vermarktungsart'] == 'Kauf' || $objekt_row['obj_vermarktungsart'] == 'Erbpacht')) : ?>
				<table>
					<tbody>
						<tr>
							<td>Objektnummer</td>
							<td><?php echo $objekt_row['obj_objektnr_extern']; ?></td>
						</tr>
						<?php if(!$objekt_row['obj_unterart'] == '') : ?>
							<tr>
								<td>Objektkategorie</td>
								<td><?php echo $objekt_row['obj_unterart']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_vermarktungsart'] == '') : ?>
							<tr>
								<td>Vermarktungsart</td>
								<td><?php echo $objekt_row['obj_vermarktungsart']; ?></td>
							</tr>
						<?php endif; ?>
						<tr>
							<td>Objektanschrift</td>
							<td>
								<?php echo($objekt_row['obj_adresse_freigeben'] == '1' ? $objekt_row['obj_strasse'].' '.$objekt_row['obj_hausnummer'].'<br>' : ''); ?>
								<?php echo $objekt_row['obj_plz'].' '.$objekt_row['obj_ort']; ?>	
							</td>
						</tr>
						<?php if(!$objekt_row['obj_baujahr'] == '') : ?>
							<tr>
								<td>Baujahr</td>
								<td><?php echo $objekt_row['obj_baujahr']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_wohnflaeche'] == '') : ?>
							<tr>
								<td>Wohnfläche</td>
								<td>ca. <?php echo $objekt_row['obj_wohnflaeche']; ?> m²</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_kaufpreis'] == '') : ?>
							<tr>
								<td>Kaufpreis</td>
								<td><?php echo number_format($objekt_row['obj_kaufpreis'],2,',','.'); ?> €</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_verfuegbar_ab'] == '') : ?>
							<tr>
								<td>Bezug ab</td>
								<td><?php echo $objekt_row['obj_verfuegbar_ab']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_vermietet'] == '' && $objekt_row['obj_vermietet'] !== '9') : ?>
							<tr>
								<td>Vermietet</td>
								<td><?php echo($objekt_row['obj_vermietet'] == '1' || 'true' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_mieteinnahmen_ist'] == '') : ?>
							<tr>
								<td>Mieteinnahmen</td>
								<td><?php echo $objekt_row['obj_mieteinnahmen_ist']; ?> €</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_zustand'] == '') : ?>
							<tr>
								<td>Zustand</td>
								<td><?php echo $objekt_row['obj_zustand']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_etage'] == '') : ?>
							<tr>
								<td>Etage</td>
								<td><?php echo $objekt_row['obj_etage']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_anzahl_zimmer'] == '') : ?>
							<tr>
								<td>Zimmer</td>
								<td><?php echo $objekt_row['obj_anzahl_zimmer']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_anzahl_schlafzimmer'] == '') : ?>
							<tr>
								<td>Anzahl Schlafzimmer</td>
								<td><?php echo $objekt_row['obj_anzahl_schlafzimmer']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_anzahl_badezimmer'] == '') : ?>
							<tr>
								<td>Anzahl Badezimmer</td>
								<td><?php echo $objekt_row['obj_anzahl_badezimmer']; ?></td>
							</tr>
						<?php endif; ?>

					</tbody>
				</table>
				<br>
				<table>
					<tbody>

						<?php if(!$objekt_row['obj_epart'] == '') : ?>
							<tr>
								<td>Energieausweistyp</td>
								<td><?php echo $objekt_row['obj_epart']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_endenergiebedarf'] == '' || !$objekt_row['obj_heizwert'] == '') : ?>
							<tr>
								<td>Kennwert</td>
								<td><?php echo ($objekt_row['obj_endenergiebedarf'] == '' ? $objekt_row['obj_heizwert'] : $objekt_row['obj_endenergiebedarf']); ?> kWh/(m²*a)</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_mitwarmwasser'] == '') : ?>
							<tr>
								<td>Energieverbrauch für Warmwasser enthalten</td>
								<td><?php echo($objekt_row['obj_mitwarmwasser'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_heizung'] == '') : ?>
							<tr>
								<td>Heizungsart</td>
								<td><?php echo $objekt_row['obj_heizung']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_befeuerung'] == '') : ?>
							<tr>
								<td>Befeuerungsart</td>
								<td><?php echo $objekt_row['obj_befeuerung']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_fahrstuhl'] == '' && $objekt_row['obj_fahrstuhl'] !== '9') : ?>
							<tr>
								<td>Fahrstuhl</td>
								<td><?php echo($objekt_row['obj_fahrstuhl'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_haustiere'] == '' && $objekt_row['obj_haustiere'] !== '9') : ?>
							<tr>
								<td>Haustiere</td>
								<td><?php echo($objekt_row['obj_haustiere'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_balkon_terrasse'] == '' && $objekt_row['obj_balkon_terrasse'] !== '9') : ?>
							<tr>
								<td>Balkon/Terrasse</td>
								<td><?php echo($objekt_row['obj_balkon_terrasse'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_gartennutzung'] == '' && $objekt_row['obj_gartennutzung'] !== '9') : ?>
							<tr>
								<td>Gartenmitbenutzung</td>
								<td><?php echo($objekt_row['obj_gartennutzung'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_kueche'] == '' && $objekt_row['obj_kueche'] !== '9') : ?>
							<tr>
								<td>Einbauküche</td>
								<td><?php echo($objekt_row['obj_kueche'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_wbs_sozialwohnung'] == '' && $objekt_row['obj_wbs_sozialwohnung'] !== '9') : ?>
							<tr>
								<td>Förderung</td>
								<td><?php echo($objekt_row['obj_wbs_sozialwohnung'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_stellplatzart'] == '') : ?>
							<tr>
								<td>Parkplatz/Stellplatz</td>
								<td><?php echo $objekt_row['obj_stellplatzart']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_stp_sonstige'] == '') : ?>
							<tr>
								<td>Stellplatzmiete</td>
								<td><?php echo $objekt_row['obj_stp_sonstige']; ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<!-- Haus zum Kauf/Erbpacht -->
			<?php if($objekt_row['obj_art'] == 'Haus' && $objekt_row['obj_nutzungsart'] == 'Wohnen' && ($objekt_row['obj_vermarktungsart'] == 'Kauf' || $objekt_row['obj_vermarktungsart'] == 'Erbpacht')) : ?>
				<table>
					<tbody>
						<tr>
							<td>Objektnummer</td>
							<td><?php echo $objekt_row['obj_objektnr_extern']; ?></td>
						</tr>
						<?php if(!$objekt_row['obj_unterart'] == '') : ?>
							<tr>
								<td>Objektkategorie</td>
								<td><?php echo $objekt_row['obj_unterart']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_vermarktungsart'] == '') : ?>
							<tr>
								<td>Vermarktungsart</td>
								<td><?php echo $objekt_row['obj_vermarktungsart']; ?></td>
							</tr>
						<?php endif; ?>
						<tr>
							<td>Objektanschrift</td>
							<td>
								<?php echo($objekt_row['obj_adresse_freigeben'] == '1' ? $objekt_row['obj_strasse'].' '.$objekt_row['obj_hausnummer'].'<br>' : ''); ?>
								<?php echo $objekt_row['obj_plz'].' '.$objekt_row['obj_ort']; ?>	
							</td>
						</tr>
						<?php if(!$objekt_row['obj_baujahr'] == '') : ?>
							<tr>
								<td>Baujahr</td>
								<td><?php echo $objekt_row['obj_baujahr']; ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_kaufpreis'] == '') : ?>
							<tr>
								<td>Kaufpreis</td>
								<td><?php echo number_format($objekt_row['obj_kaufpreis'],2,',','.'); ?> €</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_wohnflaeche'] == '') : ?>
							<tr>
								<td>Wohnfläche</td>
								<td>ca. <?php echo $objekt_row['obj_wohnflaeche']; ?> m²</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_nutzflaeche'] == '') : ?>
							<tr>
								<td>Nutzfläche</td>
								<td><?php echo $objekt_row['obj_nutzflaeche']; ?> m²</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_grundstuecksflaeche'] == '') : ?>
							<tr>
								<td>Grundstücksfläche</td>
								<td><?php echo $objekt_row['obj_grundstuecksflaeche']; ?> m²</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>

						<?php if(!$objekt_row['obj_verfuegbar_ab'] == '') : ?>
							<tr>
								<td>Bezug ab</td>
								<td><?php echo $objekt_row['obj_verfuegbar_ab']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_vermietet'] == '' && $objekt_row['obj_vermietet'] !== '9') : ?>
							<tr>
								<td>Vermietet</td>
								<td><?php echo($objekt_row['obj_vermietet'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_mieteinnahmen_ist'] == '') : ?>
							<tr>
								<td>Mieteinnahmen</td>
								<td><?php echo $objekt_row['obj_mieteinnahmen_ist']; ?> €</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_zustand'] == '') : ?>
							<tr>
								<td>Zustand</td>
								<td><?php echo $objekt_row['obj_zustand']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_etage'] == '') : ?>
							<tr>
								<td>Etagenzahl</td>
								<td><?php echo $objekt_row['obj_etage']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_anzahl_zimmer'] == '') : ?>
							<tr>
								<td>Zimmer</td>
								<td><?php echo $objekt_row['obj_anzahl_zimmer']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_anzahl_schlafzimmer'] == '') : ?>
							<tr>
								<td>Anzahl Schlafzimmer</td>
								<td><?php echo $objekt_row['obj_anzahl_schlafzimmer']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_anzahl_badezimmer'] == '') : ?>
							<tr>
								<td>Anzahl Badezimmer</td>
								<td><?php echo $objekt_row['obj_anzahl_badezimmer']; ?></td>
							</tr>
						<?php endif; ?>

					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_epart'] == '') : ?>
							<tr>
								<td>Energieausweistyp</td>
								<td><?php echo $objekt_row['obj_epart']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_endenergiebedarf'] == '' || !$objekt_row['obj_heizwert'] == '') : ?>
							<tr>
								<td>Kennwert</td>
								<td><?php echo ($objekt_row['obj_endenergiebedarf'] == '' ? $objekt_row['obj_heizwert'] : $objekt_row['obj_endenergiebedarf']); ?> kWh/(m²*a)</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_mitwarmwasser'] == '') : ?>
							<tr>
								<td>Energieverbrauch für Warmwasser enthalten</td>
								<td><?php echo($objekt_row['obj_mitwarmwasser'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_heizung'] == '') : ?>
							<tr>
								<td>Heizungsart</td>
								<td><?php echo $objekt_row['obj_heizung']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_befeuerung'] == '') : ?>
							<tr>
								<td>Befeuerungsart</td>
								<td><?php echo $objekt_row['obj_befeuerung']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_fahrstuhl'] == '' && $objekt_row['obj_fahrstuhl'] !== '9') : ?>
							<tr>
								<td>Fahrstuhl</td>
								<td><?php echo($objekt_row['obj_fahrstuhl'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_haustiere'] == '' && $objekt_row['obj_haustiere'] !== '9') : ?>
							<tr>
								<td>Haustiere</td>
								<td><?php echo($objekt_row['obj_haustiere'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_balkon_terrasse'] == '' && $objekt_row['obj_balkon_terrasse'] !== '9') : ?>
							<tr>
								<td>Balkon/Terrasse</td>
								<td><?php echo($objekt_row['obj_balkon_terrasse'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_gartennutzung'] == '' && $objekt_row['obj_gartennutzung'] !== '9') : ?>
							<tr>
								<td>Gartenmitbenutzung</td>
								<td><?php echo($objekt_row['obj_gartennutzung'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_kueche'] == '' && $objekt_row['obj_kueche'] !== '9') : ?>
							<tr>
								<td>Einbauküche</td>
								<td><?php echo($objekt_row['obj_kueche'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_wbs_sozialwohnung'] == '' && $objekt_row['obj_wbs_sozialwohnung'] !== '9') : ?>
							<tr>
								<td>Förderung</td>
								<td><?php echo($objekt_row['obj_wbs_sozialwohnung'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_anzahl_stellplaetze'] == '') : ?>
							<tr>
								<td>Anzahl Garage/Stellplätze</td>
								<td><?php echo $objekt_row['obj_anzahl_stellplaetze']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_stp_sonstige'] == '') : ?>
							<tr>
								<td>Stellplatzmiete</td>
								<td><?php echo $objekt_row['obj_stp_sonstige']; ?> €</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<!-- Grundstück -->
			<?php if($objekt_row['obj_art'] == 'Grundstück') : ?>
				<table>
					<tbody>
						<tr>
							<td>Objektnummer</td>
							<td><?php echo $objekt_row['obj_objektnr_extern']; ?></td>
						</tr>
						<?php if(!$objekt_row['obj_unterart'] == '') : ?>
							<tr>
								<td>Objektkategorie</td>
								<td><?php echo $objekt_row['obj_unterart']; ?></td>
							</tr>
						<?php endif; ?>
						<tr>
							<td>Objektanschrift</td>
							<td>
								<?php echo($objekt_row['obj_adresse_freigeben'] == '1' ? $objekt_row['obj_strasse'].' '.$objekt_row['obj_hausnummer'].'<br>' : ''); ?>
								<?php echo $objekt_row['obj_plz'].' '.$objekt_row['obj_ort']; ?>	
							</td>
						</tr>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_grundstuecksflaeche'] == '') : ?>
							<tr>
								<td>Grundstücksfläche</td>
								<td><?php echo $objekt_row['obj_grundstuecksflaeche']; ?> m²</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_teilbar_ab'] == '') : ?>
							<tr>
								<td>Fläche teilbar ab</td>
								<td><?php echo $objekt_row['obj_teilbar_ab']; ?> m²</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_laufzeit'] == '') : ?>
							<tr>
								<td>Erbpachtdauer</td>
								<td><?php echo $objekt_row['obj_laufzeit']; ?> Jahre</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_grz'] == '') : ?>
							<tr>
								<td>Grundflächenzahl (GRZ)</td>
								<td>ca. <?php echo $objekt_row['obj_grz']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_gfz'] == '') : ?>
							<tr>
								<td>Geschoßflächenzahl (GFZ)</td>
								<td>ca. <?php echo $objekt_row['obj_gfz']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_erschliessung'] == '') : ?>
							<tr>
								<td>Erschließung</td>
								<td><?php echo $objekt_row['obj_erschliessung']; ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			<?php endif; ?>

			<!-- Gewerbe -->
			<?php if($objekt_row['obj_nutzungsart'] == 'Gewerbe') : ?>
				<table>
					<tbody>
						<tr>
							<td>Objektnummer</td>
							<td><?php echo $objekt_row['obj_objektnr_extern']; ?></td>
						</tr>
						<?php if(!$objekt_row['obj_art'] == '') : ?>
							<tr>
								<td>Objektart</td>
								<td><?php echo $objekt_row['obj_art']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_unterart'] == '') : ?>
							<tr>
								<td>Objektkategorie</td>
								<td><?php echo $objekt_row['obj_unterart']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_vermarktungsart'] == '') : ?>
							<tr>
								<td>Vermarktungsart</td>
								<td><?php echo $objekt_row['obj_vermarktungsart']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_verfuegbar_ab'] == '') : ?>
							<tr>
								<td>Verfügbar ab</td>
								<td><?php echo $objekt_row['obj_verfuegbar_ab']; ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_gastroflaeche'] == '') : ?>
							<tr>
								<td>Gastraumfläche</td>
								<td><?php echo $objekt_row['obj_gastroflaeche']; ?> m²</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_nebenflaeche'] == '') : ?>
							<tr>
								<td>Nebenfläche</td>
								<td><?php echo $objekt_row['obj_nebenflaeche']; ?> m²</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_gesamtflaeche'] == '') : ?>
							<tr>
								<td>Gesamtfläche</td>
								<td><?php echo $objekt_row['obj_gesamtflaeche']; ?> m²</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_grundstuecksflaeche'] == '') : ?>
							<tr>
								<td>Grundstücksfläche</td>
								<td><?php echo $objekt_row['obj_grundstuecksflaeche']; ?> m²</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_teilbar_ab'] == '') : ?>
							<tr>
								<td>Teilbar ab</td>
								<td><?php echo $objekt_row['obj_teilbar_ab']; ?> m²</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_baujahr'] == '') : ?>
							<tr>
								<td>Baujahr</td>
								<td><?php echo $objekt_row['obj_baujahr']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_etage'] == '') : ?>
							<tr>
								<td>Etage</td>
								<td><?php echo $objekt_row['obj_etage']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_zustand'] == '') : ?>
							<tr>
								<td>Zustand</td>
								<td><?php echo $objekt_row['obj_zustand']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_boden'] == '') : ?>
							<tr>
								<td>Bodenbelag</td>
								<td><?php echo $objekt_row['obj_boden']; ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_kaltmiete'] == '') : ?>
							<tr>
								<td>Kaltmiete mtl.</td>
								<td><?php echo number_format($objekt_row['obj_kaltmiete'],2,',','.'); ?> €</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_nebenkosten'] == '') : ?>
							<tr>
								<td>Nebenkosten</td>
								<td><?php echo number_format($objekt_row['obj_nebenkosten'],2,',','.'); ?> €</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_warmmiete'] == '') : ?>
							<tr>
								<td>Monatsmiete inkl. NK</td>
								<td><?php echo number_format($objekt_row['obj_warmmiete'],2,',','.'); ?> €</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_kaufpreis'] == '') : ?>
							<tr>
								<td>Kaufpreis</td>
								<td><?php echo number_format($objekt_row['obj_kaufpreis'],2,',','.'); ?> €</td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_heizung'] == '') : ?>
							<tr>
								<td>Heizungsart</td>
								<td><?php echo $objekt_row['obj_heizung']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_befeuerung'] == '') : ?>
							<tr>
								<td>Befeuerungsart</td>
								<td><?php echo $objekt_row['obj_befeuerung']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_epart'] == '') : ?>
							<tr>
								<td>Energieausweistyp</td>
								<td><?php echo $objekt_row['obj_epart']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_endenergiebedarf'] == '' || !$objekt_row['obj_heizwert'] == '') : ?>
							<tr>
								<td>Kennwert</td>
								<td><?php echo ($objekt_row['obj_endenergiebedarf'] == '' ? $objekt_row['obj_heizwert'] : $objekt_row['obj_endenergiebedarf']); ?> kWh/(m²*a)</td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_mitwarmwasser'] == '') : ?>
							<tr>
								<td>Energieverbrauch für Warmwasser enthalten</td>
								<td><?php echo($objekt_row['obj_mitwarmwasser'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
				<br>
				<table>
					<tbody>
						<?php if(!$objekt_row['obj_fahrstuhl'] == '' && $objekt_row['obj_fahrstuhl'] !== '9') : ?>
							<tr>
								<td>Fahrstuhl</td>
								<td><?php echo($objekt_row['obj_fahrstuhl'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_kueche'] == '' && $objekt_row['obj_kueche'] !== '9') : ?>
							<tr>
								<td>Küche vorhanden</td>
								<td><?php echo($objekt_row['obj_kueche'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_kantine_cafeteria'] == '' && $objekt_row['obj_kantine_cafeteria'] !== '9') : ?>
							<tr>
								<td>Kantine/Cafeteria</td>
								<td><?php echo($objekt_row['obj_kantine_cafeteria'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_dv_verkabelung'] == '' && $objekt_row['obj_dv_verkabelung'] !== '9') : ?>
							<tr>
								<td>DV-Verkabelung</td>
								<td><?php echo($objekt_row['obj_dv_verkabelung'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_fensterfront'] == '' && $objekt_row['obj_fensterfront'] !== '9') : ?>
							<tr>
								<td>Schaufensterfront</td>
								<td><?php echo($objekt_row['obj_fensterfront'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_rampe'] == '' && $objekt_row['obj_rampe'] !== '9') : ?>
							<tr>
								<td>Rampe</td>
								<td><?php echo($objekt_row['obj_rampe'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_plaetze_gastraum'] == '') : ?>
							<tr>
								<td>Anzahl Plätze Gastraum</td>
								<td><?php echo $objekt_row['obj_plaetze_gastraum']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_anzahl_betten'] == '') : ?>
							<tr>
								<td>Anzahl Betten</td>
								<td><?php echo $objekt_row['obj_anzahl_betten']; ?></td>
							</tr>
						<?php endif; ?>
						<?php if(!$objekt_row['obj_gastterrasse'] == '' && $objekt_row['obj_gastterrasse'] !== '9') : ?>
							<tr>
								<td>Gastterrasse</td>
								<td><?php echo($objekt_row['obj_gastterrasse'] == '1' ? 'Ja' : 'Nein'); ?></td>
							</tr>
						<?php endif; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
	</div>

	<div id="accordion" class="mt-4">
		<?php if(!$objekt_row['obj_dreizeiler'] == '') : ?>
			<div class="card">
				<div class="card-header" id="headingOne">
					<h5 class="mb-0">
						<a data-toggle="collapse" data-target="#kurzinfo" aria-expanded="true" aria-controls="kurzinfo">
							Kurzinfo
						</a>
					</h5>
				</div>

				<div id="kurzinfo" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
					<div class="card-body card-text">
						<?php echo $objekt_row['obj_dreizeiler']; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php if(!$objekt_row['obj_beschreibung'] == '') : ?>
			<div class="card">
				<div class="card-header" id="headingOne">
					<h5 class="mb-0">
						<a data-toggle="collapse" data-target="#beschreibung" aria-expanded="false" aria-controls="beschreibung">
							Beschreibung
						</a>
					</h5>
				</div>

				<div id="beschreibung" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
					<div class="card-body card-text">
						<?php echo $objekt_row['obj_beschreibung']; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php if(!$objekt_row['obj_ausstattung'] == '') : ?>
			<div class="card">
				<div class="card-header" id="headingOne">
					<h5 class="mb-0">
						<a data-toggle="collapse" data-target="#ausstattung" aria-expanded="false" aria-controls="ausstattung">
							Ausstattung
						</a>
					</h5>
				</div>

				<div id="ausstattung" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
					<div class="card-body card-text">
						<?php echo $objekt_row['obj_ausstattung']; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php if(!$objekt_row['obj_lage'] == '') : ?>
			<div class="card">
				<div class="card-header" id="headingOne">
					<h5 class="mb-0">
						<a data-toggle="collapse" data-target="#lage" aria-expanded="false" aria-controls="lage">
							Lage
						</a>
					</h5>
				</div>

				<div id="lage" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
					<div class="card-body card-text">
						<?php echo $objekt_row['obj_lage']; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php if(!$objekt_row['obj_sonstige_angaben'] == '') : ?>
			<div class="card">
				<div class="card-header" id="headingOne">
					<h5 class="mb-0">
						<a data-toggle="collapse" data-target="#kundenservice" aria-expanded="false" aria-controls="kundenservice">
							Sonstige Angaben
						</a>
					</h5>
				</div>

				<div id="kundenservice" class="collapse" aria-labelledby="headingOne" data-parent="#accordion">
					<div class="card-body card-text">
						<?php echo $objekt_row['obj_sonstige_angaben']; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</div>
	
	<button type="button" class="btn btn-primary mt-3" data-toggle="modal" data-target="#immo_kontakformular">
		Kontaktieren Sie uns zu dieser Immobilie!
	</button>
</div>

<?php
	require('footer.php');
?>
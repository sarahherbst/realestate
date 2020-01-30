<?php 
	$immoliste_page_sql = sql_select_where('all', 'texte', array('txt_institut', 'txt_schluessel', 'txt_alias', 'txt_rubrik', 'txt_status'), array($institut_id, 'immobilienliste', 'gameplay', $rub_id, '1'), '', ''); 
	$immoliste_page_row = mysqli_fetch_assoc($immoliste_page_sql);

	$objekte_miete_wohnungen_sql = sql_select_where('all', 'objekte', array('obj_art', 'obj_nutzungsart', 'obj_vermarktungsart', 'obj_status'), array('Wohnung', 'Wohnen', 'Miete/Pacht', '1'), '', '');
	$objekte_miete_haeuser_sql = sql_select_where('all', 'objekte', array('obj_art', 'obj_nutzungsart', 'obj_vermarktungsart', 'obj_status'), array('Haus', 'Wohnen', 'Miete/Pacht', '1'), '', '');
	$objekte_kauf_wohnungen_sql = sql_select_where('all', 'objekte', array('obj_art', 'obj_nutzungsart', 'obj_vermarktungsart', 'obj_status'), array('Wohnung', 'Wohnen', 'Kauf', '1'), '', '');
	$objekte_kauf_haeuser_sql = sql_select_where('all', 'objekte', array('obj_art', 'obj_nutzungsart', 'obj_vermarktungsart', 'obj_status'), array('Haus', 'Wohnen', 'Kauf', '1'), '', '');
	$objekte_grundstuecke_sql = sql_select_where('all', 'objekte', array('obj_art', 'obj_status'), array('Grundstück', '1'), '', '');
	$objekte_anlage_sql = sql_select_where('all', 'objekte', array('obj_nutzungsart', 'obj_status'), array('Anlage', '1'), '', ''); // zu bearbeiten
	$objekte_gewerbe_sql = sql_select_where('all', 'objekte', array('obj_nutzungsart', 'obj_status'), array('Gewerbe', '1'), '', '');


?>

<section class="pb-5 pt-5 bg-white" id="immoliste">
	<div class="container">
		<h2 class="card-title display-4 font-weight-light mt-2 mb-4"><?php echo $immoliste_page_row['txt_titel']; ?></h2>
		<p class="card-text mb-5"><?php echo $immoliste_page_row['txt_einleitung']; ?></p>
		<ul class="nav mb-4">
			<?php if(mysqli_num_rows($objekte_kauf_wohnungen_sql) >= 1) { ?>
				<li class="nav-item">
					<a class="nav-link active" id="kauf-wohnungen-tab" data-toggle="tab" data-target="#kauf-wohnungen" role="tab" aria-controls="kauf-wohnungen" aria-selected="true">Wohnung kaufen</a>
				</li>
			<?php } ?>
			<?php if(mysqli_num_rows($objekte_kauf_haeuser_sql) >= 1) { ?>
				<li class="nav-item">
					<a class="nav-link" id="kauf-haeuser-tab" data-toggle="tab" data-target="#kauf-haeuser" role="tab" aria-controls="kauf-haeuser" aria-selected="false">Haus kaufen</a>
				</li>
			<?php } ?>
			<?php if(mysqli_num_rows($objekte_miete_wohnungen_sql) >= 1) { ?>
				<li class="nav-item">
					<a class="nav-link" id="miete-wohnungen-tab" data-toggle="tab" data-target="#miete-wohnungen" role="tab" aria-controls="miete-wohnungen" aria-selected="false">Wohnung mieten</a>
				</li>
			<?php } ?>
			<?php if(mysqli_num_rows($objekte_miete_haeuser_sql) >= 1) { ?>
				<li class="nav-item">
					<a class="nav-link" id="miete-haeuser-tab" data-toggle="tab" data-target="#miete-haeuser" role="tab" aria-controls="miete-haeuser" aria-selected="false">Haus mieten</a>
				</li>
			<?php } ?>
			<?php if(mysqli_num_rows($objekte_grundstuecke_sql) >= 1) { ?>
				<li class="nav-item">
					<a class="nav-link" id="baugrundstuecke-tab" data-toggle="tab" data-target="#baugrundstuecke" role="tab" aria-controls="baugrundstuecke" aria-selected="false">Baugrundstücke</a>
				</li>
			<?php } ?>
			<?php if(mysqli_num_rows($objekte_anlage_sql) >= 1) { ?>
				<li class="nav-item">
					<a class="nav-link" id="anlage-tab" data-toggle="tab" data-target="#anlage" role="tab" aria-controls="anlage" aria-selected="false">Anlageobjekte</a>
				</li>
			<?php } ?>
			<?php if(mysqli_num_rows($objekte_gewerbe_sql) >= 1) { ?>
				<li class="nav-item">
					<a class="nav-link" id="gewerbe-tab" data-toggle="tab" data-target="#gewerbe" role="tab" aria-controls="gewerbe" aria-selected="false">Immobilien Gewerbe</a>
				</li>
			<?php } ?>
		</ul>
		<div class="tab-content" id="myTabContent">
			<?php if(mysqli_num_rows($objekte_kauf_wohnungen_sql) >= 1) { ?>
				<div class="tab-pane fade show active" id="kauf-wohnungen" role="tabpanel" aria-labelledby="kauf-wohnungen-tab">
					<div class="card-deck">
						<?php while ($objekte_kauf_wohnungen_row = mysqli_fetch_assoc($objekte_kauf_wohnungen_sql)) { ?>
						<div class="card einleitung-card shadow">
							<?php $img_sql = sql_select_where('img_bild', 'images', array('img_schluessel', 'img_item_id', 'img_titel'), array('objekt', $objekte_kauf_wohnungen_row['obj_id'], 'TITELBILD'), '', '');
							$img_row = mysqli_fetch_assoc($img_sql);
							if($img_row['img_bild'] == '') { 
								$img_sql = sql_select_where_limit('img_bild', 'images', array('img_schluessel', 'img_item_id', 'img_titel'), array('objekt', $objekte_kauf_wohnungen_row['obj_id'], 'BILD'), '', '', '1');
								$img_row = mysqli_fetch_assoc($img_sql);
							}
							?>
							<div class="card-img-top" style="background-image: url('/<?php if(mysqli_num_rows($img_sql) < 1) { echo 'img/logo_haus.svg'; } else { echo $img_row['img_bild']; } ?>');<?php if(mysqli_num_rows($img_sql) < 1) { echo 'background-size:30%'; } ?>" alt="Card image cap">
							</div>
								<div class="immoliste_content">
									<a href="/expose.php?obj_id=<?php echo $objekte_kauf_wohnungen_row['obj_id']; ?>" title="<?php echo $objekte_kauf_wohnungen_row['obj_titel']; ?>" target="_blank"><h5><?php echo $objekte_kauf_wohnungen_row['obj_titel']; ?></h5></a>
									<p>
										<?php echo ($objekte_kauf_wohnungen_row['obj_anzahl_zimmer'] == '' ? '' : 'Zimmer: '.$objekte_kauf_wohnungen_row['obj_anzahl_zimmer'].'<br>'); ?>
										<?php echo ($objekte_kauf_wohnungen_row['obj_wohnflaeche'] == '' ? '' : 'Wohnfläche: '.$objekte_kauf_wohnungen_row['obj_wohnflaeche'].' m²<br>'); ?>
										<?php echo ($objekte_kauf_wohnungen_row['obj_kaufpreis'] == '' ? '' : 'Kaufpreis: '.number_format($objekte_kauf_wohnungen_row['obj_kaufpreis'],2,',','.').' €<br>'); ?>
										<?php echo ($objekte_kauf_wohnungen_row['obj_ort'] == '' ? '' : 'Ort: '.$objekte_kauf_wohnungen_row['obj_ort']); ?>
									</p>
								</div>
							<div class="card-footer pt-1 pb-4 pl-4">
								<a href="/expose.php?obj_id=<?php echo $objekte_kauf_wohnungen_row['obj_id']; ?>" class="btn btn-outline-light text-uppercase shadow-none ml-1" title="<?php echo $objekte_kauf_wohnungen_row['obj_titel']; ?>" target="_blank">Zum Exposé</a>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<?php if(mysqli_num_rows($objekte_kauf_haeuser_sql) >= 1) { ?>
				<div class="tab-pane fade" id="kauf-haeuser" role="tabpanel" aria-labelledby="kauf-haeuser-tab">
					<div class="card-deck">
						<?php while ($objekte_kauf_haeuser_row = mysqli_fetch_assoc($objekte_kauf_haeuser_sql)) { ?>
							<div class="card einleitung-card shadow">
								<?php $img_sql = sql_select_where('img_bild', 'images', array('img_schluessel', 'img_item_id', 'img_titel'), array('objekt', $objekte_kauf_haeuser_row['obj_id'], 'TITELBILD'), '', '');
								$img_row = mysqli_fetch_assoc($img_sql);
								if($img_row['img_bild'] == '') { 
									$img_sql = sql_select_where_limit('img_bild', 'images', array('img_schluessel', 'img_item_id', 'img_titel'), array('objekt', $objekte_kauf_haeuser_row['obj_id'], 'BILD'), '', '', '1');
									$img_row = mysqli_fetch_assoc($img_sql);
								}
								?>
								<div class="card-img-top" style="background-image: url('/<?php if(mysqli_num_rows($img_sql) < 1) { echo 'img/logo_haus.svg'; } else { echo $img_row['img_bild']; } ?>');<?php if(mysqli_num_rows($img_sql) < 1) { echo 'background-size:30%'; } ?>" alt="Card image cap"></div>
									<div class="immoliste_content">
										<a href="/expose.php?obj_id=<?php echo $objekte_kauf_haeuser_row['obj_id']; ?>" title="<?php echo $objekte_kauf_haeuser_row['obj_titel']; ?>" target="_blank"><h5><?php echo $objekte_kauf_haeuser_row['obj_titel']; ?></h5></a>
										<p>
											<?php echo ($objekte_kauf_haeuser_row['obj_anzahl_zimmer'] == '' ? '' : 'Zimmer: '.$objekte_kauf_haeuser_row['obj_anzahl_zimmer'].'<br>'); ?>
											<?php echo ($objekte_kauf_haeuser_row['obj_wohnflaeche'] == '' ? '' : 'Wohnfläche: '.$objekte_kauf_haeuser_row['obj_wohnflaeche'].' m²<br>'); ?>
											<?php echo ($objekte_kauf_haeuser_row['obj_kaufpreis'] == '' ? '' : 'Kaufpreis: '.number_format($objekte_kauf_haeuser_row['obj_kaufpreis'],2,',','.').' €<br>'); ?>
											<?php echo ($objekte_kauf_haeuser_row['obj_ort'] == '' ? '' : 'Ort: '.$objekte_kauf_haeuser_row['obj_ort']); ?>
										</p>
									</div>
								<div class="card-footer pt-1 pb-4 pl-4">
									<a href="/expose.php?obj_id=<?php echo $objekte_kauf_haeuser_row['obj_id']; ?>" class="btn btn-outline-light text-uppercase shadow-none ml-1" title="<?php echo $objekte_kauf_haeuser_row['obj_titel']; ?>" target="_blank">Zum Exposé</a>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<?php if(mysqli_num_rows($objekte_miete_wohnungen_sql) >= 1) { ?>
				<div class="tab-pane fade show" id="miete-wohnungen" role="tabpanel" aria-labelledby="miete-wohnungen-tab">
					<div class="card-deck">
						<?php while ($objekte_miete_wohnungen_row = mysqli_fetch_assoc($objekte_miete_wohnungen_sql)) { ?>
							<div class="card einleitung-card shadow">
								<?php $img_sql = sql_select_where('img_bild', 'images', array('img_schluessel', 'img_item_id', 'img_titel'), array('objekt', $objekte_miete_wohnungen_row['obj_id'], 'TITELBILD'), '', '');
								$img_row = mysqli_fetch_assoc($img_sql);
								if($img_row['img_bild'] == '') { 
									$img_sql = sql_select_where_limit('img_bild', 'images', array('img_schluessel', 'img_item_id', 'img_titel'), array('objekt', $objekte_miete_wohnungen_row['obj_id'], 'BILD'), '', '', '1');
									$img_row = mysqli_fetch_assoc($img_sql);
								}
								?>
								<div class="card-img-top" style="background-image: url('/<?php if(mysqli_num_rows($img_sql) < 1) { echo 'img/logo_haus.svg'; } else { echo $img_row['img_bild']; } ?>');<?php if(mysqli_num_rows($img_sql) < 1) { echo 'background-size:30%'; } ?>" alt="Card image cap"></div>
								<div class="immoliste_content">
									<a href="/expose.php?obj_id=<?php echo $objekte_miete_wohnungen_row['obj_id']; ?>" title="<?php echo $objekte_miete_wohnungen_row['obj_titel']; ?>" target="_blank"><h5><?php echo $objekte_miete_wohnungen_row['obj_titel']; ?></h5></a>
									<p>
										<?php echo ($objekte_miete_wohnungen_row['obj_anzahl_zimmer'] == '' ? '' : 'Zimmer: '.$objekte_miete_wohnungen_row['obj_anzahl_zimmer'].'<br>'); ?>
										<?php echo ($objekte_miete_wohnungen_row['obj_wohnflaeche'] == '' ? '' : 'Wohnfläche: '.$objekte_miete_wohnungen_row['obj_wohnflaeche'].' m²<br>'); ?>
										<?php echo ($objekte_miete_wohnungen_row['obj_warmmiete'] == '' ? '' : 'Warmmiete (mtl.): '.number_format($objekte_miete_wohnungen_row['obj_warmmiete'],2,',','.').' €<br>'); ?>
										<?php echo ($objekte_miete_wohnungen_row['obj_ort'] == '' ? '' : 'Ort: '.$objekte_miete_wohnungen_row['obj_ort']); ?>
									</p>
								</div>
								<div class="card-footer pt-1 pb-4 pl-4">
									<a href="/expose.php?obj_id=<?php echo $objekte_miete_wohnungen_row['obj_id']; ?>" class="btn btn-outline-light text-uppercase shadow-none ml-1" title="<?php echo $objekte_miete_wohnungen_row['obj_titel']; ?>" target="_blank">Zum Exposé</a>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<?php if(mysqli_num_rows($objekte_miete_haeuser_sql) >= 1) { ?>
				<div class="tab-pane fade" id="miete-haeuser" role="tabpanel" aria-labelledby="miete-haeuser-tab">
					<div class="card-deck">
						<?php while ($objekte_miete_haeuser_row = mysqli_fetch_assoc($objekte_miete_haeuser_sql)) { ?>
							<div class="card einleitung-card shadow">
								<?php $img_sql = sql_select_where('img_bild', 'images', array('img_schluessel', 'img_item_id', 'img_titel'), array('objekt', $objekte_miete_haeuser_row['obj_id'], 'TITELBILD'), '', '');
								$img_row = mysqli_fetch_assoc($img_sql);
								if($img_row['img_bild'] == '') { 
									$img_sql = sql_select_where_limit('img_bild', 'images', array('img_schluessel', 'img_item_id', 'img_titel'), array('objekt', $objekte_miete_haeuser_row['obj_id'], 'BILD'), '', '', '1');
									$img_row = mysqli_fetch_assoc($img_sql);
								}
								?>
								<div class="card-img-top" style="background-image: url('/<?php if(mysqli_num_rows($img_sql) < 1) { echo 'img/logo_haus.svg'; } else { echo $img_row['img_bild']; } ?>');<?php if(mysqli_num_rows($img_sql) < 1) { echo 'background-size:30%'; } ?>" alt="Card image cap"></div>
								<div class="immoliste_content">
									<a href="/expose.php?obj_id=<?php echo $objekte_miete_haeuser_row['obj_id']; ?>" title="<?php echo $objekte_miete_haeuser_row['obj_titel']; ?>" target="_blank"><h5><?php echo $objekte_miete_haeuser_row['obj_titel']; ?></h5></a>
									<p>
										<?php echo ($objekte_miete_haeuser_row['obj_anzahl_zimmer'] == '' ? '' : 'Zimmer: '.$objekte_miete_haeuser_row['obj_anzahl_zimmer'].'<br>'); ?>
										<?php echo ($objekte_miete_haeuser_row['obj_wohnflaeche'] == '' ? '' : 'Wohnfläche: '.$objekte_miete_haeuser_row['obj_wohnflaeche'].' m²<br>'); ?>
										<?php echo ($objekte_miete_wohnungen_row['obj_warmmiete'] == '' ? '' : 'Warmmiete (mtl.): '.number_format($objekte_miete_wohnungen_row['obj_warmmiete'],2,',','.').' €<br>'); ?>
										<?php echo ($objekte_miete_haeuser_row['obj_ort'] == '' ? '' : 'Ort: '.$objekte_miete_haeuser_row['obj_ort']); ?>
									</p>
								</div>
								<div class="card-footer pt-1 pb-4 pl-4">
									<a href="/expose.php?obj_id=<?php echo $objekte_miete_haeuser_row['obj_id']; ?>" class="btn btn-outline-light text-uppercase shadow-none ml-1" title="<?php echo $objekte_miete_haeuser_row['obj_titel']; ?>" target="_blank">Zum Exposé</a>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<?php if(mysqli_num_rows($objekte_grundstuecke_sql) >= 1) { ?>
				<div class="tab-pane fade" id="baugrundstuecke" role="tabpanel" aria-labelledby="baugrundstuecke-tab">
					<div class="card-deck">
						<?php while ($objekte_grundstuecke_row = mysqli_fetch_assoc($objekte_grundstuecke_sql)) { ?>
							<div class="card einleitung-card shadow">
								<?php $img_sql = sql_select_where('img_bild', 'images', array('img_schluessel', 'img_item_id', 'img_titel'), array('objekt', $objekte_grundstuecke_row['obj_id'], 'TITELBILD'), '', '');
								$img_row = mysqli_fetch_assoc($img_sql);
								if($img_row['img_bild'] == '') { 
									$img_sql = sql_select_where_limit('img_bild', 'images', array('img_schluessel', 'img_item_id', 'img_titel'), array('objekt', $objekte_grundstuecke_row['obj_id'], 'BILD'), '', '', '1');
									$img_row = mysqli_fetch_assoc($img_sql);
								}
								?>
								<div class="card-img-top" style="background-image: url('/<?php if(mysqli_num_rows($img_sql) < 1) { echo 'img/logo_haus.svg'; } else { echo $img_row['img_bild']; } ?>');<?php if(mysqli_num_rows($img_sql) < 1) { echo 'background-size:30%'; } ?>" alt="Card image cap"></div>
									<div class="immoliste_content">
										<a href="/expose.php?obj_id=<?php echo $objekte_grundstuecke_row['obj_id']; ?>" title="<?php echo $objekte_grundstuecke_row['obj_titel']; ?>" target="_blank"><h5><?php echo $objekte_grundstuecke_row['obj_titel']; ?></h5></a>
										<p>
											<?php echo ($objekte_grundstuecke_row['obj_gesamtflaeche'] == '' ? '' : 'Gesamtfläche: '.$objekte_grundstuecke_row['obj_gesamtflaeche'].' m²<br>'); ?>
											<?php echo ($objekte_grundstuecke_row['obj_kaufpreis'] == '' ? '' : 'Kaufpreis: '.$objekte_grundstuecke_row['obj_kaufpreis'].' €<br>'); ?>
											<?php echo ($objekte_grundstuecke_row['obj_ort'] == '' ? '' : 'Ort: '.$objekte_grundstuecke_row['obj_ort']); ?>
										</p>
									</div>
								<div class="card-footer pt-1 pb-4 pl-4">
									<a href="/expose.php?obj_id=<?php echo $objekte_grundstueck_row['obj_id']; ?>" class="btn btn-outline-light text-uppercase shadow-none ml-1" title="<?php echo $objekte_grundstuecke_row['obj_titel']; ?>" target="_blank">Zum Exposé</a>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<?php if(mysqli_num_rows($objekte_anlage_sql) >= 1) { ?>
				<div class="tab-pane fade" id="anlage" role="tabpanel" aria-labelledby="anlage-tab">
					<div class="card-deck">
						<?php while ($objekte_anlage_row = mysqli_fetch_assoc($objekte_anlage_sql)) { ?>
							<div class="card einleitung-card shadow">
								<?php $img_sql = sql_select_where('img_bild', 'images', array('img_schluessel', 'img_item_id', 'img_titel'), array('objekt', $objekte_anlage_row['obj_id'], 'TITELBILD'), '', '');
								$img_row = mysqli_fetch_assoc($img_sql);
								if($img_row['img_bild'] == '') { 
									$img_sql = sql_select_where_limit('img_bild', 'images', array('img_schluessel', 'img_item_id', 'img_titel'), array('objekt', $objekte_anlage_row['obj_id'], 'BILD'), '', '', '1');
									$img_row = mysqli_fetch_assoc($img_sql);
								}
								?>
								<div class="card-img-top" style="background-image: url('/<?php if(mysqli_num_rows($img_sql) < 1) { echo 'img/logo_haus.svg'; } else { echo $img_row['img_bild']; } ?>');<?php if(mysqli_num_rows($img_sql) < 1) { echo 'background-size:30%'; } ?>" alt="Card image cap"></div>
									<div class="immoliste_content">
										<a href="/expose.php?obj_id=<?php echo $objekte_anlage_row['obj_id']; ?>" title="<?php echo $objekte_anlage_row['obj_titel']; ?>" target="_blank"><h5><?php echo $objekte_anlage_row['obj_titel']; ?></h5></a>
										<p>
											<?php echo ($objekte_anlage_row['obj_art'] == '' ? '' : 'Objektart: '.$objekte_anlage_row['obj_art'].'<br>'); ?>
											<?php echo ($objekte_anlage_row['obj_unterart'] == '' ? '' : 'Objektkategorie: '.$objekte_anlage_row['obj_unterart'].'<br>'); ?>
											<?php echo ($objekte_anlage_row['obj_vermarktungsart'] == '' ? '' : 'Vermarktungsart: '.$objekte_anlage_row['obj_vermarktungsart'].'<br>'); ?>
											<?php echo ($objekte_anlage_row['obj_anzahl_zimmer'] == '' ? '' : 'Zimmer: '.$objekte_anlage_row['obj_anzahl_zimmer'].'<br>'); ?>
											<?php echo ($objekte_anlage_row['obj_wohnflaeche'] == '' ? '' : 'Wohnfläche: '.$objekte_anlage_row['obj_wohnflaeche'].' m²<br>'); ?>
											<?php echo ($objekte_anlage_row['obj_gesamtflaeche'] == '' ? '' : 'Gesamtfläche: '.$objekte_anlage_row['obj_gesamtflaeche'].' m²<br>'); ?>
											<?php echo ($objekte_anlage_row['obj_kaufpreis'] == '' ? '' : 'Kaufpreis: '.$objekte_anlage_row['obj_kaufpreis'].' €<br>'); ?>
											<?php echo ($objekte_miete_wohnungen_row['obj_warmmiete'] == '' ? '' : 'Warmmiete (mtl.): '.number_format($objekte_miete_wohnungen_row['obj_warmmiete'],2,',','.').' €<br>'); ?>
											<?php echo ($objekte_anlage_row['obj_ort'] == '' ? '' : 'Ort: '.$objekte_anlage_row['obj_ort']); ?>
										</p>
									</div>
								<div class="card-footer pt-1 pb-4 pl-4">
									<a href="/expose.php?obj_id=<?php echo $objekte_anlage_row['obj_id']; ?>" class="btn btn-outline-light text-uppercase shadow-none ml-1" title="<?php echo $objekte_anlage_row['obj_titel']; ?>" target="_blank">Zum Exposé</a>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<?php if(mysqli_num_rows($objekte_gewerbe_sql) >= 1) { ?>
				<div class="tab-pane fade" id="gewerbe" role="tabpanel" aria-labelledby="gewerbe-tab">
					<div class="card-deck">
						<?php while ($objekte_gewerbe_row = mysqli_fetch_assoc($objekte_gewerbe_sql)) { ?>
							<div class="card einleitung-card shadow">
								<?php $img_sql = sql_select_where('img_bild', 'images', array('img_schluessel', 'img_item_id', 'img_titel'), array('objekt', $objekte_gewerbe_row['obj_id'], 'TITELBILD'), '', '');
								$img_row = mysqli_fetch_assoc($img_sql);
								if($img_row['img_bild'] == '') { 
									$img_sql = sql_select_where_limit('img_bild', 'images', array('img_schluessel', 'img_item_id', 'img_titel'), array('objekt', $objekte_gewerbe_row['obj_id'], 'BILD'), '', '', '1');
									$img_row = mysqli_fetch_assoc($img_sql);
								}
								?>
								<div class="card-img-top" style="background-image: url('/<?php if(mysqli_num_rows($img_sql) < 1) { echo 'img/logo_haus.svg'; } else { echo $img_row['img_bild']; } ?>');<?php if(mysqli_num_rows($img_sql) < 1) { echo 'background-size:30%'; } ?>" alt="Card image cap"></div>
									<div class="immoliste_content">
										<a href="/expose.php?obj_id=<?php echo $objekte_gewerbe_row['obj_id']; ?>" title="<?php echo $objekte_gewerbe_row['obj_titel']; ?>" target="_blank"><h5><?php echo $objekte_gewerbe_row['obj_titel']; ?></h5></a>
										<p>
											<?php echo ($objekte_gewerbe_row['obj_art'] == '' ? '' : 'Objektart: '.$objekte_gewerbe_row['obj_art'].'<br>'); ?>
											<?php echo ($objekte_gewerbe_row['obj_gesamtflaeche'] == '' ? '' : 'Gesamtfläche: '.$objekte_gewerbe_row['obj_gesamtflaeche'].' m²<br>'); ?>
											<?php echo ($objekte_gewerbe_row['obj_kaufpreis'] == '' ? '' : 'Kaufpreis: '.$objekte_gewerbe_row['obj_kaufpreis'].' €<br>'); ?>
											<?php echo ($objekte_miete_wohnungen_row['obj_warmmiete'] == '' ? '' : 'Warmmiete (mtl.): '.number_format($objekte_miete_wohnungen_row['obj_warmmiete'],2,',','.').' €<br>'); ?>
											<?php echo ($objekte_gewerbe_row['obj_ort'] == '' ? '' : 'Ort: '.$objekte_gewerbe_row['obj_ort']); ?>
										</p>
									</div>
								<div class="card-footer pt-1 pb-4 pl-4">
									<a href="/expose.php?obj_id=<?php echo $objekte_gewerbe_row['obj_id']; ?>" class="btn btn-outline-light text-uppercase shadow-none ml-1" title="<?php echo $objekte_gewerbe_row['obj_titel']; ?>" target="_blank">Zum Exposé</a>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</section>

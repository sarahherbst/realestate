<?php
	$page 		= 'karriere';
	$ite_rubrik = 'Karriere';

	require('connection.inc.php');
	require('function.inc.php');
	require('data.inc.php');

	$txt_sql 	= sql_select_where('all', 'texte', array('txt_status', 'txt_schluessel'), array('1', 'karriere'), '', '');
	$txt_row 	= mysqli_fetch_assoc($txt_sql);
	$img_sql 	= sql_select_where('all', 'images', array('img_status', 'img_schluessel'), array('1', 'karriere-header'), '', '');
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

<?php
	include('footer.php');
?>
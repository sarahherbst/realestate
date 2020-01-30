<?php
	$rub_id 		= $_GET['rub_id'];
	$art_id 		= $_GET['art_id'];

	$page 			= 'rub_'.$rub_id;
	$schluessel 	= 'artikel';

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
	$ite_rubrik 	= $rubrik_row['rub_name'];

	// Artikel auslesen
	$article_sql 	= sql_select_where('all', 'texte', array('txt_institut', 'txt_id', 'txt_rubrik', 'txt_schluessel', 'txt_status'), array($institut_id, $art_id, $rub_id, $schluessel, '1'), '', '' );
	$article_row 	= mysqli_fetch_assoc($article_sql);

	//Artikelbild auslesen
	$img_sql 		= sql_select_where('all', 'images', array('img_institut', 'img_rubrik', 'img_schluessel', 'img_item_id', 'img_status'), array($institut_id, $rub_id, $schluessel, $art_id, '1'), '', '');
	$img_row 		= mysqli_fetch_assoc($img_sql);

	include('header.php');
?>

<!-- Header (Logo, Überschrift, Einleitung) -->
<section class="bg-<?php echo $bg_color; ?> first-section">
	
	<!-- Überschrift & Einleitung-->
	<div class="container py-5 pt-md-1 pb-md-5">
		<span class="text-uppercase text-light"><?php echo $rubrik_row['rub_name']; ?></span>
		<h1 class="display-4 text-light"><?php echo $article_row['txt_titel']; ?></h1>
		<p class="h3 text-light font-weight-light"><?php echo $article_row['txt_einleitung']; ?></p>
		<?php if ($article_row['txt_conversion_titel'] != '') { ?>
			<?php if ($article_row['txt_conversion_titel'] != '') { ?>
				<br>
				<a href="/<?php echo $article_row['txt_conversion_ziel']; ?>" onclick="trackItem(this,'Artikel', 'Conversion Artikel');" class="btn btn-light text-<?php echo $text_color;?> text-uppercase" title="<?php echo $article_row['txt_conversion_titel']; ?>"  target="<?php echo (strpos($article_row['txt_conversion_ziel'],'form_') !== false ? '' : '_blank') ?>">
					<?php echo $article_row['txt_conversion_titel']; ?>
				</a>
			<?php } ?>
		<?php } ?>
	</div>
</section>

<!-- Inhalt -->
<div class="bg-white container p-3 p-sm-5 mb-0 mb-sm-5">
	<?php if ($img_row['img_bild'] != '') { ?> 
	<div class="float-lg-left w-lg-50 pr-lg-4">
	<img src="/<?php echo $img_row['img_bild']; ?>" class="img-fluid pt-3 pb-2" alt="<?php echo $img_row['img_titel']; ?>" title="<?php echo $img_row['img_titel']; ?>">
		<?php if ($img_row['img_bild'] != '') { ?>
		<p class="font-italic text-muted pb-3 pb-lg-0"><?php echo $img_row['img_beschreibung']; ?></p>
		<?php }	?>
	</div>
	<?php }	?>

	<?php echo str_replace('\r\n', '', $article_row['txt_beitrag']); ?>

	<?php if ($article_row['txt_conversion_titel'] != '') { ?>
	<br><br>
	<div class="col-12 text-center">
		<a href="/<?php echo $article_row['txt_conversion_ziel']; ?>" onclick="trackItem(this,'Artikel', 'Conversion Artikel');" class="btn btn-lg btn-<?php echo $text_color;?> text-uppercase" title="<?php echo $article_row['txt_conversion_titel']; ?>"  target="<?php echo (strpos($article_row['txt_conversion_ziel'],'form_') !== false ? '' : '_blank') ?>">
			<?php echo $article_row['txt_conversion_titel']; ?>
		</a>
	</div>
	<?php } ?>
</div>

<?php
	include('footer.php');
?>
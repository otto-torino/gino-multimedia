<?php
/**
* @file view/detail.php
* @ingroup multimedia
* @brief Template per la vista dettaglio media
*
* Variabili disponibili:
* - **section_id**: attributo id del tag section
* - **title**: titolo della vista
* - **insertion_date**: data inserimento media
* - **galleries**: array di array associativi di gallerie cui il media è associato. Elementi:   
*     - url: url della vista galleria 
*     - name: nome della galleria 
* - **media**: visualizzazione del media
* - **description**: descrizione del media
* - **tags**: tags del media
* - **credits**: credits del media
* - **license**: array associativo rapresentante la licenza del media. Elementi:
*     - url: url esterno con descrizione della licenza
*     - name: nome della licenza
* - **lat**: latitudine del media
* - **lng**: longitudine del media
*
* @version 0.1
* @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
* @authors Marco Guidotti guidottim@gmail.com
* @authors abidibo abidibo@gmail.com
*/
?>
<section id="<?= $section_id ?>">
<h1><?= $title ?></h1>
<p><?= _('Data di inserimento: ').$insertion_date ?></p>
<? if($galleries): ?>
	<p>
		<?= _('Gallerie: ') ?>
		<? $i = 0; ?>
		<? foreach($galleries as $g): ?>
			<? if($i) echo ", " ?><a href="<?= $g['url'] ?>"><?= $g['name'] ?></a>
			<? $i++ ?>
		<? endforeach ?>
	</p>
<? endif ?>
<div class="show_media">
	<?= $media ?>
</div>
<?= $description ?>
<? if($tags): ?>
	<p><?= _('Tags: ').$tags ?></p>
<? endif ?>
<? if($credits): ?>
	<p><?= $credits ?></p>
<? endif ?>
<? if($license): ?>
	<p><b><?= _('Licenza: ') ?><a href="<?= $license['url'] ?>" rel="external"><?= $license['name'] ?></a></p>
<? endif ?>

<!-- map -->
<? if($lat && $lng): ?>
<div id="map" style="width: 400px; height: 250px"></div>
<script>
var options = {
	center: new google.maps.LatLng(<?= $lat ?>, <?= $lng ?>),
	zoom: 12,
	mapTypeId: google.maps.MapTypeId.ROADMAP,
}
var map = new google.maps.Map($('map'), options);

var marker = new google.maps.Marker({
	position: new google.maps.LatLng(<?= $lat ?>, <?= $lng ?>),
	map: map
})

</script>
<? endif ?>

</section>

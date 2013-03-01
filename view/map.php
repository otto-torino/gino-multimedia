<?php
/**
* @file view/map.php
* @ingroup multimedia
* @brief Template per la vista mappa media geolocalizzati
*
* Variabili disponibili:
* - **section_id**: attributo id del tag section
* - **title**: titolo della vista
* - **media**: array associativo di media. elementi:   
*     - id: id del media
*     - name: nome
*     - lat: latitudine
*     - lng: longitudine
*     - url: url della pagina di dettaglio
*     - thumb_path: percorso relativo della thumb
*
* @version 0.1
* @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
* @authors Marco Guidotti guidottim@gmail.com
* @authors abidibo abidibo@gmail.com
*/
?>
<section id="<?= $section_id ?>">
<h1><?= $title ?></h1>
<div id="map" class="multimedia_map"></div>

<script>
window.addEvent('load', function() {

	var gmap = new Map('map', {
		cluster_title: '<?= jsVar(_('clicca per risolvere il gruppo di media')) ?>'
	});

	<? foreach($media as $m): ?>
		var gmappoint = new MapPoint('<?= $m['id'] ?>', '<?= $m['lat'] ?>', '<?= $m['lng'] ?>', {
			name: '<?= jsVar($m['name']) ?>',	
			url: '<?= $m['url'] ?>',	
			thumb: '<img src="<?= $m['thumb_path'] ?>" />',	
		});
		gmappoint.addToMap(gmap);
	<? endforeach ?>

});

</script>

</section>

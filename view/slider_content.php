<?php
/**
 * @file view/slider_content.php
 * @ingroup multimedia
 * @brief Template slider
 *
 * Variabili disponibili:
 * - **container_id**: attributo id del contenitore
 * - **images**: array di tag immagine
 * - **animation_effect_duration**: durata effetto animazione
 * - **auto_play**: auto play
 * - **show_ctrls**: mostrare controller navigazione immagini
 * - **mouseout_hide_ctrls**: nascondere controller al mouseout
 * - **effect**: nome effetto
 * - **orientation**: orientazione effetto
 * - **animation_interval**: intervallo immagini successive
 * - **pause_on_hover**: pausa intervallo al mouseover
 * - **slices**: numero di slices
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */
?>
<div id="<?= $container_id ?>" class="nivoo-slider">
<? foreach($images as $image): ?>
<?= $image ?>
<? endforeach ?>
</div>

<script type="text/javascript">
	window.addEvent('load', function() { 
		loadSlider(
			'<?= $container_id ?>', 
			<?= $animation_effect_duration ?>, 
			<?= $auto_play ? 'true' : 'false' ?>, 
			<?= $show_ctrls ? 'true' : 'false' ?>, 
			<?= $mouseout_hide_ctrls ? 'true' : 'false' ?>, 
			'<?= $effect ?>', 
			'<?= $orientation ?>', 
			<?= $animation_interval ?>, 
			<?= $pause_on_hover ? 'true' : 'false' ?>, 
			<?= $slices ?>
		);
	});
</script>

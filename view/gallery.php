<?php
/**
* @file view/gallery.php
* @ingroup multimedia
* @brief Template per la vista galleria
*
* Variabili disponibili:
* - **section_id**: attributo id del tag section
* - **title**: titolo della vista
* - **form_search**: form di ricerca media
* - **search_text**: parole chiave di ricerca 
* - **js_items**: array media in rappresentazione javascript pronti per essere passati a moogallery
*
* @version 0.1
* @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
* @authors Marco Guidotti guidottim@gmail.com
* @authors abidibo abidibo@gmail.com
*/
?>
<section id="<?= $section_id ?>">
<header>
	<h1 class="left"><?= $title ?></h1>
	<div class="right">
	<?= $form_search ?>
	</div>
	<div class="null"></div>
</header>
<? if($search_text): ?>
<p><?= _("Risultati ricerca")." '<b>".$search_text."</b>'" ?></p>
<? endif ?>
<div id="gallery_container">
</div>
<? if(count($js_items)): ?>
	<script>
	window.addEvent('load', function() {
	
		var mg_instance = new moogallery('gallery_container', 
	  		[ 
	   		<?= implode(',', $js_items) ?>			 
	  		], 
	  		{ 
	   			show_bullets: false 
			} 
		); 

	});
	</script>
<? endif ?>
</section>

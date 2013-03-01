<?php
/**
* @file view/galleries_1.php
* @ingroup multimedia
* @brief Template per la vista elenco gallerie 1 galleria per riga
*
* Variabili disponibili:
* - **section_id**: attributo id del tag section
* - **title**: titolo della vista
* - **form_search**: form di ricerca galleria
* - **search_text**: parole chiave di ricerca 
* - **rows**: array di righe ti tabella. Il codice è definito tramite template da opzioni
* - **pagination_navigation**: navigazione paginazione
* - **pagination_summary**: riassunto paginazione (1-10 di n)
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
<? if(count($rows)): ?>
	<table class="galleries">
	<? foreach($rows as $row): ?>
		<tr>
		<?= $row ?>
		</tr>
	<? endforeach ?>
	</table>
<? else: ?>
<p><?= _('La ricerca non ha prodotto risultati') ?></p>
<? endif ?>
<div class="pagination">
<div class="left">
	<?= $pagination_navigation ?>
</div>
<div class="right">
	<?= $pagination_summary ?>
</div>
<div class="null"></div>
</div>
</section>

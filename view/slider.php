<?php
/**
 * @file view/slider.php
 * @ingroup multimedia
 * @brief Template per la vista slider
 *
 * Variabili disponibili:
 * - **section_id**: attributo id del tag section
 * - **title**: titolo della vista
 * - **content**: contenuto deciso da opzioni
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */
?>
<section id="<?= $section_id ?>">
<? if($title): ?>
	<h1><?= $title ?></h1>
<? endif ?>
<?= $content ?>
</section>


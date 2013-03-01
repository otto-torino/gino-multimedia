<?php
/**
 * @file view/box.php
 * @ingroup multimedia
 * @brief Template per la vista box ultime gallerie modificate
 *
 * Variabili disponibili:
 * - **section_id**: attributo id del tag section
 * - **title**: titolo della vista
 * - **lis**: elementi della lista gallerie renderizzati secondo il template deciso da opzioni
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */
?>
<section id="<?= $section_id ?>">
<h1><?= $title ?></h1>
<?= $promoted ?>
<? if(count($lis)): ?>
<ul>
<? foreach($lis as $li): ?>
<li><?= $li ?></li>
<? endforeach ?>
</ul>
<p><a href="<?= $all_galleries_url ?>"><?= _('tutte le gallerie') ?></a></p>
<? endif ?>
</section>

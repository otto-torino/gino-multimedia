<?php
/**
 * @file class.multimediaItemAdminTable.php
 * Contiene la definizione ed implementazione della classe multimediaItemAdminTable.
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */

/**
 * \ingroup multimedia
 * Classe per la gestione del backoffice dei media (estensione della classe adminTable del core di GINO).
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */
class multimediaItemAdminTable extends adminTable {

	/**
	 * Costruttore 
	 * 
	 * @param multimedia $instance istanza del controller 
	 * @param array $opts array associativo di opzioni, vedere la clase adminTable di GINO
	 * @return istanza di multimediaItemAdminTable
	 */
	function __construct($instance, $opts = array()) {

		parent::__construct($instance, $opts);

	}

	/**
	 * Metodo chiamato al salvataggio di un media 
	 * 
	 * @param object $model istanza del media
	 * @param array $options opzioni del form
	 * @param array $options_element opzioni dei campi
	 * @access public
	 * @return void
	 */
	public function modelAction($model, $options=array(), $options_element=array()) {

		if(!$model->id) {
			$update_galleries = true;
		}
		else {
			$update_galleries = false;
		}

		$result = parent::modelAction($model, $options, $options_element);

		if(is_array($result) && isset($result['error'])) {
			return $result;
		}

		if($update_galleries) {
			foreach(explode(',', $model->galleries) as $gid) {
				$gallery = new multimediaGallery($gid, $this->_controller);
				$gallery->last_edit_date = date('Y-m-d H:i:s');
				$gallery->updateDbData();
			}
		}

		$model_tags = array();

		foreach(explode(',', $model->tags) as $tag) {
			$tag_id = multimediaTag::saveTag($this->_controller->getInstance(), $tag);
			if($tag_id) {
				$model_tags[] = $tag_id;
			}
		}

		return $model->saveTags($model_tags);

	}

}

?>

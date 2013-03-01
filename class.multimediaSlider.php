<?php
/**
 * @file class.multimediaSlider.php
 * Contiene la definizione ed implementazione della classe multimediaSlider.
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */

/**
 * \ingroup multimedia
 * Classe che rappresenta uno slider di immagini di una galleria.
 *
 * CAMPI   
 * - **id**
 * - **instance**: id dell'istanza del controller
 * - **tpl**: template
 * - **image_order**: ordinamento immagini
 * - **gallery**: galleria
 * - **animation_effect_duration**: durata effetto animazione
 * - **auto_play**: auto play animazione
 * - **show_ctrls**: mostra i controlli per navigare attraverso le immagini
 * - **mouseout_hide_ctrls**: nascondi i controlli al mouseout
 * - **transition_effect**: effetto della transizione
 * - **animation_interval**: intervallo animazione
 * - **pause_interval_mouseover**: pausa dell'intervallo al mouseover
 * - **slices**: numero di slices utilizzate per l'effetto
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */

class multimediaSlider extends propertyObject {

	private $_controller;
	private $_transition_effects;
	private $_image_orders;

	public static $tbl_slider = "multimedia_slider";

	/**
	 * Costruttore
	 * 
	 * @param integer $id valore ID del record
	 * @param object $instance istanza del controller
	 */
	function __construct($id, $instance) {

		$this->_controller = $instance;
		$this->_tbl_data = self::$tbl_slider;

		$this->_fields_label = array(
			'tpl'=>array(_("Template"), _('Inserire lo slider con la stringa {{ slider }}')),
			'image_order'=>_('Ordinamento immagini'),
			'gallery'=>_('Galleria'),
			'animation_effect_duration'=>_('Durata effetto di animazione (ms)'),
			'auto_play'=>_('Auto play'),
			'show_ctrls'=>_('Mostra controlli navigazione'),
			'mouseout_hide_ctrls'=>_('Nascondi controlli navigazione al mouseout'),
			'transition_effect'=>_('Effetto di transizione'),
			'animation_interval'=>_('Intervallo immagini successive (ms)'),
			'pause_interval_mouseover'=>_('Pausa dell\'intervallo al mouseover'),
			'slices'=>_('Numero di slices')
		);

		$this->_image_orders = array(
			1 => _('random'),
			2 => _('data decrescente'),
			3 => _('data crescente')
		);

		$this->_transition_effects = array(
			1=>"fade",
			2=>"fold",
			3=>"random",
			4=>"horiz sliceLeftDown",
			5=>"horiz sliceLeftUp",
			6=>"horiz sliceLeftRightDown",
			7=>"horiz sliceLeftRightUp",
			8=>"horiz sliceRightDown",
			9=>"horiz sliceRightUp",
			10=>"horiz wipeDown",
			11=>"horiz wipeUp",
			12=>"vert sliceDownLeft",
			13=>"vert sliceDownRight",
			14=>"vert sliceUpDownLeft",
			15=>"vert sliceUpDownRight",
			16=>"vert sliceUpLeft",
			17=>"vert sliceUpRight",
			18=>"vert wipeLeft",
			19=>"vert wipeRight"		
		);

		parent::__construct($id);

		$this->_model_label = $this->id ? $this->id : '';
 
	}

	/**
	 * Sovrascrive la struttura di default
	 * 
	 * @see propertyObject::structure()
	 * @param integer $id
	 * @return array
	 */
	public function structure($id) {

		$structure = parent::structure($id);

		$structure['auto_play'] = new booleanField(array(
			'name'=>'auto_play', 
			'required'=>true,
			'label'=>$this->_fields_label['auto_play'], 
			'enum'=>array(1 => _('si'), 0 => _('no')), 
			'default'=>0,
			'value'=>$this->auto_play 
		));

		$structure['show_ctrls'] = new booleanField(array(
			'name'=>'show_ctrls', 
			'required'=>true,
			'label'=>$this->_fields_label['show_ctrls'], 
			'enum'=>array(1 => _('si'), 0 => _('no')), 
			'default'=>0,
			'value'=>$this->show_ctrls 
		));

		$structure['mouseout_hide_ctrls'] = new booleanField(array(
			'name'=>'mouseout_hide_ctrls', 
			'required'=>true,
			'label'=>$this->_fields_label['mouseout_hide_ctrls'], 
			'enum'=>array(1 => _('si'), 0 => _('no')), 
			'default'=>0,
			'value'=>$this->mouseout_hide_ctrls 
		));

		$structure['pause_interval_mouseover'] = new booleanField(array(
			'name'=>'pause_interval_mouseover', 
			'required'=>true,
			'label'=>$this->_fields_label['pause_interval_mouseover'], 
			'enum'=>array(1 => _('si'), 0 => _('no')), 
			'default'=>0,
			'value'=>$this->pause_interval_mouseover 
		));

		$structure['gallery'] = new foreignKeyField(array(
                        'name'=>'gallery', 
			'value'=>$this->gallery, 
			'label'=>$this->_fields_label['gallery'], 
			'lenght'=>11, 
			'fkey_table'=>multimediaGallery::$tbl_gallery, 
			'fkey_id'=>'id', 
			'fkey_field'=>'name', 
			'fkey_where'=>'instance=\''.$this->_controller->getInstance().'\'', 
			'fkey_order'=>'name'

                ));

		$structure['image_order'] = new enumField(array(
			'name'=>'image_order', 
			'required'=>true,
			'label'=>$this->_fields_label['image_order'], 
			'enum'=>$this->_image_orders, 
			'default'=>1,
			'value'=>$this->image_order 
		));

		$structure['transition_effect'] = new enumField(array(
			'name'=>'transition_effect', 
			'required'=>true,
			'label'=>$this->_fields_label['transition_effect'], 
			'enum'=>$this->_transition_effects, 
			'default'=>1,
			'value'=>$this->transition_effect 
		));

		return $structure;
	}

	/**
	 * Restituisce l'effetto e l'orientazione da usare per il codice js 
	 * 
	 * @access public
	 * @return array (effetto, orientazione)
	 */
	public function getEffectOrientation() {

		if(preg_match("#(horiz|vert) (.*)#", $this->_transition_effects[$this->transition_effect], $matches)) {
			$effect = $matches[2];
			$orientation = $matches[1] == 'horiz' ? "horizontal" : "vertical";
		}
		else {
			$effect = $this->_transition_effects[$this->transition_effect];
			$orientation = 'horizontal';
		}

		return array($effect, $orientation);
	}

	/**
	 * Istanze legate all'stanza del controller 
	 * 
	 * @param multimedia $controller istanza del controller
	 * @return istanza di multimediaSlider
	 */
	public static function getFromInstance($controller) {

		$db = db::instance();
		$rows = $db->select('id', self::$tbl_slider, "instance='".$controller->getInstance()."'", null, null);
		if($rows && count($rows)) {
			return new multimediaSlider($rows[0]['id'], $controller);
		}

		return new multimediaSlider(null, $controller);

	}

	/**
	 * Restituisce le immagini dello slider 
	 * 
	 * @param multimedia $controller istanza del controller
	 * @param string $private includere immagini private
	 * @return array di istanze di multimediaImage
	 */
	public function getItems($controller, $private) {

		$res = array();

		if(!$this->id) {
			return $res;
		}

		$gallery = new multimediaGallery($this->gallery, $controller);

		if($this->image_order == 3) {
			$order = 'insertion_date ASC';
		}
		else {
			$order = 'insertion_date DESC';
		}

		$items = $gallery->getItems($controller, array(
			'order' => $order,
			'private' => $private,
			'published' => true,
			'where_c' => "type='".IMAGE_CHOICE."'"
		));

		if($this->_image_orders == 1) {
			shuffle($items);
		}

		return $items;

	}

}

?>

<?php
/**
 * @file class.multimediaLicense.php
 * Contiene la definizione ed implementazione della classe multimediaLicense.
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */

/**
 * \ingroup multimedia
 * Classe che rappresenta le licenze dei media.
 *
 * CAMPI  
 * - **id**
 * - **instance**: id dell'istanza del controllore
 * - **name**: nome
 * - **description**: descrizione
 * - **url**: url esterno con descrizione della licenza
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */
class multimediaLicense extends propertyObject {

	private $_controller;
	public static $tbl_license = "multimedia_license";
	
	/**
	 * Costruttore
	 * 
	 * @param integer $id valore ID del record
	 * @param object $instance istanza del controller
	 */
	function __construct($id, $instance) {
		
		$this->_controller = $instance;
		$this->_tbl_data = self::$tbl_license;
		
		$this->_fields_label = array(
			'name'=>_("Nome"),
			'description'=>_("Descrizione"),
			'url'=>_("Url")
		);
		
		parent::__construct($id);
		
		$this->_model_label = $this->id ? $this->name : '';
	}
	
}

?>

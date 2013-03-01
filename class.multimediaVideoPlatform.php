<?php
/**
 * @file class.multimediaVideoPlatform.php
 * Contiene la definizione ed implementazione della classe multimediaVideoPlatform.
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */

/**
 * \ingroup multimedia
 * Classe che rappresenta una piattaforma di streaming video.
 *
 * CAMPI  
 * - **id**
 * - **instance**: id dell'istanza del controller
 * - **name**: nome della piattaforma video (il nome viene usato da moogallery controllare quali supporta)
 * - **base_url**: indirizzo base cui aggoiungere il codice video per la visualizzazione di un media di tipo video
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */
class multimediaVideoPlatform extends propertyObject {

	private $_controller;
	public static $tbl_video_platform = "multimedia_video_platform";

	/**
	 * Costruttore
	 * 
	 * @param integer $id valore ID del record
	 * @param object $instance istanza del controller
	 */
	function __construct($id, $instance) {

		$this->_controller = $instance;
		$this->_tbl_data = self::$tbl_video_platform;

		$this->_fields_label = array(
				'name'=>_("Nome"),
				'base_url'=>_("Url base cui aggiungere il codice video")
				);

		parent::__construct($id);

		$this->_model_label = $this->id ? $this->name : '';
	}

}

?>

<?php
/**
 * @file class.multimediaTag.php
 * Contiene la definizione ed implementazione della classe multimediaTag.
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */

/**
 * \ingroup multimedia
 * Classe per la gestione di tag associati ai media.
 *
 * CAMPI  
 * - **id**
 * - **instance**: id dell'istanza del controller
 * - **name**: nome tag
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */
class multimediaTag extends propertyObject {

	private $_controller;
	public static $_tbl_tag = "multimedia_tag";
	
	/**
	 * Costruttore
	 * 
	 * @param integer $id valore ID del record
	 * @param object $instance istanza del controller
	 */
	function __construct($id, $instance) {
		
		$this->_controller = $instance;
		$this->_tbl_data = self::$_tbl_tag;
		
		$this->_fields_label = array(
			'name'=>_("Nome")
		);
		
		parent::__construct($id);
		
		$this->_model_label = $this->id ? $this->name : '';
	}

	/**
	 * Restituisce una lista di tutti i tag presenti nella tabella 
	 * 
	 * @param mixed $instance istanza del controller
	 * @param array $options opzioni
	 * @return array contenente tutti i tag ineriti nella tabella
	 */
	public static function getAllList($instance, $options) {

		$db = db::instance();

		$res = array();
		$rows = $db->select(array('id', 'name'), self::$_tbl_tag, "instance='$instance'", 'name', null);
		if(count($rows)) {
			foreach($rows as $row) {
				if(gOpt('jsescape', $options, false)) {
					$name = jsVar($row['name']);
				}
				else {
					$name = htmlChars($row['name']);
				}
				$res[$row['id']] = $name;
			}
		}

		return $res;
		
	}

	/**
	 * Inserisce un tag se non ancora presente 
	 * 
	 * @param mixed $instance istanza del controller
	 * @param mixed $tag il tag da inserire
	 * @return id del record contenente il tag
	 */
	public static function saveTag($instance, $tag) {

		$db = db::instance();

		if($tag == '') return null;

		$rows = $db->select('id', self::$_tbl_tag, "instance='$instance' AND name='$tag'", null);
		if(count($rows) and $rows) {
			return $rows[0]['id'];
		}
		else {
			$query = "INSERT INTO ".self::$_tbl_tag." (instance, name) VALUES ('$instance', '$tag')";
			$res = $db->actionquery($query);
			return $db->getlastid(self::$_tbl_tag);
		}

	}
	
}

?>

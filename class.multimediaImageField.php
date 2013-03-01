<?php
/**
 * @file class.multimediaImageField.php
 * @brief Contiene la classe multimediaImageField
 *
 * @copyright 2012 Otto srl (http://www.opensource.org/licenses/mit-license.php) The MIT License
 * @author marco guidotti guidottim@gmail.com
 * @author abidibo abidibo@gmail.com
 */

/**
 * @brief Campo di tipo IMMAGINE dove il ridimensionamento avviene sul lato lungo (estensione di imageField del core di GINO)
 * 
 * @copyright 2012 Otto srl (http://www.opensource.org/licenses/mit-license.php) The MIT License
 * @author marco guidotti guidottim@gmail.com
 * @author abidibo abidibo@gmail.com
 */
class multimediaImageField extends imageField {

	/**
	 * Ridimensionamento in px del lato lungo dell'immagine 
	 */
	private $_side_dimension;

	/**
	 * Creazione thumb (alla quale applicare il ridimensionamento) 
	 */
	private $_apply_on_thumb;

	/**
	 * Costruttore 
	 * 
	 * @param mixed $options opzioni
	 * @return void
	 */
	function __construct($options) {

		parent::__construct($options);

		$this->_thumb = false;
		
		$this->_side_dimension = gOpt('side_dimension', $options, 200);
		$this->_apply_on_thumb = gOpt('apply_on_thumb', $options, false);
	}

	/**
	 * Salva le immagini eventualmente ridimensionandole
	 * 
	 * Se @b thumb_width e @b thumb_height sono nulli, il thumbnail non viene generato
	 * 
	 * @param string $filename nome del file
	 * @param string $prefix_file prefisso da aggiungere al file
	 * @param string $prefix_thumb prefisso da aggiungere al thumbnail
	 * @param integer $new_width larghezza dell'immagine
	 * @param integer $new_height altezza dell'immagine
	 * @param integer $thumb_width larghezza del thumbnail
	 * @param integer $thumb_height altezza del thumbnail
	 * @return boolean
	 */
	protected function saveImage($filename, $prefix_file, $prefix_thumb, $new_width, $new_height, $thumb_width, $thumb_height){

		$thumb = (is_null($thumb_width) && is_null($thumb_height) && !$this->_apply_on_thumb) ? false : true;
		$file = $this->_directory.$filename;
		list($im_width, $im_height, $type) = getimagesize($file);

		if($im_width >= $im_height) {
			if($this->_apply_on_thumb) {
				$thumb_width = $this->_side_dimension;
				$thumb_height = null;
			}
			else {
				$new_width = $this->_side_dimension;
				$new_height = null;
			}
		}
		else {
			if($this->_apply_on_thumb) {
				$thumb_height = $this->_side_dimension;
				$thumb_width = null;
			}
			else {
				$new_height = $this->_side_dimension;
				$new_width = null;
			}
		}

		return parent::saveImage($filename, $prefix_file, $prefix_thumb, $new_width, $new_height, $thumb_width, $thumb_height);
	}
}

?>

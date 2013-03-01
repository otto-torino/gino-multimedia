<?php
/**
 * @file class.multimediaImage.php
 * Contiene la definizione ed implementazione della classe multimediaImage.
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */

/**
 * \ingroup multimedia
 * Classe che rappresenta un media di tipo immagine.
 *
 * CAMPI  
 * Tutti i campi descritti in multimediaItem più i seguenti:
 * - **img_filename**: nome file immagine. viene generata anche una thumb con prefisso 'thumb_'
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */
class multimediaImage extends multimediaItem {

	function __construct($id, $instance) {

		parent::__construct($id, $instance);

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
		
		$structure['type'] = new hiddenField(array(
			'name'=>'type', 
			'required'=>true,
			'label'=>$this->_fields_label['type'], 
			'value'=>IMAGE_CHOICE 
		));

		$base_path = $this->_controller->getBaseAbsPath('image');

		$structure['img_filename'] = new multimediaImageField(array(
                        'name'=>'img_filename', 
                        'value'=>$this->img_filename, 
                        'label'=>$this->_fields_label['img_filename'], 
                        'lenght'=>100, 
                        'extensions'=>array('jpg', 'png'), 
                        'path'=>$base_path,
			'apply_on_thumb' => true,
			'width' => $this->_controller->getImageMaxWidth(),
			'side_dimension'=>$this->_controller->getThumbDimension()
                ));

		return $structure;
	}

	/**
	 * Path relativo della thumb associata 
	 * 
	 * @param multimedia $controller istanza del controller
	 * @return path relativo della thumb
	 */
	public function thumbPath($controller) {

		if($this->thumb) {
			return $controller->getBasePath('thumb').'/'.$this->thumb;
		}
		elseif($this->img_filename) {
			return $controller->getBasePath('image').'/thumb_'.$this->img_filename;
		}
		else {
			return $controller->defaultImageThumbPath();
		}

	}

	/**
	 * Codice js che rappresenta un item di moogallery 
	 * 
	 * @param multimedia $controller istanza del controller
	 * @access public
	 * @return codice js
	 */
	public function getMoogalleryListJs($controller, $media_base_url) {

		$name = "<a href=\"".$media_base_url.'/'.$this->id."\">".$this->name."</a>";

		$description = $this->description;
		if($this->tags) { 
			$description .= "<p>"._('Tag:').' '.$this->tags."</p>";
		}

		$credits = $this->credits;
		if($this->license) {
			$license = new multimediaLicense($this->license, $controller);
			$credits .= "<br />"._("Licenza: ")."<a href=\"".$license->url."\" target=\"_blank\">".$license->name."</a>";
		}

		$js = "{
			thumb: '".$this->thumbPath($controller)."',
			img: '".$controller->getBasePath('image')."/".$this->img_filename."',
			title: '".jsVar($name)."',
			description: '".jsVar($description)."',
			credits: '".jsVar($credits)."'
		}";

		return $js;

	}

	/**
	 * Mostra il media 
	 * 
	 * @param multimedia $controller istanza del controller
	 * @return void
	 */
	public function show($controller) {

		$buffer = "<img src=\"".$controller->getBasePath('image')."/".$this->img_filename."\" alt=\"".jsVar($this->name)."\" title=\"".jsVar($this->name)."\"/>";

		return $buffer;

	}
}

?>

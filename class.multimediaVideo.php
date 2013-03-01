<?php
/**
 * @file class.multimediaVideo.php
 * Contiene la definizione ed implementazione della classe multimediaVideo.
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */

/**
 * \ingroup multimedia
 * Classe che rappresenta un media di tipo video (streaming da diverse piattaforme video).
 *
 * CAMPI  
 * Tutti i campi descritti in multimediaItem più i seguenti:
 * - **video_code**: codice video (da aggiungere all'url base)
 * - **video_platform**: piattaforma video (chiave esterna)
 * - **video_width**: larghezza
 * - **video_height**: altezza
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */
class multimediaVideo extends multimediaItem {

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
			'value'=>VIDEO_CHOICE 
		));

		$base_path = $this->_controller->getBaseAbsPath('video');

		$structure['video_platform'] = new foreignKeyField(array(
                        'name'=>'video_platform', 
			'value'=>$this->video_platform, 
			'label'=>$this->_fields_label['video_platform'], 
			'lenght'=>11, 
			'fkey_table'=>multimediaVideoPlatform::$tbl_video_platform, 
			'fkey_id'=>'id', 
			'fkey_field'=>'name', 
			'fkey_where'=>'instance=\''.$this->_controller->getInstance().'\'', 
			'fkey_order'=>'name'

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
		else {
			return $controller->defaultVideoThumbPath();
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

		$video_platform = new multimediaVideoPlatform($this->video_platform, $controller);

		$js = "{
			thumb: '".$this->thumbPath($controller)."',
			".$video_platform->name.": '".$this->video_code."',
			video_width: ".$this->video_width.",
			video_height: ".$this->video_height.",
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

		$video_platform = new multimediaVideoPlatform($this->video_platform, $controller);

		$buffer = "<iframe frameborder=\"0\" width=\"".$this->video_width."\" height=\"".$this->video_height."\" src=\"".$video_platform->base_url.$this->video_code."\">";
		$buffer .= _("Il tuo browser non supporta gli iframe");
		$buffer .= "</iframe>";

		return $buffer;

	}
}

?>

<?php
/**
 * @file class.multimediaAudio.php
 * Contiene la definizione ed implementazione della classe multimediaAudio.
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */

/**
 * \ingroup multimedia
 * Classe che rappresenta un media di tipo audio.
 *
 * CAMPI  
 * Tutti i campi descritti in multimediaItem più i seguenti:
 * - **mpeg_filename**: nome file mpeg (mp3)
 * - **ogg_filename**: nome file ogg
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */
class multimediaAudio extends multimediaItem {

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
			'value'=>AUDIO_CHOICE 
		));

		$base_path = $this->_controller->getBaseAbsPath('audio');

		$structure['mpeg_filename'] = new fileField(array(
                        'name'=>'mpeg_filename', 
                        'value'=>$this->mpeg_filename, 
                        'label'=>$this->_fields_label['mpeg_filename'], 
                        'lenght'=>100, 
                        'extensions'=>array('mp3'), 
                        'path'=>$base_path
                ));

		$structure['ogg_filename'] = new fileField(array(
                        'name'=>'ogg_filename', 
                        'value'=>$this->ogg_filename, 
                        'label'=>$this->_fields_label['ogg_filename'], 
                        'lenght'=>100, 
                        'extensions'=>array('ogg'), 
                        'path'=>$base_path
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
			return $controller->defaultAudioThumbPath();
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
			mpeg: '".($this->mpeg_filename ? $controller->getBasePath('audio')."/".$this->mpeg_filename : '')."',
			ogg: '".($this->ogg_filename ? $controller->getBasePath('audio')."/".$this->ogg_filename : '')."',
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

		$buffer = "<audio controls=\"controls\">";
		if($this->ogg_filename) {
			$buffer .= "<source type=\"audio/ogg\" src=\"".$controller->getBasePath('audio')."/".$this->ogg_filename."\">";
		}
		if($this->mpeg_filename) {
			$buffer .= "<source type=\"audio/mpeg\" src=\"".$controller->getBasePath('audio')."/".$this->mpeg_filename."\">";
		}
		$buffer .= _("Il tuo browser non supporta l'elemento audio");
		$buffer .= "</audio>";

		return $buffer;

	}

}

?>

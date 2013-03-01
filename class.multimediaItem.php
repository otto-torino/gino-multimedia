<?php
/**
* @file class.multimediaItem.php
* Contiene la definizione ed implementazione della classe multimediaItem.
*
* @version 0.1
* @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
* @authors Marco Guidotti guidottim@gmail.com
* @authors abidibo abidibo@gmail.com
*/

/**
 * \ingroup multimedia
 * Classe ancestor che rappresenta un media.
 *
 * CAMPI   
 * - **id**
 * - **instance**: id dell'istanza del controller
 * - **type**: tipologia media (1: immagine, 2: video, 3: audio)
 * - **name**: nome
 * - **galleries**: elenco di id di gallerie separati da virgole cui il media è associato
 * - **description**: descrizione
 * - **tags**: tag separati da virgole
 * - **credits**: credits
 * - **license**: licenza (chiave esterna)
 * - **lat**: latitudine (geolocalizzazione)
 * - **lng**: longitudine (geolocalizzazione)
 * - **thumb**: thumbnail
 * - **insertion_date**: data di inserimento
 * - **last_edit_date**: data di ultima modifica
 * - **published**: pubblicazione
 * - **private**: visibile solo agli utenti appartenenti al gruppo di visualizzazione contenuti privati
 * 
 * A questo vanno aggiunti i campi specifici per ogni tipologia di media, vedere le relative classi per i dettagli
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */
class multimediaItem extends propertyObject {

	protected static $_extension_thumb = array('jpg', 'png');

	protected $_controller;
	public static $tbl_item = "multimedia_item";
	protected $_tbl_item_tag;

	/**
	 * Costruttore
	 * 
	 * @param integer $id valore ID del record
	 * @param object $instance istanza del controller
	 */
	function __construct($id, $instance) {

		$this->_controller = $instance;
		$this->_tbl_data = self::$tbl_item;

		$this->_fields_label = array(
			'type'=>_('Tipologia'),
			'name'=>_("Nome"),
			'galleries'=>_("Gallerie"),
			'description'=>_('Descrizione'),
			'tags'=>array(_("Tag"), _("inserire tag separati da virgole, utilizzare la funzione di autocompletamento quando possibile.")),
			'credits'=>_('Credits'),
			'license'=>_('Licenza'),
			'lat'=>_('Latitudine'),
			'lng'=>_('Longitudine'),
			'thumb'=>array(_('Thumbnail'), _('viene inserito un thumbnail di default oppure il thumb dell\'immagine inserita se lasciato vuoto')),
			'insertion_date'=>_('Data di inserimento'),
			'last_edit_date'=>_('Data di ultima modifica'),
			'published'=>_('Pubblicato'),
			'private'=>array(_('Privato'), _('i media privati saranno visibili solamente agli utenti iscritti al gruppo di visualizzazione di contenuti provati')),
			'img_filename'=>_('File'),
			'video_code'=>_('Codice video'),
			'video_platform'=>_('Piattaforma video'),
			'video_width'=>_('Larghezza video'),
			'video_height'=>_('Altezza video'),
			'mpeg_filename'=>_('File mpeg'),
			'ogg_filename'=>_('File ogg')
		);

		parent::__construct($id);

		$this->_model_label = $this->id ? $this->name : '';
		$this->_tbl_item_tag = 'multimedia_item_tag';
	}

	/**
	 * Setter per la proprietà tags 
	 * 
	 * @param string $tags tag separati da virgole
	 * @return void
	 */
	public function setTags($tags) {
		
		$new_string_tags = array();

		foreach(explode(',', $tags) as $tag) {
			$tag = trim($tag);
			$new_string_tags[] = $tag;
		}

		$this->_p['tags'] = implode(',', $new_string_tags);
		$this->_chgP[] = 'tags';
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

		$structure['galleries'] = new manyToManyField(array(
			'name'=>'galleries', 
			'value'=>explode(',', $this->galleries), 
			'label'=>$this->_fields_label['galleries'], 
			'lenght'=>255, 
			'fkey_table'=>multimediaGallery::$tbl_gallery, 
			'fkey_id'=>'id', 
			'fkey_field'=>'name', 
			'fkey_where'=>'instance=\''.$this->_controller->getInstance().'\'', 
			'fkey_order'=>'name',
			'table'=>$this->_tbl_data 
		));
		
		$structure['license'] = new foreignKeyField(array(
			'name'=>'license', 
			'value'=>$this->license, 
			'label'=>$this->_fields_label['license'], 
			'lenght'=>11, 
			'fkey_table'=>multimediaLicense::$tbl_license, 
			'fkey_id'=>'id', 
			'fkey_field'=>'name', 
			'fkey_where'=>'instance=\''.$this->_controller->getInstance().'\'', 
			'fkey_order'=>'name'
		));

		$structure['published'] = new booleanField(array(
			'name'=>'published', 
			'required'=>true,
			'label'=>$this->_fields_label['published'], 
			'enum'=>array(1 => _('si'), 0 => _('no')), 
			'default'=>0,
			'value'=>$this->published, 
			'table'=>$this->_tbl_data 
		));

		$structure['private'] = new booleanField(array(
			'name'=>'private', 
			'required'=>true,
			'label'=>$this->_fields_label['private'], 
			'enum'=>array(1 => _('si'), 0 => _('no')), 
			'default'=>0, 
			'value'=>$this->private, 
			'table'=>$this->_tbl_data 
		));

		$structure['insertion_date'] = new datetimeField(array(
			'name'=>'insertion_date', 
			'required'=>true,
			'label'=>$this->_fields_label['insertion_date'], 
			'auto_now'=>false, 
			'value'=>$this->insertion_date 
		));

		$base_path = $this->_controller->getBaseAbsPath('thumb');

		$structure['thumb'] = new multimediaImageField(array(
                        'name'=>'thumb', 
                        'value'=>$this->thumb, 
                        'label'=>$this->_fields_label['thumb'], 
                        'lenght'=>100, 
                        'extensions'=>self::$_extension_thumb, 
                        'path'=>$base_path, 
                        'resize'=>true,
			'side_dimension'=>$this->_controller->getThumbDimension()
                ));

		return $structure;
	}

	/**
	 * Restituisce un'istanza della sottoclasse appropriata a seconda della proprietà type 
	 * 
	 * @param int $id id del media
	 * @param multimedia $controller istanza del controllore
	 * @access public
	 * @return istanza del media (multimediaAudio | multimediaVideo | multimediaImage)
	 */
	public static function getObject($id, $controller) {

		$item = new multimediaItem($id, $controller);
		
		if($item->type == AUDIO_CHOICE) {
			return new multimediaAudio($id, $controller);
		}
		elseif($item->type == VIDEO_CHOICE) {
			return new multimediaVideo($id, $controller);
		}
		elseif($item->type == IMAGE_CHOICE) {
			return new multimediaImage($id, $controller);
		}

	}

	/**
	 * Restituisce oggetti di tipo media 
	 * 
	 * @param multimedia $instance_obj istanza del controller 
	 * @param array $options array associativo di opzioni 
	 * @return array di istanze di tipo media
	 */
	public static function get($instance_obj, $options = null) {

		$res = array();

		$private = gOpt('private', $options, false);
		$published = gOpt('published', $options, true);
		$geolocalization = gOpt('geolocalization', $options, false);
		$gallery = gOpt('gallery', $options, null);
		$order = gOpt('order', $options, 'name');
		$limit = gOpt('limit', $options, null);

		$db = db::instance();
		$selection = array('id', 'type');
		$table = self::$tbl_item;
		$where_arr = array("instance='".$instance_obj->getInstance()."'");
		if(!$private) {
			$where_arr[] = "private='0'";
		} 
		if($published) {
			$where_arr[] = "published='1'";
		}
		if($gallery) {
			$where_arr[] = "galleries REGEXP '[[:<:]]".$gallery."[[:>:]]'";
		}
		if($geolocalization) {
			$where_arr[] = "lat != '' AND lng != ''";
		}

		$where = implode(' AND ', $where_arr);

		$rows = $db->select($selection, $table, $where, $order, $limit);
		if(count($rows)) {
			foreach($rows as $row) {
				if($row['type'] == IMAGE_CHOICE) {
					$res[] = new multimediaImage($row['id'], $instance_obj);
				}
				elseif($row['type'] == VIDEO_CHOICE) {
					$res[] = new multimediaVideo($row['id'], $instance_obj);
				}
				elseif($row['type'] == AUDIO_CHOICE) {
					$res[] = new multimediaAudio($row['id'], $instance_obj);
				}
			}
		}

		return $res;

	}

	/**
	 * Salva i tag associati al media 
	 * 
	 * @param mixed $tags array di id di tag
	 * @return il risultato dell'operazione
	 */
	public function saveTags($tags) {

		$db = db::instance();

		if(!count($tags)) {
			return true;
		}

		$query = "DELETE FROM ".$this->_tbl_item_tag." WHERE item='".$this->id."'";
		$res = $db->actionquery($query);

		$inserts = array();
		foreach($tags as $tag) {
			$inserts[] = "('".$this->id."', '".$tag."')";
		}

		$query = "INSERT INTO ".$this->_tbl_item_tag." (item, tag) VALUES ".implode(',', $inserts);
		return $db->actionquery($query);

	}

	/**
	 * Elimina l'oggetto 
	 * 
	 * @return il risultato dell'operazione
	 */
	public function delete() {

		// delete tags
		$db = db::instance();
		$query = "DELETE FROM ".$this->_tbl_item_tag." WHERE item='".$this->id."'";

		$db->actionquery($query);

		return parent::delete();

	}

}

?>

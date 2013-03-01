<?php
/**
 * @file class.multimediaGallery.php
 * Contiene la definizione ed implementazione della classe multimediaGallery.
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */

/**
 * \ingroup multimedia
 * Classe che rappresenta una galleria multimediale (raccolta di media).
 *
 * CAMPI   
 * - **id**
 * - **instance**: id dell'istanza del controller
 * - **name**: nome galleria
 * - **slug**: permalink
 * - **description**: descrizione
 * - **thumb**: immagine usata come thumb
 * - **published**: pubblicazione galleria
 * - **private**: visibile solo a utenti iscritti al gruppo 'visualizza contenuti privati'
 * - **insertion_date**: data di inserimento
 * - **last_edit_date**: data di ultima associazione di un media
 *
 * @version 0.1
 * @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
 * @authors Marco Guidotti guidottim@gmail.com
 * @authors abidibo abidibo@gmail.com
 */
class multimediaGallery extends propertyObject {

	private $_controller;

	protected static $_extension_thumb = array('jpg', 'png');
	public static $tbl_gallery = "multimedia_gallery";

	/**
	 * Costruttore
	 * 
	 * @param integer $id valore ID del record
	 * @param object $instance istanza del controller
	 */
	function __construct($id, $instance) {

		$this->_controller = $instance;
		$this->_tbl_data = self::$tbl_gallery;

		$this->_fields_label = array(
			'name'=>_("Nome"),
			'slug'=>array(_("Slug"), _('utilizzato per creare un permalink alla risorsa')),
			'description'=>_('Descrizione'),
			'thumb'=>array(_('Thumbnail'), _('viene inserito il thumbnail del primo media contenuto se lasciato vuoto')),
			'published'=>_('Pubblicata'),
			'private'=>array(_('Privata'), _('le gallerie private saranno visibili solamente agli utenti iscritti al gruppo di visualizzazione di contenuti provati')),
			'insertion_date'=>_('Data di inserimento'),
			'last_edit_date'=>_('Data di ultima modifica'),
			'promoted'=>array(_('Promossa'), _('La più recente galleria promossa viene inserita in testa alla vista "ultime gallerie"'))
		);

		parent::__construct($id);

		$this->_model_label = $this->id ? $this->name : '';
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
		
		$structure['published'] = new booleanField(array(
			'name'=>'published', 
			'required'=>true,
			'label'=>$this->_fields_label['published'], 
			'enum'=>array(1 => _('si'), 0 => _('no')), 
			'default'=>0,
			'value'=>$this->published 
		));

		$structure['private'] = new booleanField(array(
			'name'=>'private', 
			'required'=>true,
			'label'=>$this->_fields_label['private'], 
			'enum'=>array(1 => _('si'), 0 => _('no')), 
			'default'=>0, 
			'value'=>$this->private 
		));

		$structure['promoted'] = new booleanField(array(
			'name'=>'promoted', 
			'required'=>true,
			'label'=>$this->_fields_label['promoted'], 
			'enum'=>array(1 => _('si'), 0 => _('no')), 
			'default'=>0, 
			'value'=>$this->promoted 
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
	 * Restituisce l'istanza galleria a partire dallo slug fornito 
	 * 
	 * @param string $slug lo slug 
	 * @param multimedia $controller istanza del controller
	 * @access public
	 * @return istanza di galleria
	 */
	public static function getFromSlug($slug, $controller) {
	
		$res = null;

		$db = db::instance();
		$rows = $db->select('id', self::$tbl_gallery, "slug='$slug'", null, array(0, 1));
		if(count($rows)) {
			$res = new multimediaGallery($rows[0]['id'], $controller);
		}

		return $res;
			
	}

	/**
	 * Restituisce la più recente galleria promossa 
	 * 
	 * @param multimedia $instance_obj istanza del controller 
	 * @param array $options array associativo di opzioni 
	 * @return più recente galleria promossa
	 */
	public static function getLEPromoted($instance_obj, $options = null) {

		$res = null;

		$private = gOpt('private', $options, false);
		$published = gOpt('published', $options, true);

		$db = db::instance();
		$selection = 'id';
		$table = self::$tbl_gallery;
		$where_arr = array("instance='".$instance_obj->getInstance()."'", "promoted='1'");
		if(!$private) {
			$where_arr[] = "private='0'";
		} 
		if($published) {
			$where_arr[] = "published='1'";
		}
		$where = implode(' AND ', $where_arr);

		$rows = $db->select($selection, $table, $where, 'last_edit_date DESC', array(0, 1));
		if($rows && count($rows)) {
			$res = new multimediaGallery($rows[0]['id'], $instance_obj);
		}

		return $res;

	}


	/**
	 * Restituisce oggetti di tipo galleria 
	 * 
	 * @param multimedia $instance_obj istanza del controller 
	 * @param array $options array associativo di opzioni 
	 * @return array di istanze di tipo galleria
	 */
	public static function get($instance_obj, $options = null) {

		$res = array();

		$private = gOpt('private', $options, false);
		$published = gOpt('published', $options, true);
		$where_q = gOpt('where', $options, '');
		$order = gOpt('order', $options, 'name');
		$limit = gOpt('limit', $options, null);

		$db = db::instance();
		$selection = 'id';
		$table = self::$tbl_gallery;
		$where_arr = array("instance='".$instance_obj->getInstance()."'");
		if(!$private) {
			$where_arr[] = "private='0'";
		} 
		if($published) {
			$where_arr[] = "published='1'";
		}
		if($where_q) {
			$where_arr[] = $where_q;
		}
		$where = implode(' AND ', $where_arr);

		$rows = $db->select($selection, $table, $where, $order, $limit);
		if(count($rows)) {
			foreach($rows as $row) {
				$res[] = new multimediaGallery($row['id'], $instance_obj);
			}
		}

		return $res;

	}

	/**
	 * Restituisce il numero di oggetti di tipo galleria che rispettano le condizioni date
	 * 
	 * @param multimedia $instance_obj istanza del controller 
	 * @param array $options array associativo di opzioni 
	 * @return numero di gallerie
	 */
	public static function getCount($instance_obj, $options = null) {

		$tot = 0;

		$private = gOpt('private', $options, false);
		$published = gOpt('published', $options, true);

		$db = db::instance();
		$selection = 'COUNT(id) AS tot';
		$table = self::$tbl_gallery;
		$where_arr = array("instance='".$instance_obj->getInstance()."'");
		if(!$private) {
			$where_arr[] = "private='0'";
		} 
		if($published) {
			$where_arr[] = "published='1'";
		}
		$where = implode(' AND ', $where_arr);

		$rows = $db->select($selection, $table, $where, null, null);
		if(count($rows)) {
			$tot = $rows[0]['tot'];
		}

		return $tot;

	}

	/**
	 * Restituisce gli oggetti di tipo media associati alla galleria 
	 * 
	 * @param multimedia $instance_obj istanza del controller 
	 * @param array $options array associativo di opzioni 
	 * @return array di istanze di tipo media
	 */
	public function getItems($instance_obj, $options = null) {

		$res = array();

		$private = gOpt('private', $options, false);
		$published = gOpt('published', $options, true);
		$order = gOpt('order', $options, 'insertion_date DESC');
		$limit = gOpt('limit', $options, null);
		$where_c = gOpt('where', $options, false);

		$db = db::instance();
		$selection = array('id', 'type');
		$table = multimediaItem::$tbl_item;
		$where_arr = array(
			"instance='".$instance_obj->getInstance()."'",
			"galleries REGEXP '[[:<:]]".$this->id."[[:>:]]'",
		);
		if(!$private) {
			$where_arr[] = "private='0'";
		} 
		if($published) {
			$where_arr[] = "published='1'";
		}
		if($where_c) {
			$where_arr[] = $where_c;
		}
		$where = implode(' AND ', $where_arr);

		$rows = $db->select($selection, $table, $where, $order, $limit);
		if(count($rows)) {
			foreach($rows as $row) {
				if($row['type'] == AUDIO_CHOICE) {
					$res[] = new multimediaAudio($row['id'], $instance_obj);
				}
				elseif($row['type'] == VIDEO_CHOICE) {
					$res[] = new multimediaVideo($row['id'], $instance_obj);
				}
				elseif($row['type'] == IMAGE_CHOICE) {
					$res[] = new multimediaImage($row['id'], $instance_obj);
				}
			}
		}

		return $res;

	}

	/**
	 * Restituisce il numero di media associati alla galleria che rispettano le condizioni date
	 * 
	 * @param multimedia $instance_obj istanza del controller 
	 * @param array $options array associativo di opzioni 
	 * @return numero di media
	 */
	public function getItemsCount($instance_obj, $options = null) {

		$tot = 0;

		$private = gOpt('private', $options, false);
		$published = gOpt('published', $options, true);

		$db = db::instance();
		$selection = 'COUNT(id) AS tot';
		$table = multimediaItem::$tbl_item;
		$where_arr = array(
			"instance='".$instance_obj->getInstance()."'",
			"galleries REGEXP '[[:<:]]".$this->id."[[:>:]]'"
		);
		if(!$private) {
			$where_arr[] = "private='0'";
		} 
		if($published) {
			$where_arr[] = "published='1'";
		}
		$where = implode(' AND ', $where_arr);

		$rows = $db->select($selection, $table, $where, null, null);
		if(count($rows)) {
			$tot = $rows[0]['tot'];
		}

		return $tot;

	}


	/**
	 * Ritorna il percorso relativo della thumb utilizzata per la galleria 
	 * 
	 * @param mixed $controller istanza del controller 
	 * @return ercorso relativo della thumb utilizzata per la galleria
	 */
	public function thumbPath($controller) {

		if($this->thumb) {
			return $controller->getBasePath('thumb').'/'.$this->thumb;
		}
		else {
			$media = multimediaItem::get($controller, array('published'=>true, 'gallery'=>$this->id, 'order'=>'insertion_date ASC', 'limit'=>array(0, 1)));
			if(count($media) && $media) {
				return $media[0]->thumbPath($controller);
			}
			else return $controller->defaultGalleryThumbPath();
		}

	}


}

?>

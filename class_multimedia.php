<?php
/**
* @file class_multimedia.php
* Contiene la definizione ed implementazione della classe multimedia.
*
* @version 0.1
* @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
* @authors Marco Guidotti guidottim@gmail.com
* @authors abidibo abidibo@gmail.com
*/

/**
 * Caratteristiche, opzioni configurabili da backoffice ed output disponibili per i template e le voci di menu.
 *
 * Collezione di media (audio, video, immagini) geolocalizzabili, suddivisi in gallerie ed etichettati con tag. I video sono inseriti come
 * codici che rimandano ad uno streaming (youtube, vimeo). Gli audio sono gestiti secondo le specifiche html5. La visualizzazione dei media
 * fa uso del plugin moogallery (http://mootools.net/forge/p/moogallery).
 *
 * OPZIONI CONFIGURABILI
 * - titolo vista gallerie
 * - titolo vista mappa con media geolocalizzati
 * - titolo vista box
 * - larghezza massima immagini
 * - larghezza lato lungo thumb
 * - template vista box
 * - numero gallerie in vista box
 * - template della vista lista gallerie (2 scelte disponibili: 1 galleria per riga, n gallerie per riga)
 * - numero massimo di gallerie per pagina
 * - numero massimo di media per pagina
 * - codice della riga di tabella template 1, con possibilità di personalizzare la visualizzazione dei campi disponibili ed applicaer filtri
 * - numero di gallerie per riga (template 2)
 * - codice della cella di tabella template 2, con possibilità di personalizzare la visualizzazione dei campi disponibili ed applicare filtri
 * - opzioni di configurazione dei pesi utlizzati per stabilire la priorità dei risultati a seguito di una ricerca
 *
 * OUTPUTS
 * - box ultime gallerie modificate
 * - lista gallerie
 * - mappa geolocalizzazione media
 * - vista galleria (richiede slug galleria)
 * - vista media (richiede id media)
 * - slider immagini di una galleria a scelta
 */

require_once('class.multimediaLicense.php');
require_once('class.multimediaVideoPlatform.php');
require_once('class.multimediaSlider.php');
require_once('class.multimediaGallery.php');
require_once('class.multimediaItem.php');
require_once('class.multimediaAudio.php');
require_once('class.multimediaVideo.php');
require_once('class.multimediaImage.php');
require_once('class.multimediaImageField.php');
require_once('class.multimediaTag.php');

define('AUDIO_CHOICE', 3);
define('VIDEO_CHOICE', 2);
define('IMAGE_CHOICE', 1);

/**
* @defgroup multimedia
* Modulo per la gestione di contenuti multimediali
*
* Il modulo contiene anche dei css, javascript e file di configurazione.
*
*/

/**
* \ingroup multimedia
* Classe controller per la gestione di contenuti multimediali.
*
* Questa classe agisce da controller per i modelli definiti negli altri file del pacchetto
*
* @version 0.1
* @copyright 2012 Otto srl MIT License http://www.opensource.org/licenses/mit-license.php
* @authors Marco Guidotti guidottim@gmail.com
* @authors abidibo abidibo@gmail.com
*/
class multimedia extends AbstractEvtClass {

	/**
	 * @brief titolo della view lista gallerie  
	 */
	private $_title_list_galleries;

	/**
	 * @brief titolo della view mappa  
	 */
	private $_title_map;

	/**
	 * @brief titolo della view slider  
	 */
	private $_title_slider;

	/**
	 * @brief titolo della view box  
	 */
	private $_title_box;

	/**
	 * @brief Dimensione in px del lato lungo della thumb  
	 */
	private $_thumb_dimension;

	/**
	 * @brief Dimensione in px della larghezza massima delle immagini  
	 */
	private $_image_max_width;

	/**
	 * @brief Template della galleria promossa nella vista box  
	 */
	private $_box_promoted_code;

	/**
	 * @brief Template della vista box  
	 */
	private $_box_tpl_code;

	/**
	 * @brief Numero di gallerie nella vista box  
	 */
	private $_box_num_galleries;

	/**
	 * @brief Template della vista elenco gallerie  
	 */
	private $_list_galleries_tpl;

	/**
	 * @brief Numero di colonne nella vista elenco gallerie con tabella thumb
	 */
	private $_list_galleries_tpl2_cols;

	/**
	 * @brief Numero di elementi per pagina nella vista elenco gallerie
	 */
	private $_list_galleries_tpl_ifp;

	/**
	 * @brief Numero di media per pagina nella vista galleria
	 */
	private $_gallery_ifp;

	/**
	 * @brief Template della cella della tabella nel layout 2 n thumb per riga
	 */
	private $_list_galleries_tpl2_code;

	/**
	 * @brief Template della riga della tabella nel layout 1 galleria per riga
	 */
	private $_list_galleries_tpl1_code;

	/*
	 * @brief Peso campo nome galleria nella ricerca gallerie
	 */
	private $_relevance_gallery_gname;

	/*
	 * @brief Peso campo descrizione galleria nella ricerca gallerie
	 */
	private $_relevance_gallery_gdescription;

	/*
	 * @brief Peso campo nome media nella ricerca gallerie
	 */
	private $_relevance_gallery_mname;

	/*
	 * @brief Peso campo descrizione media nella ricerca gallerie
	 */
	private $_relevance_gallery_mdescription;

	/*
	 * @brief Peso campo tags media nella ricerca gallerie
	 */
	private $_relevance_gallery_mtags;
	
	/*
	 * @brief Peso campo nome media nella ricerca media
	 */
	private $_relevance_media_mname;

	/*
	 * @brief Peso campo descrizione media nella ricerca media
	 */
	private $_relevance_media_mdescription;

	/*
	 * @brief Peso campo tags media nella ricerca media
	 */
	private $_relevance_media_mtags;

	/**
	 * @brief Tabella di opzioni 
	 */
	private $_tbl_opt;

	/**
	 * @brief Tabella di associazione utenti/gruppi 
	 */
	private $_tbl_usr;

	/**
	 * Contiene gli id dei gruppi abilitati alla redazione dei contenuti
	 * @var array
	 */
	private $_group_1;

	/**
	 * Contiene gli id dei gruppi abilitati visualizzazione di gruppi privati
	 * @var array
	 */
	private $_group_2;

	/**
	 * Oggetto di tipo options per la gestione automatica delle opzioni
	 */
	private $_options;

	/**
	 * Elenco di proprietà delle opzioni per la creazione del form delle opzioni
	 */
	public $_optionsLabels;

	/**
	 * Valori dei default per le opzioni
	 */
	public $_optionsValue;

	/*
	 * Parametro action letto da url 
	 */
	private $_action;

	/*
	 * Parametro block letto da url 
	 */
	private $_block;

	/**
	 * Percorso assoluto alla directory contenente le viste 
	 */
	private $_view_dir;

	/**
	 * Costruisce un'istanza di tipo multimedia
	 *
	 * @param int $mdlId id dell'istanza di tipo multimedia
	 * @return istanza di multimedia
	 */
	function __construct($mdlId) {

		parent::__construct();

		$this->_instance = $mdlId;
		$this->_instanceName = $this->_db->getFieldFromId($this->_tbl_module, 'name', 'id', $this->_instance);
		$this->_instanceLabel = $this->_db->getFieldFromId($this->_tbl_module, 'label', 'id', $this->_instance);

		$this->_data_dir = $this->_data_dir.$this->_os.$this->_instanceName;
		$this->_data_www = $this->_data_www."/".$this->_instanceName;

		$this->_tbl_opt = 'multimedia_opt';
		$this->_tbl_usr = 'multimedia_usr';

		$this->setAccess();
		$this->setGroups();

		$this->_view_dir = dirname(__FILE__).OS.'view';

		$box_promoted_code_dft = "<div class=\"thumbitem\">{{ thumb }}<div style=\"font-size: 1.1em; margin-top: 5px;margin-bottom: 8px;\">{{ name|link }}</div></div>";
		$box_tpl_code_dft = "<div class=\"thumbitem\">{{ thumb }}<div style=\"font-size: 1.1em; margin-top: 5px;margin-bottom: 8px;\">{{ name|link }}</div></div>";
		$tpl2_cell_dft = "{{ thumb }}<p>{{ name|link }}</p>";
		$tpl1_row_dft = "<td class=\"thumb\">{{ thumb }}</td><td><span class=\"date\">{{ last_edit_date }}</span><br />{{ name|link }}{{ description|chars:200 }}</td>";
		$gallery_code_exp = _("Le proprietà della galleria devono essere inserite all'interno di doppie parentesi {{ proprietà }}. Proprietà disponibili:<br/>");
		$gallery_code_exp .= "<ul>";
		$gallery_code_exp .= "<li><b>thumb</b>: "._('thumbnail')."</li>";
		$gallery_code_exp .= "<li><b>thumb_url</b>: "._('thumbnail url')."</li>";
		$gallery_code_exp .= "<li><b>name</b>: "._('nome')."</li>";
		$gallery_code_exp .= "<li><b>description</b>: "._('descrizione')."</li>";
		$gallery_code_exp .= "<li><b>insertion_date</b>: "._('data di creazione della galleria')."</li>";
		$gallery_code_exp .= "<li><b>last_edit_date</b>: "._('data di ultimo inserimento immagine')."</li>";
		$gallery_code_exp .= "</ul>";
		$gallery_code_exp .= _("Inoltre si possono eseguire dei filtri o aggiungere link facendo seguire il nome della proprietà dai caratteri '|filtro'. Disponibili:<br />");
		$gallery_code_exp .= "<ul>";
		$gallery_code_exp .= "<li><b><span style='text-style: normal'>|link</span></b>: "._('aggiunge il link che porta alla vista della galleria alla proprietà')."</li>";
		$gallery_code_exp .= "<li><b><span style='text-style: normal'>|chars:n</span></b>: "._('mostra solo n caratteri della proprietà')."</li>";
		$gallery_code_exp .= "<li><b><span style='text-style: normal'>thumb|class:name_class</span></b>: "._('aggiunge la classe name_class alla thumb')."</li>";
		$gallery_code_exp .= "</ul>";
		

		$this->_optionsValue = array(
			'title_box'=>_("Multimedia"),
			'title_list_galleries'=>_("Galleria multimediale"),
			'title_map'=>_("Mappa geolocalizzazione media"),
			'title_slider'=>'',
			'image_max_width'=>1024,
			'thumb_dimension'=>160,
			'box_promoted_code'=>$box_promoted_code_dft,
			'box_tpl_code'=>$box_tpl_code_dft,
			'box_num_galleries'=>3,
			'list_galleries_tpl'=>1,
			'list_galleries_tpl_ifp'=>'20',
			'gallery_ifp'=>'40',
			'list_galleries_tpl1_code'=>$tpl1_row_dft,
			'list_galleries_tpl2_cols'=>'',
			'list_galleries_tpl2_code'=>$tpl2_cell_dft,
			'relevance_gallery_gname'=>50,
			'relevance_gallery_gdescription'=>10,
			'relevance_gallery_mname'=>5,
			'relevance_gallery_mdescription'=>2,
			'relevance_gallery_mtags'=>5,
			'relevance_media_mname'=>50,
			'relevance_media_mdescription'=>10,
			'relevance_media_mtags'=>25,
		);
		
		$this->_title_box = htmlChars($this->setOption('title_box', array('value'=>$this->_optionsValue['title_box'], 'translation'=>true)));
		$this->_title_list_galleries = htmlChars($this->setOption('title_list_galleries', array('value'=>$this->_optionsValue['title_list_galleries'], 'translation'=>true)));
		$this->_title_map = htmlChars($this->setOption('title_map', array('value'=>$this->_optionsValue['title_map'], 'translation'=>true)));
		$this->_title_slider = htmlChars($this->setOption('title_slider', array('value'=>$this->_optionsValue['title_slider'], 'translation'=>true)));
		$this->_image_max_width = $this->setOption('image_max_width', array('value'=>$this->_optionsValue['image_max_width']));
		$this->_thumb_dimension = $this->setOption('thumb_dimension', array('value'=>$this->_optionsValue['thumb_dimension']));
		$this->_box_promoted_code = $this->setOption('box_promoted_code', array('value'=>$this->_optionsValue['box_promoted_code'], 'translation'=>true));
		$this->_box_tpl_code = $this->setOption('box_tpl_code', array('value'=>$this->_optionsValue['box_tpl_code'], 'translation'=>true));
		$this->_box_num_galleries = $this->setOption('box_num_galleries', array('value'=>$this->_optionsValue['box_num_galleries']));
		$this->_list_galleries_tpl = $this->setOption('list_galleries_tpl', array('value'=>$this->_optionsValue['list_galleries_tpl'], 'translation'=>true));
		$this->_list_galleries_tpl_ifp = $this->setOption('list_galleries_tpl_ifp', array('value'=>$this->_optionsValue['list_galleries_tpl_ifp']));
		$this->_gallery_ifp = $this->setOption('gallery_ifp', array('value'=>$this->_optionsValue['gallery_ifp']));
		$this->_list_galleries_tpl1_code = $this->setOption('list_galleries_tpl1_code', array('value'=>$this->_optionsValue['list_galleries_tpl1_code'], 'translation'=>true));
		$this->_list_galleries_tpl2_cols = $this->setOption('list_galleries_tpl2_cols', array('value'=>$this->_optionsValue['list_galleries_tpl2_cols']));
		$this->_list_galleries_tpl2_code = $this->setOption('list_galleries_tpl2_code', array('value'=>$this->_optionsValue['list_galleries_tpl2_code'], 'translation'=>true));
		$this->_relevance_gallery_gname = $this->setOption('relevance_gallery_gname', array('value'=>$this->_optionsValue['relevance_gallery_gname']));
		$this->_relevance_gallery_gdescription = $this->setOption('relevance_gallery_gdescription', array('value'=>$this->_optionsValue['relevance_gallery_gdescription']));
		$this->_relevance_gallery_mname = $this->setOption('relevance_gallery_mname', array('value'=>$this->_optionsValue['relevance_gallery_mname']));
		$this->_relevance_gallery_mdescription = $this->setOption('relevance_gallery_mdescription', array('value'=>$this->_optionsValue['relevance_gallery_mdescription']));
		$this->_relevance_gallery_mtags = $this->setOption('relevance_gallery_mtags', array('value'=>$this->_optionsValue['relevance_gallery_mtags']));
		$this->_relevance_media_mname = $this->setOption('relevance_media_mname', array('value'=>$this->_optionsValue['relevance_media_mname']));
		$this->_relevance_media_mdescription = $this->setOption('relevance_media_mdescription', array('value'=>$this->_optionsValue['relevance_media_mdescription']));
		$this->_relevance_media_mtags = $this->setOption('relevance_media_mtags', array('value'=>$this->_optionsValue['relevance_media_mtags']));

		$this->_options = new options($this->_className, $this->_instance);
		$this->_optionsLabels = array(
			"title_box"=>array(
				'label'=>_("Titolo box"),
				'value'=>$this->_optionsValue['title_box'],
				'section'=>true, 
				'section_title'=>_('Titoli delle viste pubbliche')
			),
			"title_list_galleries"=>array(
				'label'=>_("Titolo lista gallerie"), 
				'value'=>$this->_optionsValue['title_list_galleries'] 
			),
			"title_map"=>array(
				'label'=>_("Titolo mappa"),
				'value'=>$this->_optionsValue['title_map']
			),
			"title_slider"=>array(
				'label'=>_("Titolo slider"),
				'value'=>$this->_optionsValue['title_slider']
			),
			"image_max_width"=>array(
				'label'=>_("Larghezza massima immagini (px)"), 
				'value'=>$this->_optionsValue['image_max_width'],
				'section'=>true, 
				'section_title'=>_('Opzioni di ridimensionamento')
			), 
			"thumb_dimension"=>array(
				'label'=>_("Dimensione lato lungo della thumbnail (px)"),
				'value'=>$this->_optionsValue['thumb_dimension']
			), 
			"box_promoted_code"=>array(
				'label'=>array(_("Codice galleria promossa"), $gallery_code_exp), 
				'value'=>$this->_optionsValue['box_promoted_code'],
				'section'=>true, 
				'section_title'=>_('Opzioni vista box'), 
				'section_description'=>_('La vista box è una lista che mostra le ultime n gallerie modificate. Ciascun elemento viene inserito all\'interno di un elemento <b>li</b>. Se esiste una galleria "promossa" questa viene inserita in testa alla lista.'), 
				"required"=>false
			), 
			"box_tpl_code"=>array(
				'label'=>array(_("Codice singolo elemento"), $gallery_code_exp), 
				'value'=>$this->_optionsValue['box_tpl_code'],
				"required"=>false
			), 
			"box_num_galleries"=>array(
				'label'=>_("Numero di gallerie mostrate"),
				'value'=>$this->_optionsValue['box_num_galleries']
			),
			"list_galleries_tpl"=>array(
				'label'=>_("Template della vista lista gallerie<br />1. tabella 1 galleria per riga<br />2. tabella n thumb per riga"), 
				'value'=>$this->_optionsValue['list_galleries_tpl'],
				'section'=>true, 
				'section_title'=>_('Template lista gallerie')
			), 
			"list_galleries_tpl_ifp"=>array(
				'label'=>_("Numero massimo di gallerie per pagina"), 
				'value'=>$this->_optionsValue['list_galleries_tpl_ifp'],
				"required"=>false
			), 
			"gallery_ifp"=>array(
				'label'=>_("Numero massimo di media per pagina"), 
				'value'=>$this->_optionsValue['gallery_ifp'],
				"required"=>false
			), 
			"list_galleries_tpl1_code"=>array(
				'label'=>array(_("Codice riga"), $gallery_code_exp), 
				'value'=>$this->_optionsValue['list_galleries_tpl1_code'],
				'section'=>true, 
				'section_title'=>_('Opzioni template 1 galleria per riga'), 
				"required"=>false
			), 
			"list_galleries_tpl2_cols"=>array(
				'label'=>_("Numero di gallerie per riga"), 
				'value'=>$this->_optionsValue['list_galleries_tpl2_cols'],
				'section'=>true, 
				'section_title'=>_('Opzioni template n gallerie per riga'), 
				"required"=>false
			),
			"list_galleries_tpl2_code"=>array(
				'label'=>array(_("Codice cella"), $gallery_code_exp), 
				'value'=>$this->_optionsValue['list_galleries_tpl2_code'],
				"required"=>false
			),
			"relevance_gallery_gname"=>array(
				'label'=>_("Peso nome galleria nella ricerca gallerie"), 
				'value'=>$this->_optionsValue['relevance_gallery_gname'],
				'section'=>true, 
				'section_title'=>_('Configurazione parametri di ricerca'), 
				'section_description'=>"<p>"._("La rilevanza di un risultato della ricerca è data dalla somma dei pesi assegnati alla presenza della chiave di ricerca nei campi ed il numero di occorrenze della chiave di ricerca negli stessi. Le occorrenze hanno di default valore 1. Modificare i parametri che seguono a seconda dell'importanza che si vuole attribuire ai vari campi. Esempio: se si considera importante ai fini della ricerca il riscontro di una chiave all'interno dei tags aumentare il valore del 'peso tag media' rispetto agli altri parametri.")."</p>", 
				"required"=>false
			),
			"relevance_gallery_gdescription"=>array(
				'label'=>_("Peso descrizione galleria nella ricerca gallerie"), 
				'value'=>$this->_optionsValue['relevance_gallery_gdescription'],
				"required"=>false
			),
			"relevance_gallery_mname"=>array(
				'label'=>_("Peso nome media nella ricerca gallerie"), 
				'value'=>$this->_optionsValue['relevance_gallery_mname'],
				"required"=>false
			),
			"relevance_gallery_mdescription"=>array(
				'label'=>_("Peso descrizione media nella ricerca gallerie"), 
				'value'=>$this->_optionsValue['relevance_gallery_mdescription'],
				"required"=>false
			),
			"relevance_gallery_mtags"=>array(
				'label'=>_("Peso tag media nella ricerca gallerie"), 
				'value'=>$this->_optionsValue['relevance_gallery_mtags'],
				"required"=>false
			),
			"relevance_media_mname"=>array(
				'label'=>_("Peso nome media nella ricerca media"), 
				'value'=>$this->_optionsValue['relevance_media_mname'],
				"required"=>false
			),
			"relevance_media_mdescription"=>array(
				'label'=>_("Peso descrizione media nella ricerca media"), 
				'value'=>$this->_optionsValue['relevance_media_mdescription'],
				"required"=>false
			),
			"relevance_media_mtags"=>array(
				'label'=>_("Peso tag media nella ricerca media"), 
				'value'=>$this->_optionsValue['relevance_media_mtags'],
				"required"=>false
			),
		);

		$this->_action = cleanVar($_REQUEST, 'action', 'string', '');
		$this->_block = cleanVar($_REQUEST, 'block', 'string', '');

	}

	/**
	 * Restituisce alcune proprietà della classe utili per la generazione di nuove istanze
	 *
	 * @static
	 * @return lista delle proprietà utilizzate per la creazione di istanze di tipo multimedia
	 */
	public static function getClassElements() {

		return array(
			"tables"=>array(
				'multimedia_slider', 
				'multimedia_gallery', 
				'multimedia_grp', 
				'multimedia_item', 
				'multimedia_item_tag', 
				'multimedia_license', 
				'multimedia_opt', 
				'multimedia_tag', 
				'multimedia_usr',
				'multimedia_video_platform'
			),
			"css"=>array(
				'multimedia.css',
				'NivooSlider.css'
			),
			"folderStructure"=>array(
				CONTENT_DIR.OS.'multimedia'=>array(
					'image' => null,
					'audio' => null,
					'thumb' => null
				)	
	     		)
		);

	}

	/**
	 * Metodo invocato quando viene eliminata un'istanza
	 *
	 * Si esegue la cancellazione dei dati da db e l'eliminazione di file e directory
	 *
	 * @access public
	 * @return il risultato dell'operazione (true/false)
	 */
	public function deleteInstance() {

		$this->accessGroup('');

		/*
		 * delete records and translations from table multimedia_gallery
		 */
		$query = "SELECT id FROM ".multimediaGallery::$tbl_gallery." WHERE instance='$this->_instance'";
		$a = $this->_db->selectquery($query);
		if(sizeof($a)>0) 
			foreach($a as $b) 
				language::deleteTranslations(multimediaGallery::$tbl_gallery, $b['id']);

		$query = "DELETE FROM ".multimediaGallery::$tbl_gallery." WHERE instance='$this->_instance'";	
		$result = $this->_db->actionquery($query);

		/*
		 * delete records and translations from table multimedia_item and tag associations
		 */
		$query = "SELECT id FROM ".multimediaItem::$tbl_item." WHERE instance='$this->_instance'";
		$a = $this->_db->selectquery($query);
		if(sizeof($a)>0) 
			foreach($a as $b) { 
				language::deleteTranslations(multimediaItem::$tbl_item, $b['id']);
				$item = new multimediaItem($b['id'], $this);
				$item->delete();
			}

		/*
		 * delete records and translations from table multimedia_license
		 */
		$query = "SELECT id FROM ".multimediaLicense::$tbl_license." WHERE instance='$this->_instance'";
		$a = $this->_db->selectquery($query);
		if(sizeof($a)>0) 
			foreach($a as $b) 
				language::deleteTranslations(multimediaLicense::$tbl_license, $b['id']);

		$query = "DELETE FROM ".multimediaLicense::$tbl_license." WHERE instance='$this->_instance'";	
		$result = $this->_db->actionquery($query);

		/*
		 * delete records and translations from table multimedia_slider
		 */
		$query = "SELECT id FROM ".multimediaSlider::$tbl_slider." WHERE instance='$this->_instance'";
		$a = $this->_db->selectquery($query);
		if(sizeof($a)>0) 
			foreach($a as $b) 
				language::deleteTranslations(multimediaSLider::$tbl_slider, $b['id']);

		$query = "DELETE FROM ".multimediaSlider::$tbl_slider." WHERE instance='$this->_instance'";	
		$result = $this->_db->actionquery($query);
		
		/*
		 * delete records and translations from table multimedia_video_platform
		 */
		$query = "SELECT id FROM ".multimediaVideoPlatform::$tbl_video_platform." WHERE instance='$this->_instance'";
		$a = $this->_db->selectquery($query);
		if(sizeof($a)>0) 
			foreach($a as $b) 
				language::deleteTranslations(multimediaVideoPlatform::$tbl_video_platform, $b['id']);

		$query = "DELETE FROM ".multimediaVideoPlatform::$tbl_video_platform." WHERE instance='$this->_instance'";	
		$result = $this->_db->actionquery($query);

		/*
		 * delete options
		 */
		$opt_id = $this->_db->getFieldFromId($this->_tbl_opt, "id", "instance", $this->_instance);
		language::deleteTranslations($this->_tbl_opt, $opt_id);
		
		$query = "DELETE FROM ".$this->_tbl_opt." WHERE instance='$this->_instance'";	
		$result = $this->_db->actionquery($query);
		
		/*
		 * delete group users association
		 */
		$query = "DELETE FROM ".$this->_tbl_usr." WHERE instance='$this->_instance'";	
		$result = $this->_db->actionquery($query);

		/*
		 * delete css files
		 */
		$classElements = $this->getClassElements();
		foreach($classElements['css'] as $css) {
			unlink(APP_DIR.OS.$this->_className.OS.baseFileName($css)."_".$this->_instanceName.".css");
		}

		/*
		 * delete folder structure
		 */
		foreach($classElements['folderStructure'] as $fld=>$fldStructure) {
			$this->deleteFileDir($fld.OS.$this->_instanceName, true);
		}

		return $result;
	}

	/**
	 * Setter per le proprietà group
	 *
	 * Definizione dei gruppi che gestiscono l'accesso alle funzionalità amministrative e non
	 *
	 * @return void
	 */
	private function setGroups(){
		
		// drafting
		$this->_group_1 = array($this->_list_group[0], $this->_list_group[1]);
		
		// Viewer
		$this->_group_2 = array($this->_list_group[0], $this->_list_group[1], $this->_list_group[2]);

	}

	/**
 	 * Definizione dei metodi pubblici che forniscono un output per il front-end
	 *
	 * Questo metodo viene letto dal motore di generazione dei layout e dal motore di generazione di voci di menu
	 * per presentare una lista di output associati all'istanza di classe.
	 *
	 * @static
 	 * @return lista dei metodi pubblici
	*/
	public static function outputFunctions() {

		$list = array(
			"box" => array("label"=>_("Box ultime gallerie modificate"), "role"=>'1'),
			"galleries" => array("label"=>_("Lista gallerie"), "role"=>'1'),
			"slider" => array("label"=>_("Slider delle immagini della galleria selezionata"), "role"=>'1'),
			"map" => array("label"=>_("Mappa media geolocalizzati, impostare le dimesioni della mappa da css. Se in template con la vista galleria mostra i media associati alla galleria in questione."), "role"=>'1'),
		);

		return $list;
	}

	/**
	 * Path relativo alla thumb di default per le gallerie 
	 * 
	 * @return path
	 */
	public function defaultGalleryThumbPath() {
		return $this->_class_img.'/'.'gallery_thumb.png';
	}

	/**
	 * Path relativo alla thumb di default per le immagini 
	 * 
	 * @return void
	 */
	public function defaultImageThumbPath() {
		return $this->_class_img.'/'.'image_thumb.png';
	}

	/**
	 * Path relativo alla thumb di default per i video 
	 * 
	 * @return void
	 */
	public function defaultVideoThumbPath() {
		return $this->_class_img.'/'.'video_thumb.png';
	}

	/**
	 * Path relativo alla thumb di default per gli audio 
	 * 
	 * @return void
	 */
	public function defaultAudioThumbPath() {
		return $this->_class_img.'/'.'audio_thumb.png';
	}

	/**
	 * Percorso assoluto alla cartella dei contenuti 
	 * 
	 * @param string $type tipologia di media (audio, img, thumb)
	 * @return percorso assoluto
	 */
	public function getBaseAbsPath($type) {

		return $this->_data_dir.OS.$type;

	}

	/**
	 * Percorso relativo alla cartella dei contenuti 
	 * 
	 * @param string $type tipologia di media (audio, img, thumb)
	 * @return percorso relativo
	 */
	public function getBasePath($type) {

		return $this->_data_www.'/'.$type;

	}

	/**
	 * Restituisce la dimensione massima del lato maggiore delle thumb 
	 * 
	 * @return dimensione massima
	 */
	public function getThumbDimension() {

		return $this->_thumb_dimension;

	}

	/**
	 * Restituisce la larghezza massima delle immagini 
	 * 
	 * @return dimensione massima
	 */
	public function getImageMaxWidth() {

		return $this->_image_max_width;

	}

	/**
	 * Front end box ultime gallerie modificate
	 *
	 * @return box ultime gallerie modificate
	 */
	public function box() {

		$this->setAccess($this->_access_base);

		$registry = registry::instance();
		$registry->addCss($this->_class_www."/multimedia_".$this->_instanceName.".css");

		if($this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, $this->_user_group, $this->_group_2)) {
			$private = true;
		}
		else {
			$private = false;
		}

		$promoted = multimediaGallery::getLEPromoted($this, array('private'=>$private, 'published'=>true));

		$where_q = $promoted ? 'id != \'' . $promoted->id . '\'' : '';
			
		$limit = array(0, $this->_box_num_galleries);
		$gobjs = multimediaGallery::get($this, array('private'=>$private, 'published'=>true, 'where'=>$where_q, 'order'=>'last_edit_date DESC', 'limit'=>$limit));
		
		preg_match_all("#{{[^}]+}}#", $this->_box_tpl_code, $matches);

		$lis = array();
		foreach($gobjs as $g) {
			$li = $this->parseTemplate($g, $this->_box_tpl_code, $matches);
			$lis[] = $li;
		}

		$promoted_text = '';
		if($promoted) {
			preg_match_all("#{{[^}]+}}#", $this->_box_promoted_code, $matches);
			$promoted_text = $this->parseTemplate($promoted, $this->_box_promoted_code, $matches);
		}

		$view = new view($this->_view_dir);

		$view->setViewTpl('box');
		$view->assign('section_id', 'box_'.$this->_className.'_'.$this->_instanceName);
		$view->assign('title', $this->_title_box);
		$view->assign('promoted', $promoted_text);
		$view->assign('lis', $lis);
		$view->assign('all_galleries_url', $this->_plink->aLink($this->_instanceName, 'galleries'));

		return $view->render();

	}

	/**
	 * Front end slider
	 *
	 * @return slider immagini
	 */
	public function slider() {

		$this->setAccess($this->_access_base);

		$slider = multimediaSlider::getFromInstance($this);
		if(!$slider->id) return '';

		$registry = registry::instance();
		$registry->addJs($this->_class_www."/NivooSlider.js");
		$registry->addJs($this->_class_www."/multimedia.js");
		$registry->addCss($this->_class_www."/multimedia_".$this->_instanceName.".css");
		$registry->addCss($this->_class_www."/NivooSlider_".$this->_instanceName.".css");

		$content = preg_replace("#{{\s*slider\s*}}#", $this->sliderContent($slider), $slider->ml('tpl'));	

		$view = new view($this->_view_dir);
		$view->setViewTpl('slider');
		$view->assign('section_id', 'slider_'.$this->_className.'_'.$this->_instanceName);
		$view->assign('title', $this->_title_slider);
		$view->assign('content', $content);

		return $view->render();
	}

	/**
	 * Ritorna il codice necessario alla creazione dello slider 
	 * 
	 * @param multimediaSlider $slider istanza dello slider
	 * @return slider
	 */
	private function sliderContent($slider) {


		if($this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, $this->_user_group, $this->_group_2)) {
			$private = true;
		}
		else {
			$private = false;
		}

		$items = $slider->getItems($this, $private);

		$images = array();
		foreach($items as $item) {
			$images[] = $item->show($this);
		}

		list($effect, $orientation) = $slider->getEffectOrientation();

		$view = new view($this->_view_dir);
		$view->setViewTpl('slider_content');
		$view->assign('container_id', 'slider_container_multimedia_'.$this->_instanceName);
		$view->assign('images', $images);
		$view->assign('animation_effect_duration', $slider->animation_effect_duration);
		$view->assign('auto_play', $slider->auto_play);
		$view->assign('show_ctrls', $slider->show_ctrls);
		$view->assign('mouseout_hide_ctrls', $slider->mouseout_hide_ctrls);
		$view->assign('effect', $effect);
		$view->assign('orientation', $orientation);
		$view->assign('animation_interval', $slider->animation_interval);
		$view->assign('pause_on_hover', $slider->pause_interval_mouseover);
		$view->assign('slices', $slider->slices);

		return $view->render();
	}

	/**
	 * Front end dettagli media
	 *
	 * @return scheda media
	 */
	public function detail() {

		$this->setAccess($this->_access_base);

		$id = cleanVar($_GET, 'id', 'string', '');	

		$item = multimediaItem::getObject($id, $this);
		if(!$item || !$item->id) {
			error::raise404();
		}

		if($item->private && !$this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, $this->_user_group, $this->_group_2)) {
			error::raise404();
		}

		$registry = registry::instance();
		$registry->addJs("http://maps.googleapis.com/maps/api/js?key=AIzaSyArAE-uBvCZTRaf_eaFn4umUdESmoUvoxM&sensor=true");

		$license = array();
		if($item->license) {
			$l = new multimediaLicense($item->license, $this);
			$license = array(
				'name' => htmlChars($l->name),
				'description' => htmlChars($l->description),
				'url' => $l->url
			);
		}

		$galleries = array();
		if($item->galleries) {
			foreach(explode(',', $item->galleries) as $gid) {
				if($gid = trim($gid)) {
					$g = new multimediaGallery($gid, $this);
					if($this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, $this->_user_group, $this->_group_2) || !$g->private) {
						$galleries[] = array(
							'name' => htmlChars($g->name),
							'url' => $this->_plink->aLink($this->_instanceName, 'gallery', array('id'=>$g->slug)),
						);
					}
				}
			}
		}

		$view = new view($this->_view_dir);
		$view->setViewTpl('detail');
		$view->assign('section_id', 'detail_'.$this->_className.'_'.$this->_instanceName);
		$view->assign('title', htmlChars($item->name));
		$view->assign('galleries', $galleries);
		$view->assign('media', $item->show($this));
		$view->assign('description', htmlChars($item->description));
		$view->assign('credits', htmlChars($item->credits));
		$view->assign('license', $license);
		$view->assign('lat', $item->lat);
		$view->assign('lng', $item->lng);
		$view->assign('tags', $item->tags);
		$view->assign('insertion_date', $item->insertion_date);
		$view->assign('last_edit_date', date('d/m/Y', strtotime($item->last_edit_date)));

		return $view->render();
	}

	/**
	 * Front end mappa con media geolocalizzati
	 *
	 * Se inserita in un template dove il metodo chiamato da url è multimedia::gallery mostra solamente i media geolocalizzati della galleria in questione  
	 * 
	 * @return mappa media geolocalizzati
	 */
	public function map() {

		$this->setAccess($this->_access_base);

		$registry = registry::instance();
		$registry->addJs("http://maps.googleapis.com/maps/api/js?key=AIzaSyArAE-uBvCZTRaf_eaFn4umUdESmoUvoxM&sensor=true");
		$registry->addJs($this->_class_www."/markerclusterer_packed.js");
		$registry->addJs($this->_class_www."/multimedia.js");
		$registry->addCss($this->_class_www."/multimedia_".$this->_instanceName.".css");

		$session = session::instance();

		$method = '';
		$evtKey = isset($_GET[EVT_NAME])? is_array($_GET[EVT_NAME])? key($_GET[EVT_NAME]):false:false;
		if($evtKey) {
			$parts = explode('-', $evtKey);
			$method = $parts[1];
		}

		if($method == 'gallery') {
			$slug = cleanVar($_GET, 'id', 'string', '');
			$galleryobj = multimediaGallery::getFromSlug($slug, $this);
			$gallery = $galleryobj->id;	
		}
		else {
			$gallery = null;
		}

		if($this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, $this->_user_group, $this->_group_2)) {
			$private = true;
		}
		else {
			$private = false;
		}

		if($method == 'gallery' && $session->multimedia_media_search) {
			$results = $session->multimedia_media_search_results;
			$items = array();
			foreach($results as $r) {
				$items[] = multimediaItem::getObject($r, $this);
			}
		}
		else {
			$items = multimediaItem::get($this, array('published'=>true, 'private'=>true, 'geolocalization'=>true, 'gallery'=>$gallery));
		}

		$media = array();
		foreach($items as $item) {
			$media[] = array(
				'id'=>$item->id,
				'name'=>$item->name,
				'lat'=>$item->lat,
				'lng'=>$item->lng,
				'url'=>$this->_plink->aLink($this->_instanceName, 'detail', array('id'=>$item->id)),
				'thumb_path'=>$item->thumbPath($this)
			);
		}

		$view = new view($this->_view_dir);
		$view->setViewTpl('map');
		$view->assign('section_id', ($method ? $method.'_' : '').'map_'.$this->_className.'_'.$this->_instanceName);
		$view->assign('title', htmlChars($this->_title_map));
		$view->assign('media', $media);
		
		return $view->render();
	}

	/**
	 * Front end galleria 
	 * 
	 * @return vista della galleria
	 */
	public function gallery() {

		$this->setAccess($this->_access_base);

		$id = cleanVar($_GET, 'id', 'string', '');	

		$gallery = multimediaGallery::getFromSlug($id, $this);
		if(!$gallery || !$gallery->id) {
			error::raise404();
		}

		if($gallery->private && !$this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, $this->_user_group, $this->_group_2)) {
			error::raise404();
		}

		$registry = registry::instance();
		$registry->addJs($this->_class_www."/moogallery.js");
		$registry->addCss($this->_class_www."/moogallery.css");
		$session = session::instance();

		if(isset($_POST['submit_search']) || isset($_POST['submit_search_all'])) {
			$this->actionSearchMedia($gallery);
		}

		if($session->multimedia_media_search) {
			$results = $session->multimedia_media_search_results;
			$num_items = count($results);

			$pagination = new pagelist($this->_gallery_ifp, $num_items, 'array');
			
			$end = min($pagination->start() + $this->_gallery_ifp, $num_items);
			$items = array();
			for($i = $pagination->start(); $i < $end; $i++) {
				$items[] = multimediaItem::getObject($results[$i], $this);
			}
			
			$search_text = $session->multimedia_media_search;
		}
		else {

			if($this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, $this->_user_group, $this->_group_2)) {
				$private = true;
			}
			else {
				$private = false;
			}
		
			$num_items = $gallery->getItemsCount($this, array('private'=>$private, 'published'=>true));
			$pagination = new pagelist($this->_gallery_ifp, $num_items, 'array');

			$limit = array($pagination->start(), $this->_gallery_ifp);
			$items = $gallery->getItems($this, array('private'=>$private, 'published'=>true, 'limit'=>$limit));

			$search_text = '';
		}

		$js_items_ar = array();
		foreach($items as $item) {
			$js_items_ar[] = $item->getMoogalleryListJs($this, $this->_plink->aLink($this->_instanceName, 'detail'));		
		}

		$myform = new form('form_search_media', 'post', false, array('tblLayout'=>false));
		$form_search = $myform->form($this->_plink->aLink($this->_instanceName, 'gallery', array('id'=>$gallery->slug)), false, '');
		$form_search .= $myform->input('search_text', 'text', htmlInput($search_text), array('hint'=>_('cerca media'), 'size'=>20, 'maxlength'=>40));
		$form_search .= '&#160;'.$myform->input('submit_search', 'submit', _('cerca'), array('classField'=>'submit'));
		$form_search .= '&#160;'.$myform->input('submit_search_all', 'submit', _('tutti'), array('classField'=>'submit'));
		$form_search .= $myform->cform();

		$view = new view($this->_view_dir);
		$view->setViewTpl('gallery');
		$view->assign('section_id', 'gallery_'.$this->_className.'_'.$this->_instanceName);
		$view->assign('form_search', $form_search);
		$view->assign('search_text', $search_text);
		$view->assign('title', htmlChars($gallery->name));
		$view->assign('galleries_url', $this->_plink->aLink($this->_instanceName, 'galleries'));
		$view->assign('js_items', $js_items_ar);
		$view->assign('pagination_summary', $pagination->reassumedPrint());
		$view->assign('pagination_navigation', $pagination->listReferenceGINO($this->_plink->aLink($this->_instanceName, 'gallery', '', 'id='.$id, array("basename"=>false))));

		return $view->render();

	}

	/**
	 * Front end lista gallerie 
	 * 
	 * @access public
	 * @return lista gallerie
	 */
	public function galleries() {

		$this->setAccess($this->_access_base);

		if(isset($_POST['submit_search']) || isset($_POST['submit_search_all'])) {
			$this->actionSearchGalleries();
		}

		$registry = registry::instance();
		$registry->addCss($this->_class_www."/".$this->_className."_".$this->_instanceName.".css");

		$session = session::instance();
		
		if($session->multimedia_galleries_search) {
			$results = $session->multimedia_galleries_search_results;
			$num_galleries = count($results);

			$pagination = new pagelist($this->_list_galleries_tpl_ifp, $num_galleries, 'array');
			
			$end = min($pagination->start() + $this->_list_galleries_tpl_ifp, $num_galleries);
			$gobjs = array();
			for($i = $pagination->start(); $i < $end; $i++) {
				$gobjs[] = new multimediaGallery($results[$i], $this);
			}
			
			$search_text = $session->multimedia_galleries_search;
		}
		else {

			if($this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, $this->_user_group, $this->_group_2)) {
				$private = true;
			}
			else {
				$private = false;
			}
			
			$num_galleries = multimediaGallery::getCount($this, array('private'=>$private, 'published'=>true));
			$pagination = new pagelist($this->_list_galleries_tpl_ifp, $num_galleries, 'array');

			$limit = array($pagination->start(), $this->_list_galleries_tpl_ifp);
			$gobjs = multimediaGallery::get($this, array('private'=>$private, 'published'=>true, 'limit'=>$limit));

			$search_text = '';
		}

		$rows = array();
		$view = new view($this->_view_dir);

		if($this->_list_galleries_tpl == 1) {
			$view->setViewTpl('galleries_1');
			preg_match_all("#{{[^}]+}}#", $this->_list_galleries_tpl1_code, $matches);
				
			foreach($gobjs as $g) {
				$row = $this->parseTemplate($g, $this->_list_galleries_tpl1_code, $matches);
				$rows[] = $row;
			}
			$view->assign('rows', $rows);
		}
		elseif($this->_list_galleries_tpl == 2) {
			$view->setViewTpl('galleries_2');
			preg_match_all("#{{[^}]+}}#", $this->_list_galleries_tpl2_code, $matches);
			$i = 0;
			$num_gobjs = count($gobjs);
			$row = array();
			foreach($gobjs as $g) {
				$i++;
				$row_cell = $this->parseTemplate($g, $this->_list_galleries_tpl2_code, $matches);
				$row[] = $row_cell;
				if( ($i % $this->_list_galleries_tpl2_cols == 0) || ($num_gobjs == $i) ) {
					$rows[] = $row;
					$row = array();
				}
			}
			$heads = array();
			$view_table = new view();
			$view_table->setViewTpl('table');
			$view_table->assign('class', 'galleries');
			$view_table->assign('caption', '');
			$view_table->assign('heads', $heads);
			$view_table->assign('rows', $rows);

			$table = $view_table->render();

			$view->assign('table', $table);
		}

		$myform = new form('form_search_galleries', 'post', false, array('tblLayout'=>false));
		$form_search = $myform->form($this->_plink->aLink($this->_instanceName, 'galleries'), false, '');
		$form_search .= $myform->input('search_text', 'text', htmlInput($search_text), array('hint'=>_('cerca nelle gallerie'), 'size'=>20, 'maxlength'=>40));
		$form_search .= '&#160;'.$myform->input('submit_search', 'submit', _('cerca'), array('classField'=>'submit'));
		$form_search .= '&#160;'.$myform->input('submit_search_all', 'submit', _('tutte'), array('classField'=>'submit'));
		$form_search .= $myform->cform();
			
		$view->assign('title', $this->_title_list_galleries);
		$view->assign('form_search', $form_search);
		$view->assign('search_text', $search_text);
		$view->assign('section_id', 'galleries_'.$this->_className.'_'.$this->_instanceName);
		$view->assign('pagination_summary', $pagination->reassumedPrint());
		$view->assign('pagination_navigation', $pagination->listReferenceGINO($this->_plink->aLink($this->_instanceName, 'galleries', '', null, array("basename"=>false))));
		
		return $view->render();

	}

	/**
	 * Setta in variabili di sessione i media ottenuti dalla ricerca e la ricerca stessa
	 * 
	 * @param multimediaGallery $gallery galleria all'interno della quale cercare i media
	 * @access private
	 * @return void
	 */
	private function actionSearchMedia($gallery) {

		require_once(CLASSES_DIR.OS.'class.search.php');

		$db = db::instance();
		$session = session::instance();

		$search_string = cleanVar($_POST, 'search_text', 'string', '');

		$keywords = $this->getKeywords($search_string);

		if(!count($keywords) || isset($_POST['submit_search_all'])) {
			unset($session->multimedia_media_search_results);
			unset($session->multimedia_media_search);
			return 0;
		}

		$relevance_mname = $this->_relevance_media_mname;
		$relevance_mdescription = $this->_relevance_media_mdescription;
		$relevance_mtags = $this->_relevance_media_mtags;

		if($this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, $this->_user_group, $this->_group_2)) {
			$private = true;
		}
		else {
			$private = false;
		}

		$query = "SELECT DISTINCT(m.id), ";
		$relevance = "(";
		$i = 0;
		foreach($keywords as $search_text) {
			if($i) $relevance .= " + ";
			$i++;
			$relevance .= " + IFNULL(
				((m.name REGEXP '[[:<:]]".$search_text."[[:>:]]') > 0) * ".$relevance_mname.", 0
			)";
			$relevance .= " + IFNULL(
				((m.description REGEXP '[[:<:]]".$search_text."[[:>:]]') > 0) * ".$relevance_mdescription.", 0
			)";
			$relevance .= " + IFNULL(
				((m.tags REGEXP '[[:<:]]".$search_text."[[:>:]]') > 0) * ".$relevance_mtags.", 0
			)";
		}
		$relevance .= ")";
    $query .= $relevance." AS relevance, ";
		$occurences = "(";
		$i = 0;
		foreach($keywords as $search_text) {
			if($i) $occurences .= " + ";
			$i++;
			$occurences .= "+ IFNULL(
				((LENGTH(m.name) - LENGTH(replace_ci(m.name, '".$search_text."', '')))/LENGTH('".$search_text."')), 0 
			)";
			$occurences .= "+ IFNULL(
				((LENGTH(m.description) - LENGTH(replace_ci(m.description, '".$search_text."', '')))/LENGTH('".$search_text."')), 0 
			)";
			$occurences .= "+ IFNULL(
				((LENGTH(m.tags) - LENGTH(replace_ci(m.tags, '".$search_text."', '')))/LENGTH('".$search_text."')), 0 
			)";
		}
		$occurences .= ")";
    $query .= $occurences.' AS occurences ';
		$query .= "FROM ".multimediaItem::$tbl_item." AS m ";
		$query .= "WHERE m.galleries REGEXP '[[:<:]]".$gallery->id."[[:>:]]' AND ";
		if(!$private) {
			$query .= "m.private = '0' AND ";
		}
		$query .= "(";
		$i = 0;
		foreach($keywords as $search_text) {
			if($i) $query .= " OR ";
			$i++;
			$query .= "m.name REGEXP '[[:<:]]".$search_text."[[:>:]]' OR ";
			$query .= "m.description REGEXP '[[:<:]]".$search_text."[[:>:]]' OR ";
			$query .= "m.tags REGEXP '[[:<:]]".$search_text."[[:>:]]'";
		}
		$query .= ")";
		$query .= " ORDER BY (relevance + occurences) DESC";

		$rows = $db->selectquery($query);

		$results = array();

		if(count($rows)) {
			foreach($rows as $row) {
				$results[] = $row['id'];
			}
		}

		$session->multimedia_media_search_results = $results;
		$session->multimedia_media_search = $search_string;
	}

	/**
	 * Setta in variabili di sessione le gallerie ottenute dalla ricerca e la ricerca stessa
	 * 
	 * @access private
	 * @return void
	 */
	private function actionSearchGalleries() {

		require_once(CLASSES_DIR.OS.'class.search.php');

		$db = db::instance();
		$session = session::instance();

		$search_string = cleanVar($_POST, 'search_text', 'string', '');

		$keywords = $this->getKeywords($search_string);

		if(!count($keywords) || isset($_POST['submit_search_all'])) {
			unset($session->multimedia_galleries_search_results);
			unset($session->multimedia_galleries_search);
			return 0;
		}

		$relevance_gname = $this->_relevance_gallery_gname;
		$relevance_gdescription = $this->_relevance_gallery_gdescription;
		$relevance_mname = $this->_relevance_gallery_mname;
		$relevance_mdescription = $this->_relevance_gallery_mdescription;
		$relevance_mtags = $this->_relevance_gallery_mtags;

		if($this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, $this->_user_group, $this->_group_2)) {
			$private = true;
		}
		else {
			$private = false;
		}

		$query = "SELECT DISTINCT(g.id), ";
		$relevance = "(";
		$i = 0;
		foreach($keywords as $search_text) {
			if($i) $relevance .= " + ";
			$i++;
			$relevance .= "IFNULL(
				((g.name REGEXP '[[:<:]]".$search_text."[[:>:]]') > 0) * ".$relevance_gname.", 0
			)";
			$relevance .= " + IFNULL(
				((g.description REGEXP '[[:<:]]".$search_text."[[:>:]]') > 0) * ".$relevance_gdescription.", 0
			)";
			$relevance .= " + SUM(IFNULL(
				((m.name REGEXP '[[:<:]]".$search_text."[[:>:]]') > 0) * ".$relevance_mname.", 0
			))";
			$relevance .= " + SUM(IFNULL(
				((m.description REGEXP '[[:<:]]".$search_text."[[:>:]]') > 0) * ".$relevance_mdescription.", 0
			))";
			$relevance .= " + SUM(IFNULL(
				((m.tags REGEXP '[[:<:]]".$search_text."[[:>:]]') > 0) * ".$relevance_mtags.", 0
			))";
		}
		$relevance .= ")";
    $query .= $relevance.' AS relevance, ';
		$occurences = "(";
		$i = 0;
		foreach($keywords as $search_text) {
			if($i) $occurences .= " + ";
			$i++;
			$occurences .= "IFNULL(
				((LENGTH(g.name) - LENGTH(replace_ci(g.name, '".$search_text."', '')))/LENGTH('".$search_text."')), 0 
			)";
			$occurences .= "+ IFNULL(
				((LENGTH(g.description) - LENGTH(replace_ci(g.description, '".$search_text."', '')))/LENGTH('".$search_text."')), 0 
			)";
			$occurences .= "+ SUM(IFNULL(
				((LENGTH(m.name) - LENGTH(replace_ci(m.name, '".$search_text."', '')))/LENGTH('".$search_text."')), 0 
			))";
			$occurences .= "+ SUM(IFNULL(
				((LENGTH(m.description) - LENGTH(replace_ci(m.description, '".$search_text."', '')))/LENGTH('".$search_text."')), 0 
			))";
			$occurences .= "+ SUM(IFNULL(
				((LENGTH(m.tags) - LENGTH(replace_ci(m.tags, '".$search_text."', '')))/LENGTH('".$search_text."')), 0 
			))";
		}
		$occurences .= ")";
    $query .= $occurences.' AS occurences ';
		$query .= "FROM ".multimediaGallery::$tbl_gallery." AS g, ".multimediaItem::$tbl_item." AS m ";
		$query .= "WHERE m.galleries REGEXP CONCAT('[[:<:]]', g.id, '[[:>:]]') AND ";
		if(!$private) {
			$query .= "g.private = '0' AND ";
			$query .= "m.private = '0' AND ";
		}
		$query .= "(";
		$i = 0;
		foreach($keywords as $search_text) {
			if($i) $query .= " OR ";
			$i++;
			$query .= "g.name REGEXP '[[:<:]]".$search_text."[[:>:]]' OR ";
			$query .= "g.description REGEXP '[[:<:]]".$search_text."[[:>:]]' OR ";
			$query .= "m.name REGEXP '[[:<:]]".$search_text."[[:>:]]' OR ";
			$query .= "m.description REGEXP '[[:<:]]".$search_text."[[:>:]]' OR ";
			$query .= "m.tags REGEXP '[[:<:]]".$search_text."[[:>:]]'";
		}
		$query .= ")";
		$query .= " GROUP BY g.id ORDER BY (".$relevance." + ".$occurences.") DESC";

		$rows = $db->selectquery($query);

		$results = array();

		if(count($rows)) {
			foreach($rows as $row) {
				$results[] = $row['id'];
			}
		}

		$session->multimedia_galleries_search_results = $results;
		$session->multimedia_galleries_search = $search_string;
	}

	/**
	 * Ritorna keywords di ricerca a partire da un stringa di ricerca 
	 * 
	 * @param string $search_string 
	 * @access private
	 * @return void
	 */
	private function getKeywords($search_string) {
		
		$clean_string = $this->clearSearchString($search_string);

		$empty_array = array(""," ");

		return  array_diff(array_unique(explode(" ", $clean_string)), $empty_array);
	}

	/**
	 * Rimuove da una stringa di ricerca le parole non significative 
	 * 
	 * @param string $search_string stringa di ricerca
	 * @access private
	 * @return stringa ripulita
	 */
	private function clearSearchString($search_string) {

		$unconsidered = array("lo", "l", "il", "la", "i", "gli", "le", "uno", "un", "una", "un", "su", "sul", "sulla", "sullo", "sull", "in", "nel", "nello", "nella", "nell", "con", "di", "da", "dei", "d",  "della", "dello", "del", "dell", "che", "a", "dal", "è", "e", "per", "non", "si", "al", "ai", "allo", "all", "al", "o");

		$clean_string = strtolower($search_string);

		$clean_string = preg_replace("#\b(".implode("|", $unconsidered).")\b#", "", $clean_string);
		$clean_string = preg_replace("#\W|(\s+)#", " ", $clean_string);

		$clean_string = preg_quote($clean_string);
	
		return $clean_string;
	}

	/**
	 * Parserizzazione del template inserito da opzioni per la vista lista gallerie 
	 * 
	 * @param multimediaGallery $gallery istanza di galleria
	 * @param string $tpl codice del template 
	 * @param array $matches matches delle variabili da sostituire
	 * @return template parserizzato
	 */
	private function parseTemplate($gallery, $tpl, $matches) {

		if(isset($matches[0])) {
			foreach($matches[0] as $m) {
				$code = trim(preg_replace("#{|}#", "", $m));
				if($pos = strrpos($code, '|')) {
					$property = substr($code, 0, $pos);
					$filter = substr($code, $pos + 1);
				}
				else {
					$property = $code;
					$filter = null;
				}

				$replace = $this->replaceTplVar($property, $filter, $gallery);
				$tpl = preg_replace("#".preg_quote($m)."#", $replace, $tpl);
			} 
		}

		return $tpl;
	}

	/**
	 * Replace di una proprietà di multimediGallery all'interno del template 
	 * 
	 * @param mixed $property proprietà da sostituire
	 * @param mixed $filter filtro applicato
	 * @param mixed $obj istanza della galleria
	 * @return replace del parametro proprietà
	 */
	private function replaceTplVar($property, $filter, $obj) {

		$pre_filter = '';

		if($property == 'thumb') {
			$pre_filter = "<img src=\"".$obj->thumbPath($this)."\" alt=\"".jsVar($obj->name)."\" />";	
		}
    elseif($property == 'thumb_url') {
			$pre_filter = $obj->thumbPath($this);	
		}
		elseif($property == 'last_edit_date' || $property == 'insertion_date') {
			$pre_filter = date('d/m/Y', strtotime($obj->{$property}));
		}
		elseif($property == 'description' || $property == 'name') {
			$pre_filter = htmlChars($obj->{$property});
		}
		else {
			return '';
		}

		if(is_null($filter)) {
			return $pre_filter;
		}

		if($filter == 'link') {
			return "<a href=\"".$this->_plink->aLink($this->_instanceName, 'gallery', array('id'=>$obj->slug))."\">".$pre_filter."</a>";
		}
		elseif(preg_match("#chars:(\d+)#", $filter, $matches)) {
			return cutHtmlText($pre_filter, $matches[1], '...', false, false, true, array('endingPosition'=>'in'));
		}
    elseif(preg_match("#class:(.+)#", $filter, $matches)) {
			if(isset($matches[1]) && $property == 'thumb') {
				return preg_replace("#<img#", "<img class=\"".$matches[1]."\"", $pre_filter);
			}
			else return $pre_filter;
		}

	}

	/**
	 * Interfaccia di amministrazione del modulo 
	 * 
	 * @return interfaccia di back office
	 */
	public function manageDoc() {

		$this->accessGroup('ALL');
			
		$method = 'manageDoc';

		$htmltab = new htmlTab(array("linkPosition"=>'right', "title"=>$this->_instanceLabel));	
		$link_admin = "<a href=\"".$this->_home."?evt[$this->_instanceName-$method]&block=permissions\">"._("Permessi")."</a>";
		$link_css = "<a href=\"".$this->_home."?evt[$this->_instanceName-$method]&block=css\">"._("CSS")."</a>";
		$link_options = "<a href=\"".$this->_home."?evt[$this->_instanceName-$method]&block=options\">"._("Opzioni")."</a>";
		$link_license = "<a href=\"".$this->_home."?evt[$this->_instanceName-$method]&block=license\">"._("Licenze")."</a>";
		$link_video_platform = "<a href=\"".$this->_home."?evt[$this->_instanceName-$method]&block=video_platform\">"._("Piattaforme streaming video")."</a>";
		$link_gallery = "<a href=\"".$this->_home."?evt[$this->_instanceName-$method]&block=gallery\">"._("Gallerie")."</a>";
		$link_slider = "<a href=\"".$this->_home."?evt[$this->_instanceName-$method]&block=slider\">"._("Slider")."</a>";
		$link_audio = "<a href=\"".$this->_home."?evt[$this->_instanceName-$method]&block=audio\">"._("Audio")."</a>";
		$link_video = "<a href=\"".$this->_home."?evt[$this->_instanceName-$method]&block=video\">"._("Video")."</a>";
		$link_dft = "<a href=\"".$this->_home."?evt[".$this->_instanceName."-$method]\">"._("Immagini")."</a>";
		
		$sel_link = $link_dft;

		// Variables
		$id = cleanVar($_GET, 'id', 'int', '');
		$start = cleanVar($_GET, 'start', 'int', '');
		// end
		
		$registry = registry::instance();
		$registry->addCss($this->_class_www."/classroom.css");

		if($this->_block == 'css') {
			$buffer = sysfunc::manageCss($this->_instance, $this->_className);		
			$sel_link = $link_css;
		}
		elseif($this->_block == 'permissions' && $this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, '', '')) {
			$buffer = sysfunc::managePermissions($this->_instance, $this->_className);		
			$sel_link = $link_admin;
		}
		elseif($this->_block == 'options' && $this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, '', '')) {
			$buffer = sysfunc::manageOptions($this->_instance, $this->_className);		
			$sel_link = $link_options;
		}
		elseif($this->_block == 'license' && $this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, '', '')) {
			$buffer = $this->manageLicense();
			$sel_link = $link_license;
		}
		elseif($this->_block == 'video_platform' && $this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, '', '')) {
			$buffer = $this->manageVideoPlatform();
			$sel_link = $link_video_platform;
		}
		elseif($this->_block == 'slider') {
			$buffer = $this->manageSlider();
			$sel_link = $link_slider;
		}
		elseif($this->_block == 'gallery') {
			$buffer = $this->manageGallery();
			$sel_link = $link_gallery;
		}
		elseif($this->_block == 'audio') {
			$buffer = $this->manageAudio();
			$sel_link = $link_audio;
		}
		elseif($this->_block == 'video') {
			$buffer = $this->manageVideo();
			$sel_link = $link_video;
		}
		else {
			$buffer = $this->manageImage();
		}

		// groups privileges
		if($this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, '', '')) {
			$links_array = array($link_admin, $link_css, $link_options, $link_license, $link_video_platform, $link_slider, $link_gallery, $link_audio, $link_video, $link_dft);
		}
		elseif($this->_access->AccessVerifyGroupIf($this->_className, $this->_instance, $this->_user_group, $this->_group_1)) {
			$links_array = array($link_gallery, $link_audio, $link_video, $link_dft);
		}
		else $links_array = array($link_dft);

		$htmltab->navigationLinks = $links_array;
		$htmltab->selectedLink = $sel_link;
		$htmltab->htmlContent = $buffer;

		return $htmltab->render();
	}

	/**
	 * Interfaccia di amministrazione dello slider 
	 * 
	 * @return interfaccia di back office
	 */
	private function manageSlider() {
		
		$registry = registry::instance();

		$slider = multimediaSlider::getFromInstance($this);
		$allow_insertion = $slider->id ? false : true;

		$admin_table = new adminTable($this, array('allow_insertion'=>$allow_insertion, 'delete_deny'=>'all'));

		$buffer = $admin_table->backOffice(
				'multimediaSlider', 
				array(
					'list_display' => array('id', 'gallery', 'transition_effect'),
					'list_title'=>_("Slider"), 
					'list_description'=>"<p>"._("E' possibile configurare un solo slider")."</p>",
				     ),
				array(), 
				array(
					'transition_effect' =>array(
						'widget' => 'select'
					)
				)
		);

		return $buffer;
	}

	/**
	 * Interfaccia di amministrazione dei media di tipo immagine 
	 * 
	 * @return interfaccia di back office
	 */
	private function manageImage() {

		require_once('class.multimediaItemAdminTable.php');

		$registry = registry::instance();
		$registry->addJs($this->_class_www.'/MooComplete.js');
		$registry->addCss($this->_class_www.'/MooComplete.css');

		$admin_table = new multimediaItemAdminTable($this, array());

		$buffer = javascript::abiMapLib();

		$tags = multimediaTag::getAllList($this->_instance, array('jsescape'=>true));
		$js_tags_list = "['".implode("','", $tags)."']";
		
		$buffer .= "<script type=\"text/javascript\">";
		$buffer .= "function convert() {
				var addressConverter = new AddressToPointConverter('map_coord', 'lat', 'lng', $('map_address').value, {'canvasPosition':'over'});
				addressConverter.showMap();
			}\n";
		$buffer .= "window.addEvent('load', function() {
			var tag_input = new MooComplete('tags', {
  				list: $js_tags_list, // elements to use to suggest.
  				mode: 'tag', // suggestion mode (tag | text)
  				size: 5 // number of elements to suggest
			});
		})";
		$buffer .= "</script>";
		$onclick = "onclick=\"Asset.javascript('http://maps.google.com/maps/api/js?sensor=true&callback=convert')\"";
		$gform = new Form('', '', '');
		$convert_button = $gform->input('map_coord', 'button', _("converti"), array("id"=>"map_coord", "classField"=>"generic", "js"=>$onclick));		
		$addCell = array(
			'lat'=>$gform->cinput('map_address', 'text', '', array(_("Indirizzo localizzazione media"), _("es: torino, via mazzini 37<br />utilizzare 'converti' per calcolare latitudine e longitudine")), array("size"=>40, "maxlength"=>200, "id"=>"map_address", "text_add"=>"<p>".$convert_button."</p>"))
		);

		$buffer .= $admin_table->backOffice(
			'multimediaImage', 
			array(
				'list_display' => array('id', 'name', 'galleries', 'published', 'private', 'tags'),
				'filter_fields'=>array('name', 'galleries', 'published', 'private', 'tags'), 
				'list_title'=>_("Elenco file immagine"), 
				'list_description'=>"",
				'list_where'=>array("type='".IMAGE_CHOICE."'"), 
			     ),
			array(
				'removeFields' => array(
					'video_code',
					'video_platform',
					'video_width',
					'video_height',
					'mpeg_filename',
					'ogg_filename',
				),
				'addCell' => $addCell
			), 
			array(
				'description' => array(
					'widget'=>'editor', 
					'notes'=>false, 
					'img_preview'=>false, 
				),
				'tags' => array(
					'id' => 'tags',
					'size' => 60,
					'max_length' => 255
				),
				'thumb' => array(
					'preview' => true
				),
				'img_filename' => array(
					'preview' => true
				),
				'lat' => array(
					'id' => 'lat'
				),
				'lng' => array(
					'id' => 'lng'
				)
			)
		);

		return $buffer;
	}

	/**
	 * Interfaccia di amministrazione dei media di tipo video 
	 * 
	 * @return interfaccia di back office
	 */
	private function manageVideo() {

		require_once('class.multimediaItemAdminTable.php');

		$registry = registry::instance();
		$registry->addJs($this->_class_www.'/MooComplete.js');
		$registry->addCss($this->_class_www.'/MooComplete.css');

		$admin_table = new multimediaItemAdminTable($this, array());

		$buffer = javascript::abiMapLib();

		$tags = multimediaTag::getAllList($this->_instance, array('jsescape'=>true));
		$js_tags_list = "['".implode("','", $tags)."']";
		
		$buffer .= "<script type=\"text/javascript\">";
		$buffer .= "function convert() {
				var addressConverter = new AddressToPointConverter('map_coord', 'lat', 'lng', $('map_address').value, {'canvasPosition':'over'});
				addressConverter.showMap();
			}\n";
		$buffer .= "window.addEvent('load', function() {
			var tag_input = new MooComplete('tags', {
  				list: $js_tags_list, // elements to use to suggest.
  				mode: 'tag', // suggestion mode (tag | text)
  				size: 5 // number of elements to suggest
			});
		})";
		$buffer .= "</script>";
		$onclick = "onclick=\"Asset.javascript('http://maps.google.com/maps/api/js?sensor=true&callback=convert')\"";
		$gform = new Form('', '', '');
		$convert_button = $gform->input('map_coord', 'button', _("converti"), array("id"=>"map_coord", "classField"=>"generic", "js"=>$onclick));		
		$addCell = array(
			'lat'=>$gform->cinput('map_address', 'text', '', array(_("Indirizzo localizzazione media"), _("es: torino, via mazzini 37<br />utilizzare 'converti' per calcolare latitudine e longitudine")), array("size"=>40, "maxlength"=>200, "id"=>"map_address", "text_add"=>"<p>".$convert_button."</p>"))
		);

		$buffer .= $admin_table->backOffice(
			'multimediaVideo', 
			array(
				'list_display' => array('id', 'name', 'galleries', 'published', 'private', 'video_code', 'video_platform', 'tags'),
				'filter_fields'=>array('name', 'galleries', 'published', 'private', 'tags'), 
				'list_title'=>_("Elenco file video"), 
				'list_description'=>"<p>"._('Ricordarsi di inserire la larghezza ed altezza del video per una corretta visualizzazione.')."</p>",
				'list_where'=>array("type='".VIDEO_CHOICE."'"), 
			     ),
			array(
				'removeFields' => array(
					'img_filename',
					'mpeg_filename',
					'ogg_filename',
				),
				'addCell' => $addCell
			), 
			array(
				'description' => array(
					'widget'=>'editor', 
					'notes'=>false, 
					'img_preview'=>false, 
				),
				'tags' => array(
					'id' => 'tags',
					'size' => 60,
					'max_length' => 255
				),
				'thumb' => array(
					'preview' => true
				),
				'lat' => array(
					'id' => 'lat'
				),
				'lng' => array(
					'id' => 'lng'
				)
			)
		);

		return $buffer;
	}

	/**
	 * Interfaccia di amministrazione dei media di tipo audio 
	 * 
	 * @return interfaccia di back office
	 */
	private function manageAudio() {

		require_once('class.multimediaItemAdminTable.php');

		$registry = registry::instance();
		$registry->addJs($this->_class_www.'/MooComplete.js');
		$registry->addCss($this->_class_www.'/MooComplete.css');

		$admin_table = new multimediaItemAdminTable($this, array());

		$buffer = javascript::abiMapLib();

		$tags = multimediaTag::getAllList($this->_instance, array('jsescape'=>true));
		$js_tags_list = "['".implode("','", $tags)."']";
		
		$buffer .= "<script type=\"text/javascript\">";
		$buffer .= "function convert() {
				var addressConverter = new AddressToPointConverter('map_coord', 'lat', 'lng', $('map_address').value, {'canvasPosition':'over'});
				addressConverter.showMap();
			}\n";
		$buffer .= "window.addEvent('load', function() {
			var tag_input = new MooComplete('tags', {
  				list: $js_tags_list, // elements to use to suggest.
  				mode: 'tag', // suggestion mode (tag | text)
  				size: 5 // number of elements to suggest
			});
		})";
		$buffer .= "</script>";
		$onclick = "onclick=\"Asset.javascript('http://maps.google.com/maps/api/js?sensor=true&callback=convert')\"";
		$gform = new Form('', '', '');
		$convert_button = $gform->input('map_coord', 'button', _("converti"), array("id"=>"map_coord", "classField"=>"generic", "js"=>$onclick));		
		$addCell = array(
			'lat'=>$gform->cinput('map_address', 'text', '', array(_("Indirizzo localizzazione media"), _("es: torino, via mazzini 37<br />utilizzare 'converti' per calcolare latitudine e longitudine")), array("size"=>40, "maxlength"=>200, "id"=>"map_address", "text_add"=>"<p>".$convert_button."</p>"))
		);

		$buffer .= $admin_table->backOffice(
			'multimediaAudio', 
			array(
				'list_display' => array('id', 'name', 'galleries', 'published', 'private', 'mpeg_filename', 'ogg_filename', 'tags'),
				'filter_fields'=>array('name', 'galleries', 'published', 'private', 'tags'), 
				'list_title'=>_("Elenco file audio"), 
				'list_description'=>"<p>"._('Si consgilia di caricare i contenuti audio in due formati, mp3 ed ogg, per garantire la visualizzazione su diversi browser.')."</p>",
				'list_where'=>array("type='".AUDIO_CHOICE."'"), 
			     ),
			array(
				'removeFields' => array(
					'img_filename',
					'video_code',
					'video_platform',
					'video_width',
					'video_height',
				),
				'addCell' => $addCell
			), 
			array(
				'description' => array(
					'widget'=>'editor', 
					'notes'=>false, 
					'img_preview'=>false, 
				),
				'tags' => array(
					'id' => 'tags',
					'size' => 60,
					'max_length' => 255
				),
				'thumb' => array(
					'preview' => true
				),
				'lat' => array(
					'id' => 'lat'
				),
				'lng' => array(
					'id' => 'lng'
				)
			)
		);

		return $buffer;
	}

	/**
	 * Interfaccia amministrativa per la gestione di gallerie 
	 * 
	 * @return interfaccia di backoffice per le gallerie
	 */
	private function manageGallery() {
		
		$registry = registry::instance();
		$registry->addJs($this->_class_www.'/multimedia.js');

		$admin_table = new adminTable($this, array());

    $edit = cleanVar($_GET, 'edit', 'int', '');

    $name_onblur = !$edit 
      ? "onblur=\"$('slug').value = $(this).value.slugify()\""
      : "";

		$buffer = $admin_table->backOffice(
			'multimediaGallery', 
			array(
				'list_display' => array('id', 'thumb', 'name', 'slug', 'published', 'private', 'last_edit_date', 'promoted'),
				'list_title'=>_("Elenco gallerie multimediali"), 
				'list_description'=>"<p>"._('Ciascun media inserito potrà essere associato ad una o più gallerie multimediali qui definite.')."</p>",
			     ),
			array(), 
			array(
				'name' => array(
					'js' => $name_onblur
				),
				'slug' => array(
					'id' => 'slug'
				),
				'description' => array(
					'widget'=>'editor', 
					'notes'=>false, 
					'img_preview'=>false, 
				),
				'thumb' => array(
					'preview' => true
				),
			)
		);

		return $buffer;
	}

	/**
	 * Interfaccia amministrativa per la gestione di piattaforme video 
	 * 
	 * @return interfaccia di backoffice per le piattaforme video
	 */
	private function manageVideoPlatform() {

		$admin_table = new adminTable($this, array());
		
		$buffer = $admin_table->backOffice(
			'multimediaVideoPlatform', 
			array(
				'list_display' => array('id', 'name', 'base_url'),
				'filter_fields'=>array('name'), 
				'list_title'=>_("Elenco piattaform streaming video"), 
				'list_description'=>"<p>"._('Le seguenti piattaforme possono essere utilizzate per inserire contenuti multimediali video. I video vengono caricati dentro ad un iframe che richiama l\'url base qui definito cui viene concatenato il codice video. Attualmente lo script js moogallery che gestisce la visualizzazione dei media supporta youtube e vimeo (i nomi delle piattaforme video devono essere esattamente questi). In caso di aggiunte modificare lo script moogallery.js.')."</p>",
			),
			array(), 
			array()
		);
		
		return $buffer;
	}

	/**
	 * Interfaccia amministrativa per la gestione di licenze 
	 * 
	 * @return interfaccia di backoffice per le licenze
	 */
	private function manageLicense() {

		$admin_table = new adminTable($this, array());
		
		$buffer = $admin_table->backOffice(
			'multimediaLicense', 
			array(
				'list_display' => array('id', 'name', 'url'),
				'filter_fields'=>array('name'), 
				'list_title'=>_("Elenco licenze"), 
				'list_description'=>"<p>"._('Le seguenti licenze sono associabili ai media inseriti')."</p>",
			),
			array(), 
			array(
				'description' => array(
					'widget'=>'editor', 
					'notes'=>false, 
					'img_preview'=>false, 
				)
			)
		);
		
		return $buffer;
	}

} 

?>

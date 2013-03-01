--
-- Table structure for table `multimedia_gallery`
--

CREATE TABLE IF NOT EXISTS `multimedia_gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `description` text,
  `thumb` varchar(255) NOT NULL,
  `published` int(1) NOT NULL,
  `private` int(1) NOT NULL,
  `insertion_date` datetime NOT NULL,
  `last_edit_date` datetime NOT NULL,
  `promoted` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `multimedia_grp`
--

CREATE TABLE IF NOT EXISTS `multimedia_grp` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `no_admin` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `multimedia_grp`
--

INSERT INTO `multimedia_grp` (`id`, `name`, `description`, `no_admin`) VALUES
(1, 'responsabili', 'Gestiscono l''assegnazione degli utenti ai singoli gruppi.', 'no'),
(2, 'redazione', 'Gestisce la redazione dei contenuti: inserimento modifica ed eliminazione di gallerie e media', 'no'),
(3, 'Visualizzatori', 'Visualizzano anche le gallerie private', 'yes');

-- --------------------------------------------------------

--
-- Table structure for table `multimedia_item`
--

CREATE TABLE IF NOT EXISTS `multimedia_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(11) NOT NULL,
  `type` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `galleries` varchar(255) NOT NULL,
  `description` text,
  `tags` varchar(255) DEFAULT NULL,
  `credits` text,
  `license` int(2) DEFAULT NULL,
  `lat` varchar(64) DEFAULT NULL,
  `lng` varchar(64) DEFAULT NULL,
  `thumb` varchar(200) DEFAULT NULL,
  `insertion_date` datetime NOT NULL,
  `last_edit_date` datetime NOT NULL,
  `published` int(1) NOT NULL,
  `private` int(1) NOT NULL,
  `img_filename` varchar(128) DEFAULT NULL,
  `video_code` varchar(64) DEFAULT NULL,
  `video_platform` int(11) DEFAULT NULL,
  `video_width` int(3) DEFAULT NULL,
  `video_height` int(3) DEFAULT NULL,
  `mpeg_filename` varchar(128) DEFAULT NULL,
  `ogg_filename` varchar(128) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `multimedia_item_tag`
--

CREATE TABLE IF NOT EXISTS `multimedia_item_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item` int(11) NOT NULL,
  `tag` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `multimedia_license`
--

CREATE TABLE IF NOT EXISTS `multimedia_license` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `description` text,
  `url` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `multimedia_opt`
--

CREATE TABLE IF NOT EXISTS `multimedia_opt` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(11) NOT NULL,
  `title_box` varchar(200) NOT NULL,
  `title_list_galleries` varchar(200) NOT NULL,
  `title_map` varchar(200) NOT NULL,
  `title_slider` varchar(200) NOT NULL,
  `image_max_width` int(5) NOT NULL,
  `thumb_dimension` int(6) NOT NULL,
  `box_promoted_code` text NOT NULL,
  `box_tpl_code` text NOT NULL,
  `box_num_galleries` int(2) NOT NULL,
  `list_galleries_tpl` int(2) NOT NULL,
  `list_galleries_tpl_ifp` int(3) NOT NULL,
  `gallery_ifp` int(3) NOT NULL,
  `list_galleries_tpl1_code` text NOT NULL,
  `list_galleries_tpl2_cols` int(2) DEFAULT NULL,
  `list_galleries_tpl2_code` text NOT NULL,
  `relevance_gallery_gname` int(4) NOT NULL,
  `relevance_gallery_gdescription` int(4) NOT NULL,
  `relevance_gallery_mname` int(4) NOT NULL,
  `relevance_gallery_mdescription` int(4) NOT NULL,
  `relevance_gallery_mtags` int(4) NOT NULL,
  `relevance_media_mname` int(4) NOT NULL,
  `relevance_media_mdescription` int(4) NOT NULL,
  `relevance_media_mtags` int(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `multimedia_slider`
--

CREATE TABLE IF NOT EXISTS `multimedia_slider` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(3) NOT NULL,
  `tpl` text NOT NULL,
  `image_order` int(1) NOT NULL,
  `gallery` int(4) NOT NULL,
  `animation_effect_duration` int(5) NOT NULL,
  `auto_play` int(1) NOT NULL,
  `show_ctrls` int(1) NOT NULL,
  `mouseout_hide_ctrls` int(1) NOT NULL,
  `transition_effect` int(2) NOT NULL,
  `animation_interval` int(5) NOT NULL,
  `pause_interval_mouseover` int(1) NOT NULL,
  `slices` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `multimedia_tag`
--

CREATE TABLE IF NOT EXISTS `multimedia_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Table structure for table `multimedia_usr`
--

CREATE TABLE IF NOT EXISTS `multimedia_usr` (
  `instance` int(11) NOT NULL,
  `group_id` int(2) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `multimedia_video_platform`
--

CREATE TABLE IF NOT EXISTS `multimedia_video_platform` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `instance` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `base_url` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE `groups` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `description` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `groups` (`id`, `name`, `description`)
VALUES
	(1,'admin','Admin'),
	(2,'members','Member');

CREATE TABLE `languages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(3) DEFAULT NULL,
  `google_code` varchar(8) DEFAULT NULL,
  `rtl` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `languages` (`id`, `name`, `code`, `google_code`, `rtl`)
VALUES
	(1,'Abkhazian','abk',NULL,0),
	(2,'Afar','aar',NULL,0),
	(3,'Afrikaans','afr',NULL,0),
	(4,'Akan','aka',NULL,0),
	(5,'Albanian','alb','sq',0),
	(6,'Amharic','amh','am',0),
	(7,'Arabic (Standard)','arb','ar',1),
	(8,'Aragonese','arg',NULL,0),
	(9,'Armenian','arm',NULL,0),
	(10,'Assamese','asm',NULL,0),
	(11,'Avaric','ava',NULL,0),
	(12,'Avestan','ave',NULL,1),
	(13,'Aymara','aym',NULL,0),
	(14,'Azerbaijani','aze',NULL,0),
	(15,'Bambara','bam',NULL,0),
	(16,'Bashkir','bak',NULL,0),
	(17,'Basque','baq','eu',0),
	(18,'Belarusian','bel',NULL,0),
	(19,'Bengali','ben','bn',0),
	(20,'Bihari languages','bih',NULL,0),
	(21,'Bislama','bis',NULL,0),
	(22,'Bosnian','bos',NULL,0),
	(23,'Breton','bre',NULL,0),
	(24,'Bulgarian','bul','bg',0),
	(25,'Burmese','bur',NULL,0),
	(26,'Catalan, Valencian','cat',NULL,0),
	(27,'Chamorro','cha',NULL,0),
	(28,'Chechen','che',NULL,0),
	(29,'Chichewa, Chewa, Nyanja','nya',NULL,0),
	(30,'Chinese','chi','ch-CN',0),
	(31,'Chuvash','chv',NULL,0),
	(32,'Cornish','cor',NULL,0),
	(33,'Corsican','cos',NULL,0),
	(34,'Cree','cre',NULL,0),
	(35,'Croatian','hrv','hr',0),
	(36,'Czech','cze','cs',0),
	(37,'Danish','dan','da',0),
	(38,'Divehi, Dhivehi, Maldivian','div',NULL,1),
	(39,'Dutch, Flemish','dut','nl',0),
	(40,'Dzongkha','dzo',NULL,0),
	(41,'English','eng',NULL,0),
	(42,'Esperanto','epo',NULL,0),
	(43,'Estonian','est','et',0),
	(44,'Ewe','ewe',NULL,0),
	(45,'Faroese','fao',NULL,0),
	(46,'Fijian','fij',NULL,0),
	(47,'Finnish','fin','fi',0),
	(48,'French','fre','fr',0),
	(49,'Fulah','ful',NULL,1),
	(50,'Galician','glg',NULL,0),
	(51,'Georgian','geo',NULL,0),
	(52,'German','ger','de',0),
	(53,'Greek, Modern (1453-)','gre','el',0),
	(54,'Guarani','grn',NULL,0),
	(55,'Gujarati','guj','gu',0),
	(56,'Haitian, Haitian Creole','hat',NULL,0),
	(57,'Hausa','hau',NULL,1),
	(58,'Hebrew','heb','iw',1),
	(59,'Herero','her',NULL,0),
	(60,'Hindi','hin','hi',0),
	(61,'Hiri Motu','hmo',NULL,0),
	(62,'Hungarian','hun','hu',0),
	(63,'Indonesian','ind','id',0),
	(64,'Interlingue, Occidental','ile',NULL,0),
	(65,'Irish','gle',NULL,0),
	(66,'Igbo','ibo',NULL,0),
	(67,'Inupiaq','ipk',NULL,0),
	(68,'Ido','ido',NULL,0),
	(69,'Icelandic','ice','is',0),
	(70,'Italian','ita','it',0),
	(71,'Inuktitut','iku',NULL,0),
	(72,'Japanese','jpn','ja',0),
	(73,'Javanese','jav',NULL,0),
	(74,'Kalaallisut, Greenlandic','kal',NULL,0),
	(75,'Kannada','kan','kn',0),
	(76,'Kanuri','kau',NULL,0),
	(77,'Kashmiri','kas',NULL,1),
	(78,'Kazakh','kaz',NULL,0),
	(79,'Central Khmer','khm',NULL,0),
	(80,'Kikuyu, Gikuyu','kik',NULL,0),
	(81,'Kinyarwanda','kin',NULL,0),
	(82,'Kirghiz, Kyrgyz','kir',NULL,0),
	(83,'Komi','kom',NULL,0),
	(84,'Kongo','kon',NULL,0),
	(85,'Korean','kor','ko',0),
	(86,'Kurdish','kur',NULL,1),
	(87,'Kuanyama, Kwanyama','kua',NULL,0),
	(88,'Latin','lat',NULL,0),
	(89,'Luxembourgish, Letzeburgesch','ltz',NULL,0),
	(90,'Ganda','lug',NULL,0),
	(91,'Limburgan, Limburger, Limburgish','lim',NULL,0),
	(92,'Lingala','lin',NULL,0),
	(93,'Lao','lao',NULL,0),
	(94,'Lithuanian','lit','lt',0),
	(95,'Luba-Katanga','lub',NULL,0),
	(96,'Latvian','lav',NULL,0),
	(97,'Manx','glv',NULL,0),
	(98,'Macedonian','mac',NULL,0),
	(99,'Malagasy','mlg',NULL,0),
	(100,'Malay','may','ms',0),
	(101,'Malayalam','mal','ml',0),
	(102,'Maltese','mlt',NULL,0),
	(103,'Maori','mao',NULL,0),
	(104,'Marathi','mar','mr',0),
	(105,'Marshallese','mah',NULL,0),
	(106,'Mongolian','mon',NULL,0),
	(107,'Nauru','nau',NULL,0),
	(108,'Navajo, Navaho','nav',NULL,0),
	(109,'North Ndebele','nde',NULL,0),
	(110,'Nepali','nep',NULL,0),
	(111,'Ndonga','ndo',NULL,0),
	(112,'Norwegian Bokmål','nob',NULL,0),
	(113,'Norwegian Nynorsk','nno',NULL,0),
	(114,'Norwegian','nor','no',0),
	(115,'Sichuan Yi, Nuosu','iii',NULL,0),
	(116,'South Ndebele','nbl',NULL,0),
	(117,'Occitan','oci',NULL,0),
	(118,'Ojibwa','oji',NULL,0),
	(119,'Oromo','orm',NULL,0),
	(120,'Oriya','ori',NULL,0),
	(121,'Ossetian, Ossetic','oss',NULL,0),
	(122,'Panjabi, Punjabi','pan',NULL,0),
	(123,'Pali','pli',NULL,0),
	(124,'Persian','per',NULL,1),
	(125,'Polish','pol','pl',0),
	(126,'Pashto, Pushto','pus',NULL,0),
	(127,'Portuguese','por','pt',0),
	(128,'Quechua','que',NULL,0),
	(129,'Romansh','roh',NULL,0),
	(130,'Rundi','run',NULL,0),
	(131,'Romanian, Moldavian, Moldovan','rum','ro',0),
	(132,'Russian','rus','ru',0),
	(133,'Sanskrit','san',NULL,0),
	(134,'Sardinian','srd',NULL,0),
	(135,'Sindhi','snd',NULL,0),
	(136,'Northern Sami','sme',NULL,0),
	(137,'Samoan','smo',NULL,0),
	(138,'Sango','sag',NULL,0),
	(139,'Serbian','srp','sr',0),
	(140,'Gaelic, Scottish Gaelic','gla',NULL,0),
	(141,'Shona','sna',NULL,0),
	(142,'Sinhala, Sinhalese','sin',NULL,0),
	(143,'Slovak','slo','sk',0),
	(144,'Slovenian','slv','sl',0),
	(145,'Somali','som',NULL,0),
	(146,'Southern Sotho','sot',NULL,0),
	(147,'Spanish, Castilian','spa','es',0),
	(148,'Sundanese','sun',NULL,0),
	(149,'Swahili','swa','sw',0),
	(150,'Swati','ssw',NULL,0),
	(151,'Swedish','swe','sv',0),
	(152,'Tamil','tam','ta',0),
	(153,'Telugu','tel','te',0),
	(154,'Tajik','tgk',NULL,0),
	(155,'Thai','tha','th',0),
	(156,'Tigrinya','tir',NULL,0),
	(157,'Tibetan','tib',NULL,0),
	(158,'Turkmen','tuk',NULL,0),
	(159,'Tagalog','tgl','fil',0),
	(160,'Tswana','tsn',NULL,0),
	(161,'Tonga (Tonga Islands)','ton',NULL,0),
	(162,'Turkish','tur','tr',0),
	(163,'Tsonga','tso',NULL,0),
	(164,'Tatar','tat',NULL,0),
	(165,'Twi','twi',NULL,0),
	(166,'Tahitian','tah',NULL,0),
	(167,'Uighur, Uyghur','uig',NULL,0),
	(168,'Ukrainian','ukr','uk',0),
	(169,'Urdu','urd','ur',1),
	(170,'Uzbek','uzb',NULL,0),
	(171,'Venda','ven',NULL,0),
	(172,'Vietnamese','vie','vi',0),
	(173,'Volapük','vol',NULL,0),
	(174,'Walloon','wln',NULL,0),
	(175,'Welsh','wel','cy',0),
	(176,'Wolof','wol',NULL,0),
	(177,'Western Frisian','fry',NULL,0),
	(178,'Xhosa','xho',NULL,0),
	(179,'Yiddish','yid',NULL,1),
	(180,'Yoruba','yor',NULL,0),
	(181,'Zhuang, Chuang','zha',NULL,0),
	(182,'Zulu','zul',NULL,0);


CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(254) DEFAULT NULL,
  `activation_selector` varchar(255) DEFAULT NULL,
  `activation_code` varchar(255) DEFAULT NULL,
  `forgotten_password_selector` varchar(255) DEFAULT NULL,
  `forgotten_password_code` varchar(255) DEFAULT NULL,
  `forgotten_password_time` int(11) unsigned DEFAULT NULL,
  `remember_selector` varchar(255) DEFAULT NULL,
  `remember_code` varchar(255) DEFAULT NULL,
  `created_on` int(11) unsigned NOT NULL,
  `last_login` int(11) unsigned DEFAULT NULL,
  `active` tinyint(1) unsigned DEFAULT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `company` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `bio` text,
  `mother_language_id` int(11) unsigned DEFAULT NULL,
  `skills` text,
  `product_notify` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pro_translator` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_email` (`email`),
  UNIQUE KEY `uc_activation_selector` (`activation_selector`),
  UNIQUE KEY `uc_forgotten_password_selector` (`forgotten_password_selector`),
  UNIQUE KEY `uc_remember_selector` (`remember_selector`),
  KEY `mother_language_id` (`mother_language_id`),
  CONSTRAINT `users_ibfk_1` FOREIGN KEY (`mother_language_id`) REFERENCES `languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `login_attempts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(45) NOT NULL,
  `login` varchar(100) NOT NULL,
  `time` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `series` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `products` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `description` text,
  `cover_image` varchar(255) DEFAULT NULL,
  `author` varchar(255) DEFAULT NULL,
  `page_count` int(4) DEFAULT NULL,
  `type` enum('book','booklet','magabook','tract') DEFAULT 'book',
  `idml_file` varchar(255) DEFAULT NULL,
  `audience` varchar(255) DEFAULT NULL,
  `publisher` varchar(255) DEFAULT NULL,
  `format_open` varchar(32) DEFAULT NULL,
  `format_closed` varchar(32) DEFAULT NULL,
  `cover_colors` varchar(32) DEFAULT NULL,
  `cover_paper` varchar(32) DEFAULT NULL,
  `interior_colors` varchar(32) DEFAULT NULL,
  `interior_paper` varchar(32) DEFAULT NULL,
  `binding` varchar(32) DEFAULT NULL,
  `finishing` varchar(32) DEFAULT NULL,
  `publisher_website` varchar(255) DEFAULT NULL,
  `series_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `series_id` (`series_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`series_id`) REFERENCES `series` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `projects` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `language_id` int(11) unsigned NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `language_id` (`language_id`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `projects_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `product_sections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  `style`  varchar(255) DEFAULT NULL,
  `node_id`  varchar(255) DEFAULT NULL,
  `xliff_region` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_sections_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `product_attachments` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `language_id` int(11) unsigned DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `file_type` enum('pdf_printing','pdf_personal','indd') DEFAULT NULL,
  `product_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `language_id` (`language_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `product_attachments_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`),
  CONSTRAINT `product_attachments_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `product_content` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(11) unsigned NOT NULL,
  `content` text,
  `section_id` int(11) unsigned NOT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `xliff_tag` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `section_id` (`section_id`),
  CONSTRAINT `product_content_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_content_ibfk_2` FOREIGN KEY (`section_id`) REFERENCES `product_sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `product_content_sentence` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `product_content_id` int(11) unsigned NOT NULL,
  `content` text,
  `order` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `product_content_id` (`product_content_id`),
  CONSTRAINT `product_content_sentence_ibfk_1` FOREIGN KEY (`product_content_id`) REFERENCES `product_content` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `project_content_status` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(11) unsigned NOT NULL,
  `project_id` int(11) unsigned NOT NULL,
  `is_approved` tinyint(1) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `project_content_status_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `product_content` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_content_status_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `project_content_approval` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(11) unsigned NOT NULL,
  `project_id` int(11) unsigned NOT NULL,
  `approved_by` int(11) unsigned NOT NULL,
  `approved_on` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `approved_by` (`approved_by`),
  KEY `content_id` (`content_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `project_content_approval_ibfk_5` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  CONSTRAINT `project_content_approval_ibfk_6` FOREIGN KEY (`content_id`) REFERENCES `product_content` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_content_approval_ibfk_7` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `project_members` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NULL,
  `project_id` int(11) unsigned NOT NULL,
  `type` enum('translator','reviewer', 'manager') DEFAULT NULL,
  `invite_email` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `project_members_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `project_members_ibfk_2` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `user_languages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `language_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_languages` (`user_id`,`language_id`),
  KEY `user_id` (`user_id`),
  KEY `language_id` (`language_id`),
  CONSTRAINT `user_languages_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `user_languages_ibfk_2` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



CREATE TABLE `product_content_revisions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `project_id` int(11) unsigned NOT NULL,
  `content` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  CONSTRAINT `product_content_revisions_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `product_content` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_content_revisions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `product_content_revisions_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `users_groups` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `group_id` mediumint(8) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uc_users_groups` (`user_id`,`group_id`),
  KEY `fk_users_groups_users1_idx` (`user_id`),
  KEY `fk_users_groups_groups1_idx` (`group_id`),
  CONSTRAINT `fk_users_groups_groups1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_groups_users1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `product_content_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `content_id` int(11) unsigned DEFAULT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  `project_id` int(11) unsigned DEFAULT NULL,
  `comment` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `type` enum('approved','error') DEFAULT NULL,
  `is_resolved` tinyint(1) NOT NULL,
  `resolved_by` int(11) unsigned DEFAULT NULL,
  `resolved_on` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `content_id` (`content_id`),
  KEY `user_id` (`user_id`),
  KEY `project_id` (`project_id`),
  KEY `resolved_by` (`resolved_by`),
  CONSTRAINT `product_content_log_ibfk_1` FOREIGN KEY (`content_id`) REFERENCES `product_content` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_content_log_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `product_content_log_ibfk_3` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  CONSTRAINT `product_content_log_ibfk_4` FOREIGN KEY (`resolved_by`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

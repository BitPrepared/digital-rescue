# ------------------------------------------------------------
# ------------------------------------------------------------
#                    DIGITAL RESCUE
# ------------------------------------------------------------
# ------------------------------------------------------------

# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `csocio` char(12) NOT NULL DEFAULT '',
  `role` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '',
  `last_login` datetime DEFAULT NULL,
  `active` int(1) NOT NULL DEFAULT '0',
  `display_name` varchar(250) NOT NULL DEFAULT '',
  `registred` datetime NOT NULL,
  `activation_key` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# Dump of table profiles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `profiles`;

CREATE TABLE `profiles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `csocio` char(12) NOT NULL DEFAULT '',
  `gruppo` int(11) NOT NULL,
  `cognome` varchar(200) NOT NULL DEFAULT '',
  `nome` varchar(200) NOT NULL DEFAULT '',
  `email` varchar(200) NOT NULL,
  `indirizzo` varchar(300) NOT NULL DEFAULT '',
  `cap` char(5) NOT NULL DEFAULT '',
  `residenza` varchar(200) NOT NULL DEFAULT '',
  `prov` varchar(200) NOT NULL DEFAULT '',
  `datanascita` char(8) NOT NULL DEFAULT '' COMMENT 'YYYYMMDD',
  `luogonascita` varchar(200) NOT NULL DEFAULT '',
  `foca` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `csocio_unique` (`csocio`),
  KEY `ricerca_index` (`cognome`,`nome`,`datanascita`,`luogonascita`),
  KEY `email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dump of table contacts
# ------------------------------------------------------------

DROP TABLE IF EXISTS `contacts`;

CREATE TABLE `contacts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `csocio` char(12) NOT NULL DEFAULT '',
  `telefono` varchar(20) NOT NULL DEFAULT '',
  `type` enum('CELLULARE','FISSO') NOT NULL DEFAULT 'FISSO',
  PRIMARY KEY (`id`),
  KEY `telefono` (`telefono`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Dump of table history
# ------------------------------------------------------------

DROP TABLE IF EXISTS `history`;

CREATE TABLE `history` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `recall_url` text,
  `recall_param` longtext,
  `description` text NOT NULL COMMENT 'json descrittivo',
  `created_by` bigint(20) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `raddress` char(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table mlist
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mlist`;

CREATE TABLE `mlist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table task
# ------------------------------------------------------------

DROP TABLE IF EXISTS `task`;

CREATE TABLE `task` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `arguments` varchar(200) NOT NULL DEFAULT '' COMMENT 'json ',
  `status` enum('QUEUE','IN_PROGRESS','FAILED','ELABORATED') NOT NULL DEFAULT 'QUEUE',
  `type` enum('IMPORT','SEARCH') NOT NULL DEFAULT 'SEARCH',
  `result` varchar(200) DEFAULT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table tlog
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tlog`;

CREATE TABLE `tlog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` int(11) NOT NULL,
  `message` varchar(200) NOT NULL DEFAULT '',
  `level` enum('DEBUG','INFO','NOTICE','WARNING','ERROR','CRITICAL','ALERT','EMERGENCY') NOT NULL DEFAULT 'INFO',
  `created` datetime NOT NULL,
  `author` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


# ------------------------------------------------------------
# ------------------------------------------------------------
#                    ASA IMPORT
# ------------------------------------------------------------
# ------------------------------------------------------------


# Dump of table asa_ingcoca
# ------------------------------------------------------------

DROP TABLE IF EXISTS `asa_ingcoca`;

CREATE TABLE `asa_ingcoca` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asa_csocio` int(11) unsigned DEFAULT NULL,
  `asa_anno` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table asa_mfg
# ------------------------------------------------------------

DROP TABLE IF EXISTS `asa_mfg`;

CREATE TABLE `asa_mfg` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asa_csocio` int(11) unsigned DEFAULT NULL,
  `asa_creg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_ord` int(11) unsigned DEFAULT NULL,
  `asa_fnz` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_data` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table asa_mfp
# ------------------------------------------------------------

DROP TABLE IF EXISTS `asa_mfp`;

CREATE TABLE `asa_mfp` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asa_csocio` int(11) unsigned DEFAULT NULL,
  `asa_creg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_ord` int(11) unsigned DEFAULT NULL,
  `asa_cun` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_prog` tinyint(3) unsigned DEFAULT NULL,
  `asa_fnz` tinyint(3) unsigned DEFAULT NULL,
  `asa_data` int(11) unsigned DEFAULT NULL,
  `asa_articolo_capo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table asa_recapiti
# ------------------------------------------------------------

DROP TABLE IF EXISTS `asa_recapiti`;

CREATE TABLE `asa_recapiti` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asa_csocio` int(11) unsigned DEFAULT NULL,
  `asa_numero` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_tipo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table asa_regionisys
# ------------------------------------------------------------

DROP TABLE IF EXISTS `asa_regionisys`;

CREATE TABLE `asa_regionisys` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asa_creg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_nome` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_nome_short` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_area` tinyint(3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table asa_soci
# ------------------------------------------------------------

DROP TABLE IF EXISTS `asa_soci`;

CREATE TABLE `asa_soci` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asa_csocio` int(11) unsigned DEFAULT NULL,
  `asa_creg` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_ord` int(11) unsigned DEFAULT NULL,
  `asa_cun` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_prog` tinyint(1) unsigned DEFAULT NULL,
  `asa_cognome` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_nome` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_sesso` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_indirizzo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_cap` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_residenza` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_prov` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_datan` int(11) unsigned DEFAULT NULL,
  `asa_nascita` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_status` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_prof` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_foca` tinyint(3) unsigned DEFAULT NULL,
  `asa_com` tinyint(1) unsigned DEFAULT NULL,
  `asa_naz` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table asa_userprofile
# ------------------------------------------------------------

DROP TABLE IF EXISTS `asa_userprofile`;

CREATE TABLE `asa_userprofile` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `asa_email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `asa_tipo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

# Dump of table asa_cellulari
# ------------------------------------------------------------

CREATE VIEW asa_cellulari AS select * from asa_recapiti rcell where rcell.asa_tipo = 'B';




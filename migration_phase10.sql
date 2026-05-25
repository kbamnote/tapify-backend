-- Migration Phase 10: Iframes and Instagram Feeds

CREATE TABLE IF NOT EXISTS `vcard_iframes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vcard_id` int(11) NOT NULL,
  `url` varchar(500) NOT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vcard_id` (`vcard_id`),
  CONSTRAINT `fk_iframe_vcard` FOREIGN KEY (`vcard_id`) REFERENCES `vcards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `vcard_instagram_feeds` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `vcard_id` int(11) NOT NULL,
  `type` varchar(50) DEFAULT 'post',
  `embed_url` varchar(1000) DEFAULT NULL,
  `tag` text DEFAULT NULL,
  `display_order` int(11) DEFAULT 0,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `vcard_id` (`vcard_id`),
  CONSTRAINT `fk_insta_vcard` FOREIGN KEY (`vcard_id`) REFERENCES `vcards` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

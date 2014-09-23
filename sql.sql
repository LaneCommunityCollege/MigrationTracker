CREATE TABLE IF NOT EXISTS `history` (
  `actionid` bigint(20) NOT NULL AUTO_INCREMENT,
  `linkid` bigint(20) NOT NULL,
  `time` datetime NOT NULL,
  `action` varchar(255) NOT NULL,
  `author` bigint(20) NOT NULL,
  PRIMARY KEY (`actionid`),
  KEY `time_index` (`time`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `urls` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `nodenumber` int(11) NOT NULL,
  `oldurl` varchar(300) NOT NULL,
  `newurl` varchar(300) NOT NULL,
  `chunk` varchar(300) NOT NULL,
  `status` set('stub','review','complete','checked-out','archive-todo','archived','intranet-todo','intraneted','next') NOT NULL,
  PRIMARY KEY (`id`),
  KEY `url_chunk` (`chunk`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;

CREATE TABLE IF NOT EXISTS `users` (
  `userid` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `salt` varchar(8) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`userid`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;
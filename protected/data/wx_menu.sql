
CREATE TABLE IF NOT EXISTS `wx_menu` (
  `name` varchar(64) NOT NULL COMMENT '标题',
  `type` int(11) DEFAULT NULL COMMENT '类型1为链接2为点击事件',
  `obj` varchar(128) DEFAULT NULL COMMENT '内容',
  `parent` varchar(64) DEFAULT NULL COMMENT '父类名称',
  PRIMARY KEY (`name`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
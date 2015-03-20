CREATE TABLE IF NOT EXISTS `wx_group` (
  `id` int(11) NOT NULL COMMENT '分组编号',
  `name` varchar(64) NOT NULL COMMENT '分组名称',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

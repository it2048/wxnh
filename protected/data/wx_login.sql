CREATE TABLE IF NOT EXISTS `wx_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增长编号，保证二维码唯一性',
  `open_id` varchar(64) DEFAULT NULL COMMENT '管理员唯一标识',
  `type` tinyint(1) NOT NULL COMMENT '是否登录成功',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
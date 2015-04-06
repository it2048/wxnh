--
-- 表的结构 `sms_notice`
--

CREATE TABLE IF NOT EXISTS `sms_notice` (
  `telorsb` varchar(128) NOT NULL COMMENT '电话或者设备编号',
  `ftime` int(11) NOT NULL COMMENT '第一条短信时间',
  `ctn` int(11) NOT NULL COMMENT '短信条数',
  `ltime` int(11) NOT NULL COMMENT '最后一条短信时间',
  PRIMARY KEY (`telorsb`),
  UNIQUE KEY `telorsb_UNIQUE` (`telorsb`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='防止短信骚扰';

CREATE TABLE IF NOT EXISTS `sms_send` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '短信编号',
  `tel` varchar(45) DEFAULT NULL COMMENT '发送电话',
  `content` varchar(320) DEFAULT NULL COMMENT '发送内容',
  `time` int(11) DEFAULT NULL COMMENT '发送时间',
  `type` varchar(16) DEFAULT NULL COMMENT '发送类型标识',
  `rtn` int(11) DEFAULT NULL COMMENT '短信回执',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='短信发送记录表' AUTO_INCREMENT=1 ;
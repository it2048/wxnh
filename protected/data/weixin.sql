-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- 主机: 127.0.0.1
-- 生成日期: 2015 年 03 月 20 日 18:07
-- 服务器版本: 5.5.27
-- PHP 版本: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `weixin`
--

-- --------------------------------------------------------

--
-- 表的结构 `wx_admin`
--

CREATE TABLE IF NOT EXISTS `wx_admin` (
  `username` varchar(24) NOT NULL COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `tel` varchar(16) DEFAULT NULL COMMENT '电话',
  `email` varchar(32) DEFAULT NULL COMMENT 'email',
  PRIMARY KEY (`username`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `wx_admin`
--

INSERT INTO `wx_admin` (`username`, `password`, `tel`, `email`) VALUES
('admin', '21232f297a57a5a743894a0e4a801fc3', '18228041350', 'jsjscool@163.com'),
('it2048', '4297f44b13955235245b2497399d7a93', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `wx_group`
--

CREATE TABLE IF NOT EXISTS `wx_group` (
  `id` int(11) NOT NULL COMMENT '分组编号',
  `name` varchar(64) NOT NULL COMMENT '分组名称',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `wx_group`
--

INSERT INTO `wx_group` (`id`, `name`) VALUES
(0, '未分组'),
(1, '黑名单'),
(2, '星标组'),
(100, '客服中心'),
(101, '管理员组'),
(102, '剑侠世界客服'),
(103, '剑三客服'),
(104, '普通用户');

-- --------------------------------------------------------

--
-- 表的结构 `wx_log`
--

CREATE TABLE IF NOT EXISTS `wx_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号',
  `ip` varchar(16) NOT NULL COMMENT 'ip',
  `times` int(11) NOT NULL COMMENT '次数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `wx_login`
--

CREATE TABLE IF NOT EXISTS `wx_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增长编号，保证二维码唯一性',
  `open_id` varchar(64) DEFAULT NULL COMMENT '管理员唯一标识',
  `type` tinyint(1) NOT NULL COMMENT '是否登录成功',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=44 ;

--
-- 转存表中的数据 `wx_login`
--

INSERT INTO `wx_login` (`id`, `open_id`, `type`) VALUES
(1, NULL, 0),
(2, NULL, 0),
(3, NULL, 0),
(4, NULL, 0),
(5, NULL, 0),
(6, NULL, 0),
(7, NULL, 0),
(8, NULL, 0),
(9, NULL, 0),
(10, NULL, 0),
(11, NULL, 0),
(12, NULL, 0),
(13, NULL, 0),
(14, NULL, 0),
(15, NULL, 0),
(16, NULL, 0),
(17, NULL, 0),
(18, NULL, 1),
(19, NULL, 1),
(20, NULL, 0),
(21, NULL, 0),
(22, '123123123123', 1),
(23, 'o_oABj-ZF7H_7g7cCsVoiqU7Z020', 1),
(24, NULL, 0),
(25, 'o_oABj-ZF7H_7g7cCsVoiqU7Z020', 1),
(26, NULL, 0),
(27, 'o_oABj-ZF7H_7g7cCsVoiqU7Z020', 1),
(28, 'o_oABj-ZF7H_7g7cCsVoiqU7Z020', 1),
(29, 'o_oABj-ZF7H_7g7cCsVoiqU7Z020', 1),
(30, 'o_oABj-ZF7H_7g7cCsVoiqU7Z020', 1),
(31, NULL, 0),
(32, NULL, 0),
(33, NULL, 0),
(34, NULL, 0),
(35, NULL, 0),
(36, NULL, 0),
(37, NULL, 0),
(38, NULL, 0),
(39, NULL, 0),
(40, NULL, 0),
(41, NULL, 0),
(42, NULL, 0),
(43, NULL, 0);

-- --------------------------------------------------------

--
-- 表的结构 `wx_menu`
--

CREATE TABLE IF NOT EXISTS `wx_menu` (
  `name` varchar(64) NOT NULL COMMENT '标题',
  `type` int(11) DEFAULT NULL COMMENT '类型1为链接2为点击事件',
  `obj` varchar(128) DEFAULT NULL COMMENT '内容',
  `parent` varchar(64) DEFAULT NULL COMMENT '父类名称',
  PRIMARY KEY (`name`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `wx_menu`
--

INSERT INTO `wx_menu` (`name`, `type`, `obj`, `parent`) VALUES
('听音乐', 1, 'http://fm.baidu.com/', ''),
('点击测试', 2, 'V1001_TODAY_SINGER', ''),
('菜单', 0, '', ''),
('赞一下我们', 2, 'V1001_GOOD', '菜单'),
('验证邮箱', 2, 'cdxsj_checkemail', '菜单');

-- --------------------------------------------------------

--
-- 表的结构 `wx_msg`
--

CREATE TABLE IF NOT EXISTS `wx_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '全局编号',
  `receive_id` varchar(64) NOT NULL COMMENT '接收方编号',
  `tm` int(11) NOT NULL COMMENT '时间',
  `type` varchar(16) NOT NULL COMMENT '类型',
  `content` text NOT NULL COMMENT '内容',
  `send_id` varchar(64) NOT NULL COMMENT '发送方编号',
  PRIMARY KEY (`id`),
  KEY `receive_id` (`receive_id`),
  KEY `send_id` (`send_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `wx_msg`
--

INSERT INTO `wx_msg` (`id`, `receive_id`, `tm`, `type`, `content`, `send_id`) VALUES
(1, 'toUser', 1348831860, 'text', 'this is a test', 'fromUser');

-- --------------------------------------------------------

--
-- 表的结构 `wx_user`
--

CREATE TABLE IF NOT EXISTS `wx_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户表ID，对应用户唯一id',
  `open_id` varchar(64) NOT NULL COMMENT '用户的微信编号',
  `group_id` int(11) NOT NULL DEFAULT '0' COMMENT '分组编号',
  `nickname` varchar(36) DEFAULT NULL COMMENT '昵称',
  `tel` varchar(12) DEFAULT NULL COMMENT '用户电话',
  `email` varchar(64) DEFAULT NULL COMMENT '邮箱',
  `sex` int(11) DEFAULT NULL COMMENT '性别(1男2女)',
  `name` varchar(32) DEFAULT NULL COMMENT '姓名',
  `gm_id` varchar(32) DEFAULT NULL COMMENT 'GM编号',
  `employee_id` varchar(32) DEFAULT NULL COMMENT '员工编号(公司内部活动备用)',
  `city` varchar(12) DEFAULT NULL COMMENT '城市',
  `province` varchar(12) DEFAULT NULL COMMENT '省份',
  `country` varchar(32) DEFAULT NULL COMMENT '国家',
  `subscribe_time` int(11) NOT NULL COMMENT '关注时间，时间戳',
  `subscribe` int(11) NOT NULL COMMENT '关注状态（1关注，0未关注）',
  `type` int(11) NOT NULL DEFAULT '0' COMMENT 'type 用户类型(0未验证，1已抓取参数，2已验证邮箱)',
  PRIMARY KEY (`open_id`),
  UNIQUE KEY `open_id` (`open_id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='微信用户表' AUTO_INCREMENT=13 ;

--
-- 转存表中的数据 `wx_user`
--

INSERT INTO `wx_user` (`id`, `open_id`, `group_id`, `nickname`, `tel`, `email`, `sex`, `name`, `gm_id`, `employee_id`, `city`, `province`, `country`, `subscribe_time`, `subscribe`, `type`) VALUES
(11, 'ojotVuCe2r8Vv4_kfjfYmCa3n5X0', 101, '私奔的', '18228041350', 'xiongfanglei@kingsoft.com', 1, '熊方磊', 'gm110', '4466', '成都', '四川', '中国', 1400579748, 1, 2),
(7, 'ojotVuG2WnzcGzdgtuaqF-swZOP0', 0, '金山caster', NULL, NULL, 1, NULL, NULL, NULL, '成都', '四川', '中国', 1400579748, 1, 2),
(9, 'ojotVuKc7Q845VgM5T2Q2phtI8wc', 0, '雨吻成都', NULL, NULL, 1, NULL, NULL, NULL, '成都', '四川', '中国', 1400579748, 1, 1),
(12, 'ojotVuLdeuMvs3edHi05uq3S9lmA', 0, '笨小孩', NULL, NULL, 1, NULL, NULL, NULL, '朝阳', '北京', '中国', 0, 1, 1),
(8, 'ojotVuO3HHE6vNYbroWgxOauQGWs', 100, '陈畅', NULL, NULL, 2, NULL, NULL, NULL, '深圳', '广东', '中国', 1400579748, 1, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

--
-- 表的结构 `wx_employee`
--

CREATE TABLE IF NOT EXISTS `wx_employee` (
  `emp_id` varchar(64) NOT NULL COMMENT '员工编号',
  `emp_name` varchar(64) DEFAULT NULL COMMENT '员工姓名',
  `dep_id` varchar(64) DEFAULT NULL COMMENT '部门编号',
  `dep_name` varchar(128) DEFAULT NULL COMMENT '部门名称',
  `add` varchar(128) DEFAULT NULL COMMENT '地点',
  `degree` varchar(64) DEFAULT NULL COMMENT '职位名称',
  `emp_type` varchar(32) DEFAULT NULL COMMENT '员工类别',
  `cid` varchar(64) DEFAULT NULL COMMENT '身份证号码',
  `sex` int(11) DEFAULT NULL COMMENT '性别，1男2女',
  `company` varchar(32) DEFAULT NULL COMMENT '公司名称',
  `tel` varchar(32) DEFAULT NULL COMMENT '员工电话',
  `email` varchar(64) DEFAULT NULL COMMENT '员工邮箱',
  PRIMARY KEY (`emp_id`),
  UNIQUE KEY `emp_id` (`emp_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='员工表';

--
-- 转存表中的数据 `wx_employee`
--

INSERT INTO `wx_employee` (`emp_id`, `emp_name`, `dep_id`, `dep_name`, `add`, `degree`, `emp_type`, `cid`, `sex`, `company`, `tel`, `email`) VALUES
('100035828', '熊方磊', '00800001', '研发', '孵化园', '餐厅经理', '经理', '429006199012120000', 1, '必胜客', '', '');

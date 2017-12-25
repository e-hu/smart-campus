-- phpMyAdmin SQL Dump
-- version 4.6.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 2017-06-21 08:28:20
-- 服务器版本： 5.7.14
-- PHP Version: 7.0.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `think.admin`
--

-- --------------------------------------------------------

--
-- 表的结构 `system_auth`
--

CREATE TABLE `system_auth` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(20) NOT NULL COMMENT '权限名称',
  `status` tinyint(1) UNSIGNED DEFAULT '1' COMMENT '状态(1:禁用,2:启用)',
  `sort` smallint(6) UNSIGNED DEFAULT '0' COMMENT '排序权重',
  `desc` varchar(255) DEFAULT NULL COMMENT '备注说明',
  `create_by` bigint(11) UNSIGNED DEFAULT '0' COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统权限表';

--
-- 转存表中的数据 `system_auth`
--

INSERT INTO `system_auth` (`id`, `title`, `status`, `sort`, `desc`, `create_by`, `create_at`) VALUES
(51, '微信管理员', 1, 0, '只能进行微信操作', 0, '2017-06-21 08:04:01'),
(52, '系统管理员', 1, 0, '只能管理系统', 0, '2017-06-21 08:06:40'),
(53, '业务管理员', 1, 0, '只管理业务', 0, '2017-06-21 08:06:57');

-- --------------------------------------------------------

--
-- 表的结构 `system_auth_node`
--

CREATE TABLE `system_auth_node` (
  `auth` bigint(20) UNSIGNED DEFAULT NULL COMMENT '角色ID',
  `node` varchar(200) DEFAULT NULL COMMENT '节点路径'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='角色与节点关系表';

--
-- 转存表中的数据 `system_auth_node`
--

INSERT INTO `system_auth_node` (`auth`, `node`) VALUES
(51, 'wechat'),
(51, 'wechat/config'),
(51, 'wechat/config/index'),
(51, 'wechat/config/pay'),
(51, 'wechat/fans'),
(51, 'wechat/fans/index'),
(51, 'wechat/fans/back'),
(51, 'wechat/fans/backadd'),
(51, 'wechat/fans/backdel'),
(51, 'wechat/fans/tagadd'),
(51, 'wechat/fans/tagdel'),
(51, 'wechat/fans/sync'),
(51, 'wechat/keys'),
(51, 'wechat/keys/index'),
(51, 'wechat/keys/add'),
(51, 'wechat/keys/edit'),
(51, 'wechat/keys/del'),
(51, 'wechat/keys/forbid'),
(51, 'wechat/keys/resume'),
(51, 'wechat/keys/subscribe'),
(51, 'wechat/keys/defaults'),
(51, 'wechat/menu'),
(51, 'wechat/menu/index'),
(51, 'wechat/menu/edit'),
(51, 'wechat/menu/cancel'),
(51, 'wechat/news'),
(51, 'wechat/news/index'),
(51, 'wechat/news/select'),
(51, 'wechat/news/add'),
(51, 'wechat/news/edit'),
(51, 'wechat/news/del'),
(51, 'wechat/news/push'),
(51, 'wechat/tags'),
(51, 'wechat/tags/index'),
(51, 'wechat/tags/add'),
(51, 'wechat/tags/edit'),
(51, 'wechat/tags/sync'),
(52, 'admin'),
(52, 'admin/auth'),
(52, 'admin/auth/index'),
(52, 'admin/auth/apply'),
(52, 'admin/auth/add'),
(52, 'admin/auth/edit'),
(52, 'admin/auth/forbid'),
(52, 'admin/auth/resume'),
(52, 'admin/auth/del'),
(52, 'admin/config'),
(52, 'admin/config/index'),
(52, 'admin/config/file'),
(52, 'admin/log'),
(52, 'admin/log/index'),
(52, 'admin/log/del'),
(52, 'admin/menu'),
(52, 'admin/menu/index'),
(52, 'admin/menu/add'),
(52, 'admin/menu/del'),
(52, 'admin/menu/forbid'),
(52, 'admin/menu/resume'),
(52, 'admin/node'),
(52, 'admin/node/index'),
(52, 'admin/node/save'),
(52, 'admin/user'),
(52, 'admin/user/index'),
(52, 'admin/user/auth'),
(52, 'admin/user/add'),
(52, 'admin/user/edit'),
(52, 'admin/user/pass'),
(52, 'admin/user/del'),
(52, 'admin/user/forbid'),
(52, 'admin/user/resume');

-- --------------------------------------------------------

--
-- 表的结构 `system_config`
--

CREATE TABLE `system_config` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(100) DEFAULT NULL COMMENT '配置编码',
  `value` varchar(500) DEFAULT NULL COMMENT '配置值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `system_config`
--

INSERT INTO `system_config` (`id`, `name`, `value`) VALUES
(148, 'site_name', '智慧校园'),
(149, 'site_copy', '浙农茂阳农产品配送有限公司 © 2017~2020'),
(164, 'storage_type', 'local'),
(165, 'storage_qiniu_is_https', '1'),
(166, 'storage_qiniu_bucket', 'static'),
(167, 'storage_qiniu_domain', 'static.ctolog.com'),
(168, 'storage_qiniu_access_key', ''),
(169, 'storage_qiniu_secret_key', ''),
(170, 'storage_qiniu_region', '华东'),
(173, 'app_name', '智慧校园'),
(174, 'app_version', '1.00 dev'),
(176, 'browser_icon', 'https://think.ctolog.com/static/upload/f47b8fe06e38ae99/08e8398da45583b9.png'),
(184, 'wechat_appid', ''),
(185, 'wechat_appsecret', ''),
(186, 'wechat_token', ''),
(187, 'wechat_encodingaeskey', ''),
(188, 'wechat_mch_id', ''),
(189, 'wechat_partnerkey', ''),
(194, 'wechat_cert_key', ''),
(196, 'wechat_cert_cert', ''),
(197, 'tongji_baidu_key', 'aa2f9869e9b578122e4692de2bd9f80f'),
(198, 'tongji_cnzz_key', '1261854404'),
(199, 'storage_oss_bucket', ''),
(200, 'storage_oss_keyid', ''),
(201, 'storage_oss_secret', ''),
(202, 'storage_oss_domain', ''),
(203, 'storage_oss_is_https', '1');

-- --------------------------------------------------------

--
-- 表的结构 `system_log`
--

CREATE TABLE `system_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `ip` char(15) NOT NULL DEFAULT '' COMMENT '操作者IP地址',
  `node` char(200) NOT NULL DEFAULT '' COMMENT '当前操作节点',
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '操作人用户名',
  `action` varchar(200) NOT NULL DEFAULT '' COMMENT '操作行为',
  `content` text NOT NULL COMMENT '操作内容描述',
  `create_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统操作日志表';

--
-- 转存表中的数据 `system_log`
--

INSERT INTO `system_log` (`id`, `ip`, `node`, `username`, `action`, `content`, `create_at`) VALUES
(5128, '0.0.0.0', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2017-06-21 07:28:16'),
(5129, '0.0.0.0', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2017-06-21 07:51:55'),
(5130, '0.0.0.0', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2017-06-21 07:52:06'),
(5131, '0.0.0.0', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2017-06-21 07:56:17'),
(5132, '0.0.0.0', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2017-06-21 08:04:38'),
(5133, '0.0.0.0', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2017-06-21 08:04:58'),
(5134, '0.0.0.0', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2017-06-21 08:07:48'),
(5135, '0.0.0.0', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2017-06-21 08:08:06'),
(5136, '0.0.0.0', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2017-06-21 08:08:24'),
(5137, '0.0.0.0', 'admin/login/index', 'nijianqi', '系统管理', '用户登录系统成功', '2017-06-21 08:08:42'),
(5138, '0.0.0.0', 'admin/login/out', 'nijianqi', '系统管理', '用户退出系统成功', '2017-06-21 08:09:02'),
(5139, '0.0.0.0', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2017-06-21 08:09:17'),
(5140, '0.0.0.0', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2017-06-21 08:09:46'),
(5141, '0.0.0.0', 'admin/login/index', '123456', '系统管理', '用户登录系统成功', '2017-06-21 08:09:53'),
(5142, '0.0.0.0', 'admin/login/out', '123456', '系统管理', '用户退出系统成功', '2017-06-21 08:10:13'),
(5143, '0.0.0.0', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2017-06-21 08:10:21'),
(5144, '0.0.0.0', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2017-06-21 08:10:49'),
(5145, '0.0.0.0', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2017-06-21 08:11:00'),
(5146, '0.0.0.0', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2017-06-21 08:13:36'),
(5147, '0.0.0.0', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2017-06-21 08:13:42'),
(5148, '0.0.0.0', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2017-06-21 08:18:51'),
(5149, '0.0.0.0', 'admin/login/out', 'admin', '系统管理', '用户退出系统成功', '2017-06-21 08:19:14'),
(5150, '0.0.0.0', 'admin/login/index', 'admin', '系统管理', '用户登录系统成功', '2017-06-21 08:19:26');

-- --------------------------------------------------------

--
-- 表的结构 `system_menu`
--

CREATE TABLE `system_menu` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pid` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父id',
  `title` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '名称',
  `node` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '节点代码',
  `icon` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单图标',
  `url` varchar(400) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '链接',
  `params` varchar(500) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '' COMMENT '链接参数',
  `target` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '_self' COMMENT '链接打开方式',
  `sort` int(11) UNSIGNED DEFAULT '0' COMMENT '菜单排序',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态(0:禁用,1:启用)',
  `create_by` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统菜单表';

--
-- 转存表中的数据 `system_menu`
--

INSERT INTO `system_menu` (`id`, `pid`, `title`, `node`, `icon`, `url`, `params`, `target`, `sort`, `status`, `create_by`, `create_at`) VALUES
(2, 0, '系统管理', '', '', '#', '', '_self', 1000, 1, 0, '2015-11-16 11:15:38'),
(3, 4, '后台首页', '', 'fa fa-fw fa-tachometer', 'admin/index/main', '', '_self', 10, 1, 0, '2015-11-17 05:27:25'),
(4, 2, '系统配置', '', '', '#', '', '_self', 100, 1, 0, '2016-03-14 10:12:55'),
(5, 4, '网站参数', '', 'fa fa-apple', 'admin/config/index', '', '_self', 20, 1, 0, '2016-05-06 06:36:49'),
(6, 4, '文件存储', '', 'fa fa-hdd-o', 'admin/config/file', '', '_self', 30, 1, 0, '2016-05-06 06:39:43'),
(9, 20, '操作日志', '', 'glyphicon glyphicon-console', 'admin/log/index', '', '_self', 50, 1, 0, '2017-03-24 07:49:31'),
(19, 20, '权限管理', '', 'fa fa-user-secret', 'admin/auth/index', '', '_self', 20, 1, 0, '2015-11-17 05:18:12'),
(20, 2, '系统权限', '', '', '#', '', '_self', 200, 1, 0, '2016-03-14 10:11:41'),
(21, 20, '系统菜单', '', 'glyphicon glyphicon-menu-hamburger', 'admin/menu/index', '', '_self', 30, 1, 0, '2015-11-16 11:16:16'),
(22, 20, '节点管理', '', 'fa fa-ellipsis-v', 'admin/node/index', '', '_self', 10, 1, 0, '2015-11-16 11:16:16'),
(29, 20, '系统用户', '', 'fa fa-users', 'admin/user/index', '', '_self', 40, 1, 0, '2016-10-31 06:31:40'),
(61, 0, '微信管理', '', '', '#', '', '_self', 2000, 1, 0, '2017-03-29 03:00:21'),
(62, 61, '微信对接配置', '', '', '#', '', '_self', 0, 1, 0, '2017-03-29 03:03:38'),
(63, 62, '微信接口配置\r\n', '', 'fa fa-usb', 'wechat/config/index', '', '_self', 0, 1, 0, '2017-03-29 03:04:44'),
(64, 62, '微信支付配置', '', 'fa fa-paypal', 'wechat/config/pay', '', '_self', 0, 1, 0, '2017-03-29 03:05:29'),
(65, 61, '微信粉丝管理', '', '', '#', '', '_self', 0, 1, 0, '2017-03-29 03:08:32'),
(66, 65, '粉丝标签', '', 'fa fa-tags', 'wechat/tags/index', '', '_self', 0, 1, 0, '2017-03-29 03:09:41'),
(67, 65, '已关注粉丝', '', 'fa fa-wechat', 'wechat/fans/index', '', '_self', 0, 1, 0, '2017-03-29 03:10:07'),
(68, 61, '微信订制', '', '', '#', '', '_self', 0, 1, 0, '2017-03-29 03:10:39'),
(69, 68, '微信菜单定制', '', 'glyphicon glyphicon-phone', 'wechat/menu/index', '', '_self', 0, 1, 0, '2017-03-29 03:11:08'),
(70, 68, '关键字管理', '', 'fa fa-paw', 'wechat/keys/index', '', '_self', 0, 1, 0, '2017-03-29 03:11:49'),
(71, 68, '关注自动回复', '', 'fa fa-commenting-o', 'wechat/keys/subscribe', '', '_self', 0, 1, 0, '2017-03-29 03:12:32'),
(81, 68, '无配置默认回复', '', 'fa fa-commenting-o', 'wechat/keys/defaults', '', '_self', 0, 1, 0, '2017-04-21 06:48:25'),
(82, 61, '素材资源管理', '', '', '#', '', '_self', 0, 1, 0, '2017-04-24 03:23:18'),
(83, 82, '添加图文', '', 'fa fa-folder-open-o', 'wechat/news/add?id=1', '', '_self', 0, 1, 0, '2017-04-24 03:23:40'),
(85, 82, '图文列表', '', 'fa fa-file-pdf-o', 'wechat/news/index', '', '_self', 0, 1, 0, '2017-04-24 03:25:45'),
(86, 65, '粉丝黑名单', '', 'fa fa-reddit-alien', 'wechat/fans/back', '', '_self', 0, 1, 0, '2017-05-05 08:17:03');

-- --------------------------------------------------------

--
-- 表的结构 `system_node`
--

CREATE TABLE `system_node` (
  `id` int(11) UNSIGNED NOT NULL,
  `node` varchar(100) DEFAULT NULL COMMENT '节点代码',
  `title` varchar(500) DEFAULT NULL COMMENT '节点标题',
  `is_menu` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否可设置为菜单',
  `is_auth` tinyint(1) UNSIGNED DEFAULT '1' COMMENT '是启启动RBAC权限控制',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统节点表' ROW_FORMAT=DYNAMIC;

--
-- 转存表中的数据 `system_node`
--

INSERT INTO `system_node` (`id`, `node`, `title`, `is_menu`, `is_auth`, `create_at`) VALUES
(3, 'admin', '系统管理', 0, 1, '2017-03-10 07:31:29'),
(4, 'admin/menu/add', '添加菜单', 0, 1, '2017-03-10 07:32:21'),
(5, 'admin/config', '系统配置', 0, 1, '2017-03-10 07:32:56'),
(6, 'admin/config/index', '网站配置', 1, 1, '2017-03-10 07:32:58'),
(7, 'admin/config/file', '文件配置', 1, 1, '2017-03-10 07:33:02'),
(8, 'admin/config/mail', '邮件配置', 0, 0, '2017-03-10 07:36:42'),
(9, 'admin/config/sms', '短信配置', 0, 0, '2017-03-10 07:36:43'),
(10, 'admin/menu/index', '菜单列表', 1, 1, '2017-03-10 07:36:50'),
(11, 'admin/node/index', '节点列表', 1, 1, '2017-03-10 07:36:59'),
(12, 'admin/node/save', '节点更新', 0, 1, '2017-03-10 07:36:59'),
(13, 'store/menu/index', '菜单列表', 1, 1, '2017-03-10 07:37:22'),
(14, 'store/menu/add', '添加菜单', 0, 1, '2017-03-10 07:37:23'),
(15, 'store/menu/edit', '编辑菜单', 0, 1, '2017-03-10 07:37:24'),
(16, 'store/menu/del', '删除菜单', 0, 1, '2017-03-10 07:37:24'),
(17, 'admin/index/index', '', 1, 1, '2017-03-10 07:39:23'),
(18, 'admin/index/main', '', 0, 1, '2017-03-14 08:21:54'),
(19, 'admin/index/pass', NULL, 0, 1, '2017-03-14 08:25:56'),
(20, 'admin/index/info', NULL, 0, 1, '2017-03-14 08:25:57'),
(21, 'store/menu/tagmove', '移动标签', 0, 1, '2017-03-14 09:07:12'),
(22, 'store/menu/tagedit', '编辑标签', 0, 1, '2017-03-14 09:07:13'),
(23, 'store/menu/tagdel', '删除标签', 0, 1, '2017-03-14 09:07:13'),
(24, 'store/menu/resume', '启用菜单', 0, 1, '2017-03-14 09:07:14'),
(25, 'store/menu/forbid', '禁用菜单', 0, 1, '2017-03-14 09:07:15'),
(26, 'store/menu/tagadd', '添加标签', 0, 1, '2017-03-14 09:07:15'),
(27, 'store/menu/hot', '设置热卖', 0, 1, '2017-03-14 09:07:18'),
(28, 'admin/index', '', 0, 1, '2017-03-14 09:27:00'),
(29, 'store/order/index', '订单列表', 1, 1, '2017-03-14 09:52:51'),
(30, 'store/index/qrcimg', '店铺二维码', 0, 1, '2017-03-14 09:52:52'),
(31, 'store/index/edit', '编辑店铺', 0, 1, '2017-03-14 09:52:55'),
(32, 'store/index/qrc', '二维码列表', 0, 1, '2017-03-14 09:52:56'),
(33, 'store/index/add', '添加店铺', 0, 1, '2017-03-14 09:52:56'),
(34, 'store/index/index', '我的店铺', 1, 1, '2017-03-14 09:52:57'),
(35, 'store/api/delcache', NULL, 0, 1, '2017-03-14 09:52:59'),
(36, 'store/api/getcache', NULL, 0, 1, '2017-03-14 09:53:00'),
(37, 'store/api/setcache', NULL, 0, 1, '2017-03-14 09:53:01'),
(38, 'store/api/response', NULL, 0, 1, '2017-03-14 09:53:01'),
(39, 'store/api/auth', NULL, 0, 1, '2017-03-14 09:53:02'),
(40, 'admin/user/resume', '启用用户', 1, 1, '2017-03-14 09:53:03'),
(41, 'admin/user/forbid', '禁用用户', 1, 1, '2017-03-14 09:53:03'),
(42, 'admin/user/del', '删除用户', 0, 1, '2017-03-14 09:53:04'),
(43, 'admin/user/pass', '密码修改', 0, 1, '2017-03-14 09:53:04'),
(44, 'admin/user/edit', '编辑用户', 1, 1, '2017-03-14 09:53:05'),
(45, 'admin/user/index', '用户列表', 1, 1, '2017-03-14 09:53:07'),
(46, 'admin/user/auth', '用户授权', 1, 1, '2017-03-14 09:53:08'),
(47, 'admin/user/add', '新增用户', 1, 1, '2017-03-14 09:53:09'),
(48, 'admin/plugs/icon', NULL, 0, 1, '2017-03-14 09:53:09'),
(49, 'admin/plugs/upload', NULL, 0, 1, '2017-03-14 09:53:10'),
(50, 'admin/plugs/upfile', NULL, 0, 1, '2017-03-14 09:53:11'),
(51, 'admin/plugs/upstate', NULL, 0, 1, '2017-03-14 09:53:11'),
(52, 'admin/menu/resume', '菜单启用', 0, 1, '2017-03-14 09:53:14'),
(53, 'admin/menu/forbid', '菜单禁用', 1, 1, '2017-03-14 09:53:15'),
(54, 'admin/login/index', NULL, 0, 1, '2017-03-14 09:53:17'),
(55, 'admin/login/out', '', 0, 1, '2017-03-14 09:53:18'),
(56, 'admin/menu/edit', '编辑菜单', 0, 0, '2017-03-14 09:53:20'),
(57, 'admin/menu/del', '菜单删除', 0, 1, '2017-03-14 09:53:21'),
(58, 'store/menu', '菜谱管理', 0, 1, '2017-03-14 09:57:47'),
(59, 'store/index', '店铺管理', 0, 1, '2017-03-14 09:58:28'),
(60, 'store', '店铺管理', 0, 1, '2017-03-14 09:58:29'),
(61, 'store/order', '订单管理', 0, 1, '2017-03-14 09:58:56'),
(62, 'admin/user', '用户管理', 0, 1, '2017-03-14 09:59:39'),
(63, 'admin/node', '节点管理', 0, 1, '2017-03-14 09:59:53'),
(64, 'admin/menu', '菜单管理', 0, 1, '2017-03-14 10:00:31'),
(65, 'admin/auth', '权限管理', 0, 1, '2017-03-17 06:37:05'),
(66, 'admin/auth/index', '权限列表', 1, 1, '2017-03-17 06:37:14'),
(67, 'admin/auth/apply', '权限节点', 1, 1, '2017-03-17 06:37:29'),
(68, 'admin/auth/add', '添加权', 0, 1, '2017-03-17 06:37:32'),
(69, 'admin/auth/edit', '编辑权限', 0, 1, '2017-03-17 06:37:36'),
(70, 'admin/auth/forbid', '禁用权限', 0, 1, '2017-03-17 06:37:38'),
(71, 'admin/auth/resume', '启用权限', 0, 1, '2017-03-17 06:37:41'),
(72, 'admin/auth/del', '删除权限', 0, 1, '2017-03-17 06:37:47'),
(73, 'admin/log/index', '日志列表', 1, 1, '2017-03-25 01:54:57'),
(74, 'admin/log/del', '删除日志', 1, 1, '2017-03-25 01:54:59'),
(75, 'admin/log', '系统日志', 0, 1, '2017-03-25 02:56:53'),
(76, 'wechat', '微信管理', 0, 1, '2017-04-05 02:52:31'),
(77, 'wechat/article', '微信文章', 0, 1, '2017-04-05 02:52:47'),
(78, 'wechat/article/index', '文章列表', 1, 1, '2017-04-05 02:52:54'),
(79, 'wechat/config', '微信配置', 0, 1, '2017-04-05 02:53:02'),
(80, 'wechat/config/index', '微信接口配置', 1, 1, '2017-04-05 02:53:16'),
(81, 'wechat/config/pay', '微信支付配置', 1, 1, '2017-04-05 02:53:18'),
(82, 'wechat/fans', '微信粉丝', 0, 1, '2017-04-05 02:53:34'),
(83, 'wechat/fans/index', '粉丝列表', 1, 1, '2017-04-05 02:53:39'),
(84, 'wechat/index', '微信主页', 0, 1, '2017-04-05 02:53:49'),
(85, 'wechat/index/index', '微信主页', 1, 1, '2017-04-05 02:53:49'),
(86, 'wechat/keys', '微信关键字', 0, 1, '2017-04-05 02:54:00'),
(87, 'wechat/keys/index', '关键字列表', 1, 1, '2017-04-05 02:54:14'),
(88, 'wechat/keys/subscribe', '关键自动回复', 1, 1, '2017-04-05 02:54:23'),
(89, 'wechat/keys/defaults', '默认自动回复', 1, 1, '2017-04-05 02:54:29'),
(90, 'wechat/menu', '微信菜单管理', 0, 1, '2017-04-05 02:54:34'),
(91, 'wechat/menu/index', '微信菜单', 1, 1, '2017-04-05 02:54:41'),
(92, 'wechat/news', '微信图文管理', 0, 1, '2017-04-05 02:54:51'),
(93, 'wechat/news/index', '图文列表', 1, 1, '2017-04-05 02:54:59'),
(94, 'wechat/notify/index', '', 0, 0, '2017-04-05 09:59:20'),
(95, 'wechat/api/index', '', 1, 1, '2017-04-06 01:30:28'),
(96, 'wechat/api', '', 0, 1, '2017-04-06 02:11:23'),
(97, 'wechat/notify', '', 0, 1, '2017-04-10 02:37:33'),
(98, 'wechat/fans/sync', '同步粉丝', 0, 1, '2017-04-13 08:30:29'),
(99, 'wechat/menu/edit', '编辑微信菜单', 0, 1, '2017-04-19 15:36:52'),
(100, 'wechat/menu/cancel', '取消微信菜单', 0, 1, '2017-04-19 15:36:54'),
(101, 'wechat/keys/edit', '编辑关键字', 0, 1, '2017-04-21 02:24:09'),
(102, 'wechat/keys/add', '添加关键字', 0, 1, '2017-04-21 02:24:09'),
(103, 'wechat/review/index', NULL, 0, 1, '2017-04-21 02:24:11'),
(104, 'wechat/review', '', 0, 1, '2017-04-21 02:24:18'),
(105, 'wechat/keys/del', '删除关键字', 0, 1, '2017-04-21 06:22:31'),
(106, 'wechat/news/add', '添加图文', 0, 1, '2017-04-22 14:17:29'),
(107, 'wechat/news/select', '图文选择器', 1, 1, '2017-04-22 14:17:31'),
(108, 'wechat/keys/resume', '启用关键字', 0, 1, '2017-04-25 03:03:52'),
(109, 'wechat/news/edit', '编辑图文', 0, 1, '2017-04-25 08:15:23'),
(110, 'wechat/news/push', '推送图文', 0, 1, '2017-04-25 14:32:08'),
(111, 'wechat/news/del', '删除图文', 0, 1, '2017-04-26 02:48:24'),
(112, 'wechat/keys/forbid', '禁用关键字', 0, 1, '2017-04-26 02:48:28'),
(113, 'wechat/tags/index', '标签列表', 1, 1, '2017-05-04 08:03:37'),
(114, 'wechat/tags/add', '添加标签', 0, 1, '2017-05-05 04:48:28'),
(115, 'wechat/tags/edit', '编辑标签', 0, 1, '2017-05-05 04:48:29'),
(116, 'wechat/tags/sync', '同步标签', 0, 1, '2017-05-05 04:48:30'),
(117, 'wechat/tags', '粉丝标签管理', 0, 1, '2017-05-05 05:17:12'),
(118, 'wechat/fans/backdel', '移除粉丝黑名单', 0, 1, '2017-05-05 08:56:23'),
(119, 'wechat/fans/backadd', '移入粉丝黑名单', 0, 1, '2017-05-05 08:56:30'),
(120, 'wechat/fans/back', '粉丝黑名单列表', 1, 1, '2017-05-05 08:56:38'),
(121, 'wechat/fans/tagadd', '添加粉丝标签', 0, 1, '2017-05-08 06:46:13'),
(122, 'wechat/fans/tagdel', '删除粉丝标签', 0, 1, '2017-05-08 06:46:20');

-- --------------------------------------------------------

--
-- 表的结构 `system_sequence`
--

CREATE TABLE `system_sequence` (
  `id` bigint(20) NOT NULL,
  `type` varchar(20) DEFAULT NULL COMMENT '序号类型',
  `sequence` char(50) NOT NULL COMMENT '序号值',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统序号表';

-- --------------------------------------------------------

--
-- 表的结构 `system_user`
--

CREATE TABLE `system_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL DEFAULT '' COMMENT '用户登录名',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '用户登录密码',
  `qq` varchar(16) DEFAULT NULL COMMENT '联系QQ',
  `mail` varchar(32) DEFAULT NULL COMMENT '联系邮箱',
  `phone` varchar(16) DEFAULT NULL COMMENT '联系手机号',
  `desc` varchar(255) DEFAULT '' COMMENT '备注说明',
  `login_num` bigint(20) UNSIGNED DEFAULT '0' COMMENT '登录次数',
  `login_at` datetime DEFAULT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态(0:禁用,1:启用)',
  `authorize` varchar(255) DEFAULT NULL,
  `is_deleted` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '删除状态(1:删除,0:未删)',
  `create_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='系统用户表';

--
-- 转存表中的数据 `system_user`
--

INSERT INTO `system_user` (`id`, `username`, `password`, `qq`, `mail`, `phone`, `desc`, `login_num`, `login_at`, `status`, `authorize`, `is_deleted`, `create_by`, `create_at`) VALUES
(10001, 'admin', '25f9e794323b453885f5181f1b624d0b', '22222222', '1198952901@qq.com', '17805858309', '<script>alert("a")</script>', 12610, '2017-06-21 16:19:26', 1, '47,49', 0, NULL, '2015-11-13 07:14:22'),
(10145, '管理员', '21232f297a57a5a743894a0e4a801fc3', NULL, '1198952901@qq.com', '13095676007', '呜呜呜呜', 0, NULL, 1, '51', 1, NULL, '2017-06-21 08:03:15'),
(10146, 'nijianqi', '21232f297a57a5a743894a0e4a801fc3', NULL, 'njq199627@gmail.com', '13095676007', '微信管理员', 1, '2017-06-21 16:08:42', 1, '51', 0, NULL, '2017-06-21 08:06:13'),
(10147, '123456', '21232f297a57a5a743894a0e4a801fc3', NULL, '1198952901@qq.com', '17805858309', '1111', 1, '2017-06-21 16:09:53', 1, '53', 0, NULL, '2017-06-21 08:07:43');

-- --------------------------------------------------------

--
-- 表的结构 `wechat_fans`
--

CREATE TABLE `wechat_fans` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '粉丝表ID',
  `appid` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '公众号Appid',
  `groupid` bigint(20) UNSIGNED DEFAULT NULL COMMENT '分组ID',
  `tagid_list` varchar(100) CHARACTER SET utf8 DEFAULT '' COMMENT '标签id',
  `is_back` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否为黑名单用户',
  `subscribe` tinyint(1) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户是否订阅该公众号,0：未关注,1：已关注',
  `openid` char(100) CHARACTER SET utf8 NOT NULL DEFAULT '' COMMENT '用户的标识,对当前公众号唯一',
  `spread_openid` char(100) CHARACTER SET utf8 DEFAULT NULL COMMENT '推荐人OPENID',
  `spread_at` datetime DEFAULT NULL,
  `nickname` varchar(20) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '用户的昵称',
  `sex` tinyint(1) UNSIGNED DEFAULT NULL COMMENT '用户的性别,值为1时是男性,值为2时是女性,值为0时是未知',
  `country` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '用户所在国家',
  `province` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '用户所在省份',
  `city` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '用户所在城市',
  `language` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '用户的语言,简体中文为zh_CN',
  `headimgurl` varchar(500) CHARACTER SET utf8 DEFAULT NULL COMMENT '用户头像',
  `subscribe_time` bigint(20) UNSIGNED DEFAULT NULL COMMENT '用户关注时间',
  `subscribe_at` datetime DEFAULT NULL COMMENT '关注时间',
  `unionid` varchar(100) CHARACTER SET utf8 DEFAULT NULL COMMENT 'unionid',
  `remark` varchar(50) CHARACTER SET utf8 DEFAULT NULL COMMENT '备注',
  `expires_in` bigint(20) UNSIGNED DEFAULT '0' COMMENT '有效时间',
  `refresh_token` varchar(200) CHARACTER SET utf8 DEFAULT NULL COMMENT '刷新token',
  `access_token` varchar(200) CHARACTER SET utf8 DEFAULT NULL COMMENT '访问token',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=sjis COMMENT='微信粉丝';

-- --------------------------------------------------------

--
-- 表的结构 `wechat_fans_tags`
--

CREATE TABLE `wechat_fans_tags` (
  `id` bigint(20) UNSIGNED NOT NULL COMMENT '标签ID',
  `appid` char(50) DEFAULT NULL COMMENT '公众号APPID',
  `name` varchar(35) DEFAULT NULL COMMENT '标签名称',
  `count` int(11) UNSIGNED DEFAULT NULL COMMENT '总数',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建日期'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信会员标签';

-- --------------------------------------------------------

--
-- 表的结构 `wechat_keys`
--

CREATE TABLE `wechat_keys` (
  `id` bigint(20) NOT NULL,
  `appid` char(50) DEFAULT NULL COMMENT '公众号APPID',
  `type` varchar(20) DEFAULT NULL COMMENT '类型,text 文件消息,image 图片消息,news 图文消息',
  `keys` varchar(100) DEFAULT NULL COMMENT '关键字',
  `content` text COMMENT '文本内容',
  `image_url` varchar(255) DEFAULT NULL COMMENT '图片链接',
  `voice_url` varchar(255) DEFAULT NULL COMMENT '语音链接',
  `music_title` varchar(100) DEFAULT NULL COMMENT '音乐标题',
  `music_url` varchar(255) DEFAULT NULL COMMENT '音乐链接',
  `music_image` varchar(255) DEFAULT NULL COMMENT '音乐缩略图链接',
  `music_desc` varchar(255) DEFAULT NULL COMMENT '音乐描述',
  `video_title` varchar(100) DEFAULT NULL COMMENT '视频标题',
  `video_url` varchar(255) DEFAULT NULL COMMENT '视频URL',
  `video_desc` varchar(255) DEFAULT NULL COMMENT '视频描述',
  `news_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT '图文ID',
  `sort` bigint(20) UNSIGNED DEFAULT '0' COMMENT '排序字段',
  `status` tinyint(1) UNSIGNED DEFAULT '1' COMMENT '0 禁用,1 启用',
  `create_by` bigint(20) UNSIGNED DEFAULT NULL COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT=' 微信关键字';

-- --------------------------------------------------------

--
-- 表的结构 `wechat_menu`
--

CREATE TABLE `wechat_menu` (
  `id` bigint(16) UNSIGNED NOT NULL,
  `index` bigint(20) DEFAULT NULL,
  `pindex` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '父id',
  `type` varchar(24) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单类型 null主菜单 link链接 keys关键字',
  `name` varchar(256) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT '菜单名称',
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '文字内容',
  `sort` bigint(20) UNSIGNED DEFAULT '0' COMMENT '排序',
  `status` tinyint(1) UNSIGNED DEFAULT '1' COMMENT '状态(0禁用1启用)',
  `create_by` bigint(20) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建人',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `wechat_menu`
--

INSERT INTO `wechat_menu` (`id`, `index`, `pindex`, `type`, `name`, `content`, `sort`, `status`, `create_by`, `create_at`) VALUES
(1502, 1, 0, 'text', '关键字', '2234123413', 0, 1, 0, '2017-04-27 06:49:14'),
(1503, 11, 1, 'keys', '图片', '图片', 0, 1, 0, '2017-04-27 06:49:14'),
(1504, 12, 1, 'keys', '音乐', '音乐', 1, 1, 0, '2017-04-27 06:49:14'),
(1505, 2, 0, 'event', '事件类', 'pic_weixin', 1, 1, 0, '2017-04-27 06:49:14'),
(1506, 3, 0, 'view', '微信支付', 'index/wap/payjs', 2, 1, 0, '2017-04-27 06:49:14');

-- --------------------------------------------------------

--
-- 表的结构 `wechat_news`
--

CREATE TABLE `wechat_news` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `media_id` varchar(100) DEFAULT NULL COMMENT '永久素材MediaID',
  `local_url` varchar(300) DEFAULT NULL COMMENT '永久素材显示URL',
  `article_id` varchar(60) DEFAULT NULL COMMENT '关联图文ID,用,号做分割',
  `is_deleted` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否删除',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `create_by` bigint(20) DEFAULT NULL COMMENT '创建人'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信图文表';

-- --------------------------------------------------------

--
-- 表的结构 `wechat_news_article`
--

CREATE TABLE `wechat_news_article` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(50) DEFAULT NULL COMMENT '素材标题',
  `local_url` varchar(300) DEFAULT NULL COMMENT '永久素材显示URL',
  `show_cover_pic` tinyint(4) UNSIGNED DEFAULT '0' COMMENT '是否显示封面 0不显示,1 显示',
  `author` varchar(20) DEFAULT NULL COMMENT '作者',
  `digest` varchar(300) DEFAULT NULL COMMENT '摘要内容',
  `content` longtext COMMENT '图文内容',
  `content_source_url` varchar(200) DEFAULT NULL COMMENT '图文消息原文地址',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `create_by` bigint(20) DEFAULT NULL COMMENT '创建人'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信素材表';

-- --------------------------------------------------------

--
-- 表的结构 `wechat_news_image`
--

CREATE TABLE `wechat_news_image` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `appid` varchar(200) DEFAULT NULL COMMENT '公众号ID',
  `md5` varchar(32) DEFAULT NULL COMMENT '文件md5',
  `media_id` varchar(100) DEFAULT NULL COMMENT '永久素材MediaID',
  `local_url` varchar(300) DEFAULT NULL COMMENT '本地文件链接',
  `media_url` varchar(300) DEFAULT NULL COMMENT '远程图片链接',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信服务器图片';

-- --------------------------------------------------------

--
-- 表的结构 `wechat_news_media`
--

CREATE TABLE `wechat_news_media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `appid` varchar(200) DEFAULT NULL COMMENT '公众号ID',
  `md5` varchar(32) DEFAULT NULL COMMENT '文件md5',
  `type` varchar(20) DEFAULT NULL COMMENT '媒体类型',
  `media_id` varchar(100) DEFAULT NULL COMMENT '永久素材MediaID',
  `local_url` varchar(300) DEFAULT NULL COMMENT '本地文件链接',
  `media_url` varchar(300) DEFAULT NULL COMMENT '远程图片链接',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='微信素材表';

-- --------------------------------------------------------

--
-- 表的结构 `wechat_pay_notify`
--

CREATE TABLE `wechat_pay_notify` (
  `id` int(20) NOT NULL,
  `appid` varchar(50) DEFAULT NULL COMMENT '公众号Appid',
  `bank_type` varchar(50) DEFAULT NULL COMMENT '银行类型',
  `cash_fee` bigint(20) DEFAULT NULL COMMENT '现金价',
  `fee_type` char(20) DEFAULT NULL COMMENT '币种,1人民币',
  `is_subscribe` char(1) DEFAULT 'N' COMMENT '是否关注,Y为关注,N为未关注',
  `mch_id` varchar(50) DEFAULT NULL COMMENT '商户MCH_ID',
  `nonce_str` varchar(32) DEFAULT NULL COMMENT '随机串',
  `openid` varchar(50) DEFAULT NULL COMMENT '微信用户openid',
  `out_trade_no` varchar(50) DEFAULT NULL COMMENT '支付平台退款交易号',
  `sign` varchar(100) DEFAULT NULL COMMENT '签名',
  `time_end` datetime DEFAULT NULL COMMENT '结束时间',
  `result_code` varchar(10) DEFAULT NULL,
  `return_code` varchar(10) DEFAULT NULL,
  `total_fee` varchar(11) DEFAULT NULL COMMENT '支付总金额,单位为分',
  `trade_type` varchar(20) DEFAULT NULL COMMENT '支付方式',
  `transaction_id` varchar(64) DEFAULT NULL COMMENT '订单号',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '本地系统时间'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='支付日志表';

-- --------------------------------------------------------

--
-- 表的结构 `wechat_pay_prepayid`
--

CREATE TABLE `wechat_pay_prepayid` (
  `id` int(20) NOT NULL,
  `appid` char(50) DEFAULT NULL COMMENT '公众号APPID',
  `from` char(32) DEFAULT 'shop' COMMENT '支付来源',
  `fee` bigint(20) UNSIGNED DEFAULT NULL COMMENT '支付费用(分)',
  `trade_type` varchar(20) DEFAULT NULL COMMENT '订单类型',
  `order_no` varchar(50) DEFAULT NULL COMMENT '内部订单号',
  `out_trade_no` varchar(50) DEFAULT NULL COMMENT '外部订单号',
  `prepayid` varchar(500) DEFAULT NULL COMMENT '预支付码',
  `expires_in` bigint(20) UNSIGNED DEFAULT NULL COMMENT '有效时间',
  `transaction_id` varchar(64) DEFAULT NULL COMMENT '微信平台订单号',
  `is_pay` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '1已支付,0未支退款',
  `pay_at` datetime DEFAULT NULL COMMENT '支付时间',
  `is_refund` tinyint(1) UNSIGNED DEFAULT '0' COMMENT '是否退款,退款单号(T+原来订单)',
  `refund_at` datetime DEFAULT NULL COMMENT '退款时间',
  `create_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '本地系统时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='支付订单号对应表';

--
-- Indexes for dumped tables
--

--
-- Indexes for table `system_auth`
--
ALTER TABLE `system_auth`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index_system_auth_title` (`title`) USING BTREE,
  ADD KEY `index_system_auth_status` (`status`) USING BTREE;

--
-- Indexes for table `system_auth_node`
--
ALTER TABLE `system_auth_node`
  ADD KEY `index_system_auth_auth` (`auth`) USING BTREE,
  ADD KEY `index_system_auth_node` (`node`) USING BTREE;

--
-- Indexes for table `system_config`
--
ALTER TABLE `system_config`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_log`
--
ALTER TABLE `system_log`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `system_menu`
--
ALTER TABLE `system_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_system_menu_node` (`node`) USING BTREE;

--
-- Indexes for table `system_node`
--
ALTER TABLE `system_node`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_system_node_node` (`node`);

--
-- Indexes for table `system_sequence`
--
ALTER TABLE `system_sequence`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index_system_sequence_unique` (`type`,`sequence`) USING BTREE,
  ADD KEY `index_system_sequence_type` (`type`),
  ADD KEY `index_system_sequence_number` (`sequence`);

--
-- Indexes for table `system_user`
--
ALTER TABLE `system_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index_system_user_username` (`username`) USING BTREE;

--
-- Indexes for table `wechat_fans`
--
ALTER TABLE `wechat_fans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_wechat_fans_spread_openid` (`spread_openid`) USING BTREE,
  ADD KEY `index_wechat_fans_openid` (`openid`) USING BTREE;

--
-- Indexes for table `wechat_fans_tags`
--
ALTER TABLE `wechat_fans_tags`
  ADD KEY `index_wechat_fans_tags_id` (`id`) USING BTREE,
  ADD KEY `index_wechat_fans_tags_appid` (`appid`) USING BTREE;

--
-- Indexes for table `wechat_keys`
--
ALTER TABLE `wechat_keys`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wechat_menu`
--
ALTER TABLE `wechat_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `wechat_menu_pid` (`pindex`) USING BTREE;

--
-- Indexes for table `wechat_news`
--
ALTER TABLE `wechat_news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_wechat_new_artcle_id` (`article_id`) USING BTREE;

--
-- Indexes for table `wechat_news_article`
--
ALTER TABLE `wechat_news_article`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wechat_news_image`
--
ALTER TABLE `wechat_news_image`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wechat_news_media`
--
ALTER TABLE `wechat_news_media`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wechat_pay_notify`
--
ALTER TABLE `wechat_pay_notify`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_wechat_pay_notify_openid` (`openid`) USING BTREE,
  ADD KEY `index_wechat_pay_notify_out_trade_no` (`out_trade_no`) USING BTREE,
  ADD KEY `index_wechat_pay_notify_transaction_id` (`transaction_id`) USING BTREE;

--
-- Indexes for table `wechat_pay_prepayid`
--
ALTER TABLE `wechat_pay_prepayid`
  ADD PRIMARY KEY (`id`),
  ADD KEY `index_wechat_pay_prepayid_outer_no` (`out_trade_no`) USING BTREE,
  ADD KEY `index_wechat_pay_prepayid_order_no` (`order_no`) USING BTREE,
  ADD KEY `index_wechat_pay_is_pay` (`is_pay`) USING BTREE,
  ADD KEY `index_wechat_pay_is_refund` (`is_refund`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `system_auth`
--
ALTER TABLE `system_auth`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;
--
-- 使用表AUTO_INCREMENT `system_config`
--
ALTER TABLE `system_config`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;
--
-- 使用表AUTO_INCREMENT `system_log`
--
ALTER TABLE `system_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5151;
--
-- 使用表AUTO_INCREMENT `system_menu`
--
ALTER TABLE `system_menu`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;
--
-- 使用表AUTO_INCREMENT `system_node`
--
ALTER TABLE `system_node`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;
--
-- 使用表AUTO_INCREMENT `system_sequence`
--
ALTER TABLE `system_sequence`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1450;
--
-- 使用表AUTO_INCREMENT `system_user`
--
ALTER TABLE `system_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10148;
--
-- 使用表AUTO_INCREMENT `wechat_fans`
--
ALTER TABLE `wechat_fans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '粉丝表ID', AUTO_INCREMENT=9427;
--
-- 使用表AUTO_INCREMENT `wechat_keys`
--
ALTER TABLE `wechat_keys`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;
--
-- 使用表AUTO_INCREMENT `wechat_menu`
--
ALTER TABLE `wechat_menu`
  MODIFY `id` bigint(16) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1507;
--
-- 使用表AUTO_INCREMENT `wechat_news`
--
ALTER TABLE `wechat_news`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;
--
-- 使用表AUTO_INCREMENT `wechat_news_article`
--
ALTER TABLE `wechat_news_article`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;
--
-- 使用表AUTO_INCREMENT `wechat_news_image`
--
ALTER TABLE `wechat_news_image`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- 使用表AUTO_INCREMENT `wechat_news_media`
--
ALTER TABLE `wechat_news_media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;
--
-- 使用表AUTO_INCREMENT `wechat_pay_notify`
--
ALTER TABLE `wechat_pay_notify`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- 使用表AUTO_INCREMENT `wechat_pay_prepayid`
--
ALTER TABLE `wechat_pay_prepayid`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

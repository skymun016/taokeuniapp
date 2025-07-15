<?php
if (!pdo_tableexists('xm_mallv3_paymethod')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_paymethod` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `code` varchar(20) NOT NULL,
    `title` varchar(30) NOT NULL,
    `status` tinyint(1) NOT NULL DEFAULT '0',
    `collection_voucher` tinyint(1) NOT NULL DEFAULT '0' COMMENT '收款凭证状态：0.关闭，1.开启',
    `group_ids` varchar(255) NOT NULL
  ) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='支付方式表';
  
  INSERT INTO `ims_xm_mallv3_paymethod` (`id`, `weid`, `code`, `title`, `status`, `collection_voucher`, `group_ids`) VALUES
  (1, 0, 'wx_pay', '微信支付', 1, 0, '');
  
  ALTER TABLE `ims_xm_mallv3_paymethod`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_paymethod`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;");
}

if (!pdo_tableexists('xm_mallv3_article_category')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_article_category` (
    `id` int(11) UNSIGNED NOT NULL,
    `title` varchar(255) DEFAULT '' COMMENT '名称',
    `weid` int(11) UNSIGNED NOT NULL,
    `image` varchar(100) DEFAULT NULL,
    `sort` int(11) NOT NULL DEFAULT '100' COMMENT '排序 升序',
    `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0',
    `update_time` int(11) DEFAULT '0',
    `is_delete` smallint(1) UNSIGNED NOT NULL DEFAULT '0',
    `status` tinyint(1) DEFAULT '0' COMMENT '状态'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章分类';
  
  ALTER TABLE `ims_xm_mallv3_article_category`
    ADD PRIMARY KEY (`id`),
    ADD KEY `is_delete` (`is_delete`);
  
  ALTER TABLE `ims_xm_mallv3_article_category`
    MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_article')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_article` (
    `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
    `weid` int(11) NOT NULL,
    `cid` int(11) DEFAULT '0' COMMENT '类别',
    `title` varchar(50) DEFAULT '' COMMENT '标题',
    `content` text NOT NULL COMMENT '内容',
    `image` varchar(100) DEFAULT NULL,
    `create_time` int(11) NOT NULL DEFAULT '0',
    `update_time` int(10) DEFAULT '0',
    `sort` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '1'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='文章';
  
  ALTER TABLE `ims_xm_mallv3_article`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_article`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
  COMMIT;");
}

if (pdo_tableexists('xm_mallv3_Paymethod') && !pdo_tableexists('xm_mallv3_paymethod')) {
  pdo_run("alter table ims_xm_mallv3_Paymethod rename to ims_xm_mallv3_paymethod;");
}
if (pdo_tableexists('xm_mallv3_account') && !pdo_tableexists('xm_mallv3_platform')) {
  pdo_run("alter table ims_xm_mallv3_account rename to ims_xm_mallv3_platform;");
}

if (pdo_tableexists('xm_mallv3_store_cate') && !pdo_tableexists('xm_mallv3_store_level')) {
  pdo_run("alter table ims_xm_mallv3_store_cate rename to ims_xm_mallv3_store_level;");
}

if (!pdo_tableexists('xm_mallv3_diy_page')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_diy_page` (
  `id` int(11) unsigned NOT NULL COMMENT 'ID',
  `weid` int(11) NOT NULL,
  `title` varchar(60) NOT NULL,
  `page_type` tinyint(3) unsigned NOT NULL DEFAULT '10' COMMENT '页面类型(10首页 20自定义页)',
  `page_data` longtext NOT NULL COMMENT '页面数据',
  `pagebase` text COMMENT '页面基础设置',
  `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `update_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `is_index` tinyint(1) NOT NULL COMMENT '设为首页',
  `status` tinyint(4) NOT NULL COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ims_xm_mallv3_diy_page`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ims_xm_mallv3_diy_page`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID';");
}
if (!pdo_tableexists('xm_mallv3_sms_code')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_sms_code` (
    `id` int(11) NOT NULL,
    `telephone` varchar(64) NOT NULL COMMENT '验证码',
    `code` int(11) NOT NULL COMMENT '验证码',
    `create_time` int(11) NOT NULL COMMENT '创建时间',
    `end_time` int(11) NOT NULL COMMENT '过期时间'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='短信表';
  
  
  ALTER TABLE `ims_xm_mallv3_sms_code`
    ADD PRIMARY KEY (`id`);
  
  
  ALTER TABLE `ims_xm_mallv3_sms_code`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;  ");
}

if (!pdo_tableexists('xm_mallv3_goods_combination')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_goods_combination` (
    `id` int(10) NOT NULL,
    `goods_id` int(10) NOT NULL COMMENT '商品id',
    `parent_id` int(10) NOT NULL COMMENT '所属套装主id',
    `numbers` int(10) NOT NULL COMMENT '数量'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品套装组合表';
  
  ALTER TABLE `ims_xm_mallv3_goods_combination`
    ADD PRIMARY KEY (`id`);
  
  
  ALTER TABLE `ims_xm_mallv3_goods_combination`
    MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_agent_distribution')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_agent_distribution` (
  `id` int(11) NOT NULL COMMENT 'id',
  `uid` int(11) NOT NULL COMMENT '会员id',
  `goods_id` int(11) NOT NULL COMMENT '商品id',
  `status` int(1) NOT NULL DEFAULT '0' COMMENT '状态:0:原价,1:加价,2:减价',
  `dprice` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT '价格设置'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代理分销价格表';

ALTER TABLE `ims_xm_mallv3_agent_distribution`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ims_xm_mallv3_agent_distribution`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id';");
}

if (!pdo_tableexists('xm_mallv3_order_express')) {
  pdo_run("CREATE TABLE IF NOT EXISTS " . tablename('xm_mallv3_order_express') . " (
  `id` int(11) NOT NULL,
  `weid` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `expressname` varchar(255) NOT NULL COMMENT '快递公司名称',
  `express_code` varchar(255) DEFAULT NULL COMMENT '快递公司编码',
  `express_no` varchar(255) NOT NULL COMMENT '快递单号',
  `create_time` int(11) NOT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单电子面单记录';

ALTER TABLE " . tablename('xm_mallv3_order_express') . "
  ADD PRIMARY KEY (`id`);

ALTER TABLE " . tablename('xm_mallv3_order_express') . "
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_order_express')) {
  pdo_run("CREATE TABLE IF NOT EXISTS " . tablename('xm_mallv3_order_express') . " (
  `id` int(11) NOT NULL,
  `weid` int(11) DEFAULT NULL,
  `order_id` int(11) DEFAULT NULL,
  `expressname` varchar(255) NOT NULL COMMENT '快递公司名称',
  `express_code` varchar(255) DEFAULT NULL COMMENT '快递公司编码',
  `express_no` varchar(255) NOT NULL COMMENT '快递单号',
  `create_time` int(11) NOT NULL COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单电子面单记录';

ALTER TABLE " . tablename('xm_mallv3_order_express') . "
  ADD PRIMARY KEY (`id`);

ALTER TABLE " . tablename('xm_mallv3_order_express') . "
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_member_cashlogs')) {
  pdo_run("DROP TABLE IF EXISTS " . tablename('xm_mallv3_member_cashlogs') . ";
CREATE TABLE IF NOT EXISTS " . tablename('xm_mallv3_member_cashlogs') . " (
  `id` int(11) NOT NULL,
  `weid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_num_alias` varchar(40) NOT NULL,
  `remarks` varchar(255) NOT NULL COMMENT '说明',
  `prefix` tinyint(2) NOT NULL COMMENT '1.增加，2.减少',
  `amount` decimal(9,2) NOT NULL COMMENT '订单总价',
  `create_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='代理商分红';

ALTER TABLE " . tablename('xm_mallv3_member_cashlogs') . "
  ADD PRIMARY KEY (`id`);

ALTER TABLE " . tablename('xm_mallv3_member_cashlogs') . "
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_goods_member_discount')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_goods_member_discount` (
    `id` int(11) NOT NULL,
    `goods_id` int(11) NOT NULL DEFAULT '0',
    `mgid` int(11) DEFAULT '0' COMMENT '会员组',
    `discount_method` tinyint(1) DEFAULT '0' COMMENT '折扣方式，0.折扣 1.加减金额',
    `addsubtract` tinyint(1) DEFAULT '0' COMMENT '0:加1减',
    `price` decimal(15,2) NOT NULL DEFAULT '0.00'
  ) ENGINE=InnoDB AUTO_INCREMENT=216 DEFAULT CHARSET=utf8 COMMENT='会员分组价格';

ALTER TABLE " . tablename('xm_mallv3_goods_member_discount') . "
  ADD PRIMARY KEY (`id`),
  ADD KEY `goods_id` (`goods_id`);

ALTER TABLE " . tablename('xm_mallv3_goods_member_discount') . "
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_auth_group')) {
  pdo_run("CREATE TABLE IF NOT EXISTS " . tablename('xm_mallv3_auth_group') . " (
  `id` int(10) unsigned NOT NULL,
  `weid` int(11) NOT NULL COMMENT '平台id',
  `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父组别',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '组名',
  `datarules` int(2) NOT NULL COMMENT '访问数据权限',
  `rules` text NOT NULL COMMENT '规则ID',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='分组表';

ALTER TABLE " . tablename('xm_mallv3_auth_group') . "
  ADD PRIMARY KEY (`id`);

ALTER TABLE " . tablename('xm_mallv3_auth_group') . "
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_auth_group_access')) {
  pdo_run("CREATE TABLE IF NOT EXISTS " . tablename('xm_mallv3_auth_group_access') . " (
  `uid` int(10) unsigned NOT NULL COMMENT '会员ID',
  `group_id` int(10) unsigned NOT NULL COMMENT '级别ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='权限分组表';

ALTER TABLE " . tablename('xm_mallv3_auth_group_access') . "
  ADD UNIQUE KEY `uid_group_id` (`uid`,`group_id`),
  ADD KEY `uid` (`uid`),
  ADD KEY `group_id` (`group_id`);");
}

if (!pdo_tableexists('xm_mallv3_user')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_user` (
    `id` int(11) NOT NULL COMMENT 'id',
    `uuid` varchar(68) DEFAULT '',
    `weid` int(11) NOT NULL,
    `lastweid` int(11) DEFAULT '0' COMMENT '最后进入的平台',
    `uid` int(11) DEFAULT NULL,
    `w7uid` int(11) NOT NULL,
    `did` int(11) DEFAULT NULL COMMENT '部门id',
    `tid` int(11) DEFAULT '0' COMMENT '师傅id',
    `sid` int(11) DEFAULT '0' COMMENT '店id',
    `ocid` int(11) DEFAULT '0',
    `username` varchar(50) DEFAULT '' COMMENT '用户名',
    `password` varchar(100) DEFAULT '',
    `salt` varchar(10) DEFAULT '',
    `touxiang` varchar(255) DEFAULT '' COMMENT '头像',
    `qianming` varchar(200) DEFAULT '',
    `title` varchar(50) DEFAULT '' COMMENT '姓名',
    `sex` tinyint(1) DEFAULT '0',
    `mobile` varchar(20) DEFAULT NULL COMMENT '手机号',
    `role_id` int(11) DEFAULT '0',
    `remark` varchar(50) DEFAULT '' COMMENT '备注',
    `login_ip` varchar(30) DEFAULT NULL COMMENT '最近登录IP',
    `login_time` int(11) DEFAULT NULL COMMENT '最近登录时间',
    `px` int(6) DEFAULT '0',
    `time` int(11) DEFAULT '0',
    `role` varchar(20) DEFAULT NULL COMMENT '微擎权限分组',
    `create_time` int(11) DEFAULT '0',
    `update_time` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '1'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  

ALTER TABLE " . tablename('xm_mallv3_user') . "
  ADD PRIMARY KEY (`id`);

ALTER TABLE " . tablename('xm_mallv3_user') . "
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id';");
}

if (!pdo_tableexists('xm_mallv3_order_refund')) {
  pdo_run("CREATE TABLE IF NOT EXISTS " . tablename('xm_mallv3_order_refund') . " (
  `id` int(11) NOT NULL,
  `weid` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_refund_no` varchar(255) NOT NULL DEFAULT '' COMMENT '退款单号',
  `lianxiren` varchar(20) NOT NULL COMMENT '联系人',
  `tel` varchar(20) NOT NULL COMMENT '电话',
  `refund_type` smallint(6) NOT NULL DEFAULT '1' COMMENT '售后类型：1=未发货退款，2=退货退款，3=换货',
  `refund_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `refund_desc` varchar(500) NOT NULL DEFAULT '' COMMENT '退款说明',
  `pic_list` longtext COMMENT '凭证图片列表：json格式',
  `refund_status` smallint(1) NOT NULL DEFAULT '0' COMMENT '状态：0=待商家处理，1=同意并已退款，2=已同意换货，3=已拒绝退换货',
  `refuse_desc` varchar(500) NOT NULL DEFAULT '' COMMENT '拒绝退换货原因',
  `addtime` int(11) NOT NULL DEFAULT '0',
  `is_delete` smallint(1) NOT NULL DEFAULT '0',
  `response_time` int(11) NOT NULL DEFAULT '0' COMMENT '商家处理时间',
  `is_agree` smallint(1) NOT NULL DEFAULT '0' COMMENT '是否已同意退、换货：0=待处理，1=已同意，2=已拒绝',
  `is_user_send` smallint(1) NOT NULL DEFAULT '0' COMMENT '用户已发货：0=未发货，1=已发货',
  `user_send_express` varchar(32) NOT NULL DEFAULT '' COMMENT '用户发货快递公司',
  `user_send_express_code` varchar(10) NOT NULL,
  `user_send_express_no` varchar(32) NOT NULL DEFAULT '' COMMENT '用户发货快递单号',
  `refund_address_id` int(11) DEFAULT '0' COMMENT '退货 换货地址id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='售后订单';

ALTER TABLE " . tablename('xm_mallv3_order_refund') . "
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`uid`),
  ADD KEY `is_delete` (`is_delete`);

ALTER TABLE " . tablename('xm_mallv3_order_refund') . "
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_refund_address')) {
  pdo_run("CREATE TABLE IF NOT EXISTS " . tablename('xm_mallv3_refund_address') . " (
  `id` int(11) NOT NULL,
  `weid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '收件人名称',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '收件人地址',
  `mobile` varchar(255) NOT NULL DEFAULT '' COMMENT '收件人电话',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL COMMENT '更新时间',
  `is_delete` smallint(6) DEFAULT '0',
  `status` tinyint(1) NOT NULL COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE " . tablename('xm_mallv3_refund_address') . "
  ADD PRIMARY KEY (`id`);

ALTER TABLE " . tablename('xm_mallv3_refund_address') . "
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_store')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_store` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `uid` int(11) DEFAULT '0',
    `level` int(10) DEFAULT '0' COMMENT '店等级',
    `title` varchar(100) DEFAULT '' COMMENT '店名',
    `owner_name` varchar(60) DEFAULT '' COMMENT '店主名称',
    `latitude` varchar(30) DEFAULT '' COMMENT '纬度',
    `longitude` varchar(30) DEFAULT '' COMMENT '经度',
    `region_name` varchar(100) DEFAULT NULL COMMENT '地址名称',
    `province` int(11) DEFAULT '0' COMMENT '省',
    `province_name` varchar(30) DEFAULT '' COMMENT '省名称',
    `city` int(11) DEFAULT '0' COMMENT '市',
    `city_name` varchar(30) DEFAULT '' COMMENT '市名称',
    `district` int(11) DEFAULT '0' COMMENT '区',
    `district_name` varchar(30) DEFAULT '' COMMENT '区名称',
    `community` int(11) DEFAULT '0' COMMENT '镇街',
    `community_name` varchar(30) DEFAULT '' COMMENT '镇街名称',
    `address` varchar(255) DEFAULT '' COMMENT '地址',
    `house_number` varchar(100) DEFAULT '' COMMENT '门牌号',
    `zipcode` varchar(20) DEFAULT '' COMMENT '邮政编码',
    `tel` varchar(60) DEFAULT '' COMMENT '联系电话',
    `grade` tinyint(3) UNSIGNED DEFAULT '0' COMMENT '店等级',
    `credit_value` int(10) DEFAULT '0' COMMENT '信用值(多少星)',
    `praise` int(8) UNSIGNED DEFAULT '0' COMMENT '好评',
    `viewed` int(11) DEFAULT '0' COMMENT '点击量',
    `domain` varchar(100) DEFAULT NULL COMMENT '业务范围',
    `certification` varchar(255) DEFAULT NULL COMMENT '认证',
    `sort` int(10) UNSIGNED DEFAULT '0' COMMENT '排序',
    `recommended` tinyint(2) DEFAULT '0' COMMENT '推荐',
    `theme` varchar(60) DEFAULT '' COMMENT '主题风格',
    `store_logo` varchar(255) DEFAULT NULL COMMENT '店LOGO',
    `slogan` varchar(100) DEFAULT '' COMMENT '标语',
    `description` varchar(255) DEFAULT NULL COMMENT '描述',
    `content` text COMMENT '内容',
    `enable_radar` tinyint(1) UNSIGNED DEFAULT '1' COMMENT '启用雷达',
    `create_time` int(10) UNSIGNED DEFAULT NULL COMMENT '创建时间',
    `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
    `end_time` int(10) UNSIGNED DEFAULT '0' COMMENT '结束时间(0为永久)',
    `close_reason` varchar(255) DEFAULT '' COMMENT '关闭原因',
    `status` tinyint(1) DEFAULT '1' COMMENT '状态'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
  
  ALTER TABLE `ims_xm_mallv3_store`
    ADD PRIMARY KEY (`id`),
    ADD KEY `store_name` (`title`),
    ADD KEY `owner_name` (`owner_name`),
    ADD KEY `domain` (`domain`),
    ADD KEY `id` (`id`);
  
  ALTER TABLE `ims_xm_mallv3_store`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_store_to_admin')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_store_to_admin` (
    `sid` int(11) NOT NULL DEFAULT '0',
    `uid` int(11) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='店管理员';");
}

if (!pdo_tableexists('xm_mallv3_store_level')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_store_level` (
  `id` int(10) unsigned NOT NULL COMMENT 'ID',
  `weid` int(11) NOT NULL,
  `title` varchar(50) NOT NULL COMMENT '标题',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `create_time` int(11) NOT NULL DEFAULT '0',
  `update_time` int(10) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商店等级';

ALTER TABLE `ims_xm_mallv3_store_level`
  ADD PRIMARY KEY (`id`),

ALTER TABLE `ims_xm_mallv3_store_level`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID';");
}
if (!pdo_tableexists('xm_mallv3_order_offline')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_order_offline` (
      `id` int(11) NOT NULL,
      `weid` int(11) NOT NULL,
      `order_id` int(11) NOT NULL,
      `remark` varchar(500) NOT NULL,
      `image` varchar(64) CHARACTER SET utf8mb4 NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='线下订单用户上传信息记录';
    
    ALTER TABLE `ims_xm_mallv3_order_offline`
      ADD PRIMARY KEY (`id`);
    
    ALTER TABLE `ims_xm_mallv3_order_offline`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_store_image')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_store_image` (
`id` int(11) NOT NULL,
  `weid` int(11) NOT NULL,
  `sid` int(11) NOT NULL DEFAULT '0',
  `ptype` varchar(20) NOT NULL COMMENT '类别',
  `image` varchar(255) DEFAULT NULL,
  `sort` int(3) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品图片表';

ALTER TABLE `ims_xm_mallv3_store_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sid` (`sid`);

ALTER TABLE `ims_xm_mallv3_store_image`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}


if (!pdo_tableexists('xm_mallv3_comment')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_comment` (
    `id` int(11) NOT NULL,
    `reply_id` int(11) DEFAULT '0',
    `weid` int(11) NOT NULL DEFAULT '0',
    `store_id` int(11) DEFAULT '0',
    `order_id` int(11) DEFAULT '0',
    `cat_id` int(11) DEFAULT '0' COMMENT '分类ID',
    `goods_id` int(11) DEFAULT '0',
    `technical_uuid` int(11) DEFAULT '0' COMMENT '师傅id',
    `uid` int(11) DEFAULT '0',
    `nick_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '',
    `head_img_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '头像',
    `content` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '内容',
    `level` tinyint(1) DEFAULT '0' COMMENT '评分等级',
    `images` text COLLATE utf8mb4_unicode_ci,
    `create_time` int(11) NOT NULL DEFAULT '0',
    `update_time` int(11) DEFAULT NULL,
    `delete_time` int(11) DEFAULT NULL,
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `ims_xm_mallv3_comment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_uniacid` (`weid`),
  ADD KEY `idx_orderid` (`order_id`),
  ADD KEY `idx_goodsid` (`goods_id`),
  ADD KEY `idx_openid` (`uid`),
  ADD KEY `idx_createtime` (`create_time`);

ALTER TABLE `ims_xm_mallv3_comment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_bottom_menu')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_bottom_menu` (
    `id` int(10) unsigned NOT NULL COMMENT '文档ID',
    `weid` int(11) NOT NULL,
    `module` varchar(30) DEFAULT NULL,
    `tid` int(11) DEFAULT '0',
    `title` varchar(50) DEFAULT NULL COMMENT '标题',
    `title1` varchar(50) DEFAULT '',
    `tiaojian` varchar(30) DEFAULT '',
    `url` varchar(160) DEFAULT NULL COMMENT '链接地址',
    `customurl` varchar(160) DEFAULT '' COMMENT '自定义url',
    `zdyLinktype` varchar(20) DEFAULT '' COMMENT '自定义类型',
    `zdyappid` varchar(20) DEFAULT '',
    `icon` varchar(160) DEFAULT NULL,
    `iconactive` varchar(160) DEFAULT '',
    `hump` tinyint(1) DEFAULT '0' COMMENT '是否凸起',
    `plugin` varchar(60) DEFAULT '' COMMENT '插件',
    `is_index` tinyint(1) DEFAULT '0',
    `is_submitaudit` tinyint(1) DEFAULT '0',
    `sort` int(10) unsigned DEFAULT '0' COMMENT '排序（同级有效）',
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='小程序底部菜单';
  
  ALTER TABLE `ims_xm_mallv3_bottom_menu`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_bottom_menu`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文档ID';");
}

if (!pdo_tableexists('xm_mallv3_printer')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_printer` (
`id` int(10) unsigned NOT NULL COMMENT '配置ID',
  `weid` int(11) NOT NULL,
  `sid` int(11) DEFAULT '0',
  `name` varchar(60) DEFAULT '' COMMENT '名称',
  `pinpai` varchar(20) NOT NULL,
  `settings` text COMMENT '配置值',
  `status` tinyint(2) NOT NULL DEFAULT '1' COMMENT '状态，1启用，0禁用',
  `sort` int(5) NOT NULL DEFAULT '0' COMMENT '排序',
  `update_time` int(11) DEFAULT '0',
  `create_time` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ims_xm_mallv3_printer`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ims_xm_mallv3_printer`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID';");
}

if (!pdo_tableexists('xm_mallv3_withdraw')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_withdraw` (
  `id` int(10) unsigned NOT NULL,
  `withdraw_sn` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '编号',
  `weid` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `amounts` decimal(14,2) DEFAULT NULL COMMENT '金额',
  `poundage` decimal(14,2) DEFAULT NULL COMMENT '手续费',
  `poundage_rate` decimal(11,2) DEFAULT NULL COMMENT '手续费率',
  `poundage_type` tinyint(4) DEFAULT '0' COMMENT '手续费类型 0:比例 1:固定',
  `pay_way` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '支付方式',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `audit_time` int(11) DEFAULT NULL COMMENT '审计时间',
  `pay_time` int(11) DEFAULT NULL COMMENT '支付时间',
  `arrival_time` int(11) DEFAULT NULL COMMENT '到帐时间',
  `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
  `deleted_time` int(11) DEFAULT NULL COMMENT '删除时间',
  `actual_amounts` decimal(14,2) NOT NULL COMMENT '实际金额',
  `actual_poundage` decimal(14,2) NOT NULL COMMENT '实际手续费',
  `servicetax` decimal(12,2) DEFAULT NULL COMMENT '劳务税',
  `servicetax_rate` decimal(11,2) DEFAULT NULL COMMENT '劳务税比例',
  `actual_servicetax` decimal(12,2) DEFAULT NULL COMMENT '最终劳务税',
  `remark` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '备注',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE `ims_xm_mallv3_withdraw`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ims_xm_mallv3_withdraw`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_coupon')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_coupon` (
    `id` int(11) unsigned NOT NULL,
    `ptype` tinyint(1) DEFAULT '1',
    `weid` int(11) NOT NULL,
    `ocid` int(11) DEFAULT '0',
    `sid` int(11) DEFAULT '0',
    `name` varchar(255) DEFAULT '' COMMENT '优惠券名称',
    `buy_price` decimal(10,2) DEFAULT '0.00' COMMENT '购买价格',
    `color` varchar(10) DEFAULT '10' COMMENT '优惠券颜色',
    `coupon_type` tinyint(3) unsigned DEFAULT '10' COMMENT '优惠券类型(10满减券 20折扣券)',
    `use_goods` tinyint(1) DEFAULT '0' COMMENT '适用商品',
    `cat_ids` varchar(200) DEFAULT '' COMMENT '适用品类',
    `goods_ids` text COMMENT '适用产品编号',
    `reduce_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '满减券-减免金额',
    `discount` tinyint(3) unsigned DEFAULT '0' COMMENT '折扣券-折扣率(0-100)',
    `condition_type` tinyint(1) DEFAULT '0' COMMENT '使用门槛0无门槛1有门槛',
    `min_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '最低消费金额',
    `expire_type` tinyint(3) unsigned DEFAULT '10' COMMENT '到期类型(10领取后生效 20固定时间)',
    `expire_day` int(11) unsigned DEFAULT '0' COMMENT '领取后生效-有效天数',
    `start_time` int(11) unsigned DEFAULT '0' COMMENT '固定时间-开始时间',
    `end_time` int(11) unsigned DEFAULT '0' COMMENT '固定时间-结束时间',
    `total_num` int(11) DEFAULT '0' COMMENT '发放总数量(-1为不限制)',
    `receive_num` int(11) unsigned DEFAULT '0' COMMENT '已领取数量',
    `sort` int(11) unsigned DEFAULT '0' COMMENT '排序方式(数字越小越靠前)',
    `is_delete` tinyint(3) unsigned DEFAULT '0' COMMENT '软删除',
    `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
    `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
    `status` tinyint(1) DEFAULT NULL COMMENT '状态'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='优惠券';
  
  ALTER TABLE `ims_xm_mallv3_coupon`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_coupon`
    MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_coupon_receive')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_coupon_receive` (
    `id` int(11) unsigned NOT NULL COMMENT '主键id',
    `weid` int(11) NOT NULL,
    `uid` int(11) unsigned NOT NULL DEFAULT '0',
    `coupon_id` int(11) unsigned NOT NULL COMMENT '优惠券id',
    `name` varchar(255) DEFAULT '' COMMENT '优惠券名称',
    `buy_price` decimal(10,2) DEFAULT NULL COMMENT '购买价格',
    `number` int(5) DEFAULT '1',
    `color` varchar(10) DEFAULT '' COMMENT '优惠券颜色',
    `coupon_type` tinyint(3) unsigned DEFAULT '10' COMMENT '优惠券类型(10满减券 20折扣券)',
    `reduce_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '满减券-减免金额',
    `discount` tinyint(3) unsigned DEFAULT '0' COMMENT '折扣券-折扣率(0-100)',
    `condition_type` tinyint(1) DEFAULT '0' COMMENT '使用门槛0无门槛1有门槛',
    `use_goods` tinyint(1) DEFAULT '0' COMMENT '适用商品',
    `cat_ids` varchar(200) DEFAULT '' COMMENT '适用品类',
    `goods_ids` text COMMENT '适用产品编号',
    `min_price` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '最低消费金额',
    `expire_type` tinyint(3) unsigned DEFAULT '10' COMMENT '到期类型(10领取后生效 20固定时间)',
    `expire_day` int(11) unsigned DEFAULT '0' COMMENT '领取后生效-有效天数',
    `start_time` int(11) unsigned DEFAULT '0' COMMENT '有效期开始时间',
    `end_time` int(11) unsigned DEFAULT '0' COMMENT '有效期结束时间',
    `is_expire` tinyint(3) unsigned DEFAULT '0' COMMENT '是否过期(0未过期 1已过期)',
    `is_use` tinyint(3) unsigned DEFAULT '0' COMMENT '是否已使用(0未使用 1已使用)',
    `create_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
    `update_time` int(11) unsigned DEFAULT '0' COMMENT '更新时间',
    `status` tinyint(1) DEFAULT '1'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='优惠券领取记录';
  
  ALTER TABLE `ims_xm_mallv3_coupon_receive`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_coupon_receive`
    MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键id';");
}

if (!pdo_tableexists('xm_mallv3_department')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_department` (
  `id` int(11) NOT NULL,
  `weid` int(11) NOT NULL,
  `pid` int(11) NOT NULL COMMENT '上级部门id',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '名称',
  `owner_name` varchar(60) NOT NULL DEFAULT '' COMMENT '部门主管名称',
  `owner_uid` int(11) NOT NULL COMMENT '部门主管id',
  `regionid` int(10) NOT NULL COMMENT '址区id',
  `region_name` varchar(100) DEFAULT NULL COMMENT '地区名称',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '地址',
  `zipcode` varchar(20) NOT NULL DEFAULT '' COMMENT '邮编',
  `tel` varchar(60) NOT NULL DEFAULT '' COMMENT '联系电话',
  `close_reason` varchar(255) NOT NULL DEFAULT '' COMMENT '关闭原因',
  `create_time` int(11) DEFAULT NULL COMMENT '建立的时间',
  `update_time` int(11) NOT NULL,
  `end_time` int(10) NOT NULL COMMENT '过期时间',
  `slogan` varchar(100) NOT NULL COMMENT '部门口号',
  `description` text COMMENT '部门描述',
  `status` tinyint(1) NOT NULL COMMENT '是否公开'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

ALTER TABLE `ims_xm_mallv3_department`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ims_xm_mallv3_department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_miaosha_goods')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_miaosha_goods` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `sid` int(11) DEFAULT '0',
    `title` varchar(100) DEFAULT NULL COMMENT '标题',
    `ocid` int(11) DEFAULT '0',
    `goods_id` int(11) NOT NULL,
    `price` decimal(15,2) NOT NULL COMMENT '秒杀价格',
    `sale_count` int(11) DEFAULT '0',
    `end_date` int(11) DEFAULT '0' COMMENT '结束时间',
    `begin_date` int(11) NOT NULL COMMENT '开始时间',
    `buy_max` int(11) DEFAULT '0' COMMENT '限购数量，0=不限购',
    `member_buy_max` int(6) DEFAULT '0' COMMENT '每人限购',
    `buy_limit` int(11) UNSIGNED DEFAULT '0' COMMENT '限单',
    `is_level` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否享受会员折扣 0-不享受 1--享受',
    `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
    `update_time` int(11) DEFAULT '0',
    `sort` int(11) DEFAULT '60',
    `status` tinyint(1) DEFAULT '1' COMMENT '状态'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  ALTER TABLE `ims_xm_mallv3_miaosha_goods`
    ADD PRIMARY KEY (`id`),
    ADD KEY `goods_id` (`goods_id`),
    ADD KEY `weid` (`weid`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_miaosha_goods`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_invite_code')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_invite_code` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `gid` int(11) NOT NULL COMMENT '邀请码所属会员组',
    `code` varchar(40) NOT NULL,
    `is_use` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否使用1否 0是'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员注册邀请码';
  
  ALTER TABLE `ims_xm_mallv3_invite_code`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_invite_code`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_register_field')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_register_field` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `ptype` varchar(30) DEFAULT '',
    `fieldsmingcheng` varchar(50) DEFAULT NULL COMMENT '字段名称',
    `viewmingcheng` varchar(50) NOT NULL COMMENT '显示文字',
    `is_sys` tinyint(1) DEFAULT '0' COMMENT '是否系统字段',
    `is_listView` tinyint(1) DEFAULT '0' COMMENT '是否列表显示',
    `is_search` tinyint(1) DEFAULT '0' COMMENT '是否搜索',
    `is_front` tinyint(1) DEFAULT '0' COMMENT '前端显示',
    `is_import` tinyint(1) DEFAULT '0' COMMENT '导入导出',
    `inputtype` varchar(20) NOT NULL COMMENT '输入方式',
    `selectvalue` text COMMENT '选择的项目',
    `defaultvalue` text COMMENT '默认值',
    `filetype` varchar(100) DEFAULT '' COMMENT '可以上传文件类型',
    `shuoming` varchar(100) DEFAULT NULL COMMENT '说明',
    `create_time` int(11) NOT NULL COMMENT '创建时间',
    `update_time` int(11) DEFAULT NULL COMMENT '更新时间',
    `is_input` tinyint(1) DEFAULT '1' COMMENT '是否可以输入',
    `sort` int(11) DEFAULT NULL COMMENT '排序',
    `status` tinyint(1) DEFAULT '1' COMMENT '状态'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_register_field`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_register_field`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_member_bankcard')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_member_bankcard` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `uid` int(11) NOT NULL DEFAULT '0',
    `collect_type` varchar(20) DEFAULT NULL COMMENT '帐号类型',
    `ptype` int(3) NOT NULL COMMENT '类别',
    `name` varchar(20) DEFAULT NULL,
    `accounts` varchar(100) NOT NULL COMMENT '帐号',
    `telephone` varchar(20) DEFAULT NULL,
    `bankname` varchar(200) DEFAULT NULL COMMENT '银行',
    `branchname` varchar(200) DEFAULT NULL COMMENT '支行',
    `isDefault` tinyint(1) DEFAULT '0' COMMENT '是否为默认'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户收款帐号';
  
  ALTER TABLE `ims_xm_mallv3_member_bankcard`
    ADD PRIMARY KEY (`id`),
    ADD KEY `uid` (`uid`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_member_bankcard`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_service_time')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_service_time` (
    `id` int(10) unsigned NOT NULL COMMENT 'ID',
    `weid` int(11) NOT NULL,
    `begin_time` varchar(10) NOT NULL COMMENT '天始时间',
    `end_time` varchar(10) NOT NULL COMMENT '结速时间',
    `sort` int(6) unsigned DEFAULT '0' COMMENT '排序',
    `create_time` int(11) NOT NULL DEFAULT '0',
    `update_time` int(10) DEFAULT '0',
    `status` tinyint(1) DEFAULT '1'
  ) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='服务时间';
  
  ALTER TABLE `ims_xm_mallv3_service_time`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_service_time`
    MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',AUTO_INCREMENT=7;");
}

if (!pdo_tableexists('xm_mallv3_order_staff')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_order_staff` (
    `id` int(11) NOT NULL,
    `order_id` int(11) DEFAULT NULL,
    `uid` int(11) NOT NULL,
    `title` varchar(20) DEFAULT NULL COMMENT '师傅姓名',
    `create_time` int(11) NOT NULL COMMENT '创建时间'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='师傅';
  
  ALTER TABLE `ims_xm_mallv3_order_staff`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_order_staff`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_technical')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_technical` (
    `id` int(11) NOT NULL,
    `uuid` varchar(68) DEFAULT '',
    `weid` int(11) NOT NULL,
    `sid` int(11) DEFAULT '0',
    `hid` int(11) DEFAULT NULL COMMENT '医院id',
    `ocid` int(11) DEFAULT '0',
    `uid` int(11) DEFAULT NULL,
    `answer` varchar(255) DEFAULT '' COMMENT '留言',
    `tel` varchar(20) DEFAULT '' COMMENT '手机号',
    `title` varchar(40) DEFAULT NULL,
    `category_id` int(3) DEFAULT '0' COMMENT '分类',
    `level` int(3) DEFAULT '0' COMMENT '等级',
    `touxiang` varchar(200) DEFAULT NULL COMMENT '头像',
    `photoalbum` text COMMENT '相册',
    `videourl` varchar(200) DEFAULT NULL,
    `service_times` int(11) DEFAULT '0' COMMENT '服务次数',
    `service_times_base` int(11) DEFAULT '0' COMMENT '接单基数',
    `comment` int(11) DEFAULT '0' COMMENT '评价数量',
    `comment_base` int(11) DEFAULT '0' COMMENT '评价基数',
    `viewed` int(11) DEFAULT '1',
    `viewed_base` int(11) DEFAULT '0' COMMENT '人气基数',
    `cate_ids` varchar(200) DEFAULT '' COMMENT '可接服务分类id',
    `total_income` decimal(9,2) DEFAULT '0.00' COMMENT '总收入',
    `income` decimal(9,2) DEFAULT '0.00' COMMENT '收入',
    `points` int(11) DEFAULT '0' COMMENT '积分',
    `email` varchar(40) DEFAULT '',
    `workunits` varchar(200) DEFAULT '' COMMENT '工作单位',
    `introduction` text COMMENT '简介',
    `id_cart` varchar(64) DEFAULT '' COMMENT '身份证',
    `province_name` varchar(30) DEFAULT NULL,
    `city_name` varchar(30) DEFAULT NULL,
    `district_name` varchar(30) DEFAULT NULL,
    `region_name` varchar(100) DEFAULT NULL,
    `longitude` varchar(30) DEFAULT NULL,
    `latitude` varchar(30) DEFAULT NULL,
    `dizhi` text COMMENT '地址',
    `house_number` varchar(100) DEFAULT NULL,
    `customtext` text,
    `create_time` int(11) DEFAULT '0',
    `end_time` int(11) DEFAULT '0' COMMENT '到期时间',
    `is_business` tinyint(1) DEFAULT '1' COMMENT '是否营业',
    `sort` int(11) DEFAULT '100',
    `status` tinyint(1) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='师傅表';
  
  ALTER TABLE `ims_xm_mallv3_technical`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_technical`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_operatingcity')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_operatingcity` (
    `id` int(10) UNSIGNED NOT NULL,
    `uuid` varchar(68) DEFAULT '',
    `weid` int(11) NOT NULL,
    `level` tinyint(3) DEFAULT '0',
    `areatype` tinyint(2) DEFAULT '0',
    `title` varchar(60) DEFAULT NULL,
    `income` decimal(15,2) DEFAULT '0.00' COMMENT '当前收入',
    `total_income` decimal(15,2) DEFAULT '0.00' COMMENT '总收入',
    `cate_ids` varchar(200) DEFAULT '' COMMENT '可经营的类目',
    `region_name` varchar(200) DEFAULT NULL COMMENT '位置名称',
    `province_id` int(11) DEFAULT NULL,
    `province_name` varchar(30) DEFAULT '',
    `city_id` int(11) DEFAULT NULL,
    `city_name` varchar(30) DEFAULT '',
    `district_id` int(11) DEFAULT '0',
    `district_name` varchar(30) DEFAULT '',
    `area_name` varchar(60) DEFAULT NULL COMMENT '区域名',
    `house_number` varchar(200) DEFAULT NULL,
    `tel` varchar(60) DEFAULT '',
    `customtext` text,
    `settings` text COMMENT '配置',
    `create_time` int(11) NOT NULL,
    `update_time` int(11) NOT NULL,
    `end_time` int(11) DEFAULT '0' COMMENT '结速时间',
    `is_default` tinyint(1) DEFAULT '0' COMMENT '是否默认',
    `sort` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='城市运营表';
  
  ALTER TABLE `ims_xm_mallv3_operatingcity`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_operatingcity`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_operatingcity_level')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_operatingcity_level` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `title` varchar(40) DEFAULT '' COMMENT '等级名称',
    `return_percent` decimal(9,2) DEFAULT '0.00' COMMENT '返佣比例',
    `sort` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_operatingcity_level`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_operatingcity_level`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_goods_sku')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_goods_sku` (
    `id` int(11) NOT NULL,
    `goods_id` int(11) NOT NULL DEFAULT '0',
    `name` varchar(100) DEFAULT NULL,
    `item` text,
    `ptype` varchar(40) DEFAULT NULL,
    `required` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  ALTER TABLE `ims_xm_mallv3_goods_sku`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_goods_sku`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_goods_sku_value')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_goods_sku_value` (
    `id` int(11) NOT NULL,
    `goods_id` int(11) NOT NULL DEFAULT '0',
    `sku` text,
    `image` varchar(255) DEFAULT NULL,
    `quantity` int(6) DEFAULT '0',
    `price` decimal(15,2) NOT NULL DEFAULT '0.00'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_goods_sku_value`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_goods_sku_value`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_sys_files')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_sys_files` (
    `id` int(11) NOT NULL,
    `weid` int(11) DEFAULT '0',
    `filepath` varchar(255) DEFAULT NULL COMMENT '图片路径',
    `hash` varchar(32) DEFAULT NULL COMMENT '文件hash值',
    `create_time` int(10) DEFAULT NULL COMMENT '创建时间'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
  
  ALTER TABLE `ims_xm_mallv3_sys_files`
    ADD PRIMARY KEY (`id`) USING BTREE,
    ADD KEY `hash` (`hash`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_sys_files`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_sys_log')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_sys_log` (
    `id` int(11) NOT NULL,
    `weid` int(11) DEFAULT '0',
    `application_name` varchar(50) DEFAULT NULL COMMENT '应用名称',
    `username` varchar(250) DEFAULT NULL COMMENT '操作用户',
    `url` varchar(250) DEFAULT NULL COMMENT '请求url',
    `ip` varchar(250) DEFAULT NULL COMMENT 'ip',
    `useragent` text COMMENT 'useragent',
    `content` text COMMENT '请求内容',
    `errmsg` varchar(250) DEFAULT NULL COMMENT '异常信息',
    `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
    `type` smallint(6) DEFAULT NULL COMMENT '类型'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
  
  ALTER TABLE `ims_xm_mallv3_sys_log`
    ADD PRIMARY KEY (`id`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_sys_log`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_users_roles')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_users_roles` (
    `id` int(11) NOT NULL COMMENT '编号',
    `pid` int(11) DEFAULT '0' COMMENT '所属父类',
    `weid` int(11) DEFAULT '0',
    `title` varchar(36) DEFAULT NULL COMMENT '分组名称',
    `datarules` tinyint(2) DEFAULT '0',
    `status` tinyint(4) DEFAULT NULL COMMENT '状态',
    `description` text COMMENT '描述',
    `access` text COMMENT '权限节点'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

  ALTER TABLE `ims_xm_mallv3_users_roles`
    ADD PRIMARY KEY (`id`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_users_roles`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号';");
}

if (!pdo_tableexists('xm_mallv3_users_sessions')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_users_sessions` (
    `id` int(11) NOT NULL,
    `weid` int(11) DEFAULT '0',
    `ptype` varchar(20) DEFAULT '' COMMENT '那个平台',
    `token` varchar(68) NOT NULL,
    `ip` varchar(30) DEFAULT '',
    `data` text,
    `expire_time` int(11) DEFAULT '0',
    `last_time` int(11) DEFAULT '0',
    `status` tinyint(4) DEFAULT '1' COMMENT '状态',
    `dev_status` tinyint(4) DEFAULT '1' COMMENT '1正常 0下线'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
  
  ALTER TABLE `ims_xm_mallv3_users_sessions`
    ADD PRIMARY KEY (`id`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_users_sessions`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_message')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_message` (
    `id` int(11) NOT NULL COMMENT '消息id',
    `weid` int(11) NOT NULL,
    `title` varchar(250) NOT NULL DEFAULT '' COMMENT '主题',
    `cishu` int(2) DEFAULT '0' COMMENT '提醒次数',
    `content` varchar(500) NOT NULL DEFAULT '' COMMENT '内容',
    `pages` varchar(100) DEFAULT '' COMMENT '打开的页面',
    `ptype` varchar(20) NOT NULL DEFAULT '' COMMENT '消息类型',
    `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 -1删除 0默认',
    `create_date` varchar(20) DEFAULT '',
    `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_message`
    ADD PRIMARY KEY (`id`),
    ADD KEY `subject` (`title`);
  
  ALTER TABLE `ims_xm_mallv3_message`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '消息id';");
}

if (!pdo_tableexists('xm_mallv3_message_broadcast')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_message_broadcast` (
    `id` int(11) NOT NULL,
    `message_id` int(11) NOT NULL DEFAULT '0' COMMENT '消息id',
    `sender` int(11) NOT NULL DEFAULT '0' COMMENT '发送者',
    `receiver` int(11) NOT NULL DEFAULT '0' COMMENT '接收者',
    `is_read` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0未读 1已读'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='消息广播表';
  
  ALTER TABLE `ims_xm_mallv3_message_broadcast`
    ADD PRIMARY KEY (`id`),
    ADD KEY `message_id` (`message_id`),
    ADD KEY `sender` (`sender`),
    ADD KEY `receiver` (`receiver`),
    ADD KEY `is_read` (`is_read`);
  
  ALTER TABLE `ims_xm_mallv3_message_broadcast`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_openid')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_openid` (
    `id` int(11) NOT NULL,
    `weid` int(11) DEFAULT '0',
    `uid` int(11) NOT NULL,
    `ptype` varchar(20) DEFAULT '' COMMENT '那个平台',
    `openid` varchar(68) NOT NULL
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

  ALTER TABLE `ims_xm_mallv3_openid`
    ADD PRIMARY KEY (`id`) USING BTREE;

  ALTER TABLE `ims_xm_mallv3_openid`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_uuid_relation')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_uuid_relation` (
    `id` int(11) NOT NULL,
    `weid` int(11) DEFAULT '0',
    `uid` int(11) DEFAULT '0',
    `uuid` varchar(68) DEFAULT ''
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
  
  ALTER TABLE `ims_xm_mallv3_uuid_relation`
    ADD PRIMARY KEY (`id`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_uuid_relation`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_lang')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_lang` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `locale` varchar(10) DEFAULT 'zh' COMMENT '语言环境',
    `item` varchar(50) DEFAULT NULL,
    `title` varchar(50) NOT NULL COMMENT '显示文字',
    `shuoming` varchar(100) NOT NULL COMMENT '说明',
    `sort` int(11) DEFAULT NULL COMMENT '排序',
    `status` tinyint(1) DEFAULT '1' COMMENT '状态'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_lang`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_lang`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_kefu_chat')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_kefu_chat` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `title` varchar(20) DEFAULT '' COMMENT '名称',
    `manage` varchar(55) DEFAULT '',
    `crowd` text NOT NULL COMMENT '成员',
    `is_group` tinyint(1) DEFAULT '0' COMMENT '是否群',
    `time` int(10) NOT NULL COMMENT '记录时间'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  ALTER TABLE `ims_xm_mallv3_kefu_chat`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_kefu_chat`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_kefu_chat_log')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_kefu_chat_log` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `chatgroupid` int(11) NOT NULL COMMENT '对话组',
    `fromclient` int(2) DEFAULT NULL COMMENT '来自那个客户端',
    `fromid` varchar(55) NOT NULL COMMENT '网页用户随机编号(仅为记录参考记录)',
    `fromname` varchar(255) DEFAULT NULL COMMENT '发送者名称',
    `fromavatar` varchar(255) DEFAULT NULL COMMENT '发送者头像',
    `toid` varchar(55) NOT NULL COMMENT '接收方',
    `toname` varchar(255) DEFAULT NULL COMMENT '接受者名称',
    `chattype` varchar(10) DEFAULT NULL,
    `contentType` varchar(20) DEFAULT NULL COMMENT '信息类型',
    `content` text COMMENT '发送的内容',
    `time` int(10) NOT NULL COMMENT '记录时间',
    `reading` tinyint(1) DEFAULT NULL,
    `fromprogram` varchar(50) DEFAULT '' COMMENT '来自那个小程序'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_kefu_chat_log`
    ADD PRIMARY KEY (`id`),
    ADD KEY `fromid` (`fromid`(4)) USING BTREE,
    ADD KEY `toid` (`toid`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_kefu_chat_log`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_kefu_commonly')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_kefu_commonly` (
    `id` int(11) NOT NULL,
    `weid` int(11) DEFAULT '0',
    `content` varchar(255) NOT NULL COMMENT '常用语内容',
    `create_time` int(11) NOT NULL COMMENT '添加时间',
    `update_time` int(11) NOT NULL,
    `px` int(11) DEFAULT '10',
    `status` tinyint(1) NOT NULL COMMENT '是否启用'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_kefu_commonly`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_kefu_commonly`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_kefu_contacts')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_kefu_contacts` (
    `id` int(10) unsigned NOT NULL,
    `weid` int(11) NOT NULL,
    `uid` int(11) DEFAULT '0',
    `chatid` varchar(68) DEFAULT '' COMMENT '聊天id',
    `sid` int(11) DEFAULT '0' COMMENT '门店id',
    `title` varchar(20) NOT NULL DEFAULT '' COMMENT '名称',
    `fromavatar` varchar(225) DEFAULT '' COMMENT '头像',
    `mobile` varchar(50) DEFAULT '' COMMENT '手机',
    `weixin` varchar(30) DEFAULT '' COMMENT '微信号',
    `province` varchar(20) DEFAULT NULL,
    `city` varchar(20) DEFAULT NULL,
    `district` varchar(20) DEFAULT NULL,
    `community` varchar(20) DEFAULT NULL,
    `sex` tinyint(1) DEFAULT '0' COMMENT '性别',
    `nianling` int(3) DEFAULT NULL COMMENT '年龄',
    `xvqiu` varchar(200) DEFAULT '' COMMENT '需求',
    `laiyuan` int(3) DEFAULT '0' COMMENT '来源',
    `create_time` int(11) NOT NULL COMMENT '创建时间',
    `status` tinyint(1) NOT NULL COMMENT '是否公开'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
  
  ALTER TABLE `ims_xm_mallv3_kefu_contacts`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_kefu_contacts`
    MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_kefu_reply')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_kefu_reply` (
    `id` int(11) NOT NULL,
    `weid` int(11) DEFAULT '0',
    `keyword` varchar(100) NOT NULL COMMENT '关健词',
    `content` text NOT NULL,
    `create_time` int(11) NOT NULL,
    `update_time` int(11) NOT NULL,
    `status` tinyint(1) NOT NULL DEFAULT '2' COMMENT '是否自动回复'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_kefu_reply`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_kefu_reply`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_kefu_seating')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_kefu_seating` (
    `id` int(11) NOT NULL COMMENT '客服id',
    `uid` int(11) NOT NULL,
    `chatid` varchar(68) DEFAULT '',
    `weid` int(11) DEFAULT '0',
    `w7uid` int(11) DEFAULT '0',
    `wxpid` varchar(50) DEFAULT '' COMMENT '小程序来源id',
    `reception` tinyint(2) DEFAULT '0',
    `tel` varchar(20) DEFAULT '',
    `qq` varchar(20) DEFAULT '',
    `title` varchar(255) DEFAULT '' COMMENT '客服名称',
    `touxiang` varchar(255) DEFAULT '' COMMENT '客服头像',
    `setopenidqrcode` varchar(255) DEFAULT '',
    `week` varchar(32) DEFAULT '' COMMENT '服务时间',
    `recovery` varchar(1024) DEFAULT '' COMMENT '进入客服时回复的内容',
    `online` tinyint(1) DEFAULT '1' COMMENT '是否在线',
    `groupid` int(11) DEFAULT '0' COMMENT '所属分组id',
    `sid` int(11) DEFAULT '0' COMMENT '部门id',
    `begintime` varchar(6) NOT NULL,
    `endtime` varchar(6) NOT NULL,
    `is_mpkefu` tinyint(1) NOT NULL DEFAULT '0',
    `is_mobilekefu` tinyint(1) NOT NULL DEFAULT '0',
    `is_wxappkefu` tinyint(1) NOT NULL DEFAULT '0',
    `is_webkefu` tinyint(1) NOT NULL DEFAULT '0',
    `lastgettime` int(11) DEFAULT '0',
    `px` int(6) DEFAULT '10',
    `status` tinyint(1) NOT NULL
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_kefu_seating`
    ADD PRIMARY KEY (`id`);

  ALTER TABLE `ims_xm_mallv3_kefu_seating`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '客服id';");
}

if (!pdo_tableexists('xm_mallv3_kefu_seatinggroups')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_kefu_seatinggroups` (
    `id` int(11) NOT NULL COMMENT '分组id',
    `weid` int(11) DEFAULT '0',
    `title` varchar(255) NOT NULL COMMENT '分组名称',
    `touxiang` varchar(200) DEFAULT '',
    `status` tinyint(1) NOT NULL COMMENT '分组状态',
    `px` int(11) DEFAULT '0'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

  ALTER TABLE `ims_xm_mallv3_kefu_seatinggroups`
    ADD PRIMARY KEY (`id`);

  ALTER TABLE `ims_xm_mallv3_kefu_seatinggroups`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '分组id';");
}

if (!pdo_tableexists('xm_mallv3_technical_level')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_technical_level` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `title` varchar(40) DEFAULT '' COMMENT '等级名称',
    `return_percent` decimal(9,2) DEFAULT '0.00' COMMENT '返佣比例',
    `sort` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  ALTER TABLE `ims_xm_mallv3_technical_level`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_technical_level`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_agent_code')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_agent_code` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `uid` int(11) NOT NULL,
    `agent_code` varchar(20) DEFAULT '',
    `create_time` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  ALTER TABLE `ims_xm_mallv3_agent_code`
    ADD PRIMARY KEY (`id`);

  ALTER TABLE `ims_xm_mallv3_agent_code`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_incomelog')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_incomelog` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `uid` int(11) NOT NULL,
    `level` int(11) DEFAULT '0',
    `order_id` int(11) DEFAULT '0',
    `ptype` int(3) NOT NULL COMMENT '流水类型',
    `order_num_alias` varchar(40) DEFAULT '',
    `buyer_id` int(11) NOT NULL COMMENT '下单人的id',
    `income` decimal(9,3) NOT NULL COMMENT '奖金',
    `return_percent` decimal(9,2) DEFAULT '0.00' COMMENT '提成点数',
    `percentremark` varchar(50) DEFAULT '' COMMENT '佣金公式',
    `order_total` decimal(6,2) DEFAULT '0.00' COMMENT '订单总价',
    `pay_time` int(11) DEFAULT '0' COMMENT '下单时间',
    `create_time` int(11) NOT NULL,
    `month_time` varchar(40) DEFAULT '',
    `year_time` varchar(40) DEFAULT '',
    `order_status_id` int(11) DEFAULT '0' COMMENT '订单状态'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收入明细';
  
  ALTER TABLE `ims_xm_mallv3_incomelog`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_incomelog`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_tongue')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_tongue` (
    `id` int(10) unsigned NOT NULL COMMENT '配置ID',
    `weid` int(11) NOT NULL,
    `uid` int(11) NOT NULL,
    `price` decimal(9,2) DEFAULT '0.00' COMMENT '价格',
    `is_pay` tinyint(1) DEFAULT '0' COMMENT '是否已支付',
    `img` varchar(255) NOT NULL,
    `bodydata` text COMMENT '配置值',
    `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态，1启用，0禁用'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_tongue`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_tongue`
    MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID';");
}

if (!pdo_tableexists('xm_mallv3_store_incomelog')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_store_incomelog` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `sid` int(11) NOT NULL,
    `level` int(11) DEFAULT '0',
    `order_id` int(11) DEFAULT '0',
    `ptype` int(3) NOT NULL COMMENT '流水类型',
    `order_num_alias` varchar(40) DEFAULT '',
    `buyer_id` int(11) NOT NULL COMMENT '下单人的id',
    `income` decimal(9,3) NOT NULL COMMENT '奖金',
    `return_percent` decimal(9,2) DEFAULT '0.00' COMMENT '提成点数',
    `percentremark` varchar(50) DEFAULT '' COMMENT '佣金公式',
    `order_total` decimal(6,2) DEFAULT '0.00' COMMENT '订单总价',
    `pay_time` int(11) DEFAULT '0' COMMENT '下单时间',
    `create_time` int(11) NOT NULL,
    `month_time` varchar(40) DEFAULT '',
    `year_time` varchar(40) DEFAULT '',
    `order_status_id` int(11) DEFAULT '0' COMMENT '订单状态'
  ) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='店铺收入明细';
  
  ALTER TABLE `ims_xm_mallv3_store_incomelog`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_store_incomelog`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=3;");
}

if (!pdo_tableexists('xm_mallv3_platform')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_platform` (
    `id` int(10) unsigned NOT NULL,
    `title` varchar(100) NOT NULL,
    `logo` text,
    `loginbgimg` varchar(200) DEFAULT NULL,
    `endtime` int(10) unsigned NOT NULL,
    `send_account_expire_status` tinyint(1) DEFAULT '1',
    `sort` int(11) DEFAULT '100',
    `create_time` int(11) DEFAULT '0',
    `update_time` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

  ALTER TABLE `ims_xm_mallv3_platform`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_platform`
    MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_oss_upload')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_oss_upload` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `code` varchar(20) NOT NULL,
    `title` varchar(30) NOT NULL,
    `settings` text,
    `status` tinyint(1) NOT NULL DEFAULT '0',
    `sort` int(3) DEFAULT '100'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  ALTER TABLE `ims_xm_mallv3_oss_upload`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_oss_upload`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_technical_incomelog')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_technical_incomelog` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `uuid` varchar(68) NOT NULL,
    `level` int(11) DEFAULT '0',
    `order_id` int(11) DEFAULT '0',
    `ptype` int(3) NOT NULL COMMENT '流水类型',
    `order_num_alias` varchar(40) DEFAULT '',
    `buyer_id` int(11) NOT NULL COMMENT '下单人的id',
    `income` decimal(9,3) NOT NULL COMMENT '奖金',
    `return_percent` decimal(9,2) DEFAULT '0.00' COMMENT '提成点数',
    `percentremark` varchar(50) DEFAULT '' COMMENT '佣金公式',
    `order_total` decimal(6,2) DEFAULT '0.00' COMMENT '订单总价',
    `pay_time` int(11) DEFAULT '0' COMMENT '下单时间',
    `create_time` int(11) NOT NULL,
    `month_time` varchar(40) DEFAULT '',
    `year_time` varchar(40) DEFAULT '',
    `order_status_id` int(11) DEFAULT '0' COMMENT '订单状态'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='师傅收入明细';

  ALTER TABLE `ims_xm_mallv3_technical_incomelog`
    ADD PRIMARY KEY (`id`);

  ALTER TABLE `ims_xm_mallv3_technical_incomelog`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_agreement')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_agreement` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `code` varchar(20) NOT NULL,
    `name` varchar(30) NOT NULL,
    `title` varchar(30) NOT NULL,
    `content` text,
    `status` tinyint(1) NOT NULL DEFAULT '0',
    `sort` int(3) DEFAULT '100'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='使用协议表';
  
  ALTER TABLE `ims_xm_mallv3_agreement`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_agreement`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_broadcast')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_broadcast` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `username` varchar(100) NOT NULL DEFAULT '',
    `touxiang` varchar(255) DEFAULT NULL,
    `content` varchar(100) NOT NULL DEFAULT '' COMMENT '内容',
    `ptype` varchar(20) DEFAULT NULL COMMENT '类型',
    `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 -1删除 0默认',
    `create_date` varchar(20) DEFAULT '',
    `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_broadcast`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_broadcast`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_order_remind')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_order_remind` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `order_id` int(11) DEFAULT NULL,
    `content` varchar(100) NOT NULL DEFAULT '' COMMENT '内容',
    `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_order_remind`
    ADD PRIMARY KEY (`id`);

  ALTER TABLE `ims_xm_mallv3_order_remind`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_viporder')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_viporder` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `sid` int(11) DEFAULT '0' COMMENT '店ID',
    `order_num_alias` varchar(40) DEFAULT NULL COMMENT '订单编号',
    `pay_subject` varchar(255) DEFAULT NULL,
    `uid` int(11) NOT NULL DEFAULT '0',
    `gid` int(11) NOT NULL COMMENT '会员等级id',
    `return_points` int(11) DEFAULT '0' COMMENT '可得积分',
    `pay_points` int(11) DEFAULT '0' COMMENT '兑换需要的积分',
    `points_price` decimal(15,2) DEFAULT '0.00' COMMENT '积分可以抵扣金额',
    `points_order` int(11) DEFAULT '0' COMMENT '是否是积分兑换订单',
    `name` varchar(32) DEFAULT NULL COMMENT '购买的会员名字',
    `email` varchar(64) DEFAULT NULL,
    `tel` varchar(64) DEFAULT NULL,
    `pay_method_id` int(11) NOT NULL DEFAULT '1',
    `remark` text COMMENT '订单备注',
    `total` decimal(15,2) NOT NULL DEFAULT '0.00',
    `ip` varchar(40) DEFAULT NULL,
    `user_agent` text COMMENT '用户系统信息',
    `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
    `begin_time` int(11) DEFAULT '0' COMMENT '开始时间',
    `end_time` int(11) DEFAULT '0' COMMENT '结束时间',
    `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
    `pay_time` int(11) DEFAULT '0' COMMENT '付款时间'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_viporder`
    ADD PRIMARY KEY (`id`);

  ALTER TABLE `ims_xm_mallv3_viporder`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_tuan_goods')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_tuan_goods` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `title` varchar(100) DEFAULT NULL COMMENT '标题',
    `ocid` int(11) DEFAULT '0',
    `goods_id` int(11) NOT NULL,
    `people_num` int(6) NOT NULL COMMENT '成团人数',
    `robot_num` int(6) DEFAULT '0' COMMENT '凑数机器人',
    `time_limit` int(11) DEFAULT '0' COMMENT '成团时效',
    `price` decimal(15,2) NOT NULL COMMENT '秒杀价格',
    `end_date` int(11) DEFAULT '0' COMMENT '结束时间',
    `begin_date` int(11) NOT NULL COMMENT '开始时间',
    `buy_max` int(11) DEFAULT '0' COMMENT '限购数量，0=不限购',
    `buy_limit` int(11) unsigned DEFAULT '0' COMMENT '限单',
    `is_level` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否享受会员折扣 0-不享受 1--享受',
    `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
    `update_time` int(11) DEFAULT '0',
    `sort` int(11) DEFAULT '60',
    `status` tinyint(1) DEFAULT '1' COMMENT '状态'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_tuan_goods`
    ADD PRIMARY KEY (`id`),
    ADD KEY `goods_id` (`goods_id`),
    ADD KEY `weid` (`weid`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_tuan_goods`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_bargain_goods')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_bargain_goods` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `title` varchar(100) DEFAULT NULL COMMENT '标题',
    `ocid` int(11) DEFAULT '0',
    `goods_id` int(11) NOT NULL,
    `bargain_price` decimal(15,2) NOT NULL COMMENT '秒杀价格',
    `payment_where` tinyint(1) NOT NULL DEFAULT '1' COMMENT '购买条件:1.砍到指定底价才可购买，2.任意金额可购买',
    `knife_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '每刀金额：1.随机金额,2.随机金额',
    `min_knife_price` decimal(15,2) DEFAULT '0.00' COMMENT '最小金额',
    `max_knife_price` decimal(15,2) DEFAULT '0.00' COMMENT '最大金额',
    `fixed_knife_price` decimal(15,2) DEFAULT '0.00' COMMENT '固定金额',
    `time_limit` int(11) DEFAULT '0' COMMENT '时效',
    `end_date` int(11) DEFAULT '0' COMMENT '结束时间',
    `begin_date` int(11) NOT NULL COMMENT '开始时间',
    `buy_max` int(11) DEFAULT '0' COMMENT '限购数量，0=不限购',
    `buy_limit` int(11) unsigned DEFAULT '0' COMMENT '限单',
    `is_level` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否享受会员折扣 0-不享受 1--享受',
    `create_time` int(11) DEFAULT '0' COMMENT '创建时间',
    `update_time` int(11) DEFAULT '0',
    `sort` int(11) DEFAULT '60',
    `status` tinyint(1) DEFAULT '1' COMMENT '状态'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_bargain_goods`
    ADD PRIMARY KEY (`id`),
    ADD KEY `goods_id` (`goods_id`),
    ADD KEY `weid` (`weid`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_bargain_goods`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_bargain_goods_sku_value')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_bargain_goods_sku_value` (
    `id` int(11) NOT NULL,
    `bargain_id` int(11) NOT NULL,
    `goods_id` int(11) NOT NULL DEFAULT '0',
    `sku` text,
    `image` varchar(255) DEFAULT NULL,
    `quantity` int(11) DEFAULT '0',
    `price` decimal(15,2) NOT NULL DEFAULT '0.00'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_bargain_goods_sku_value`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_bargain_goods_sku_value`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_forhelp')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_forhelp` (
    `id` int(11) NOT NULL,
    `uuid` varchar(68) DEFAULT '',
    `weid` int(11) NOT NULL,
    `sid` int(11) DEFAULT '0',
    `ocid` int(11) DEFAULT '0',
    `uid` int(11) DEFAULT NULL,
    `username` varchar(100) DEFAULT NULL,
    `tel` varchar(20) DEFAULT '' COMMENT '手机号',
    `title` varchar(40) DEFAULT NULL,
    `content` varchar(200) DEFAULT NULL,
    `province_name` varchar(30) DEFAULT NULL,
    `city_name` varchar(30) DEFAULT NULL,
    `district_name` varchar(30) DEFAULT NULL,
    `region_name` varchar(100) DEFAULT NULL,
    `longitude` varchar(30) DEFAULT NULL,
    `latitude` varchar(30) DEFAULT NULL,
    `dizhi` text COMMENT '地址',
    `create_time` int(11) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='紧急求助';

  ALTER TABLE `ims_xm_mallv3_forhelp`
    ADD PRIMARY KEY (`id`);

  ALTER TABLE `ims_xm_mallv3_forhelp`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_order_image')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_order_image` (
    `id` int(11) NOT NULL,
    `weid` int(11) DEFAULT '0',
    `order_id` int(11) NOT NULL DEFAULT '0',
    `image` varchar(255) DEFAULT NULL,
    `sort` int(3) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='商品图片表';

  ALTER TABLE `ims_xm_mallv3_order_image`
    ADD PRIMARY KEY (`id`);

  ALTER TABLE `ims_xm_mallv3_order_image`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_operatingcity_incomelog')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_operatingcity_incomelog` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `ocid` int(11) NOT NULL,
    `level` int(11) DEFAULT '0',
    `order_id` int(11) DEFAULT '0',
    `ptype` int(3) NOT NULL COMMENT '流水类型',
    `order_num_alias` varchar(40) DEFAULT '',
    `buyer_id` int(11) NOT NULL COMMENT '下单人的id',
    `income` decimal(9,3) NOT NULL COMMENT '奖金',
    `return_percent` decimal(9,2) DEFAULT '0.00' COMMENT '提成点数',
    `percentremark` varchar(50) DEFAULT '' COMMENT '佣金公式',
    `order_total` decimal(6,2) DEFAULT '0.00' COMMENT '订单总价',
    `pay_time` int(11) DEFAULT '0' COMMENT '下单时间',
    `create_time` int(11) NOT NULL,
    `month_time` varchar(40) DEFAULT '',
    `year_time` varchar(40) DEFAULT '',
    `order_status_id` int(11) DEFAULT '0' COMMENT '订单状态'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='城市代理收入明细';

  ALTER TABLE `ims_xm_mallv3_operatingcity_incomelog`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_operatingcity_incomelog`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_order_address')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_order_address` (
    `id` int(11) NOT NULL,
    `order_id` int(11) NOT NULL DEFAULT '0',
    `ptype` tinyint(1) DEFAULT '1' COMMENT '1:服务,上门地址2:取件地址',
    `name` varchar(20) DEFAULT NULL,
    `telephone` varchar(20) DEFAULT NULL,
    `region_name` varchar(60) DEFAULT '',
    `province_name` varchar(30) DEFAULT '',
    `city_name` varchar(30) DEFAULT '',
    `district_name` varchar(30) DEFAULT '',
    `address` varchar(128) DEFAULT NULL COMMENT '地址',
    `longitude` varchar(30) DEFAULT '',
    `latitude` varchar(30) DEFAULT ''
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单地址';
  
  ALTER TABLE `ims_xm_mallv3_order_address`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_order_address`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if (!pdo_tableexists('xm_mallv3_miaosha_goods_sku_value')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_miaosha_goods_sku_value` (
    `id` int(11) NOT NULL,
    `ms_id` int(11) NOT NULL,
    `goods_id` int(11) NOT NULL DEFAULT '0',
    `sku` text,
    `image` varchar(255) DEFAULT NULL,
    `quantity` int(11) DEFAULT '0',
    `price` decimal(15,2) NOT NULL DEFAULT '0.00'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_miaosha_goods_sku_value`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_miaosha_goods_sku_value`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if (!pdo_tableexists('xm_mallv3_tuan_follow')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_tuan_follow` (
    `id` int(11) unsigned NOT NULL,
    `uid` int(11) DEFAULT '0' COMMENT '参团会员id',
    `nickname` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '参团会员昵称',
    `avatar` varchar(255) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '会员头像',
    `join_time` int(11) DEFAULT '0' COMMENT '参团时间',
    `order_id` int(11) DEFAULT '0' COMMENT '订单id',
    `found_id` int(10) DEFAULT '0' COMMENT '开团ID',
    `tuan_id` int(10) DEFAULT '0' COMMENT '拼团活动id',
    `is_head` tinyint(1) DEFAULT '0' COMMENT '参团身份:0团员,1团长',
    `is_robot` tinyint(1) DEFAULT '0' COMMENT '机器人0不是1是',
    `pay_time` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0' COMMENT '状态0待成团1成团成功2成团失败',
    `tuan_end_time` int(10) unsigned DEFAULT '0' COMMENT '团结束时间'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_tuan_follow`
    ADD PRIMARY KEY (`id`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_tuan_follow`
    MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}
if (!pdo_tableexists('xm_mallv3_tuan_found')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_tuan_found` (
    `id` int(10) unsigned NOT NULL,
    `weid` int(11) NOT NULL,
    `sid` int(11) DEFAULT '0',
    `ocid` int(11) DEFAULT '0' COMMENT '城市id',
    `sn` varchar(20) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '拼团编号',
    `found_time` int(11) DEFAULT '0' COMMENT '开团时间',
    `found_end_time` int(11) DEFAULT '0' COMMENT '成团截止时间',
    `uid` int(11) DEFAULT '0' COMMENT '团长id',
    `tuan_id` int(10) DEFAULT '0' COMMENT '拼团id',
    `nickname` varchar(100) CHARACTER SET utf8mb4 DEFAULT NULL COMMENT '团长用户名昵称',
    `avatar` varchar(255) CHARACTER SET utf8mb4 DEFAULT '' COMMENT '团长头像',
    `join` int(8) DEFAULT '0' COMMENT '已参团人数',
    `need` int(8) DEFAULT '0' COMMENT '需多少人成团',
    `pay_time` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0' COMMENT '0待成团1拼团成功2拼团失败',
    `tuan_end_time` int(10) unsigned DEFAULT '0' COMMENT '团结束时间'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  ALTER TABLE `ims_xm_mallv3_tuan_found`
    ADD PRIMARY KEY (`id`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_tuan_found`
    MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;");
}
if (!pdo_tableexists('xm_mallv3_tuan_goods_sku_value')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_tuan_goods_sku_value` (
    `id` int(11) NOT NULL,
    `tuan_id` int(11) NOT NULL,
    `goods_id` int(11) NOT NULL DEFAULT '0',
    `sku` text,
    `image` varchar(255) DEFAULT NULL,
    `quantity` int(11) DEFAULT '0',
    `price` decimal(15,2) NOT NULL DEFAULT '0.00'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_tuan_goods_sku_value`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_tuan_goods_sku_value`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_agent_teama_ward')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_agent_teama_ward` (
    `id` int(10) unsigned NOT NULL COMMENT 'ID',
    `weid` int(11) NOT NULL,
    `beginamount` decimal(15,2) DEFAULT NULL COMMENT '业绩开始',
    `endamount` decimal(15,2) DEFAULT NULL COMMENT '最高业绩',
    `beginnumber` int(11) DEFAULT NULL COMMENT '开始人数',
    `endnumber` int(11) DEFAULT NULL COMMENT '最高人数',
    `return_percent` decimal(6,2) NOT NULL DEFAULT '0.00' COMMENT '佣金比例',
    `sort` int(6) unsigned DEFAULT '0' COMMENT '排序',
    `create_time` int(11) NOT NULL DEFAULT '0',
    `update_time` int(10) DEFAULT '0',
    `status` tinyint(1) DEFAULT '1'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='团队奖';
  
  ALTER TABLE `ims_xm_mallv3_agent_teama_ward`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_agent_teama_ward`
    MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID';");
}

if (!pdo_tableexists('xm_mallv3_miaosha_time')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_miaosha_time` (
    `id` int(10) unsigned NOT NULL COMMENT 'ID',
    `weid` int(11) NOT NULL,
    `begin_time` varchar(10) NOT NULL COMMENT '天始时间',
    `end_time` varchar(10) NOT NULL COMMENT '结速时间',
    `sort` int(6) unsigned DEFAULT '0' COMMENT '排序',
    `create_time` int(11) NOT NULL DEFAULT '0',
    `update_time` int(10) DEFAULT '0',
    `status` tinyint(1) DEFAULT '1'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='秒杀时间段';
  
  ALTER TABLE `ims_xm_mallv3_miaosha_time`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_miaosha_time`
    MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID';");
}

if (!pdo_tableexists('xm_mallv3_partner')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_partner` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `total_income` decimal(9,2) DEFAULT '0.00' COMMENT '总收入',
    `income` decimal(9,2) DEFAULT '0.00' COMMENT '收入',
    `cash` decimal(9,3) DEFAULT '0.000' COMMENT '已经提现的',
    `no_cash` decimal(9,3) DEFAULT '0.000' COMMENT '未提现',
    `uid` int(11) DEFAULT '0',
    `title` varchar(40) DEFAULT NULL,
    `tel` varchar(40) DEFAULT '',
    `touxiang` varchar(200) DEFAULT '',
    `email` varchar(40) DEFAULT '',
    `id_card` varchar(64) DEFAULT '',
    `level` int(11) DEFAULT '0',
    `customtext` text,
    `create_time` int(11) DEFAULT NULL,
    `sort` int(11) DEFAULT '100',
    `status` int(11) NOT NULL DEFAULT '1'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='合伙人';
  
  ALTER TABLE `ims_xm_mallv3_partner`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_partner`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_partner_incomelog')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_partner_incomelog` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `uid` int(11) NOT NULL,
    `level` int(11) DEFAULT '0',
    `order_id` int(11) DEFAULT '0',
    `ptype` int(3) NOT NULL COMMENT '流水类型',
    `order_num_alias` varchar(40) DEFAULT '',
    `buyer_id` int(11) NOT NULL COMMENT '下单人的id',
    `income` decimal(9,3) NOT NULL COMMENT '奖金',
    `return_percent` decimal(9,2) DEFAULT '0.00' COMMENT '提成点数',
    `percentremark` varchar(50) DEFAULT '' COMMENT '佣金公式',
    `description` varchar(200) DEFAULT NULL,
    `order_total` decimal(6,2) DEFAULT '0.00' COMMENT '订单总价',
    `pay_time` int(11) DEFAULT '0' COMMENT '下单时间',
    `create_time` int(11) NOT NULL,
    `month_time` varchar(40) DEFAULT '',
    `year_time` varchar(40) DEFAULT '',
    `order_status_id` int(11) DEFAULT '0' COMMENT '订单状态'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='合伙人收入明细';
  
  ALTER TABLE `ims_xm_mallv3_partner_incomelog`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_partner_incomelog`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_partner_level')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_partner_level` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `name` varchar(40) DEFAULT '' COMMENT '等级名称',
    `discount` decimal(10,2) DEFAULT '0.00' COMMENT '折扣',
    `return_percent` decimal(9,2) DEFAULT '0.00' COMMENT '分红比例',
    `is_buytotal` tinyint(1) DEFAULT '0' COMMENT '是否按购买总额分红',
    `is_average` tinyint(1) DEFAULT '0' COMMENT '除以同级的人数',
    `upgrademoney` decimal(15,2) DEFAULT '0.00',
    `upgrade_pullnew` int(11) DEFAULT '0',
    `sort` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_partner_level`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_partner_level`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_tuanzhang')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_tuanzhang` (
    `id` int(11) NOT NULL,
    `uuid` varchar(68) DEFAULT '',
    `weid` int(11) NOT NULL,
    `ocid` int(11) DEFAULT '0',
    `uid` int(11) DEFAULT NULL,
    `answer` varchar(255) DEFAULT '' COMMENT '留言',
    `tel` varchar(20) DEFAULT '' COMMENT '手机号',
    `title` varchar(40) DEFAULT NULL,
    `level` int(3) DEFAULT '0' COMMENT '等级',
    `touxiang` varchar(200) DEFAULT NULL COMMENT '头像',
    `service_times` int(11) DEFAULT '0' COMMENT '服务次数',
    `cate_ids` varchar(200) DEFAULT '' COMMENT '可接服务分类id',
    `total_income` decimal(9,2) DEFAULT '0.00' COMMENT '总收入',
    `income` decimal(9,2) DEFAULT '0.00' COMMENT '收入',
    `points` int(11) DEFAULT '0' COMMENT '积分',
    `email` varchar(40) DEFAULT '',
    `introduction` text COMMENT '简介',
    `id_cart` varchar(64) DEFAULT '' COMMENT '身份证',
    `province_name` varchar(30) DEFAULT NULL,
    `city_name` varchar(30) DEFAULT NULL,
    `district_name` varchar(30) DEFAULT NULL,
    `region_name` varchar(100) DEFAULT NULL,
    `longitude` varchar(30) DEFAULT NULL,
    `latitude` varchar(30) DEFAULT NULL,
    `dizhi` text COMMENT '地址',
    `house_number` varchar(100) DEFAULT NULL,
    `customtext` text,
    `create_time` int(11) DEFAULT '0',
    `is_business` tinyint(1) DEFAULT '1' COMMENT '是否营业',
    `sort` int(11) DEFAULT '100',
    `status` tinyint(1) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='团长';
  
  ALTER TABLE `ims_xm_mallv3_tuanzhang`
    ADD PRIMARY KEY (`id`);

  ALTER TABLE `ims_xm_mallv3_tuanzhang`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_tuanzhang_incomelog')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_tuanzhang_incomelog` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `uuid` varchar(68) NOT NULL,
    `level` int(11) DEFAULT '0',
    `order_id` int(11) DEFAULT '0',
    `ptype` int(3) NOT NULL COMMENT '流水类型',
    `order_num_alias` varchar(40) DEFAULT '',
    `buyer_id` int(11) NOT NULL COMMENT '下单人的id',
    `income` decimal(9,3) NOT NULL COMMENT '奖金',
    `return_percent` decimal(9,2) DEFAULT '0.00' COMMENT '提成点数',
    `percentremark` varchar(50) DEFAULT '' COMMENT '佣金公式',
    `description` varchar(200) DEFAULT NULL,
    `order_total` decimal(6,2) DEFAULT '0.00' COMMENT '订单总价',
    `pay_time` int(11) DEFAULT '0' COMMENT '下单时间',
    `create_time` int(11) NOT NULL,
    `month_time` varchar(40) DEFAULT '',
    `year_time` varchar(40) DEFAULT '',
    `order_status_id` int(11) DEFAULT '0' COMMENT '订单状态'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='团长收入明细';
  
  ALTER TABLE `ims_xm_mallv3_tuanzhang_incomelog`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_tuanzhang_incomelog`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_tuanzhang_level')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_tuanzhang_level` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `title` varchar(40) DEFAULT '' COMMENT '等级名称',
    `return_percent` decimal(9,2) DEFAULT '0.00' COMMENT '返佣比例',
    `sort` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_tuanzhang_level`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_tuanzhang_level`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_bottom_menu_type')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_bottom_menu_type` (
    `id` int(11) unsigned NOT NULL,
    `title` varchar(255) DEFAULT '' COMMENT '名称',
    `weid` int(11) unsigned NOT NULL,
    `sort` int(11) NOT NULL DEFAULT '100' COMMENT '排序 升序',
    `create_time` int(11) unsigned NOT NULL DEFAULT '0',
    `update_time` int(11) DEFAULT '0',
    `is_delete` smallint(1) unsigned NOT NULL DEFAULT '0',
    `status` tinyint(1) DEFAULT '0' COMMENT '状态'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='底部菜单分类';
  
  ALTER TABLE `ims_xm_mallv3_bottom_menu_type`
    ADD PRIMARY KEY (`id`),
    ADD KEY `is_delete` (`is_delete`);
  
  ALTER TABLE `ims_xm_mallv3_bottom_menu_type`
    MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_authorization')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_authorization` (
    `id` int(2) unsigned NOT NULL COMMENT '配置ID',
    `seed` varchar(100) NOT NULL,
    `secret` varchar(100) DEFAULT '',
    `ip` varchar(30) DEFAULT NULL,
    `domainname` varchar(100) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_authorization`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_authorization`
    MODIFY `id` int(2) unsigned NOT NULL AUTO_INCREMENT COMMENT '配置ID';");
}

if (!pdo_tableexists('xm_mallv3_hospital')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_hospital` (
    `id` int(11) NOT NULL,
    `uuid` varchar(68) DEFAULT '',
    `weid` int(11) NOT NULL,
    `uid` int(11) DEFAULT '0',
    `ocid` int(11) DEFAULT '0',
    `cid` int(10) DEFAULT '0' COMMENT '分类',
    `level` int(11) DEFAULT '0',
    `title` varchar(100) DEFAULT NULL COMMENT '名称',
    `image` varchar(200) DEFAULT NULL,
    `income` decimal(9,2) DEFAULT '0.00',
    `total_income` decimal(9,2) DEFAULT '0.00',
    `cate_ids` varchar(200) DEFAULT '' COMMENT '可接的服务',
    `latitude` varchar(30) DEFAULT '' COMMENT '纬度',
    `longitude` varchar(30) DEFAULT '' COMMENT '经度',
    `region_name` varchar(100) DEFAULT NULL COMMENT '地址名称',
    `province_name` varchar(30) DEFAULT '' COMMENT '省名称',
    `city_name` varchar(30) DEFAULT '' COMMENT '市名称',
    `district_name` varchar(30) DEFAULT '' COMMENT '区名称',
    `community_name` varchar(30) DEFAULT '' COMMENT '镇街名称',
    `address` varchar(255) DEFAULT '' COMMENT '地址',
    `house_number` varchar(100) DEFAULT '' COMMENT '门牌号',
    `tel` varchar(60) DEFAULT '' COMMENT '联系电话',
    `praise` int(8) unsigned DEFAULT '0' COMMENT '好评',
    `viewed` int(11) DEFAULT '0' COMMENT '点击量',
    `domain` varchar(100) DEFAULT NULL COMMENT '范围',
    `focuson` text COMMENT '重点科室',
    `certification` varchar(255) DEFAULT NULL COMMENT '认证',
    `sort` int(10) unsigned DEFAULT '0' COMMENT '排序',
    `recommended` tinyint(2) DEFAULT '0' COMMENT '推荐',
    `theme` varchar(60) DEFAULT '' COMMENT '主题风格',
    `slogan` varchar(100) DEFAULT '' COMMENT '标语',
    `description` varchar(255) DEFAULT NULL COMMENT '描述',
    `content` text COMMENT '内容',
    `customtext` text,
    `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
    `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
    `end_time` int(10) unsigned DEFAULT '0' COMMENT '结束时间(0为永久)',
    `status` tinyint(1) DEFAULT '1' COMMENT '状态'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;
  
  ALTER TABLE `ims_xm_mallv3_hospital`
    ADD PRIMARY KEY (`id`),
    ADD KEY `store_name` (`title`),
    ADD KEY `domain` (`domain`),
    ADD KEY `id` (`id`);
  
  ALTER TABLE `ims_xm_mallv3_hospital`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_hospital_cate')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_hospital_cate` (
    `id` int(10) unsigned NOT NULL COMMENT 'ID',
    `weid` int(11) NOT NULL,
    `title` varchar(50) DEFAULT NULL COMMENT '标题',
    `return_percent` decimal(9,2) DEFAULT '0.00',
    `sort` int(10) unsigned DEFAULT '0' COMMENT '排序',
    `create_time` int(11) NOT NULL DEFAULT '0',
    `update_time` int(10) DEFAULT '0',
    `status` tinyint(1) DEFAULT '1'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='医院类别';
  
  ALTER TABLE `ims_xm_mallv3_hospital_cate`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_hospital_cate`
    MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID';");
}

if (!pdo_tableexists('xm_mallv3_hospital_departments')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_hospital_departments` (
    `id` int(10) unsigned NOT NULL COMMENT 'ID',
    `weid` int(11) NOT NULL,
    `pid` int(10) unsigned DEFAULT '0' COMMENT '上级分类ID',
    `title` varchar(50) DEFAULT '' COMMENT '标题',
    `image` varchar(100) DEFAULT NULL,
    `sort` int(11) DEFAULT '60',
    `create_time` int(11) NOT NULL DEFAULT '0',
    `update_time` int(10) DEFAULT '0',
    `status` tinyint(1) DEFAULT '1'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='医院科室';
  
  ALTER TABLE `ims_xm_mallv3_hospital_departments`
    ADD PRIMARY KEY (`id`),
    ADD KEY `pid` (`pid`);
  
  ALTER TABLE `ims_xm_mallv3_hospital_departments`
    MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID';");
}

if (!pdo_tableexists('xm_mallv3_hospital_image')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_hospital_image` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `hid` int(11) DEFAULT '0',
    `ptype` varchar(20) DEFAULT '' COMMENT '类别',
    `image` varchar(255) DEFAULT NULL,
    `sort` int(3) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='医院图片表';
  
  ALTER TABLE `ims_xm_mallv3_hospital_image`
    ADD PRIMARY KEY (`id`),
    ADD KEY `sid` (`hid`);
  
  ALTER TABLE `ims_xm_mallv3_hospital_image`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_hospital_incomelog')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_hospital_incomelog` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `hid` int(11) NOT NULL,
    `level` int(11) DEFAULT '0',
    `order_id` int(11) DEFAULT '0',
    `ptype` int(3) NOT NULL COMMENT '流水类型',
    `order_num_alias` varchar(40) DEFAULT '',
    `buyer_id` int(11) NOT NULL COMMENT '下单人的id',
    `income` decimal(9,3) NOT NULL COMMENT '奖金',
    `return_percent` decimal(9,2) DEFAULT '0.00' COMMENT '提成点数',
    `percentremark` varchar(50) DEFAULT '' COMMENT '佣金公式',
    `description` varchar(200) DEFAULT NULL,
    `order_total` decimal(6,2) DEFAULT '0.00' COMMENT '订单总价',
    `pay_time` int(11) DEFAULT '0' COMMENT '下单时间',
    `create_time` int(11) NOT NULL,
    `month_time` varchar(40) DEFAULT '',
    `year_time` varchar(40) DEFAULT '',
    `order_status_id` int(11) DEFAULT '0' COMMENT '订单状态'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收入明细';
  
  ALTER TABLE `ims_xm_mallv3_hospital_incomelog`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_hospital_incomelog`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_hospital_level')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_hospital_level` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `title` varchar(40) DEFAULT '' COMMENT '等级名称',
    `sort` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_hospital_level`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_hospital_level`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_technical_fans')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_technical_fans` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `tid` int(11) NOT NULL,
    `uid` int(11) NOT NULL,
    `create_time` int(11) NOT NULL,
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_technical_fans`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_technical_fans`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_rotarytable_prize')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_rotarytable_prize` (
    `id` int(10) unsigned NOT NULL,
    `weid` int(11) NOT NULL,
    `rid` int(11) NOT NULL COMMENT '活动id',
    `ptype` varchar(20) NOT NULL,
    `title` varchar(100) DEFAULT '',
    `image` varchar(200) DEFAULT '',
    `quantity` int(11) NOT NULL COMMENT '数量',
    `price` decimal(15,2) DEFAULT '0.00',
    `points` int(11) DEFAULT '0' COMMENT '积分',
    `goods_id` int(11) DEFAULT '0' COMMENT '商品id',
    `coupon_id` int(11) DEFAULT '0' COMMENT '优惠券id',
    `probability` int(11) DEFAULT '0' COMMENT '中奖概率',
    `sort` int(11) DEFAULT '20',
    `create_time` int(11) NOT NULL,
    `update_time` int(11) NOT NULL,
    `status` tinyint(1) DEFAULT '0' COMMENT '0待成团1拼团成功2拼团失败'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_rotarytable_prize`
    ADD PRIMARY KEY (`id`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_rotarytable_prize`
    MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_live_anchor')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_live_anchor` (
    `id` int(10) NOT NULL COMMENT '自增ID',
    `weid` int(11) NOT NULL,
    `name` varchar(50) NOT NULL DEFAULT '' COMMENT '主播名称',
    `cover_img` varchar(255) NOT NULL DEFAULT '' COMMENT '主播图像',
    `wechat` varchar(50) NOT NULL DEFAULT '' COMMENT '主播微信号',
    `phone` varchar(32) NOT NULL DEFAULT '' COMMENT '手机号',
    `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
    `sort` int(11) DEFAULT '0',
    `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
    `update_time` int(11) DEFAULT '0',
    `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='直播主播表';
  
  ALTER TABLE `ims_xm_mallv3_live_anchor`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_live_anchor`
    MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID';");
}

if (!pdo_tableexists('xm_mallv3_live_goods')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_live_goods` (
    `id` int(10) NOT NULL COMMENT '自增ID',
    `weid` int(11) NOT NULL,
    `goods_id` int(10) NOT NULL DEFAULT '0' COMMENT '商品ID',
    `title` varchar(30) NOT NULL DEFAULT '' COMMENT '商品名称',
    `price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '一口价/最低价',
    `buy_max` int(11) DEFAULT '0' COMMENT '限购数量，0=不限购',
    `buy_limit` int(11) DEFAULT '0' COMMENT '限单',
    `sale_count` int(11) DEFAULT '1',
    `sort` smallint(5) NOT NULL DEFAULT '0' COMMENT '排序',
    `is_show` tinyint(1) NOT NULL DEFAULT '1' COMMENT '是否显示',
    `is_del` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
    `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
    `update_time` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='直播商品表';

  ALTER TABLE `ims_xm_mallv3_live_goods`
    ADD PRIMARY KEY (`id`);

  ALTER TABLE `ims_xm_mallv3_live_goods`
    MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增ID';");
}

if (!pdo_tableexists('xm_mallv3_live_room')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_live_room` (
    `id` int(10) unsigned NOT NULL COMMENT '自增ID',
    `weid` int(11) NOT NULL,
    `room_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '直播间 id',
    `name` varchar(32) NOT NULL DEFAULT '' COMMENT '直播间名字',
    `cover_img` varchar(255) NOT NULL DEFAULT '' COMMENT '背景图',
    `share_img` varchar(255) NOT NULL DEFAULT '' COMMENT '分享图',
    `start_time` int(10) NOT NULL DEFAULT '0' COMMENT '直播计划开始时间',
    `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '直播计划结束时间',
    `anchor_id` int(11) DEFAULT '0',
    `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '直播间类型 【1: 推流，0：手机直播】',
    `screen_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '横屏、竖屏 【1：横屏，0：竖屏】',
    `close_like` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否关闭点赞',
    `close_goods` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否关闭货架',
    `close_comment` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否关闭评论',
    `error_msg` varchar(255) NOT NULL DEFAULT '' COMMENT '未通过原因',
    `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '审核状态0=未审核1=微信审核2=审核通过-1=审核未通过',
    `live_status` smallint(5) unsigned NOT NULL DEFAULT '102' COMMENT '直播状态101：直播中，102：未开始，103已结束，104禁播，105：暂停，106：异常，107：已过期',
    `mark` varchar(512) NOT NULL DEFAULT '' COMMENT '备注',
    `replay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '回放状态',
    `sort` smallint(5) NOT NULL DEFAULT '0' COMMENT '排序',
    `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否显示',
    `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
    `create_time` int(10) NOT NULL DEFAULT '0' COMMENT '添加时间',
    `update_time` int(11) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='直播间表';

  ALTER TABLE `ims_xm_mallv3_live_room`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_live_room`
    MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID';");
}

if (!pdo_tableexists('xm_mallv3_live_room_goods')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_live_room_goods` (
    `live_room_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '直播间id',
    `live_goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品id'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='直播挂车商品';
  
  ALTER TABLE `ims_xm_mallv3_live_room_goods`
    ADD KEY `broadcast_room_id` (`live_room_id`,`live_goods_id`) USING BTREE;");
}

if (!pdo_tableexists('xm_mallv3_uploadminiprogram')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_uploadminiprogram` (
    `id` int(8) unsigned NOT NULL,
    `weid` int(11) NOT NULL,
    `version` varchar(12) DEFAULT NULL,
    `desctext` varchar(200) DEFAULT NULL,
    `create_time` int(11) NOT NULL,
    `update_time` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_uploadminiprogram`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_uploadminiprogram`
    MODIFY `id` int(8) unsigned NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_order_count')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_order_count` (
    `id` int(11) NOT NULL,
    `ptype` varchar(20) DEFAULT '',
    `uid` int(11) DEFAULT '0',
    `uuid` varchar(68) DEFAULT '',
    `order_id` int(11) NOT NULL DEFAULT '0',
    `order_status_id` int(5) NOT NULL DEFAULT '0',
    `is_read` tinyint(1) DEFAULT '0',
    `create_time` int(11) NOT NULL DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_order_count`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_order_count`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if (!pdo_tableexists('xm_mallv3_domain_replace')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_domain_replace` (
    `id` int(8) unsigned NOT NULL,
    `weid` int(11) NOT NULL,
    `title` varchar(80) DEFAULT '',
    `sourceurl` varchar(200) DEFAULT NULL,
    `replaceurl` varchar(200) DEFAULT NULL,
    `sort` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_domain_replace`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_domain_replace`
    MODIFY `id` int(8) unsigned NOT NULL AUTO_INCREMENT;");
}
if (!pdo_tableexists('xm_mallv3_order_tuanzhang')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_order_tuanzhang` (
    `id` int(11) NOT NULL,
    `uuid` varchar(68) DEFAULT '',
    `order_id` int(11) DEFAULT NULL,
    `uid` int(11) DEFAULT '0',
    `title` varchar(20) DEFAULT NULL COMMENT '团长姓名',
    `verification_time` int(11) DEFAULT '0' COMMENT '核销时间',
    `accept_time` int(11) DEFAULT '0' COMMENT '接收时间',
    `is_complete` tinyint(1) DEFAULT '0',
    `is_confirm` tinyint(1) DEFAULT '0',
    `is_settlement` tinyint(1) DEFAULT '0' COMMENT '结算',
    `create_time` int(11) NOT NULL COMMENT '创建时间'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单关联团长';
  
  ALTER TABLE `ims_xm_mallv3_order_tuanzhang`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_order_tuanzhang`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}
if (!pdo_tableexists('xm_mallv3_files_cate')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_files_cate` (
    `id` int(10) unsigned NOT NULL COMMENT '主键ID',
    `weid` int(11) DEFAULT '0',
    `pid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '父级ID',
    `type` tinyint(2) unsigned NOT NULL DEFAULT '10' COMMENT '类型[10=图片，20=视频，30=文件]',
    `name` varchar(32) NOT NULL DEFAULT '' COMMENT '分类名称',
    `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
    `update_time` int(10) unsigned DEFAULT NULL COMMENT '更新时间',
    `delete_time` int(10) unsigned DEFAULT NULL COMMENT '删除时间',
    `sort` int(11) DEFAULT '100'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件分类表';
  
  ALTER TABLE `ims_xm_mallv3_files_cate`
    ADD PRIMARY KEY (`id`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_files_cate`
    MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID';");
}

if (!pdo_tableexists('xm_mallv3_files')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_files` (
    `id` int(10) unsigned NOT NULL COMMENT '主键ID',
    `weid` int(11) DEFAULT '0',
    `cid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '类目ID',
    `source_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '上传者id',
    `type` varchar(30) DEFAULT NULL,
    `source` tinyint(1) DEFAULT '0' COMMENT '来源类型[0-后台,1-用户]',
    `name` varchar(100) DEFAULT '' COMMENT '文件名称',
    `uri` varchar(200) NOT NULL COMMENT '文件路径',
    `create_time` int(10) unsigned DEFAULT NULL COMMENT '创建时间',
    `update_time` int(10) DEFAULT NULL COMMENT '更新时间',
    `delete_time` int(10) DEFAULT NULL COMMENT '删除时间',
    `sort` int(11) DEFAULT '100'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件表';
  
  ALTER TABLE `ims_xm_mallv3_files`
    ADD PRIMARY KEY (`id`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_files`
    MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '主键ID';");
}

if (!pdo_tableexists('xm_mallv3_goods_time_discount')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_goods_time_discount` (
    `id` int(11) NOT NULL,
    `goods_id` int(11) NOT NULL DEFAULT '0',
    `discount_method` tinyint(1) DEFAULT '0' COMMENT '折扣方式，0.折扣 1.加减金额',
    `begin_time` varchar(10) NOT NULL,
    `end_time` varchar(10) NOT NULL,
    `addsubtract` tinyint(1) DEFAULT '0' COMMENT '0:加1减',
    `price` decimal(15,2) NOT NULL DEFAULT '0.00'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='是间段价格';
  
  ALTER TABLE `ims_xm_mallv3_goods_time_discount`
    ADD PRIMARY KEY (`id`),
    ADD KEY `goods_id` (`goods_id`);
  
  ALTER TABLE `ims_xm_mallv3_goods_time_discount`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_goods_buynowinfo')) {
  pdo_run("CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_goods_buynowinfo` (
    `id` int(11) NOT NULL,
    `weid` int(11) DEFAULT '0',
    `ip` varchar(30) DEFAULT '',
    `data` longtext,
    `expire_time` int(11) DEFAULT '0',
    `status` tinyint(4) DEFAULT '1' COMMENT '状态'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
  
  ALTER TABLE `ims_xm_mallv3_goods_buynowinfo`
    ADD PRIMARY KEY (`id`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_goods_buynowinfo`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
}

if (!pdo_tableexists('xm_mallv3_signin')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_signin` (
    `id` int(11) NOT NULL COMMENT '自增ID',
    `weid` int(11) NOT NULL,
    `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户uid',
    `title` varchar(255) NOT NULL DEFAULT '' COMMENT '签到说明',
    `number` int(11) NOT NULL DEFAULT '0' COMMENT '获得积分',
    `balance` int(11) NOT NULL DEFAULT '0' COMMENT '剩余积分',
    `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
    `update_time` int(11) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签到记录表';
  
  ALTER TABLE `ims_xm_mallv3_signin`
    ADD PRIMARY KEY (`id`) USING BTREE,
    ADD KEY `uid` (`uid`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_signin`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID';
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_signin_config')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_signin_config` (
    `id` int(11) NOT NULL COMMENT '自增ID',
    `weid` int(11) NOT NULL,
    `day` varchar(100) NOT NULL DEFAULT '',
    `number` int(11) NOT NULL DEFAULT '0' COMMENT '获得积分',
    `sort` int(11) DEFAULT '100' COMMENT '排序',
    `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
    `update_time` int(11) DEFAULT NULL,
    `status` tinyint(1) DEFAULT '1'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='签到配置';
  
  ALTER TABLE `ims_xm_mallv3_signin_config`
    ADD PRIMARY KEY (`id`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_signin_config`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '自增ID';
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_rotarytable_log')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_rotarytable_log` (
    `id` int(10) UNSIGNED NOT NULL,
    `weid` int(11) NOT NULL,
    `uid` int(11) NOT NULL,
    `rid` int(11) NOT NULL COMMENT '活动id',
    `ptype` varchar(20) NOT NULL,
    `title` varchar(100) DEFAULT '',
    `image` varchar(200) DEFAULT '',
    `price` decimal(15,2) DEFAULT '0.00',
    `points` int(11) DEFAULT '0' COMMENT '积分',
    `goods_id` int(11) DEFAULT '0' COMMENT '商品id',
    `coupon_id` int(11) DEFAULT '0' COMMENT '优惠券id',
    `create_time` int(11) NOT NULL,
    `update_time` int(11) NOT NULL,
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_rotarytable_log`
    ADD PRIMARY KEY (`id`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_rotarytable_log`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_text_replace')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_text_replace` (
    `id` int(8) UNSIGNED NOT NULL,
    `weid` int(11) NOT NULL,
    `sourcetext` varchar(200) DEFAULT NULL,
    `replacetext` varchar(200) DEFAULT NULL,
    `notes` varchar(200) DEFAULT NULL COMMENT '备注',
    `sort` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='文字替换';
  
  ALTER TABLE `ims_xm_mallv3_text_replace`
    ADD PRIMARY KEY (`id`),
    ADD KEY `sourcetext` (`sourcetext`);
  
  ALTER TABLE `ims_xm_mallv3_text_replace`
    MODIFY `id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_housing_estate')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_housing_estate` (
    `id` int(10) UNSIGNED NOT NULL,
    `weid` int(11) NOT NULL,
    `tzid` int(11) NOT NULL COMMENT '团长id',
    `title` varchar(60) DEFAULT NULL,
    `image` varchar(200) DEFAULT '' COMMENT '缩略图',
    `cate_ids` varchar(200) DEFAULT '' COMMENT '可经营的类目',
    `region_name` varchar(200) DEFAULT NULL COMMENT '位置名称',
    `province_name` varchar(30) DEFAULT '',
    `city_name` varchar(30) DEFAULT '',
    `district_name` varchar(30) DEFAULT '',
    `area_name` varchar(60) DEFAULT NULL COMMENT '区域名',
    `house_number` varchar(200) DEFAULT NULL,
    `longitude` varchar(30) DEFAULT '',
    `latitude` varchar(30) DEFAULT '',
    `tel` varchar(60) DEFAULT '',
    `settings` text COMMENT '配置',
    `create_time` int(11) NOT NULL,
    `update_time` int(11) DEFAULT NULL,
    `is_default` tinyint(1) DEFAULT '0' COMMENT '是否默认',
    `sort` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='小区';
  
  ALTER TABLE `ims_xm_mallv3_housing_estate`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_housing_estate`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_service_time_ptype')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_service_time_ptype` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `title` varchar(40) DEFAULT '' COMMENT '等级名称',
    `sort` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  ALTER TABLE `ims_xm_mallv3_service_time_ptype`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_service_time_ptype`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_member_commission')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_member_commission` (
    `id` int(11) NOT NULL,
    `mgid` int(11) DEFAULT '0' COMMENT '会员组',
    `roletype` varchar(30) DEFAULT '',
    `return_percent` decimal(15,2) NOT NULL DEFAULT '0.00'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='会员分佣';
  
  ALTER TABLE `ims_xm_mallv3_member_commission`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_member_commission`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}
if (!pdo_tableexists('xm_mallv3_member_task')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_member_task` (
    `id` int(10) UNSIGNED NOT NULL,
    `mgid` int(11) NOT NULL,
    `title` varchar(20) DEFAULT NULL,
    `number` int(8) DEFAULT '1' COMMENT '送优惠券数量',
    `coupon_id` int(11) DEFAULT '0' COMMENT '赠送优惠券',
    `goods_id` int(11) DEFAULT '0' COMMENT '完成送商品',
    `points` int(8) DEFAULT '0' COMMENT '赠送得积分',
    `balance` decimal(15,2) DEFAULT NULL COMMENT '完成任务金额',
    `expire_day` int(5) DEFAULT '0' COMMENT '有效期',
    `start_time` int(5) DEFAULT '0' COMMENT '生效时间',
    `description` varchar(80) DEFAULT NULL COMMENT '描述信息',
    `invitation` int(11) DEFAULT NULL COMMENT '直推多少人消费完成',
    `invitation_member` int(11) DEFAULT '0' COMMENT '直推多少人购买会员完成',
    `invitation_member_mgid` int(11) DEFAULT '0' COMMENT '购买会员等级',
    `consumption` decimal(15,2) DEFAULT '0.00' COMMENT '累计消费多少钱完成',
    `complete_where` tinyint(2) DEFAULT '0' COMMENT '完成条件',
    `sort` int(11) DEFAULT '100',
    `status` tinyint(1) DEFAULT '1' COMMENT '用户组状态：为1正常，为0禁用,-1为删除'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_member_task`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_member_task`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_goods_quantity_unit')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_goods_quantity_unit` (
    `id` int(10) NOT NULL,
    `weid` int(11) NOT NULL,
    `ptype` int(3) NOT NULL,
    `title` varchar(40) DEFAULT '',
    `create_time` int(11) NOT NULL DEFAULT '0',
    `update_time` int(10) DEFAULT '0',
    `sort` int(11) DEFAULT '30',
    `status` tinyint(1) DEFAULT '1'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_goods_quantity_unit`
    ADD PRIMARY KEY (`id`);

  ALTER TABLE `ims_xm_mallv3_goods_quantity_unit`
    MODIFY `id` int(10) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}
if (!pdo_tableexists('xm_mallv3_member_wishlist')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_member_wishlist` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `uid` int(11) NOT NULL,
    `goods_id` int(11) NOT NULL DEFAULT '0',
    `ptype` varchar(20) DEFAULT '',
    `title` varchar(200) DEFAULT '',
    `image` varchar(255) DEFAULT '',
    `url` varchar(255) DEFAULT '',
    `create_time` int(11) NOT NULL,
    `update_time` int(11) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收藏';
  
  ALTER TABLE `ims_xm_mallv3_member_wishlist`
    ADD PRIMARY KEY (`id`),
    ADD KEY `uid` (`uid`,`goods_id`);
  

  ALTER TABLE `ims_xm_mallv3_member_wishlist`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}
if (!pdo_tableexists('xm_mallv3_reglike')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_reglike` (
    `id` int(8) UNSIGNED NOT NULL,
    `weid` int(11) NOT NULL,
    `title` varchar(80) DEFAULT '',
    `pic` varchar(200) NOT NULL DEFAULT '',
    `sort` int(11) DEFAULT '0',
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_reglike`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_reglike`
    MODIFY `id` int(8) UNSIGNED NOT NULL AUTO_INCREMENT;
  COMMIT;");
}
if (!pdo_tableexists('xm_mallv3_order_timescard')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_order_timescard` (
    `id` int(11) NOT NULL,
    `order_id` int(11) DEFAULT NULL,
    `timestype` tinyint(1) DEFAULT '0' COMMENT '方式0周约1月约',
    `yue_date` int(11) DEFAULT NULL COMMENT '日期如星期几几号',
    `yue_begin_time` int(11) DEFAULT '0',
    `yue_end_time` int(11) DEFAULT '0',
    `is_settlement` tinyint(1) DEFAULT '0' COMMENT '结算',
    `create_time` int(11) NOT NULL COMMENT '创建时间',
    `update_time` int(11) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='次卡';
  
  ALTER TABLE `ims_xm_mallv3_order_timescard`
    ADD PRIMARY KEY (`id`);

  ALTER TABLE `ims_xm_mallv3_order_timescard`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_order_timescard_record')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_order_timescard_record` (
    `id` int(11) NOT NULL,
    `uuid` varchar(68) DEFAULT '',
    `order_id` int(11) DEFAULT NULL,
    `uid` int(11) DEFAULT '0',
    `title` varchar(20) DEFAULT NULL COMMENT '姓名',
    `yue_begin_time` int(11) DEFAULT '0',
    `yue_end_time` int(11) DEFAULT '0',
    `end_time` int(11) DEFAULT '0' COMMENT '结速时间',
    `begin_time` int(11) DEFAULT '0' COMMENT '开始时间',
    `is_complete` tinyint(1) DEFAULT '0',
    `is_confirm` tinyint(1) DEFAULT '0',
    `is_settlement` tinyint(1) DEFAULT '0' COMMENT '结算',
    `create_time` int(11) NOT NULL COMMENT '创建时间',
    `update_time` int(11) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='次卡使用记录';
  
  ALTER TABLE `ims_xm_mallv3_order_timescard_record`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_order_timescard_record`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_goods_giftcard_type')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_goods_giftcard_type` (
    `id` int(11) UNSIGNED NOT NULL,
    `ptype` tinyint(1) DEFAULT '1',
    `weid` int(11) NOT NULL,
    `ocid` int(11) DEFAULT '0',
    `sid` int(11) DEFAULT '0',
    `name` varchar(255) DEFAULT '' COMMENT '名称',
    `buy_price` decimal(10,2) DEFAULT '0.00' COMMENT '购买价格',
    `facevalue` decimal(15,2) DEFAULT '0.00' COMMENT '面值',
    `points_method` tinyint(1) DEFAULT '0',
    `points` int(11) DEFAULT '0' COMMENT '购买得积分',
    `commission_method` tinyint(1) DEFAULT '0' COMMENT '佣金结算方式',
    `color` varchar(10) DEFAULT '10' COMMENT '颜色',
    `use_goods` tinyint(1) DEFAULT '0' COMMENT '是否适用商品',
    `cat_ids` varchar(200) DEFAULT '' COMMENT '适用品类',
    `goods_ids` text COMMENT '适用产品编号',
    `condition_type` tinyint(1) DEFAULT '0' COMMENT '使用门槛0无门槛1有门槛',
    `min_price` decimal(10,2) UNSIGNED DEFAULT '0.00' COMMENT '最低消费金额',
    `expire_type` tinyint(3) UNSIGNED DEFAULT '10' COMMENT '到期类型(10领取后生效 20固定时间)',
    `expire_day` int(11) UNSIGNED DEFAULT '0' COMMENT '领取后生效-有效天数',
    `start_time` int(11) UNSIGNED DEFAULT '0' COMMENT '固定时间-开始时间',
    `end_time` int(11) UNSIGNED DEFAULT '0' COMMENT '固定时间-结束时间',
    `total_num` int(11) DEFAULT '0' COMMENT '发放总数量(-1为不限制)',
    `receive_num` int(11) UNSIGNED DEFAULT '0' COMMENT '已领取数量',
    `sort` int(11) UNSIGNED DEFAULT '0' COMMENT '排序方式(数字越小越靠前)',
    `is_delete` tinyint(3) UNSIGNED DEFAULT '0' COMMENT '软删除',
    `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
    `update_time` int(11) UNSIGNED DEFAULT '0' COMMENT '更新时间',
    `status` tinyint(1) DEFAULT NULL COMMENT '状态'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='购物卡类型';
  
  ALTER TABLE `ims_xm_mallv3_goods_giftcard_type`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_goods_giftcard_type`
    MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_order_card')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_order_card` (
    `id` int(11) UNSIGNED NOT NULL COMMENT '主键id',
    `ptype` int(3) DEFAULT '1',
    `weid` int(11) NOT NULL,
    `order_id` int(11) DEFAULT '0',
    `ocid` int(11) DEFAULT '0',
    `sid` int(11) DEFAULT '0',
    `uid` int(11) UNSIGNED NOT NULL DEFAULT '0',
    `card_tid` int(11) UNSIGNED NOT NULL COMMENT '购物卡类型id',
    `name` varchar(255) DEFAULT '' COMMENT '名称',
    `image` varchar(200) DEFAULT '',
    `facevalue` decimal(15,2) DEFAULT '0.00' COMMENT '面值',
    `timesmum` int(3) DEFAULT '0' COMMENT '次数',
    `is_timing` tinyint(1) DEFAULT '0' COMMENT '是否定期服务',
    `timing_unit` varchar(60) DEFAULT '' COMMENT '时间单位',
    `color` varchar(10) DEFAULT '' COMMENT '颜色',
    `condition_type` tinyint(1) DEFAULT '0' COMMENT '使用门槛0无门槛1有门槛',
    `use_goods` tinyint(1) DEFAULT '0' COMMENT '适用商品',
    `cat_ids` varchar(200) DEFAULT '' COMMENT '适用品类',
    `goods_ids` text COMMENT '适用产品编号',
    `min_price` decimal(10,2) UNSIGNED DEFAULT '0.00' COMMENT '最低消费金额',
    `expire_type` tinyint(3) UNSIGNED DEFAULT '10' COMMENT '到期类型(10领取后生效 20固定时间)',
    `expire_day` int(11) UNSIGNED DEFAULT '0' COMMENT '领取后生效-有效天数',
    `start_time` int(11) UNSIGNED DEFAULT '0' COMMENT '有效期开始时间',
    `end_time` int(11) UNSIGNED DEFAULT '0' COMMENT '有效期结束时间',
    `is_expire` tinyint(3) UNSIGNED DEFAULT '0' COMMENT '是否过期(0未过期 1已过期)',
    `is_use` tinyint(3) UNSIGNED DEFAULT '0' COMMENT '是否已使用(0未使用 1已使用)',
    `create_time` int(11) UNSIGNED NOT NULL DEFAULT '0' COMMENT '创建时间',
    `update_time` int(11) UNSIGNED DEFAULT '0' COMMENT '更新时间',
    `status` tinyint(1) DEFAULT '1'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='卡';
  
  ALTER TABLE `ims_xm_mallv3_order_card`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_order_card`
    MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键id';
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_technical_certificate')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_technical_certificate` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `title` varchar(64) DEFAULT NULL,
    `status` tinyint(1) NOT NULL DEFAULT '1',
    `sort` int(11) DEFAULT '10'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='师傅认证';
  
  ALTER TABLE `ims_xm_mallv3_technical_certificate`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_technical_certificate`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_goods_giftcard_commission')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_goods_giftcard_commission` (
    `id` int(11) NOT NULL,
    `card_tid` int(11) DEFAULT '0',
    `commission_method` tinyint(1) DEFAULT '0',
    `roletype` varchar(30) DEFAULT '',
    `return_percent` decimal(15,2) NOT NULL DEFAULT '0.00'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='购物卡分佣';
  
  ALTER TABLE `ims_xm_mallv3_goods_giftcard_commission`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_goods_giftcard_commission`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_recovery_category')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_recovery_category` (
    `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
    `weid` int(11) NOT NULL,
    `pid` int(10) UNSIGNED DEFAULT '0' COMMENT '上级分类ID',
    `title` varchar(50) DEFAULT '' COMMENT '标题',
    `price` decimal(15,2) DEFAULT '0.00',
    `ptype` tinyint(2) NOT NULL COMMENT '类型',
    `servicetime_ptype` int(11) DEFAULT '0',
    `image` varchar(100) DEFAULT NULL,
    `meta_keyword` varchar(255) DEFAULT NULL,
    `meta_description` varchar(255) DEFAULT NULL,
    `sort` int(11) DEFAULT '60',
    `create_time` int(11) NOT NULL DEFAULT '0',
    `update_time` int(10) DEFAULT '0',
    `status` tinyint(1) DEFAULT '1'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='回收分类';
  
  ALTER TABLE `ims_xm_mallv3_recovery_category`
    ADD PRIMARY KEY (`id`),
    ADD KEY `pid` (`pid`);
  
  ALTER TABLE `ims_xm_mallv3_recovery_category`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_recovery_weight')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_recovery_weight` (
    `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
    `weid` int(11) NOT NULL,
    `begin_weight` varchar(10) DEFAULT '0' COMMENT '开始重量',
    `end_weight` varchar(10) DEFAULT '0' COMMENT '结束重量',
    `sort` int(6) UNSIGNED DEFAULT '0' COMMENT '排序',
    `create_time` int(11) NOT NULL DEFAULT '0',
    `update_time` int(10) DEFAULT '0',
    `status` tinyint(1) DEFAULT '1'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='重量';
  
  ALTER TABLE `ims_xm_mallv3_recovery_weight`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_recovery_weight`
    MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID';
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_cashregister')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_cashregister` (
    `id` int(11) UNSIGNED NOT NULL,
    `weid` int(11) NOT NULL,
    `operator_id` int(11) NOT NULL DEFAULT '0' COMMENT '操作员',
    `uid` int(11) DEFAULT '0',
    `tel` varchar(20) DEFAULT '',
    `create_time` int(11) NOT NULL DEFAULT '0',
    `update_time` int(11) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收银台';
  
  ALTER TABLE `ims_xm_mallv3_cashregister`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_cashregister`
    MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_cashregister_log')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_cashregister_log` (
    `id` int(11) UNSIGNED NOT NULL,
    `cid` int(11) NOT NULL DEFAULT '0',
    `goods_id` int(11) DEFAULT '0',
    `sku` text,
    `quantity` int(11) DEFAULT '0',
    `create_time` int(11) NOT NULL DEFAULT '0',
    `update_time` int(11) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收银台记录';
  
  ALTER TABLE `ims_xm_mallv3_cashregister_log`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_cashregister_log`
    MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_house')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_house` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `ocid` int(11) DEFAULT '0',
    `sid` int(11) DEFAULT '0' COMMENT '店铺ID',
    `name` varchar(64) DEFAULT NULL COMMENT '名称',
    `keyword` varchar(100) DEFAULT '',
    `cat_id` int(11) DEFAULT '0' COMMENT '分类ID',
    `heid` int(11) DEFAULT '0' COMMENT '小区',
    `ptype` tinyint(2) DEFAULT '0' COMMENT '类型',
    `hall` int(3) DEFAULT '0' COMMENT '厅',
    `room` int(3) DEFAULT '0' COMMENT '房',
    `kitchen` int(3) DEFAULT NULL COMMENT '厨房',
    `toilet` int(3) DEFAULT '0' COMMENT '卫生间',
    `balcony` int(3) DEFAULT '0' COMMENT '阳台',
    `toward` varchar(20) DEFAULT '' COMMENT '朝向',
    `floorspace` decimal(6,2) DEFAULT '0.00' COMMENT '楼面面积',
    `floorheight` int(3) DEFAULT '0' COMMENT '楼层高度',
    `years` varchar(10) DEFAULT '' COMMENT '年代',
    `totalfloorheight` int(3) DEFAULT '0' COMMENT '楼层总高度',
    `tel` varchar(20) DEFAULT '' COMMENT '联系电话',
    `image` varchar(200) DEFAULT NULL,
    `videotype` tinyint(2) DEFAULT '0' COMMENT '1服务器2腾讯视频',
    `videoid` varchar(100) DEFAULT '',
    `videourl` varchar(200) DEFAULT '',
    `price` decimal(15,2) DEFAULT '0.00' COMMENT '价格',
    `sort` int(11) DEFAULT '0' COMMENT '排序',
    `is_hot` tinyint(1) DEFAULT '0' COMMENT '热卖',
    `is_discount` tinyint(1) DEFAULT '0' COMMENT '促销',
    `is_recommended` tinyint(1) DEFAULT '0' COMMENT '推荐',
    `is_new` tinyint(1) DEFAULT '0' COMMENT '新上架',
    `is_commission` tinyint(1) DEFAULT '0' COMMENT '是否独立佣金',
    `is_comment` tinyint(1) DEFAULT '0' COMMENT '是否可评论',
    `commission_price` decimal(15,2) DEFAULT '0.00' COMMENT '独立佣金',
    `viewed` int(5) DEFAULT '0' COMMENT '点击量',
    `viewed_base` int(11) DEFAULT '0' COMMENT '点击量基数',
    `province_name` varchar(30) DEFAULT '',
    `city_name` varchar(30) DEFAULT '',
    `district_name` varchar(30) DEFAULT '',
    `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
    `update_time` int(11) DEFAULT '0' COMMENT '更新时间',
    `status` tinyint(1) DEFAULT '0' COMMENT '是否公开'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='房屋信息';
  
  ALTER TABLE `ims_xm_mallv3_house`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_house`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_house_description')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_house_description` (
    `id` int(11) NOT NULL,
    `house_id` int(11) NOT NULL DEFAULT '0',
    `description` text COMMENT '商品详情',
    `meta_description` varchar(255) DEFAULT NULL,
    `meta_keyword` varchar(255) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='房产信息描述表';
  
  ALTER TABLE `ims_xm_mallv3_house_description`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_house_description`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_house_image')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_house_image` (
    `id` int(11) NOT NULL,
    `weid` int(11) DEFAULT '0',
    `house_id` int(11) NOT NULL DEFAULT '0',
    `image` varchar(255) DEFAULT NULL,
    `sort` int(3) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='房产图片表';
  
  ALTER TABLE `ims_xm_mallv3_house_image`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_house_image`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_platform_scheme')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_platform_scheme` (
    `id` int(11) NOT NULL COMMENT '编号',
    `title` varchar(36) DEFAULT NULL COMMENT '分组名称',
    `status` tinyint(1) DEFAULT '1' COMMENT '状态',
    `description` text COMMENT '描述',
    `access` text COMMENT '功能',
    `is_allrole` tinyint(1) DEFAULT '0'
  ) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
  
  ALTER TABLE `ims_xm_mallv3_platform_scheme`
    ADD PRIMARY KEY (`id`) USING BTREE;
  
  ALTER TABLE `ims_xm_mallv3_platform_scheme`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '编号';
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_recovery_order')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_recovery_order` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `ocid` int(11) DEFAULT '0',
    `cat_id` int(11) DEFAULT '0' COMMENT '分类',
    `sid` int(11) DEFAULT '0' COMMENT '店ID',
    `ptype` tinyint(2) NOT NULL,
    `sendto` tinyint(1) DEFAULT '0' COMMENT '1商家2平台师傅',
    `is_timing` tinyint(1) DEFAULT '0' COMMENT '是否定期服务',
    `distance` decimal(15,2) DEFAULT '0.00' COMMENT '距离',
    `order_num_alias` varchar(40) DEFAULT NULL COMMENT '订单编号',
    `uid` int(11) DEFAULT '0',
    `aid` varchar(100) DEFAULT NULL COMMENT '代理id',
    `return_points` int(11) DEFAULT '0' COMMENT '可得积分',
    `name` varchar(32) DEFAULT NULL COMMENT '购买的会员名字',
    `email` varchar(64) DEFAULT NULL,
    `tel` varchar(64) DEFAULT NULL,
    `contacts_name` varchar(32) DEFAULT NULL COMMENT '联系人姓名',
    `address_id` int(11) DEFAULT '0',
    `contacts_tel` varchar(20) DEFAULT NULL COMMENT '电话',
    `province_name` varchar(30) DEFAULT '',
    `city_name` varchar(30) DEFAULT '',
    `district_name` varchar(30) DEFAULT '',
    `address` varchar(255) DEFAULT NULL COMMENT '地址',
    `latitude` varchar(30) DEFAULT NULL,
    `longitude` varchar(30) DEFAULT NULL,
    `remark` text COMMENT '订单备注',
    `total` decimal(15,2) NOT NULL DEFAULT '0.00',
    `order_status_id` int(11) NOT NULL DEFAULT '0',
    `is_comment` tinyint(1) DEFAULT '0' COMMENT '是否已评价',
    `ip` varchar(40) DEFAULT NULL,
    `searchkeyword` longtext,
    `user_agent` text COMMENT '用户系统信息',
    `customtext` text COMMENT '附件',
    `pay_time` int(11) DEFAULT '0' COMMENT '付款时间',
    `complete_time` int(11) DEFAULT '0' COMMENT '完成服务时间',
    `create_time` int(11) NOT NULL DEFAULT '0' COMMENT '添加时间',
    `update_time` int(11) DEFAULT '0' COMMENT '更新时间'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_recovery_order`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_recovery_order`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_recovery_order_history')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_recovery_order_history` (
    `id` int(11) NOT NULL,
    `order_id` int(11) NOT NULL DEFAULT '0',
    `order_status_id` int(5) NOT NULL DEFAULT '0',
    `notify` tinyint(1) NOT NULL DEFAULT '0',
    `remark` text,
    `create_time` int(11) NOT NULL DEFAULT '0',
    `image` varchar(64) DEFAULT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
  
  ALTER TABLE `ims_xm_mallv3_recovery_order_history`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_recovery_order_history`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_recovery_order_staff')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_recovery_order_staff` (
    `id` int(11) NOT NULL,
    `uuid` varchar(68) DEFAULT '',
    `order_id` int(11) DEFAULT NULL,
    `uid` int(11) DEFAULT '0',
    `title` varchar(20) DEFAULT NULL COMMENT '师傅姓名',
    `yue_begin_time` int(11) DEFAULT '0',
    `yue_end_time` int(11) DEFAULT '0',
    `end_time` int(11) DEFAULT '0' COMMENT '结速时间',
    `begin_time` int(11) DEFAULT '0' COMMENT '开始时间',
    `is_complete` tinyint(1) DEFAULT '0',
    `is_confirm` tinyint(1) DEFAULT '0',
    `is_settlement` tinyint(1) DEFAULT '0' COMMENT '结算',
    `create_time` int(11) NOT NULL COMMENT '创建时间'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='订单关联回收员';
  
  ALTER TABLE `ims_xm_mallv3_recovery_order_staff`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_recovery_order_staff`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_recovery_personnel')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_recovery_personnel` (
    `id` int(11) NOT NULL,
    `uuid` varchar(68) DEFAULT '',
    `weid` int(11) NOT NULL,
    `sid` int(11) DEFAULT '0',
    `ocid` int(11) DEFAULT '0',
    `uid` int(11) DEFAULT NULL,
    `answer` varchar(255) DEFAULT '' COMMENT '留言',
    `tel` varchar(20) DEFAULT '' COMMENT '手机号',
    `title` varchar(40) DEFAULT NULL,
    `level` int(3) DEFAULT '0' COMMENT '等级',
    `touxiang` varchar(200) DEFAULT NULL COMMENT '头像',
    `photoalbum` text COMMENT '相册',
    `videourl` varchar(200) DEFAULT NULL,
    `service_times` int(11) DEFAULT '0' COMMENT '服务次数',
    `service_times_base` int(11) DEFAULT '0' COMMENT '接单基数',
    `comment` int(11) DEFAULT '0' COMMENT '评价数量',
    `comment_base` int(11) DEFAULT '0' COMMENT '评价基数',
    `viewed` int(11) DEFAULT '1',
    `viewed_base` int(11) DEFAULT '0' COMMENT '人气基数',
    `cate_ids` varchar(200) DEFAULT '' COMMENT '可接服务分类id',
    `certificate_ids` varchar(100) DEFAULT '' COMMENT '认证',
    `total_income` decimal(9,2) DEFAULT '0.00' COMMENT '总收入',
    `income` decimal(9,2) DEFAULT '0.00' COMMENT '收入',
    `points` int(11) DEFAULT '0' COMMENT '积分',
    `email` varchar(40) DEFAULT '',
    `introduction` text COMMENT '简介',
    `id_cart` varchar(64) DEFAULT '' COMMENT '身份证',
    `province_name` varchar(30) DEFAULT NULL,
    `city_name` varchar(30) DEFAULT NULL,
    `district_name` varchar(30) DEFAULT NULL,
    `region_name` varchar(100) DEFAULT NULL,
    `longitude` varchar(30) DEFAULT NULL,
    `latitude` varchar(30) DEFAULT NULL,
    `dizhi` text COMMENT '地址',
    `house_number` varchar(100) DEFAULT NULL,
    `customtext` text,
    `create_time` int(11) DEFAULT '0',
    `end_time` int(11) DEFAULT '0' COMMENT '到期时间',
    `is_business` tinyint(1) DEFAULT '1' COMMENT '是否营业',
    `sort` int(11) DEFAULT '100',
    `status` tinyint(1) NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='回收人员';
  
  ALTER TABLE `ims_xm_mallv3_recovery_personnel`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_recovery_personnel`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_recovery_personnel_incomelog')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_recovery_personnel_incomelog` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL,
    `uuid` varchar(68) NOT NULL,
    `level` int(11) DEFAULT '0',
    `order_id` int(11) DEFAULT '0',
    `ptype` int(3) NOT NULL COMMENT '流水类型',
    `order_num_alias` varchar(40) DEFAULT '',
    `buyer_id` int(11) NOT NULL COMMENT '下单人的id',
    `income` decimal(9,3) NOT NULL COMMENT '奖金',
    `return_percent` decimal(9,2) DEFAULT '0.00' COMMENT '提成点数',
    `percentremark` varchar(50) DEFAULT '' COMMENT '佣金公式',
    `description` varchar(200) DEFAULT NULL,
    `order_total` decimal(6,2) DEFAULT '0.00' COMMENT '订单总价',
    `pay_time` int(11) DEFAULT '0' COMMENT '下单时间',
    `create_time` int(11) NOT NULL,
    `month_time` varchar(40) DEFAULT '',
    `year_time` varchar(40) DEFAULT '',
    `order_status_id` int(11) DEFAULT '0' COMMENT '订单状态'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='收入明细';
  
  ALTER TABLE `ims_xm_mallv3_recovery_personnel_incomelog`
    ADD PRIMARY KEY (`id`);
  
  ALTER TABLE `ims_xm_mallv3_recovery_personnel_incomelog`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_feedback')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_feedback` (
    `id` int(11) NOT NULL,
    `reply_id` int(11) DEFAULT '0',
    `weid` int(11) NOT NULL DEFAULT '0',
    `store_id` int(11) DEFAULT '0',
    `order_id` int(11) DEFAULT '0',
    `otr_id` int(11) DEFAULT '0' COMMENT '次卡id',
    `cat_id` int(11) DEFAULT '0' COMMENT '分类ID',
    `goods_id` int(11) DEFAULT '0',
    `technical_uuid` varchar(68) COLLATE utf8mb4_unicode_ci DEFAULT '',
    `uid` int(11) DEFAULT '0',
    `nick_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '',
    `head_img_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '头像',
    `content` text COLLATE utf8mb4_unicode_ci COMMENT '内容',
    `images` text COLLATE utf8mb4_unicode_ci,
    `create_time` int(11) NOT NULL DEFAULT '0',
    `update_time` int(11) DEFAULT NULL,
    `delete_time` int(11) DEFAULT NULL,
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
  
  ALTER TABLE `ims_xm_mallv3_feedback`
    ADD PRIMARY KEY (`id`),
    ADD KEY `idx_uniacid` (`weid`),
    ADD KEY `idx_orderid` (`order_id`),
    ADD KEY `idx_goodsid` (`goods_id`),
    ADD KEY `idx_openid` (`uid`),
    ADD KEY `idx_createtime` (`create_time`);
  
  ALTER TABLE `ims_xm_mallv3_feedback`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_rewardspenalties')) {
  pdo_run("CREATE TABLE `ims_xm_mallv3_rewardspenalties` (
    `id` int(11) NOT NULL,
    `weid` int(11) NOT NULL DEFAULT '0',
    `ptype` tinyint(1) DEFAULT '0' COMMENT '0惩1奖',
    `store_id` int(11) DEFAULT '0',
    `order_id` int(11) DEFAULT '0',
    `technical_uuid` varchar(68) COLLATE utf8mb4_unicode_ci DEFAULT '',
    `uid` int(11) DEFAULT '0',
    `nick_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT '',
    `content` text COLLATE utf8mb4_unicode_ci COMMENT '内容',
    `level` tinyint(1) DEFAULT '0' COMMENT '等级',
    `fraction` int(3) DEFAULT '0' COMMENT '分数',
    `images` text COLLATE utf8mb4_unicode_ci,
    `create_time` int(11) NOT NULL DEFAULT '0',
    `update_time` int(11) DEFAULT NULL,
    `delete_time` int(11) DEFAULT NULL,
    `status` tinyint(1) DEFAULT '0'
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
  
  ALTER TABLE `ims_xm_mallv3_rewardspenalties`
    ADD PRIMARY KEY (`id`),
    ADD KEY `idx_orderid` (`order_id`),
    ADD KEY `idx_openid` (`uid`),
    ADD KEY `idx_createtime` (`create_time`);

  ALTER TABLE `ims_xm_mallv3_rewardspenalties`
    MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
  COMMIT;");
}

if (!pdo_tableexists('xm_mallv3_express')) {
  pdo_run("DROP TABLE IF EXISTS `ims_xm_mallv3_express`;
    CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_express` (
      `id` int(11) NOT NULL,
      `weid` int(11) NOT NULL,
      `name` varchar(255) DEFAULT '',
      `code` varchar(255) DEFAULT '',
      `sort` int(11) DEFAULT '100',
      `type` varchar(255) DEFAULT '' COMMENT '快递接品类型：kdniao=快递鸟',
      `status` smallint(1) DEFAULT '0'
    ) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8 COMMENT='快递公司';
    
    INSERT INTO `ims_xm_mallv3_express` (`id`, `weid`, `name`, `code`, `sort`, `type`, `status`) VALUES
    (1, 0, '顺丰快递', 'SF', 1, 'kdniao', 0),
    (2, 0, '申通快递', 'STO', 1, 'kdniao', 0),
    (3, 0, '韵达快递', 'YD', 1, 'kdniao', 0),
    (4, 0, '圆通速递', 'YTO', 1, 'kdniao', 0),
    (5, 0, '中通速递', 'ZTO', 1, 'kdniao', 0),
    (6, 0, '百世快递', 'HTKY', 1, 'kdniao', 0),
    (7, 0, 'EMS', 'EMS', 2, 'kdniao', 0),
    (8, 0, '天天快递', 'HHTT', 2, 'kdniao', 0),
    (9, 0, '邮政平邮/小包', 'YZPY', 2, 'kdniao', 0),
    (10, 0, '宅急送', 'ZJS', 2, 'kdniao', 0),
    (11, 0, '国通快递', 'GTO', 5, 'kdniao', 0),
    (12, 0, '全峰快递', 'QFKD', 5, 'kdniao', 0),
    (13, 0, '优速快递', 'UC', 5, 'kdniao', 0),
    (14, 0, '中铁快运', 'ZTKY', 5, 'kdniao', 0),
    (15, 0, '中铁物流', 'ZTWL', 5, 'kdniao', 0),
    (16, 0, '亚马逊物流', 'AMAZON', 5, 'kdniao', 0),
    (17, 0, '城际快递', 'CJKD', 5, 'kdniao', 0),
    (18, 0, '德邦', 'DBL', 5, 'kdniao', 0),
    (19, 0, '汇丰物流', 'HFWL', 5, 'kdniao', 0),
    (20, 0, '百世快运', 'BTWL', 100, 'kdniao', 0),
    (21, 0, '安捷快递', 'AJ', 100, 'kdniao', 0),
    (22, 0, '安能物流', 'ANE', 100, 'kdniao', 0),
    (23, 0, '安信达快递', 'AXD', 100, 'kdniao', 0),
    (24, 0, '北青小红帽', 'BQXHM', 100, 'kdniao', 0),
    (25, 0, '百福东方', 'BFDF', 100, 'kdniao', 0),
    (26, 0, 'CCES快递', 'CCES', 100, 'kdniao', 0),
    (27, 0, '城市100', 'CITY100', 100, 'kdniao', 0),
    (28, 0, 'COE东方快递', 'COE', 100, 'kdniao', 0),
    (29, 0, '长沙创一', 'CSCY', 100, 'kdniao', 0),
    (30, 0, '成都善途速运', 'CDSTKY', 100, 'kdniao', 0),
    (31, 0, 'D速物流', 'DSWL', 100, 'kdniao', 0),
    (32, 0, '大田物流', 'DTWL', 100, 'kdniao', 0),
    (33, 0, '快捷速递', 'FAST', 100, 'kdniao', 0),
    (34, 0, 'FEDEX联邦(国内件）', 'FEDEX', 100, 'kdniao', 0),
    (35, 0, 'FEDEX联邦(国际件）', 'FEDEX_GJ', 100, 'kdniao', 0),
    (36, 0, '飞康达', 'FKD', 100, 'kdniao', 0),
    (37, 0, '广东邮政', 'GDEMS', 100, 'kdniao', 0),
    (38, 0, '共速达', 'GSD', 100, 'kdniao', 0),
    (39, 0, '高铁速递', 'GTSD', 100, 'kdniao', 0),
    (40, 0, '恒路物流', 'HLWL', 100, 'kdniao', 0),
    (41, 0, '天地华宇', 'HOAU', 100, 'kdniao', 0),
    (42, 0, '华强物流', 'hq568', 100, 'kdniao', 0),
    (43, 0, '华夏龙物流', 'HXLWL', 100, 'kdniao', 0),
    (44, 0, '好来运快递', 'HYLSD', 100, 'kdniao', 0),
    (45, 0, '京广速递', 'JGSD', 100, 'kdniao', 0),
    (46, 0, '九曳供应链', 'JIUYE', 100, 'kdniao', 0),
    (47, 0, '佳吉快运', 'JJKY', 100, 'kdniao', 0),
    (48, 0, '嘉里物流', 'JLDT', 100, 'kdniao', 0),
    (49, 0, '捷特快递', 'JTKD', 100, 'kdniao', 0),
    (50, 0, '急先达', 'JXD', 100, 'kdniao', 0),
    (51, 0, '晋越快递', 'JYKD', 100, 'kdniao', 0),
    (52, 0, '加运美', 'JYM', 100, 'kdniao', 0),
    (53, 0, '佳怡物流', 'JYWL', 100, 'kdniao', 0),
    (54, 0, '跨越物流', 'KYWL', 100, 'kdniao', 0),
    (55, 0, '龙邦快递', 'LB', 100, 'kdniao', 0),
    (56, 0, '联昊通速递', 'LHT', 100, 'kdniao', 0),
    (57, 0, '民航快递', 'MHKD', 100, 'kdniao', 0),
    (58, 0, '明亮物流', 'MLWL', 100, 'kdniao', 0),
    (59, 0, '能达速递', 'NEDA', 100, 'kdniao', 0),
    (60, 0, '平安达腾飞快递', 'PADTF', 100, 'kdniao', 0),
    (61, 0, '全晨快递', 'QCKD', 100, 'kdniao', 0),
    (62, 0, '全日通快递', 'QRT', 100, 'kdniao', 0),
    (63, 0, '如风达', 'RFD', 100, 'kdniao', 0),
    (64, 0, '赛澳递', 'SAD', 100, 'kdniao', 0),
    (65, 0, '圣安物流', 'SAWL', 100, 'kdniao', 0),
    (66, 0, '盛邦物流', 'SBWL', 100, 'kdniao', 0),
    (67, 0, '上大物流', 'SDWL', 100, 'kdniao', 0),
    (68, 0, '盛丰物流', 'SFWL', 100, 'kdniao', 0),
    (69, 0, '盛辉物流', 'SHWL', 100, 'kdniao', 0),
    (70, 0, '速通物流', 'ST', 100, 'kdniao', 0),
    (71, 0, '速腾快递', 'STWL', 100, 'kdniao', 0),
    (72, 0, '速尔快递', 'SURE', 100, 'kdniao', 0),
    (73, 0, '唐山申通', 'TSSTO', 100, 'kdniao', 0),
    (74, 0, '全一快递', 'UAPEX', 100, 'kdniao', 0),
    (75, 0, '万家物流', 'WJWL', 100, 'kdniao', 0),
    (76, 0, '万象物流', 'WXWL', 100, 'kdniao', 0),
    (77, 0, '新邦物流', 'XBWL', 100, 'kdniao', 0),
    (78, 0, '信丰快递', 'XFEX', 100, 'kdniao', 0),
    (79, 0, '希优特', 'XYT', 100, 'kdniao', 0),
    (80, 0, '新杰物流', 'XJ', 100, 'kdniao', 0),
    (81, 0, '源安达快递', 'YADEX', 100, 'kdniao', 0),
    (82, 0, '远成物流', 'YCWL', 100, 'kdniao', 0),
    (83, 0, '义达国际物流', 'YDH', 100, 'kdniao', 0),
    (84, 0, '越丰物流', 'YFEX', 100, 'kdniao', 0),
    (85, 0, '原飞航物流', 'YFHEX', 100, 'kdniao', 0),
    (86, 0, '亚风快递', 'YFSD', 100, 'kdniao', 0),
    (87, 0, '运通快递', 'YTKD', 100, 'kdniao', 0),
    (88, 0, '亿翔快递', 'YXKD', 100, 'kdniao', 0),
    (89, 0, '增益快递', 'ZENY', 100, 'kdniao', 0),
    (90, 0, '汇强快递', 'ZHQKD', 100, 'kdniao', 0),
    (91, 0, '众通快递', 'ZTE', 100, 'kdniao', 0),
    (92, 0, '中邮物流', 'ZYWL', 100, 'kdniao', 0),
    (93, 0, '速必达物流', 'SUBIDA', 100, 'kdniao', 0),
    (94, 0, '瑞丰速递', 'RFEX', 100, 'kdniao', 0),
    (95, 0, '快客快递', 'QUICK', 100, 'kdniao', 0),
    (96, 0, 'CNPEX中邮快递', 'CNPEX', 100, 'kdniao', 0),
    (97, 0, '鸿桥供应链', 'HOTSCM', 100, 'kdniao', 0),
    (98, 0, '海派通物流公司', 'HPTEX', 100, 'kdniao', 0),
    (99, 0, '澳邮专线', 'AYCA', 100, 'kdniao', 0),
    (100, 0, '泛捷快递', 'PANEX', 100, 'kdniao', 0),
    (101, 0, 'PCA Express', 'PCA', 100, 'kdniao', 0),
    (102, 0, 'UEQ Express', 'UEQ', 100, 'kdniao', 0);
    
    ALTER TABLE `ims_xm_mallv3_express`
      ADD PRIMARY KEY (`id`),
      ADD KEY `is_delete` (`status`);
    
    ALTER TABLE `ims_xm_mallv3_express`
      MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=103;");
}

//删除字段
if (pdo_fieldexists('xm_mallv3_paymethod', 'update_time')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_paymethod') . " DROP `update_time`;");
}
if (pdo_fieldexists('xm_mallv3_paymethod', 'order_status_id')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_paymethod') . " DROP `order_status_id`;");
}
if (pdo_fieldexists('xm_mallv3_goods', 'is_share_charges')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " DROP `is_share_charges`;");
}

if (pdo_fieldexists('xm_mallv3_order_staff', 'weid')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order_staff') . " DROP `weid`;");
}

//修改字段
if (pdo_fieldexists('xm_mallv3_goods', 'goods_id')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " CHANGE `goods_id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
}
if (pdo_fieldexists('xm_mallv3_brand', 'brand_id')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_brand') . " CHANGE `brand_id` `id` INT(8) NOT NULL AUTO_INCREMENT;");
}

if (pdo_fieldexists('xm_mallv3_order', 'comment')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order') . " CHANGE `comment` `remark` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '订单备注';");
}

if (pdo_fieldexists('xm_mallv3_order_history', 'comment')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order_history') . " CHANGE `comment` `remark` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
}

if (pdo_fieldexists('xm_mallv3_address', 'address_id')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_address') . " CHANGE `address_id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
}

if (pdo_fieldexists('xm_mallv3_address', 'country_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_address` CHANGE `country_id` `district_id` INT(11) NULL DEFAULT '0' COMMENT '县乡';");
}

if (pdo_fieldexists('xm_mallv3_member', 'uid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member` CHANGE `uid` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '用户id';");
}

if (pdo_fieldexists('xm_mallv3_member', 'checked')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member` CHANGE `checked` `status` TINYINT(1) NULL DEFAULT '0' COMMENT '是否审核';");
}

if (pdo_fieldexists('xm_mallv3_order', 'beginTime') && !pdo_fieldexists('xm_mallv3_order', 'begin_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` CHANGE `beginTime` `begin_time` INT(11) NULL DEFAULT '0' COMMENT '开始时间';");
}
if (pdo_fieldexists('xm_mallv3_order_staff', 'beginTime') && !pdo_fieldexists('xm_mallv3_order_staff', 'begin_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_staff` CHANGE `beginTime` `begin_time` INT(11) NULL DEFAULT '0' COMMENT '开始时间';");
}
if (pdo_fieldexists('xm_mallv3_service_time', 'beginTime') && !pdo_fieldexists('xm_mallv3_service_time', 'begin_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_service_time` CHANGE `beginTime` `begin_time` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '天始时间';");
}
if (pdo_fieldexists('xm_mallv3_viporder', 'beginTime') && !pdo_fieldexists('xm_mallv3_viporder', 'begin_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_viporder` CHANGE `beginTime` `begin_time` INT(11) NULL DEFAULT '0' COMMENT '开始时间';");
}
if (pdo_fieldexists('xm_mallv3_miaosha_time', 'beginTime') && !pdo_fieldexists('xm_mallv3_miaosha_time', 'begin_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_miaosha_time` CHANGE `beginTime` `begin_time` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '天始时间';");
}

if (pdo_fieldexists('xm_mallv3_order', 'endTime') && !pdo_fieldexists('xm_mallv3_order', 'end_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` CHANGE `endTime` `end_time` INT(11) NULL DEFAULT '0' COMMENT '结束时间';");
}
if (pdo_fieldexists('xm_mallv3_order_staff', 'endTime') && !pdo_fieldexists('xm_mallv3_order_staff', 'end_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_staff` CHANGE `endTime` `end_time` INT(11) NULL DEFAULT '0' COMMENT '结速时间';");
}
if (pdo_fieldexists('xm_mallv3_service_time', 'endTime') && !pdo_fieldexists('xm_mallv3_service_time', 'end_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_service_time` CHANGE `endTime` `end_time` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '结速时间';");
}
if (pdo_fieldexists('xm_mallv3_viporder', 'endTime') && !pdo_fieldexists('xm_mallv3_viporder', 'end_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_viporder` CHANGE `endTime` `end_time` INT(11) NULL DEFAULT '0' COMMENT '结束时间';");
}
if (pdo_fieldexists('xm_mallv3_miaosha_time', 'endTime') && !pdo_fieldexists('xm_mallv3_miaosha_time', 'end_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_miaosha_time` CHANGE `endTime` `end_time` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '结速时间';");
}

if (pdo_fieldexists('xm_mallv3_store', 'stid') && !pdo_fieldexists('xm_mallv3_store', 'level')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_store` CHANGE `stid` `level` INT(10) NULL DEFAULT '0' COMMENT '店等级';");
}

if (pdo_fieldexists('xm_mallv3_order', 'order_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` CHANGE `order_id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
}

if (pdo_fieldexists('xm_mallv3_attribute', 'attribute_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_attribute` CHANGE `attribute_id` `id` INT(10) NOT NULL AUTO_INCREMENT;");
}

if (pdo_fieldexists('xm_mallv3_attribute_value', 'attribute_value_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_attribute_value` CHANGE `attribute_value_id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
}

if (pdo_fieldexists('xm_mallv3_cart', 'cart_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_cart` CHANGE `cart_id` `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT;");
}

if (pdo_fieldexists('xm_mallv3_order_goods', 'order_goods_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_goods` CHANGE `order_goods_id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
}

if (pdo_fieldexists('xm_mallv3_order_history', 'order_history_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_history` CHANGE `order_history_id` `id` INT(11) NOT NULL AUTO_INCREMENT;");
}

if (pdo_fieldexists('xm_mallv3_order_total', 'order_total_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_total` CHANGE `order_total_id` `id` INT(10) NOT NULL AUTO_INCREMENT;");
}

if (pdo_fieldexists('xm_mallv3_order_refund', 'desc')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order_refund') . " CHANGE `desc` `refund_desc` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '退款说明';");
}

if (!pdo_fieldexists('xm_mallv3_register_field', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_register_field` CHANGE `px` `sort` INT(11) NULL DEFAULT NULL COMMENT '排序';");
}

if (!pdo_fieldexists('xm_mallv3_attribute_value', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_attribute_value` CHANGE `value_sort_order` `sort` INT(3) NULL DEFAULT '0';");
}

if (!pdo_fieldexists('xm_mallv3_bottom_menu', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_bottom_menu` CHANGE `sort_order` `sort` INT(10) UNSIGNED NULL DEFAULT '0' COMMENT '排序（同级有效）';");
}

if (!pdo_fieldexists('xm_mallv3_bottom_menu', 'tiaojian')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_bottom_menu` ADD `tiaojian` VARCHAR(30) NULL DEFAULT '' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_bottom_menu', 'title1')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_bottom_menu` ADD `title1` VARCHAR(50) NULL DEFAULT '' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_bottom_menu', 'customurl')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_bottom_menu` ADD `customurl` VARCHAR(160) NULL DEFAULT '' COMMENT '自定义url' AFTER `url`;");
}

if (!pdo_fieldexists('xm_mallv3_brand', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_brand` CHANGE `sort_order` `sort` INT(11) NULL DEFAULT '100' COMMENT '排序';");
}

if (!pdo_fieldexists('xm_mallv3_category', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_category` CHANGE `sort_order` `sort` INT(11) NULL DEFAULT '60';");
}

if (!pdo_fieldexists('xm_mallv3_config', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_config` CHANGE `sort_order` `sort` INT(5) NULL DEFAULT '0' COMMENT '排序';");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` CHANGE `sort_order` `sort` INT(11) NULL DEFAULT '0' COMMENT '排序';");
}

if (!pdo_fieldexists('xm_mallv3_goods_image', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods_image` CHANGE `sort_order` `sort` INT(3) NULL DEFAULT '0';");
}

if (!pdo_fieldexists('xm_mallv3_order_total', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_total` CHANGE `sort_order` `sort` INT(3) NULL DEFAULT '0';");
}

if (!pdo_fieldexists('xm_mallv3_printer', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_printer` CHANGE `sort_order` `sort` INT(5) NULL DEFAULT '0' COMMENT '排序';");
}

if (!pdo_fieldexists('xm_mallv3_store', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_store` CHANGE `sort_order` `sort` INT(10) UNSIGNED NULL DEFAULT '0' COMMENT '排序';");
}

if (!pdo_fieldexists('xm_mallv3_store_level', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_store_level` CHANGE `sort_order` `sort` INT(10) UNSIGNED NULL DEFAULT '0' COMMENT '排序';");
}

if (!pdo_fieldexists('xm_mallv3_store_level', 'return_percent')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_store_level` ADD `return_percent` DECIMAL(4,2) NULL DEFAULT '0' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_store_image', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_store_image` CHANGE `sort_order` `sort` INT(3) NULL DEFAULT '0';");
}

if (pdo_fieldexists('xm_mallv3_goods', 'sku') && !pdo_fieldexists('xm_mallv3_goods', 'quantity_unit')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` CHANGE `sku` `quantity_unit` VARCHAR(10) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '单位';");
}

if (!pdo_fieldexists('xm_mallv3_ad', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_ad` CHANGE `px` `sort` INT(11) NULL DEFAULT '0';");
}

if (!pdo_fieldexists('xm_mallv3_agent', 'title')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agent` CHANGE `name` `title` VARCHAR(40) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
}

if (!pdo_fieldexists('xm_mallv3_transport_extend', 'is_default')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_transport_extend` CHANGE `is_default` `is_default` TINYINT(1) NULL DEFAULT '0' COMMENT '是否默认运费1是0否';");
}

if (!pdo_fieldexists('xm_mallv3_miaosha_goods', 'price') && pdo_fieldexists('xm_mallv3_miaosha_goods', 'ms_price')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_miaosha_goods` CHANGE `ms_price` `price` DECIMAL(15,2) NOT NULL COMMENT '秒杀价格';");
}

if (!pdo_fieldexists('xm_mallv3_tuan_goods', 'price') && pdo_fieldexists('xm_mallv3_tuan_goods', 'tuan_price')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuan_goods` CHANGE `tuan_price` `price` DECIMAL(15,2) NOT NULL COMMENT '团购价格';");
}

if (!pdo_fieldexists('xm_mallv3_bottom_menu', 'is_submitaudit') && pdo_fieldexists('xm_mallv3_bottom_menu', 'is_audit')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_bottom_menu` CHANGE `is_audit` `is_submitaudit` TINYINT(1) NULL DEFAULT '0';");
}
if (!pdo_fieldexists('xm_mallv3_diy_page', 'is_submitaudit') && pdo_fieldexists('xm_mallv3_diy_page', 'is_audit')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_diy_page` CHANGE `is_audit` `is_submitaudit` TINYINT(1) NULL DEFAULT '0' COMMENT '1为审核页面';");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'is_recommended') && pdo_fieldexists('xm_mallv3_goods', 'is_recommand')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` CHANGE `is_recommand` `is_recommended` TINYINT(1) NULL DEFAULT '0' COMMENT '推荐';");
}

//添加字段*********////////////////////////////////////////////
//********************************************************* */
if (!pdo_fieldexists('xm_mallv3_ad', 'sid')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_ad') . " ADD `sid` INT NOT NULL COMMENT '商店id' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_ad', 'ocid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_ad` ADD `ocid` INT NULL DEFAULT '0' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_transport_extend', 'status')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_transport_extend` ADD `status` TINYINT(1) NULL DEFAULT '0' AFTER `is_default`;");
}

if (!pdo_fieldexists('xm_mallv3_transport_extend', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_transport_extend` ADD `sort` INT NULL DEFAULT '0' AFTER `is_default`;");
}

if (!pdo_fieldexists('xm_mallv3_paymethod', 'group_ids')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_paymethod') . " ADD `group_ids` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER `collection_voucher`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'pay_method_id')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order') . "  ADD `pay_method_id` INT(11) NOT NULL DEFAULT '1' AFTER `payment_code`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'aid')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order') . " ADD `aid` VARCHAR(100) NULL DEFAULT NULL COMMENT '代理id' AFTER `uid`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'begin_time')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order') . " ADD `begin_time` INT NULL DEFAULT '0' COMMENT '结束时间' AFTER `create_time`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'end_time')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order') . " ADD `end_time` INT NULL DEFAULT '0' COMMENT '结束时间' AFTER `create_time`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'customtext')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `customtext` TEXT NULL COMMENT '附件' AFTER `update_time`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'ocid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `ocid` INT NULL DEFAULT '0' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'take_address_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `take_address_id` INT NULL DEFAULT '0' COMMENT '取件地址' AFTER `tel`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'costs')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . "  ADD `costs` DECIMAL(15,2) NOT NULL DEFAULT '0.00' COMMENT '成本' AFTER `price`;");
}

if (pdo_fieldmatch('xm_mallv3_goods', 'image', 'varchar', 200)<1) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` CHANGE `image` `image` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");
}

if (pdo_fieldmatch('xm_mallv3_order_goods', 'image', 'varchar', 200)<1) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_goods` CHANGE `image` `image` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '图片';");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'cat_id')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " ADD `cat_id` INT NOT NULL AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'ptype')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " ADD `ptype` TINYINT(2) NOT NULL COMMENT '类型' AFTER `cat_id`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'ocid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `ocid` INT NULL DEFAULT '0' AFTER `weid`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'sid')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order') . " ADD `sid` INT NULL DEFAULT '0' COMMENT '店ID' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'deliverymode')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order') . " ADD `deliverymode` INT(2) NOT NULL AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'ptype')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order') . " ADD `ptype` TINYINT(2) NOT NULL AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'is_offline_pay')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order') . " ADD `is_offline_pay` TINYINT(1) NULL DEFAULT '0' COMMENT '是否上传支付凭证' AFTER `payment_code`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'offline_pay_time')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order') . " ADD `offline_pay_time` INT NULL DEFAULT '0' AFTER `payment_code`;");
}

if (pdo_fieldmatch('xm_mallv3_order_offline', 'image', 'varchar', 200)<1) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order_offline') . " CHANGE `image` `image` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;");
}

if (!pdo_fieldexists('xm_mallv3_category', 'deliverymode')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_category') . " ADD `deliverymode` INT(2) NOT NULL AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_category', 'ptype')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_category') . " ADD `ptype` TINYINT(2) NOT NULL COMMENT '类型' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_member', 'balance')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member') . " ADD `balance` DECIMAL(9,2) NOT NULL DEFAULT '0.00' COMMENT '余额' AFTER `pid`;");
}

if (!pdo_fieldexists('xm_mallv3_member', 'freeze')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member') . " ADD `freeze` DECIMAL(9.2) NOT NULL DEFAULT '0.00' COMMENT '冻结金额' AFTER `pid`;");
}

if (!pdo_fieldexists('xm_mallv3_member', 'totleconsumed')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member') . " ADD `totleconsumed` DECIMAL(9,2) NOT NULL DEFAULT '0.00' COMMENT '累计消费金额' AFTER `balance`;");
}

if (!pdo_fieldexists('xm_mallv3_member', 'xingming')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member') . " ADD `xingming` VARCHAR(20) NULL DEFAULT '' COMMENT '姓名' AFTER `nickname`;");
}

if (!pdo_fieldexists('xm_mallv3_member', 'customtext')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member') . " ADD `customtext` TEXT NULL COMMENT '自定义定字段' AFTER `category_id`;");
}

if (!pdo_fieldexists('xm_mallv3_member', 'ocid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member` ADD `ocid` INT NULL DEFAULT '0' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_member_wishlist', 'id')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member_wishlist') . " ADD `id` INT NOT NULL AUTO_INCREMENT FIRST, ADD PRIMARY KEY (`id`);");
}

if (!pdo_fieldexists('xm_mallv3_order', 'update_time')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order') . " ADD `update_time` INT NOT NULL AFTER `pay_time`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'create_time')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order') . " ADD `create_time` INT NOT NULL AFTER `pay_time`;");
}

if (!pdo_fieldexists('xm_mallv3_order_history', 'create_time')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order_history') . " ADD `create_time` INT NOT NULL AFTER `remark`;");
}

if (!pdo_fieldexists('xm_mallv3_member_wishlist', 'create_time')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member_wishlist') . " ADD `create_time` INT NOT NULL AFTER `goods_id`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'shipping_address')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order') . " ADD `shipping_address` VARCHAR(255) NOT NULL COMMENT '地址' AFTER `shipping_method`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'longitude')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `longitude` VARCHAR(30) NULL AFTER `shipping_method`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'latitude')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `latitude` VARCHAR(30) NULL AFTER `shipping_method`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'is_new')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " ADD `is_new` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '新产品' AFTER `subtract`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'is_recommended')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " ADD `is_recommended` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '推荐' AFTER `subtract`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'is_discount')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " ADD `is_discount` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '促销' AFTER `subtract`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'is_hot')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " ADD `is_hot` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '热卖' AFTER `subtract`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'is_comment')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " ADD `is_comment` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否可评论' AFTER `status`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'tel')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " ADD `tel` VARCHAR(20) NOT NULL COMMENT '联系电话' AFTER `location`;");
}

if (!pdo_fieldexists('xm_mallv3_brand', 'sort')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_brand') . " ADD `sort` INT NOT NULL DEFAULT '100' COMMENT '排序' AFTER `name`;");
}

if (!pdo_fieldexists('xm_mallv3_brand', 'create_time')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_brand') . " ADD `create_time` INT NOT NULL DEFAULT '0' AFTER `sort`;");
}

if (!pdo_fieldexists('xm_mallv3_brand', 'update_time')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_brand') . " ADD `update_time` INT NOT NULL DEFAULT '0' AFTER `create_time`;");
}

if (!pdo_fieldexists('xm_mallv3_category', 'create_time')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_category') . " ADD `create_time` INT NOT NULL DEFAULT '0' AFTER `sort`;");
}

if (!pdo_fieldexists('xm_mallv3_cart', 'update_time')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_cart') . " ADD `update_time` INT NOT NULL DEFAULT '0' AFTER `create_time`;");
}

if (!pdo_fieldexists('xm_mallv3_attribute', 'create_time')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_attribute') . " ADD `create_time` INT NOT NULL DEFAULT '0' AFTER `value`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'update_time')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " ADD `update_time` INT NOT NULL DEFAULT '0' COMMENT '更新时间' AFTER `is_new`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'create_time')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " ADD `create_time` INT NOT NULL DEFAULT '0' COMMENT '添加时间' AFTER `is_new`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'is_member_discount')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " ADD `is_member_discount` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '开启会员组价格' AFTER `is_new`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'member_discount_method')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " ADD `member_discount_method` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '折扣方式，0.折扣 1.加减金额' AFTER `is_member_discount`;");
}

if (!pdo_fieldexists('xm_mallv3_agent_level', 'return_percent')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_agent_level') . " ADD `return_percent` DECIMAL(6,2) NOT NULL COMMENT '比例' AFTER `name`;");
}

if (!pdo_fieldexists('xm_mallv3_agent_level', 'upgrade_pullnew')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agent_level` ADD `upgrade_pullnew` INT NULL DEFAULT '0' AFTER `return_percent`;");
}

if (!pdo_fieldexists('xm_mallv3_agent_level', 'upgrademoney')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agent_level` ADD `upgrademoney` DECIMAL(15,2) NULL DEFAULT '0' AFTER `return_percent`;");
}

if (!pdo_fieldexists('xm_mallv3_agent_level', 'upgrade_sales')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agent_level` ADD `upgrade_sales` DECIMAL(15,2) NULL DEFAULT '0' AFTER `return_percent`;");
}

if (!pdo_fieldexists('xm_mallv3_agent_level', 'grade')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agent_level` ADD `grade` INT NOT NULL DEFAULT '0' COMMENT '等级' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_address', 'isDefault')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_address') . " ADD `isDefault` TINYINT(1) NOT NULL COMMENT '是否为默认地址' AFTER `address`;");
}

if (!pdo_fieldexists('xm_mallv3_address', 'latitude')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_address` ADD `latitude` VARCHAR(30) NULL DEFAULT '' AFTER `address`;");
}

if (!pdo_fieldexists('xm_mallv3_address', 'longitude')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_address` ADD `longitude` VARCHAR(30) NULL DEFAULT '' AFTER `address`;");
}

if (!pdo_fieldexists('xm_mallv3_address', 'district_name')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_address` ADD `district_name` VARCHAR(30) NULL DEFAULT '' AFTER `address`;");
}

if (!pdo_fieldexists('xm_mallv3_address', 'city_name')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_address` ADD `city_name` VARCHAR(30) NULL DEFAULT '' AFTER `address`;");
}

if (!pdo_fieldexists('xm_mallv3_address', 'region_name')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_address` ADD `region_name` VARCHAR(60) NULL DEFAULT '' AFTER `address`;");
}

if (!pdo_fieldexists('xm_mallv3_address', 'is_bindingaddress')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_address` ADD `is_bindingaddress` TINYINT(1) NULL DEFAULT '0' COMMENT '会员等级地址' AFTER `district_name`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'is_commission')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `is_commission` TINYINT(1) NULL DEFAULT '0' COMMENT '是否独立佣金' AFTER `is_member_discount`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'commission_method')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `commission_method` TINYINT(1) NULL DEFAULT '0' COMMENT '分佣方式，0.折扣 1.固定金额' AFTER `member_discount_method`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'commission_price')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `commission_price` DECIMAL(15,2) NULL DEFAULT '0.00' COMMENT '独立佣金' AFTER `commission_method`;");
}

if (!pdo_fieldexists('xm_mallv3_member', 'resume')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member') . " ADD `resume` TEXT NOT NULL COMMENT '简介' AFTER `islock`;");
}

if (!pdo_fieldexists('xm_mallv3_member', 'level')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member') . " ADD `level` INT NOT NULL COMMENT '等级' AFTER `telephone`; ");
}

if (!pdo_fieldexists('xm_mallv3_member', 'gid')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member') . " ADD `gid` INT NOT NULL COMMENT '会员组id' AFTER `telephone`;");
}

if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'is_lookprice')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member_auth_group') . " ADD `is_lookprice` TINYINT(1) NOT NULL DEFAULT '1' COMMENT '是否看到价格' AFTER `description`;");
}

if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'discount')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member_auth_group') . " ADD `discount` DECIMAL(10,2) NOT NULL COMMENT '会员组折扣' AFTER `description`;");
}

if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'upgrademoney')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member_auth_group') . " ADD `upgrademoney` DECIMAL(10,2) NOT NULL COMMENT '升级条件' AFTER `description`;");
}

if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'is_bindingaddress')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `is_bindingaddress` TINYINT NULL DEFAULT '0' COMMENT '绑定地址优惠' AFTER `is_buyright`;");
}

if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'expire_day')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `expire_day` INT(5) NULL DEFAULT '0' COMMENT '有效期' AFTER `points`;");
}

if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'start_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `start_time` INT(5) NULL DEFAULT '0' COMMENT '生效时间' AFTER `expire_day`;");
}

if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'grade')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `grade` INT(5) NULL DEFAULT '0' COMMENT '等级' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'is_combination')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `is_combination` TINYINT(1) NOT NULL DEFAULT '0' COMMENT '是否为套装商品:0:否, 1:是' AFTER `status`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'combination_ids')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `combination_ids` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '套装商品id组' AFTER `is_combination`;");
}

if (!pdo_fieldexists('xm_mallv3_order_goods', 'image')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order_goods') . " ADD `image` VARCHAR(100) NOT NULL COMMENT '图片' AFTER `model`;");
}

if (!pdo_fieldexists('xm_mallv3_order_goods', 'is_commission')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_goods` ADD `is_commission` TINYINT(1) NULL DEFAULT '0' COMMENT '是否独立佣金' AFTER `total`;");
}
if (!pdo_fieldexists('xm_mallv3_order_goods', 'commission_method')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_goods` ADD `commission_method` TINYINT(1) NULL DEFAULT '0' COMMENT '分佣方式，0.折扣 1.固定金额' AFTER `is_commission`;");
}
if (!pdo_fieldexists('xm_mallv3_order_goods', 'commission_price')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_goods` ADD `commission_price` DECIMAL(15,2) NULL DEFAULT '0.00' COMMENT '独立佣金' AFTER `commission_method`;");
}

if (!pdo_fieldexists('xm_mallv3_store', 'province')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_store') . " ADD `province` INT NOT NULL COMMENT '省' AFTER `region_name`;");
}

if (!pdo_fieldexists('xm_mallv3_store', 'province_name')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_store') . " ADD `province_name` VARCHAR(30) NOT NULL COMMENT '省名称' AFTER `province`;");
}

if (!pdo_fieldexists('xm_mallv3_store', 'city')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_store') . " ADD `city` INT NOT NULL COMMENT '市' AFTER `province_name`;");
}

if (!pdo_fieldexists('xm_mallv3_store', 'city_name')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_store') . " ADD `city_name` VARCHAR(30) NOT NULL COMMENT '市名称' AFTER `city`;");
}

if (!pdo_fieldexists('xm_mallv3_store', 'district')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_store') . " ADD `district` INT NOT NULL COMMENT '区' AFTER `city_name`;");
}

if (!pdo_fieldexists('xm_mallv3_store', 'district_name')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_store') . " ADD `district_name` VARCHAR(30) NOT NULL COMMENT '区名称' AFTER `district`;");
}

if (!pdo_fieldexists('xm_mallv3_store', 'community')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_store') . " ADD `community` INT NOT NULL COMMENT '镇街' AFTER `district_name`;");
}

if (!pdo_fieldexists('xm_mallv3_store', 'community_name')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_store') . " ADD `community_name` VARCHAR(30) NOT NULL COMMENT '镇街名称' AFTER `community`;");
}

if (!pdo_fieldexists('xm_mallv3_store', 'latitude')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_store') . " ADD `latitude` VARCHAR(20) NOT NULL COMMENT '纬度' AFTER `owner_name`;");
}

if (!pdo_fieldexists('xm_mallv3_store', 'longitude')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_store') . " ADD `longitude` VARCHAR(20) NOT NULL COMMENT '经度' AFTER `latitude`;");
}

if (!pdo_fieldexists('xm_mallv3_store', 'total_income')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_store` ADD `total_income` DECIMAL(9,2) NULL DEFAULT '0.00' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_store', 'income')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_store` ADD `income` DECIMAL(9,2) NULL DEFAULT '0.00' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'is_comment')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order') . " ADD `is_comment` TINYINT(1) NOT NULL COMMENT '是否已评价' AFTER `order_status_id`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'is_express')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order') . " ADD `is_express` TINYINT(1) NOT NULL COMMENT '是否需要物流' AFTER `is_comment`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'sale_count_base')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " ADD `sale_count_base` INT NOT NULL COMMENT '销量基数' AFTER `sale_count`;");
}

if (!pdo_fieldexists('xm_mallv3_order_history', 'image')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order_history') . "ADD `image` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `date_added`;");
}
if (!pdo_fieldexists('xm_mallv3_order_history', 'order_status_id')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_order_history') . "ADD `order_status_id` INT(5) NOT NULL DEFAULT '0' AFTER `order_id`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'viewed_base')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " ADD `viewed_base` INT NOT NULL COMMENT '点击量基数' AFTER `viewed`;");
}

if (!pdo_fieldexists('xm_mallv3_member', 'province')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member') . " ADD `province` VARCHAR(80) NOT NULL COMMENT '省' AFTER `resume`;");
}

if (!pdo_fieldexists('xm_mallv3_member', 'country')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member') . " ADD `country` VARCHAR(80) NOT NULL COMMENT '国家' AFTER `resume`;");
}

if (!pdo_fieldexists('xm_mallv3_member', 'city')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member') . " ADD `city` VARCHAR(80) NOT NULL COMMENT '城市' AFTER `province`;");
}

if (!pdo_fieldexists('xm_mallv3_address', 'street')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_address') . " ADD `street` INT NOT NULL COMMENT '街道' AFTER `isDefault`;");
}

if (!pdo_fieldexists('xm_mallv3_user', 'uid')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_user') . " ADD `uid` INT NOT NULL AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_miaosha_goods', 'begin_date')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_miaosha_goods` ADD `begin_date` INT NOT NULL COMMENT '开始时间' AFTER `price_method`;");
}

if (!pdo_fieldexists('xm_mallv3_miaosha_goods', 'end_date')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_miaosha_goods` ADD `end_date` INT NULL DEFAULT '0' COMMENT '结束时间' AFTER `price_method`;");
}

if (!pdo_fieldexists('xm_mallv3_miaosha_goods', 'status')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_miaosha_goods` ADD `status` TINYINT(1) NULL DEFAULT '1' COMMENT '状态' AFTER `is_level`;");
}

if (!pdo_fieldexists('xm_mallv3_miaosha_goods', 'update_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_miaosha_goods` ADD `update_time` INT NULL DEFAULT '0' AFTER `is_level`;");
}

if (!pdo_fieldexists('xm_mallv3_miaosha_goods', 'create_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_miaosha_goods` ADD `create_time` INT NULL DEFAULT '0' COMMENT '创建时间' AFTER `is_level`;");
}

if (!pdo_fieldexists('xm_mallv3_miaosha_goods', 'ocid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_miaosha_goods` ADD `ocid` INT NULL DEFAULT '0' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_miaosha_goods', 'title')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_miaosha_goods` ADD `title` VARCHAR(100) NULL COMMENT '标题' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_miaosha_goods', 'price')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_miaosha_goods` ADD `price` DECIMAL(15,2) NOT NULL COMMENT '秒杀价格' AFTER `goods_id`;");
}

if (!pdo_fieldexists('xm_mallv3_printer', 'name')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_printer` ADD `name` VARCHAR(60) NULL DEFAULT '' COMMENT '名称' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_printer', 'create_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_printer` ADD `create_time` INT NOT NULL AFTER `sort`;");
}

if (!pdo_fieldexists('xm_mallv3_printer', 'update_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_printer` ADD `update_time` INT NULL DEFAULT '0' AFTER `sort`;");
}

if (!pdo_fieldexists('xm_mallv3_printer', 'sid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_printer` ADD `sid` INT NULL DEFAULT '0' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'sid')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_goods') . " ADD  `sid` INT(11) NOT NULL DEFAULT '0' COMMENT '店铺ID' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_express', 'weid')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_express') . " ADD `weid` INT NOT NULL AFTER `id`;");
}

if (!pdo_fieldexists('xm_mallv3_express', 'weid')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_express') . " ADD `weid` INT NOT NULL AFTER `id`;");
}
if (!pdo_fieldexists('xm_mallv3_paymethod', 'code')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_paymethod') . " ADD `code` VARCHAR(20) NOT NULL AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_withdraw', 'bid')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_withdraw') . " ADD `bid` INT NOT NULL COMMENT '收款帐号' AFTER `uid`;");
}

if (!pdo_fieldexists('xm_mallv3_withdraw', 'sid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_withdraw` ADD `sid` INT NULL DEFAULT '0' COMMENT '店铺ID' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_withdraw', 'mo')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_withdraw` ADD `mo` VARCHAR(20) NULL DEFAULT '' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_user', 'password')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_user` ADD `password` VARCHAR(100) NULL DEFAULT '' AFTER `username`;");
}

if (!pdo_fieldexists('xm_mallv3_user', 'salt')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_user` ADD `salt` VARCHAR(10) NULL DEFAULT '' AFTER `password`;");
}

if (!pdo_fieldexists('xm_mallv3_user', 'create_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_user` ADD `create_time` INT NULL DEFAULT '0' AFTER `role`;");
}

if (!pdo_fieldexists('xm_mallv3_user', 'update_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_user` ADD `update_time` INT NULL DEFAULT '0' AFTER `create_time`;");
}

if (!pdo_fieldexists('xm_mallv3_user', 'role_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_user` ADD `role_id` INT NULL DEFAULT '0' AFTER `mobile`;");
}

if (!pdo_fieldexists('xm_mallv3_user', 'qianming')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_user` ADD `qianming` VARCHAR(200) NULL DEFAULT '' AFTER `touxiang`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `sort` INT NULL DEFAULT '0' AFTER `is_default`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity', 'uuid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `uuid` VARCHAR(68) NULL DEFAULT '' AFTER `id`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity', 'income')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `income` DECIMAL(15,2) NULL DEFAULT '0' COMMENT '当前收入' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity', 'total_income')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `total_income` DECIMAL(15,2) NULL DEFAULT '0' COMMENT '总收入' AFTER `income`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity', 'cate_ids')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `cate_ids` VARCHAR(200) NULL DEFAULT '' COMMENT '可经营的类目' AFTER `total_income`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity', 'province_name')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `province_name` VARCHAR(30) NULL DEFAULT '' AFTER `province_id`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity', 'city_name')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `city_name` VARCHAR(30) NULL DEFAULT '' AFTER `city_id`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity', 'district_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `district_id` INT NULL DEFAULT '0' AFTER `city_name`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity', 'district_name')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `district_name` VARCHAR(30) NULL DEFAULT '' AFTER `district_id`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity', 'end_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `end_time` INT NULL DEFAULT '0' COMMENT '结速时间' AFTER `update_time`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity', 'tel')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `tel` VARCHAR(60) NULL DEFAULT '' AFTER `area_name`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity', 'level')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `level` TINYINT(3) NULL DEFAULT '0' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity', 'areatype')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `areatype` TINYINT(2) NULL DEFAULT '0' AFTER `level`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity', 'house_number')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `house_number` VARCHAR(200) NULL AFTER `area_name`;");
}

if (!pdo_fieldexists('xm_mallv3_user', 'sex')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_user` ADD `sex` TINYINT(1) NULL DEFAULT '0' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `sort` INT NULL DEFAULT '100' AFTER `is_lookprice`;");
}

if (!pdo_fieldexists('xm_mallv3_member', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member` ADD `sort` INT NULL DEFAULT '100' AFTER `customtext`;");
}

if (!pdo_fieldexists('xm_mallv3_agent_level', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agent_level` ADD `sort` INT NULL DEFAULT '0' AFTER `return_percent`;");
}
if (!pdo_fieldexists('xm_mallv3_agent_level', 'is_default')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agent_level` ADD `is_default` TINYINT(1) NULL DEFAULT '0' COMMENT '默认' AFTER `sort`;");
}
if (!pdo_fieldexists('xm_mallv3_department', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_department` ADD `sort` INT NULL DEFAULT '0' AFTER `description`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'is_times')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `is_times` TINYINT(1) NULL DEFAULT '0' COMMENT '0单次服务1次卡2包月包年服务' AFTER `sort`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'timesmum')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `timesmum` INT(3) NOT NULL DEFAULT '0' COMMENT '次数' AFTER `sort`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'is_timing')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `is_timing` TINYINT(1) NULL DEFAULT '0' COMMENT '是否定期服务' AFTER `sort`;");
}

if (!pdo_fieldexists('xm_mallv3_store', 'house_number')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_store` ADD `house_number` VARCHAR(100) NULL DEFAULT '' COMMENT '门牌号' AFTER `address`;");
}

if (!pdo_fieldexists('xm_mallv3_store', 'license')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_store` ADD `license` VARCHAR(255) NULL COMMENT '营业执照' AFTER `store_logo`;");
}

if (!pdo_fieldexists('xm_mallv3_member', 'sid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member` ADD `sid` INT NULL DEFAULT '0' COMMENT '店铺ID' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_user', 'sid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_user` ADD `sid` INT NULL DEFAULT '0' COMMENT '店id' AFTER `did`;");
}

if (!pdo_fieldexists('xm_mallv3_user', 'ocid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_user` ADD `ocid` INT NULL DEFAULT '0' AFTER `sid`;");
}
if (!pdo_fieldexists('xm_mallv3_user', 'tzid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_user` ADD `tzid` INT NULL DEFAULT '0' COMMENT '团长id' AFTER `ocid`;");
}
if (!pdo_fieldexists('xm_mallv3_user', 'tid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_user` ADD `tid` INT NULL DEFAULT '0' COMMENT '师傅id' AFTER `did`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'is_times')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `is_times` TINYINT(1) NULL DEFAULT '0' COMMENT '0单次服务1次卡2包月包年服务' AFTER `deliverymode`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'is_errands')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `is_errands` TINYINT(1) NULL DEFAULT '0' COMMENT '跑腿订单' AFTER `is_times`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'distance')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `distance` DECIMAL(15,2) NULL DEFAULT '0' COMMENT '距离' AFTER `is_times`;");
}

if (!pdo_fieldexists('xm_mallv3_bottom_menu', 'is_submitaudit')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_bottom_menu` ADD `is_submitaudit` TINYINT(1) NULL DEFAULT '0' AFTER `plugin`;");
}

if (!pdo_fieldexists('xm_mallv3_bottom_menu', 'module')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_bottom_menu` ADD `module` VARCHAR(30) NULL AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_register_field', 'is_input')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_register_field` ADD `is_input` TINYINT(1) NULL DEFAULT '1' COMMENT '是否可以输入' AFTER `update_time`;");
}

if (!pdo_fieldexists('xm_mallv3_register_field', 'is_import')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_register_field` ADD `is_import` TINYINT(1) NULL DEFAULT '0' COMMENT '导入导出' AFTER `is_front`;");
}

if (!pdo_fieldexists('xm_mallv3_register_field', 'ptype')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_register_field` ADD `ptype` VARCHAR(30) NULL DEFAULT '' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_register_field', 'valuerules')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_register_field` ADD `valuerules` VARCHAR(60) NULL DEFAULT '' COMMENT '验证规则' AFTER `inputtype`;");
}

if (!pdo_fieldexists('xm_mallv3_register_field', 'is_frontinput')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_register_field` ADD `is_frontinput` TINYINT(1) NULL DEFAULT '0' COMMENT '是否前端输入' AFTER `is_input`;");
}

if (!pdo_fieldexists('xm_mallv3_paymethod', 'settings')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_paymethod` ADD `settings` TEXT NULL DEFAULT '' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_paymethod', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_paymethod` ADD `sort` INT(3) NULL DEFAULT '100' AFTER `status`;");
}

if (!pdo_fieldexists('xm_mallv3_order_staff', 'begin_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_staff` ADD `begin_time` INT NOT NULL COMMENT '开始时间' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_order_staff', 'end_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_staff` ADD `end_time` INT NOT NULL COMMENT '结速时间' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_order_goods', 'sku')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_goods` ADD `sku` TEXT NULL DEFAULT '' AFTER `image`;");
}

if (!pdo_fieldexists('xm_mallv3_order_goods', 'time_amount')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_goods` ADD `time_amount` INT NULL DEFAULT '0' COMMENT '服务时长分钟' AFTER `price`;");
}

if (!pdo_fieldexists('xm_mallv3_order_goods', 'is_timer')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_goods` ADD `is_timer` TINYINT(1) NULL DEFAULT '0' COMMENT '计时' AFTER `price`;");
}

if (!pdo_fieldexists('xm_mallv3_cart', 'sku')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_cart` ADD `sku` TEXT NULL DEFAULT '' AFTER `goods_id`;");
}

if (!pdo_fieldexists('xm_mallv3_diy_page', 'modulelist')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_diy_page` ADD `modulelist` LONGTEXT NULL COMMENT '模块' AFTER `page_data`;");
}

if (!pdo_fieldexists('xm_mallv3_diy_page', 'pagebase')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_diy_page` ADD `pagebase` TEXT NULL COMMENT '页面基础设置' AFTER `page_data`;");
}

if (!pdo_fieldexists('xm_mallv3_diy_page', 'version')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_diy_page` ADD `version` TINYINT(1) NULL AFTER `is_index`;");
}

if (!pdo_fieldexists('xm_mallv3_diy_page', 'is_submitaudit')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_diy_page` ADD `is_submitaudit` TINYINT(1) NULL DEFAULT '0' COMMENT '1为审核页面' AFTER `is_index`;");
}

if (!pdo_fieldexists('xm_mallv3_diy_page', 'ocid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_diy_page` ADD `ocid` INT NULL DEFAULT '0' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_technical', 'touxiang')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `touxiang` VARCHAR(200) NULL COMMENT '头像' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_technical', 'customtext')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `customtext` TEXT NULL DEFAULT '' AFTER `house_number`;");
}

if (!pdo_fieldexists('xm_mallv3_technical', 'sid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `sid` INT NULL DEFAULT '0' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_technical', 'ocid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `ocid` INT NULL DEFAULT '0' AFTER `sid`;");
}

if (!pdo_fieldexists('xm_mallv3_bottom_menu', 'hump')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_bottom_menu` ADD `hump` TINYINT(1) NULL DEFAULT '0' COMMENT '是否凸起' AFTER `iconactive`;");
}

if (!pdo_fieldexists('xm_mallv3_bottom_menu', 'is_index')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_bottom_menu` ADD `is_index` TINYINT(1) NULL DEFAULT '0' AFTER `plugin`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'cat_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `cat_id` INT NULL DEFAULT '0' COMMENT '分类' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_agent', 'customtext')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agent` ADD `customtext` TEXT NULL AFTER `deal_num`;");
}

if (!pdo_fieldexists('xm_mallv3_agent', 'touxiang')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agent` ADD `touxiang` VARCHAR(200) NULL DEFAULT '' AFTER `tel`;");
}

if (!pdo_fieldexists('xm_mallv3_agent', 'income')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agent` ADD `income` DECIMAL(9,2) NULL DEFAULT '0' COMMENT '收入' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_agent', 'total_income')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agent` ADD `total_income` DECIMAL(9,2) NULL DEFAULT '0' COMMENT '总收入' AFTER `weid`;");
}
if (!pdo_fieldexists('xm_mallv3_agent', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agent` ADD `sort` INT NULL DEFAULT '100' AFTER `create_time`;");
}

if (!pdo_fieldexists('xm_mallv3_store', 'cate_ids')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_store` ADD `cate_ids` VARCHAR(200) NULL DEFAULT '' COMMENT '可接的服务' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'is_skumore')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `is_skumore` TINYINT(1) NULL DEFAULT '0' COMMENT '是否多规格下单' AFTER `is_combination`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'is_additional')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `is_additional` TINYINT(1) NULL DEFAULT '0' COMMENT '是否支持尾款' AFTER `is_skumore`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'original_price')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `original_price` DECIMAL(15,2) NULL DEFAULT '0' COMMENT '原价' AFTER `price`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'time_amount')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `time_amount` INT NULL DEFAULT '0' COMMENT '服务时长分钟' AFTER `price`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'takeeffecttime')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `takeeffecttime` INT(6) NULL DEFAULT '0' COMMENT '购买后多久生效' AFTER `timesmum`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'additional')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `additional` DECIMAL(15,2) NULL DEFAULT '0' COMMENT '尾款' AFTER `total`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'is_additional')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `is_additional` TINYINT(1) NULL DEFAULT '0' COMMENT '是否支持尾款' AFTER `total`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'additional_pay_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `additional_pay_time` INT NULL DEFAULT '0' COMMENT '尾款支付时间' AFTER `pay_time`;");
}

if (!pdo_fieldexists('xm_mallv3_technical', 'level')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `level` INT(3) NULL DEFAULT '0' COMMENT '等级' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_technical', 'category_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `category_id` INT(3) NULL DEFAULT '0' COMMENT '分类' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_technical', 'service_times')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `service_times` INT NULL DEFAULT '0' COMMENT '服务次数' AFTER `touxiang`;");
}

if (!pdo_fieldexists('xm_mallv3_technical', 'introduction')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `introduction` TEXT NULL COMMENT '简介' AFTER `email`;");
}

if (!pdo_fieldexists('xm_mallv3_technical', 'workunits')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `workunits` VARCHAR(200) NULL DEFAULT '' COMMENT '工作单位' AFTER `email`;");
}

if (!pdo_fieldexists('xm_mallv3_miaosha_goods', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_miaosha_goods` ADD `sort` INT NULL DEFAULT '60' AFTER `update_time`;");
}

if (!pdo_fieldexists('xm_mallv3_tuan_goods', 'sale_count')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuan_goods` ADD `sale_count` INT NULL DEFAULT '0' COMMENT '销量' AFTER `goods_id`;");
}

if (!pdo_fieldexists('xm_mallv3_tuan_goods', 'tuan_max_price')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuan_goods` ADD `tuan_max_price` DECIMAL(15,2) NULL DEFAULT '0' COMMENT '最高拼团价' AFTER `price`;");
}

if (!pdo_fieldexists('xm_mallv3_tuan_goods', 'tuan_min_price')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuan_goods` ADD `tuan_min_price` DECIMAL(15,2) NULL DEFAULT '0' COMMENT '最低拼团价' AFTER `price`;");
}

if (!pdo_fieldexists('xm_mallv3_category', 'title')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_category') . " ADD `title` VARCHAR(50) NOT NULL COMMENT '标题' AFTER `pid`;");
}
if (!pdo_fieldexists('xm_mallv3_category', 'status')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_category') . " ADD `status` TINYINT(1) NOT NULL DEFAULT '1';");
}
if (!pdo_fieldexists('xm_mallv3_brand', 'status')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_brand') . " ADD `status` TINYINT(1) NOT NULL DEFAULT '1';");
}
if (!pdo_fieldexists('xm_mallv3_member', 'name')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member') . " ADD `name` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `username`;");
}
if (!pdo_fieldexists('xm_mallv3_member', 'address')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member') . " ADD `address` VARCHAR(64) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `name`;");
}
if (!pdo_fieldexists('xm_mallv3_member', 'category_id')) {
  pdo_run("ALTER TABLE " . tablename('xm_mallv3_member') . " ADD `category_id` INT NOT NULL DEFAULT '0' COMMENT '会员类别' AFTER `city`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'points_price')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `points_price` DECIMAL(15,2) NULL DEFAULT '0' COMMENT '积分可以抵扣金额' AFTER `pay_points`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'shipping_district_name')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `shipping_district_name` VARCHAR(30) NULL DEFAULT '' AFTER `shipping_tel`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'shipping_city_name')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `shipping_city_name` VARCHAR(30) NULL DEFAULT '' AFTER `shipping_tel`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'shipping_province_name')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `shipping_province_name` VARCHAR(30) NULL DEFAULT '' AFTER `shipping_tel`;");
}
if (!pdo_fieldexists('xm_mallv3_users_roles', 'is_console')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_users_roles` ADD `is_console` TINYINT(1) NULL DEFAULT '0' COMMENT '控制台' AFTER `description`;");
}
if (!pdo_fieldexists('xm_mallv3_users_roles', 'is_allrole')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_users_roles` ADD `is_allrole` TINYINT(1) NULL DEFAULT '0' AFTER `access`;");
}
if (!pdo_fieldexists('xm_mallv3_users_roles', 'ocid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_users_roles` ADD `ocid` INT NULL DEFAULT '0' AFTER `weid`;");
}
if (!pdo_fieldexists('xm_mallv3_users_roles', 'tzid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_users_roles` ADD `tzid` INT NULL DEFAULT '0' COMMENT '团长id' AFTER `ocid`;");
}
if (!pdo_fieldexists('xm_mallv3_users_roles', 'sid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_users_roles` ADD `sid` INT NULL DEFAULT '0' AFTER `weid`;");
}
if (!pdo_fieldexists('xm_mallv3_openid', 'uuid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_openid` ADD `uuid` VARCHAR(68) NULL DEFAULT '' AFTER `uid`;");
}
if (!pdo_fieldexists('xm_mallv3_member', 'uuid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member` ADD `uuid` VARCHAR(68) NULL DEFAULT '' AFTER `id`;");
}
if (!pdo_fieldexists('xm_mallv3_user', 'uuid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_user` ADD `uuid` VARCHAR(68) NULL DEFAULT '' AFTER `id`;");
}
if (!pdo_fieldexists('xm_mallv3_technical', 'uuid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `uuid` VARCHAR(68) NULL DEFAULT '' AFTER `id`;");
}
if (!pdo_fieldexists('xm_mallv3_store', 'uuid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_store` ADD `uuid` VARCHAR(68) NULL DEFAULT '' AFTER `id`;");
}
if (!pdo_fieldexists('xm_mallv3_store', 'ocid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_store` ADD `ocid` INT NULL DEFAULT '0' AFTER `uid`;");
}
if (!pdo_fieldexists('xm_mallv3_order_staff', 'uuid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_staff` ADD `uuid` VARCHAR(68) NULL DEFAULT '' AFTER `id`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'points_price')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `points_price` DECIMAL(15,2) NULL DEFAULT '0' COMMENT '积分可以抵扣金额' AFTER `pay_points`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'points_method')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `points_method` TINYINT(1) NULL DEFAULT '0' AFTER `costs`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'district_name')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `district_name` VARCHAR(30) NULL DEFAULT '' AFTER `viewed_base`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'city_name')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `city_name` VARCHAR(30) NULL DEFAULT '' AFTER `viewed_base`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'province_name')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `province_name` VARCHAR(30) NULL DEFAULT '' AFTER `viewed_base`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'videotype')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `videotype` TINYINT(2) NULL DEFAULT '0' COMMENT '1服务器2腾讯视频' AFTER `image`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'videourl')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `videourl` VARCHAR(200) NULL DEFAULT '' AFTER `videotype`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'videoid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `videoid` VARCHAR(100) NULL DEFAULT '' AFTER `videotype`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'keyword')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `keyword` VARCHAR(100) NULL DEFAULT '' AFTER `name`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'is_timer')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `is_timer` TINYINT(1) NULL DEFAULT '0' COMMENT '计时' AFTER `price`;");
}
if (!pdo_fieldexists('xm_mallv3_user', 'lastweid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_user` ADD `lastweid` INT NULL DEFAULT '0' COMMENT '最后进入的平台' AFTER `weid`;");
}
if (!pdo_fieldexists('xm_mallv3_user', 'login_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_user` ADD `login_time` INT NULL COMMENT '最近登录时间' AFTER `remark`;");
}
if (!pdo_fieldexists('xm_mallv3_user', 'login_ip')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_user` ADD `login_ip` VARCHAR(30) NULL COMMENT '最近登录IP' AFTER `remark`;");
}
if (!pdo_fieldexists('xm_mallv3_technical', 'is_business')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `is_business` TINYINT(1) NULL DEFAULT '1' COMMENT '是否营业' AFTER `create_time`;");
}
if (!pdo_fieldexists('xm_mallv3_technical', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `sort` INT NULL DEFAULT '100' AFTER `is_business`;");
}
if (!pdo_fieldexists('xm_mallv3_store', 'is_business')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_store` ADD `is_business` TINYINT(1) NULL DEFAULT '1' COMMENT '是否营业' AFTER `end_time`;");
}
if (!pdo_fieldexists('xm_mallv3_message', 'params')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_message` ADD `params` TEXT NULL AFTER `content`;");
}
if (!pdo_fieldexists('xm_mallv3_coupon', 'ptype')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon` ADD `ptype` TINYINT(1) NULL DEFAULT '1' AFTER `id`;");
}
if (!pdo_fieldexists('xm_mallv3_coupon_receive', 'ptype')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon_receive` ADD `ptype` TINYINT(1) NULL DEFAULT '1' AFTER `id`;");
}
if (!pdo_fieldexists('xm_mallv3_coupon_receive', 'number')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon_receive` ADD `number` INT(5) NULL DEFAULT '1' AFTER `name`;");
}
if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'points')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `points` INT(8) NULL DEFAULT '0' COMMENT '购买得积分' AFTER `title`;");
}
if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'coupon_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `coupon_id` INT NULL DEFAULT '0' COMMENT '赠送优惠券' AFTER `title`;");
}
if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'number')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `number` INT(8) NULL DEFAULT '1' COMMENT '送优惠券数量' AFTER `title`;");
}
if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'upgrade_goods_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `upgrade_goods_id` INT NULL DEFAULT '0' COMMENT '购买指定商品升级' AFTER `upgrademoney`;");
}
if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'upgrade_invitation')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `upgrade_invitation` INT NULL COMMENT '直推多少人升级' AFTER `upgrade_goods_id`;");
}
if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'upgrade_consumption')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `upgrade_consumption` DECIMAL(15,2) NULL DEFAULT '0' COMMENT '累计消费多少钱升级' AFTER `upgrade_invitation`;");
}
if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'auto_upgrade_where')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `auto_upgrade_where` TINYINT(2) NULL DEFAULT '0' COMMENT '自动升级条件' AFTER `upgrade_consumption`;");
}
if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'is_buyright')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `is_buyright` TINYINT(1) NULL DEFAULT '1' COMMENT '商品购买权' AFTER `is_lookprice`;");
}
if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'is_default')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `is_default` TINYINT(1) NULL DEFAULT '0' COMMENT '默认' AFTER `status`;");
}
if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'commission_method')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `commission_method` TINYINT(1) NULL DEFAULT '0' COMMENT '佣金结算类型' AFTER `upgrademoney`;");
}
if (!pdo_fieldexists('xm_mallv3_platform', 'loginbgimg')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_platform` ADD `loginbgimg` VARCHAR(200) NULL AFTER `logo`;");
}
if (!pdo_fieldexists('xm_mallv3_platform', 'scheme_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_platform` ADD `scheme_id` INT NULL DEFAULT '0' COMMENT '方案' AFTER `title`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'tuan_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `tuan_id` INT NULL DEFAULT '0' COMMENT '拼团活动id' AFTER `deliverymode`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'tuan_found_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `tuan_found_id` INT NULL DEFAULT '0' COMMENT '开团id' AFTER `deliverymode`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'ms_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `ms_id` INT NULL DEFAULT '0' COMMENT '秒杀id' AFTER `tuan_id`;");
}
if (!pdo_fieldexists('xm_mallv3_miaosha_goods', 'sale_count')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_miaosha_goods` ADD `sale_count` INT NULL DEFAULT '0' AFTER `goods_id`;");
}
if (!pdo_fieldexists('xm_mallv3_agent_level', 'is_teamaward')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agent_level` ADD `is_teamaward` TINYINT(1) NULL DEFAULT '0' AFTER `sort`;");
}
if (!pdo_fieldexists('xm_mallv3_incomelog', 'description')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_incomelog` ADD `description` VARCHAR(200) NULL AFTER `percentremark`;");
}
if (!pdo_fieldexists('xm_mallv3_technical_incomelog', 'description')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical_incomelog` ADD `description` VARCHAR(200) NULL AFTER `percentremark`;");
}
if (!pdo_fieldexists('xm_mallv3_store_incomelog', 'description')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_store_incomelog` ADD `description` VARCHAR(200) NULL AFTER `percentremark`;");
}
if (!pdo_fieldexists('xm_mallv3_operatingcity_incomelog', 'description')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity_incomelog` ADD `description` VARCHAR(200) NULL AFTER `percentremark`;");
}
if (!pdo_fieldexists('xm_mallv3_operatingcity', 'region_name')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `region_name` VARCHAR(200) NULL COMMENT '位置名称' AFTER `cate_ids`;");
}
if (!pdo_fieldexists('xm_mallv3_store', 'customtext')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_store` ADD `customtext` TEXT NULL AFTER `content`;");
}
if (!pdo_fieldexists('xm_mallv3_operatingcity', 'customtext')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `customtext` TEXT NULL AFTER `tel`;");
}
if (!pdo_fieldexists('xm_mallv3_operatingcity', 'settings')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `settings` TEXT NULL COMMENT '配置' AFTER `customtext`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'complete_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `complete_time` INT NULL DEFAULT '0' COMMENT '完成服务时间' AFTER `pay_time`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'sendto')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `sendto` TINYINT(1) NULL DEFAULT '0' COMMENT '1商家2平台师傅' AFTER `deliverymode`;");
}
if (!pdo_fieldexists('xm_mallv3_agent_teama_ward', 'beginamount')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agent_teama_ward` ADD `beginamount` DECIMAL(15,2) NULL COMMENT '业绩开始' AFTER `weid`;");
}
if (!pdo_fieldexists('xm_mallv3_agent_teama_ward', 'endamount')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agent_teama_ward` ADD `endamount` DECIMAL(15,2) NULL COMMENT '最高业绩' AFTER `beginamount`;");
}
if (!pdo_fieldexists('xm_mallv3_comment', 'technical_uuid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_comment` ADD `technical_uuid` VARCHAR(68) NULL DEFAULT '' AFTER `goods_id`;");
}
if (!pdo_fieldexists('xm_mallv3_comment', 'cat_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_comment` ADD `cat_id` INT NULL DEFAULT '0' COMMENT '分类ID' AFTER `order_id`;");
}
if (!pdo_fieldexists('xm_mallv3_coupon', 'sid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon` ADD `sid` INT NULL DEFAULT '0' AFTER `weid`;");
}
if (!pdo_fieldexists('xm_mallv3_coupon', 'ocid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon` ADD `ocid` INT NULL DEFAULT '0' AFTER `weid`;");
}
if (!pdo_fieldexists('xm_mallv3_coupon', 'buy_price')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon` ADD `buy_price` DECIMAL(10,2) NULL DEFAULT '0' COMMENT '购买价格' AFTER `name`;");
}

if (!pdo_fieldexists('xm_mallv3_coupon', 'condition_type')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon` ADD `condition_type` TINYINT(1) NULL DEFAULT '0' COMMENT '使用门槛0无门槛1有门槛' AFTER `discount`;");
}
if (!pdo_fieldexists('xm_mallv3_coupon', 'use_goods')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon` ADD `use_goods` TINYINT(1) NULL DEFAULT '0' COMMENT '适用商品' AFTER `coupon_type`;");
}
if (!pdo_fieldexists('xm_mallv3_coupon', 'cat_ids')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon` ADD `cat_ids` VARCHAR(200) NULL DEFAULT '' COMMENT '适用品类' AFTER `use_goods`;");
}
if (!pdo_fieldexists('xm_mallv3_coupon', 'goods_ids')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon` ADD `goods_ids` TEXT NULL COMMENT '适用产品编号' AFTER `cat_ids`;");
}

if (!pdo_fieldexists('xm_mallv3_coupon_receive', 'buy_price')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon_receive` ADD `buy_price` DECIMAL(10,2) NULL COMMENT '购买价格' AFTER `name`;");
}
if (!pdo_fieldexists('xm_mallv3_coupon_receive', 'condition_type')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon_receive` ADD `condition_type` TINYINT(1) NULL DEFAULT '0' COMMENT '使用门槛0无门槛1有门槛' AFTER `discount`;");
}
if (!pdo_fieldexists('xm_mallv3_coupon_receive', 'use_goods')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon_receive` ADD `use_goods` TINYINT(1) NULL DEFAULT '0' COMMENT '适用商品' AFTER `condition_type`;");
}
if (!pdo_fieldexists('xm_mallv3_coupon_receive', 'cat_ids')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon_receive` ADD `cat_ids` VARCHAR(200) NULL DEFAULT '' COMMENT '适用品类' AFTER `use_goods`;");
}
if (!pdo_fieldexists('xm_mallv3_coupon_receive', 'goods_ids')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon_receive` ADD `goods_ids` TEXT NULL COMMENT '适用产品编号' AFTER `cat_ids`;");
}

if (!pdo_fieldexists('xm_mallv3_tuan_goods', 'sid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuan_goods` ADD `sid` INT NULL DEFAULT '0' AFTER `weid`;");
}
if (!pdo_fieldexists('xm_mallv3_miaosha_goods', 'sid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_miaosha_goods` ADD `sid` INT NULL DEFAULT '0' AFTER `weid`;");
}
if (!pdo_fieldexists('xm_mallv3_bargain_goods', 'sid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_bargain_goods` ADD `sid` INT NULL DEFAULT '0' AFTER `weid`;");
}
if (!pdo_fieldexists('xm_mallv3_users_sessions', 'last_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_users_sessions` ADD `last_time` INT NULL DEFAULT '0' AFTER `expire_time`;");
}
if (!pdo_fieldexists('xm_mallv3_users_sessions', 'is_error')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_users_sessions` ADD `is_error` TINYINT(1) NULL DEFAULT '0' AFTER `status`;");
}
if (!pdo_fieldexists('xm_mallv3_bottom_menu', 'tid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_bottom_menu` ADD `tid` INT NULL DEFAULT '0' AFTER `module`;");
}
if (!pdo_fieldexists('xm_mallv3_bottom_menu', 'zdyLinktype')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_bottom_menu` ADD `zdyLinktype` VARCHAR(20) NULL DEFAULT '' COMMENT '自定义类型' AFTER `customurl`;");
}
if (!pdo_fieldexists('xm_mallv3_bottom_menu', 'zdyappid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_bottom_menu` ADD `zdyappid` VARCHAR(20) NULL DEFAULT '' AFTER `zdyLinktype`;");
}
if (!pdo_fieldexists('xm_mallv3_bottom_menu', 'params')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_bottom_menu` ADD `params` VARCHAR(100) NULL DEFAULT '' AFTER `url`;");
}
if (!pdo_fieldexists('xm_mallv3_member_bankcard', 'collect_type')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_bankcard` ADD `collect_type` VARCHAR(20) NULL COMMENT '帐号类型' AFTER `uid`;");
}
if (!pdo_fieldexists('xm_mallv3_agreement', 'is_v3')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agreement` ADD `is_v3` TINYINT(1) NULL DEFAULT '1' AFTER `status`;");
}
if (!pdo_fieldexists('xm_mallv3_agreement', 'is_v2')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agreement` ADD `is_v2` TINYINT(1) NULL DEFAULT '1' AFTER `status`;");
}
if (!pdo_fieldexists('xm_mallv3_kefu_seating', 'setopenidqrcode')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_kefu_seating` ADD `setopenidqrcode` VARCHAR(255) NULL DEFAULT '' AFTER `touxiang`;");
}
if (!pdo_fieldexists('xm_mallv3_category', 'is_binding')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_category` ADD `is_binding` TINYINT(1) NULL DEFAULT '0' COMMENT '选定师傅才能下单' AFTER `title`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'start_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `start_time` INT NULL DEFAULT '0' COMMENT '开始服务时间' AFTER `pay_time`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'pay_from')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `pay_from` VARCHAR(20) NULL DEFAULT '' COMMENT '来自那个客户端' AFTER `payment_code`;");
}
if (!pdo_fieldexists('xm_mallv3_agreement', 'is_v6')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_agreement` ADD `is_v6` TINYINT(1) NULL DEFAULT '1' AFTER `is_v3`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'hid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `hid` INT NULL DEFAULT '0' COMMENT '医院id' AFTER `sid`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'hcat_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `hcat_id` INT NULL DEFAULT '0' COMMENT '医院科室id' AFTER `cat_id`;");
}
if (!pdo_fieldexists('xm_mallv3_technical', 'hid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `hid` INT NULL COMMENT '医院id' AFTER `sid`;");
}
if (!pdo_fieldexists('xm_mallv3_technical', 'viewed')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `viewed` INT NULL DEFAULT '1' AFTER `service_times`;");
}
if (!pdo_fieldexists('xm_mallv3_order_staff', 'yue_begin_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_staff` ADD `yue_begin_time` INT NULL DEFAULT '0' AFTER `title`;");
}
if (!pdo_fieldexists('xm_mallv3_order_staff', 'yue_end_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_staff` ADD `yue_end_time` INT NULL DEFAULT '0' AFTER `yue_begin_time`;");
}
if (!pdo_fieldexists('xm_mallv3_order_staff', 'is_complete')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_staff` ADD `is_complete` TINYINT(1) NULL DEFAULT '0' AFTER `begin_time`;");
}
if (!pdo_fieldexists('xm_mallv3_order_staff', 'is_confirm')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_staff` ADD `is_confirm` TINYINT(1) NULL DEFAULT '0' AFTER `is_complete`;");
}
if (!pdo_fieldexists('xm_mallv3_order_staff', 'is_settlement')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_staff` ADD `is_settlement` TINYINT(1) NULL DEFAULT '0' COMMENT '结算' AFTER `is_confirm`;");
}
if (!pdo_fieldexists('xm_mallv3_technical', 'end_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `end_time` INT NULL DEFAULT '0' COMMENT '到期时间' AFTER `create_time`;");
}
if (!pdo_fieldexists('xm_mallv3_withdraw', 'pay_from')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_withdraw` ADD `pay_from` VARCHAR(20) NOT NULL COMMENT '来自那个客户端' AFTER `create_time`;");
}
if (!pdo_fieldexists('xm_mallv3_tuan_goods', 'is_luckydraw')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuan_goods` ADD `is_luckydraw` TINYINT(1) NULL DEFAULT '0' COMMENT '抽奖团' AFTER `people_num`;");
}
if (!pdo_fieldexists('xm_mallv3_tuan_goods', 'luckydraw_num')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuan_goods` ADD `luckydraw_num` INT(6) NULL DEFAULT '0' COMMENT '中奖人数' AFTER `is_luckydraw`;");
}
if (!pdo_fieldexists('xm_mallv3_tuan_goods', 'robot_num')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuan_goods` ADD `robot_num` INT(6) NULL DEFAULT '0' COMMENT '凑数机器人' AFTER `people_num`;");
}
if (!pdo_fieldexists('xm_mallv3_tuan_goods', 'not_winning_ptype')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuan_goods` ADD `not_winning_ptype` TINYINT(1) NULL DEFAULT '0' AFTER `luckydraw_num`;");
}
if (!pdo_fieldexists('xm_mallv3_tuan_goods', 'not_winning_redenvelope')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuan_goods` ADD `not_winning_redenvelope` DECIMAL(15,2) NULL DEFAULT '0' COMMENT '没拼中发红包金额' AFTER `not_winning_ptype`;");
}
if (!pdo_fieldexists('xm_mallv3_tuan_goods', 'not_winning_points')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuan_goods` ADD `not_winning_points` INT NULL DEFAULT '0' COMMENT '没拼中送积分' AFTER `not_winning_redenvelope`;");
}
if (!pdo_fieldexists('xm_mallv3_tuan_goods', 'not_winning_coupon_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuan_goods` ADD `not_winning_coupon_id` INT NULL DEFAULT '0' COMMENT '没拼中送优惠券' AFTER `not_winning_points`;");
}
if (!pdo_fieldexists('xm_mallv3_tuan_goods', 'auto_initiate')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuan_goods` ADD `auto_initiate` TINYINT(1) NULL DEFAULT '0' COMMENT '自动开团' AFTER `people_num`;");
}
if (!pdo_fieldexists('xm_mallv3_tuan_found', 'sid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuan_found` ADD `sid` INT NULL DEFAULT '0' AFTER `weid`;");
}
if (!pdo_fieldexists('xm_mallv3_tuan_found', 'ocid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuan_found` ADD `ocid` INT NULL DEFAULT '0' COMMENT '城市id' AFTER `sid`;");
}
if (!pdo_fieldexists('xm_mallv3_rotarytable_prize', 'points')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_rotarytable_prize` ADD `points` INT NULL DEFAULT '0' COMMENT '积分' AFTER `price`;");
}
if (!pdo_fieldexists('xm_mallv3_tuan_follow', 'is_refund')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuan_follow` ADD `is_refund` TINYINT(1) NULL DEFAULT '0' AFTER `pay_time`;");
}
if (!pdo_fieldexists('xm_mallv3_uuid_relation', 'ptype')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_uuid_relation` ADD `ptype` VARCHAR(20) NULL DEFAULT '' AFTER `weid`;");
}
if (!pdo_fieldexists('xm_mallv3_order_status', 'sort')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_status` ADD `sort` INT(6) NULL DEFAULT '0' AFTER `status`;");
}
if (!pdo_fieldexists('xm_mallv3_order_status', 'ptype')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_status` ADD `ptype` VARCHAR(20) NULL DEFAULT '' AFTER `weid`;");
}
if (!pdo_fieldexists('xm_mallv3_order_status', 'val')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_status` ADD `val` INT(6) NULL DEFAULT '0' AFTER `id`;");
}
if (!pdo_fieldexists('xm_mallv3_order_status', 'nextval')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_status` ADD `nextval` VARCHAR(50) NULL DEFAULT '' COMMENT '可操件的下一步' AFTER `val`;");
}
if (!pdo_fieldexists('xm_mallv3_order_status', 'is_sys')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_status` ADD `is_sys` TINYINT(1) NULL DEFAULT '0' AFTER `name`;");
}
if (!pdo_fieldexists('xm_mallv3_order_status', 'create_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_status` ADD `create_time` INT NULL DEFAULT '0' AFTER `sort`;");
}
if (!pdo_fieldexists('xm_mallv3_order_status', 'update_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_status` ADD `update_time` INT NULL DEFAULT '0' AFTER `sort`;");
}
if (!pdo_fieldexists('xm_mallv3_order_status', 'custom_name')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_status` ADD `custom_name` VARCHAR(30) NULL DEFAULT '' COMMENT '自定义名称' AFTER `name`;");
}
if (!pdo_fieldexists('xm_mallv3_technical', 'comment')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `comment` INT NULL DEFAULT '0' COMMENT '评价数量' AFTER `service_times`;");
}
if (!pdo_fieldexists('xm_mallv3_uploadminiprogram', 'ptype')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_uploadminiprogram` ADD `ptype` INT NOT NULL AFTER `weid`;");
}
if (!pdo_fieldexists('xm_mallv3_uploadminiprogram', 'is_up')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_uploadminiprogram` ADD `is_up` TINYINT(1) NULL DEFAULT '0' AFTER `update_time`;");
}
if (!pdo_fieldexists('xm_mallv3_order', 'cate_ids')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `cate_ids` VARCHAR(200) NULL DEFAULT '' AFTER `deliverymode`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'otype')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `otype` INT(3) NULL DEFAULT '0' AFTER `ptype`;");
}

if (!pdo_fieldexists('xm_mallv3_partner_level', 'is_default')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_partner_level` ADD `is_default` TINYINT(1) NULL DEFAULT '0' AFTER `sort`;");
}

if (!pdo_fieldexists('xm_mallv3_partner_level', 'is_default')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_partner_level` ADD `is_default` TINYINT(1) NULL DEFAULT '0' AFTER `sort`;");
}
if (!pdo_fieldexists('xm_mallv3_test', 'title')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_test` ADD `title` VARCHAR(200) NULL DEFAULT '' AFTER `id`;");
}

if (!pdo_fieldexists('xm_mallv3_order_goods', 'cat_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_goods` ADD `cat_id` INT NULL DEFAULT '0' AFTER `goods_id`;");
}

if (!pdo_fieldexists('xm_mallv3_technical', 'service_times_base')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `service_times_base` INT NULL DEFAULT '0' COMMENT '接单基数' AFTER `service_times`;");
}

if (!pdo_fieldexists('xm_mallv3_technical', 'comment_base')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `comment_base` INT NULL DEFAULT '0' COMMENT '评价基数' AFTER `comment`;");
}

if (!pdo_fieldexists('xm_mallv3_technical', 'viewed_base')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `viewed_base` INT NULL DEFAULT '0' COMMENT '人气基数' AFTER `viewed`;");
}

if (!pdo_fieldexists('xm_mallv3_technical', 'videourl')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `videourl` VARCHAR(200) NULL AFTER `touxiang`;");
}

if (!pdo_fieldexists('xm_mallv3_technical', 'photoalbum')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `photoalbum` TEXT NULL COMMENT '相册' AFTER `touxiang`;");
}

if (!pdo_fieldexists('xm_mallv3_category', 'goodsnum')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_category` ADD `goodsnum` INT NULL DEFAULT '0' COMMENT '商品数量' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_goods_member_discount', 'addsubtract')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods_member_discount` ADD `addsubtract` TINYINT(1) NULL DEFAULT '0' COMMENT '0:加1减' AFTER `discount_method`;");
}

if (!pdo_fieldexists('xm_mallv3_goods_member_discount', 'is_free')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods_member_discount` ADD `is_free` TINYINT(1) NULL DEFAULT '0' COMMENT '免费' AFTER `price`;");
}

if (!pdo_fieldexists('xm_mallv3_service_time', 'quantity')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_service_time` ADD `quantity` INT NULL DEFAULT '0' COMMENT '可约数量' AFTER `end_time`;");
}

if (!pdo_fieldexists('xm_mallv3_service_time', 'ptype')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_service_time` ADD `ptype` INT NULL DEFAULT '0' COMMENT '分组' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_store', 'tzid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_store` ADD `tzid` INT NULL DEFAULT '0' COMMENT '社区店' AFTER `ocid`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'effective_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `effective_time` INT(6) NULL DEFAULT '0' COMMENT '有效时间天' AFTER `is_times`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'timing_unit')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `timing_unit` VARCHAR(20) NULL DEFAULT '' COMMENT '定期周期' AFTER `is_timing`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'timing_mum')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `timing_mum` INT(6) NULL DEFAULT '0' COMMENT '周期可约次数' AFTER `timing_unit`;");
}
if (!pdo_fieldexists('xm_mallv3_tuanzhang', 'community_title')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_tuanzhang` ADD `community_title` VARCHAR(200) NULL DEFAULT '' COMMENT '社区名称' AFTER `tel`;");
}

if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'points_method')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `points_method` TINYINT(1) NULL DEFAULT '0' AFTER `points`;");
}

if (!pdo_fieldexists('xm_mallv3_order_goods', 'mgid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_goods` ADD `mgid` INT NULL DEFAULT '0' AFTER `cat_id`;");
}
if (!pdo_fieldexists('xm_mallv3_member_commission', 'commission_method')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_commission` ADD `commission_method` TINYINT(1) NULL DEFAULT '0' AFTER `mgid`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity_incomelog', 'areatype')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity_incomelog` ADD `areatype` TINYINT(1) NULL DEFAULT '0' AFTER `ptype`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity', 'longitude')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `longitude` VARCHAR(30) NULL DEFAULT '' AFTER `region_name`;");
}

if (!pdo_fieldexists('xm_mallv3_operatingcity', 'latitude')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` ADD `latitude` VARCHAR(30) NULL DEFAULT '' AFTER `region_name`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'hangingpoint')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `hangingpoint` VARCHAR(100) NULL DEFAULT '' AFTER `remark`;");
}

if (!pdo_fieldexists('xm_mallv3_member_wishlist', 'weid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_wishlist` ADD `weid` INT NOT NULL AFTER `id`;");
}

if (!pdo_fieldexists('xm_mallv3_member_wishlist', 'update_time')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_wishlist` ADD `update_time` INT NULL DEFAULT '0' AFTER `create_time`;");
}

if (!pdo_fieldexists('xm_mallv3_member_wishlist', 'title')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_wishlist` ADD `title` VARCHAR(200) NULL DEFAULT '' AFTER `goods_id`;");
}

if (!pdo_fieldexists('xm_mallv3_member_wishlist', 'image')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_wishlist` ADD `image` VARCHAR(255) NULL DEFAULT '' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_member_wishlist', 'ptype')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_wishlist` ADD `ptype` VARCHAR(20) NULL DEFAULT '' AFTER `goods_id`;");
}

if (!pdo_fieldexists('xm_mallv3_member_wishlist', 'url')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_wishlist` ADD `url` VARCHAR(255) NULL DEFAULT '' AFTER `image`;");
}

if (!pdo_fieldexists('xm_mallv3_miaosha_goods', 'member_buy_max')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_miaosha_goods` ADD `member_buy_max` INT(6) NULL DEFAULT '0' COMMENT '每人限购' AFTER `buy_max`;");
}

if (!pdo_fieldexists('xm_mallv3_order_timescard_record', 'update_time')) {
   pdo_run("ALTER TABLE `ims_xm_mallv3_order_timescard_record` ADD `update_time` INT NULL DEFAULT '0' AFTER `create_time`;");
}

if (!pdo_fieldexists('xm_mallv3_order_timescard_record', 'create_day')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_timescard_record` ADD `create_day` INT NULL AFTER `is_settlement`;");
}

if (!pdo_fieldexists('xm_mallv3_member_auth_group', 'giftcard_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` ADD `giftcard_id` INT NULL DEFAULT '0' COMMENT '购买送购物卡' AFTER `coupon_id`;");
}
if (!pdo_fieldexists('xm_mallv3_goods', 'extraprice')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `extraprice` DECIMAL(15,2) NULL DEFAULT '0' COMMENT '购物卡赠送金额' AFTER `is_times`;");
}

if (!pdo_fieldexists('xm_mallv3_technical', 'certificate_ids')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_technical` ADD `certificate_ids` VARCHAR(100) NULL DEFAULT '' COMMENT '师傅认证' AFTER `cate_ids`;");
}

if (!pdo_fieldexists('xm_mallv3_goods', 'card_tid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods` ADD `card_tid` INT NULL DEFAULT '0' COMMENT '卡类型id' AFTER `sid`;");
}

if (!pdo_fieldexists('xm_mallv3_order_card', 'balance')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_card` ADD `balance` DECIMAL(15,2) NULL DEFAULT '0' COMMENT '余额' AFTER `facevalue`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'searchkeyword')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `searchkeyword` LONGTEXT NULL AFTER `ip`;");
}

if (!pdo_fieldexists('xm_mallv3_goods_giftcard_type', 'commission_method')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods_giftcard_type` ADD `commission_method` TINYINT(1) NULL DEFAULT '0' COMMENT '佣金结算方式' AFTER `facevalue`;");
}

if (!pdo_fieldexists('xm_mallv3_goods_giftcard_type', 'points')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods_giftcard_type` ADD `points` INT NULL DEFAULT '0' COMMENT '购买得积分' AFTER `facevalue`;");
}

if (!pdo_fieldexists('xm_mallv3_goods_giftcard_type', 'points')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods_giftcard_type` ADD `points` INT NULL DEFAULT '0' COMMENT '购买得积分' AFTER `facevalue`;");
}

if (!pdo_fieldexists('xm_mallv3_goods_giftcard_type', 'points_method')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_goods_giftcard_type` ADD `points_method` TINYINT(1) NULL DEFAULT '0' AFTER `facevalue`;");
}

if (!pdo_fieldexists('xm_mallv3_order_goods', 'card_tid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_goods` ADD `card_tid` INT NULL DEFAULT '0' AFTER `cat_id`;");
}

if (!pdo_fieldexists('xm_mallv3_coupon_receive', 'ocid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon_receive` ADD `ocid` INT NULL DEFAULT '0' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_coupon_receive', 'ocid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon_receive` ADD `ocid` INT NULL DEFAULT '0' AFTER `weid`;");
}

if (!pdo_fieldexists('xm_mallv3_coupon_receive', 'sid')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_coupon_receive` ADD `sid` INT NULL DEFAULT '0' AFTER `ocid`;");
}

if (!pdo_fieldexists('xm_mallv3_recovery_category', 'price')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_recovery_category` ADD `price` DECIMAL(15,2) NULL DEFAULT '0' AFTER `title`;");
}

if (!pdo_fieldexists('xm_mallv3_comment', 'otr_id')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_comment` ADD `otr_id` INT NULL DEFAULT '0' COMMENT '次卡id' AFTER `order_id`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'is_cashregister')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `is_cashregister` TINYINT(1) NULL DEFAULT '0' COMMENT '收银台订单' AFTER `sid`;");
}

if (!pdo_fieldexists('xm_mallv3_order', 'auth_code')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order` ADD `auth_code` VARCHAR(60) NULL DEFAULT '' COMMENT '付款码' AFTER `payment_code`;");
}


pdo_run("ALTER TABLE `ims_xm_mallv3_register_field` CHANGE `selectvalue` `selectvalue` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '选择的项目';");

pdo_run("ALTER TABLE `ims_xm_mallv3_register_field` CHANGE `defaultvalue` `defaultvalue` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '默认值';");

pdo_run("ALTER TABLE `ims_xm_mallv3_register_field` CHANGE `shuoming` `shuoming` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '说明';");

pdo_run("ALTER TABLE `ims_xm_mallv3_goods` CHANGE `sid` `sid` INT(11) NULL DEFAULT '0' COMMENT '店铺ID';");

pdo_run("ALTER TABLE `ims_xm_mallv3_goods` CHANGE `ptype` `ptype` TINYINT(2) NULL DEFAULT '0' COMMENT '类型';");

pdo_run("ALTER TABLE `ims_xm_mallv3_goods` CHANGE `tel` `tel` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' COMMENT '联系电话';");

pdo_run("ALTER TABLE `ims_xm_mallv3_goods` CHANGE `quantity` `quantity` INT(4) NULL DEFAULT '0' COMMENT '商品数目';");

pdo_run("ALTER TABLE `ims_xm_mallv3_goods` CHANGE `sale_count` `sale_count` INT(11) NULL DEFAULT '0' COMMENT '销量';");

pdo_run("ALTER TABLE `ims_xm_mallv3_goods` CHANGE `sale_count_base` `sale_count_base` INT(11) NULL DEFAULT '0' COMMENT '销量基数';");

pdo_run("ALTER TABLE `ims_xm_mallv3_goods` CHANGE `stock_status_id` `stock_status_id` INT(11) NULL DEFAULT '0' COMMENT '库存状态编号（关联stock_status主键）';");

pdo_run("ALTER TABLE `ims_xm_mallv3_goods` CHANGE `price` `price` DECIMAL(15,2) NULL DEFAULT '0.00' COMMENT '商品价格';");

pdo_run("ALTER TABLE `ims_xm_mallv3_goods` CHANGE `costs` `costs` DECIMAL(15,2) NULL DEFAULT '0.00' COMMENT '成本';");

pdo_run("ALTER TABLE `ims_xm_mallv3_goods` CHANGE `is_combination` `is_combination` TINYINT(1) NULL DEFAULT '0' COMMENT '是否为套装商品:0:否, 1:是';");

pdo_run("ALTER TABLE `ims_xm_mallv3_goods` CHANGE `timesmum` `timesmum` INT(3) NULL DEFAULT '0' COMMENT '次数';");

pdo_run("ALTER TABLE `ims_xm_mallv3_register_field` CHANGE `is_search` `is_search` TINYINT(1) NULL DEFAULT '0' COMMENT '是否搜索';");

pdo_run("ALTER TABLE `ims_xm_mallv3_register_field` CHANGE `is_listView` `is_listView` TINYINT(1) NULL DEFAULT '0' COMMENT '是否列表显示';");

pdo_run("ALTER TABLE `ims_xm_mallv3_sys_log` CHANGE `useragent` `useragent` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'useragent';");

pdo_run("ALTER TABLE `ims_xm_mallv3_diy_page` CHANGE `version` `version` TINYINT(1) NULL DEFAULT '0';");

pdo_run("ALTER TABLE `ims_xm_mallv3_agent_code` CHANGE `weid` `weid` INT(11) NULL DEFAULT '0';");

pdo_run("ALTER TABLE `ims_xm_mallv3_order_staff` CHANGE `end_time` `end_time` INT(11) NULL DEFAULT '0' COMMENT '结速时间';");

pdo_run("ALTER TABLE `ims_xm_mallv3_order_staff` CHANGE `begin_time` `begin_time` INT(11) NULL DEFAULT '0' COMMENT '开始时间';");

pdo_run("ALTER TABLE `ims_xm_mallv3_member` CHANGE `nickname` `nickname` VARCHAR(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL DEFAULT NULL COMMENT '昵称';");

pdo_run("ALTER TABLE `ims_xm_mallv3_withdraw` CHANGE `actual_poundage` `actual_poundage` DECIMAL(14,2) NULL DEFAULT '0' COMMENT '实际手续费';");

pdo_run("ALTER TABLE `ims_xm_mallv3_openid` CHANGE `uid` `uid` INT(11) NULL DEFAULT '0';");

pdo_run("ALTER TABLE `ims_xm_mallv3_order_staff` CHANGE `uid` `uid` INT(11) NULL DEFAULT '0';");

pdo_run("ALTER TABLE `ims_xm_mallv3_technical` CHANGE `uid` `uid` INT(11) NULL;");

pdo_run("ALTER TABLE `ims_xm_mallv3_cart` CHANGE `quantity` `quantity` INT(11) NULL DEFAULT '0';");

pdo_run("ALTER TABLE `ims_xm_mallv3_goods` CHANGE `quantity` `quantity` INT(11) NULL DEFAULT '0' COMMENT '商品数目';");

pdo_run("ALTER TABLE `ims_xm_mallv3_goods_discount` CHANGE `quantity` `quantity` INT(11) NOT NULL DEFAULT '0';");

pdo_run("ALTER TABLE `ims_xm_mallv3_goods_sku_value` CHANGE `quantity` `quantity` INT(11) NULL DEFAULT '0';");

pdo_run("ALTER TABLE `ims_xm_mallv3_order_goods` CHANGE `quantity` `quantity` INT(11) NOT NULL DEFAULT '0';");

pdo_run("ALTER TABLE `ims_xm_mallv3_technical` CHANGE `dizhi` `dizhi` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '地址';");

pdo_run("ALTER TABLE `ims_xm_mallv3_member_auth_group` CHANGE `upgrademoney` `upgrademoney` DECIMAL(10,2) NULL COMMENT '购买升级价格';");

pdo_run("ALTER TABLE `ims_xm_mallv3_order` CHANGE `uid` `uid` INT(11) NULL DEFAULT '0';");

pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` CHANGE `province_id` `province_id` INT(11) NULL;");

pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` CHANGE `city_id` `city_id` INT(11) NULL;");

pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity` CHANGE `title` `title` VARCHAR(60) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;");

pdo_run("ALTER TABLE `ims_xm_mallv3_agent_teama_ward` CHANGE `beginnumber` `beginnumber` INT(11) NULL COMMENT '开始人数';");

pdo_run("ALTER TABLE `ims_xm_mallv3_agent_teama_ward` CHANGE `endnumber` `endnumber` INT(11) NULL COMMENT '最高人数';");

pdo_run("ALTER TABLE `ims_xm_mallv3_comment` CHANGE `goods_id` `goods_id` INT(11) NULL DEFAULT '0';");

pdo_run("ALTER TABLE `ims_xm_mallv3_sys_log` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '请求内容';");

pdo_run("ALTER TABLE `ims_xm_mallv3_category` CHANGE `deliverymode` `deliverymode` VARCHAR(60) NOT NULL COMMENT '交付方式';");

pdo_run("ALTER TABLE `ims_xm_mallv3_agent_level` CHANGE `return_percent` `return_percent` DECIMAL(9,2) NULL DEFAULT '0.00' COMMENT '返佣比例';");

pdo_run("ALTER TABLE `ims_xm_mallv3_store_level` CHANGE `return_percent` `return_percent` DECIMAL(9,2) NULL DEFAULT '0.00';");

pdo_run("ALTER TABLE `ims_xm_mallv3_technical_level` CHANGE `return_percent` `return_percent` DECIMAL(9,2) NULL DEFAULT '0.00' COMMENT '返佣比例';");

pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity_level` CHANGE `return_percent` `return_percent` DECIMAL(9,2) NULL DEFAULT '0.00' COMMENT '返佣比例';");

pdo_run("ALTER TABLE `ims_xm_mallv3_tuanzhang_level` CHANGE `return_percent` `return_percent` DECIMAL(9,2) NULL DEFAULT '0.00' COMMENT '返佣比例';");

pdo_run("ALTER TABLE `ims_xm_mallv3_partner_level` CHANGE `return_percent` `return_percent` DECIMAL(9,2) NULL DEFAULT '0.00' COMMENT '分红比例';");

pdo_run("ALTER TABLE `ims_xm_mallv3_incomelog` CHANGE `return_percent` `return_percent` DECIMAL(9,2) NULL DEFAULT '0.00' COMMENT '提成点数';");

pdo_run("ALTER TABLE `ims_xm_mallv3_technical_incomelog` CHANGE `return_percent` `return_percent` DECIMAL(9,2) NULL DEFAULT '0.00' COMMENT '提成点数';");

pdo_run("ALTER TABLE `ims_xm_mallv3_operatingcity_incomelog` CHANGE `return_percent` `return_percent` DECIMAL(9,2) NULL DEFAULT '0.00' COMMENT '提成点数';");

pdo_run("ALTER TABLE `ims_xm_mallv3_partner_incomelog` CHANGE `return_percent` `return_percent` DECIMAL(9,2) NULL DEFAULT '0.00' COMMENT '提成点数';");

pdo_run("ALTER TABLE `ims_xm_mallv3_store_incomelog` CHANGE `return_percent` `return_percent` DECIMAL(9,2) NULL DEFAULT '0.00' COMMENT '提成点数';");

pdo_run("ALTER TABLE `ims_xm_mallv3_tuanzhang_incomelog` CHANGE `return_percent` `return_percent` DECIMAL(8,2) NULL DEFAULT '0.00' COMMENT '提成点数';");

pdo_run("ALTER TABLE `ims_xm_mallv3_agreement` CHANGE `content` `content` LONGTEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL;");

pdo_run("ALTER TABLE `ims_xm_mallv3_kefu_seating` CHANGE `chatid` `chatid` VARCHAR(68) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '';");

if (pdo_fieldexists('xm_mallv3_order_status', 'name_yuyue')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_status` CHANGE `name_yuyue` `name_yuyue` VARCHAR(32) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;");
}

if (pdo_fieldexists('xm_mallv3_order_status', 'status_yuyue')) {
  pdo_run("ALTER TABLE `ims_xm_mallv3_order_status` CHANGE `status_yuyue` `status_yuyue` TINYINT(1) NULL;");
}

pdo_run("DROP TABLE IF EXISTS `ims_xm_mallv3_sys_admin_menu`;
CREATE TABLE `ims_xm_mallv3_sys_admin_menu` (
  `id` int(11) NOT NULL,
  `pid` smallint(6) DEFAULT '0',
  `title` varchar(250) DEFAULT NULL COMMENT '名称',
  `type` smallint(6) DEFAULT NULL COMMENT '类型',
  `path` varchar(250) DEFAULT NULL COMMENT '标识',
  `pages_path` varchar(250) DEFAULT NULL COMMENT 'vue组件路径',
  `selected` varchar(200) DEFAULT '',
  `params` varchar(200) DEFAULT '',
  `icon` varchar(250) DEFAULT NULL COMMENT '图标',
  `sort` int(11) DEFAULT NULL COMMENT '顺序',
  `is_console` tinyint(1) DEFAULT '0',
  `is_city` tinyint(1) DEFAULT '0' COMMENT '城市代理菜单',
  `is_tuanzhang` tinyint(1) DEFAULT '0',
  `is_store` tinyint(1) DEFAULT '0' COMMENT '店菜单',
  `is_cashregister` tinyint(1) DEFAULT '0' COMMENT '收银机',
  `is_admin` tinyint(1) DEFAULT '0' COMMENT '管理员菜单',
  `is_show` tinyint(1) UNSIGNED DEFAULT '1',
  `is_cache` tinyint(1) DEFAULT '0' COMMENT '是否缓存: 0=否, 1=是',
  `w7_hidden` tinyint(1) DEFAULT '0',
  `is_v2` tinyint(1) DEFAULT '0',
  `is_v3` tinyint(1) DEFAULT '0',
  `is_v6` tinyint(1) DEFAULT '0',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

INSERT INTO `ims_xm_mallv3_sys_admin_menu` (`id`, `pid`, `title`, `type`, `path`, `pages_path`, `selected`, `params`, `icon`, `sort`, `is_console`, `is_city`, `is_tuanzhang`, `is_store`, `is_cashregister`, `is_admin`, `is_show`, `is_cache`, `w7_hidden`, `is_v2`, `is_v3`, `is_v6`, `status`) VALUES
(14, 0, '系统设置', 0, 'config', '', '', '', 'el-icon-s-tools', 998, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(15, 14, '基本设置', 1, '/config', 'config/index.vue', '', '', 'el-icon-setting', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(17, 0, '管理员管理', 0, 'users', '', '', '', 'el-icon-s-custom', 990, 1, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(18, 17, '管理员帐号', 1, '/users', 'users/index.vue', '', '', 'el-icon-user', 10, 1, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(19, 17, '权限组设置', 1, '/usersroles', 'usersroles/index.vue', '', '', 'el-icon-s-check', 40, 1, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(20, 14, '操作日志', 1, '/log', 'log/index.vue', '', '', 'el-icon-date', 120, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(154, 144, '分销审核', 1, '/agent/audit', 'agent/audit.vue', '', '', 'el-icon-check', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(23, 18, '修改', 2, '/users/update', '', '', '', '', 23, 1, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(24, 18, '增加', 2, '/users/add', '', '', '', '', 1, 1, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(25, 19, '添加', 2, '/usersroles/add', '', '', '', '', 1, 1, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(26, 19, '修改', 2, '/usersroles/update', '', '', '', '', 2, 1, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(27, 0, '客服管理', 0, 'kefu', '', '', '', 'el-icon-headset', 160, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(28, 27, '客服分组', 1, '/kefu/seatinggroups', 'kefu/seatinggroups/index.vue', '', '', 'el-icon-cpu', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(29, 27, '平台客服管理', 1, '/kefu/seating', 'kefu/seating/index.vue', '', '', 'el-icon-headset', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(31, 27, '常用语设置', 1, '/kefu/commonly', 'kefu/commonly/index.vue', '', '', 'el-icon-magic-stick', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 0),
(32, 27, '自动回复设置', 1, '/kefu/reply', 'kefu/reply/index.vue', '', '', 'el-icon-refresh-right', 50, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(34, 27, '基本配置', 1, '/config/kefu', 'config/kefu.vue', '', '', 'el-icon-s-tools', 90, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(156, 144, '佣金设置', 1, '/config/share', 'config/share.vue', '', '', 'el-icon-orange', 50, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(36, 0, '服务管理', 0, 'service', '', '', '', 'el-icon-s-cooperation', 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(37, 39, '会员字段', 1, '/registerfield/users', 'registerfield/index.vue', '', '', 'el-icon-cpu', 60, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(39, 0, '页面装修', 0, 'bottommenu', '', '', '', 'el-icon-mouse', 138, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(40, 39, '底部菜单', 1, '/bottommenu/index', 'bottommenu/index.vue', '', '', 'el-icon-cpu', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(42, 36, '服务分类', 1, '/servicecategory/index', 'servicecategory/index.vue', '', '', 'el-icon-set-up', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(142, 0, '订单管理', 0, 'order', '', '', '', 'el-icon-printer', 30, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(144, 0, '分销管理', 0, 'agent', '', '', '', 'el-icon-share', 60, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(44, 36, '预约时间段', 1, '/servicetime/index', 'servicetime/index.vue', '', '', 'el-icon-price-tag', 38, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(45, 0, '文章管理', 0, 'article', '', '', '', 'el-icon-collection', 170, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(46, 45, '分类', 1, '/articlecategory/index', 'articlecategory/index.vue', '', '', 'el-icon-notebook-2', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(47, 45, '文章管理', 1, '/article/index', 'article/index.vue', '', '', 'el-icon-notebook-1', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(48, 501, '评价管理', 1, '/comment/index', 'comment/index.vue', '', '', 'el-icon-star-on', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(49, 36, '单次服务项目', 1, '/service/index', 'service/index.vue', '', '', 'el-icon-s-order', 9, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(150, 144, '分销等级', 1, '/agentlevel/index', 'agentlevel/index.vue', '', '', 'el-icon-s-opportunity', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(53, 420, '公众号配置', 1, '/config/mp', 'config/mp.vue', '', '', 'el-icon-cpu', 40, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(55, 17, '部门/分公司', 1, '/department/index', 'department/index.vue', '', '', 'el-icon-rank', 50, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 0),
(56, 136, '会员管理', 1, '/member/index', 'member/index.vue', '', '', 'el-icon-coordinate', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(57, 18, '删除', 2, '/users/delete', '', '', '', '', 57, 1, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(58, 18, '禁用', 2, '/users/forbidden', '', '', '', '', 58, 1, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(59, 18, '重置密码', 2, '/users/resetPwd', '', '', '', '', 59, 1, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(60, 18, '导入', 2, '/users/importData', '', '', '', '', 60, 1, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(61, 18, '导出', 2, '/users/dumpdata', '', '', '', '', 61, 1, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(62, 19, '删除', 2, '/usersroles/delete', '', '', '', '', 62, 1, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(63, 55, '添加', 2, '/department/add', '', '', '', '', 63, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(64, 55, '修改', 2, '/department/update', NULL, '', '', NULL, 64, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(65, 55, '删除', 2, '/department/delete', NULL, '', '', NULL, 65, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(66, 20, '删除', 2, '/log/delete', NULL, '', '', NULL, 66, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(67, 20, '导出', 2, '/log/dumpdata', NULL, '', '', NULL, 67, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(68, 20, '详情', 2, '/log/detail', NULL, '', '', NULL, 68, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(258, 39, '师傅端底部菜单', 1, '/bottommenu/technical', 'bottommenu/index.vue', '', '', 'el-icon-s-operation', 32, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(72, 37, '添加', 2, '/registerfield/add', '', '', '', '', 72, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(73, 37, '修改', 2, '/registerfield/update', '', '', '', '', 73, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(74, 37, '删除', 2, '/registerfield/delete', '', '', '', '', 74, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(78, 46, '添加', 2, '/articlecategory/add', '', '', '', '', 78, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(79, 46, '修改', 2, '/articlecategory/update', '', '', '', '', 79, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(80, 46, '删除', 2, '/articlecategory/delete', '', '', '', '', 80, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(81, 47, '添加', 2, '/article/add', '', '', '', '', 81, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(82, 47, '修改', 2, '/article/update', '', '', '', '', 82, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(83, 47, '删除', 2, '/article/delete', '', '', '', '', 83, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(84, 32, '添加', 2, '/kefu/reply/add', NULL, '', '', NULL, 84, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(85, 32, '修改', 2, '/kefu/reply/update', NULL, '', '', NULL, 85, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(86, 32, '删除', 2, '/kefu/reply/delete', NULL, '', '', NULL, 86, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(87, 31, '添加', 2, '/kefu/commonly/add', NULL, '', '', NULL, 87, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(88, 31, '修改', 2, '/kefu/commonly/update', NULL, '', '', NULL, 88, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(89, 31, '删除', 2, '/kefu/commonly/delete', NULL, '', '', NULL, 89, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(91, 28, '添加', 2, '/kefu/seatinggroups/add', '', '', '', '', 91, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(92, 28, '修改', 2, '/kefu/seatinggroups/update', '', '', '', '', 92, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(93, 28, '删除', 2, '/kefu/seatinggroups/delete', NULL, '', '', NULL, 93, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(94, 29, '添加', 2, '/kefu/seating/add', NULL, '', '', NULL, 94, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(95, 29, '修改', 2, '/kefu/seating/update', NULL, '', '', NULL, 95, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(96, 29, '删除', 2, '/kefu/seating/delete', NULL, '', '', NULL, 96, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(97, 44, '添加', 2, '/servicetime/add', '', '', '', '', 97, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(98, 44, '修改', 2, '/servicetime/update', '', '', '', '', 98, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(99, 44, '删除', 2, '/servicetime/delete', '', '', '', '', 99, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(143, 456, '上门服务订单', 1, '/order/service', 'order/service.vue', '', '', 'el-icon-date', 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(103, 42, '添加', 2, '/servicecategory/add', '', '', '', '', 103, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(105, 42, '修改', 2, '/servicecategory/update', '', '', '', '', 105, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(106, 42, '删除', 2, '/servicecategory/delete', '', '', '', '', 106, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(269, 134, '添加', 2, '/store/add', NULL, '', '', NULL, 10, 0, 1, 1, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(115, 56, '修改', 2, '/member/update', '', '', '', '', 115, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(116, 56, '删除', 2, '/member/delete', '', '', '', '', 116, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(138, 137, '添加', 2, '/memberauthgroup/add', '', '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(137, 136, '会员等级', 1, '/memberauthgroup/index', 'memberauthgroup/index.vue', '', '', 'el-icon-finished', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(121, 49, '添加', 2, '/service/add', '', '', '', '', 121, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(122, 49, '修改', 2, '/service/update', '', '', '', '', 122, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(123, 49, '删除', 2, '/service/delete', '', '', '', '', 123, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(145, 0, '营销活动', 0, 'coupon', '', '', '', 'el-icon-present', 50, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(146, 189, '师傅审核', 1, '/technical/audit', 'technical/audit.vue', '', '', 'el-icon-finished', 20, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(128, 0, '城市代理/运营', 0, 'operatingcity', '', '', '', 'el-icon-position', 110, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(129, 128, '代理/运营管理', 1, '/operatingcity/index', 'operatingcity/index.vue', '', '', 'el-icon-guide', 10, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(130, 129, '添加', 2, '/operatingcity/add', '', '', '', '', 10, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(131, 129, '修改', 2, '/operatingcity/update', '', '', '', '', 131, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(132, 129, '删除', 2, '/operatingcity/delete', '', '', '', '', 132, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(133, 0, '商家/门店', 0, 'store', '', '', '', 'el-icon-s-flag', 100, 0, 1, 1, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(134, 133, '商家管理', 1, '/store/index', 'store/index.vue', '', '', 'el-icon-collection-tag', 10, 0, 1, 1, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(135, 133, '商家等级', 1, '/storelevel/index', 'storelevel/index.vue', '', '', 'el-icon-menu', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(136, 0, '会员管理', 0, 'member', '', '', '', 'el-icon-user-solid', 70, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(139, 137, '修改', 2, '/memberauthgroup/update', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(140, 137, '删除', 2, '/memberauthgroup/delete', NULL, '', '', NULL, 40, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(147, 146, '审核', 2, '/technical/auditdetail', '', '', '', '', 147, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(308, 133, '商家设置', 1, '/config/store', 'config/store.vue', '', '', 'el-icon-set-up', 60, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(149, 145, '优惠券', 1, '/coupon/index', 'coupon/index.vue', '', '', 'el-icon-s-ticket', 10, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(152, 133, '商家审核', 1, '/store/audit', 'store/audit.vue', '', '', 'el-icon-check', 20, 0, 1, 1, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(153, 134, '修改', 2, '/store/update', NULL, '', '', NULL, 153, 0, 1, 1, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(155, 144, '分销设置', 1, '/config/agent', 'config/agent.vue', '', '', 'el-icon-setting', 40, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(157, 0, '配送/物流', 0, 'transportextend', '', '', '', 'el-icon-truck', 180, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(158, 157, '快递设置', 1, '/transportextend/index', 'transportextend/index.vue', '', '', 'el-icon-truck', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(159, 157, '退货地址', 1, '/refundaddress/index', 'refundaddress/index.vue', '', '', 'el-icon-refresh-right', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(160, 420, '公众号模板消息', 1, '/config/messagetpl', 'config/messagetpl.vue', '', '', 'el-icon-message-solid', 50, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(162, 14, '打印机设置', 1, '/printer/index', 'printer/index.vue', '', '', 'el-icon-printer', 100, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(163, 14, '支付设置', 1, '/paymethod/index', 'paymethod/index.vue', '', '', 'el-icon-s-claim', 70, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(165, 142, '次卡订单', 1, '/order/timescard', 'order/timescard.vue', '', '', 'el-icon-s-grid', 36, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(166, 142, '商品订单', 1, '/order/goods', 'order/goods.vue', '', '', 'el-icon-s-claim', 30, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(167, 39, '旧版DIY', 1, '/diypage/index', 'diypage/index.vue', '', '', 'el-icon-mouse', 60, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 0),
(171, 39, '分类页幻灯片', 1, '/ad/index', 'ad/index.vue', '', '', 'el-icon-picture-outline-round', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(172, 171, '添加', 2, '/ad/add', NULL, '', '', NULL, 172, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(173, 171, '修改', 2, '/ad/update', NULL, '', '', NULL, 173, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(174, 171, '删除', 2, '/ad/delete', NULL, '', '', NULL, 174, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(175, 143, '服务订单详情', 2, '/order/servicedetail', NULL, '', '', NULL, 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(176, 166, '商品订单详情', 2, '/order/goodsdetail', NULL, '', '', NULL, 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(177, 166, '删除', 2, '/order/delete', NULL, '', '', NULL, 30, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(178, 0, '商品管理', 0, 'goods', '', '', '', 'el-icon-s-goods', 20, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(179, 182, '添加', 2, '/goods/add', '', '', '', '', 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(180, 182, '修改', 2, '/goods/update', '', '', '', '', 20, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(181, 182, '删除', 2, '/goods/delete', '', '', '', '', 30, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(182, 178, '商品列表', 1, '/goods/index', 'goods/index.vue', '', '', 'el-icon-goods', 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(183, 178, '商品分类', 1, '/goodscategory/index', 'goodscategory/index.vue', '', '', 'el-icon-set-up', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(184, 183, '添加', 2, '/goodscategory/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(185, 183, '修改', 2, '/goodscategory/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(186, 183, '删除', 2, '/goodscategory/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(191, 39, 'DIY面页设计', 1, '/diypage/diy', 'diypage/diy.vue', '', '', 'el-icon-mouse', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(203, 419, '小程序配置', 1, '/config/miniprogram', 'config/miniprogram.vue', '', '', 'el-icon-link', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(188, 189, '师傅设置 ', 1, '/config/technical', 'config/technical.vue', '', '', 'el-icon-set-up', 90, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(189, 0, '师傅管理', 0, 'technical', '', '', '', 'el-icon-help', 80, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(190, 189, '师傅列表', 1, '/technical/index', 'technical/index.vue', '', '', 'el-icon-s-help', 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(195, 158, '添加', 2, '/transportextend/add', '', '', '', '', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(196, 158, '修改', 2, '/transportextend/update', '', '', '', '', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(197, 158, '删除', 2, '/transportextend/delete', '', '', '', '', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(198, 159, '添加', 2, '/refundaddress/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(199, 159, '修改', 2, '/refundaddress/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(200, 159, '删除', 2, '/refundaddress/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(201, 190, '修改', 2, '/technical/update', '', '', '', '', 20, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(202, 190, '删除', 2, '/technical/delete', NULL, '', '', NULL, 50, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(204, 134, '登录', 2, '/store/login', NULL, '', '', NULL, 20, 0, 1, 1, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(208, 149, '添加', 2, '/coupon/add', NULL, '', '', NULL, 10, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(209, 149, '修改', 2, '/coupon/update', NULL, '', '', NULL, 209, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(210, 149, '删除', 2, '/coupon/delete', NULL, '', '', NULL, 30, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(211, 163, '设置', 2, '/paymethod/update', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(212, 150, '添加', 2, '/agentlevel/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(213, 150, '修改', 2, '/agentlevel/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(214, 150, '删除', 2, '/agentlevel/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(241, 157, '物流信息配置', 1, '/config/transport', 'config/transport.vue', '', '', 'el-icon-location-outline', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(217, 39, '显示文字设置', 1, '/lang/index', 'lang/index.vue', '', '', 'el-icon-mobile', 28, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(218, 135, '添加', 2, '/storelevel/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(219, 135, '修改', 2, '/storelevel/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(220, 135, '删除', 2, '/storelevel/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(221, 39, '师傅字段', 1, '/registerfield/technical', 'registerfield/index.vue', '', '', 'el-icon-cpu', 80, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(222, 39, '分销商字段', 1, '/registerfield/agent', 'registerfield/index.vue', '', '', 'el-icon-cpu', 60, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(223, 144, '分销员管理', 1, '/agent/index', 'agent/index.vue', '', '', 'el-icon-connection', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(224, 39, '个人中心菜单', 1, '/clientmenu/member', 'clientmenu/index.vue', '', '', 'el-icon-menu', 31, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(225, 189, '师傅等级', 1, '/technicallevel/index', 'technicallevel/index.vue', '', '', 'el-icon-menu', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(226, 225, '添加', 2, '/technicallevel/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(227, 225, '修改', 2, '/technicallevel/update', '', '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(228, 225, '删除', 2, '/technicallevel/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(229, 189, '师傅认证', 1, '/technicalcertificate/index', 'technicalcertificate/index.vue', '', '', 'el-icon-s-help', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(230, 229, '添加', 2, '/technicalcertificate/add', '', '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(231, 229, '修改', 2, '/technicalcertificate/update', '', '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(232, 229, '删除', 2, '/technicalcertificate/delete', '', '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(233, 14, '手机短信配置', 1, '/config/sms', 'config/sms.vue', '', '', 'el-icon-mobile', 110, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(234, 144, '邀请码审核', 1, '/agentcode/index', 'agentcode/index.vue', '', '', 'el-icon-check', 40, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 0),
(235, 234, '修改', 2, '/agentcode/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(236, 234, '删除', 2, '/agentcode/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(237, 0, '财务管理', 0, 'withdraw', '', '', '', 'el-icon-s-claim', 150, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(238, 237, '分销提现', 1, '/withdraw/agent', 'withdraw/index.vue', '', '', 'el-icon-finished', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(239, 237, '师傅提现', 1, '/withdraw/technical', 'withdraw/index.vue', '', '', 'el-icon-finished', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(240, 237, '商家提现', 1, '/withdraw/store', 'withdraw/index.vue', '', '', 'el-icon-s-cooperation', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(242, 14, 'AI舌诊配置', 1, '/config/tongue', 'config/tongue.vue', '', '', 'el-icon-cpu', 90, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 0),
(243, 39, '订单附件字段', 1, '/registerfield/complete', 'registerfield/index.vue', '', '', 'el-icon-soccer', 60, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(245, 39, '商家字段', 1, '/registerfield/store', 'registerfield/index.vue', '', '', 'el-icon-cpu', 60, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(246, 142, '退款/售后', 1, '/orderrefund/index', 'orderrefund/index.vue', '', '', 'el-icon-guide', 60, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(247, 0, '平台管理', 0, 'platform', '', '', '', 'el-icon-s-grid', 30, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(248, 247, '平台列表', 1, '/platform/index', 'platform/index.vue', '', '', 'el-icon-menu', 10, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(249, 0, '站点设置', 0, 'sitesetup', '', '', '', 'el-icon-s-tools', 999, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(250, 249, '站点设置', 1, '/sitesetup/index', 'sitesetup/index.vue', '', '', 'el-icon-setting', 10, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(251, 249, '系统更新', 1, '/sitesetup/upgrade', 'sitesetup/upgrade.vue', '', '', 'el-icon-upload', 999, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(252, 248, '添加', 2, '/platform/add', NULL, '', '', NULL, 10, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(253, 248, '修改', 2, '/platform/update', '', '', '', NULL, 20, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(254, 248, '删除', 2, '/platform/delete', NULL, '', '', NULL, 30, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(255, 39, '页面样式设置', 1, '/config/pagestyle', 'config/pagestyle.vue', '', '', 'el-icon-s-custom', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(256, 14, '远程附件设置', 1, '/ossupload/index', 'ossupload/index.vue', '', '', 'el-icon-connection', 80, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(257, 249, '远程附件设置', 1, '/sitesetup/ossupload', 'ossupload/index.vue', '', '', 'el-icon-connection', 38, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(259, 14, '使用协议', 1, '/agreement/index', 'agreement/index.vue', '', '', 'el-icon-s-claim', 60, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(260, 259, '修改', 2, '/agreement/update', '', '', '', '', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(337, 143, '结算订单', 2, '/service/delivery', NULL, '', '', NULL, 60, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(262, 261, '添加', 2, '/broadcast/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(263, 261, '修改', 2, '/broadcast/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(264, 261, '删除', 2, '/broadcast/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(265, 419, '小程序订阅消息', 1, '/config/subscribemessage', 'config/subscribemessage.vue', '', '', 'el-icon-message-solid', 40, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(266, 190, '添加', 2, '/technical/add', NULL, '', '', NULL, 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(267, 190, '导入', 2, '/technical/importData', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(268, 190, '导出', 2, '/technical/dumpdata', NULL, '', '', NULL, 40, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(270, 134, '删除', 2, '/store/delete', '', '', '', '', 50, 0, 1, 1, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(271, 134, '导入', 2, '/store/importData', NULL, '', '', NULL, 60, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(272, 134, '导出', 2, '/store/dumpdata', NULL, '', '', NULL, 60, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(273, 223, '修改', 2, '/agent/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(274, 223, '删除', 2, '/agent/delete', NULL, '', '', NULL, 274, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(275, 152, '审核', 2, '/store/auditdetail', NULL, '', '', NULL, 10, 0, 1, 1, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(277, 154, '审核', 2, '/agent/auditdetail', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(279, 143, '派单', 2, '/order/staff', NULL, '', '', NULL, 30, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(280, 166, '发货', 2, '/order/send', NULL, '', '', NULL, 20, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(281, 431, '送积分配置', 1, '/config/points', 'config/points.vue', '', '', 'el-icon-s-opportunity', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(282, 321, '秒杀商品', 1, '/miaoshagoods/index', 'miaoshagoods/index.vue', '', '', 'el-icon-s-order', 20, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(283, 282, '添加', 2, '/miaoshagoods/add', NULL, '', '', NULL, 10, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(284, 282, '修改', 2, '/miaoshagoods/update', NULL, '', '', NULL, 20, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(285, 282, '删除', 2, '/miaoshagoods/delete', NULL, '', '', NULL, 30, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(286, 393, '拼团商品', 1, '/tuangoods/index', 'tuangoods/index.vue', '', '', 'el-icon-money', 20, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(287, 286, '添加', 2, '/tuangoods/add', NULL, '', '', NULL, 10, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(288, 286, '修改', 2, '/tuangoods/update', NULL, '', '', NULL, 20, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(289, 286, '删除', 2, '/tuangoods/delete', NULL, '', '', NULL, 30, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(290, 145, '砍价活动', 1, '/bargaingoods/index', 'bargaingoods/index.vue', '', '', 'el-icon-watermelon', 80, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(291, 290, '添加', 2, '/bargaingoods/add', NULL, '', '', NULL, 10, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(292, 290, '修改', 2, '/bargaingoods/update', NULL, '', '', NULL, 20, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(293, 290, '删除', 2, '/bargaingoods/delete', NULL, '', '', NULL, 30, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(294, 39, '城市代理字段', 1, '/registerfield/operatingcity', 'registerfield/index.vue', '', '', 'el-icon-cpu', 60, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(295, 128, '代理等级', 1, '/operatingcitylevel/index', 'operatingcitylevel/index.vue', '', '', 'el-icon-menu', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(296, 128, '代理审核', 1, '/operatingcity/audit', 'operatingcity/audit.vue', '', '', 'el-icon-s-check', 20, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(301, 295, '修改', 2, '/operatingcitylevel/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(300, 295, '添加', 2, '/operatingcitylevel/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(302, 295, '删除', 2, '/operatingcitylevel/delete', '', '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(338, 166, '结算订单', 2, '/goods/delivery', NULL, '', '', NULL, 60, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(305, 14, '求助记录', 1, '/forhelp/index', 'forhelp/index.vue', '', '', 'el-icon-phone', 111, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(306, 305, '祥情', 2, '/forhelp/detail', '', '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(307, 305, '删除', 2, '/forhelp/delete', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(309, 128, '城市代理设置', 1, '/config/operatingcity', 'config/operatingcity.vue', '', '', 'el-icon-set-up', 60, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(310, 237, '财务统计', 1, '/statistical/index', 'statistical/index.vue', '', '', 'el-icon-s-marketing', 60, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(311, 189, '已结算订单', 1, '/technicalincomelog/index', 'technicalincomelog/index.vue', '', '', 'el-icon-s-claim', 40, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(336, 39, '店铺底部菜单', 1, '/bottommenu/store', 'bottommenu/index.vue', '', '', 'el-icon-s-operation', 33, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(313, 312, '添加', 2, '/agentteamaward/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(314, 312, '修改', 2, '/agentteamaward/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(315, 312, '删除', 2, '/agentteamaward/delete', NULL, '', '', NULL, 40, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(316, 143, '添加', 2, '/order/serviceadd', NULL, '', '', NULL, 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(317, 143, '删除', 2, '/order/servicedelete', NULL, '', '', NULL, 50, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(318, 246, '处理售后', 2, '/orderrefund/retreat', NULL, '', '', NULL, 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(319, 246, '删除', 2, '/orderrefund/delete', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(320, 321, '时间段设置', 1, '/miaoshatime/index', 'miaoshatime/index.vue', '', '', 'el-icon-odometer', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(321, 145, '限时秒杀', 1, '/miaoshagoods', '', '', '', 'el-icon-time', 50, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(322, 237, '代理提现', 1, '/withdraw/operatingcity', 'withdraw/index.vue', '', '', 'el-icon-finished', 40, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(323, 320, '添加', 2, '/miaoshatime/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(324, 320, '修改', 2, '/miaoshatime/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(325, 320, '删除', 2, '/miaoshatime/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(339, 14, '跑腿设置', 1, '/config/errands', 'config/errands.vue', '', '', 'el-icon-bicycle', 72, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(340, 14, '上门车费设置', 1, '/config/thefare', 'config/thefare.vue', '', '', 'el-icon-bangzhu', 73, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(261, 39, '虚拟订单播报', 1, '/broadcast/index', 'broadcast/index.vue', '', '', 'el-icon-s-flag', 23, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(342, 144, '团队奖设置', 1, '/agentteamaward/index', 'agentteamaward/index.vue', '', '', 'el-icon-medal-1', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(343, 145, '合伙人分红', 1, 'partner', '', '', '', 'el-icon-orange', 108, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(344, 343, '合伙人管理', 1, '/partner/index', 'partner/index.vue', '', '', 'el-icon-attract', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(345, 344, '修改', 2, '/partner/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(346, 344, '删除', 2, '/partner/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(347, 343, '合伙人审核', 1, '/partner/audit', 'partner/audit.vue', '', '', 'el-icon-s-check', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(348, 347, '审核', 2, '/partner/auditdetail', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(349, 343, '合伙人级别', 1, '/partnerlevel/index', 'partnerlevel/index.vue', '', '', 'el-icon-grape', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(350, 349, '添加', 2, '/partnerlevel/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(351, 349, '修改', 2, '/partnerlevel/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(352, 349, '删除', 2, '/partnerlevel/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(353, 343, '合伙人设置', 1, '/config/partner', 'config/partner.vue', '', '', 'el-icon-s-operation', 50, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(354, 497, '次卡项目', 1, '/timecard/index', 'timecard/index.vue', '', '', 'el-icon-bank-card', 18, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(355, 354, '添加', 2, '/timecard/add', '', '', '', '', 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(356, 354, '修改', 2, '/timecard/update', '', '', '', '', 20, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(357, 354, '删除', 2, '/timecard/delete', NULL, '', '', NULL, 30, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(358, 0, '社区/团长管理', 0, 'tuanzhang', '', '', '', 'el-icon-s-help', 130, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(359, 358, '社区/团长列表', 1, '/tuanzhang/index', 'tuanzhang/index.vue', '', '', 'el-icon-s-custom', 10, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(360, 359, '添加', 2, '/tuanzhang/add', NULL, '', '', NULL, 10, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(361, 359, '修改', 2, '/tuanzhang/update', NULL, '', '', NULL, 20, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(362, 359, '删除', 2, '/tuanzhang/delete', NULL, '', '', NULL, 30, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(363, 358, '社区/团长审核', 1, '/tuanzhang/audit', 'tuanzhang/audit.vue', '', '', 'el-icon-s-check', 20, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(364, 363, '审核', 2, '/tuanzhang/auditdetail', NULL, '', '', NULL, 10, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(365, 358, '社区/团长等级', 1, '/tuanzhanglevel/index', 'tuanzhanglevel/index.vue', '', '', 'el-icon-medal', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(366, 365, '添加', 2, '/tuanzhanglevel/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(367, 365, '修改', 2, '/tuanzhanglevel/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(368, 365, '删除', 2, '/tuanzhanglevel/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(369, 358, '社区/团长设置', 1, '/config/tuanzhang', 'config/tuanzhang.vue', '', '', 'el-icon-s-tools', 40, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(370, 249, '系统授权', 1, '/sitesetup/authorization', 'sitesetup/authorization.vue', '', '', 'el-icon-check', 20, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(371, 39, '合伙人字段', 1, '/registerfield/partner', 'registerfield/index.vue', '', '', 'el-icon-s-operation', 90, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(372, 39, '社区/团长字段', 1, '/registerfield/tuanzhang', 'registerfield/index.vue', '', '', 'el-icon-orange', 90, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(373, 237, '提现设置', 1, '/config/collect', 'config/collect.vue', '', '', 'el-icon-setting', 80, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(374, 0, '医院管理', 0, 'hospital', '', '', '', 'el-icon-school', 120, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(375, 374, '医院列表', 1, '/hospital/index', 'hospital/index.vue', '', '', 'el-icon-office-building', 10, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(376, 375, '添加', 2, '/hospital/add', '', '', '', '', 10, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(377, 375, '修改', 2, '/hospital/update', '', '', '', NULL, 10, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(378, 375, '登录', 2, '/hospital/login', '', '', '', '', 50, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(379, 375, '删除', 2, '/hospital/delete', NULL, '', '', NULL, 50, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(380, 374, '医院分类', 1, '/hospitalcate/index', 'hospitalcate/index.vue', '', '', 'el-icon-s-grid', 30, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(381, 380, '添加', 2, '/hospitalcatel/add', NULL, '', '', NULL, 10, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(382, 380, '修改', 2, '/hospitalcate/update', '', '', '', '', 20, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(383, 380, '删除', 2, '/hospitalcate/delete', NULL, '', '', NULL, 10, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(384, 374, '医院级别', 1, '/hospitallevel/index', 'hospitallevel/index.vue', '', '', 'el-icon-s-data', 40, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(385, 384, '添加', 2, '/hospitallevel/add', NULL, '', '', NULL, 10, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(386, 384, '修改', 2, '/hospitallevel/update', NULL, '', '', NULL, 20, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(387, 384, '删除', 2, '/hospitallevel/delete', '', '', '', NULL, 30, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(388, 374, '医院科室', 1, '/hospitaldepartments/index', 'hospitaldepartments/index.vue', '', '', 'el-icon-first-aid-kit', 20, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(389, 388, '添加', 2, '/hospitaldepartments/add', NULL, '', '', NULL, 10, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(390, 388, '修改', 2, '/hospitaldepartments/update', NULL, '', '', NULL, 20, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(391, 388, '删除', 2, '/hospitaldepartments/delete', NULL, '', '', NULL, 10, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 0, 1, 1),
(392, 249, '清除缓存', 1, '/sitesetup/clearcache', 'sitesetup/clearcache.vue', '', '', 'el-icon-delete', 50, 1, 0, 0, 1, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(393, 145, '拼团活动', 1, '/tuan', '', '', '', 'el-icon-menu', 30, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(394, 393, '拼团列表', 1, '/tuanfound/index', 'tuanfound/index.vue', '', '', 'el-icon-s-flag', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(395, 145, '幸运抽奖', 1, '/rotarytableprize', '', '', '', 'el-icon-orange', 60, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(396, 395, '大转盘设置', 1, '/config/rotarytable', 'config/rotarytable.vue', '', '', 'el-icon-orange', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(400, 395, '奖品设置', 1, '/rotarytableprize/index', 'rotarytableprize/index.vue', '', '', 'el-icon-present', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(401, 400, '添加', 2, '/rotarytableprize/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(402, 400, '修改', 2, '/rotarytableprize/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(403, 400, '删除', 2, '/rotarytableprize/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(404, 394, '详情', 2, '/tuanfound/detail', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(405, 145, '直播管理', 1, 'liveanchor', '', '', '', 'el-icon-video-camera-solid', 70, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(406, 405, '主播管理', 1, '/liveanchor/index', 'liveanchor/index.vue', '', '', 'el-icon-mic', 30, 0, 0, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(407, 405, '直播间管理', 1, '/liveroom/index', 'liveroom/index.vue', '', '', 'el-icon-video-camera', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(408, 405, '商品管理', 1, '/livegoods/index', 'livegoods/index.vue', '', '', 'el-icon-shopping-cart-2', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(409, 406, '添加', 2, '/liveanchor/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(410, 406, '修改', 2, '/liveanchor/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(411, 406, '删除', 2, '/liveanchor/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(412, 407, '添加', 2, '/liveroom/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(413, 407, '修改', 2, '/liveroom/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(414, 407, '删除', 2, '/liveroom/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(415, 408, '添加', 2, '/livegoods/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(416, 408, '修改', 2, '/livegoods/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(417, 408, '删除', 2, '/livegoods/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(418, 249, '接口请求记录', 1, '/test/index', 'test/index.vue', '', '', 'el-icon-wind-power', 1000, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(419, 14, '小程序', 1, 'miniprogram', '', '', '', 'el-icon-link', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(420, 14, '公众号', 1, 'configmpset', '', '', '', 'el-icon-cpu', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(421, 419, '小程序发布', 1, '/config/uploadminiprogram', 'config/uploadminiprogram.vue', '', '', 'el-icon-upload', 20, 0, 0, 0, 0, 0, 1, 1, 0, 1, 1, 1, 1, 1),
(422, 419, '师傅小程序发布', 1, '/config/techuploadminiprogram', 'config/uploadminiprogram.vue', '', '', 'el-icon-upload', 30, 0, 0, 0, 0, 0, 1, 1, 0, 1, 0, 1, 1, 1),
(423, 14, '换域名设置', 1, '/domainreplace/index', 'domainreplace/index.vue', '', '', 'el-icon-guide', 82, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(424, 423, '添加', 2, '/domainreplace/add', '', '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(425, 423, '修改', 2, '/domainreplace/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(426, 423, '删除', 2, '/domainreplace/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(427, 14, '会员设置', 1, '/config/member', 'config/member.vue', '', '', 'el-icon-user-solid', 11, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(428, 0, '首页', 1, 'dashboard', 'dashboard/index.vue', '', '', 'el-icon-help', 1, 1, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(430, 128, '城市价格设置', 1, '/operatingcity/cityconfig', 'operatingcity/cityconfig.vue', '', '', 'el-icon-set-up', 30, 0, 1, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(431, 145, '积分设置', 1, 'points', NULL, '', '', 'el-icon-coin', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(432, 431, ' 积分记录', 1, '/points/index', 'points/index.vue', '', '', 'el-icon-tickets', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(433, 431, '签到配置', 1, '/signinconfig/index', 'signinconfig/index.vue', '', '', 'el-icon-s-operation', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(434, 39, '显示文字替换', 1, '/textreplace/index', 'textreplace/index.vue', '', '', 'el-icon-guide', 29, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(435, 434, '添加', 2, '/textreplace/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(436, 434, '修改', 2, '/textreplace/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(437, 237, '社区团长提现', 1, '/withdraw/tuanzhang', 'withdraw/index.vue', '', '', 'el-icon-s-finance', 50, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(438, 129, '登录后台', 2, 'operatingcity/login', NULL, '', '', NULL, 30, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(439, 359, '登录后台', 2, '/tuanzhang/login', NULL, '', '', '', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(440, 358, '小区管理', 1, '/housingestate/index', 'housingestate/index.vue', '', '', 'el-icon-office-building', 20, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(441, 440, '添加', 2, '/housingestate/add', NULL, '', '', NULL, 10, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(442, 440, '修改', 2, '/housingestate/update', NULL, '', '', NULL, 20, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(443, 440, '删除', 2, '/housingestate/delete', NULL, '', '', NULL, 30, 0, 1, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(444, 190, '已结算订单', 2, '/technical/incomelog', NULL, '', '', NULL, 30, 0, 1, 0, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(445, 36, '预约时间分组', 1, '/servicetimeptype/index', 'servicetimeptype/index.vue', '', '', 'el-icon-guide', 39, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(446, 445, '添加', 2, '/servicetimeptype/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(447, 445, '修改', 2, '/servicetimeptype/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(448, 445, '删除', 2, '/servicetimeptype/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(449, 497, '购物卡', 1, '/goodsgiftcard/index', 'goodsgiftcard/index.vue', '', '', 'el-icon-s-finance', 20, 0, 0, 1, 1, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(450, 449, '添加', 2, '/goodsgiftcard/add', NULL, '', '', '', 10, 0, 0, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(451, 449, '修改', 2, '/goodsgiftcard/update', NULL, '', '', NULL, 20, 0, 0, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(452, 449, '删除', 2, '/goodsgiftcard/delete', NULL, '', '', NULL, 30, 0, 0, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(453, 0, '我的小区', 0, 'housingestate', NULL, '', '', 'el-icon-school', 90, 0, 0, 1, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(454, 453, '小区管理', 1, '/housingestate/my', 'housingestate/my.vue', '', '', 'el-icon-office-building', 10, 0, 0, 1, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(456, 142, '服务订单', 0, 'orderservice', NULL, '', '', 'el-icon-tickets', 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(457, 456, '到店服务订单', 1, '/order/storeservice', 'order/storeservice.vue', '', '', 'el-icon-s-shop', 20, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(458, 39, '小程序页面', 1, '/miniprogrampage/index', 'miniprogrampage/index.vue', '', '', 'el-icon-s-flag', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(459, 36, '服务项目单位', 1, '/goodsquantityunitl/service', 'goodsquantityunitl/service.vue', '', '', 'el-icon-files', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(460, 459, '添加', 2, '/goodsquantityunitlservice/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(461, 459, '修改', 2, '/goodsquantityunitlservice/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(462, 459, '删除', 2, '/goodsquantityunitlservice/delete', NULL, '', '', '', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(463, 178, '商品单位', 1, '/goodsquantityunitl/goods', 'goodsquantityunitl/goods.vue', '', '', 'el-icon-files', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(464, 463, '添加', 2, '/goodsquantityunitlgoods/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(465, 463, '修改', 2, '/goodsquantityunitlgoods/update', NULL, '', '', NULL, 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(466, 463, '删除', 2, '/goodsquantityunitlgoods/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(467, 456, '在线服务订单', 1, '/order/onlineservice', 'order/onlineservice.vue', '', '', 'el-icon-s-flag', 30, 0, 0, 0, 1, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(468, 136, '会员喜爱选项', 1, '/reglike/index', 'reglike/index.vue', '', '', 'el-icon-s-grid', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 0),
(469, 468, '添加', 2, '/reglike/add', NULL, '', '', 'el-icon-menu', 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0, 1, 1, 1),
(470, 468, '修改', 2, '/reglike/update', NULL, '', '', 'el-icon-menu', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(471, 468, '删除', 2, '/reglike/delete', NULL, '', '', 'el-icon-menu', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(472, 497, '包月/包年卡', 1, '/timepack/index', 'timepack/index.vue', '', '', 'el-icon-date', 19, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(473, 472, '添加', 2, '/timepack/add', '', '', '', 'el-icon-close', 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(474, 472, '修改', 2, '/timepack/update', NULL, '', '', '', 30, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(475, 472, '删除', 2, '/timepack/delete', NULL, '', '', NULL, 30, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(476, 56, '导出', 2, '/member/dumpdata', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(477, 223, '导出', 2, '/agent/dumpdata', NULL, '', '', NULL, 477, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(478, 0, '收银', 1, 'cashregister/index', 'cashregister/index.vue', '', '', 'el-icon-s-claim', 10, 0, 0, 0, 0, 1, 0, 1, 0, 0, 1, 1, 0, 1),
(479, 0, '订单', 1, 'order', '', '', '', 'el-icon-s-grid', 479, 0, 0, 0, 0, 1, 0, 1, 0, 0, 1, 1, 0, 0),
(480, 497, '购物卡类型', 1, '/goodsgiftcardtype/index', 'goodsgiftcardtype/index.vue', '', '', 'el-icon-set-up', 21, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 0, 1),
(482, 249, '地图api设置', 1, '/sitesetup/lbsapi', 'sitesetup/lbsapi.vue', '', '', 'el-icon-position', 30, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(483, 14, '地图api设置', 1, '/config/lbsapi', 'sitesetup/lbsapi.vue', '', '', 'el-icon-position', 71, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(484, 223, '下级会员', 2, '/agent/subordinates', NULL, '', '', NULL, 30, 0, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1),
(485, 223, '推广订单', 2, '/agent/agentorder', NULL, '', '', NULL, 30, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(486, 56, '查看服务订单', 2, '/member/serviceorder', NULL, '', '', NULL, 30, 0, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1),
(487, 56, '查看商品订单', 2, '/member/goodsorder', NULL, '', '', NULL, 30, 0, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1),
(488, 428, '订单数', 2, '/dashboard/deliverOrder', NULL, '', '', NULL, 10, 0, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1),
(489, 428, '成交金额', 2, '/dashboard/order_paytotal', NULL, '', '', NULL, 20, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(490, 428, '售后订单', 2, '/dashboard/returnOrder', NULL, '', '', NULL, 30, 0, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1),
(491, 428, '会员数', 2, '/dashboard/member_count', NULL, '', '', NULL, 40, 0, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1),
(492, 428, '分销达人数/师傅数', 2, '/dashboard/agent_count', NULL, '', '', NULL, 50, 0, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1),
(493, 428, '成交额图表', 2, '/dashboard/clinchadeal', NULL, '', '', NULL, 60, 0, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1),
(494, 428, '销量排行TOP5', 2, '/dashboard/goodssaletop5', '', '', '', NULL, 70, 0, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1),
(495, 428, '排行TOP5', 2, '/dashboard/service_timestop5', NULL, '', '', NULL, 80, 0, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1),
(496, 428, '昨天转化', 2, '/dashboard/conversionrate', NULL, '', '', NULL, 90, 0, 1, 1, 1, 1, 1, 1, 0, 0, 1, 1, 1, 1),
(497, 0, '卡类管理', 0, 'goodscard', NULL, '', '', 'el-icon-bank-card', 29, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(498, 480, '添加', 2, '/goodsgiftcardtype/add', NULL, '', '', NULL, 10, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(499, 480, '修改', 2, '/goodsgiftcardtype/update', NULL, '', '', '', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(500, 480, '删除', 2, '/goodsgiftcardtype/delete', NULL, '', '', NULL, 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(501, 0, '反馈/奖惩', 0, 'relationship', NULL, '', '', 'el-icon-warning-outline', 40, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(502, 501, '投诉与反馈', 1, '/feedback/index.vue', 'feedback/index.vue', '', '', 'el-icon-info', 20, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(503, 501, '奖惩管理', 1, '/rewardspenalties/index', 'rewardspenalties/index.vue', '', '', 'el-icon-magic-stick', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(504, 142, '购物卡订单', 1, '/order/goodsgiftcard', 'order/goodsgiftcard.vue', '', '', 'el-icon-bank-card', 37, 0, 1, 1, 1, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(505, 0, '上门回收', 0, 'recovery', NULL, '', '', 'el-icon-refresh', 30, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(506, 505, '回收分类', 1, '/recoverycategory/index', 'recoverycategory/index.vue', '', '', 'el-icon-truck', 20, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(507, 506, '添加', 2, '/recoverycategory/add', NULL, '', '', NULL, 10, 0, 1, 1, 1, 1, 1, 1, 0, 0, 0, 1, 0, 1),
(508, 506, '修改', 2, '/recoverycategory/update', NULL, '', '', NULL, 20, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(509, 506, '删除', 2, '/recoverycategory/delete', NULL, '', '', NULL, 30, 0, 1, 1, 0, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(510, 505, '重量设置', 1, '/recoveryweight/index', 'recoveryweight/index.vue', '', '', 'el-icon-set-up', 20, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(511, 510, '添加', 2, '/recoveryweight/add', '', '', '', NULL, 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(512, 510, '修改', 2, '/recoveryweight/update', NULL, '', '', NULL, 20, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(513, 510, '删除', 2, '/recoveryweight/delete', NULL, '', '', NULL, 30, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(514, 501, '信用评分设置', 1, '/config/credit', 'config/credit.vue', '', '', 'el-icon-setting', 30, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1),
(515, 0, '房产租售', 0, 'house', NULL, '', '', 'el-icon-office-building', 30, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(516, 515, '房产出售', 1, '/housesell/index', 'housesell/index.vue', '', '', 'el-icon-office-building', 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(517, 454, '添加', 2, '/housingestate/add', NULL, '', '', NULL, 10, 0, 0, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(518, 454, '修改', 1, '/housingestate/update', NULL, '', '', '20', 20, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(519, 454, '删除', 2, '/housingestate/delete', NULL, '', '', '', 30, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(520, 516, '添加', 2, '/housesell/add', NULL, '', '', NULL, 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(521, 516, '修改', 2, '/housesell/update', NULL, '', '', NULL, 20, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(522, 516, '删除', 2, '/housesell/delete', '', '', '', NULL, 30, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(523, 515, '房产出租', 1, '/househire/index', 'househire/index.vue', '', '', 'el-icon-document-copy', 20, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(524, 523, '添加', 2, '/househire/add', NULL, '', '', NULL, 10, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(525, 523, '修改', 2, '/househire/update', NULL, '', '', NULL, 20, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1);
INSERT INTO `ims_xm_mallv3_sys_admin_menu` (`id`, `pid`, `title`, `type`, `path`, `pages_path`, `selected`, `params`, `icon`, `sort`, `is_console`, `is_city`, `is_tuanzhang`, `is_store`, `is_cashregister`, `is_admin`, `is_show`, `is_cache`, `w7_hidden`, `is_v2`, `is_v3`, `is_v6`, `status`) VALUES
(526, 523, '删除', 2, '/househire/delete', NULL, '', '', NULL, 30, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1),
(527, 247, '模块方案', 1, '/platformscheme/index', 'platformscheme/index.vue', '', '', 'el-icon-guide', 20, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(528, 527, '添加', 2, '/platformscheme/add', NULL, '', '', NULL, 10, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(529, 527, '修改', 2, '/platformscheme/update', NULL, '', '', NULL, 20, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(530, 527, '删除', 2, '/platformscheme/delete', NULL, '', '', NULL, 30, 1, 0, 0, 0, 0, 0, 1, 0, 0, 1, 1, 1, 1),
(531, 505, '回收订单', 1, '/recoveryorder/index', 'recoveryorder/index.vue', '', '', 'el-icon-s-order', 30, 0, 1, 1, 1, 0, 1, 1, 0, 0, 0, 1, 0, 1);

ALTER TABLE `ims_xm_mallv3_sys_admin_menu`
  ADD PRIMARY KEY (`id`) USING BTREE;

ALTER TABLE `ims_xm_mallv3_sys_admin_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=532;
COMMIT;");

pdo_run("DROP TABLE IF EXISTS `ims_xm_mallv3_bottom_menu_original`;
CREATE TABLE `ims_xm_mallv3_bottom_menu_original` (
  `id` int(10) UNSIGNED NOT NULL COMMENT '文档ID',
  `weid` int(11) NOT NULL,
  `module` varchar(30) DEFAULT NULL,
  `tid` int(3) DEFAULT '0',
  `title` varchar(50) DEFAULT NULL COMMENT '标题',
  `title1` varchar(50) DEFAULT '',
  `tiaojian` varchar(30) DEFAULT '',
  `url` varchar(160) DEFAULT NULL COMMENT '链接地址',
  `icon` varchar(160) DEFAULT NULL,
  `iconactive` varchar(160) NOT NULL,
  `hump` tinyint(1) DEFAULT '0' COMMENT '是否凸起',
  `plugin` varchar(60) NOT NULL COMMENT '插件',
  `is_submitaudit` tinyint(1) DEFAULT '0',
  `is_v2` tinyint(1) DEFAULT '0',
  `is_v3` tinyint(1) DEFAULT '0',
  `is_v6` tinyint(1) DEFAULT '0',
  `is_base` tinyint(1) DEFAULT '0',
  `is_index` tinyint(1) DEFAULT '0',
  `sort` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT '排序（同级有效）',
  `status` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='小程序底部菜单';

INSERT INTO `ims_xm_mallv3_bottom_menu_original` (`id`, `weid`, `module`, `tid`, `title`, `title1`, `tiaojian`, `url`, `icon`, `iconactive`, `hump`, `plugin`, `is_submitaudit`, `is_v2`, `is_v3`, `is_v6`, `is_base`, `is_index`, `sort`, `status`) VALUES
(32, 0, 'bottom', 0, '首页', '', '', '/pages/index/index', 'https://daopic.samcms.com/xm_static/images/nav/home-off.png', 'https://daopic.samcms.com/xm_static/images/nav/home-on.png', 0, '', 0, 1, 1, 1, 1, 1, 1, 1),
(33, 0, 'bottom', 0, '全部分类', '', '', '/pages/category/category', 'https://daopic.samcms.com/xm_static/images/nav/ic_catefory_normal.png', 'https://daopic.samcms.com/xm_static/images/nav/ic_catefory_pressed.png', 0, '', 1, 1, 1, 0, 1, 0, 2, 1),
(34, 0, 'bottom', 0, '购物车', '', '', '/pages/cart/cart', 'https://daopic.samcms.com/xm_static/images/nav/cart-off.png', 'https://daopic.samcms.com/xm_static/images/nav/cart-on.png', 0, '', 0, 1, 1, 0, 1, 0, 6, 1),
(35, 0, 'bottom', 0, '我的', '', '', '/pagesA/my/userInfo/index', 'https://daopic.samcms.com/xm_static/images/nav/my-off.png', 'https://daopic.samcms.com/xm_static/images/nav/my-on.png', 0, '', 0, 1, 1, 0, 1, 0, 7, 1),
(37, 0, 'bottom', 0, '附近商家', '', '', '/pages/store_list/store_list', 'https://daopic.samcms.com/xm_static/images/nav/store-off.png', 'https://daopic.samcms.com/xm_static/images/nav/store-on.png', 0, '', 1, 1, 1, 0, 1, 0, 3, 1),
(38, 0, 'bottom', 0, '师傅列表', '', '', '/pages/technical/list', 'https://daopic.samcms.com/xm_static/images/nav/technical-off.png', 'https://daopic.samcms.com/xm_static/images/nav/technical-on.png', 0, '', 1, 0, 1, 0, 1, 0, 5, 1),
(39, 0, 'bottom', 0, '需求', '', '', '/pagesA/submitOrder/demandOrder', 'https://daopic.samcms.com/xm_static/images/nav/add-off.png', 'https://daopic.samcms.com/xm_static/images/nav/add-on.png', 1, '', 1, 0, 1, 0, 1, 0, 4, 1),
(40, 0, 'member', 0, '店铺入口', '店铺入驻', 'is_storeadmin', '/pagesA/my/adminstore/index', 'https://daopic.samcms.com/xm_static/images/nav/my-shop.png', 'https://daopic.samcms.com/xm_static/images/nav/my-shop.png', 0, '', 1, 1, 1, 0, 1, 0, 100, 1),
(41, 0, 'member', 0, '师傅入口', '师傅入驻', 'is_technical', '/pagesA/my/admintechnical/index', 'https://daopic.samcms.com/xm_static/images/nav/my-technical.png', 'https://daopic.samcms.com/xm_static/images/nav/my-technical.png', 0, '', 1, 0, 1, 0, 1, 0, 80, 1),
(42, 0, 'member', 0, '管理员入口', '', '', '/pagesA/my/admin/index', 'https://daopic.samcms.com/xm_static/images/nav/my-admin.png', 'https://daopic.samcms.com/xm_static/images/nav/my-admin.png', 0, '', 1, 1, 1, 0, 1, 0, 110, 1),
(43, 0, 'member', 0, '用户订单', '', '', '/pagesA/my/myOrder/myOrder', 'https://daopic.samcms.com/xm_static/images/nav/my-order.png', 'https://daopic.samcms.com/xm_static/images/nav/my-order.png', 0, '', 1, 1, 1, 0, 0, 0, 3, 1),
(44, 0, 'bottom', 0, '文章', '', '', '/pages/article/list', 'https://daopic.samcms.com/xm_static/images/nav/article-off.png', 'https://daopic.samcms.com/xm_static/images/nav/article-on.png', 0, '', 0, 1, 1, 0, 1, 0, 6, 0),
(45, 0, 'bottom', 0, '师傅分类', '', '', '/pages/category/technicaldetailcategory', 'https://daopic.samcms.com/xm_static/images/nav/ic_catefory_normal.png', 'https://daopic.samcms.com/xm_static/images/nav/ic_catefory_pressed.png', 0, '', 0, 0, 1, 0, 0, 0, 2, 1),
(46, 0, 'technical', 0, '师傅接单', '', '', '/pagesA/my/publicOrder/order?identity=technical', 'https://daopic.samcms.com/xm_static/images/nav/publicorder-off.png', 'https://daopic.samcms.com/xm_static/images/nav/publicorder-on.png', 0, '', 0, 0, 1, 0, 1, 0, 2, 1),
(47, 0, 'technical', 0, '师傅订单', '', '', '/pagesA/my/admintechnical/order?ptype=2', 'https://daopic.samcms.com/xm_static/images/nav/order-off.png', 'https://daopic.samcms.com/xm_static/images/nav/order-on.png', 0, '', 0, 0, 1, 0, 1, 0, 2, 1),
(48, 0, 'technical', 0, '师傅工作台', '', '', '/pagesA/my/admintechnical/index', 'https://daopic.samcms.com/xm_static/images/nav/my-off.png', 'https://daopic.samcms.com/xm_static/images/nav/my-on.png', 0, '', 0, 0, 1, 0, 1, 0, 2, 1),
(49, 0, 'bottom', 0, '跑腿', '', '', '/pagesA/submitOrder/errandsOrder', 'https://daopic.samcms.com/xm_static/images/nav/errands-off.png', 'https://daopic.samcms.com/xm_static/images/nav/errands-on.png', 0, '', 0, 0, 1, 0, 0, 0, 3, 1),
(50, 0, 'member', 0, '城市代理入口', '城市代理入驻', 'is_adminoperatingcity', '/pagesA/my/adminoperatingcity/index', 'https://daopic.samcms.com/xm_static/images/nav/my-operatingcity.png', 'https://daopic.samcms.com/xm_static/images/nav/my-operatingcity.png', 0, '', 1, 1, 1, 0, 1, 0, 70, 1),
(51, 0, 'bottom', 0, '领券中心', '', '', '/pages/coupon/coupon', 'https://daopic.samcms.com/xm_static/images/nav/coupon-off.png', 'https://daopic.samcms.com/xm_static/images/nav/coupon-on.png', 0, '', 0, 1, 1, 0, 0, 0, 3, 1),
(52, 0, 'bottom', 0, '拼团专区', '', '', '/pages/groupList/groupList', 'https://daopic.samcms.com/xm_static/images/nav/group-off.png', 'https://daopic.samcms.com/xm_static/images/nav/group-on.png', 0, '', 0, 1, 1, 0, 0, 0, 3, 1),
(53, 0, 'bottom', 0, '限时秒杀', '', '', '/pages/seckillList/seckillList', 'https://daopic.samcms.com/xm_static/images/nav/seckill-off.png', 'https://daopic.samcms.com/xm_static/images/nav/seckill-on.png', 0, '', 0, 1, 1, 0, 0, 0, 3, 1),
(54, 0, 'bottom', 0, '短视频', '', '', '/pages/video/index', 'https://daopic.samcms.com/xm_static/images/nav/video-off.png', 'https://daopic.samcms.com/xm_static/images/nav/video-on.png', 0, '', 1, 1, 1, 0, 1, 0, 2, 0),
(55, 0, 'store', 0, '店铺接单', '', '', '/pagesA/my/publicOrder/order?identity=store', 'https://daopic.samcms.com/xm_static/images/nav/publicorder-off.png', 'https://daopic.samcms.com/xm_static/images/nav/publicorder-on.png', 0, '', 0, 0, 1, 0, 1, 0, 2, 1),
(56, 0, 'store', 0, '店铺订单', '', '', '/pagesA/my/adminstore/order?ptype=2', 'https://daopic.samcms.com/xm_static/images/nav/order-off.png', 'https://daopic.samcms.com/xm_static/images/nav/order-on.png', 0, '', 0, 0, 1, 0, 1, 0, 2, 1),
(57, 0, 'store', 0, '店铺工作台', '', '', '/pagesA/my/adminstore/index', 'https://daopic.samcms.com/xm_static/images/nav/my-off.png', 'https://daopic.samcms.com/xm_static/images/nav/my-on.png', 0, '', 0, 0, 1, 0, 1, 0, 2, 1),
(59, 0, 'member', 0, '合伙人', '合伙人申请', 'is_partneradmin', '/pagesA/my/partner/index', 'https://daopic.samcms.com/xm_static/images/nav/partner-off.png', 'https://daopic.samcms.com/xm_static/images/nav/partner-on.png', 0, '', 1, 1, 1, 0, 1, 0, 90, 0),
(60, 0, 'member', 0, '社区/团长入口', '社区/团长入驻', 'is_tuanzhang', '/pagesA/my/admintuanzhang/index', 'https://daopic.samcms.com/xm_static/images/nav/my-tuanzhang.png', 'https://daopic.samcms.com/xm_static/images/nav/my-tuanzhang.png', 0, '', 1, 1, 1, 0, 1, 0, 60, 1),
(61, 0, 'bottom', 999, '服务分类', '', '', '/pages/category/category?ptype=2', 'https://daopic.samcms.com/xm_static/images/nav/ic_catefory_normal.png', 'https://daopic.samcms.com/xm_static/images/nav/ic_catefory_pressed.png', 0, '', 1, 1, 1, 0, 1, 0, 2, 1),
(62, 0, 'bottom', 999, '商品分类', '', '', '/pages/category/category?ptype=1', 'https://daopic.samcms.com/xm_static/images/nav/ic_catefory_normal.png', 'https://daopic.samcms.com/xm_static/images/nav/ic_catefory_pressed.png', 0, '', 1, 1, 1, 0, 1, 0, 2, 1),
(63, 0, 'member', 0, '推广赚钱', '', '', '/pagesA/my/fx/index', 'https://daopic.samcms.com/xm_static/images/nav/icon_fanxian_3x.png', 'https://daopic.samcms.com/xm_static/images/nav/icon_fanxian_3x.png', 0, '', 0, 1, 1, 0, 1, 0, 10, 1),
(64, 0, 'member', 0, '领券中心', '', '', '/pages/coupon/coupon', 'https://daopic.samcms.com/xm_static/images/nav/icon_ticket_3x.png', 'https://daopic.samcms.com/xm_static/images/nav/icon_ticket_3x.png', 0, '', 0, 1, 1, 0, 1, 0, 21, 1),
(65, 0, 'member', 0, '直接付款', '', '', '/pagesA/my/maidan/index', 'https://daopic.samcms.com/xm_static/images/nav/fx.png', 'https://daopic.samcms.com/xm_static/images/nav/fx.png', 0, '', 0, 1, 1, 0, 1, 0, 30, 1),
(66, 0, 'member', 0, '我的卡包', '', '', '/pagesA/my/myOrder/myTimes', 'https://daopic.samcms.com/xm_static/images/nav/ka.png', 'https://daopic.samcms.com/xm_static/images/nav/ka.png', 0, '', 0, 0, 1, 0, 1, 0, 20, 1),
(67, 0, 'member', 0, '地址管理', '', '', '/pagesA/my/address/address', 'https://daopic.samcms.com/xm_static/images/nav/address.png', 'https://daopic.samcms.com/xm_static/images/nav/address.png', 0, '', 0, 1, 1, 0, 1, 0, 50, 1),
(68, 0, 'bottom', 0, '用户订单', '', '', '/pagesA/my/myOrder/myOrder', 'https://daopic.samcms.com/xm_static/images/nav/my-order.png', 'https://daopic.samcms.com/xm_static/images/nav/my-order.png', 0, '', 1, 0, 1, 1, 1, 0, 3, 0),
(69, 0, 'bottom', 0, '直播', '', '', '/pages/live_list/index', 'https://daopic.samcms.com/xm_static/images/nav/liveroom-off.png', 'https://daopic.samcms.com/xm_static/images/nav/liveroom-on.png', 0, '', 0, 1, 1, 0, 1, 0, 6, 0),
(70, 0, 'member', 0, '积分订单', '', '', '/pagesA/my/myOrder/myOrder?ispoints=1', 'https://daopic.samcms.com/xm_static/images/nav/my-order.png', 'https://daopic.samcms.com/xm_static/images/nav/my-order.png', 0, '', 1, 1, 1, 0, 0, 0, 3, 1),
(71, 0, 'member', 0, '每日签到', '', '', '/pagesA/my/signin/index', 'https://daopic.samcms.com/xm_static/images/nav/signin.png', 'https://daopic.samcms.com/xm_static/images/nav/signin.png', 0, '', 0, 1, 1, 0, 1, 0, 20, 1),
(72, 0, 'member', 0, '大转盘抽奖', '', '', '/pages/lottery/rotarytable', 'https://daopic.samcms.com/xm_static/images/nav/rotarytable.png', 'https://daopic.samcms.com/xm_static/images/nav/rotarytable.png', 0, '', 0, 1, 1, 0, 1, 0, 20, 1),
(73, 0, 'member', 0, 'VIP会员卡', '', '', '/pagesA/my/vip_paid/index', 'https://daopic.samcms.com/xm_static/images/nav/ka.png', 'https://daopic.samcms.com/xm_static/images/nav/ka.png', 0, '', 0, 1, 1, 0, 0, 0, 10, 1),
(75, 0, 'bottom', 0, '回收', '', '', '/pagesA/submitOrder/recoveryOrder', 'https://daopic.samcms.com/xm_static/images/nav/errands-off.png', 'https://daopic.samcms.com/xm_static/images/nav/errands-on.png', 0, '', 0, 0, 1, 0, 0, 0, 3, 1),
(76, 0, 'bottom', 0, '房产租售', '', '', '/pages/house/list', 'https://daopic.samcms.com/xm_static/images/nav/errands-off.png', 'https://daopic.samcms.com/xm_static/images/nav/errands-on.png', 0, '', 0, 0, 1, 0, 0, 0, 3, 1);

ALTER TABLE `ims_xm_mallv3_bottom_menu_original`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `ims_xm_mallv3_bottom_menu_original`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '文档ID', AUTO_INCREMENT=77;
COMMIT;");
  
pdo_run("DROP TABLE IF EXISTS `ims_xm_mallv3_dict_type`;
CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_dict_type` (
  `id` int(11) NOT NULL COMMENT 'id',
  `name` varchar(255) NOT NULL DEFAULT '' COMMENT '字典名称',
  `type` varchar(255) NOT NULL DEFAULT '' COMMENT '字典类型名称',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0-停用 1-正常',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间'
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COMMENT='字典类型表';

INSERT INTO `ims_xm_mallv3_dict_type` (`id`, `name`, `type`, `status`, `remark`, `create_time`, `update_time`, `delete_time`) VALUES
(5, '用户性别', 'sex', 1, '', 1656062946, 1656380925, NULL),
(6, '产品交付方式', 'goodsdeliverymode', 1, '', 1684236661, 1684236661, NULL),
(7, '服务交付方式', 'servicedeliverymode', 1, '', 1684236782, 1684236782, NULL);

ALTER TABLE `ims_xm_mallv3_dict_type`
  ADD PRIMARY KEY (`id`) USING BTREE;

ALTER TABLE `ims_xm_mallv3_dict_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',AUTO_INCREMENT=8;");

pdo_run("DROP TABLE IF EXISTS `ims_xm_mallv3_dict_data`;
CREATE TABLE IF NOT EXISTS `ims_xm_mallv3_dict_data` (
  `id` int(11) NOT NULL COMMENT 'id',
  `name` varchar(255) NOT NULL COMMENT '数据名称',
  `value` varchar(255) NOT NULL COMMENT '数据值',
  `type_id` int(11) NOT NULL COMMENT '字典类型id',
  `type_value` varchar(255) NOT NULL COMMENT '字典类型',
  `sort` int(10) DEFAULT '0' COMMENT '排序值',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态 0-停用 1-正常',
  `remark` varchar(255) DEFAULT '' COMMENT '备注',
  `create_time` int(10) NOT NULL COMMENT '创建时间',
  `update_time` int(10) DEFAULT NULL COMMENT '修改时间',
  `delete_time` int(10) DEFAULT NULL COMMENT '删除时间'
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COMMENT='字典数据表';

INSERT INTO `ims_xm_mallv3_dict_data` (`id`, `name`, `value`, `type_id`, `type_value`, `sort`, `status`, `remark`, `create_time`, `update_time`, `delete_time`) VALUES
(11, '保密', '0', 5, 'sex', 0, 1, '', 1656062988, 1656062988, NULL),
(12, '男', '1', 5, 'sex', 0, 1, '', 1656062999, 1656062999, NULL),
(13, '女', '2', 5, 'sex', 0, 1, '', 1656063009, 1656063009, NULL),
(14, '同城配送', '1', 6, 'goodsdeliverymode', 10, 1, '', 1684236686, 1684236686, NULL),
(15, '到店自提', '2', 6, 'goodsdeliverymode', 20, 1, '', 1684236698, 1684236698, NULL),
(16, '快递', '3', 6, 'goodsdeliverymode', 30, 1, '', 1684236722, 1684236722, NULL),
(17, '社区点自提', '5', 6, 'goodsdeliverymode', 50, 1, '', 1684236738, 1684236738, NULL),
(18, '上门服务', '1', 7, 'servicedeliverymode', 10, 1, '', 1684236799, 1684236799, NULL),
(19, '到店服务', '2', 7, 'servicedeliverymode', 20, 1, '', 1684236870, 1684236870, NULL),
(20, '在线服务', '4', 7, 'servicedeliverymode', 40, 1, '', 1684236880, 1684236880, NULL);

ALTER TABLE `ims_xm_mallv3_dict_data`
  ADD PRIMARY KEY (`id`) USING BTREE;

ALTER TABLE `ims_xm_mallv3_dict_data`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',AUTO_INCREMENT=21;");

pdo_run("DROP TABLE IF EXISTS `ims_xm_mallv3_area`;
CREATE TABLE `ims_xm_mallv3_area` (
  `id` int(11) UNSIGNED NOT NULL COMMENT '索引ID',
  `letter` varchar(3) DEFAULT '' COMMENT '首字母',
  `area_name` varchar(50) DEFAULT '' COMMENT '地区名称',
  `keyword` varchar(100) DEFAULT NULL COMMENT '关建词',
  `area_parent_id` int(11) UNSIGNED DEFAULT '0' COMMENT '地区父ID',
  `area_sort` int(6) UNSIGNED DEFAULT '0' COMMENT '排序',
  `area_deep` tinyint(1) UNSIGNED DEFAULT '1' COMMENT '地区深度，从1开始'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='地区表';

INSERT INTO `ims_xm_mallv3_area` (`id`, `letter`, `area_name`, `keyword`, `area_parent_id`, `area_sort`, `area_deep`) VALUES
(1, 'B', '北京', '北京BEIJING', 0, 0, 1),
(2, 'T', '天津', '天津TIANJIN', 0, 0, 1),
(3, 'H', '河北省', '河北省HEBEISHENG', 0, 0, 1),
(4, 'S', '山西省', '山西省SHANXISHENG', 0, 0, 1),
(5, 'N', '内蒙古', '内蒙古NEIMENGGU', 0, 0, 1),
(6, 'L', '辽宁省', '辽宁省LIAONINGSHENG', 0, 0, 1),
(7, 'J', '吉林省', '吉林省JILINSHENG', 0, 0, 1),
(8, 'H', '黑龙江省', '黑龙江省HEILONGJIANGSHENG', 0, 0, 1),
(9, 'S', '上海', '上海SHANGHAI', 0, 0, 1),
(10, 'J', '江苏省', '江苏省JIANGSUSHENG', 0, 0, 1),
(11, 'Z', '浙江省', '浙江省ZHEJIANGSHENG', 0, 0, 1),
(12, 'A', '安徽省', '安徽省ANHUISHENG', 0, 0, 1),
(13, 'F', '福建省', '福建省FUJIANSHENG', 0, 0, 1),
(14, 'J', '江西省', '江西省JIANGXISHENG', 0, 0, 1),
(15, 'S', '山东省', '山东省SHANDONGSHENG', 0, 0, 1),
(16, 'H', '河南省', '河南省HENANSHENG', 0, 0, 1),
(17, 'H', '湖北省', '湖北省HUBEISHENG', 0, 0, 1),
(18, 'H', '湖南省', '湖南省HUNANSHENG', 0, 0, 1),
(19, 'G', '广东省', '广东省GUANGDONGSHENG', 0, 0, 1),
(20, 'G', '广西', '广西GUANGXI', 0, 0, 1),
(21, 'H', '海南省', '海南省HAINANSHENG', 0, 0, 1),
(22, 'C', '重庆', '重庆CHONGQING', 0, 0, 1),
(23, 'S', '四川省', '四川省SICHUANSHENG', 0, 0, 1),
(24, 'G', '贵州省', '贵州省GUIZHOUSHENG', 0, 0, 1),
(25, 'Y', '云南省', '云南省YUNNANSHENG', 0, 0, 1),
(26, 'X', '西藏', '西藏XIZANG', 0, 0, 1),
(27, 'S', '陕西省', '陕西省SHANXISHENG', 0, 0, 1),
(28, 'G', '甘肃省', '甘肃省GANSUSHENG', 0, 0, 1),
(29, 'Q', '青海省', '青海省QINGHAISHENG', 0, 0, 1),
(30, 'N', '宁夏', '宁夏NINGXIA', 0, 0, 1),
(31, 'X', '新疆', '新疆XINJIANG', 0, 0, 1),
(32, 'T', '台湾省', '台湾省TAIWANSHENG', 0, 0, 1),
(33, 'X', '香港', '香港XIANGGANG', 0, 0, 1),
(34, 'A', '澳门', '澳门AOMEN', 0, 0, 1),
(36, 'B', '北京市', '北京市BEIJINGSHI', 1, 0, 2),
(37, 'D', '东城区', '东城区DONGCHENGQU', 36, 0, 3),
(38, 'X', '西城区', '西城区XICHENGQU', 36, 0, 3),
(39, 'S', '上海市', '上海市SHANGHAISHI', 9, 0, 2),
(40, 'T', '天津市', '天津市TIANJINSHI', 2, 0, 2),
(41, 'C', '朝阳区', '朝阳区CHAOYANGQU', 36, 0, 3),
(42, 'F', '丰台区', '丰台区FENGTAIQU', 36, 0, 3),
(43, 'S', '石景山区', '石景山区SHIJINGSHANQU', 36, 0, 3),
(44, 'H', '海淀区', '海淀区HAIDIANQU', 36, 0, 3),
(45, 'M', '门头沟区', '门头沟区MENTOUGOUQU', 36, 0, 3),
(46, 'F', '房山区', '房山区FANGSHANQU', 36, 0, 3),
(47, 'T', '通州区', '通州区TONGZHOUQU', 36, 0, 3),
(48, 'S', '顺义区', '顺义区SHUNYIQU', 36, 0, 3),
(49, 'C', '昌平区', '昌平区CHANGPINGQU', 36, 0, 3),
(50, 'D', '大兴区', '大兴区DAXINGQU', 36, 0, 3),
(51, 'H', '怀柔区', '怀柔区HUAIROUQU', 36, 0, 3),
(52, 'P', '平谷区', '平谷区PINGGUQU', 36, 0, 3),
(53, 'M', '密云县', '密云县MIYUNXIAN', 36, 0, 3),
(54, 'Y', '延庆县', '延庆县YANQINGXIAN', 36, 0, 3),
(55, 'H', '和平区', '和平区HEPINGQU', 40, 0, 3),
(56, 'H', '河东区', '河东区HEDONGQU', 40, 0, 3),
(57, 'H', '河西区', '河西区HEXIQU', 40, 0, 3),
(58, 'N', '南开区', '南开区NANKAIQU', 40, 0, 3),
(59, 'H', '河北区', '河北区HEBEIQU', 40, 0, 3),
(60, 'H', '红桥区', '红桥区HONGQIAOQU', 40, 0, 3),
(61, 'T', '塘沽区', '塘沽区TANGGUQU', 40, 0, 3),
(62, 'C', '重庆市', '重庆市CHONGQINGSHI', 22, 0, 2),
(64, 'D', '东丽区', '东丽区DONGLIQU', 40, 0, 3),
(65, 'X', '西青区', '西青区XIQINGQU', 40, 0, 3),
(66, 'J', '津南区', '津南区JINNANQU', 40, 0, 3),
(67, 'B', '北辰区', '北辰区BEICHENQU', 40, 0, 3),
(68, 'W', '武清区', '武清区WUQINGQU', 40, 0, 3),
(69, 'B', '宝坻区', '宝坻区BAODIQU', 40, 0, 3),
(70, 'N', '宁河县', '宁河县NINGHEXIAN', 40, 0, 3),
(71, 'J', '静海县', '静海县JINGHAIXIAN', 40, 0, 3),
(72, 'J', '蓟县', '蓟县JIXIAN', 40, 0, 3),
(73, 'S', '石家庄市', '石家庄市SHIJIAZHUANGSHI', 3, 0, 2),
(74, 'T', '唐山市', '唐山市TANGSHANSHI', 3, 0, 2),
(75, 'Q', '秦皇岛市', '秦皇岛市QINHUANGDAOSHI', 3, 0, 2),
(76, 'H', '邯郸市', '邯郸市HANDANSHI', 3, 0, 2),
(77, 'X', '邢台市', '邢台市XINGTAISHI', 3, 0, 2),
(78, 'B', '保定市', '保定市BAODINGSHI', 3, 0, 2),
(79, 'Z', '张家口市', '张家口市ZHANGJIAKOUSHI', 3, 0, 2),
(80, 'C', '承德市', '承德市CHENGDESHI', 3, 0, 2),
(81, 'H', '衡水市', '衡水市HENGSHUISHI', 3, 0, 2),
(82, 'L', '廊坊市', '廊坊市LANGFANGSHI', 3, 0, 2),
(83, 'C', '沧州市', '沧州市CANGZHOUSHI', 3, 0, 2),
(84, 'T', '太原市', '太原市TAIYUANSHI', 4, 0, 2),
(85, 'D', '大同市', '大同市DATONGSHI', 4, 0, 2),
(86, 'Y', '阳泉市', '阳泉市YANGQUANSHI', 4, 0, 2),
(87, 'C', '长治市', '长治市CHANGZHISHI', 4, 0, 2),
(88, 'J', '晋城市', '晋城市JINCHENGSHI', 4, 0, 2),
(89, 'S', '朔州市', '朔州市SHUOZHOUSHI', 4, 0, 2),
(90, 'J', '晋中市', '晋中市JINZHONGSHI', 4, 0, 2),
(91, 'Y', '运城市', '运城市YUNCHENGSHI', 4, 0, 2),
(92, 'X', '忻州市', '忻州市XINZHOUSHI', 4, 0, 2),
(93, 'L', '临汾市', '临汾市LINFENSHI', 4, 0, 2),
(94, 'L', '吕梁市', '吕梁市LYULIANGSHI', 4, 0, 2),
(95, 'H', '呼和浩特市', '呼和浩特市HUHEHAOTESHI', 5, 0, 2),
(96, 'B', '包头市', '包头市BAOTOUSHI', 5, 0, 2),
(97, 'W', '乌海市', '乌海市WUHAISHI', 5, 0, 2),
(98, 'C', '赤峰市', '赤峰市CHIFENGSHI', 5, 0, 2),
(99, 'T', '通辽市', '通辽市TONGLIAOSHI', 5, 0, 2),
(100, 'E', '鄂尔多斯市', '鄂尔多斯市EERDUOSISHI', 5, 0, 2),
(101, 'H', '呼伦贝尔市', '呼伦贝尔市HULUNBEIERSHI', 5, 0, 2),
(102, 'B', '巴彦淖尔市', '巴彦淖尔市BAYANNAOERSHI', 5, 0, 2),
(103, 'W', '乌兰察布市', '乌兰察布市WULANCHABUSHI', 5, 0, 2),
(104, 'X', '兴安盟', '兴安盟XINGANMENG', 5, 0, 2),
(105, 'X', '锡林郭勒盟', '锡林郭勒盟XILINGUOLEMENG', 5, 0, 2),
(106, 'A', '阿拉善盟', '阿拉善盟ALASHANMENG', 5, 0, 2),
(107, 'S', '沈阳市', '沈阳市SHENYANGSHI', 6, 0, 2),
(108, 'D', '大连市', '大连市DALIANSHI', 6, 0, 2),
(109, 'A', '鞍山市', '鞍山市ANSHANSHI', 6, 0, 2),
(110, 'F', '抚顺市', '抚顺市FUSHUNSHI', 6, 0, 2),
(111, 'B', '本溪市', '本溪市BENXISHI', 6, 0, 2),
(112, 'D', '丹东市', '丹东市DANDONGSHI', 6, 0, 2),
(113, 'J', '锦州市', '锦州市JINZHOUSHI', 6, 0, 2),
(114, 'Y', '营口市', '营口市YINGKOUSHI', 6, 0, 2),
(115, 'F', '阜新市', '阜新市FUXINSHI', 6, 0, 2),
(116, 'L', '辽阳市', '辽阳市LIAOYANGSHI', 6, 0, 2),
(117, 'P', '盘锦市', '盘锦市PANJINSHI', 6, 0, 2),
(118, 'T', '铁岭市', '铁岭市TIELINGSHI', 6, 0, 2),
(119, 'Z', '朝阳市', '朝阳市ZHAOYANGSHI', 6, 0, 2),
(120, 'H', '葫芦岛市', '葫芦岛市HULUDAOSHI', 6, 0, 2),
(121, 'C', '长春市', '长春市CHANGCHUNSHI', 7, 0, 2),
(122, 'J', '吉林市', '吉林市JILINSHI', 7, 0, 2),
(123, 'S', '四平市', '四平市SIPINGSHI', 7, 0, 2),
(124, 'L', '辽源市', '辽源市LIAOYUANSHI', 7, 0, 2),
(125, 'T', '通化市', '通化市TONGHUASHI', 7, 0, 2),
(126, 'B', '白山市', '白山市BAISHANSHI', 7, 0, 2),
(127, 'S', '松原市', '松原市SONGYUANSHI', 7, 0, 2),
(128, 'B', '白城市', '白城市BAICHENGSHI', 7, 0, 2),
(129, 'Y', '延边朝鲜族自治州', '延边朝鲜族自治州YANBIANCHAOXIANZUZIZHIZHOU', 7, 0, 2),
(130, 'H', '哈尔滨市', '哈尔滨市HAERBINSHI', 8, 0, 2),
(131, 'Q', '齐齐哈尔市', '齐齐哈尔市QIQIHAERSHI', 8, 0, 2),
(132, 'J', '鸡西市', '鸡西市JIXISHI', 8, 0, 2),
(133, 'H', '鹤岗市', '鹤岗市HEGANGSHI', 8, 0, 2),
(134, 'S', '双鸭山市', '双鸭山市SHUANGYASHANSHI', 8, 0, 2),
(135, 'D', '大庆市', '大庆市DAQINGSHI', 8, 0, 2),
(136, 'Y', '伊春市', '伊春市YICHUNSHI', 8, 0, 2),
(137, 'J', '佳木斯市', '佳木斯市JIAMUSISHI', 8, 0, 2),
(138, 'Q', '七台河市', '七台河市QITAIHESHI', 8, 0, 2),
(139, 'M', '牡丹江市', '牡丹江市MUDANJIANGSHI', 8, 0, 2),
(140, 'H', '黑河市', '黑河市HEIHESHI', 8, 0, 2),
(141, 'S', '绥化市', '绥化市SUIHUASHI', 8, 0, 2),
(142, 'D', '大兴安岭地区', '大兴安岭地区DAXINGANLINGDIQU', 8, 0, 2),
(143, 'H', '黄浦区', '黄浦区HUANGPUQU', 39, 0, 3),
(144, 'L', '卢湾区', '卢湾区LUWANQU', 39, 0, 3),
(145, 'X', '徐汇区', '徐汇区XUHUIQU', 39, 0, 3),
(146, 'C', '长宁区', '长宁区CHANGNINGQU', 39, 0, 3),
(147, 'J', '静安区', '静安区JINGANQU', 39, 0, 3),
(148, 'P', '普陀区', '普陀区PUTUOQU', 39, 0, 3),
(149, 'Z', '闸北区', '闸北区ZHABEIQU', 39, 0, 3),
(150, 'H', '虹口区', '虹口区HONGKOUQU', 39, 0, 3),
(151, 'Y', '杨浦区', '杨浦区YANGPUQU', 39, 0, 3),
(152, 'M', '闵行区', '闵行区MINHANGQU', 39, 0, 3),
(153, 'B', '宝山区', '宝山区BAOSHANQU', 39, 0, 3),
(154, 'J', '嘉定区', '嘉定区JIADINGQU', 39, 0, 3),
(155, 'P', '浦东新区', '浦东新区PUDONGXINQU', 39, 0, 3),
(156, 'J', '金山区', '金山区JINSHANQU', 39, 0, 3),
(157, 'S', '松江区', '松江区SONGJIANGQU', 39, 0, 3),
(158, 'Q', '青浦区', '青浦区QINGPUQU', 39, 0, 3),
(159, 'N', '南汇区', '南汇区NANHUIQU', 39, 0, 3),
(160, 'F', '奉贤区', '奉贤区FENGXIANQU', 39, 0, 3),
(161, 'C', '崇明县', '崇明县CHONGMINGXIAN', 39, 0, 3),
(162, 'N', '南京市', '南京市NANJINGSHI', 10, 0, 2),
(163, 'W', '无锡市', '无锡市WUXISHI', 10, 0, 2),
(164, 'X', '徐州市', '徐州市XUZHOUSHI', 10, 0, 2),
(165, 'C', '常州市', '常州市CHANGZHOUSHI', 10, 0, 2),
(166, 'S', '苏州市', '苏州市SUZHOUSHI', 10, 0, 2),
(167, 'N', '南通市', '南通市NANTONGSHI', 10, 0, 2),
(168, 'L', '连云港市', '连云港市LIANYUNGANGSHI', 10, 0, 2),
(169, 'H', '淮安市', '淮安市HUAIANSHI', 10, 0, 2),
(170, 'Y', '盐城市', '盐城市YANCHENGSHI', 10, 0, 2),
(171, 'Y', '扬州市', '扬州市YANGZHOUSHI', 10, 0, 2),
(172, 'Z', '镇江市', '镇江市ZHENJIANGSHI', 10, 0, 2),
(173, 'T', '泰州市', '泰州市TAIZHOUSHI', 10, 0, 2),
(174, 'S', '宿迁市', '宿迁市SUQIANSHI', 10, 0, 2),
(175, 'H', '杭州市', '杭州市HANGZHOUSHI', 11, 0, 2),
(176, 'N', '宁波市', '宁波市NINGBOSHI', 11, 0, 2),
(177, 'W', '温州市', '温州市WENZHOUSHI', 11, 0, 2),
(178, 'J', '嘉兴市', '嘉兴市JIAXINGSHI', 11, 0, 2),
(179, 'H', '湖州市', '湖州市HUZHOUSHI', 11, 0, 2),
(180, 'S', '绍兴市', '绍兴市SHAOXINGSHI', 11, 0, 2),
(181, 'Z', '舟山市', '舟山市ZHOUSHANSHI', 11, 0, 2),
(182, 'Q', '衢州市', '衢州市QUZHOUSHI', 11, 0, 2),
(183, 'J', '金华市', '金华市JINHUASHI', 11, 0, 2),
(184, 'T', '台州市', '台州市TAIZHOUSHI', 11, 0, 2),
(185, 'L', '丽水市', '丽水市LISHUISHI', 11, 0, 2),
(186, 'H', '合肥市', '合肥市HEFEISHI', 12, 0, 2),
(187, 'W', '芜湖市', '芜湖市WUHUSHI', 12, 0, 2),
(188, 'B', '蚌埠市', '蚌埠市BENGBUSHI', 12, 0, 2),
(189, 'H', '淮南市', '淮南市HUAINANSHI', 12, 0, 2),
(190, 'M', '马鞍山市', '马鞍山市MAANSHANSHI', 12, 0, 2),
(191, 'H', '淮北市', '淮北市HUAIBEISHI', 12, 0, 2),
(192, 'T', '铜陵市', '铜陵市TONGLINGSHI', 12, 0, 2),
(193, 'A', '安庆市', '安庆市ANQINGSHI', 12, 0, 2),
(194, 'H', '黄山市', '黄山市HUANGSHANSHI', 12, 0, 2),
(195, 'C', '滁州市', '滁州市CHUZHOUSHI', 12, 0, 2),
(196, 'F', '阜阳市', '阜阳市FUYANGSHI', 12, 0, 2),
(197, 'S', '宿州市', '宿州市SUZHOUSHI', 12, 0, 2),
(198, 'C', '巢湖市', '巢湖市CHAOHUSHI', 12, 0, 2),
(199, 'L', '六安市', '六安市LUANSHI', 12, 0, 2),
(200, 'B', '亳州市', '亳州市BOZHOUSHI', 12, 0, 2),
(201, 'C', '池州市', '池州市CHIZHOUSHI', 12, 0, 2),
(202, 'X', '宣城市', '宣城市XUANCHENGSHI', 12, 0, 2),
(203, 'F', '福州市', '福州市FUZHOUSHI', 13, 0, 2),
(204, 'X', '厦门市', '厦门市XIAMENSHI', 13, 0, 2),
(205, 'P', '莆田市', '莆田市PUTIANSHI', 13, 0, 2),
(206, 'S', '三明市', '三明市SANMINGSHI', 13, 0, 2),
(207, 'Q', '泉州市', '泉州市QUANZHOUSHI', 13, 0, 2),
(208, 'Z', '漳州市', '漳州市ZHANGZHOUSHI', 13, 0, 2),
(209, 'N', '南平市', '南平市NANPINGSHI', 13, 0, 2),
(210, 'L', '龙岩市', '龙岩市LONGYANSHI', 13, 0, 2),
(211, 'N', '宁德市', '宁德市NINGDESHI', 13, 0, 2),
(212, 'N', '南昌市', '南昌市NANCHANGSHI', 14, 0, 2),
(213, 'J', '景德镇市', '景德镇市JINGDEZHENSHI', 14, 0, 2),
(214, 'P', '萍乡市', '萍乡市PINGXIANGSHI', 14, 0, 2),
(215, 'J', '九江市', '九江市JIUJIANGSHI', 14, 0, 2),
(216, 'X', '新余市', '新余市XINYUSHI', 14, 0, 2),
(217, 'Y', '鹰潭市', '鹰潭市YINGTANSHI', 14, 0, 2),
(218, 'G', '赣州市', '赣州市GANZHOUSHI', 14, 0, 2),
(219, 'J', '吉安市', '吉安市JIANSHI', 14, 0, 2),
(220, 'Y', '宜春市', '宜春市YICHUNSHI', 14, 0, 2),
(221, 'F', '抚州市', '抚州市FUZHOUSHI', 14, 0, 2),
(222, 'S', '上饶市', '上饶市SHANGRAOSHI', 14, 0, 2),
(223, 'J', '济南市', '济南市JINANSHI', 15, 0, 2),
(224, 'Q', '青岛市', '青岛市QINGDAOSHI', 15, 0, 2),
(225, 'Z', '淄博市', '淄博市ZIBOSHI', 15, 0, 2),
(226, 'Z', '枣庄市', '枣庄市ZAOZHUANGSHI', 15, 0, 2),
(227, 'D', '东营市', '东营市DONGYINGSHI', 15, 0, 2),
(228, 'Y', '烟台市', '烟台市YANTAISHI', 15, 0, 2),
(229, 'W', '潍坊市', '潍坊市WEIFANGSHI', 15, 0, 2),
(230, 'J', '济宁市', '济宁市JININGSHI', 15, 0, 2),
(231, 'T', '泰安市', '泰安市TAIANSHI', 15, 0, 2),
(232, 'W', '威海市', '威海市WEIHAISHI', 15, 0, 2),
(233, 'R', '日照市', '日照市RIZHAOSHI', 15, 0, 2),
(234, 'L', '莱芜市', '莱芜市LAIWUSHI', 15, 0, 2),
(235, 'L', '临沂市', '临沂市LINYISHI', 15, 0, 2),
(236, 'D', '德州市', '德州市DEZHOUSHI', 15, 0, 2),
(237, 'L', '聊城市', '聊城市LIAOCHENGSHI', 15, 0, 2),
(238, 'B', '滨州市', '滨州市BINZHOUSHI', 15, 0, 2),
(239, 'H', '菏泽市', '菏泽市HEZESHI', 15, 0, 2),
(240, 'Z', '郑州市', '郑州市ZHENGZHOUSHI', 16, 0, 2),
(241, 'K', '开封市', '开封市KAIFENGSHI', 16, 0, 2),
(242, 'L', '洛阳市', '洛阳市LUOYANGSHI', 16, 0, 2),
(243, 'P', '平顶山市', '平顶山市PINGDINGSHANSHI', 16, 0, 2),
(244, 'A', '安阳市', '安阳市ANYANGSHI', 16, 0, 2),
(245, 'H', '鹤壁市', '鹤壁市HEBISHI', 16, 0, 2),
(246, 'X', '新乡市', '新乡市XINXIANGSHI', 16, 0, 2),
(247, 'J', '焦作市', '焦作市JIAOZUOSHI', 16, 0, 2),
(248, 'P', '濮阳市', '濮阳市PUYANGSHI', 16, 0, 2),
(249, 'X', '许昌市', '许昌市XUCHANGSHI', 16, 0, 2),
(250, 'T', '漯河市', '漯河市TAHESHI', 16, 0, 2),
(251, 'S', '三门峡市', '三门峡市SANMENXIASHI', 16, 0, 2),
(252, 'N', '南阳市', '南阳市NANYANGSHI', 16, 0, 2),
(253, 'S', '商丘市', '商丘市SHANGQIUSHI', 16, 0, 2),
(254, 'X', '信阳市', '信阳市XINYANGSHI', 16, 0, 2),
(255, 'Z', '周口市', '周口市ZHOUKOUSHI', 16, 0, 2),
(256, 'Z', '驻马店市', '驻马店市ZHUMADIANSHI', 16, 0, 2),
(257, 'J', '济源市', '济源市JIYUANSHI', 16, 0, 2),
(258, 'W', '武汉市', '武汉市WUHANSHI', 17, 0, 2),
(259, 'H', '黄石市', '黄石市HUANGSHISHI', 17, 0, 2),
(260, 'S', '十堰市', '十堰市SHIYANSHI', 17, 0, 2),
(261, 'Y', '宜昌市', '宜昌市YICHANGSHI', 17, 0, 2),
(262, 'X', '襄阳市', '襄阳市XIANGYANGSHI', 17, 0, 2),
(263, 'E', '鄂州市', '鄂州市EZHOUSHI', 17, 0, 2),
(264, 'J', '荆门市', '荆门市JINGMENSHI', 17, 0, 2),
(265, 'X', '孝感市', '孝感市XIAOGANSHI', 17, 0, 2),
(266, 'J', '荆州市', '荆州市JINGZHOUSHI', 17, 0, 2),
(267, 'H', '黄冈市', '黄冈市HUANGGANGSHI', 17, 0, 2),
(268, 'X', '咸宁市', '咸宁市XIANNINGSHI', 17, 0, 2),
(269, 'S', '随州市', '随州市SUIZHOUSHI', 17, 0, 2),
(270, 'E', '恩施土家族苗族自治州', '恩施土家族苗族自治州ENSHITUJIAZUMIAOZUZIZHIZHOU', 17, 0, 2),
(271, 'X', '仙桃市', '仙桃市XIANTAOSHI', 17, 0, 2),
(272, 'Q', '潜江市', '潜江市QIANJIANGSHI', 17, 0, 2),
(273, 'T', '天门市', '天门市TIANMENSHI', 17, 0, 2),
(274, 'S', '神农架林区', '神农架林区SHENNONGJIALINQU', 17, 0, 2),
(275, 'C', '长沙市', '长沙市CHANGSHASHI', 18, 0, 2),
(276, 'Z', '株洲市', '株洲市ZHUZHOUSHI', 18, 0, 2),
(277, 'X', '湘潭市', '湘潭市XIANGTANSHI', 18, 0, 2),
(278, 'H', '衡阳市', '衡阳市HENGYANGSHI', 18, 0, 2),
(279, 'S', '邵阳市', '邵阳市SHAOYANGSHI', 18, 0, 2),
(280, 'Y', '岳阳市', '岳阳市YUEYANGSHI', 18, 0, 2),
(281, 'C', '常德市', '常德市CHANGDESHI', 18, 0, 2),
(282, 'Z', '张家界市', '张家界市ZHANGJIAJIESHI', 18, 0, 2),
(283, 'Y', '益阳市', '益阳市YIYANGSHI', 18, 0, 2),
(284, 'C', '郴州市', '郴州市CHENZHOUSHI', 18, 0, 2),
(285, 'Y', '永州市', '永州市YONGZHOUSHI', 18, 0, 2),
(286, 'H', '怀化市', '怀化市HUAIHUASHI', 18, 0, 2),
(287, 'L', '娄底市', '娄底市LOUDISHI', 18, 0, 2),
(288, 'X', '湘西土家族苗族自治州', '湘西土家族苗族自治州XIANGXITUJIAZUMIAOZUZIZHIZHOU', 18, 0, 2),
(289, 'G', '广州市', '广州市GUANGZHOUSHI', 19, 0, 2),
(290, 'S', '韶关市', '韶关市SHAOGUANSHI', 19, 0, 2),
(291, 'S', '深圳市', '深圳市SHENZHENSHI', 19, 0, 2),
(292, 'Z', '珠海市', '珠海市ZHUHAISHI', 19, 0, 2),
(293, 'S', '汕头市', '汕头市SHANTOUSHI', 19, 0, 2),
(294, 'F', '佛山市', '佛山市FOSHANSHI', 19, 0, 2),
(295, 'J', '江门市', '江门市JIANGMENSHI', 19, 0, 2),
(296, 'Z', '湛江市', '湛江市ZHANJIANGSHI', 19, 0, 2),
(297, 'M', '茂名市', '茂名市MAOMINGSHI', 19, 0, 2),
(298, 'Z', '肇庆市', '肇庆市ZHAOQINGSHI', 19, 0, 2),
(299, 'H', '惠州市', '惠州市HUIZHOUSHI', 19, 0, 2),
(300, 'M', '梅州市', '梅州市MEIZHOUSHI', 19, 0, 2),
(301, 'S', '汕尾市', '汕尾市SHANWEISHI', 19, 0, 2),
(302, 'H', '河源市', '河源市HEYUANSHI', 19, 0, 2),
(303, 'Y', '阳江市', '阳江市YANGJIANGSHI', 19, 0, 2),
(304, 'Q', '清远市', '清远市QINGYUANSHI', 19, 0, 2),
(305, 'D', '东莞市', '东莞市DONGGUANSHI', 19, 0, 2),
(306, 'Z', '中山市', '中山市ZHONGSHANSHI', 19, 0, 2),
(307, 'C', '潮州市', '潮州市CHAOZHOUSHI', 19, 0, 2),
(308, 'J', '揭阳市', '揭阳市JIEYANGSHI', 19, 0, 2),
(309, 'Y', '云浮市', '云浮市YUNFUSHI', 19, 0, 2),
(310, 'N', '南宁市', '南宁市NANNINGSHI', 20, 0, 2),
(311, 'L', '柳州市', '柳州市LIUZHOUSHI', 20, 0, 2),
(312, 'G', '桂林市', '桂林市GUILINSHI', 20, 0, 2),
(313, 'W', '梧州市', '梧州市WUZHOUSHI', 20, 0, 2),
(314, 'B', '北海市', '北海市BEIHAISHI', 20, 0, 2),
(315, 'F', '防城港市', '防城港市FANGCHENGGANGSHI', 20, 0, 2),
(316, 'Q', '钦州市', '钦州市QINZHOUSHI', 20, 0, 2),
(317, 'G', '贵港市', '贵港市GUIGANGSHI', 20, 0, 2),
(318, 'Y', '玉林市', '玉林市YULINSHI', 20, 0, 2),
(319, 'B', '百色市', '百色市BAISESHI', 20, 0, 2),
(320, 'H', '贺州市', '贺州市HEZHOUSHI', 20, 0, 2),
(321, 'H', '河池市', '河池市HECHISHI', 20, 0, 2),
(322, 'L', '来宾市', '来宾市LAIBINSHI', 20, 0, 2),
(323, 'C', '崇左市', '崇左市CHONGZUOSHI', 20, 0, 2),
(324, 'H', '海口市', '海口市HAIKOUSHI', 21, 0, 2),
(325, 'S', '三亚市', '三亚市SANYASHI', 21, 0, 2),
(326, 'W', '五指山市', '五指山市WUZHISHANSHI', 21, 0, 2),
(327, 'Q', '琼海市', '琼海市QIONGHAISHI', 21, 0, 2),
(328, 'D', '儋州市', '儋州市DANZHOUSHI', 21, 0, 2),
(329, 'W', '文昌市', '文昌市WENCHANGSHI', 21, 0, 2),
(330, 'W', '万宁市', '万宁市WANNINGSHI', 21, 0, 2),
(331, 'D', '东方市', '东方市DONGFANGSHI', 21, 0, 2),
(332, 'D', '定安县', '定安县DINGANXIAN', 21, 0, 2),
(333, 'T', '屯昌县', '屯昌县TUNCHANGXIAN', 21, 0, 2),
(334, 'C', '澄迈县', '澄迈县CHENGMAIXIAN', 21, 0, 2),
(335, 'L', '临高县', '临高县LINGAOXIAN', 21, 0, 2),
(336, 'B', '白沙黎族自治县', '白沙黎族自治县BAISHALIZUZIZHIXIAN', 21, 0, 2),
(337, 'C', '昌江黎族自治县', '昌江黎族自治县CHANGJIANGLIZUZIZHIXIAN', 21, 0, 2),
(338, 'L', '乐东黎族自治县', '乐东黎族自治县LEDONGLIZUZIZHIXIAN', 21, 0, 2),
(339, 'L', '陵水黎族自治县', '陵水黎族自治县LINGSHUILIZUZIZHIXIAN', 21, 0, 2),
(340, 'B', '保亭黎族苗族自治县', '保亭黎族苗族自治县BAOTINGLIZUMIAOZUZIZHIXIAN', 21, 0, 2),
(341, 'Q', '琼中黎族苗族自治县', '琼中黎族苗族自治县QIONGZHONGLIZUMIAOZUZIZHIXIAN', 21, 0, 2),
(342, 'X', '西沙群岛', '西沙群岛XISHAQUNDAO', 21, 0, 2),
(343, 'N', '南沙群岛', '南沙群岛NANSHAQUNDAO', 21, 0, 2),
(344, 'Z', '中沙群岛的岛礁及其海域', '中沙群岛的岛礁及其海域ZHONGSHAQUNDAODEDAOJIAOJIQIHAIYU', 21, 0, 2),
(345, 'W', '万州区', '万州区WANZHOUQU', 62, 0, 3),
(346, 'F', '涪陵区', '涪陵区FULINGQU', 62, 0, 3),
(347, 'Y', '渝中区', '渝中区YUZHONGQU', 62, 0, 3),
(348, 'D', '大渡口区', '大渡口区DADUKOUQU', 62, 0, 3),
(349, 'J', '江北区', '江北区JIANGBEIQU', 62, 0, 3),
(350, 'S', '沙坪坝区', '沙坪坝区SHAPINGBAQU', 62, 0, 3),
(351, 'J', '九龙坡区', '九龙坡区JIULONGPOQU', 62, 0, 3),
(352, 'N', '南岸区', '南岸区NANANQU', 62, 0, 3),
(353, 'B', '北碚区', '北碚区BEIBEIQU', 62, 0, 3),
(354, 'S', '双桥区', '双桥区SHUANGQIAOQU', 62, 0, 3),
(355, 'W', '万盛区', '万盛区WANSHENGQU', 62, 0, 3),
(356, 'Y', '渝北区', '渝北区YUBEIQU', 62, 0, 3),
(357, 'B', '巴南区', '巴南区BANANQU', 62, 0, 3),
(358, 'Q', '黔江区', '黔江区QIANJIANGQU', 62, 0, 3),
(359, 'C', '长寿区', '长寿区CHANGSHOUQU', 62, 0, 3),
(360, 'Q', '綦江县', '綦江县QIJIANGXIAN', 62, 0, 3),
(361, 'T', '潼南县', '潼南县TONGNANXIAN', 62, 0, 3),
(362, 'T', '铜梁县', '铜梁县TONGLIANGXIAN', 62, 0, 3),
(363, 'D', '大足县', '大足县DAZUXIAN', 62, 0, 3),
(364, 'R', '荣昌县', '荣昌县RONGCHANGXIAN', 62, 0, 3),
(365, 'B', '璧山县', '璧山县BISHANXIAN', 62, 0, 3),
(366, 'L', '梁平县', '梁平县LIANGPINGXIAN', 62, 0, 3),
(367, 'C', '城口县', '城口县CHENGKOUXIAN', 62, 0, 3),
(368, 'F', '丰都县', '丰都县FENGDUXIAN', 62, 0, 3),
(369, 'D', '垫江县', '垫江县DIANJIANGXIAN', 62, 0, 3),
(370, 'W', '武隆县', '武隆县WULONGXIAN', 62, 0, 3),
(371, 'Z', '忠县', '忠县ZHONGXIAN', 62, 0, 3),
(372, 'K', '开县', '开县KAIXIAN', 62, 0, 3),
(373, 'Y', '云阳县', '云阳县YUNYANGXIAN', 62, 0, 3),
(374, 'F', '奉节县', '奉节县FENGJIEXIAN', 62, 0, 3),
(375, 'W', '巫山县', '巫山县WUSHANXIAN', 62, 0, 3),
(376, 'W', '巫溪县', '巫溪县WUXIXIAN', 62, 0, 3),
(377, 'S', '石柱土家族自治县', '石柱土家族自治县SHIZHUTUJIAZUZIZHIXIAN', 62, 0, 3),
(378, 'X', '秀山土家族苗族自治县', '秀山土家族苗族自治县XIUSHANTUJIAZUMIAOZUZIZHIXIAN', 62, 0, 3),
(379, 'Y', '酉阳土家族苗族自治县', '酉阳土家族苗族自治县YOUYANGTUJIAZUMIAOZUZIZHIXIAN', 62, 0, 3),
(380, 'P', '彭水苗族土家族自治县', '彭水苗族土家族自治县PENGSHUIMIAOZUTUJIAZUZIZHIXIAN', 62, 0, 3),
(381, 'J', '江津市', '江津市JIANGJINSHI', 62, 0, 3),
(382, 'H', '合川市', '合川市HECHUANSHI', 62, 0, 3),
(383, 'Y', '永川市', '永川市YONGCHUANSHI', 62, 0, 3),
(384, 'N', '南川市', '南川市NANCHUANSHI', 62, 0, 3),
(385, 'C', '成都市', '成都市CHENGDUSHI', 23, 0, 2),
(386, 'Z', '自贡市', '自贡市ZIGONGSHI', 23, 0, 2),
(387, 'P', '攀枝花市', '攀枝花市PANZHIHUASHI', 23, 0, 2),
(388, 'L', '泸州市', '泸州市LUZHOUSHI', 23, 0, 2),
(389, 'D', '德阳市', '德阳市DEYANGSHI', 23, 0, 2),
(390, 'M', '绵阳市', '绵阳市MIANYANGSHI', 23, 0, 2),
(391, 'G', '广元市', '广元市GUANGYUANSHI', 23, 0, 2),
(392, 'S', '遂宁市', '遂宁市SUININGSHI', 23, 0, 2),
(393, 'N', '内江市', '内江市NEIJIANGSHI', 23, 0, 2),
(394, 'L', '乐山市', '乐山市LESHANSHI', 23, 0, 2),
(395, 'N', '南充市', '南充市NANCHONGSHI', 23, 0, 2),
(396, 'M', '眉山市', '眉山市MEISHANSHI', 23, 0, 2),
(397, 'Y', '宜宾市', '宜宾市YIBINSHI', 23, 0, 2),
(398, 'G', '广安市', '广安市GUANGANSHI', 23, 0, 2),
(399, 'D', '达州市', '达州市DAZHOUSHI', 23, 0, 2),
(400, 'Y', '雅安市', '雅安市YAANSHI', 23, 0, 2),
(401, 'B', '巴中市', '巴中市BAZHONGSHI', 23, 0, 2),
(402, 'Z', '资阳市', '资阳市ZIYANGSHI', 23, 0, 2),
(403, 'A', '阿坝藏族羌族自治州', '阿坝藏族羌族自治州ABAZANGZUQIANGZUZIZHIZHOU', 23, 0, 2),
(404, 'G', '甘孜藏族自治州', '甘孜藏族自治州GANZIZANGZUZIZHIZHOU', 23, 0, 2),
(405, 'L', '凉山彝族自治州', '凉山彝族自治州LIANGSHANYIZUZIZHIZHOU', 23, 0, 2),
(406, 'G', '贵阳市', '贵阳市GUIYANGSHI', 24, 0, 2),
(407, 'L', '六盘水市', '六盘水市LIUPANSHUISHI', 24, 0, 2),
(408, 'Z', '遵义市', '遵义市ZUNYISHI', 24, 0, 2),
(409, 'A', '安顺市', '安顺市ANSHUNSHI', 24, 0, 2),
(410, 'T', '铜仁地区', '铜仁地区TONGRENDIQU', 24, 0, 2),
(411, 'Q', '黔西南布依族苗族自治州', '黔西南布依族苗族自治州QIANXINANBUYIZUMIAOZUZIZHIZHOU', 24, 0, 2),
(412, 'B', '毕节地区', '毕节地区BIJIEDIQU', 24, 0, 2),
(413, 'Q', '黔东南苗族侗族自治州', '黔东南苗族侗族自治州QIANDONGNANMIAOZUDONGZUZIZHIZHOU', 24, 0, 2),
(414, 'Q', '黔南布依族苗族自治州', '黔南布依族苗族自治州QIANNANBUYIZUMIAOZUZIZHIZHOU', 24, 0, 2),
(415, 'K', '昆明市', '昆明市KUNMINGSHI', 25, 0, 2),
(416, 'Q', '曲靖市', '曲靖市QUJINGSHI', 25, 0, 2),
(417, 'Y', '玉溪市', '玉溪市YUXISHI', 25, 0, 2),
(418, 'B', '保山市', '保山市BAOSHANSHI', 25, 0, 2),
(419, 'Z', '昭通市', '昭通市ZHAOTONGSHI', 25, 0, 2),
(420, 'L', '丽江市', '丽江市LIJIANGSHI', 25, 0, 2),
(421, 'S', '思茅市', '思茅市SIMAOSHI', 25, 0, 2),
(422, 'L', '临沧市', '临沧市LINCANGSHI', 25, 0, 2),
(423, 'C', '楚雄彝族自治州', '楚雄彝族自治州CHUXIONGYIZUZIZHIZHOU', 25, 0, 2),
(424, 'H', '红河哈尼族彝族自治州', '红河哈尼族彝族自治州HONGHEHANIZUYIZUZIZHIZHOU', 25, 0, 2),
(425, 'W', '文山壮族苗族自治州', '文山壮族苗族自治州WENSHANZHUANGZUMIAOZUZIZHIZHOU', 25, 0, 2),
(426, 'X', '西双版纳傣族自治州', '西双版纳傣族自治州XISHUANGBANNADAIZUZIZHIZHOU', 25, 0, 2),
(427, 'D', '大理白族自治州', '大理白族自治州DALIBAIZUZIZHIZHOU', 25, 0, 2),
(428, 'D', '德宏傣族景颇族自治州', '德宏傣族景颇族自治州DEHONGDAIZUJINGPOZUZIZHIZHOU', 25, 0, 2),
(429, 'N', '怒江傈僳族自治州', '怒江傈僳族自治州NUJIANGLISUZUZIZHIZHOU', 25, 0, 2),
(430, 'D', '迪庆藏族自治州', '迪庆藏族自治州DIQINGZANGZUZIZHIZHOU', 25, 0, 2),
(431, 'L', '拉萨市', '拉萨市LASASHI', 26, 0, 2),
(432, 'C', '昌都地区', '昌都地区CHANGDUDIQU', 26, 0, 2),
(433, 'S', '山南地区', '山南地区SHANNANDIQU', 26, 0, 2),
(434, 'R', '日喀则地区', '日喀则地区RIKAZEDIQU', 26, 0, 2),
(435, 'N', '那曲地区', '那曲地区NAQUDIQU', 26, 0, 2),
(436, 'A', '阿里地区', '阿里地区ALIDIQU', 26, 0, 2),
(437, 'L', '林芝地区', '林芝地区LINZHIDIQU', 26, 0, 2),
(438, 'X', '西安市', '西安市XIANSHI', 27, 0, 2),
(439, 'T', '铜川市', '铜川市TONGCHUANSHI', 27, 0, 2),
(440, 'B', '宝鸡市', '宝鸡市BAOJISHI', 27, 0, 2),
(441, 'X', '咸阳市', '咸阳市XIANYANGSHI', 27, 0, 2),
(442, 'W', '渭南市', '渭南市WEINANSHI', 27, 0, 2),
(443, 'Y', '延安市', '延安市YANANSHI', 27, 0, 2),
(444, 'H', '汉中市', '汉中市HANZHONGSHI', 27, 0, 2),
(445, 'Y', '榆林市', '榆林市YULINSHI', 27, 0, 2),
(446, 'A', '安康市', '安康市ANKANGSHI', 27, 0, 2),
(447, 'S', '商洛市', '商洛市SHANGLUOSHI', 27, 0, 2),
(448, 'L', '兰州市', '兰州市LANZHOUSHI', 28, 0, 2),
(449, 'J', '嘉峪关市', '嘉峪关市JIAYUGUANSHI', 28, 0, 2),
(450, 'J', '金昌市', '金昌市JINCHANGSHI', 28, 0, 2),
(451, 'B', '白银市', '白银市BAIYINSHI', 28, 0, 2),
(452, 'T', '天水市', '天水市TIANSHUISHI', 28, 0, 2),
(453, 'W', '武威市', '武威市WUWEISHI', 28, 0, 2),
(454, 'Z', '张掖市', '张掖市ZHANGYESHI', 28, 0, 2),
(455, 'P', '平凉市', '平凉市PINGLIANGSHI', 28, 0, 2),
(456, 'J', '酒泉市', '酒泉市JIUQUANSHI', 28, 0, 2),
(457, 'Q', '庆阳市', '庆阳市QINGYANGSHI', 28, 0, 2),
(458, 'D', '定西市', '定西市DINGXISHI', 28, 0, 2),
(459, 'L', '陇南市', '陇南市LONGNANSHI', 28, 0, 2),
(460, 'L', '临夏回族自治州', '临夏回族自治州LINXIAHUIZUZIZHIZHOU', 28, 0, 2),
(461, 'G', '甘南藏族自治州', '甘南藏族自治州GANNANZANGZUZIZHIZHOU', 28, 0, 2),
(462, 'X', '西宁市', '西宁市XININGSHI', 29, 0, 2),
(463, 'H', '海东地区', '海东地区HAIDONGDIQU', 29, 0, 2),
(464, 'H', '海北藏族自治州', '海北藏族自治州HAIBEIZANGZUZIZHIZHOU', 29, 0, 2),
(465, 'H', '黄南藏族自治州', '黄南藏族自治州HUANGNANZANGZUZIZHIZHOU', 29, 0, 2),
(466, 'H', '海南藏族自治州', '海南藏族自治州HAINANZANGZUZIZHIZHOU', 29, 0, 2),
(467, 'G', '果洛藏族自治州', '果洛藏族自治州GUOLUOZANGZUZIZHIZHOU', 29, 0, 2),
(468, 'Y', '玉树藏族自治州', '玉树藏族自治州YUSHUZANGZUZIZHIZHOU', 29, 0, 2),
(469, 'H', '海西蒙古族藏族自治州', '海西蒙古族藏族自治州HAIXIMENGGUZUZANGZUZIZHIZHOU', 29, 0, 2),
(470, 'Y', '银川市', '银川市YINCHUANSHI', 30, 0, 2),
(471, 'S', '石嘴山市', '石嘴山市SHIZUISHANSHI', 30, 0, 2),
(472, 'W', '吴忠市', '吴忠市WUZHONGSHI', 30, 0, 2),
(473, 'G', '固原市', '固原市GUYUANSHI', 30, 0, 2),
(474, 'Z', '中卫市', '中卫市ZHONGWEISHI', 30, 0, 2),
(475, 'W', '乌鲁木齐市', '乌鲁木齐市WULUMUQISHI', 31, 0, 2),
(476, 'K', '克拉玛依市', '克拉玛依市KELAMAYISHI', 31, 0, 2),
(477, 'T', '吐鲁番地区', '吐鲁番地区TULUFANDIQU', 31, 0, 2),
(478, 'H', '哈密地区', '哈密地区HAMIDIQU', 31, 0, 2),
(479, 'C', '昌吉回族自治州', '昌吉回族自治州CHANGJIHUIZUZIZHIZHOU', 31, 0, 2),
(480, 'B', '博尔塔拉蒙古自治州', '博尔塔拉蒙古自治州BOERTALAMENGGUZIZHIZHOU', 31, 0, 2),
(481, 'B', '巴音郭楞蒙古自治州', '巴音郭楞蒙古自治州BAYINGUOLENGMENGGUZIZHIZHOU', 31, 0, 2),
(482, 'A', '阿克苏地区', '阿克苏地区AKESUDIQU', 31, 0, 2),
(483, 'K', '克孜勒苏柯尔克孜自治州', '克孜勒苏柯尔克孜自治州KEZILESUKEERKEZIZIZHIZHOU', 31, 0, 2),
(484, 'K', '喀什地区', '喀什地区KASHIDIQU', 31, 0, 2),
(485, 'H', '和田地区', '和田地区HETIANDIQU', 31, 0, 2),
(486, 'Y', '伊犁哈萨克自治州', '伊犁哈萨克自治州YILIHASAKEZIZHIZHOU', 31, 0, 2),
(487, 'T', '塔城地区', '塔城地区TACHENGDIQU', 31, 0, 2),
(488, 'A', '阿勒泰地区', '阿勒泰地区ALETAIDIQU', 31, 0, 2),
(489, 'S', '石河子市', '石河子市SHIHEZISHI', 31, 0, 2),
(490, 'A', '阿拉尔市', '阿拉尔市ALAERSHI', 31, 0, 2),
(491, 'T', '图木舒克市', '图木舒克市TUMUSHUKESHI', 31, 0, 2),
(492, 'W', '五家渠市', '五家渠市WUJIAQUSHI', 31, 0, 2),
(493, 'T', '台北市', '台北市TAIBEISHI', 45056, 0, 3),
(494, 'G', '高雄市', '高雄市GAOXIONGSHI', 45056, 0, 3),
(495, 'J', '基隆市', '基隆市JILONGSHI', 45056, 0, 3),
(496, 'T', '台中市', '台中市TAIZHONGSHI', 45056, 0, 3),
(497, 'T', '台南市', '台南市TAINANSHI', 45056, 0, 3),
(498, 'X', '新竹市', '新竹市XINZHUSHI', 45056, 0, 3),
(499, 'J', '嘉义市', '嘉义市JIAYISHI', 45056, 0, 3),
(500, 'T', '台北县', '台北县TAIBEIXIAN', 45056, 0, 3),
(501, 'Y', '宜兰县', '宜兰县YILANXIAN', 45056, 0, 3),
(502, 'T', '桃园县', '桃园县TAOYUANXIAN', 45056, 0, 3),
(503, 'X', '新竹县', '新竹县XINZHUXIAN', 45056, 0, 3),
(504, 'M', '苗栗县', '苗栗县MIAOLIXIAN', 45056, 0, 3),
(505, 'T', '台中县', '台中县TAIZHONGXIAN', 45056, 0, 3),
(506, 'Z', '彰化县', '彰化县ZHANGHUAXIAN', 45056, 0, 3),
(507, 'N', '南投县', '南投县NANTOUXIAN', 45056, 0, 3),
(508, 'Y', '云林县', '云林县YUNLINXIAN', 45056, 0, 3),
(509, 'J', '嘉义县', '嘉义县JIAYIXIAN', 45056, 0, 3),
(510, 'T', '台南县', '台南县TAINANXIAN', 45056, 0, 3),
(511, 'G', '高雄县', '高雄县GAOXIONGXIAN', 45056, 0, 3),
(512, 'P', '屏东县', '屏东县PINGDONGXIAN', 45056, 0, 3),
(513, 'P', '澎湖县', '澎湖县PENGHUXIAN', 45056, 0, 3),
(514, 'T', '台东县', '台东县TAIDONGXIAN', 45056, 0, 3),
(515, 'H', '花莲县', '花莲县HUALIANXIAN', 45056, 0, 3),
(516, 'Z', '中西区', '中西区ZHONGXIQU', 45057, 0, 3),
(517, 'D', '东区', '东区DONGQU', 45057, 0, 3),
(518, 'J', '九龙城区', '九龙城区JIULONGCHENGQU', 45057, 0, 3),
(519, 'G', '观塘区', '观塘区GUANTANGQU', 45057, 0, 3),
(520, 'N', '南区', '南区NANQU', 45057, 0, 3),
(521, 'S', '深水埗区', '深水埗区SHENSHUIBUQU', 45057, 0, 3),
(522, 'H', '黄大仙区', '黄大仙区HUANGDAXIANQU', 45057, 0, 3),
(523, 'W', '湾仔区', '湾仔区WANZAIQU', 45057, 0, 3),
(524, 'Y', '油尖旺区', '油尖旺区YOUJIANWANGQU', 45057, 0, 3),
(525, 'L', '离岛区', '离岛区LIDAOQU', 45057, 0, 3),
(526, 'K', '葵青区', '葵青区KUIQINGQU', 45057, 0, 3),
(527, 'B', '北区', '北区BEIQU', 45057, 0, 3),
(528, 'X', '西贡区', '西贡区XIGONGQU', 45057, 0, 3),
(529, 'S', '沙田区', '沙田区SHATIANQU', 45057, 0, 3),
(530, 'T', '屯门区', '屯门区TUNMENQU', 45057, 0, 3),
(531, 'D', '大埔区', '大埔区DABUQU', 45057, 0, 3),
(532, 'Q', '荃湾区', '荃湾区QUANWANQU', 45057, 0, 3),
(533, 'Y', '元朗区', '元朗区YUANLANGQU', 45057, 0, 3),
(534, 'A', '澳门特别行政区', '澳门特别行政区AOMENTEBIEXINGZHENGQU', 34, 0, 2),
(566, 'Q', '其他', '其他QITA', 36, 0, 3),
(1126, 'J', '井陉县', '井陉县JINGXINGXIAN', 73, 0, 3),
(1127, 'J', '井陉矿区', '井陉矿区JINGXINGKUANGQU', 73, 0, 3),
(1128, 'Y', '元氏县', '元氏县YUANSHIXIAN', 73, 0, 3),
(1129, 'P', '平山县', '平山县PINGSHANXIAN', 73, 0, 3),
(1130, 'X', '新乐市', '新乐市XINLESHI', 73, 0, 3),
(1131, 'X', '新华区', '新华区XINHUAQU', 73, 0, 3),
(1132, 'W', '无极县', '无极县WUJIXIAN', 73, 0, 3),
(1133, 'J', '晋州市', '晋州市JINZHOUSHI', 73, 0, 3),
(1134, 'L', '栾城县', '栾城县LUANCHENGXIAN', 73, 0, 3),
(1135, 'Q', '桥东区', '桥东区QIAODONGQU', 73, 0, 3),
(1136, 'Q', '桥西区', '桥西区QIAOXIQU', 73, 0, 3),
(1137, 'Z', '正定县', '正定县ZHENGDINGXIAN', 73, 0, 3),
(1138, 'S', '深泽县', '深泽县SHENZEXIAN', 73, 0, 3),
(1139, 'L', '灵寿县', '灵寿县LINGSHOUXIAN', 73, 0, 3),
(1140, 'G', '藁城市', '藁城市GAOCHENGSHI', 73, 0, 3),
(1141, 'X', '行唐县', '行唐县XINGTANGXIAN', 73, 0, 3),
(1142, 'Y', '裕华区', '裕华区YUHUAQU', 73, 0, 3),
(1143, 'Z', '赞皇县', '赞皇县ZANHUANGXIAN', 73, 0, 3),
(1144, 'Z', '赵县', '赵县ZHAOXIAN', 73, 0, 3),
(1145, 'X', '辛集市', '辛集市XINJISHI', 73, 0, 3),
(1146, 'C', '长安区', '长安区CHANGANQU', 73, 0, 3),
(1147, 'G', '高邑县', '高邑县GAOYIXIAN', 73, 0, 3),
(1148, 'L', '鹿泉市', '鹿泉市LUQUANSHI', 73, 0, 3),
(1149, 'F', '丰南区', '丰南区FENGNANQU', 74, 0, 3),
(1150, 'F', '丰润区', '丰润区FENGRUNQU', 74, 0, 3),
(1151, 'L', '乐亭县', '乐亭县LETINGXIAN', 74, 0, 3),
(1152, 'G', '古冶区', '古冶区GUYEQU', 74, 0, 3),
(1153, 'T', '唐海县', '唐海县TANGHAIXIAN', 74, 0, 3),
(1154, 'K', '开平区', '开平区KAIPINGQU', 74, 0, 3),
(1155, 'L', '滦南县', '滦南县LUANNANXIAN', 74, 0, 3),
(1156, 'L', '滦县', '滦县LUANXIAN', 74, 0, 3),
(1157, 'Y', '玉田县', '玉田县YUTIANXIAN', 74, 0, 3),
(1158, 'L', '路北区', '路北区LUBEIQU', 74, 0, 3),
(1159, 'L', '路南区', '路南区LUNANQU', 74, 0, 3),
(1160, 'Q', '迁安市', '迁安市QIANANSHI', 74, 0, 3),
(1161, 'Q', '迁西县', '迁西县QIANXIXIAN', 74, 0, 3),
(1162, 'Z', '遵化市', '遵化市ZUNHUASHI', 74, 0, 3),
(1163, 'B', '北戴河区', '北戴河区BEIDAIHEQU', 75, 0, 3),
(1164, 'L', '卢龙县', '卢龙县LULONGXIAN', 75, 0, 3),
(1165, 'S', '山海关区', '山海关区SHANHAIGUANQU', 75, 0, 3),
(1166, 'F', '抚宁县', '抚宁县FUNINGXIAN', 75, 0, 3),
(1167, 'C', '昌黎县', '昌黎县CHANGLIXIAN', 75, 0, 3),
(1168, 'H', '海港区', '海港区HAIGANGQU', 75, 0, 3),
(1169, 'Q', '青龙满族自治县', '青龙满族自治县QINGLONGMANZUZIZHIXIAN', 75, 0, 3),
(1170, 'C', '丛台区', '丛台区CONGTAIQU', 76, 0, 3),
(1171, 'L', '临漳县', '临漳县LINZHANGXIAN', 76, 0, 3),
(1172, 'F', '复兴区', '复兴区FUXINGQU', 76, 0, 3),
(1173, 'D', '大名县', '大名县DAMINGXIAN', 76, 0, 3),
(1174, 'F', '峰峰矿区', '峰峰矿区FENGFENGKUANGQU', 76, 0, 3),
(1175, 'G', '广平县', '广平县GUANGPINGXIAN', 76, 0, 3),
(1176, 'C', '成安县', '成安县CHENGANXIAN', 76, 0, 3),
(1177, 'Q', '曲周县', '曲周县QUZHOUXIAN', 76, 0, 3),
(1178, 'W', '武安市', '武安市WUANSHI', 76, 0, 3),
(1179, 'Y', '永年县', '永年县YONGNIANXIAN', 76, 0, 3),
(1180, 'S', '涉县', '涉县SHEXIAN', 76, 0, 3),
(1181, 'C', '磁县', '磁县CIXIAN', 76, 0, 3),
(1182, 'F', '肥乡县', '肥乡县FEIXIANGXIAN', 76, 0, 3),
(1183, 'H', '邯山区', '邯山区HANSHANQU', 76, 0, 3),
(1184, 'H', '邯郸县', '邯郸县HANDANXIAN', 76, 0, 3),
(1185, 'Q', '邱县', '邱县QIUXIAN', 76, 0, 3),
(1186, 'G', '馆陶县', '馆陶县GUANTAOXIAN', 76, 0, 3),
(1187, 'W', '魏县', '魏县WEIXIAN', 76, 0, 3),
(1188, 'J', '鸡泽县', '鸡泽县JIZEXIAN', 76, 0, 3),
(1189, 'L', '临城县', '临城县LINCHENGXIAN', 77, 0, 3),
(1190, 'L', '临西县', '临西县LINXIXIAN', 77, 0, 3),
(1191, 'R', '任县', '任县RENXIAN', 77, 0, 3),
(1192, 'N', '内丘县', '内丘县NEIQIUXIAN', 77, 0, 3),
(1193, 'N', '南和县', '南和县NANHEXIAN', 77, 0, 3),
(1194, 'N', '南宫市', '南宫市NANGONGSHI', 77, 0, 3),
(1195, 'W', '威县', '威县WEIXIAN', 77, 0, 3),
(1196, 'N', '宁晋县', '宁晋县NINGJINXIAN', 77, 0, 3),
(1197, 'J', '巨鹿县', '巨鹿县JULUXIAN', 77, 0, 3),
(1198, 'P', '平乡县', '平乡县PINGXIANGXIAN', 77, 0, 3),
(1199, 'G', '广宗县', '广宗县GUANGZONGXIAN', 77, 0, 3),
(1200, 'X', '新河县', '新河县XINHEXIAN', 77, 0, 3),
(1201, 'B', '柏乡县', '柏乡县BAIXIANGXIAN', 77, 0, 3),
(1202, 'Q', '桥东区', '桥东区QIAODONGQU', 77, 0, 3),
(1203, 'Q', '桥西区', '桥西区QIAOXIQU', 77, 0, 3),
(1204, 'S', '沙河市', '沙河市SHAHESHI', 77, 0, 3),
(1205, 'Q', '清河县', '清河县QINGHEXIAN', 77, 0, 3),
(1206, 'X', '邢台县', '邢台县XINGTAIXIAN', 77, 0, 3),
(1207, 'L', '隆尧县', '隆尧县LONGYAOXIAN', 77, 0, 3),
(1208, 'B', '北市区', '北市区BEISHIQU', 78, 0, 3),
(1209, 'N', '南市区', '南市区NANSHIQU', 78, 0, 3),
(1210, 'B', '博野县', '博野县BOYEXIAN', 78, 0, 3),
(1211, 'T', '唐县', '唐县TANGXIAN', 78, 0, 3),
(1212, 'A', '安国市', '安国市ANGUOSHI', 78, 0, 3),
(1213, 'A', '安新县', '安新县ANXINXIAN', 78, 0, 3),
(1214, 'D', '定兴县', '定兴县DINGXINGXIAN', 78, 0, 3),
(1215, 'D', '定州市', '定州市DINGZHOUSHI', 78, 0, 3),
(1216, 'R', '容城县', '容城县RONGCHENGXIAN', 78, 0, 3),
(1217, 'X', '徐水县', '徐水县XUSHUIXIAN', 78, 0, 3),
(1218, 'X', '新市区', '新市区XINSHIQU', 78, 0, 3),
(1219, 'Y', '易县', '易县YIXIAN', 78, 0, 3),
(1220, 'Q', '曲阳县', '曲阳县QUYANGXIAN', 78, 0, 3),
(1221, 'W', '望都县', '望都县WANGDUXIAN', 78, 0, 3),
(1222, 'L', '涞水县', '涞水县LAISHUIXIAN', 78, 0, 3),
(1223, 'L', '涞源县', '涞源县LAIYUANXIAN', 78, 0, 3),
(1224, 'Z', '涿州市', '涿州市ZHUOZHOUSHI', 78, 0, 3),
(1225, 'Q', '清苑县', '清苑县QINGYUANXIAN', 78, 0, 3),
(1226, 'M', '满城县', '满城县MANCHENGXIAN', 78, 0, 3),
(1227, 'L', '蠡县', '蠡县LIXIAN', 78, 0, 3),
(1228, 'F', '阜平县', '阜平县FUPINGXIAN', 78, 0, 3),
(1229, 'X', '雄县', '雄县XIONGXIAN', 78, 0, 3),
(1230, 'S', '顺平县', '顺平县SHUNPINGXIAN', 78, 0, 3),
(1231, 'G', '高碑店市', '高碑店市GAOBEIDIANSHI', 78, 0, 3),
(1232, 'G', '高阳县', '高阳县GAOYANGXIAN', 78, 0, 3),
(1233, 'W', '万全县', '万全县WANQUANXIAN', 79, 0, 3),
(1234, 'X', '下花园区', '下花园区XIAHUAYUANQU', 79, 0, 3),
(1235, 'X', '宣化区', '宣化区XUANHUAQU', 79, 0, 3),
(1236, 'X', '宣化县', '宣化县XUANHUAXIAN', 79, 0, 3),
(1237, 'S', '尚义县', '尚义县SHANGYIXIAN', 79, 0, 3),
(1238, 'C', '崇礼县', '崇礼县CHONGLIXIAN', 79, 0, 3),
(1239, 'K', '康保县', '康保县KANGBAOXIAN', 79, 0, 3),
(1240, 'Z', '张北县', '张北县ZHANGBEIXIAN', 79, 0, 3),
(1241, 'H', '怀安县', '怀安县HUAIANXIAN', 79, 0, 3),
(1242, 'H', '怀来县', '怀来县HUAILAIXIAN', 79, 0, 3),
(1243, 'Q', '桥东区', '桥东区QIAODONGQU', 79, 0, 3),
(1244, 'Q', '桥西区', '桥西区QIAOXIQU', 79, 0, 3),
(1245, 'G', '沽源县', '沽源县GUYUANXIAN', 79, 0, 3),
(1246, 'Z', '涿鹿县', '涿鹿县ZHUOLUXIAN', 79, 0, 3),
(1247, 'Y', '蔚县', '蔚县YUXIAN', 79, 0, 3),
(1248, 'C', '赤城县', '赤城县CHICHENGXIAN', 79, 0, 3),
(1249, 'Y', '阳原县', '阳原县YANGYUANXIAN', 79, 0, 3),
(1250, 'F', '丰宁满族自治县', '丰宁满族自治县FENGNINGMANZUZIZHIXIAN', 80, 0, 3),
(1251, 'X', '兴隆县', '兴隆县XINGLONGXIAN', 80, 0, 3),
(1252, 'S', '双桥区', '双桥区SHUANGQIAOQU', 80, 0, 3),
(1253, 'S', '双滦区', '双滦区SHUANGLUANQU', 80, 0, 3),
(1254, 'W', '围场满族蒙古族自治县', '围场满族蒙古族自治县WEICHANGMANZUMENGGUZUZIZHIXIAN', 80, 0, 3),
(1255, 'K', '宽城满族自治县', '宽城满族自治县KUANCHENGMANZUZIZHIXIAN', 80, 0, 3),
(1256, 'P', '平泉县', '平泉县PINGQUANXIAN', 80, 0, 3),
(1257, 'C', '承德县', '承德县CHENGDEXIAN', 80, 0, 3),
(1258, 'L', '滦平县', '滦平县LUANPINGXIAN', 80, 0, 3),
(1259, 'L', '隆化县', '隆化县LONGHUAXIAN', 80, 0, 3),
(1260, 'Y', '鹰手营子矿区', '鹰手营子矿区YINGSHOUYINGZIKUANGQU', 80, 0, 3),
(1261, 'J', '冀州市', '冀州市JIZHOUSHI', 81, 0, 3),
(1262, 'A', '安平县', '安平县ANPINGXIAN', 81, 0, 3),
(1263, 'G', '故城县', '故城县GUCHENGXIAN', 81, 0, 3),
(1264, 'J', '景县', '景县JINGXIAN', 81, 0, 3),
(1265, 'Z', '枣强县', '枣强县ZAOQIANGXIAN', 81, 0, 3),
(1266, 'T', '桃城区', '桃城区TAOCHENGQU', 81, 0, 3),
(1267, 'W', '武强县', '武强县WUQIANGXIAN', 81, 0, 3),
(1268, 'W', '武邑县', '武邑县WUYIXIAN', 81, 0, 3),
(1269, 'S', '深州市', '深州市SHENZHOUSHI', 81, 0, 3),
(1270, 'F', '阜城县', '阜城县FUCHENGXIAN', 81, 0, 3),
(1271, 'R', '饶阳县', '饶阳县RAOYANGXIAN', 81, 0, 3),
(1272, 'S', '三河市', '三河市SANHESHI', 82, 0, 3),
(1273, 'G', '固安县', '固安县GUANXIAN', 82, 0, 3),
(1274, 'D', '大厂回族自治县', '大厂回族自治县DACHANGHUIZUZIZHIXIAN', 82, 0, 3),
(1275, 'D', '大城县', '大城县DAICHENGXIAN', 82, 0, 3),
(1276, 'A', '安次区', '安次区ANCIQU', 82, 0, 3),
(1277, 'G', '广阳区', '广阳区GUANGYANGQU', 82, 0, 3),
(1278, 'W', '文安县', '文安县WENANXIAN', 82, 0, 3),
(1279, 'Y', '永清县', '永清县YONGQINGXIAN', 82, 0, 3),
(1280, 'B', '霸州市', '霸州市BAZHOUSHI', 82, 0, 3),
(1281, 'X', '香河县', '香河县XIANGHEXIAN', 82, 0, 3),
(1282, 'D', '东光县', '东光县DONGGUANGXIAN', 83, 0, 3),
(1283, 'R', '任丘市', '任丘市RENQIUSHI', 83, 0, 3),
(1284, 'N', '南皮县', '南皮县NANPIXIAN', 83, 0, 3),
(1285, 'W', '吴桥县', '吴桥县WUQIAOXIAN', 83, 0, 3),
(1286, 'M', '孟村回族自治县', '孟村回族自治县MENGCUNHUIZUZIZHIXIAN', 83, 0, 3),
(1287, 'X', '新华区', '新华区XINHUAQU', 83, 0, 3),
(1288, 'C', '沧县', '沧县CANGXIAN', 83, 0, 3),
(1289, 'H', '河间市', '河间市HEJIANSHI', 83, 0, 3),
(1290, 'B', '泊头市', '泊头市BOTOUSHI', 83, 0, 3),
(1291, 'H', '海兴县', '海兴县HAIXINGXIAN', 83, 0, 3),
(1292, 'X', '献县', '献县XIANXIAN', 83, 0, 3),
(1293, 'Y', '盐山县', '盐山县YANSHANXIAN', 83, 0, 3),
(1294, 'S', '肃宁县', '肃宁县SUNINGXIAN', 83, 0, 3),
(1295, 'Y', '运河区', '运河区YUNHEQU', 83, 0, 3),
(1296, 'Q', '青县', '青县QINGXIAN', 83, 0, 3),
(1297, 'H', '黄骅市', '黄骅市HUANGHUASHI', 83, 0, 3),
(1298, 'W', '万柏林区', '万柏林区WANBOLINQU', 84, 0, 3),
(1299, 'G', '古交市', '古交市GUJIAOSHI', 84, 0, 3),
(1300, 'L', '娄烦县', '娄烦县LOUFANXIAN', 84, 0, 3),
(1301, 'X', '小店区', '小店区XIAODIANQU', 84, 0, 3),
(1302, 'J', '尖草坪区', '尖草坪区JIANCAOPINGQU', 84, 0, 3),
(1303, 'J', '晋源区', '晋源区JINYUANQU', 84, 0, 3),
(1304, 'X', '杏花岭区', '杏花岭区XINGHUALINGQU', 84, 0, 3),
(1305, 'Q', '清徐县', '清徐县QINGXUXIAN', 84, 0, 3),
(1306, 'Y', '迎泽区', '迎泽区YINGZEQU', 84, 0, 3),
(1307, 'Y', '阳曲县', '阳曲县YANGQUXIAN', 84, 0, 3),
(1308, 'N', '南郊区', '南郊区NANJIAOQU', 85, 0, 3),
(1309, 'C', '城区', '城区CHENGQU', 85, 0, 3),
(1310, 'D', '大同县', '大同县DATONGXIAN', 85, 0, 3),
(1311, 'T', '天镇县', '天镇县TIANZHENXIAN', 85, 0, 3),
(1312, 'Z', '左云县', '左云县ZUOYUNXIAN', 85, 0, 3),
(1313, 'G', '广灵县', '广灵县GUANGLINGXIAN', 85, 0, 3),
(1314, 'X', '新荣区', '新荣区XINRONGQU', 85, 0, 3),
(1315, 'H', '浑源县', '浑源县HUNYUANXIAN', 85, 0, 3),
(1316, 'L', '灵丘县', '灵丘县LINGQIUXIAN', 85, 0, 3),
(1317, 'K', '矿区', '矿区KUANGQU', 85, 0, 3),
(1318, 'Y', '阳高县', '阳高县YANGGAOXIAN', 85, 0, 3),
(1319, 'C', '城区', '城区CHENGQU', 86, 0, 3),
(1320, 'P', '平定县', '平定县PINGDINGXIAN', 86, 0, 3),
(1321, 'Y', '盂县', '盂县YUXIAN', 86, 0, 3),
(1322, 'K', '矿区', '矿区KUANGQU', 86, 0, 3),
(1323, 'J', '郊区', '郊区JIAOQU', 86, 0, 3),
(1324, 'C', '城区', '城区CHENGQU', 87, 0, 3),
(1325, 'H', '壶关县', '壶关县HUGUANXIAN', 87, 0, 3),
(1326, 'T', '屯留县', '屯留县TUNLIUXIAN', 87, 0, 3),
(1327, 'P', '平顺县', '平顺县PINGSHUNXIAN', 87, 0, 3),
(1328, 'W', '武乡县', '武乡县WUXIANGXIAN', 87, 0, 3),
(1329, 'Q', '沁县', '沁县QINXIAN', 87, 0, 3),
(1330, 'Q', '沁源县', '沁源县QINYUANXIAN', 87, 0, 3),
(1331, 'L', '潞城市', '潞城市LUCHENGSHI', 87, 0, 3),
(1332, 'X', '襄垣县', '襄垣县XIANGYUANXIAN', 87, 0, 3),
(1333, 'J', '郊区', '郊区JIAOQU', 87, 0, 3),
(1334, 'C', '长子县', '长子县CHANGZIXIAN', 87, 0, 3),
(1335, 'C', '长治县', '长治县CHANGZHIXIAN', 87, 0, 3),
(1336, 'L', '黎城县', '黎城县LICHENGXIAN', 87, 0, 3),
(1337, 'C', '城区', '城区CHENGQU', 88, 0, 3),
(1338, 'Q', '沁水县', '沁水县QINSHUIXIAN', 88, 0, 3),
(1339, 'Z', '泽州县', '泽州县ZEZHOUXIAN', 88, 0, 3),
(1340, 'Y', '阳城县', '阳城县YANGCHENGXIAN', 88, 0, 3),
(1341, 'L', '陵川县', '陵川县LINGCHUANXIAN', 88, 0, 3),
(1342, 'G', '高平市', '高平市GAOPINGSHI', 88, 0, 3),
(1343, 'Y', '右玉县', '右玉县YOUYUXIAN', 89, 0, 3),
(1344, 'S', '山阴县', '山阴县SHANYINXIAN', 89, 0, 3),
(1345, 'P', '平鲁区', '平鲁区PINGLUQU', 89, 0, 3),
(1346, 'Y', '应县', '应县YINGXIAN', 89, 0, 3),
(1347, 'H', '怀仁县', '怀仁县HUAIRENXIAN', 89, 0, 3),
(1348, 'S', '朔城区', '朔城区SHUOCHENGQU', 89, 0, 3),
(1349, 'J', '介休市', '介休市JIEXIUSHI', 90, 0, 3),
(1350, 'H', '和顺县', '和顺县HESHUNXIAN', 90, 0, 3),
(1351, 'T', '太谷县', '太谷县TAIGUXIAN', 90, 0, 3),
(1352, 'S', '寿阳县', '寿阳县SHOUYANGXIAN', 90, 0, 3),
(1353, 'Z', '左权县', '左权县ZUOQUANXIAN', 90, 0, 3),
(1354, 'P', '平遥县', '平遥县PINGYAOXIAN', 90, 0, 3),
(1355, 'X', '昔阳县', '昔阳县XIYANGXIAN', 90, 0, 3),
(1356, 'Y', '榆次区', '榆次区YUCIQU', 90, 0, 3),
(1357, 'Y', '榆社县', '榆社县YUSHEXIAN', 90, 0, 3),
(1358, 'L', '灵石县', '灵石县LINGSHIXIAN', 90, 0, 3),
(1359, 'Q', '祁县', '祁县QIXIAN', 90, 0, 3),
(1360, 'W', '万荣县', '万荣县WANRONGXIAN', 91, 0, 3),
(1361, 'L', '临猗县', '临猗县LINYIXIAN', 91, 0, 3),
(1362, 'Y', '垣曲县', '垣曲县YUANQUXIAN', 91, 0, 3),
(1363, 'X', '夏县', '夏县XIAXIAN', 91, 0, 3),
(1364, 'P', '平陆县', '平陆县PINGLUXIAN', 91, 0, 3),
(1365, 'X', '新绛县', '新绛县XINJIANGXIAN', 91, 0, 3),
(1366, 'Y', '永济市', '永济市YONGJISHI', 91, 0, 3),
(1367, 'H', '河津市', '河津市HEJINSHI', 91, 0, 3),
(1368, 'Y', '盐湖区', '盐湖区YANHUQU', 91, 0, 3),
(1369, 'J', '稷山县', '稷山县JISHANXIAN', 91, 0, 3),
(1370, 'J', '绛县', '绛县JIANGXIAN', 91, 0, 3),
(1371, 'R', '芮城县', '芮城县RUICHENGXIAN', 91, 0, 3),
(1372, 'W', '闻喜县', '闻喜县WENXIXIAN', 91, 0, 3),
(1373, 'W', '五台县', '五台县WUTAIXIAN', 92, 0, 3),
(1374, 'W', '五寨县', '五寨县WUZHAIXIAN', 92, 0, 3),
(1375, 'D', '代县', '代县DAIXIAN', 92, 0, 3),
(1376, 'B', '保德县', '保德县BAODEXIAN', 92, 0, 3),
(1377, 'P', '偏关县', '偏关县PIANGUANXIAN', 92, 0, 3),
(1378, 'Y', '原平市', '原平市YUANPINGSHI', 92, 0, 3),
(1379, 'N', '宁武县', '宁武县NINGWUXIAN', 92, 0, 3),
(1380, 'D', '定襄县', '定襄县DINGXIANGXIAN', 92, 0, 3),
(1381, 'K', '岢岚县', '岢岚县KELANXIAN', 92, 0, 3),
(1382, 'X', '忻府区', '忻府区XINFUQU', 92, 0, 3),
(1383, 'H', '河曲县', '河曲县HEQUXIAN', 92, 0, 3),
(1384, 'S', '神池县', '神池县SHENCHIXIAN', 92, 0, 3),
(1385, 'F', '繁峙县', '繁峙县FANSHIXIAN', 92, 0, 3),
(1386, 'J', '静乐县', '静乐县JINGLEXIAN', 92, 0, 3),
(1387, 'X', '乡宁县', '乡宁县XIANGNINGXIAN', 93, 0, 3),
(1388, 'H', '侯马市', '侯马市HOUMASHI', 93, 0, 3),
(1389, 'G', '古县', '古县GUXIAN', 93, 0, 3),
(1390, 'J', '吉县', '吉县JIXIAN', 93, 0, 3),
(1391, 'D', '大宁县', '大宁县DANINGXIAN', 93, 0, 3),
(1392, 'A', '安泽县', '安泽县ANZEXIAN', 93, 0, 3),
(1393, 'Y', '尧都区', '尧都区YAODUQU', 93, 0, 3),
(1394, 'Q', '曲沃县', '曲沃县QUWOXIAN', 93, 0, 3),
(1395, 'Y', '永和县', '永和县YONGHEXIAN', 93, 0, 3),
(1396, 'F', '汾西县', '汾西县FENXIXIAN', 93, 0, 3),
(1397, 'H', '洪洞县', '洪洞县HONGTONGXIAN', 93, 0, 3),
(1398, 'F', '浮山县', '浮山县FUSHANXIAN', 93, 0, 3),
(1399, 'Y', '翼城县', '翼城县YICHENGXIAN', 93, 0, 3),
(1400, 'P', '蒲县', '蒲县PUXIAN', 93, 0, 3),
(1401, 'X', '襄汾县', '襄汾县XIANGFENXIAN', 93, 0, 3),
(1402, 'X', '隰县', '隰县XIXIAN', 93, 0, 3),
(1403, 'H', '霍州市', '霍州市HUOZHOUSHI', 93, 0, 3),
(1404, 'Z', '中阳县', '中阳县ZHONGYANGXIAN', 94, 0, 3),
(1405, 'L', '临县', '临县LINXIAN', 94, 0, 3),
(1406, 'J', '交口县', '交口县JIAOKOUXIAN', 94, 0, 3),
(1407, 'J', '交城县', '交城县JIAOCHENGXIAN', 94, 0, 3),
(1408, 'X', '兴县', '兴县XINGXIAN', 94, 0, 3),
(1409, 'X', '孝义市', '孝义市XIAOYISHI', 94, 0, 3),
(1410, 'L', '岚县', '岚县LANXIAN', 94, 0, 3),
(1411, 'W', '文水县', '文水县WENSHUIXIAN', 94, 0, 3),
(1412, 'F', '方山县', '方山县FANGSHANXIAN', 94, 0, 3),
(1413, 'L', '柳林县', '柳林县LIULINXIAN', 94, 0, 3),
(1414, 'F', '汾阳市', '汾阳市FENYANGSHI', 94, 0, 3),
(1415, 'S', '石楼县', '石楼县SHILOUXIAN', 94, 0, 3),
(1416, 'L', '离石区', '离石区LISHIQU', 94, 0, 3),
(1417, 'H', '和林格尔县', '和林格尔县HELINGEERXIAN', 95, 0, 3),
(1418, 'H', '回民区', '回民区HUIMINQU', 95, 0, 3),
(1419, 'T', '土默特左旗', '土默特左旗TUMOTEZUOQI', 95, 0, 3),
(1420, 'T', '托克托县', '托克托县TUOKETUOXIAN', 95, 0, 3),
(1421, 'X', '新城区', '新城区XINCHENGQU', 95, 0, 3),
(1422, 'W', '武川县', '武川县WUCHUANXIAN', 95, 0, 3),
(1423, 'Q', '清水河县', '清水河县QINGSHUIHEXIAN', 95, 0, 3),
(1424, 'Y', '玉泉区', '玉泉区YUQUANQU', 95, 0, 3),
(1425, 'S', '赛罕区', '赛罕区SAIHANQU', 95, 0, 3),
(1426, 'D', '东河区', '东河区DONGHEQU', 96, 0, 3),
(1427, 'J', '九原区', '九原区JIUYUANQU', 96, 0, 3),
(1428, 'G', '固阳县', '固阳县GUYANGXIAN', 96, 0, 3),
(1429, 'T', '土默特右旗', '土默特右旗TUMOTEYOUQI', 96, 0, 3),
(1430, 'K', '昆都仑区', '昆都仑区KUNDULUNQU', 96, 0, 3),
(1431, 'B', '白云矿区', '白云矿区BAIYUNKUANGQU', 96, 0, 3),
(1432, 'S', '石拐区', '石拐区SHIGUAIQU', 96, 0, 3),
(1433, 'D', '达尔罕茂明安联合旗', '达尔罕茂明安联合旗DAERHANMAOMINGANLIANHEQI', 96, 0, 3),
(1434, 'Q', '青山区', '青山区QINGSHANQU', 96, 0, 3),
(1435, 'W', '乌达区', '乌达区WUDAQU', 97, 0, 3),
(1436, 'H', '海勃湾区', '海勃湾区HAIBOWANQU', 97, 0, 3),
(1437, 'H', '海南区', '海南区HAINANQU', 97, 0, 3),
(1438, 'Y', '元宝山区', '元宝山区YUANBAOSHANQU', 98, 0, 3),
(1439, 'K', '克什克腾旗', '克什克腾旗KESHIKETENGQI', 98, 0, 3),
(1440, 'K', '喀喇沁旗', '喀喇沁旗KALAQINQI', 98, 0, 3),
(1441, 'N', '宁城县', '宁城县NINGCHENGXIAN', 98, 0, 3),
(1442, 'B', '巴林右旗', '巴林右旗BALINYOUQI', 98, 0, 3),
(1443, 'B', '巴林左旗', '巴林左旗BALINZUOQI', 98, 0, 3),
(1444, 'A', '敖汉旗', '敖汉旗AOHANQI', 98, 0, 3),
(1445, 'S', '松山区', '松山区SONGSHANQU', 98, 0, 3),
(1446, 'L', '林西县', '林西县LINXIXIAN', 98, 0, 3),
(1447, 'H', '红山区', '红山区HONGSHANQU', 98, 0, 3),
(1448, 'W', '翁牛特旗', '翁牛特旗WENGNIUTEQI', 98, 0, 3),
(1449, 'A', '阿鲁科尔沁旗', '阿鲁科尔沁旗ALUKEERQINQI', 98, 0, 3),
(1450, 'N', '奈曼旗', '奈曼旗NAIMANQI', 99, 0, 3),
(1451, 'K', '库伦旗', '库伦旗KULUNQI', 99, 0, 3),
(1452, 'K', '开鲁县', '开鲁县KAILUXIAN', 99, 0, 3),
(1453, 'Z', '扎鲁特旗', '扎鲁特旗ZALUTEQI', 99, 0, 3),
(1454, 'K', '科尔沁区', '科尔沁区KEERQINQU', 99, 0, 3),
(1455, 'K', '科尔沁左翼中旗', '科尔沁左翼中旗KEERQINZUOYIZHONGQI', 99, 0, 3),
(1456, 'K', '科尔沁左翼后旗', '科尔沁左翼后旗KEERQINZUOYIHOUQI', 99, 0, 3),
(1457, 'H', '霍林郭勒市', '霍林郭勒市HUOLINGUOLESHI', 99, 0, 3),
(1458, 'D', '东胜区', '东胜区DONGSHENGQU', 100, 0, 3),
(1459, 'W', '乌审旗', '乌审旗WUSHENQI', 100, 0, 3),
(1460, 'Y', '伊金霍洛旗', '伊金霍洛旗YIJINHUOLUOQI', 100, 0, 3),
(1461, 'Z', '准格尔旗', '准格尔旗ZHUNGEERQI', 100, 0, 3),
(1462, 'H', '杭锦旗', '杭锦旗HANGJINQI', 100, 0, 3),
(1463, 'D', '达拉特旗', '达拉特旗DALATEQI', 100, 0, 3),
(1464, 'E', '鄂东胜区', '鄂东胜区EDONGSHENGQU', 100, 0, 3),
(1465, 'E', '鄂托克前旗', '鄂托克前旗ETUOKEQIANQI', 100, 0, 3),
(1466, 'E', '鄂托克旗', '鄂托克旗ETUOKEQI', 100, 0, 3),
(1467, 'Z', '扎兰屯市', '扎兰屯市ZHALANTUNSHI', 101, 0, 3),
(1468, 'X', '新巴尔虎右旗', '新巴尔虎右旗XINBAERHUYOUQI', 101, 0, 3),
(1469, 'X', '新巴尔虎左旗', '新巴尔虎左旗XINBAERHUZUOQI', 101, 0, 3),
(1470, 'G', '根河市', '根河市GENHESHI', 101, 0, 3),
(1471, 'H', '海拉尔区', '海拉尔区HAILAERQU', 101, 0, 3),
(1472, 'M', '满洲里市', '满洲里市MANZHOULISHI', 101, 0, 3),
(1473, 'Y', '牙克石市', '牙克石市YAKESHISHI', 101, 0, 3),
(1474, 'M', '莫力达瓦达斡尔族自治旗', '莫力达瓦达斡尔族自治旗MOLIDAWADAWOERZUZIZHIQI', 101, 0, 3),
(1475, 'E', '鄂伦春自治旗', '鄂伦春自治旗ELUNCHUNZIZHIQI', 101, 0, 3),
(1476, 'E', '鄂温克族自治旗', '鄂温克族自治旗EWENKEZUZIZHIQI', 101, 0, 3),
(1477, 'A', '阿荣旗', '阿荣旗ARONGQI', 101, 0, 3),
(1478, 'C', '陈巴尔虎旗', '陈巴尔虎旗CHENBAERHUQI', 101, 0, 3),
(1479, 'E', '额尔古纳市', '额尔古纳市EERGUNASHI', 101, 0, 3),
(1480, 'L', '临河区', '临河区LINHEQU', 102, 0, 3),
(1481, 'W', '乌拉特中旗', '乌拉特中旗WULATEZHONGQI', 102, 0, 3),
(1482, 'W', '乌拉特前旗', '乌拉特前旗WULATEQIANQI', 102, 0, 3),
(1483, 'W', '乌拉特后旗', '乌拉特后旗WULATEHOUQI', 102, 0, 3),
(1484, 'W', '五原县', '五原县WUYUANXIAN', 102, 0, 3),
(1485, 'H', '杭锦后旗', '杭锦后旗HANGJINHOUQI', 102, 0, 3),
(1486, 'D', '磴口县', '磴口县DENGKOUXIAN', 102, 0, 3),
(1487, 'F', '丰镇市', '丰镇市FENGZHENSHI', 103, 0, 3),
(1488, 'X', '兴和县', '兴和县XINGHEXIAN', 103, 0, 3),
(1489, 'L', '凉城县', '凉城县LIANGCHENGXIAN', 103, 0, 3),
(1490, 'H', '化德县', '化德县HUADEXIAN', 103, 0, 3),
(1491, 'Z', '卓资县', '卓资县ZHUOZIXIAN', 103, 0, 3),
(1492, 'S', '商都县', '商都县SHANGDUXIAN', 103, 0, 3),
(1493, 'S', '四子王旗', '四子王旗SIZIWANGQI', 103, 0, 3),
(1494, 'C', '察哈尔右翼中旗', '察哈尔右翼中旗CHAHAERYOUYIZHONGQI', 103, 0, 3),
(1495, 'C', '察哈尔右翼前旗', '察哈尔右翼前旗CHAHAERYOUYIQIANQI', 103, 0, 3),
(1496, 'C', '察哈尔右翼后旗', '察哈尔右翼后旗CHAHAERYOUYIHOUQI', 103, 0, 3),
(1497, 'J', '集宁区', '集宁区JININGQU', 103, 0, 3),
(1498, 'W', '乌兰浩特市', '乌兰浩特市WULANHAOTESHI', 104, 0, 3),
(1499, 'Z', '扎赉特旗', '扎赉特旗ZHALAITEQI', 104, 0, 3),
(1500, 'K', '科尔沁右翼中旗', '科尔沁右翼中旗KEERQINYOUYIZHONGQI', 104, 0, 3),
(1501, 'K', '科尔沁右翼前旗', '科尔沁右翼前旗KEERQINYOUYIQIANQI', 104, 0, 3),
(1502, 'T', '突泉县', '突泉县TUQUANXIAN', 104, 0, 3),
(1503, 'A', '阿尔山市', '阿尔山市AERSHANSHI', 104, 0, 3),
(1504, 'D', '东乌珠穆沁旗', '东乌珠穆沁旗DONGWUZHUMUQINQI', 105, 0, 3),
(1505, 'E', '二连浩特市', '二连浩特市ERLIANHAOTESHI', 105, 0, 3),
(1506, 'D', '多伦县', '多伦县DUOLUNXIAN', 105, 0, 3),
(1507, 'T', '太仆寺旗', '太仆寺旗TAIPUSIQI', 105, 0, 3),
(1508, 'Z', '正蓝旗', '正蓝旗ZHENGLANQI', 105, 0, 3),
(1509, 'Z', '正镶白旗', '正镶白旗ZHENGXIANGBAIQI', 105, 0, 3),
(1510, 'S', '苏尼特右旗', '苏尼特右旗SUNITEYOUQI', 105, 0, 3),
(1511, 'S', '苏尼特左旗', '苏尼特左旗SUNITEZUOQI', 105, 0, 3),
(1512, 'X', '西乌珠穆沁旗', '西乌珠穆沁旗XIWUZHUMUQINQI', 105, 0, 3),
(1513, 'X', '锡林浩特市', '锡林浩特市XILINHAOTESHI', 105, 0, 3),
(1514, 'X', '镶黄旗', '镶黄旗XIANGHUANGQI', 105, 0, 3),
(1515, 'A', '阿巴嘎旗', '阿巴嘎旗ABAGAQI', 105, 0, 3),
(1516, 'A', '阿拉善右旗', '阿拉善右旗ALASHANYOUQI', 106, 0, 3),
(1517, 'A', '阿拉善左旗', '阿拉善左旗ALASHANZUOQI', 106, 0, 3),
(1518, 'E', '额济纳旗', '额济纳旗EJINAQI', 106, 0, 3),
(1519, 'D', '东陵区', '东陵区DONGLINGQU', 107, 0, 3),
(1520, 'Y', '于洪区', '于洪区YUHONGQU', 107, 0, 3),
(1521, 'H', '和平区', '和平区HEPINGQU', 107, 0, 3),
(1522, 'D', '大东区', '大东区DADONGQU', 107, 0, 3),
(1523, 'K', '康平县', '康平县KANGPINGXIAN', 107, 0, 3),
(1524, 'X', '新民市', '新民市XINMINSHI', 107, 0, 3),
(1525, 'S', '沈北新区', '沈北新区SHENBEIXINQU', 107, 0, 3),
(1526, 'S', '沈河区', '沈河区SHENHEQU', 107, 0, 3),
(1527, 'F', '法库县', '法库县FAKUXIAN', 107, 0, 3),
(1528, 'H', '皇姑区', '皇姑区HUANGGUQU', 107, 0, 3),
(1529, 'S', '苏家屯区', '苏家屯区SUJIATUNQU', 107, 0, 3),
(1530, 'L', '辽中县', '辽中县LIAOZHONGXIAN', 107, 0, 3),
(1531, 'T', '铁西区', '铁西区TIEXIQU', 107, 0, 3),
(1532, 'Z', '中山区', '中山区ZHONGSHANQU', 108, 0, 3),
(1533, 'Z', '庄河市', '庄河市ZHUANGHESHI', 108, 0, 3),
(1534, 'L', '旅顺口区', '旅顺口区LYUSHUNKOUQU', 108, 0, 3),
(1535, 'P', '普兰店市', '普兰店市PULANDIANSHI', 108, 0, 3),
(1536, 'S', '沙河口区', '沙河口区SHAHEKOUQU', 108, 0, 3),
(1537, 'W', '瓦房店市', '瓦房店市WAFANGDIANSHI', 108, 0, 3),
(1538, 'G', '甘井子区', '甘井子区GANJINGZIQU', 108, 0, 3),
(1539, 'X', '西岗区', '西岗区XIGANGQU', 108, 0, 3),
(1540, 'J', '金州区', '金州区JINZHOUQU', 108, 0, 3),
(1541, 'C', '长海县', '长海县CHANGHAIXIAN', 108, 0, 3),
(1542, 'Q', '千山区', '千山区QIANSHANQU', 109, 0, 3),
(1543, 'T', '台安县', '台安县TAIANXIAN', 109, 0, 3),
(1544, 'X', '岫岩满族自治县', '岫岩满族自治县XIUYANMANZUZIZHIXIAN', 109, 0, 3),
(1545, 'H', '海城市', '海城市HAICHENGSHI', 109, 0, 3),
(1546, 'L', '立山区', '立山区LISHANQU', 109, 0, 3),
(1547, 'T', '铁东区', '铁东区TIEDONGQU', 109, 0, 3),
(1548, 'T', '铁西区', '铁西区TIEXIQU', 109, 0, 3),
(1549, 'D', '东洲区', '东洲区DONGZHOUQU', 110, 0, 3),
(1550, 'F', '抚顺县', '抚顺县FUSHUNXIAN', 110, 0, 3),
(1551, 'X', '新宾满族自治县', '新宾满族自治县XINBINMANZUZIZHIXIAN', 110, 0, 3),
(1552, 'X', '新抚区', '新抚区XINFUQU', 110, 0, 3),
(1553, 'W', '望花区', '望花区WANGHUAQU', 110, 0, 3),
(1554, 'Q', '清原满族自治县', '清原满族自治县QINGYUANMANZUZIZHIXIAN', 110, 0, 3),
(1555, 'S', '顺城区', '顺城区SHUNCHENGQU', 110, 0, 3),
(1556, 'N', '南芬区', '南芬区NANFENQU', 111, 0, 3),
(1557, 'P', '平山区', '平山区PINGSHANQU', 111, 0, 3),
(1558, 'M', '明山区', '明山区MINGSHANQU', 111, 0, 3),
(1559, 'B', '本溪满族自治县', '本溪满族自治县BENXIMANZUZIZHIXIAN', 111, 0, 3),
(1560, 'H', '桓仁满族自治县', '桓仁满族自治县HUANRENMANZUZIZHIXIAN', 111, 0, 3),
(1561, 'X', '溪湖区', '溪湖区XIHUQU', 111, 0, 3),
(1562, 'D', '东港市', '东港市DONGGANGSHI', 112, 0, 3),
(1563, 'Y', '元宝区', '元宝区YUANBAOQU', 112, 0, 3),
(1564, 'F', '凤城市', '凤城市FENGCHENGSHI', 112, 0, 3),
(1565, 'K', '宽甸满族自治县', '宽甸满族自治县KUANDIANMANZUZIZHIXIAN', 112, 0, 3),
(1566, 'Z', '振兴区', '振兴区ZHENXINGQU', 112, 0, 3),
(1567, 'Z', '振安区', '振安区ZHENANQU', 112, 0, 3),
(1568, 'Y', '义县', '义县YIXIAN', 113, 0, 3),
(1569, 'L', '凌河区', '凌河区LINGHEQU', 113, 0, 3),
(1570, 'L', '凌海市', '凌海市LINGHAISHI', 113, 0, 3),
(1571, 'B', '北镇市', '北镇市BEIZHENSHI', 113, 0, 3),
(1572, 'G', '古塔区', '古塔区GUTAQU', 113, 0, 3),
(1573, 'T', '太和区', '太和区TAIHEQU', 113, 0, 3),
(1574, 'H', '黑山县', '黑山县HEISHANXIAN', 113, 0, 3),
(1575, 'D', '大石桥市', '大石桥市DASHIQIAOSHI', 114, 0, 3),
(1576, 'G', '盖州市', '盖州市GAIZHOUSHI', 114, 0, 3),
(1577, 'Z', '站前区', '站前区ZHANQIANQU', 114, 0, 3),
(1578, 'L', '老边区', '老边区LAOBIANQU', 114, 0, 3),
(1579, 'X', '西市区', '西市区XISHIQU', 114, 0, 3),
(1580, 'B', '鲅鱼圈区', '鲅鱼圈区BAYUQUANQU', 114, 0, 3),
(1581, 'T', '太平区', '太平区TAIPINGQU', 115, 0, 3),
(1582, 'Z', '彰武县', '彰武县ZHANGWUXIAN', 115, 0, 3),
(1583, 'X', '新邱区', '新邱区XINQIUQU', 115, 0, 3),
(1584, 'H', '海州区', '海州区HAIZHOUQU', 115, 0, 3),
(1585, 'Q', '清河门区', '清河门区QINGHEMENQU', 115, 0, 3),
(1586, 'X', '细河区', '细河区XIHEQU', 115, 0, 3),
(1587, 'M', '蒙古族自治县', '蒙古族自治县MENGGUZUZIZHIXIAN', 115, 0, 3),
(1588, 'T', '太子河区', '太子河区TAIZIHEQU', 116, 0, 3),
(1589, 'H', '宏伟区', '宏伟区HONGWEIQU', 116, 0, 3),
(1590, 'G', '弓长岭区', '弓长岭区GONGCHANGLINGQU', 116, 0, 3),
(1591, 'W', '文圣区', '文圣区WENSHENGQU', 116, 0, 3),
(1592, 'D', '灯塔市', '灯塔市DENGTASHI', 116, 0, 3),
(1593, 'B', '白塔区', '白塔区BAITAQU', 116, 0, 3),
(1594, 'L', '辽阳县', '辽阳县LIAOYANGXIAN', 116, 0, 3),
(1595, 'X', '兴隆台区', '兴隆台区XINGLONGTAIQU', 117, 0, 3),
(1596, 'S', '双台子区', '双台子区SHUANGTAIZIQU', 117, 0, 3),
(1597, 'D', '大洼县', '大洼县DAWAXIAN', 117, 0, 3),
(1598, 'P', '盘山县', '盘山县PANSHANXIAN', 117, 0, 3),
(1599, 'K', '开原市', '开原市KAIYUANSHI', 118, 0, 3),
(1600, 'C', '昌图县', '昌图县CHANGTUXIAN', 118, 0, 3),
(1601, 'Q', '清河区', '清河区QINGHEQU', 118, 0, 3),
(1602, 'X', '西丰县', '西丰县XIFENGXIAN', 118, 0, 3),
(1603, 'D', '调兵山市', '调兵山市DIAOBINGSHANSHI', 118, 0, 3),
(1604, 'T', '铁岭县', '铁岭县TIELINGXIAN', 118, 0, 3),
(1605, 'Y', '银州区', '银州区YINZHOUQU', 118, 0, 3),
(1606, 'L', '凌源市', '凌源市LINGYUANSHI', 119, 0, 3),
(1607, 'B', '北票市', '北票市BEIPIAOSHI', 119, 0, 3),
(1608, 'S', '双塔区', '双塔区SHUANGTAQU', 119, 0, 3),
(1609, 'K', '喀喇沁左翼蒙古族自治县', '喀喇沁左翼蒙古族自治县KALAQINZUOYIMENGGUZUZIZHIXIAN', 119, 0, 3),
(1610, 'J', '建平县', '建平县JIANPINGXIAN', 119, 0, 3),
(1611, 'Z', '朝阳县', '朝阳县ZHAOYANGXIAN', 119, 0, 3),
(1612, 'L', '龙城区', '龙城区LONGCHENGQU', 119, 0, 3),
(1613, 'X', '兴城市', '兴城市XINGCHENGSHI', 120, 0, 3),
(1614, 'N', '南票区', '南票区NANPIAOQU', 120, 0, 3),
(1615, 'J', '建昌县', '建昌县JIANCHANGXIAN', 120, 0, 3),
(1616, 'S', '绥中县', '绥中县SUIZHONGXIAN', 120, 0, 3),
(1617, 'L', '连山区', '连山区LIANSHANQU', 120, 0, 3),
(1618, 'L', '龙港区', '龙港区LONGGANGQU', 120, 0, 3),
(1619, 'J', '九台市', '九台市JIUTAISHI', 121, 0, 3),
(1620, 'E', '二道区', '二道区ERDAOQU', 121, 0, 3),
(1621, 'N', '农安县', '农安县NONGANXIAN', 121, 0, 3),
(1622, 'N', '南关区', '南关区NANGUANQU', 121, 0, 3),
(1623, 'S', '双阳区', '双阳区SHUANGYANGQU', 121, 0, 3),
(1624, 'K', '宽城区', '宽城区KUANCHENGQU', 121, 0, 3),
(1625, 'D', '德惠市', '德惠市DEHUISHI', 121, 0, 3),
(1626, 'G', '高新区', '高新区GAOXINQU', 121, 0, 3),
(1627, 'Y', '榆树市', '榆树市YUSHUSHI', 121, 0, 3),
(1628, 'L', '绿园区', '绿园区LYUYUANQU', 121, 0, 3),
(1629, 'F', '丰满区', '丰满区FENGMANQU', 122, 0, 3),
(1630, 'C', '昌邑区', '昌邑区CHANGYIQU', 122, 0, 3),
(1631, 'H', '桦甸市', '桦甸市HUADIANSHI', 122, 0, 3),
(1632, 'Y', '永吉县', '永吉县YONGJIXIAN', 122, 0, 3),
(1633, 'P', '磐石市', '磐石市PANSHISHI', 122, 0, 3),
(1634, 'S', '舒兰市', '舒兰市SHULANSHI', 122, 0, 3),
(1635, 'C', '船营区', '船营区CHUANYINGQU', 122, 0, 3),
(1636, 'J', '蛟河市', '蛟河市JIAOHESHI', 122, 0, 3),
(1637, 'L', '龙潭区', '龙潭区LONGTANQU', 122, 0, 3),
(1638, 'Y', '伊通满族自治县', '伊通满族自治县YITONGMANZUZIZHIXIAN', 123, 0, 3),
(1639, 'G', '公主岭市', '公主岭市GONGZHULINGSHI', 123, 0, 3),
(1640, 'S', '双辽市', '双辽市SHUANGLIAOSHI', 123, 0, 3),
(1641, 'L', '梨树县', '梨树县LISHUXIAN', 123, 0, 3),
(1642, 'T', '铁东区', '铁东区TIEDONGQU', 123, 0, 3),
(1643, 'T', '铁西区', '铁西区TIEXIQU', 123, 0, 3),
(1644, 'D', '东丰县', '东丰县DONGFENGXIAN', 124, 0, 3),
(1645, 'D', '东辽县', '东辽县DONGLIAOXIAN', 124, 0, 3),
(1646, 'X', '西安区', '西安区XIANQU', 124, 0, 3),
(1647, 'L', '龙山区', '龙山区LONGSHANQU', 124, 0, 3),
(1648, 'D', '东昌区', '东昌区DONGCHANGQU', 125, 0, 3),
(1649, 'E', '二道江区', '二道江区ERDAOJIANGQU', 125, 0, 3),
(1650, 'L', '柳河县', '柳河县LIUHEXIAN', 125, 0, 3),
(1651, 'M', '梅河口市', '梅河口市MEIHEKOUSHI', 125, 0, 3),
(1652, 'H', '辉南县', '辉南县HUINANXIAN', 125, 0, 3),
(1653, 'T', '通化县', '通化县TONGHUAXIAN', 125, 0, 3),
(1654, 'J', '集安市', '集安市JIANSHI', 125, 0, 3),
(1655, 'L', '临江市', '临江市LINJIANGSHI', 126, 0, 3),
(1656, 'B', '八道江区', '八道江区BADAOJIANGQU', 126, 0, 3),
(1657, 'F', '抚松县', '抚松县FUSONGXIAN', 126, 0, 3),
(1658, 'J', '江源区', '江源区JIANGYUANQU', 126, 0, 3),
(1659, 'C', '长白朝鲜族自治县', '长白朝鲜族自治县CHANGBAICHAOXIANZUZIZHIXIAN', 126, 0, 3),
(1660, 'J', '靖宇县', '靖宇县JINGYUXIAN', 126, 0, 3),
(1661, 'G', '干安县', '干安县GANANXIAN', 127, 0, 3),
(1662, 'Q', '前郭尔罗斯蒙古族自治县', '前郭尔罗斯蒙古族自治县QIANGUOERLUOSIMENGGUZUZIZHIXIAN', 127, 0, 3),
(1663, 'N', '宁江区', '宁江区NINGJIANGQU', 127, 0, 3),
(1664, 'F', '扶余县', '扶余县FUYUXIAN', 127, 0, 3),
(1665, 'C', '长岭县', '长岭县CHANGLINGXIAN', 127, 0, 3),
(1666, 'D', '大安市', '大安市DAANSHI', 128, 0, 3),
(1667, 'T', '洮北区', '洮北区TAOBEIQU', 128, 0, 3),
(1668, 'T', '洮南市', '洮南市TAONANSHI', 128, 0, 3);
INSERT INTO `ims_xm_mallv3_area` (`id`, `letter`, `area_name`, `keyword`, `area_parent_id`, `area_sort`, `area_deep`) VALUES
(1669, 'T', '通榆县', '通榆县TONGYUXIAN', 128, 0, 3),
(1670, 'Z', '镇赉县', '镇赉县ZHENLAIXIAN', 128, 0, 3),
(1671, 'H', '和龙市', '和龙市HELONGSHI', 129, 0, 3),
(1672, 'T', '图们市', '图们市TUMENSHI', 129, 0, 3),
(1673, 'A', '安图县', '安图县ANTUXIAN', 129, 0, 3),
(1674, 'Y', '延吉市', '延吉市YANJISHI', 129, 0, 3),
(1675, 'D', '敦化市', '敦化市DUNHUASHI', 129, 0, 3),
(1676, 'W', '汪清县', '汪清县WANGQINGXIAN', 129, 0, 3),
(1677, 'H', '珲春市', '珲春市HUNCHUNSHI', 129, 0, 3),
(1678, 'L', '龙井市', '龙井市LONGJINGSHI', 129, 0, 3),
(1679, 'W', '五常市', '五常市WUCHANGSHI', 130, 0, 3),
(1680, 'Y', '依兰县', '依兰县YILANXIAN', 130, 0, 3),
(1681, 'N', '南岗区', '南岗区NANGANGQU', 130, 0, 3),
(1682, 'S', '双城市', '双城市SHUANGCHENGSHI', 130, 0, 3),
(1683, 'H', '呼兰区', '呼兰区HULANQU', 130, 0, 3),
(1684, 'H', '哈尔滨市道里区', '哈尔滨市道里区HAERBINSHIDAOLIQU', 130, 0, 3),
(1685, 'B', '宾县', '宾县BINXIAN', 130, 0, 3),
(1686, 'S', '尚志市', '尚志市SHANGZHISHI', 130, 0, 3),
(1687, 'B', '巴彦县', '巴彦县BAYANXIAN', 130, 0, 3),
(1688, 'P', '平房区', '平房区PINGFANGQU', 130, 0, 3),
(1689, 'Y', '延寿县', '延寿县YANSHOUXIAN', 130, 0, 3),
(1690, 'F', '方正县', '方正县FANGZHENGXIAN', 130, 0, 3),
(1691, 'M', '木兰县', '木兰县MULANXIAN', 130, 0, 3),
(1692, 'S', '松北区', '松北区SONGBEIQU', 130, 0, 3),
(1693, 'T', '通河县', '通河县TONGHEXIAN', 130, 0, 3),
(1694, 'D', '道外区', '道外区DAOWAIQU', 130, 0, 3),
(1695, 'A', '阿城区', '阿城区ACHENGQU', 130, 0, 3),
(1696, 'X', '香坊区', '香坊区XIANGFANGQU', 130, 0, 3),
(1697, 'Y', '依安县', '依安县YIANXIAN', 131, 0, 3),
(1698, 'K', '克东县', '克东县KEDONGXIAN', 131, 0, 3),
(1699, 'K', '克山县', '克山县KESHANXIAN', 131, 0, 3),
(1700, 'F', '富拉尔基区', '富拉尔基区FULAERJIQU', 131, 0, 3),
(1701, 'F', '富裕县', '富裕县FUYUXIAN', 131, 0, 3),
(1702, 'J', '建华区', '建华区JIANHUAQU', 131, 0, 3),
(1703, 'B', '拜泉县', '拜泉县BAIQUANXIAN', 131, 0, 3),
(1704, 'A', '昂昂溪区', '昂昂溪区ANGANGXIQU', 131, 0, 3),
(1705, 'M', '梅里斯达斡尔族区', '梅里斯达斡尔族区MEILISIDAWOERZUQU', 131, 0, 3),
(1706, 'T', '泰来县', '泰来县TAILAIXIAN', 131, 0, 3),
(1707, 'G', '甘南县', '甘南县GANNANXIAN', 131, 0, 3),
(1708, 'N', '碾子山区', '碾子山区NIANZISHANQU', 131, 0, 3),
(1709, 'N', '讷河市', '讷河市NEHESHI', 131, 0, 3),
(1710, 'T', '铁锋区', '铁锋区TIEFENGQU', 131, 0, 3),
(1711, 'L', '龙江县', '龙江县LONGJIANGXIAN', 131, 0, 3),
(1712, 'L', '龙沙区', '龙沙区LONGSHAQU', 131, 0, 3),
(1713, 'C', '城子河区', '城子河区CHENGZIHEQU', 132, 0, 3),
(1714, 'M', '密山市', '密山市MISHANSHI', 132, 0, 3),
(1715, 'H', '恒山区', '恒山区HENGSHANQU', 132, 0, 3),
(1716, 'L', '梨树区', '梨树区LISHUQU', 132, 0, 3),
(1717, 'D', '滴道区', '滴道区DIDAOQU', 132, 0, 3),
(1718, 'H', '虎林市', '虎林市HULINSHI', 132, 0, 3),
(1719, 'J', '鸡东县', '鸡东县JIDONGXIAN', 132, 0, 3),
(1720, 'J', '鸡冠区', '鸡冠区JIGUANQU', 132, 0, 3),
(1721, 'M', '麻山区', '麻山区MASHANQU', 132, 0, 3),
(1722, 'D', '东山区', '东山区DONGSHANQU', 133, 0, 3),
(1723, 'X', '兴安区', '兴安区XINGANQU', 133, 0, 3),
(1724, 'X', '兴山区', '兴山区XINGSHANQU', 133, 0, 3),
(1725, 'N', '南山区', '南山区NANSHANQU', 133, 0, 3),
(1726, 'X', '向阳区', '向阳区XIANGYANGQU', 133, 0, 3),
(1727, 'G', '工农区', '工农区GONGNONGQU', 133, 0, 3),
(1728, 'S', '绥滨县', '绥滨县SUIBINXIAN', 133, 0, 3),
(1729, 'L', '萝北县', '萝北县LUOBEIXIAN', 133, 0, 3),
(1730, 'Y', '友谊县', '友谊县YOUYIXIAN', 134, 0, 3),
(1731, 'S', '四方台区', '四方台区SIFANGTAIQU', 134, 0, 3),
(1732, 'B', '宝山区', '宝山区BAOSHANQU', 134, 0, 3),
(1733, 'B', '宝清县', '宝清县BAOQINGXIAN', 134, 0, 3),
(1734, 'J', '尖山区', '尖山区JIANSHANQU', 134, 0, 3),
(1735, 'L', '岭东区', '岭东区LINGDONGQU', 134, 0, 3),
(1736, 'J', '集贤县', '集贤县JIXIANXIAN', 134, 0, 3),
(1737, 'R', '饶河县', '饶河县RAOHEXIAN', 134, 0, 3),
(1738, 'D', '大同区', '大同区DATONGQU', 135, 0, 3),
(1739, 'D', '杜尔伯特蒙古族自治县', '杜尔伯特蒙古族自治县DUERBOTEMENGGUZUZIZHIXIAN', 135, 0, 3),
(1740, 'L', '林甸县', '林甸县LINDIANXIAN', 135, 0, 3),
(1741, 'H', '红岗区', '红岗区HONGGANGQU', 135, 0, 3),
(1742, 'Z', '肇州县', '肇州县ZHAOZHOUXIAN', 135, 0, 3),
(1743, 'Z', '肇源县', '肇源县ZHAOYUANXIAN', 135, 0, 3),
(1744, 'H', '胡路区', '胡路区HULUQU', 135, 0, 3),
(1745, 'S', '萨尔图区', '萨尔图区SAERTUQU', 135, 0, 3),
(1746, 'L', '龙凤区', '龙凤区LONGFENGQU', 135, 0, 3),
(1747, 'S', '上甘岭区', '上甘岭区SHANGGANLINGQU', 136, 0, 3),
(1748, 'W', '乌伊岭区', '乌伊岭区WUYILINGQU', 136, 0, 3),
(1749, 'W', '乌马河区', '乌马河区WUMAHEQU', 136, 0, 3),
(1750, 'W', '五营区', '五营区WUYINGQU', 136, 0, 3),
(1751, 'Y', '伊春区', '伊春区YICHUNQU', 136, 0, 3),
(1752, 'N', '南岔区', '南岔区NANCHAQU', 136, 0, 3),
(1753, 'Y', '友好区', '友好区YOUHAOQU', 136, 0, 3),
(1754, 'J', '嘉荫县', '嘉荫县JIAYINXIAN', 136, 0, 3),
(1755, 'D', '带岭区', '带岭区DAILINGQU', 136, 0, 3),
(1756, 'X', '新青区', '新青区XINQINGQU', 136, 0, 3),
(1757, 'T', '汤旺河区', '汤旺河区TANGWANGHEQU', 136, 0, 3),
(1758, 'H', '红星区', '红星区HONGXINGQU', 136, 0, 3),
(1759, 'M', '美溪区', '美溪区MEIXIQU', 136, 0, 3),
(1760, 'C', '翠峦区', '翠峦区CUILUANQU', 136, 0, 3),
(1761, 'X', '西林区', '西林区XILINQU', 136, 0, 3),
(1762, 'J', '金山屯区', '金山屯区JINSHANZHUNQU', 136, 0, 3),
(1763, 'T', '铁力市', '铁力市TIELISHI', 136, 0, 3),
(1764, 'D', '东风区', '东风区DONGFENGQU', 137, 0, 3),
(1765, 'Q', '前进区', '前进区QIANJINQU', 137, 0, 3),
(1766, 'T', '同江市', '同江市TONGJIANGSHI', 137, 0, 3),
(1767, 'X', '向阳区', '向阳区XIANGYANGQU', 137, 0, 3),
(1768, 'F', '富锦市', '富锦市FUJINSHI', 137, 0, 3),
(1769, 'F', '抚远县', '抚远县FUYUANXIAN', 137, 0, 3),
(1770, 'H', '桦南县', '桦南县HUANANXIAN', 137, 0, 3),
(1771, 'H', '桦川县', '桦川县HUACHUANXIAN', 137, 0, 3),
(1772, 'T', '汤原县', '汤原县TANGYUANXIAN', 137, 0, 3),
(1773, 'J', '郊区', '郊区JIAOQU', 137, 0, 3),
(1774, 'B', '勃利县', '勃利县BOLIXIAN', 138, 0, 3),
(1775, 'X', '新兴区', '新兴区XINXINGQU', 138, 0, 3),
(1776, 'T', '桃山区', '桃山区TAOSHANQU', 138, 0, 3),
(1777, 'Q', '茄子河区', '茄子河区QIEZIHEQU', 138, 0, 3),
(1778, 'D', '东宁县', '东宁县DONGNINGXIAN', 139, 0, 3),
(1779, 'D', '东安区', '东安区DONGANQU', 139, 0, 3),
(1780, 'N', '宁安市', '宁安市NINGANSHI', 139, 0, 3),
(1781, 'L', '林口县', '林口县LINKOUXIAN', 139, 0, 3),
(1782, 'H', '海林市', '海林市HAILINSHI', 139, 0, 3),
(1783, 'A', '爱民区', '爱民区AIMINQU', 139, 0, 3),
(1784, 'M', '穆棱市', '穆棱市MULINGSHI', 139, 0, 3),
(1785, 'S', '绥芬河市', '绥芬河市SUIFENHESHI', 139, 0, 3),
(1786, 'X', '西安区', '西安区XIANQU', 139, 0, 3),
(1787, 'Y', '阳明区', '阳明区YANGMINGQU', 139, 0, 3),
(1788, 'W', '五大连池市', '五大连池市WUDALIANCHISHI', 140, 0, 3),
(1789, 'B', '北安市', '北安市BEIANSHI', 140, 0, 3),
(1790, 'N', '嫩江县', '嫩江县NENJIANGXIAN', 140, 0, 3),
(1791, 'S', '孙吴县', '孙吴县SUNWUXIAN', 140, 0, 3),
(1792, 'A', '爱辉区', '爱辉区AIHUIQU', 140, 0, 3),
(1793, 'C', '车逊克县', '车逊克县CHEXUNKEXIAN', 140, 0, 3),
(1794, 'X', '逊克县', '逊克县XUNKEXIAN', 140, 0, 3),
(1795, 'L', '兰西县', '兰西县LANXIXIAN', 141, 0, 3),
(1796, 'A', '安达市', '安达市ANDASHI', 141, 0, 3),
(1797, 'Q', '庆安县', '庆安县QINGANXIAN', 141, 0, 3),
(1798, 'M', '明水县', '明水县MINGSHUIXIAN', 141, 0, 3),
(1799, 'W', '望奎县', '望奎县WANGKUIXIAN', 141, 0, 3),
(1800, 'H', '海伦市', '海伦市HAILUNSHI', 141, 0, 3),
(1801, 'S', '绥化市北林区', '绥化市北林区SUIHUASHIBEILINQU', 141, 0, 3),
(1802, 'S', '绥棱县', '绥棱县SUILENGXIAN', 141, 0, 3),
(1803, 'Z', '肇东市', '肇东市ZHAODONGSHI', 141, 0, 3),
(1804, 'Q', '青冈县', '青冈县QINGGANGXIAN', 141, 0, 3),
(1805, 'H', '呼玛县', '呼玛县HUMAXIAN', 142, 0, 3),
(1806, 'T', '塔河县', '塔河县TAHEXIAN', 142, 0, 3),
(1807, 'D', '大兴安岭地区加格达奇区', '大兴安岭地区加格达奇区DAXINGANLINGDIQUJIAGEDAQIQU', 142, 0, 3),
(1808, 'D', '大兴安岭地区呼中区', '大兴安岭地区呼中区DAXINGANLINGDIQUHUZHONGQU', 142, 0, 3),
(1809, 'D', '大兴安岭地区新林区', '大兴安岭地区新林区DAXINGANLINGDIQUXINLINQU', 142, 0, 3),
(1810, 'D', '大兴安岭地区松岭区', '大兴安岭地区松岭区DAXINGANLINGDIQUSONGLINGQU', 142, 0, 3),
(1811, 'M', '漠河县', '漠河县MOHEXIAN', 142, 0, 3),
(2028, 'L', '六合区', '六合区LUHEQU', 162, 0, 3),
(2029, 'J', '建邺区', '建邺区JIANYEQU', 162, 0, 3),
(2030, 'Q', '栖霞区', '栖霞区QIXIAQU', 162, 0, 3),
(2031, 'J', '江宁区', '江宁区JIANGNINGQU', 162, 0, 3),
(2032, 'P', '浦口区', '浦口区PUKOUQU', 162, 0, 3),
(2033, 'L', '溧水县', '溧水县LISHUIXIAN', 162, 0, 3),
(2034, 'X', '玄武区', '玄武区XUANWUQU', 162, 0, 3),
(2035, 'B', '白下区', '白下区BAIXIAQU', 162, 0, 3),
(2036, 'Q', '秦淮区', '秦淮区QINHUAIQU', 162, 0, 3),
(2037, 'Y', '雨花台区', '雨花台区YUHUATAIQU', 162, 0, 3),
(2038, 'G', '高淳县', '高淳县GAOCHUNXIAN', 162, 0, 3),
(2039, 'G', '鼓楼区', '鼓楼区GULOUQU', 162, 0, 3),
(2042, 'Y', '宜兴市', '宜兴市YIXINGSHI', 163, 7, 3),
(2044, 'H', '惠山区', '惠山区HUISHANQU', 163, 2, 3),
(2045, 'J', '江阴市', '江阴市JIANGYINSHI', 163, 6, 3),
(2046, 'B', '滨湖区', '滨湖区BINHUQU', 163, 3, 3),
(2047, 'X', '锡山区', '锡山区XISHANQU', 163, 1, 3),
(2048, 'F', '丰县', '丰县FENGXIAN', 164, 0, 3),
(2049, 'J', '九里区', '九里区JIULIQU', 164, 0, 3),
(2050, 'Y', '云龙区', '云龙区YUNLONGQU', 164, 0, 3),
(2051, 'X', '新沂市', '新沂市XINYISHI', 164, 0, 3),
(2052, 'P', '沛县', '沛县PEIXIAN', 164, 0, 3),
(2053, 'Q', '泉山区', '泉山区QUANSHANQU', 164, 0, 3),
(2054, 'S', '睢宁县', '睢宁县SUININGXIAN', 164, 0, 3),
(2055, 'J', '贾汪区', '贾汪区JIAWANGQU', 164, 0, 3),
(2056, 'P', '邳州市', '邳州市PIZHOUSHI', 164, 0, 3),
(2057, 'T', '铜山县', '铜山县TONGSHANXIAN', 164, 0, 3),
(2058, 'G', '鼓楼区', '鼓楼区GULOUQU', 164, 0, 3),
(2059, 'T', '天宁区', '天宁区TIANNINGQU', 165, 0, 3),
(2060, 'Q', '戚墅堰区', '戚墅堰区QISHUYANQU', 165, 0, 3),
(2061, 'X', '新北区', '新北区XINBEIQU', 165, 0, 3),
(2062, 'W', '武进区', '武进区WUJINQU', 165, 0, 3),
(2063, 'L', '溧阳市', '溧阳市LIYANGSHI', 165, 0, 3),
(2064, 'J', '金坛市', '金坛市JINTANSHI', 165, 0, 3),
(2065, 'Z', '钟楼区', '钟楼区ZHONGLOUQU', 165, 0, 3),
(2066, 'W', '吴中区', '吴中区WUZHONGQU', 166, 0, 3),
(2067, 'W', '吴江市', '吴江市WUJIANGSHI', 166, 0, 3),
(2068, 'T', '太仓市', '太仓市TAICANGSHI', 166, 0, 3),
(2069, 'C', '常熟市', '常熟市CHANGSHUSHI', 166, 0, 3),
(2070, 'P', '平江区', '平江区PINGJIANGQU', 166, 0, 3),
(2071, 'Z', '张家港市', '张家港市ZHANGJIAGANGSHI', 166, 0, 3),
(2072, 'K', '昆山市', '昆山市KUNSHANSHI', 166, 0, 3),
(2073, 'C', '沧浪区', '沧浪区CANGLANGQU', 166, 0, 3),
(2074, 'X', '相城区', '相城区XIANGCHENGQU', 166, 0, 3),
(2075, 'S', '苏州工业园区', '苏州工业园区SUZHOUGONGYEYUANQU', 166, 0, 3),
(2076, 'H', '虎丘区', '虎丘区HUQIUQU', 166, 0, 3),
(2077, 'J', '金阊区', '金阊区JINCHANGQU', 166, 0, 3),
(2078, 'Q', '启东市', '启东市QIDONGSHI', 167, 0, 3),
(2079, 'R', '如东县', '如东县RUDONGXIAN', 167, 0, 3),
(2080, 'R', '如皋市', '如皋市RUGAOSHI', 167, 0, 3),
(2081, 'C', '崇川区', '崇川区CHONGCHUANQU', 167, 0, 3),
(2082, 'H', '海安县', '海安县HAIANXIAN', 167, 0, 3),
(2083, 'H', '海门市', '海门市HAIMENSHI', 167, 0, 3),
(2084, 'G', '港闸区', '港闸区GANGZHAQU', 167, 0, 3),
(2085, 'T', '通州市', '通州市TONGZHOUSHI', 167, 0, 3),
(2086, 'D', '东海县', '东海县DONGHAIXIAN', 168, 0, 3),
(2087, 'X', '新浦区', '新浦区XINPUQU', 168, 0, 3),
(2088, 'H', '海州区', '海州区HAIZHOUQU', 168, 0, 3),
(2089, 'G', '灌云县', '灌云县GUANYUNXIAN', 168, 0, 3),
(2090, 'G', '灌南县', '灌南县GUANNANXIAN', 168, 0, 3),
(2091, 'G', '赣榆县', '赣榆县GANYUXIAN', 168, 0, 3),
(2092, 'L', '连云区', '连云区LIANYUNQU', 168, 0, 3),
(2093, 'C', '楚州区', '楚州区CHUZHOUQU', 169, 0, 3),
(2094, 'H', '洪泽县', '洪泽县HONGZEXIAN', 169, 0, 3),
(2095, 'L', '涟水县', '涟水县LIANSHUIXIAN', 169, 0, 3),
(2096, 'H', '淮阴区', '淮阴区HUAIYINQU', 169, 0, 3),
(2097, 'Q', '清河区', '清河区QINGHEQU', 169, 0, 3),
(2098, 'Q', '清浦区', '清浦区QINGPUQU', 169, 0, 3),
(2099, 'X', '盱眙县', '盱眙县XUYIXIAN', 169, 0, 3),
(2100, 'J', '金湖县', '金湖县JINHUXIAN', 169, 0, 3),
(2101, 'D', '东台市', '东台市DONGTAISHI', 170, 0, 3),
(2102, 'T', '亭湖区', '亭湖区TINGHUQU', 170, 0, 3),
(2103, 'X', '响水县', '响水县XIANGSHUIXIAN', 170, 0, 3),
(2104, 'D', '大丰区', '大丰区DAFENGQU', 170, 0, 3),
(2105, 'S', '射阳县', '射阳县SHEYANGXIAN', 170, 0, 3),
(2106, 'J', '建湖县', '建湖县JIANHUXIAN', 170, 0, 3),
(2107, 'B', '滨海县', '滨海县BINHAIXIAN', 170, 0, 3),
(2108, 'Y', '盐都区', '盐都区YANDUQU', 170, 0, 3),
(2109, 'F', '阜宁县', '阜宁县FUNINGXIAN', 170, 0, 3),
(2110, 'Y', '仪征市', '仪征市YIZHENGSHI', 171, 0, 3),
(2111, 'B', '宝应县', '宝应县BAOYINGXIAN', 171, 0, 3),
(2112, 'G', '广陵区', '广陵区GUANGLINGQU', 171, 0, 3),
(2113, 'J', '江都市', '江都市JIANGDUSHI', 171, 0, 3),
(2114, 'W', '维扬区', '维扬区WEIYANGQU', 171, 0, 3),
(2115, 'H', '邗江区', '邗江区HANJIANGQU', 171, 0, 3),
(2116, 'G', '高邮市', '高邮市GAOYOUSHI', 171, 0, 3),
(2117, 'D', '丹徒区', '丹徒区DANTUQU', 172, 0, 3),
(2118, 'D', '丹阳市', '丹阳市DANYANGSHI', 172, 0, 3),
(2119, 'J', '京口区', '京口区JINGKOUQU', 172, 0, 3),
(2120, 'J', '句容市', '句容市JURONGSHI', 172, 0, 3),
(2121, 'Y', '扬中市', '扬中市YANGZHONGSHI', 172, 0, 3),
(2122, 'R', '润州区', '润州区RUNZHOUQU', 172, 0, 3),
(2123, 'X', '兴化市', '兴化市XINGHUASHI', 173, 0, 3),
(2124, 'J', '姜堰市', '姜堰市JIANGYANSHI', 173, 0, 3),
(2125, 'T', '泰兴市', '泰兴市TAIXINGSHI', 173, 0, 3),
(2126, 'H', '海陵区', '海陵区HAILINGQU', 173, 0, 3),
(2127, 'J', '靖江市', '靖江市JINGJIANGSHI', 173, 0, 3),
(2128, 'G', '高港区', '高港区GAOGANGQU', 173, 0, 3),
(2129, 'S', '宿城区', '宿城区SUCHENGQU', 174, 0, 3),
(2130, 'S', '宿豫区', '宿豫区SUYUQU', 174, 0, 3),
(2131, 'S', '沭阳县', '沭阳县SHUYANGXIAN', 174, 0, 3),
(2132, 'S', '泗洪县', '泗洪县SIHONGXIAN', 174, 0, 3),
(2133, 'S', '泗阳县', '泗阳县SIYANGXIAN', 174, 0, 3),
(2134, 'S', '上城区', '上城区SHANGCHENGQU', 175, 0, 3),
(2135, 'X', '下城区', '下城区XIACHENGQU', 175, 0, 3),
(2136, 'L', '临安市', '临安市LINANSHI', 175, 0, 3),
(2137, 'Y', '余杭区', '余杭区YUHANGQU', 175, 0, 3),
(2138, 'F', '富阳市', '富阳市FUYANGSHI', 175, 0, 3),
(2139, 'J', '建德市', '建德市JIANDESHI', 175, 0, 3),
(2140, 'G', '拱墅区', '拱墅区GONGSHUQU', 175, 0, 3),
(2141, 'T', '桐庐县', '桐庐县TONGLUXIAN', 175, 0, 3),
(2142, 'J', '江干区', '江干区JIANGGANQU', 175, 0, 3),
(2143, 'C', '淳安县', '淳安县CHUNANXIAN', 175, 0, 3),
(2144, 'B', '滨江区', '滨江区BINJIANGQU', 175, 0, 3),
(2145, 'X', '萧山区', '萧山区XIAOSHANQU', 175, 0, 3),
(2146, 'X', '西湖区', '西湖区XIHUQU', 175, 0, 3),
(2147, 'Y', '余姚市', '余姚市YUYAOSHI', 176, 0, 3),
(2148, 'B', '北仑区', '北仑区BEILUNQU', 176, 0, 3),
(2149, 'F', '奉化市', '奉化市FENGHUASHI', 176, 0, 3),
(2150, 'N', '宁海县', '宁海县NINGHAIXIAN', 176, 0, 3),
(2151, 'C', '慈溪市', '慈溪市CIXISHI', 176, 0, 3),
(2152, 'J', '江东区', '江东区JIANGDONGQU', 176, 0, 3),
(2153, 'J', '江北区', '江北区JIANGBEIQU', 176, 0, 3),
(2154, 'H', '海曙区', '海曙区HAISHUQU', 176, 0, 3),
(2155, 'X', '象山县', '象山县XIANGSHANXIAN', 176, 0, 3),
(2156, 'Y', '鄞州区', '鄞州区YINZHOUQU', 176, 0, 3),
(2157, 'Z', '镇海区', '镇海区ZHENHAIQU', 176, 0, 3),
(2158, 'Y', '乐清市', '乐清市YUEQINGSHI', 177, 0, 3),
(2159, 'P', '平阳县', '平阳县PINGYANGXIAN', 177, 0, 3),
(2160, 'W', '文成县', '文成县WENCHENGXIAN', 177, 0, 3),
(2161, 'Y', '永嘉县', '永嘉县YONGJIAXIAN', 177, 0, 3),
(2162, 'T', '泰顺县', '泰顺县TAISHUNXIAN', 177, 0, 3),
(2163, 'D', '洞头县', '洞头县DONGTOUXIAN', 177, 0, 3),
(2164, 'R', '瑞安市', '瑞安市RUIANSHI', 177, 0, 3),
(2165, 'O', '瓯海区', '瓯海区OUHAIQU', 177, 0, 3),
(2166, 'C', '苍南县', '苍南县CANGNANXIAN', 177, 0, 3),
(2167, 'L', '鹿城区', '鹿城区LUCHENGQU', 177, 0, 3),
(2168, 'L', '龙湾区', '龙湾区LONGWANQU', 177, 0, 3),
(2169, 'N', '南湖区', '南湖区NANHUQU', 178, 0, 3),
(2170, 'J', '嘉善县', '嘉善县JIASHANXIAN', 178, 0, 3),
(2171, 'P', '平湖市', '平湖市PINGHUSHI', 178, 0, 3),
(2172, 'T', '桐乡市', '桐乡市TONGXIANGSHI', 178, 0, 3),
(2173, 'H', '海宁市', '海宁市HAININGSHI', 178, 0, 3),
(2174, 'H', '海盐县', '海盐县HAIYANXIAN', 178, 0, 3),
(2175, 'X', '秀洲区', '秀洲区XIUZHOUQU', 178, 0, 3),
(2176, 'N', '南浔区', '南浔区NANXUNQU', 179, 0, 3),
(2177, 'W', '吴兴区', '吴兴区WUXINGQU', 179, 0, 3),
(2178, 'A', '安吉县', '安吉县ANJIXIAN', 179, 0, 3),
(2179, 'D', '德清县', '德清县DEQINGXIAN', 179, 0, 3),
(2180, 'C', '长兴县', '长兴县CHANGXINGXIAN', 179, 0, 3),
(2181, 'S', '上虞市', '上虞市SHANGYUSHI', 180, 0, 3),
(2182, 'S', '嵊州市', '嵊州市SHENGZHOUSHI', 180, 0, 3),
(2183, 'X', '新昌县', '新昌县XINCHANGXIAN', 180, 0, 3),
(2184, 'S', '绍兴县', '绍兴县SHAOXINGXIAN', 180, 0, 3),
(2185, 'Z', '诸暨市', '诸暨市ZHUJISHI', 180, 0, 3),
(2186, 'Y', '越城区', '越城区YUECHENGQU', 180, 0, 3),
(2187, 'D', '定海区', '定海区DINGHAIQU', 181, 0, 3),
(2188, 'D', '岱山县', '岱山县DAISHANXIAN', 181, 0, 3),
(2189, 'S', '嵊泗县', '嵊泗县SHENGSIXIAN', 181, 0, 3),
(2190, 'P', '普陀区', '普陀区PUTUOQU', 181, 0, 3),
(2191, 'C', '常山县', '常山县CHANGSHANXIAN', 182, 0, 3),
(2192, 'K', '开化县', '开化县KAIHUAXIAN', 182, 0, 3),
(2193, 'K', '柯城区', '柯城区KECHENGQU', 182, 0, 3),
(2194, 'J', '江山市', '江山市JIANGSHANSHI', 182, 0, 3),
(2195, 'Q', '衢江区', '衢江区QUJIANGQU', 182, 0, 3),
(2196, 'L', '龙游县', '龙游县LONGYOUXIAN', 182, 0, 3),
(2197, 'D', '东阳市', '东阳市DONGYANGSHI', 183, 0, 3),
(2198, 'Y', '义乌市', '义乌市YIWUSHI', 183, 0, 3),
(2199, 'L', '兰溪市', '兰溪市LANXISHI', 183, 0, 3),
(2200, 'W', '婺城区', '婺城区WUCHENGQU', 183, 0, 3),
(2201, 'W', '武义县', '武义县WUYIXIAN', 183, 0, 3),
(2202, 'Y', '永康市', '永康市YONGKANGSHI', 183, 0, 3),
(2203, 'P', '浦江县', '浦江县PUJIANGXIAN', 183, 0, 3),
(2204, 'P', '磐安县', '磐安县PANANXIAN', 183, 0, 3),
(2205, 'J', '金东区', '金东区JINDONGQU', 183, 0, 3),
(2206, 'S', '三门县', '三门县SANMENXIAN', 184, 0, 3),
(2207, 'L', '临海市', '临海市LINHAISHI', 184, 0, 3),
(2208, 'X', '仙居县', '仙居县XIANJUXIAN', 184, 0, 3),
(2209, 'T', '天台县', '天台县TIANTAIXIAN', 184, 0, 3),
(2210, 'J', '椒江区', '椒江区JIAOJIANGQU', 184, 0, 3),
(2211, 'W', '温岭市', '温岭市WENLINGSHI', 184, 0, 3),
(2212, 'Y', '玉环县', '玉环县YUHUANXIAN', 184, 0, 3),
(2213, 'L', '路桥区', '路桥区LUQIAOQU', 184, 0, 3),
(2214, 'H', '黄岩区', '黄岩区HUANGYANQU', 184, 0, 3),
(2215, 'Y', '云和县', '云和县YUNHEXIAN', 185, 0, 3),
(2216, 'Q', '庆元县', '庆元县QINGYUANXIAN', 185, 0, 3),
(2217, 'J', '景宁畲族自治县', '景宁畲族自治县JINGNINGSHEZUZIZHIXIAN', 185, 0, 3),
(2218, 'S', '松阳县', '松阳县SONGYANGXIAN', 185, 0, 3),
(2219, 'J', '缙云县', '缙云县JINYUNXIAN', 185, 0, 3),
(2220, 'L', '莲都区', '莲都区LIANDUQU', 185, 0, 3),
(2221, 'S', '遂昌县', '遂昌县SUICHANGXIAN', 185, 0, 3),
(2222, 'Q', '青田县', '青田县QINGTIANXIAN', 185, 0, 3),
(2223, 'L', '龙泉市', '龙泉市LONGQUANSHI', 185, 0, 3),
(2224, 'B', '包河区', '包河区BAOHEQU', 186, 0, 3),
(2225, 'L', '庐阳区', '庐阳区LUYANGQU', 186, 0, 3),
(2226, 'Y', '瑶海区', '瑶海区YAOHAIQU', 186, 0, 3),
(2227, 'F', '肥东县', '肥东县FEIDONGXIAN', 186, 0, 3),
(2228, 'F', '肥西县', '肥西县FEIXIXIAN', 186, 0, 3),
(2229, 'S', '蜀山区', '蜀山区SHUSHANQU', 186, 0, 3),
(2230, 'C', '长丰县', '长丰县CHANGFENGXIAN', 186, 0, 3),
(2231, 'S', '三山区', '三山区SANSHANQU', 187, 0, 3),
(2232, 'N', '南陵县', '南陵县NANLINGXIAN', 187, 0, 3),
(2233, 'Y', '弋江区', '弋江区YIJIANGQU', 187, 0, 3),
(2234, 'F', '繁昌县', '繁昌县FANCHANGXIAN', 187, 0, 3),
(2235, 'W', '芜湖县', '芜湖县WUHUXIAN', 187, 0, 3),
(2236, 'J', '镜湖区', '镜湖区JINGHUQU', 187, 0, 3),
(2237, 'J', '鸠江区', '鸠江区JIUJIANGQU', 187, 0, 3),
(2238, 'W', '五河县', '五河县WUHEXIAN', 188, 0, 3),
(2239, 'G', '固镇县', '固镇县GUZHENXIAN', 188, 0, 3),
(2240, 'H', '怀远县', '怀远县HUAIYUANXIAN', 188, 0, 3),
(2241, 'H', '淮上区', '淮上区HUAISHANGQU', 188, 0, 3),
(2242, 'Y', '禹会区', '禹会区YUHUIQU', 188, 0, 3),
(2243, 'B', '蚌山区', '蚌山区BENGSHANQU', 188, 0, 3),
(2244, 'L', '龙子湖区', '龙子湖区LONGZIHUQU', 188, 0, 3),
(2245, 'B', '八公山区', '八公山区BAGONGSHANQU', 189, 0, 3),
(2246, 'F', '凤台县', '凤台县FENGTAIXIAN', 189, 0, 3),
(2247, 'D', '大通区', '大通区DATONGQU', 189, 0, 3),
(2248, 'P', '潘集区', '潘集区PANJIQU', 189, 0, 3),
(2249, 'T', '田家庵区', '田家庵区TIANJIAANQU', 189, 0, 3),
(2250, 'X', '谢家集区', '谢家集区XIEJIAJIQU', 189, 0, 3),
(2251, 'D', '当涂县', '当涂县DANGTUXIAN', 190, 0, 3),
(2252, 'H', '花山区', '花山区HUASHANQU', 190, 0, 3),
(2253, 'J', '金家庄区', '金家庄区JINJIAZHUANGQU', 190, 0, 3),
(2254, 'Y', '雨山区', '雨山区YUSHANQU', 190, 0, 3),
(2255, 'D', '杜集区', '杜集区DUJIQU', 191, 0, 3),
(2256, 'S', '濉溪县', '濉溪县SUIXIXIAN', 191, 0, 3),
(2257, 'L', '烈山区', '烈山区LIESHANQU', 191, 0, 3),
(2258, 'X', '相山区', '相山区XIANGSHANQU', 191, 0, 3),
(2259, 'S', '狮子山区', '狮子山区SHIZISHANQU', 192, 0, 3),
(2260, 'J', '郊区', '郊区JIAOQU', 192, 0, 3),
(2261, 'T', '铜官山区', '铜官山区TONGGUANSHANQU', 192, 0, 3),
(2262, 'T', '铜陵县', '铜陵县TONGLINGXIAN', 192, 0, 3),
(2263, 'D', '大观区', '大观区DAGUANQU', 193, 0, 3),
(2264, 'T', '太湖县', '太湖县TAIHUXIAN', 193, 0, 3),
(2265, 'Y', '宜秀区', '宜秀区YIXIUQU', 193, 0, 3),
(2266, 'S', '宿松县', '宿松县SUSONGXIAN', 193, 0, 3),
(2267, 'Y', '岳西县', '岳西县YUEXIXIAN', 193, 0, 3),
(2268, 'H', '怀宁县', '怀宁县HUAININGXIAN', 193, 0, 3),
(2269, 'W', '望江县', '望江县WANGJIANGXIAN', 193, 0, 3),
(2270, 'Z', '枞阳县', '枞阳县ZONGYANGXIAN', 193, 0, 3),
(2271, 'T', '桐城市', '桐城市TONGCHENGSHI', 193, 0, 3),
(2272, 'Q', '潜山县', '潜山县QIANSHANXIAN', 193, 0, 3),
(2273, 'Y', '迎江区', '迎江区YINGJIANGQU', 193, 0, 3),
(2274, 'X', '休宁县', '休宁县XIUNINGXIAN', 194, 0, 3),
(2275, 'T', '屯溪区', '屯溪区TUNXIQU', 194, 0, 3),
(2276, 'H', '徽州区', '徽州区HUIZHOUQU', 194, 0, 3),
(2277, 'S', '歙县', '歙县SHEXIAN', 194, 0, 3),
(2278, 'Q', '祁门县', '祁门县QIMENXIAN', 194, 0, 3),
(2279, 'H', '黄山区', '黄山区HUANGSHANQU', 194, 0, 3),
(2280, 'Y', '黟县', '黟县YIXIAN', 194, 0, 3),
(2281, 'Q', '全椒县', '全椒县QUANJIAOXIAN', 195, 0, 3),
(2282, 'F', '凤阳县', '凤阳县FENGYANGXIAN', 195, 0, 3),
(2283, 'N', '南谯区', '南谯区NANQIAOQU', 195, 0, 3),
(2284, 'T', '天长市', '天长市TIANCHANGSHI', 195, 0, 3),
(2285, 'D', '定远县', '定远县DINGYUANXIAN', 195, 0, 3),
(2286, 'M', '明光市', '明光市MINGGUANGSHI', 195, 0, 3),
(2287, 'L', '来安县', '来安县LAIANXIAN', 195, 0, 3),
(2288, 'L', '琅玡区', '琅玡区LANGYAQU', 195, 0, 3),
(2289, 'L', '临泉县', '临泉县LINQUANXIAN', 196, 0, 3),
(2290, 'T', '太和县', '太和县TAIHEXIAN', 196, 0, 3),
(2291, 'J', '界首市', '界首市JIESHOUSHI', 196, 0, 3),
(2292, 'F', '阜南县', '阜南县FUNANXIAN', 196, 0, 3),
(2293, 'Y', '颍东区', '颍东区YINGDONGQU', 196, 0, 3),
(2294, 'Y', '颍州区', '颍州区YINGZHOUQU', 196, 0, 3),
(2295, 'Y', '颍泉区', '颍泉区YINGQUANQU', 196, 0, 3),
(2296, 'Y', '颖上县', '颖上县YINGSHANGXIAN', 196, 0, 3),
(2297, 'Y', '埇桥区', '埇桥区YONGQIAOQU', 197, 0, 3),
(2298, 'S', '泗县辖', '泗县辖SIXIANXIA', 197, 0, 3),
(2299, 'L', '灵璧县', '灵璧县LINGBIXIAN', 197, 0, 3),
(2300, 'D', '砀山县', '砀山县DANGSHANXIAN', 197, 0, 3),
(2301, 'X', '萧县', '萧县XIAOXIAN', 197, 0, 3),
(2302, 'H', '含山县', '含山县HANSHANXIAN', 198, 0, 3),
(2303, 'H', '和县', '和县HEXIAN', 198, 0, 3),
(2304, 'J', '居巢区', '居巢区JUCHAOQU', 198, 0, 3),
(2305, 'L', '庐江县', '庐江县LUJIANGXIAN', 198, 0, 3),
(2306, 'W', '无为县', '无为县WUWEIXIAN', 198, 0, 3),
(2307, 'S', '寿县', '寿县SHOUXIAN', 199, 0, 3),
(2308, 'S', '舒城县', '舒城县SHUCHENGXIAN', 199, 0, 3),
(2309, 'Y', '裕安区', '裕安区YUANQU', 199, 0, 3),
(2310, 'J', '金安区', '金安区JINANQU', 199, 0, 3),
(2311, 'J', '金寨县', '金寨县JINZHAIXIAN', 199, 0, 3),
(2312, 'H', '霍山县', '霍山县HUOSHANXIAN', 199, 0, 3),
(2313, 'H', '霍邱县', '霍邱县HUOQIUXIAN', 199, 0, 3),
(2314, 'L', '利辛县', '利辛县LIXINXIAN', 200, 0, 3),
(2315, 'G', '涡阳县', '涡阳县GUOYANGXIAN', 200, 0, 3),
(2316, 'M', '蒙城县', '蒙城县MENGCHENGXIAN', 200, 0, 3),
(2317, 'Q', '谯城区', '谯城区QIAOCHENGQU', 200, 0, 3),
(2318, 'D', '东至县', '东至县DONGZHIXIAN', 201, 0, 3),
(2319, 'S', '石台县', '石台县SHITAIXIAN', 201, 0, 3),
(2320, 'G', '贵池区', '贵池区GUICHIQU', 201, 0, 3),
(2321, 'Q', '青阳县', '青阳县QINGYANGXIAN', 201, 0, 3),
(2322, 'N', '宁国市', '宁国市NINGGUOSHI', 202, 0, 3),
(2323, 'X', '宣州区', '宣州区XUANZHOUQU', 202, 0, 3),
(2324, 'G', '广德县', '广德县GUANGDEXIAN', 202, 0, 3),
(2325, 'J', '旌德县', '旌德县JINGDEXIAN', 202, 0, 3),
(2326, 'J', '泾县', '泾县JINGXIAN', 202, 0, 3),
(2327, 'J', '绩溪县', '绩溪县JIXIXIAN', 202, 0, 3),
(2328, 'L', '郎溪县', '郎溪县LANGXIXIAN', 202, 0, 3),
(2329, 'C', '仓山区', '仓山区CANGSHANQU', 203, 0, 3),
(2330, 'T', '台江区', '台江区TAIJIANGQU', 203, 0, 3),
(2331, 'P', '平潭县', '平潭县PINGTANXIAN', 203, 0, 3),
(2332, 'J', '晋安区', '晋安区JINANQU', 203, 0, 3),
(2333, 'Y', '永泰县', '永泰县YONGTAIXIAN', 203, 0, 3),
(2334, 'F', '福清市', '福清市FUQINGSHI', 203, 0, 3),
(2335, 'L', '罗源县', '罗源县LUOYUANXIAN', 203, 0, 3),
(2336, 'L', '连江县', '连江县LIANJIANGXIAN', 203, 0, 3),
(2337, 'C', '长乐市', '长乐市CHANGLESHI', 203, 0, 3),
(2338, 'M', '闽侯县', '闽侯县MINHOUXIAN', 203, 0, 3),
(2339, 'M', '闽清县', '闽清县MINQINGXIAN', 203, 0, 3),
(2340, 'M', '马尾区', '马尾区MAWEIQU', 203, 0, 3),
(2341, 'G', '鼓楼区', '鼓楼区GULOUQU', 203, 0, 3),
(2342, 'T', '同安区', '同安区TONGANQU', 204, 0, 3),
(2343, 'S', '思明区', '思明区SIMINGQU', 204, 0, 3),
(2344, 'H', '海沧区', '海沧区HAICANGQU', 204, 0, 3),
(2345, 'H', '湖里区', '湖里区HULIQU', 204, 0, 3),
(2346, 'X', '翔安区', '翔安区XIANGANQU', 204, 0, 3),
(2347, 'J', '集美区', '集美区JIMEIQU', 204, 0, 3),
(2348, 'X', '仙游县', '仙游县XIANYOUXIAN', 205, 0, 3),
(2349, 'C', '城厢区', '城厢区CHENGXIANGQU', 205, 0, 3),
(2350, 'H', '涵江区', '涵江区HANJIANGQU', 205, 0, 3),
(2351, 'X', '秀屿区', '秀屿区XIUYUQU', 205, 0, 3),
(2352, 'L', '荔城区', '荔城区LICHENGQU', 205, 0, 3),
(2353, 'S', '三元区', '三元区SANYUANQU', 206, 0, 3),
(2354, 'D', '大田县', '大田县DATIANXIAN', 206, 0, 3),
(2355, 'N', '宁化县', '宁化县NINGHUAXIAN', 206, 0, 3),
(2356, 'J', '将乐县', '将乐县JIANGLEXIAN', 206, 0, 3),
(2357, 'Y', '尤溪县', '尤溪县YOUXIXIAN', 206, 0, 3),
(2358, 'J', '建宁县', '建宁县JIANNINGXIAN', 206, 0, 3),
(2359, 'M', '明溪县', '明溪县MINGXIXIAN', 206, 0, 3),
(2360, 'M', '梅列区', '梅列区MEILIEQU', 206, 0, 3),
(2361, 'Y', '永安市', '永安市YONGANSHI', 206, 0, 3),
(2362, 'S', '沙县', '沙县SHAXIAN', 206, 0, 3),
(2363, 'T', '泰宁县', '泰宁县TAININGXIAN', 206, 0, 3),
(2364, 'Q', '清流县', '清流县QINGLIUXIAN', 206, 0, 3),
(2365, 'F', '丰泽区', '丰泽区FENGZEQU', 207, 0, 3),
(2366, 'N', '南安市', '南安市NANANSHI', 207, 0, 3),
(2367, 'A', '安溪县', '安溪县ANXIXIAN', 207, 0, 3),
(2368, 'D', '德化县', '德化县DEHUAXIAN', 207, 0, 3),
(2369, 'H', '惠安县', '惠安县HUIANXIAN', 207, 0, 3),
(2370, 'J', '晋江市', '晋江市JINJIANGSHI', 207, 0, 3),
(2371, 'Y', '永春县', '永春县YONGCHUNXIAN', 207, 0, 3),
(2372, 'Q', '泉港区', '泉港区QUANGANGQU', 207, 0, 3),
(2373, 'L', '洛江区', '洛江区LUOJIANGQU', 207, 0, 3),
(2374, 'S', '石狮市', '石狮市SHISHISHI', 207, 0, 3),
(2375, 'J', '金门县', '金门县JINMENXIAN', 207, 0, 3),
(2376, 'L', '鲤城区', '鲤城区LICHENGQU', 207, 0, 3),
(2377, 'D', '东山县', '东山县DONGSHANXIAN', 208, 0, 3),
(2378, 'Y', '云霄县', '云霄县YUNXIAOXIAN', 208, 0, 3),
(2379, 'H', '华安县', '华安县HUAANXIAN', 208, 0, 3),
(2380, 'N', '南靖县', '南靖县NANJINGXIAN', 208, 0, 3),
(2381, 'P', '平和县', '平和县PINGHEXIAN', 208, 0, 3),
(2382, 'Z', '漳浦县', '漳浦县ZHANGPUXIAN', 208, 0, 3),
(2383, 'X', '芗城区', '芗城区XIANGCHENGQU', 208, 0, 3),
(2384, 'Z', '诏安县', '诏安县ZHAOANXIAN', 208, 0, 3),
(2385, 'C', '长泰县', '长泰县CHANGTAIXIAN', 208, 0, 3),
(2386, 'L', '龙文区', '龙文区LONGWENQU', 208, 0, 3),
(2387, 'L', '龙海市', '龙海市LONGHAISHI', 208, 0, 3),
(2388, 'G', '光泽县', '光泽县GUANGZEXIAN', 209, 0, 3),
(2389, 'Y', '延平区', '延平区YANPINGQU', 209, 0, 3),
(2390, 'J', '建瓯市', '建瓯市JIANOUSHI', 209, 0, 3),
(2391, 'J', '建阳市', '建阳市JIANYANGSHI', 209, 0, 3),
(2392, 'Z', '政和县', '政和县ZHENGHEXIAN', 209, 0, 3),
(2393, 'S', '松溪县', '松溪县SONGXIXIAN', 209, 0, 3),
(2394, 'W', '武夷山市', '武夷山市WUYISHANSHI', 209, 0, 3),
(2395, 'P', '浦城县', '浦城县PUCHENGXIAN', 209, 0, 3),
(2396, 'S', '邵武市', '邵武市SHAOWUSHI', 209, 0, 3),
(2397, 'S', '顺昌县', '顺昌县SHUNCHANGXIAN', 209, 0, 3),
(2398, 'S', '上杭县', '上杭县SHANGHANGXIAN', 210, 0, 3),
(2399, 'X', '新罗区', '新罗区XINLUOQU', 210, 0, 3),
(2400, 'W', '武平县', '武平县WUPINGXIAN', 210, 0, 3),
(2401, 'Y', '永定县', '永定县YONGDINGXIAN', 210, 0, 3),
(2402, 'Z', '漳平市', '漳平市ZHANGPINGSHI', 210, 0, 3),
(2403, 'L', '连城县', '连城县LIANCHENGXIAN', 210, 0, 3),
(2404, 'C', '长汀县', '长汀县CHANGTINGXIAN', 210, 0, 3),
(2405, 'G', '古田县', '古田县GUTIANXIAN', 211, 0, 3),
(2406, 'Z', '周宁县', '周宁县ZHOUNINGXIAN', 211, 0, 3),
(2407, 'S', '寿宁县', '寿宁县SHOUNINGXIAN', 211, 0, 3),
(2408, 'P', '屏南县', '屏南县PINGNANXIAN', 211, 0, 3),
(2409, 'Z', '柘荣县', '柘荣县ZHERONGXIAN', 211, 0, 3),
(2410, 'F', '福安市', '福安市FUANSHI', 211, 0, 3),
(2411, 'F', '福鼎市', '福鼎市FUDINGSHI', 211, 0, 3),
(2412, 'J', '蕉城区', '蕉城区JIAOCHENGQU', 211, 0, 3),
(2413, 'X', '霞浦县', '霞浦县XIAPUXIAN', 211, 0, 3),
(2414, 'D', '东湖区', '东湖区DONGHUQU', 212, 0, 3),
(2415, 'N', '南昌县', '南昌县NANCHANGXIAN', 212, 0, 3),
(2416, 'A', '安义县', '安义县ANYIXIAN', 212, 0, 3),
(2417, 'X', '新建县', '新建县XINJIANXIAN', 212, 0, 3),
(2418, 'W', '湾里区', '湾里区WANLIQU', 212, 0, 3),
(2419, 'X', '西湖区', '西湖区XIHUQU', 212, 0, 3),
(2420, 'J', '进贤县', '进贤县JINXIANXIAN', 212, 0, 3),
(2421, 'Q', '青云谱区', '青云谱区QINGYUNPUQU', 212, 0, 3),
(2422, 'Q', '青山湖区', '青山湖区QINGSHANHUQU', 212, 0, 3),
(2423, 'L', '乐平市', '乐平市LEPINGSHI', 213, 0, 3),
(2424, 'C', '昌江区', '昌江区CHANGJIANGQU', 213, 0, 3),
(2425, 'F', '浮梁县', '浮梁县FULIANGXIAN', 213, 0, 3),
(2426, 'Z', '珠山区', '珠山区ZHUSHANQU', 213, 0, 3),
(2427, 'S', '上栗县', '上栗县SHANGLIXIAN', 214, 0, 3),
(2428, 'A', '安源区', '安源区ANYUANQU', 214, 0, 3),
(2429, 'X', '湘东区', '湘东区XIANGDONGQU', 214, 0, 3),
(2430, 'L', '芦溪县', '芦溪县LUXIXIAN', 214, 0, 3),
(2431, 'L', '莲花县', '莲花县LIANHUAXIAN', 214, 0, 3),
(2432, 'J', '九江县', '九江县JIUJIANGXIAN', 215, 0, 3),
(2433, 'X', '修水县', '修水县XIUSHUIXIAN', 215, 0, 3),
(2434, 'L', '庐山区', '庐山区LUSHANQU', 215, 0, 3),
(2435, 'P', '彭泽县', '彭泽县PENGZEXIAN', 215, 0, 3),
(2436, 'D', '德安县', '德安县DEANXIAN', 215, 0, 3),
(2437, 'X', '星子县', '星子县XINGZIXIAN', 215, 0, 3),
(2438, 'W', '武宁县', '武宁县WUNINGXIAN', 215, 0, 3),
(2439, 'Y', '永修县', '永修县YONGXIUXIAN', 215, 0, 3),
(2440, 'X', '浔阳区', '浔阳区XUNYANGQU', 215, 0, 3),
(2441, 'H', '湖口县', '湖口县HUKOUXIAN', 215, 0, 3),
(2442, 'R', '瑞昌市', '瑞昌市RUICHANGSHI', 215, 0, 3),
(2443, 'D', '都昌县', '都昌县DUCHANGXIAN', 215, 0, 3),
(2444, 'F', '分宜县', '分宜县FENYIXIAN', 216, 0, 3),
(2445, 'Y', '渝水区', '渝水区YUSHUIQU', 216, 0, 3),
(2446, 'Y', '余江县', '余江县YUJIANGXIAN', 217, 0, 3),
(2447, 'Y', '月湖区', '月湖区YUEHUQU', 217, 0, 3),
(2448, 'G', '贵溪市', '贵溪市GUIXISHI', 217, 0, 3),
(2449, 'S', '上犹县', '上犹县SHANGYOUXIAN', 218, 0, 3),
(2450, 'Y', '于都县', '于都县YUDUXIAN', 218, 0, 3),
(2451, 'H', '会昌县', '会昌县HUICHANGXIAN', 218, 0, 3),
(2452, 'X', '信丰县', '信丰县XINFENGXIAN', 218, 0, 3),
(2453, 'Q', '全南县', '全南县QUANNANXIAN', 218, 0, 3),
(2454, 'X', '兴国县', '兴国县XINGGUOXIAN', 218, 0, 3),
(2455, 'N', '南康市', '南康市NANKANGSHI', 218, 0, 3),
(2456, 'D', '大余县', '大余县DAYUXIAN', 218, 0, 3),
(2457, 'N', '宁都县', '宁都县NINGDUXIAN', 218, 0, 3),
(2458, 'A', '安远县', '安远县ANYUANXIAN', 218, 0, 3),
(2459, 'D', '定南县', '定南县DINGNANXIAN', 218, 0, 3),
(2460, 'X', '寻乌县', '寻乌县XUNWUXIAN', 218, 0, 3),
(2461, 'C', '崇义县', '崇义县CHONGYIXIAN', 218, 0, 3),
(2462, 'R', '瑞金市', '瑞金市RUIJINSHI', 218, 0, 3),
(2463, 'S', '石城县', '石城县SHICHENGXIAN', 218, 0, 3),
(2464, 'Z', '章贡区', '章贡区ZHANGGONGQU', 218, 0, 3),
(2465, 'G', '赣县', '赣县GANXIAN', 218, 0, 3),
(2466, 'L', '龙南县', '龙南县LONGNANXIAN', 218, 0, 3),
(2467, 'W', '万安县', '万安县WANANXIAN', 219, 0, 3),
(2468, 'J', '井冈山市', '井冈山市JINGGANGSHANSHI', 219, 0, 3),
(2469, 'J', '吉安县', '吉安县JIANXIAN', 219, 0, 3),
(2470, 'J', '吉州区', '吉州区JIZHOUQU', 219, 0, 3),
(2471, 'J', '吉水县', '吉水县JISHUIXIAN', 219, 0, 3),
(2472, 'A', '安福县', '安福县ANFUXIAN', 219, 0, 3),
(2473, 'X', '峡江县', '峡江县XIAJIANGXIAN', 219, 0, 3),
(2474, 'X', '新干县', '新干县XINGANXIAN', 219, 0, 3),
(2475, 'Y', '永丰县', '永丰县YONGFENGXIAN', 219, 0, 3),
(2476, 'Y', '永新县', '永新县YONGXINXIAN', 219, 0, 3),
(2477, 'T', '泰和县', '泰和县TAIHEXIAN', 219, 0, 3),
(2478, 'S', '遂川县', '遂川县SUICHUANXIAN', 219, 0, 3),
(2479, 'Q', '青原区', '青原区QINGYUANQU', 219, 0, 3),
(2480, 'W', '万载县', '万载县WANZAIXIAN', 220, 0, 3),
(2481, 'S', '上高县', '上高县SHANGGAOXIAN', 220, 0, 3),
(2482, 'F', '丰城市', '丰城市FENGCHENGSHI', 220, 0, 3),
(2483, 'F', '奉新县', '奉新县FENGXINXIAN', 220, 0, 3),
(2484, 'Y', '宜丰县', '宜丰县YIFENGXIAN', 220, 0, 3),
(2485, 'Z', '樟树市', '樟树市ZHANGSHUSHI', 220, 0, 3),
(2486, 'Y', '袁州区', '袁州区YUANZHOUQU', 220, 0, 3),
(2487, 'T', '铜鼓县', '铜鼓县TONGGUXIAN', 220, 0, 3),
(2488, 'J', '靖安县', '靖安县JINGANXIAN', 220, 0, 3),
(2489, 'G', '高安市', '高安市GAOANSHI', 220, 0, 3),
(2490, 'D', '东乡县', '东乡县DONGXIANGXIAN', 221, 0, 3),
(2491, 'L', '临川区', '临川区LINCHUANQU', 221, 0, 3),
(2492, 'L', '乐安县', '乐安县LEANXIAN', 221, 0, 3),
(2493, 'N', '南丰县', '南丰县NANFENGXIAN', 221, 0, 3),
(2494, 'N', '南城县', '南城县NANCHENGXIAN', 221, 0, 3),
(2495, 'Y', '宜黄县', '宜黄县YIHUANGXIAN', 221, 0, 3),
(2496, 'C', '崇仁县', '崇仁县CHONGRENXIAN', 221, 0, 3),
(2497, 'G', '广昌县', '广昌县GUANGCHANGXIAN', 221, 0, 3),
(2498, 'Z', '资溪县', '资溪县ZIXIXIAN', 221, 0, 3),
(2499, 'J', '金溪县', '金溪县JINXIXIAN', 221, 0, 3),
(2500, 'L', '黎川县', '黎川县LICHUANXIAN', 221, 0, 3),
(2501, 'W', '万年县', '万年县WANNIANXIAN', 222, 0, 3),
(2502, 'S', '上饶县', '上饶县SHANGRAOXIAN', 222, 0, 3),
(2503, 'Y', '余干县', '余干县YUGANXIAN', 222, 0, 3),
(2504, 'X', '信州区', '信州区XINZHOUQU', 222, 0, 3),
(2505, 'W', '婺源县', '婺源县WUYUANXIAN', 222, 0, 3),
(2506, 'G', '广丰县', '广丰县GUANGFENGXIAN', 222, 0, 3),
(2507, 'Y', '弋阳县', '弋阳县YIYANGXIAN', 222, 0, 3),
(2508, 'D', '德兴市', '德兴市DEXINGSHI', 222, 0, 3),
(2509, 'H', '横峰县', '横峰县HENGFENGXIAN', 222, 0, 3),
(2510, 'Y', '玉山县', '玉山县YUSHANXIAN', 222, 0, 3),
(2511, 'P', '鄱阳县', '鄱阳县POYANGXIAN', 222, 0, 3),
(2512, 'Y', '铅山县', '铅山县YANSHANXIAN', 222, 0, 3),
(2513, 'L', '历下区', '历下区LIXIAQU', 223, 0, 3),
(2514, 'L', '历城区', '历城区LICHENGQU', 223, 0, 3),
(2515, 'S', '商河县', '商河县SHANGHEXIAN', 223, 0, 3),
(2516, 'T', '天桥区', '天桥区TIANQIAOQU', 223, 0, 3),
(2517, 'S', '市中区', '市中区SHIZHONGQU', 223, 0, 3),
(2518, 'P', '平阴县', '平阴县PINGYINXIAN', 223, 0, 3),
(2519, 'H', '槐荫区', '槐荫区HUAIYINQU', 223, 0, 3),
(2520, 'J', '济阳县', '济阳县JIYANGXIAN', 223, 0, 3),
(2521, 'Z', '章丘市', '章丘市ZHANGQIUSHI', 223, 0, 3),
(2522, 'C', '长清区', '长清区CHANGQINGQU', 223, 0, 3),
(2523, 'J', '即墨市', '即墨市JIMOSHI', 224, 0, 3),
(2524, 'S', '四方区', '四方区SIFANGQU', 224, 0, 3),
(2525, 'C', '城阳区', '城阳区CHENGYANGQU', 224, 0, 3),
(2526, 'L', '崂山区', '崂山区LAOSHANQU', 224, 0, 3),
(2527, 'S', '市北区', '市北区SHIBEIQU', 224, 0, 3),
(2528, 'S', '市南区', '市南区SHINANQU', 224, 0, 3),
(2529, 'P', '平度市', '平度市PINGDUSHI', 224, 0, 3),
(2530, 'L', '李沧区', '李沧区LICANGQU', 224, 0, 3),
(2531, 'J', '胶南市', '胶南市JIAONANSHI', 224, 0, 3),
(2532, 'J', '胶州市', '胶州市JIAOZHOUSHI', 224, 0, 3),
(2533, 'L', '莱西市', '莱西市LAIXISHI', 224, 0, 3),
(2534, 'H', '黄岛区', '黄岛区HUANGDAOQU', 224, 0, 3),
(2535, 'L', '临淄区', '临淄区LINZIQU', 225, 0, 3),
(2536, 'B', '博山区', '博山区BOSHANQU', 225, 0, 3),
(2537, 'Z', '周村区', '周村区ZHOUCUNQU', 225, 0, 3),
(2538, 'Z', '张店区', '张店区ZHANGDIANQU', 225, 0, 3),
(2539, 'H', '桓台县', '桓台县HUANTAIXIAN', 225, 0, 3),
(2540, 'Y', '沂源县', '沂源县YIYUANXIAN', 225, 0, 3),
(2541, 'Z', '淄川区', '淄川区ZICHUANQU', 225, 0, 3),
(2542, 'G', '高青县', '高青县GAOQINGXIAN', 225, 0, 3),
(2543, 'T', '台儿庄区', '台儿庄区TAIERZHUANGQU', 226, 0, 3),
(2544, 'S', '山亭区', '山亭区SHANTINGQU', 226, 0, 3),
(2545, 'Y', '峄城区', '峄城区YICHENGQU', 226, 0, 3),
(2546, 'S', '市中区', '市中区SHIZHONGQU', 226, 0, 3),
(2547, 'T', '滕州市', '滕州市TENGZHOUSHI', 226, 0, 3),
(2548, 'X', '薛城区', '薛城区XUECHENGQU', 226, 0, 3),
(2549, 'D', '东营区', '东营区DONGYINGQU', 227, 0, 3),
(2550, 'L', '利津县', '利津县LIJINXIAN', 227, 0, 3),
(2551, 'K', '垦利县', '垦利县KENLIXIAN', 227, 0, 3),
(2552, 'G', '广饶县', '广饶县GUANGRAOXIAN', 227, 0, 3),
(2553, 'H', '河口区', '河口区HEKOUQU', 227, 0, 3),
(2554, 'Z', '招远市', '招远市ZHAOYUANSHI', 228, 0, 3),
(2555, 'X', '栖霞市', '栖霞市XIXIASHI', 228, 0, 3),
(2556, 'H', '海阳市', '海阳市HAIYANGSHI', 228, 0, 3),
(2557, 'M', '牟平区', '牟平区MUPINGQU', 228, 0, 3),
(2558, 'F', '福山区', '福山区FUSHANQU', 228, 0, 3),
(2559, 'Z', '芝罘区', '芝罘区ZHIFUQU', 228, 0, 3),
(2560, 'L', '莱山区', '莱山区LAISHANQU', 228, 0, 3),
(2561, 'L', '莱州市', '莱州市LAIZHOUSHI', 228, 0, 3),
(2562, 'L', '莱阳市', '莱阳市LAIYANGSHI', 228, 0, 3),
(2563, 'P', '蓬莱市', '蓬莱市PENGLAISHI', 228, 0, 3),
(2564, 'C', '长岛县', '长岛县CHANGDAOXIAN', 228, 0, 3),
(2565, 'L', '龙口市', '龙口市LONGKOUSHI', 228, 0, 3),
(2566, 'L', '临朐县', '临朐县LINQUXIAN', 229, 0, 3),
(2567, 'F', '坊子区', '坊子区FANGZIQU', 229, 0, 3),
(2568, 'K', '奎文区', '奎文区KUIWENQU', 229, 0, 3),
(2569, 'A', '安丘市', '安丘市ANQIUSHI', 229, 0, 3),
(2570, 'H', '寒亭区', '寒亭区HANTINGQU', 229, 0, 3),
(2571, 'S', '寿光市', '寿光市SHOUGUANGSHI', 229, 0, 3),
(2572, 'C', '昌乐县', '昌乐县CHANGLEXIAN', 229, 0, 3),
(2573, 'C', '昌邑市', '昌邑市CHANGYISHI', 229, 0, 3),
(2574, 'W', '潍城区', '潍城区WEICHENGQU', 229, 0, 3),
(2575, 'Z', '诸城市', '诸城市ZHUCHENGSHI', 229, 0, 3),
(2576, 'Q', '青州市', '青州市QINGZHOUSHI', 229, 0, 3),
(2577, 'G', '高密市', '高密市GAOMISHI', 229, 0, 3),
(2578, 'R', '任城区', '任城区RENCHENGQU', 230, 0, 3),
(2579, 'Y', '兖州市', '兖州市YANZHOUSHI', 230, 0, 3),
(2580, 'J', '嘉祥县', '嘉祥县JIAXIANGXIAN', 230, 0, 3),
(2581, 'S', '市中区', '市中区SHIZHONGQU', 230, 0, 3),
(2582, 'W', '微山县', '微山县WEISHANXIAN', 230, 0, 3),
(2583, 'Q', '曲阜市', '曲阜市QUFUSHI', 230, 0, 3),
(2584, 'L', '梁山县', '梁山县LIANGSHANXIAN', 230, 0, 3),
(2585, 'W', '汶上县', '汶上县WENSHANGXIAN', 230, 0, 3),
(2586, 'S', '泗水县', '泗水县SISHUIXIAN', 230, 0, 3),
(2587, 'Z', '邹城市', '邹城市ZOUCHENGSHI', 230, 0, 3),
(2588, 'J', '金乡县', '金乡县JINXIANGXIAN', 230, 0, 3),
(2589, 'Y', '鱼台县', '鱼台县YUTAIXIAN', 230, 0, 3),
(2590, 'D', '东平县', '东平县DONGPINGXIAN', 231, 0, 3),
(2591, 'N', '宁阳县', '宁阳县NINGYANGXIAN', 231, 0, 3),
(2592, 'D', '岱岳区', '岱岳区DAIYUEQU', 231, 0, 3),
(2593, 'X', '新泰市', '新泰市XINTAISHI', 231, 0, 3),
(2594, 'T', '泰山区', '泰山区TAISHANQU', 231, 0, 3),
(2595, 'F', '肥城市', '肥城市FEICHENGSHI', 231, 0, 3),
(2596, 'R', '乳山市', '乳山市RUSHANSHI', 232, 0, 3),
(2597, 'W', '文登市', '文登市WENDENGSHI', 232, 0, 3),
(2598, 'H', '环翠区', '环翠区HUANCUIQU', 232, 0, 3),
(2599, 'R', '荣成市', '荣成市RONGCHENGSHI', 232, 0, 3),
(2600, 'D', '东港区', '东港区DONGGANGQU', 233, 0, 3),
(2601, 'W', '五莲县', '五莲县WULIANXIAN', 233, 0, 3),
(2602, 'L', '岚山区', '岚山区LANSHANQU', 233, 0, 3),
(2603, 'J', '莒县', '莒县JUXIAN', 233, 0, 3),
(2604, 'L', '莱城区', '莱城区LAICHENGQU', 234, 0, 3),
(2605, 'G', '钢城区', '钢城区GANGCHENGQU', 234, 0, 3),
(2606, 'L', '临沭县', '临沭县LINSHUXIAN', 235, 0, 3),
(2607, 'L', '兰山区', '兰山区LANSHANQU', 235, 0, 3),
(2608, 'P', '平邑县', '平邑县PINGYIXIAN', 235, 0, 3),
(2609, 'Y', '沂南县', '沂南县YINANXIAN', 235, 0, 3),
(2610, 'Y', '沂水县', '沂水县YISHUIXIAN', 235, 0, 3),
(2611, 'H', '河东区', '河东区HEDONGQU', 235, 0, 3),
(2612, 'L', '罗庄区', '罗庄区LUOZHUANGQU', 235, 0, 3),
(2613, 'C', '苍山县', '苍山县CANGSHANXIAN', 235, 0, 3),
(2614, 'J', '莒南县', '莒南县JUNANXIAN', 235, 0, 3),
(2615, 'M', '蒙阴县', '蒙阴县MENGYINXIAN', 235, 0, 3),
(2616, 'F', '费县', '费县FEIXIAN', 235, 0, 3),
(2617, 'T', '郯城县', '郯城县TANCHENGXIAN', 235, 0, 3),
(2618, 'L', '临邑县', '临邑县LINYIXIAN', 236, 0, 3),
(2619, 'L', '乐陵市', '乐陵市LELINGSHI', 236, 0, 3),
(2620, 'X', '夏津县', '夏津县XIAJINXIAN', 236, 0, 3),
(2621, 'N', '宁津县', '宁津县NINGJINXIAN', 236, 0, 3),
(2622, 'P', '平原县', '平原县PINGYUANXIAN', 236, 0, 3),
(2623, 'Q', '庆云县', '庆云县QINGYUNXIAN', 236, 0, 3),
(2624, 'D', '德城区', '德城区DECHENGQU', 236, 0, 3),
(2625, 'W', '武城县', '武城县WUCHENGXIAN', 236, 0, 3),
(2626, 'Y', '禹城市', '禹城市YUCHENGSHI', 236, 0, 3),
(2627, 'L', '陵县', '陵县LINGXIAN', 236, 0, 3),
(2628, 'Q', '齐河县', '齐河县QIHEXIAN', 236, 0, 3),
(2629, 'D', '东昌府区', '东昌府区DONGCHANGFUQU', 237, 0, 3),
(2630, 'D', '东阿县', '东阿县DONGEXIAN', 237, 0, 3),
(2631, 'L', '临清市', '临清市LINQINGSHI', 237, 0, 3),
(2632, 'G', '冠县', '冠县GUANXIAN', 237, 0, 3),
(2633, 'C', '茌平县', '茌平县CHIPINGXIAN', 237, 0, 3),
(2634, 'S', '莘县', '莘县SHENXIAN', 237, 0, 3),
(2635, 'Y', '阳谷县', '阳谷县YANGGUXIAN', 237, 0, 3),
(2636, 'G', '高唐县', '高唐县GAOTANGXIAN', 237, 0, 3),
(2637, 'B', '博兴县', '博兴县BOXINGXIAN', 238, 0, 3),
(2638, 'H', '惠民县', '惠民县HUIMINXIAN', 238, 0, 3),
(2639, 'W', '无棣县', '无棣县WUDIXIAN', 238, 0, 3),
(2640, 'Z', '沾化县', '沾化县ZHANHUAXIAN', 238, 0, 3),
(2641, 'B', '滨城区', '滨城区BINCHENGQU', 238, 0, 3),
(2642, 'Z', '邹平县', '邹平县ZOUPINGXIAN', 238, 0, 3),
(2643, 'Y', '阳信县', '阳信县YANGXINXIAN', 238, 0, 3),
(2644, 'D', '东明县', '东明县DONGMINGXIAN', 239, 0, 3),
(2645, 'S', '单县', '单县SHANXIAN', 239, 0, 3),
(2646, 'D', '定陶县', '定陶县DINGTAOXIAN', 239, 0, 3),
(2647, 'J', '巨野县', '巨野县JUYEXIAN', 239, 0, 3),
(2648, 'C', '成武县', '成武县CHENGWUXIAN', 239, 0, 3),
(2649, 'C', '曹县', '曹县CAOXIAN', 239, 0, 3),
(2650, 'M', '牡丹区', '牡丹区MUDANQU', 239, 0, 3),
(2651, 'Y', '郓城县', '郓城县YUNCHENGXIAN', 239, 0, 3),
(2652, 'J', '鄄城县', '鄄城县JUANCHENGXIAN', 239, 0, 3),
(2653, 'S', '上街区', '上街区SHANGJIEQU', 240, 5, 3),
(2654, 'Z', '中原区', '中原区ZHONGYUANQU', 240, 1, 3),
(2655, 'Z', '中牟县', '中牟县ZHONGMUXIAN', 240, 7, 3),
(2656, 'E', '二七区', '二七区ERQIQU', 240, 2, 3),
(2657, 'G', '巩义市', '巩义市GONGYISHI', 240, 8, 3),
(2658, 'H', '惠济区', '惠济区HUIJIQU', 240, 6, 3),
(2659, 'X', '新密市', '新密市XINMISHI', 240, 10, 3),
(2660, 'X', '新郑市', '新郑市XINZHENGSHI', 240, 11, 3),
(2661, 'D', '登封市', '登封市DENGFENGSHI', 240, 12, 3),
(2662, 'G', '管城回族区', '管城回族区GUANCHENGHUIZUQU', 240, 3, 3),
(2663, 'X', '荥阳市', '荥阳市XINGYANGSHI', 240, 9, 3),
(2664, 'J', '金水区', '金水区JINSHUIQU', 240, 4, 3),
(2665, 'L', '兰考县', '兰考县LANKAOXIAN', 241, 0, 3),
(2666, 'W', '尉氏县', '尉氏县WEISHIXIAN', 241, 0, 3),
(2667, 'K', '开封县', '开封县KAIFENGXIAN', 241, 0, 3),
(2668, 'Q', '杞县', '杞县QIXIAN', 241, 0, 3),
(2669, 'Y', '禹王台区', '禹王台区YUWANGTAIQU', 241, 0, 3),
(2670, 'T', '通许县', '通许县TONGXUXIAN', 241, 0, 3),
(2671, 'J', '金明区', '金明区JINMINGQU', 241, 0, 3),
(2672, 'S', '顺河回族区', '顺河回族区SHUNHEHUIZUQU', 241, 0, 3),
(2673, 'G', '鼓楼区', '鼓楼区GULOUQU', 241, 0, 3),
(2674, 'L', '龙亭区', '龙亭区LONGTINGQU', 241, 0, 3),
(2675, 'Y', '伊川县', '伊川县YICHUANXIAN', 242, 0, 3),
(2676, 'Y', '偃师市', '偃师市YANSHISHI', 242, 0, 3),
(2677, 'J', '吉利区', '吉利区JILIQU', 242, 0, 3),
(2678, 'M', '孟津县', '孟津县MENGJINXIAN', 242, 0, 3),
(2679, 'Y', '宜阳县', '宜阳县YIYANGXIAN', 242, 0, 3),
(2680, 'S', '嵩县', '嵩县SONGXIAN', 242, 0, 3),
(2681, 'X', '新安县', '新安县XINANXIAN', 242, 0, 3),
(2682, 'L', '栾川县', '栾川县LUANCHUANXIAN', 242, 0, 3),
(2683, 'R', '汝阳县', '汝阳县RUYANGXIAN', 242, 0, 3),
(2684, 'L', '洛宁县', '洛宁县LUONINGXIAN', 242, 0, 3),
(2685, 'L', '洛龙区', '洛龙区LUOLONGQU', 242, 0, 3),
(2686, 'J', '涧西区', '涧西区JIANXIQU', 242, 0, 3),
(2687, 'C', '瀍河回族区', '瀍河回族区CHANHEHUIZUQU', 242, 0, 3),
(2688, 'L', '老城区', '老城区LAOCHENGQU', 242, 0, 3),
(2689, 'X', '西工区', '西工区XIGONGQU', 242, 0, 3),
(2690, 'W', '卫东区', '卫东区WEIDONGQU', 243, 0, 3),
(2691, 'Y', '叶县', '叶县YEXIAN', 243, 0, 3),
(2692, 'B', '宝丰县', '宝丰县BAOFENGXIAN', 243, 0, 3),
(2693, 'X', '新华区', '新华区XINHUAQU', 243, 0, 3),
(2694, 'R', '汝州市', '汝州市RUZHOUSHI', 243, 0, 3),
(2695, 'Z', '湛河区', '湛河区ZHANHEQU', 243, 0, 3),
(2696, 'S', '石龙区', '石龙区SHILONGQU', 243, 0, 3),
(2697, 'W', '舞钢市', '舞钢市WUGANGSHI', 243, 0, 3),
(2698, 'J', '郏县', '郏县JIAXIAN', 243, 0, 3),
(2699, 'L', '鲁山县', '鲁山县LUSHANXIAN', 243, 0, 3),
(2700, 'N', '内黄县', '内黄县NEIHUANGXIAN', 244, 0, 3),
(2701, 'B', '北关区', '北关区BEIGUANQU', 244, 0, 3),
(2702, 'A', '安阳县', '安阳县ANYANGXIAN', 244, 0, 3),
(2703, 'W', '文峰区', '文峰区WENFENGQU', 244, 0, 3),
(2704, 'L', '林州市', '林州市LINZHOUSHI', 244, 0, 3),
(2705, 'Y', '殷都区', '殷都区YINDUQU', 244, 0, 3),
(2706, 'T', '汤阴县', '汤阴县TANGYINXIAN', 244, 0, 3),
(2707, 'H', '滑县', '滑县HUAXIAN', 244, 0, 3),
(2708, 'L', '龙安区', '龙安区LONGANQU', 244, 0, 3),
(2709, 'S', '山城区', '山城区SHANCHENGQU', 245, 0, 3),
(2710, 'J', '浚县', '浚县JUNXIAN', 245, 0, 3),
(2711, 'Q', '淇县', '淇县QIXIAN', 245, 0, 3),
(2712, 'Q', '淇滨区', '淇滨区QIBINQU', 245, 0, 3),
(2713, 'H', '鹤山区', '鹤山区HESHANQU', 245, 0, 3),
(2714, 'F', '凤泉区', '凤泉区FENGQUANQU', 246, 3, 3),
(2715, 'W', '卫滨区', '卫滨区WEIBINQU', 246, 2, 3),
(2716, 'W', '卫辉市', '卫辉市WEIHUISHI', 246, 11, 3),
(2717, 'Y', '原阳县', '原阳县YUANYANGXIAN', 246, 7, 3),
(2718, 'F', '封丘县', '封丘县FENGQIUXIAN', 246, 9, 3),
(2719, 'Y', '延津县', '延津县YANJINXIAN', 246, 8, 3),
(2720, 'X', '新乡县', '新乡县XINXIANGXIAN', 246, 5, 3),
(2721, 'M', '牧野区', '牧野区MUYEQU', 246, 4, 3),
(2722, 'H', '红旗区', '红旗区HONGQIQU', 246, 1, 3),
(2723, 'H', '获嘉县', '获嘉县HUOJIAXIAN', 246, 6, 3),
(2724, 'H', '辉县市', '辉县市HUIXIANSHI', 246, 12, 3),
(2725, 'C', '长垣市', '长垣市CHANGYUANSHI', 246, 10, 3),
(2726, 'Z', '中站区', '中站区ZHONGZHANQU', 247, 0, 3),
(2727, 'X', '修武县', '修武县XIUWUXIAN', 247, 0, 3),
(2728, 'B', '博爱县', '博爱县BOAIXIAN', 247, 0, 3),
(2729, 'M', '孟州市', '孟州市MENGZHOUSHI', 247, 0, 3),
(2730, 'S', '山阳区', '山阳区SHANYANGQU', 247, 0, 3),
(2731, 'W', '武陟县', '武陟县WUZHIXIAN', 247, 0, 3),
(2732, 'Q', '沁阳市', '沁阳市QINYANGSHI', 247, 0, 3),
(2733, 'W', '温县', '温县WENXIAN', 247, 0, 3),
(2734, 'J', '解放区', '解放区JIEFANGQU', 247, 0, 3),
(2735, 'M', '马村区', '马村区MACUNQU', 247, 0, 3),
(2736, 'H', '华龙区', '华龙区HUALONGQU', 248, 0, 3),
(2737, 'N', '南乐县', '南乐县NANLEXIAN', 248, 0, 3),
(2738, 'T', '台前县', '台前县TAIQIANXIAN', 248, 0, 3),
(2739, 'Q', '清丰县', '清丰县QINGFENGXIAN', 248, 0, 3),
(2740, 'P', '濮阳县', '濮阳县PUYANGXIAN', 248, 0, 3),
(2741, 'F', '范县', '范县FANXIAN', 248, 0, 3),
(2742, 'Y', '禹州市', '禹州市YUZHOUSHI', 249, 0, 3),
(2743, 'X', '襄城县', '襄城县XIANGCHENGXIAN', 249, 0, 3),
(2744, 'X', '许昌县', '许昌县XUCHANGXIAN', 249, 0, 3),
(2745, 'Y', '鄢陵县', '鄢陵县YANLINGXIAN', 249, 0, 3),
(2746, 'C', '长葛市', '长葛市CHANGGESHI', 249, 0, 3),
(2747, 'W', '魏都区', '魏都区WEIDUQU', 249, 0, 3),
(2748, 'L', '临颍县', '临颍县LINYINGXIAN', 250, 0, 3),
(2749, 'S', '召陵区', '召陵区SHAOLINGQU', 250, 0, 3),
(2750, 'Y', '源汇区', '源汇区YUANHUIQU', 250, 0, 3),
(2751, 'W', '舞阳县', '舞阳县WUYANGXIAN', 250, 0, 3),
(2752, 'Y', '郾城区', '郾城区YANCHENGQU', 250, 0, 3),
(2753, 'Y', '义马市', '义马市YIMASHI', 251, 0, 3),
(2754, 'L', '卢氏县', '卢氏县LUSHIXIAN', 251, 0, 3),
(2755, 'M', '渑池县', '渑池县MIANCHIXIAN', 251, 0, 3),
(2756, 'H', '湖滨区', '湖滨区HUBINQU', 251, 0, 3),
(2757, 'L', '灵宝市', '灵宝市LINGBAOSHI', 251, 0, 3),
(2758, 'S', '陕县', '陕县SHANXIAN', 251, 0, 3),
(2759, 'N', '内乡县', '内乡县NEIXIANGXIAN', 252, 0, 3),
(2760, 'N', '南召县', '南召县NANZHAOXIAN', 252, 0, 3),
(2761, 'W', '卧龙区', '卧龙区WOLONGQU', 252, 0, 3),
(2762, 'T', '唐河县', '唐河县TANGHEXIAN', 252, 0, 3),
(2763, 'W', '宛城区', '宛城区WANCHENGQU', 252, 0, 3),
(2764, 'X', '新野县', '新野县XINYEXIAN', 252, 0, 3),
(2765, 'F', '方城县', '方城县FANGCHENGXIAN', 252, 0, 3),
(2766, 'T', '桐柏县', '桐柏县TONGBAIXIAN', 252, 0, 3),
(2767, 'X', '淅川县', '淅川县XICHUANXIAN', 252, 0, 3),
(2768, 'S', '社旗县', '社旗县SHEQIXIAN', 252, 0, 3),
(2769, 'X', '西峡县', '西峡县XIXIAXIAN', 252, 0, 3),
(2770, 'D', '邓州市', '邓州市DENGZHOUSHI', 252, 0, 3),
(2771, 'Z', '镇平县', '镇平县ZHENPINGXIAN', 252, 0, 3),
(2772, 'X', '夏邑县', '夏邑县XIAYIXIAN', 253, 0, 3),
(2773, 'N', '宁陵县', '宁陵县NINGLINGXIAN', 253, 0, 3),
(2774, 'Z', '柘城县', '柘城县ZHECHENGXIAN', 253, 0, 3),
(2775, 'M', '民权县', '民权县MINQUANXIAN', 253, 0, 3),
(2776, 'Y', '永城市', '永城市YONGCHENGSHI', 253, 0, 3),
(2777, 'S', '睢县', '睢县SUIXIAN', 253, 0, 3),
(2778, 'S', '睢阳区', '睢阳区SUIYANGQU', 253, 0, 3),
(2779, 'L', '粱园区', '粱园区LIANGYUANQU', 253, 0, 3),
(2780, 'Y', '虞城县', '虞城县YUCHENGXIAN', 253, 0, 3),
(2781, 'G', '光山县', '光山县GUANGSHANXIAN', 254, 0, 3),
(2782, 'S', '商城县', '商城县SHANGCHENGXIAN', 254, 0, 3),
(2783, 'G', '固始县', '固始县GUSHIXIAN', 254, 0, 3),
(2784, 'P', '平桥区', '平桥区PINGQIAOQU', 254, 0, 3),
(2785, 'X', '息县', '息县XIXIAN', 254, 0, 3),
(2786, 'X', '新县', '新县XINXIAN', 254, 0, 3),
(2787, 'S', '浉河区', '浉河区SHIHEQU', 254, 0, 3),
(2788, 'H', '淮滨县', '淮滨县HUAIBINXIAN', 254, 0, 3),
(2789, 'H', '潢川县', '潢川县HUANGCHUANXIAN', 254, 0, 3),
(2790, 'L', '罗山县', '罗山县LUOSHANXIAN', 254, 0, 3),
(2791, 'S', '商水县', '商水县SHANGSHUIXIAN', 255, 0, 3),
(2792, 'T', '太康县', '太康县TAIKANGXIAN', 255, 0, 3),
(2793, 'C', '川汇区', '川汇区CHUANHUIQU', 255, 0, 3),
(2794, 'F', '扶沟县', '扶沟县FUGOUXIAN', 255, 0, 3),
(2795, 'S', '沈丘县', '沈丘县SHENQIUXIAN', 255, 0, 3),
(2796, 'H', '淮阳县', '淮阳县HUAIYANGXIAN', 255, 0, 3),
(2797, 'X', '西华县', '西华县XIHUAXIAN', 255, 0, 3),
(2798, 'D', '郸城县', '郸城县DANCHENGXIAN', 255, 0, 3),
(2799, 'X', '项城市', '项城市XIANGCHENGSHI', 255, 0, 3),
(2800, 'L', '鹿邑县', '鹿邑县LUYIXIAN', 255, 0, 3),
(2801, 'S', '上蔡县', '上蔡县SHANGCAIXIAN', 256, 0, 3),
(2802, 'P', '平舆县', '平舆县PINGYUXIAN', 256, 0, 3),
(2803, 'X', '新蔡县', '新蔡县XINCAIXIAN', 256, 0, 3),
(2804, 'Z', '正阳县', '正阳县ZHENGYANGXIAN', 256, 0, 3),
(2805, 'R', '汝南县', '汝南县RUNANXIAN', 256, 0, 3),
(2806, 'B', '泌阳县', '泌阳县BIYANGXIAN', 256, 0, 3),
(2807, 'Q', '确山县', '确山县QUESHANXIAN', 256, 0, 3),
(2808, 'X', '西平县', '西平县XIPINGXIAN', 256, 0, 3),
(2809, 'S', '遂平县', '遂平县SUIPINGXIAN', 256, 0, 3),
(2810, 'Y', '驿城区', '驿城区YICHENGQU', 256, 0, 3),
(2811, 'J', '济源市', '济源市JIYUANSHI', 257, 0, 3),
(2812, 'D', '东西湖区', '东西湖区DONGXIHUQU', 258, 0, 3),
(2813, 'X', '新洲区', '新洲区XINZHOUQU', 258, 0, 3),
(2814, 'W', '武昌区', '武昌区WUCHANGQU', 258, 0, 3),
(2815, 'H', '汉南区', '汉南区HANNANQU', 258, 0, 3),
(2816, 'H', '汉阳区', '汉阳区HANYANGQU', 258, 0, 3),
(2817, 'J', '江夏区', '江夏区JIANGXIAQU', 258, 0, 3),
(2818, 'J', '江岸区', '江岸区JIANGANQU', 258, 0, 3),
(2819, 'J', '江汉区', '江汉区JIANGHANQU', 258, 0, 3),
(2820, 'H', '洪山区', '洪山区HONGSHANQU', 258, 0, 3),
(2821, 'Q', '硚口区', '硚口区QIAOKOUQU', 258, 0, 3),
(2822, 'C', '蔡甸区', '蔡甸区CAIDIANQU', 258, 0, 3),
(2823, 'Q', '青山区', '青山区QINGSHANQU', 258, 0, 3),
(2824, 'H', '黄陂区', '黄陂区HUANGPIQU', 258, 0, 3),
(2825, 'X', '下陆区', '下陆区XIALUQU', 259, 0, 3),
(2826, 'D', '大冶市', '大冶市DAYESHI', 259, 0, 3),
(2827, 'X', '西塞山区', '西塞山区XISAISHANQU', 259, 0, 3),
(2828, 'T', '铁山区', '铁山区TIESHANQU', 259, 0, 3),
(2829, 'Y', '阳新县', '阳新县YANGXINXIAN', 259, 0, 3),
(2830, 'H', '黄石港区', '黄石港区HUANGSHIGANGQU', 259, 0, 3),
(2831, 'D', '丹江口市', '丹江口市DANJIANGKOUSHI', 260, 0, 3),
(2832, 'Z', '张湾区', '张湾区ZHANGWANQU', 260, 0, 3),
(2833, 'F', '房县', '房县FANGXIAN', 260, 0, 3),
(2834, 'Z', '竹山县', '竹山县ZHUSHANXIAN', 260, 0, 3),
(2835, 'Z', '竹溪县', '竹溪县ZHUXIXIAN', 260, 0, 3),
(2836, 'M', '茅箭区', '茅箭区MAOJIANQU', 260, 0, 3),
(2837, 'Y', '郧县', '郧县YUNXIAN', 260, 0, 3),
(2838, 'Y', '郧西县', '郧西县YUNXIXIAN', 260, 0, 3),
(2839, 'W', '五峰土家族自治县', '五峰土家族自治县WUFENGTUJIAZUZIZHIXIAN', 261, 0, 3),
(2840, 'W', '伍家岗区', '伍家岗区WUJIAGANGQU', 261, 0, 3),
(2841, 'X', '兴山县', '兴山县XINGSHANXIAN', 261, 0, 3),
(2842, 'Y', '夷陵区', '夷陵区YILINGQU', 261, 0, 3),
(2843, 'Y', '宜都市', '宜都市YIDUSHI', 261, 0, 3),
(2844, 'D', '当阳市', '当阳市DANGYANGSHI', 261, 0, 3),
(2845, 'Z', '枝江市', '枝江市ZHIJIANGSHI', 261, 0, 3),
(2846, 'D', '点军区', '点军区DIANJUNQU', 261, 0, 3),
(2847, 'Z', '秭归县', '秭归县ZIGUIXIAN', 261, 0, 3),
(2848, 'X', '猇亭区', '猇亭区XIAOTINGQU', 261, 0, 3),
(2849, 'X', '西陵区', '西陵区XILINGQU', 261, 0, 3),
(2850, 'Y', '远安县', '远安县YUANANXIAN', 261, 0, 3),
(2851, 'C', '长阳土家族自治县', '长阳土家族自治县CHANGYANGTUJIAZUZIZHIXIAN', 261, 0, 3),
(2852, 'B', '保康县', '保康县BAOKANGXIAN', 262, 0, 3),
(2853, 'N', '南漳县', '南漳县NANZHANGXIAN', 262, 0, 3),
(2854, 'Y', '宜城市', '宜城市YICHENGSHI', 262, 0, 3),
(2855, 'Z', '枣阳市', '枣阳市ZAOYANGSHI', 262, 0, 3),
(2856, 'F', '樊城区', '樊城区FANCHENGQU', 262, 0, 3),
(2857, 'L', '老河口市', '老河口市LAOHEKOUSHI', 262, 0, 3),
(2858, 'X', '襄城区', '襄城区XIANGCHENGQU', 262, 0, 3),
(2859, 'X', '襄阳区', '襄阳区XIANGYANGQU', 262, 0, 3),
(2860, 'G', '谷城县', '谷城县GUCHENGXIAN', 262, 0, 3),
(2861, 'H', '华容区', '华容区HUARONGQU', 263, 0, 3),
(2862, 'L', '粱子湖', '粱子湖LIANGZIHU', 263, 0, 3),
(2863, 'E', '鄂城区', '鄂城区ECHENGQU', 263, 0, 3),
(2864, 'D', '东宝区', '东宝区DONGBAOQU', 264, 0, 3),
(2865, 'J', '京山县', '京山县JINGSHANXIAN', 264, 0, 3),
(2866, 'D', '掇刀区', '掇刀区DUODAOQU', 264, 0, 3),
(2867, 'S', '沙洋县', '沙洋县SHAYANGXIAN', 264, 0, 3),
(2868, 'Z', '钟祥市', '钟祥市ZHONGXIANGSHI', 264, 0, 3),
(2869, 'Y', '云梦县', '云梦县YUNMENGXIAN', 265, 0, 3),
(2870, 'D', '大悟县', '大悟县DAWUXIAN', 265, 0, 3),
(2871, 'X', '孝南区', '孝南区XIAONANQU', 265, 0, 3),
(2872, 'X', '孝昌县', '孝昌县XIAOCHANGXIAN', 265, 0, 3),
(2873, 'A', '安陆市', '安陆市ANLUSHI', 265, 0, 3),
(2874, 'Y', '应城市', '应城市YINGCHENGSHI', 265, 0, 3),
(2875, 'H', '汉川市', '汉川市HANCHUANSHI', 265, 0, 3),
(2876, 'G', '公安县', '公安县GONGANXIAN', 266, 0, 3),
(2877, 'S', '松滋市', '松滋市SONGZISHI', 266, 0, 3),
(2878, 'J', '江陵县', '江陵县JIANGLINGXIAN', 266, 0, 3),
(2879, 'S', '沙市区', '沙市区SHASHIQU', 266, 0, 3),
(2880, 'H', '洪湖市', '洪湖市HONGHUSHI', 266, 0, 3),
(2881, 'J', '监利县', '监利县JIANLIXIAN', 266, 0, 3),
(2882, 'S', '石首市', '石首市SHISHOUSHI', 266, 0, 3),
(2883, 'J', '荆州区', '荆州区JINGZHOUQU', 266, 0, 3),
(2884, 'T', '团风县', '团风县TUANFENGXIAN', 267, 0, 3),
(2885, 'W', '武穴市', '武穴市WUXUESHI', 267, 0, 3),
(2886, 'X', '浠水县', '浠水县XISHUIXIAN', 267, 0, 3),
(2887, 'H', '红安县', '红安县HONGANXIAN', 267, 0, 3),
(2888, 'L', '罗田县', '罗田县LUOTIANXIAN', 267, 0, 3),
(2889, 'Y', '英山县', '英山县YINGSHANXIAN', 267, 0, 3),
(2890, 'Q', '蕲春县', '蕲春县QICHUNXIAN', 267, 0, 3),
(2891, 'M', '麻城市', '麻城市MACHENGSHI', 267, 0, 3),
(2892, 'H', '黄州区', '黄州区HUANGZHOUQU', 267, 0, 3),
(2893, 'H', '黄梅县', '黄梅县HUANGMEIXIAN', 267, 0, 3),
(2894, 'X', '咸安区', '咸安区XIANANQU', 268, 0, 3),
(2895, 'J', '嘉鱼县', '嘉鱼县JIAYUXIAN', 268, 0, 3),
(2896, 'C', '崇阳县', '崇阳县CHONGYANGXIAN', 268, 0, 3),
(2897, 'C', '赤壁市', '赤壁市CHIBISHI', 268, 0, 3),
(2898, 'T', '通城县', '通城县TONGCHENGXIAN', 268, 0, 3),
(2899, 'T', '通山县', '通山县TONGSHANXIAN', 268, 0, 3),
(2900, 'G', '广水市', '广水市GUANGSHUISHI', 269, 0, 3),
(2901, 'Z', '曾都区', '曾都区ZENGDUQU', 269, 0, 3),
(2902, 'L', '利川市', '利川市LICHUANSHI', 270, 0, 3),
(2903, 'X', '咸丰县', '咸丰县XIANFENGXIAN', 270, 0, 3),
(2904, 'X', '宣恩县', '宣恩县XUANENXIAN', 270, 0, 3),
(2905, 'B', '巴东县', '巴东县BADONGXIAN', 270, 0, 3),
(2906, 'J', '建始县', '建始县JIANSHIXIAN', 270, 0, 3),
(2907, 'E', '恩施市', '恩施市ENSHISHI', 270, 0, 3),
(2908, 'L', '来凤县', '来凤县LAIFENGXIAN', 270, 0, 3),
(2909, 'H', '鹤峰县', '鹤峰县HEFENGXIAN', 270, 0, 3),
(2910, 'X', '仙桃市', '仙桃市XIANTAOSHI', 271, 0, 3),
(2911, 'Q', '潜江市', '潜江市QIANJIANGSHI', 272, 0, 3),
(2912, 'T', '天门市', '天门市TIANMENSHI', 273, 0, 3),
(2913, 'S', '神农架林区', '神农架林区SHENNONGJIALINQU', 274, 0, 3),
(2914, 'T', '天心区', '天心区TIANXINQU', 275, 0, 3),
(2915, 'N', '宁乡县', '宁乡县NINGXIANGXIAN', 275, 0, 3),
(2916, 'Y', '岳麓区', '岳麓区YUELUQU', 275, 0, 3),
(2917, 'K', '开福区', '开福区KAIFUQU', 275, 0, 3),
(2918, 'W', '望城县', '望城县WANGCHENGXIAN', 275, 0, 3),
(2919, 'L', '浏阳市', '浏阳市LIUYANGSHI', 275, 0, 3),
(2920, 'F', '芙蓉区', '芙蓉区FURONGQU', 275, 0, 3),
(2921, 'C', '长沙县', '长沙县CHANGSHAXIAN', 275, 0, 3),
(2922, 'Y', '雨花区', '雨花区YUHUAQU', 275, 0, 3),
(2923, 'T', '天元区', '天元区TIANYUANQU', 276, 0, 3),
(2924, 'Y', '攸县', '攸县YOUXIAN', 276, 0, 3),
(2925, 'Z', '株洲县', '株洲县ZHUZHOUXIAN', 276, 0, 3),
(2926, 'Y', '炎陵县', '炎陵县YANLINGXIAN', 276, 0, 3),
(2927, 'S', '石峰区', '石峰区SHIFENGQU', 276, 0, 3),
(2928, 'L', '芦淞区', '芦淞区LUSONGQU', 276, 0, 3),
(2929, 'C', '茶陵县', '茶陵县CHALINGXIAN', 276, 0, 3),
(2930, 'H', '荷塘区', '荷塘区HETANGQU', 276, 0, 3),
(2931, 'L', '醴陵市', '醴陵市LILINGSHI', 276, 0, 3),
(2932, 'Y', '岳塘区', '岳塘区YUETANGQU', 277, 0, 3),
(2933, 'X', '湘乡市', '湘乡市XIANGXIANGSHI', 277, 0, 3),
(2934, 'X', '湘潭县', '湘潭县XIANGTANXIAN', 277, 0, 3),
(2935, 'Y', '雨湖区', '雨湖区YUHUQU', 277, 0, 3),
(2936, 'S', '韶山市', '韶山市SHAOSHANSHI', 277, 0, 3),
(2937, 'N', '南岳区', '南岳区NANYUEQU', 278, 0, 3),
(2938, 'C', '常宁市', '常宁市CHANGNINGSHI', 278, 0, 3),
(2939, 'Z', '珠晖区', '珠晖区ZHUHUIQU', 278, 0, 3),
(2940, 'D', '石鼓区', '石鼓区DANGUQU', 278, 0, 3),
(2941, 'Q', '祁东县', '祁东县QIDONGXIAN', 278, 0, 3),
(2942, 'L', '耒阳市', '耒阳市LEIYANGSHI', 278, 0, 3),
(2943, 'Z', '蒸湘区', '蒸湘区ZHENGXIANGQU', 278, 0, 3),
(2944, 'H', '衡东县', '衡东县HENGDONGXIAN', 278, 0, 3),
(2945, 'H', '衡南县', '衡南县HENGNANXIAN', 278, 0, 3),
(2946, 'H', '衡山县', '衡山县HENGSHANXIAN', 278, 0, 3),
(2947, 'H', '衡阳县', '衡阳县HENGYANGXIAN', 278, 0, 3),
(2948, 'Y', '雁峰区', '雁峰区YANFENGQU', 278, 0, 3),
(2949, 'B', '北塔区', '北塔区BEITAQU', 279, 0, 3),
(2950, 'S', '双清区', '双清区SHUANGQINGQU', 279, 0, 3),
(2951, 'C', '城步苗族自治县', '城步苗族自治县CHENGBUMIAOZUZIZHIXIAN', 279, 0, 3),
(2952, 'D', '大祥区', '大祥区DAXIANGQU', 279, 0, 3),
(2953, 'X', '新宁县', '新宁县XINNINGXIAN', 279, 0, 3),
(2954, 'X', '新邵县', '新邵县XINSHAOXIAN', 279, 0, 3),
(2955, 'W', '武冈市', '武冈市WUGANGSHI', 279, 0, 3),
(2956, 'D', '洞口县', '洞口县DONGKOUXIAN', 279, 0, 3),
(2957, 'S', '绥宁县', '绥宁县SUININGXIAN', 279, 0, 3),
(2958, 'S', '邵东县', '邵东县SHAODONGXIAN', 279, 0, 3),
(2959, 'S', '邵阳县', '邵阳县SHAOYANGXIAN', 279, 0, 3),
(2960, 'L', '隆回县', '隆回县LONGHUIXIAN', 279, 0, 3),
(2961, 'L', '临湘市', '临湘市LINXIANGSHI', 280, 0, 3),
(2962, 'Y', '云溪区', '云溪区YUNXIQU', 280, 0, 3),
(2963, 'H', '华容县', '华容县HUARONGXIAN', 280, 0, 3),
(2964, 'J', '君山区', '君山区JUNSHANQU', 280, 0, 3),
(2965, 'Y', '岳阳县', '岳阳县YUEYANGXIAN', 280, 0, 3),
(2966, 'Y', '岳阳楼区', '岳阳楼区YUEYANGLOUQU', 280, 0, 3),
(2967, 'P', '平江县', '平江县PINGJIANGXIAN', 280, 0, 3),
(2968, 'M', '汨罗市', '汨罗市MILUOSHI', 280, 0, 3);
INSERT INTO `ims_xm_mallv3_area` (`id`, `letter`, `area_name`, `keyword`, `area_parent_id`, `area_sort`, `area_deep`) VALUES
(2969, 'X', '湘阴县', '湘阴县XIANGYINXIAN', 280, 0, 3),
(2970, 'L', '临澧县', '临澧县LINLIXIAN', 281, 0, 3),
(2971, 'A', '安乡县', '安乡县ANXIANGXIAN', 281, 0, 3),
(2972, 'T', '桃源县', '桃源县TAOYUANXIAN', 281, 0, 3),
(2973, 'W', '武陵区', '武陵区WULINGQU', 281, 0, 3),
(2974, 'H', '汉寿县', '汉寿县HANSHOUXIAN', 281, 0, 3),
(2975, 'J', '津市市', '津市市JINSHISHI', 281, 0, 3),
(2976, 'L', '澧县', '澧县LIXIAN', 281, 0, 3),
(2977, 'S', '石门县', '石门县SHIMENXIAN', 281, 0, 3),
(2978, 'D', '鼎城区', '鼎城区DINGCHENGQU', 281, 0, 3),
(2979, 'C', '慈利县', '慈利县CILIXIAN', 282, 0, 3),
(2980, 'S', '桑植县', '桑植县SANGZHIXIAN', 282, 0, 3),
(2981, 'W', '武陵源区', '武陵源区WULINGYUANQU', 282, 0, 3),
(2982, 'Y', '永定区', '永定区YONGDINGQU', 282, 0, 3),
(2983, 'N', '南县', '南县NANXIAN', 283, 0, 3),
(2984, 'A', '安化县', '安化县ANHUAXIAN', 283, 0, 3),
(2985, 'T', '桃江县', '桃江县TAOJIANGXIAN', 283, 0, 3),
(2986, 'Y', '沅江市', '沅江市YUANJIANGSHI', 283, 0, 3),
(2987, 'Z', '资阳区', '资阳区ZIYANGQU', 283, 0, 3),
(2988, 'H', '赫山区', '赫山区HESHANQU', 283, 0, 3),
(2989, 'L', '临武县', '临武县LINWUXIAN', 284, 0, 3),
(2990, 'B', '北湖区', '北湖区BEIHUQU', 284, 0, 3),
(2991, 'J', '嘉禾县', '嘉禾县JIAHEXIAN', 284, 0, 3),
(2992, 'A', '安仁县', '安仁县ANRENXIAN', 284, 0, 3),
(2993, 'Y', '宜章县', '宜章县YIZHANGXIAN', 284, 0, 3),
(2994, 'G', '桂东县', '桂东县GUIDONGXIAN', 284, 0, 3),
(2995, 'G', '桂阳县', '桂阳县GUIYANGXIAN', 284, 0, 3),
(2996, 'Y', '永兴县', '永兴县YONGXINGXIAN', 284, 0, 3),
(2997, 'R', '汝城县', '汝城县RUCHENGXIAN', 284, 0, 3),
(2998, 'S', '苏仙区', '苏仙区SUXIANQU', 284, 0, 3),
(2999, 'Z', '资兴市', '资兴市ZIXINGSHI', 284, 0, 3),
(3000, 'D', '东安县', '东安县DONGANXIAN', 285, 0, 3),
(3001, 'L', '冷水滩区', '冷水滩区LENGSHUITANQU', 285, 0, 3),
(3002, 'S', '双牌县', '双牌县SHUANGPAIXIAN', 285, 0, 3),
(3003, 'N', '宁远县', '宁远县NINGYUANXIAN', 285, 0, 3),
(3004, 'X', '新田县', '新田县XINTIANXIAN', 285, 0, 3),
(3005, 'J', '江华瑶族自治县', '江华瑶族自治县JIANGHUAYAOZUZIZHIXIAN', 285, 0, 3),
(3006, 'J', '江永县', '江永县JIANGYONGXIAN', 285, 0, 3),
(3007, 'Q', '祁阳县', '祁阳县QIYANGXIAN', 285, 0, 3),
(3008, 'L', '蓝山县', '蓝山县LANSHANXIAN', 285, 0, 3),
(3009, 'D', '道县', '道县DAOXIAN', 285, 0, 3),
(3010, 'L', '零陵区', '零陵区LINGLINGQU', 285, 0, 3),
(3011, 'Z', '中方县', '中方县ZHONGFANGXIAN', 286, 0, 3),
(3012, 'H', '会同县', '会同县HUITONGXIAN', 286, 0, 3),
(3013, 'X', '新晃侗族自治县', '新晃侗族自治县XINHUANGDONGZUZIZHIXIAN', 286, 0, 3),
(3014, 'Y', '沅陵县', '沅陵县YUANLINGXIAN', 286, 0, 3),
(3015, 'H', '洪江市/洪江区', '洪江市/洪江区HONGJIANGSHIHONGJIANGQU', 286, 0, 3),
(3016, 'X', '溆浦县', '溆浦县XUPUXIAN', 286, 0, 3),
(3017, 'Z', '芷江侗族自治县', '芷江侗族自治县ZHIJIANGDONGZUZIZHIXIAN', 286, 0, 3),
(3018, 'C', '辰溪县', '辰溪县CHENXIXIAN', 286, 0, 3),
(3019, 'T', '通道侗族自治县', '通道侗族自治县TONGDAODONGZUZIZHIXIAN', 286, 0, 3),
(3020, 'J', '靖州苗族侗族自治县', '靖州苗族侗族自治县JINGZHOUMIAOZUDONGZUZIZHIXIAN', 286, 0, 3),
(3021, 'H', '鹤城区', '鹤城区HECHENGQU', 286, 0, 3),
(3022, 'M', '麻阳苗族自治县', '麻阳苗族自治县MAYANGMIAOZUZIZHIXIAN', 286, 0, 3),
(3023, 'L', '冷水江市', '冷水江市LENGSHUIJIANGSHI', 287, 0, 3),
(3024, 'S', '双峰县', '双峰县SHUANGFENGXIAN', 287, 0, 3),
(3025, 'L', '娄星区', '娄星区LOUXINGQU', 287, 0, 3),
(3026, 'X', '新化县', '新化县XINHUAXIAN', 287, 0, 3),
(3027, 'L', '涟源市', '涟源市LIANYUANSHI', 287, 0, 3),
(3028, 'B', '保靖县', '保靖县BAOJINGXIAN', 288, 0, 3),
(3029, 'F', '凤凰县', '凤凰县FENGHUANGXIAN', 288, 0, 3),
(3030, 'G', '古丈县', '古丈县GUZHANGXIAN', 288, 0, 3),
(3031, 'J', '吉首市', '吉首市JISHOUSHI', 288, 0, 3),
(3032, 'Y', '永顺县', '永顺县YONGSHUNXIAN', 288, 0, 3),
(3033, 'L', '泸溪县', '泸溪县LUXIXIAN', 288, 0, 3),
(3034, 'H', '花垣县', '花垣县HUAYUANXIAN', 288, 0, 3),
(3035, 'L', '龙山县', '龙山县LONGSHANXIAN', 288, 0, 3),
(3036, 'L', '萝岗区', '萝岗区LUOGANGQU', 289, 0, 3),
(3037, 'N', '南沙区', '南沙区NANSHAQU', 289, 0, 3),
(3038, 'C', '从化市', '从化市CONGHUASHI', 289, 0, 3),
(3039, 'Z', '增城市', '增城市ZENGCHENGSHI', 289, 0, 3),
(3040, 'T', '天河区', '天河区TIANHEQU', 289, 0, 3),
(3041, 'H', '海珠区', '海珠区HAIZHUQU', 289, 0, 3),
(3042, 'P', '番禺区', '番禺区PANYUQU', 289, 0, 3),
(3043, 'B', '白云区', '白云区BAIYUNQU', 289, 0, 3),
(3044, 'H', '花都区', '花都区HUADUQU', 289, 0, 3),
(3045, 'L', '荔湾区', '荔湾区LIWANQU', 289, 0, 3),
(3046, 'Y', '越秀区', '越秀区YUEXIUQU', 289, 0, 3),
(3047, 'H', '黄埔区', '黄埔区HUANGPUQU', 289, 0, 3),
(3048, 'L', '乐昌市', '乐昌市LECHANGSHI', 290, 0, 3),
(3049, 'R', '乳源瑶族自治县', '乳源瑶族自治县RUYUANYAOZUZIZHIXIAN', 290, 0, 3),
(3050, 'R', '仁化县', '仁化县RENHUAXIAN', 290, 0, 3),
(3051, 'N', '南雄市', '南雄市NANXIONGSHI', 290, 0, 3),
(3052, 'S', '始兴县', '始兴县SHIXINGXIAN', 290, 0, 3),
(3053, 'X', '新丰县', '新丰县XINFENGXIAN', 290, 0, 3),
(3054, 'Q', '曲江区', '曲江区QUJIANGQU', 290, 0, 3),
(3055, 'W', '武江区', '武江区WUJIANGQU', 290, 0, 3),
(3056, 'Z', '浈江区', '浈江区ZHENJIANGQU', 290, 0, 3),
(3057, 'W', '翁源县', '翁源县WENGYUANXIAN', 290, 0, 3),
(3058, 'N', '南山区', '南山区NANSHANQU', 291, 0, 3),
(3059, 'B', '宝安区', '宝安区BAOANQU', 291, 0, 3),
(3060, 'Y', '盐田区', '盐田区YANTIANQU', 291, 0, 3),
(3061, 'F', '福田区', '福田区FUTIANQU', 291, 0, 3),
(3062, 'L', '罗湖区', '罗湖区LUOHUQU', 291, 0, 3),
(3063, 'L', '龙岗区', '龙岗区LONGGANGQU', 291, 0, 3),
(3064, 'D', '斗门区', '斗门区DOUMENQU', 292, 0, 3),
(3065, 'J', '金湾区', '金湾区JINWANQU', 292, 0, 3),
(3066, 'X', '香洲区', '香洲区XIANGZHOUQU', 292, 0, 3),
(3067, 'N', '南澳县', '南澳县NANAOXIAN', 293, 0, 3),
(3068, 'C', '潮南区', '潮南区CHAONANQU', 293, 0, 3),
(3069, 'C', '潮阳区', '潮阳区CHAOYANGQU', 293, 0, 3),
(3070, 'C', '澄海区', '澄海区CHENGHAIQU', 293, 0, 3),
(3071, 'H', '濠江区', '濠江区HAOJIANGQU', 293, 0, 3),
(3072, 'J', '金平区', '金平区JINPINGQU', 293, 0, 3),
(3073, 'L', '龙湖区', '龙湖区LONGHUQU', 293, 0, 3),
(3074, 'S', '三水区', '三水区SANSHUIQU', 294, 0, 3),
(3075, 'N', '南海区', '南海区NANHAIQU', 294, 0, 3),
(3076, 'C', '禅城区', '禅城区CHANCHENGQU', 294, 0, 3),
(3077, 'S', '顺德区', '顺德区SHUNDEQU', 294, 0, 3),
(3078, 'G', '高明区', '高明区GAOMINGQU', 294, 0, 3),
(3079, 'T', '台山市', '台山市TAISHANSHI', 295, 0, 3),
(3080, 'K', '开平市', '开平市KAIPINGSHI', 295, 0, 3),
(3081, 'E', '恩平市', '恩平市ENPINGSHI', 295, 0, 3),
(3082, 'X', '新会区', '新会区XINHUIQU', 295, 0, 3),
(3083, 'J', '江海区', '江海区JIANGHAIQU', 295, 0, 3),
(3084, 'P', '蓬江区', '蓬江区PENGJIANGQU', 295, 0, 3),
(3085, 'H', '鹤山市', '鹤山市HESHANSHI', 295, 0, 3),
(3086, 'W', '吴川市', '吴川市WUCHUANSHI', 296, 0, 3),
(3087, 'P', '坡头区', '坡头区POTOUQU', 296, 0, 3),
(3088, 'L', '廉江市', '廉江市LIANJIANGSHI', 296, 0, 3),
(3089, 'X', '徐闻县', '徐闻县XUWENXIAN', 296, 0, 3),
(3090, 'C', '赤坎区', '赤坎区CHIKANQU', 296, 0, 3),
(3091, 'S', '遂溪县', '遂溪县SUIXIXIAN', 296, 0, 3),
(3092, 'L', '雷州市', '雷州市LEIZHOUSHI', 296, 0, 3),
(3093, 'X', '霞山区', '霞山区XIASHANQU', 296, 0, 3),
(3094, 'M', '麻章区', '麻章区MAZHANGQU', 296, 0, 3),
(3095, 'X', '信宜市', '信宜市XINYISHI', 297, 0, 3),
(3096, 'H', '化州市', '化州市HUAZHOUSHI', 297, 0, 3),
(3097, 'D', '电白县', '电白县DIANBAIXIAN', 297, 0, 3),
(3098, 'M', '茂南区', '茂南区MAONANQU', 297, 0, 3),
(3099, 'M', '茂港区', '茂港区MAOGANGQU', 297, 0, 3),
(3100, 'G', '高州市', '高州市GAOZHOUSHI', 297, 0, 3),
(3101, 'S', '四会市', '四会市SIHUISHI', 298, 0, 3),
(3102, 'F', '封开县', '封开县FENGKAIXIAN', 298, 0, 3),
(3103, 'G', '广宁县', '广宁县GUANGNINGXIAN', 298, 0, 3),
(3104, 'D', '德庆县', '德庆县DEQINGXIAN', 298, 0, 3),
(3105, 'H', '怀集县', '怀集县HUAIJIXIAN', 298, 0, 3),
(3106, 'D', '端州区', '端州区DUANZHOUQU', 298, 0, 3),
(3107, 'G', '高要市', '高要市GAOYAOSHI', 298, 0, 3),
(3108, 'D', '鼎湖区', '鼎湖区DINGHUQU', 298, 0, 3),
(3109, 'B', '博罗县', '博罗县BOLUOXIAN', 299, 0, 3),
(3110, 'H', '惠东县', '惠东县HUIDONGXIAN', 299, 0, 3),
(3111, 'H', '惠城区', '惠城区HUICHENGQU', 299, 0, 3),
(3112, 'H', '惠阳区', '惠阳区HUIYANGQU', 299, 0, 3),
(3113, 'L', '龙门县', '龙门县LONGMENXIAN', 299, 0, 3),
(3114, 'F', '丰顺县', '丰顺县FENGSHUNXIAN', 300, 0, 3),
(3115, 'W', '五华县', '五华县WUHUAXIAN', 300, 0, 3),
(3116, 'X', '兴宁市', '兴宁市XINGNINGSHI', 300, 0, 3),
(3117, 'D', '大埔县', '大埔县DABUXIAN', 300, 0, 3),
(3118, 'P', '平远县', '平远县PINGYUANXIAN', 300, 0, 3),
(3119, 'M', '梅县', '梅县MEIXIAN', 300, 0, 3),
(3120, 'M', '梅江区', '梅江区MEIJIANGQU', 300, 0, 3),
(3121, 'J', '蕉岭县', '蕉岭县JIAOLINGXIAN', 300, 0, 3),
(3122, 'C', '城区', '城区CHENGQU', 301, 0, 3),
(3123, 'H', '海丰县', '海丰县HAIFENGXIAN', 301, 0, 3),
(3124, 'L', '陆丰市', '陆丰市LUFENGSHI', 301, 0, 3),
(3125, 'L', '陆河县', '陆河县LUHEXIAN', 301, 0, 3),
(3126, 'D', '东源县', '东源县DONGYUANXIAN', 302, 0, 3),
(3127, 'H', '和平县', '和平县HEPINGXIAN', 302, 0, 3),
(3128, 'Y', '源城区', '源城区YUANCHENGQU', 302, 0, 3),
(3129, 'Z', '紫金县', '紫金县ZIJINXIAN', 302, 0, 3),
(3130, 'L', '连平县', '连平县LIANPINGXIAN', 302, 0, 3),
(3131, 'L', '龙川县', '龙川县LONGCHUANXIAN', 302, 0, 3),
(3132, 'J', '江城区', '江城区JIANGCHENGQU', 303, 0, 3),
(3133, 'Y', '阳东县', '阳东县YANGDONGXIAN', 303, 0, 3),
(3134, 'Y', '阳春市', '阳春市YANGCHUNSHI', 303, 0, 3),
(3135, 'Y', '阳西县', '阳西县YANGXIXIAN', 303, 0, 3),
(3136, 'F', '佛冈县', '佛冈县FOGANGXIAN', 304, 0, 3),
(3137, 'Q', '清城区', '清城区QINGCHENGQU', 304, 0, 3),
(3138, 'Q', '清新县', '清新县QINGXINXIAN', 304, 0, 3),
(3139, 'Y', '英德市', '英德市YINGDESHI', 304, 0, 3),
(3140, 'L', '连南瑶族自治县', '连南瑶族自治县LIANNANYAOZUZIZHIXIAN', 304, 0, 3),
(3141, 'L', '连山壮族瑶族自治县', '连山壮族瑶族自治县LIANSHANZHUANGZUYAOZUZIZHIXIAN', 304, 0, 3),
(3142, 'L', '连州市', '连州市LIANZHOUSHI', 304, 0, 3),
(3143, 'Y', '阳山县', '阳山县YANGSHANXIAN', 304, 0, 3),
(3144, 'D', '东莞市', '东莞市DONGGUANSHI', 305, 0, 3),
(3145, 'Z', '中山市', '中山市ZHONGSHANSHI', 306, 0, 3),
(3146, 'X', '湘桥区', '湘桥区XIANGQIAOQU', 307, 0, 3),
(3147, 'C', '潮安县', '潮安县CHAOANXIAN', 307, 0, 3),
(3148, 'R', '饶平县', '饶平县RAOPINGXIAN', 307, 0, 3),
(3149, 'H', '惠来县', '惠来县HUILAIXIAN', 308, 0, 3),
(3150, 'J', '揭东县', '揭东县JIEDONGXIAN', 308, 0, 3),
(3151, 'J', '揭西县', '揭西县JIEXIXIAN', 308, 0, 3),
(3152, 'P', '普宁市', '普宁市PUNINGSHI', 308, 0, 3),
(3153, 'R', '榕城区', '榕城区RONGCHENGQU', 308, 0, 3),
(3154, 'Y', '云城区', '云城区YUNCHENGQU', 309, 0, 3),
(3155, 'Y', '云安县', '云安县YUNANXIAN', 309, 0, 3),
(3156, 'X', '新兴县', '新兴县XINXINGXIAN', 309, 0, 3),
(3157, 'L', '罗定市', '罗定市LUODINGSHI', 309, 0, 3),
(3158, 'Y', '郁南县', '郁南县YUNANXIAN', 309, 0, 3),
(3159, 'S', '上林县', '上林县SHANGLINXIAN', 310, 0, 3),
(3160, 'X', '兴宁区', '兴宁区XINGNINGQU', 310, 0, 3),
(3161, 'B', '宾阳县', '宾阳县BINYANGXIAN', 310, 0, 3),
(3162, 'H', '横县', '横县HENGXIAN', 310, 0, 3),
(3163, 'W', '武鸣县', '武鸣县WUMINGXIAN', 310, 0, 3),
(3164, 'J', '江南区', '江南区JIANGNANQU', 310, 0, 3),
(3165, 'L', '良庆区', '良庆区LIANGQINGQU', 310, 0, 3),
(3166, 'X', '西乡塘区', '西乡塘区XIXIANGTANGQU', 310, 0, 3),
(3167, 'Y', '邕宁区', '邕宁区YONGNINGQU', 310, 0, 3),
(3168, 'L', '隆安县', '隆安县LONGANXIAN', 310, 0, 3),
(3169, 'Q', '青秀区', '青秀区QINGXIUQU', 310, 0, 3),
(3170, 'M', '马山县', '马山县MASHANXIAN', 310, 0, 3),
(3171, 'S', '三江侗族自治县', '三江侗族自治县SANJIANGDONGZUZIZHIXIAN', 311, 0, 3),
(3172, 'C', '城中区', '城中区CHENGZHONGQU', 311, 0, 3),
(3173, 'L', '柳北区', '柳北区LIUBEIQU', 311, 0, 3),
(3174, 'L', '柳南区', '柳南区LIUNANQU', 311, 0, 3),
(3175, 'L', '柳城县', '柳城县LIUCHENGXIAN', 311, 0, 3),
(3176, 'L', '柳江县', '柳江县LIUJIANGXIAN', 311, 0, 3),
(3177, 'R', '融安县', '融安县RONGANXIAN', 311, 0, 3),
(3178, 'R', '融水苗族自治县', '融水苗族自治县RONGSHUIMIAOZUZIZHIXIAN', 311, 0, 3),
(3179, 'Y', '鱼峰区', '鱼峰区YUFENGQU', 311, 0, 3),
(3180, 'L', '鹿寨县', '鹿寨县LUZHAIXIAN', 311, 0, 3),
(3181, 'Q', '七星区', '七星区QIXINGQU', 312, 0, 3),
(3182, 'L', '临桂县', '临桂县LINGUIXIAN', 312, 0, 3),
(3183, 'Q', '全州县', '全州县QUANZHOUXIAN', 312, 0, 3),
(3184, 'X', '兴安县', '兴安县XINGANXIAN', 312, 0, 3),
(3185, 'D', '叠彩区', '叠彩区DIECAIQU', 312, 0, 3),
(3186, 'P', '平乐县', '平乐县PINGLEXIAN', 312, 0, 3),
(3187, 'G', '恭城瑶族自治县', '恭城瑶族自治县GONGCHENGYAOZUZIZHIXIAN', 312, 0, 3),
(3188, 'Y', '永福县', '永福县YONGFUXIAN', 312, 0, 3),
(3189, 'G', '灌阳县', '灌阳县GUANYANGXIAN', 312, 0, 3),
(3190, 'L', '灵川县', '灵川县LINGCHUANXIAN', 312, 0, 3),
(3191, 'X', '秀峰区', '秀峰区XIUFENGQU', 312, 0, 3),
(3192, 'L', '荔浦县', '荔浦县LIPUXIAN', 312, 0, 3),
(3193, 'X', '象山区', '象山区XIANGSHANQU', 312, 0, 3),
(3194, 'Z', '资源县', '资源县ZIYUANXIAN', 312, 0, 3),
(3195, 'Y', '阳朔县', '阳朔县YANGSHUOXIAN', 312, 0, 3),
(3196, 'Y', '雁山区', '雁山区YANSHANQU', 312, 0, 3),
(3197, 'L', '龙胜各族自治县', '龙胜各族自治县LONGSHENGGEZUZIZHIXIAN', 312, 0, 3),
(3198, 'W', '万秀区', '万秀区WANXIUQU', 313, 0, 3),
(3199, 'C', '岑溪市', '岑溪市CENXISHI', 313, 0, 3),
(3200, 'C', '苍梧县', '苍梧县CANGWUXIAN', 313, 0, 3),
(3201, 'M', '蒙山县', '蒙山县MENGSHANXIAN', 313, 0, 3),
(3202, 'T', '藤县', '藤县TENGXIAN', 313, 0, 3),
(3203, 'D', '蝶山区', '蝶山区DIESHANQU', 313, 0, 3),
(3204, 'C', '长洲区', '长洲区CHANGZHOUQU', 313, 0, 3),
(3205, 'H', '合浦县', '合浦县HEPUXIAN', 314, 0, 3),
(3206, 'H', '海城区', '海城区HAICHENGQU', 314, 0, 3),
(3207, 'T', '铁山港区', '铁山港区TIESHANGANGQU', 314, 0, 3),
(3208, 'Y', '银海区', '银海区YINHAIQU', 314, 0, 3),
(3209, 'S', '上思县', '上思县SHANGSIXIAN', 315, 0, 3),
(3210, 'D', '东兴市', '东兴市DONGXINGSHI', 315, 0, 3),
(3211, 'G', '港口区', '港口区GANGKOUQU', 315, 0, 3),
(3212, 'F', '防城区', '防城区FANGCHENGQU', 315, 0, 3),
(3213, 'P', '浦北县', '浦北县PUBEIXIAN', 316, 0, 3),
(3214, 'L', '灵山县', '灵山县LINGSHANXIAN', 316, 0, 3),
(3215, 'Q', '钦北区', '钦北区QINBEIQU', 316, 0, 3),
(3216, 'Q', '钦南区', '钦南区QINNANQU', 316, 0, 3),
(3217, 'P', '平南县', '平南县PINGNANXIAN', 317, 0, 3),
(3218, 'G', '桂平市', '桂平市GUIPINGSHI', 317, 0, 3),
(3219, 'G', '港北区', '港北区GANGBEIQU', 317, 0, 3),
(3220, 'G', '港南区', '港南区GANGNANQU', 317, 0, 3),
(3221, 'T', '覃塘区', '覃塘区TANTANGQU', 317, 0, 3),
(3222, 'X', '兴业县', '兴业县XINGYEXIAN', 318, 0, 3),
(3223, 'B', '北流市', '北流市BEILIUSHI', 318, 0, 3),
(3224, 'B', '博白县', '博白县BOBAIXIAN', 318, 0, 3),
(3225, 'R', '容县', '容县RONGXIAN', 318, 0, 3),
(3226, 'Y', '玉州区', '玉州区YUZHOUQU', 318, 0, 3),
(3227, 'L', '陆川县', '陆川县LUCHUANXIAN', 318, 0, 3),
(3228, 'L', '乐业县', '乐业县LEYEXIAN', 319, 0, 3),
(3229, 'L', '凌云县', '凌云县LINGYUNXIAN', 319, 0, 3),
(3230, 'Y', '右江区', '右江区YOUJIANGQU', 319, 0, 3),
(3231, 'P', '平果县', '平果县PINGGUOXIAN', 319, 0, 3),
(3232, 'D', '德保县', '德保县DEBAOXIAN', 319, 0, 3),
(3233, 'T', '田东县', '田东县TIANDONGXIAN', 319, 0, 3),
(3234, 'T', '田林县', '田林县TIANLINXIAN', 319, 0, 3),
(3235, 'T', '田阳县', '田阳县TIANYANGXIAN', 319, 0, 3),
(3236, 'X', '西林县', '西林县XILINXIAN', 319, 0, 3),
(3237, 'N', '那坡县', '那坡县NAPOXIAN', 319, 0, 3),
(3238, 'L', '隆林各族自治县', '隆林各族自治县LONGLINGEZUZIZHIXIAN', 319, 0, 3),
(3239, 'J', '靖西县', '靖西县JINGXIXIAN', 319, 0, 3),
(3240, 'B', '八步区', '八步区BABUQU', 320, 0, 3),
(3241, 'F', '富川瑶族自治县', '富川瑶族自治县FUCHUANYAOZUZIZHIXIAN', 320, 0, 3),
(3242, 'Z', '昭平县', '昭平县ZHAOPINGXIAN', 320, 0, 3),
(3243, 'Z', '钟山县', '钟山县ZHONGSHANXIAN', 320, 0, 3),
(3244, 'D', '东兰县', '东兰县DONGLANXIAN', 321, 0, 3),
(3245, 'F', '凤山县', '凤山县FENGSHANXIAN', 321, 0, 3),
(3246, 'N', '南丹县', '南丹县NANDANXIAN', 321, 0, 3),
(3247, 'D', '大化瑶族自治县', '大化瑶族自治县DAHUAYAOZUZIZHIXIAN', 321, 0, 3),
(3248, 'T', '天峨县', '天峨县TIANEXIAN', 321, 0, 3),
(3249, 'Y', '宜州市', '宜州市YIZHOUSHI', 321, 0, 3),
(3250, 'B', '巴马瑶族自治县', '巴马瑶族自治县BAMAYAOZUZIZHIXIAN', 321, 0, 3),
(3251, 'H', '环江毛南族自治县', '环江毛南族自治县HUANJIANGMAONANZUZIZHIXIAN', 321, 0, 3),
(3252, 'L', '罗城仫佬族自治县', '罗城仫佬族自治县LUOCHENGMULAOZUZIZHIXIAN', 321, 0, 3),
(3253, 'D', '都安瑶族自治县', '都安瑶族自治县DUANYAOZUZIZHIXIAN', 321, 0, 3),
(3254, 'J', '金城江区', '金城江区JINCHENGJIANGQU', 321, 0, 3),
(3255, 'X', '兴宾区', '兴宾区XINGBINQU', 322, 0, 3),
(3256, 'H', '合山市', '合山市HESHANSHI', 322, 0, 3),
(3257, 'X', '忻城县', '忻城县XINCHENGXIAN', 322, 0, 3),
(3258, 'W', '武宣县', '武宣县WUXUANXIAN', 322, 0, 3),
(3259, 'X', '象州县', '象州县XIANGZHOUXIAN', 322, 0, 3),
(3260, 'J', '金秀瑶族自治县', '金秀瑶族自治县JINXIUYAOZUZIZHIXIAN', 322, 0, 3),
(3261, 'P', '凭祥市', '凭祥市PINGXIANGSHI', 323, 0, 3),
(3262, 'D', '大新县', '大新县DAXINXIAN', 323, 0, 3),
(3263, 'T', '天等县', '天等县TIANDENGXIAN', 323, 0, 3),
(3264, 'N', '宁明县', '宁明县NINGMINGXIAN', 323, 0, 3),
(3265, 'F', '扶绥县', '扶绥县FUSUIXIAN', 323, 0, 3),
(3266, 'J', '江州区', '江州区JIANGZHOUQU', 323, 0, 3),
(3267, 'L', '龙州县', '龙州县LONGZHOUXIAN', 323, 0, 3),
(3268, 'Q', '琼山区', '琼山区QIONGSHANQU', 324, 0, 3),
(3269, 'X', '秀英区', '秀英区XIUYINGQU', 324, 0, 3),
(3270, 'M', '美兰区', '美兰区MEILANQU', 324, 0, 3),
(3271, 'L', '龙华区', '龙华区LONGHUAQU', 324, 0, 3),
(3272, 'S', '三亚市', '三亚市SANYASHI', 325, 0, 3),
(3273, 'W', '五指山市', '五指山市WUZHISHANSHI', 326, 0, 3),
(3274, 'Q', '琼海市', '琼海市QIONGHAISHI', 327, 0, 3),
(3275, 'D', '儋州市', '儋州市DANZHOUSHI', 328, 0, 3),
(3276, 'W', '文昌市', '文昌市WENCHANGSHI', 329, 0, 3),
(3277, 'W', '万宁市', '万宁市WANNINGSHI', 330, 0, 3),
(3278, 'D', '东方市', '东方市DONGFANGSHI', 331, 0, 3),
(3279, 'D', '定安县', '定安县DINGANXIAN', 332, 0, 3),
(3280, 'T', '屯昌县', '屯昌县TUNCHANGXIAN', 333, 0, 3),
(3281, 'C', '澄迈县', '澄迈县CHENGMAIXIAN', 334, 0, 3),
(3282, 'L', '临高县', '临高县LINGAOXIAN', 335, 0, 3),
(3283, 'B', '白沙黎族自治县', '白沙黎族自治县BAISHALIZUZIZHIXIAN', 336, 0, 3),
(3284, 'C', '昌江黎族自治县', '昌江黎族自治县CHANGJIANGLIZUZIZHIXIAN', 337, 0, 3),
(3285, 'L', '乐东黎族自治县', '乐东黎族自治县LEDONGLIZUZIZHIXIAN', 338, 0, 3),
(3286, 'L', '陵水黎族自治县', '陵水黎族自治县LINGSHUILIZUZIZHIXIAN', 339, 0, 3),
(3287, 'B', '保亭黎族苗族自治县', '保亭黎族苗族自治县BAOTINGLIZUMIAOZUZIZHIXIAN', 340, 0, 3),
(3288, 'Q', '琼中黎族苗族自治县', '琼中黎族苗族自治县QIONGZHONGLIZUMIAOZUZIZHIXIAN', 341, 0, 3),
(4209, 'S', '双流县', '双流县SHUANGLIUXIAN', 385, 0, 3),
(4210, 'D', '大邑县', '大邑县DAYIXIAN', 385, 0, 3),
(4211, 'C', '崇州市', '崇州市CHONGZHOUSHI', 385, 0, 3),
(4212, 'P', '彭州市', '彭州市PENGZHOUSHI', 385, 0, 3),
(4213, 'C', '成华区', '成华区CHENGHUAQU', 385, 0, 3),
(4214, 'X', '新津县', '新津县XINJINXIAN', 385, 0, 3),
(4215, 'X', '新都区', '新都区XINDUQU', 385, 0, 3),
(4216, 'W', '武侯区', '武侯区WUHOUQU', 385, 0, 3),
(4217, 'W', '温江区', '温江区WENJIANGQU', 385, 0, 3),
(4218, 'P', '蒲江县', '蒲江县PUJIANGXIAN', 385, 0, 3),
(4219, 'Q', '邛崃市', '邛崃市QIONGLAISHI', 385, 0, 3),
(4220, 'P', '郫县', '郫县PIXIAN', 385, 0, 3),
(4221, 'D', '都江堰市', '都江堰市DUJIANGYANSHI', 385, 0, 3),
(4222, 'J', '金堂县', '金堂县JINTANGXIAN', 385, 0, 3),
(4223, 'J', '金牛区', '金牛区JINNIUQU', 385, 0, 3),
(4224, 'J', '锦江区', '锦江区JINJIANGQU', 385, 0, 3),
(4225, 'Q', '青白江区', '青白江区QINGBAIJIANGQU', 385, 0, 3),
(4226, 'Q', '青羊区', '青羊区QINGYANGQU', 385, 0, 3),
(4227, 'L', '龙泉驿区', '龙泉驿区LONGQUANYIQU', 385, 0, 3),
(4228, 'D', '大安区', '大安区DAANQU', 386, 0, 3),
(4229, 'F', '富顺县', '富顺县FUSHUNXIAN', 386, 0, 3),
(4230, 'Y', '沿滩区', '沿滩区YANTANQU', 386, 0, 3),
(4231, 'Z', '自流井区', '自流井区ZILIUJINGQU', 386, 0, 3),
(4232, 'R', '荣县', '荣县RONGXIAN', 386, 0, 3),
(4233, 'G', '贡井区', '贡井区GONGJINGQU', 386, 0, 3),
(4234, 'D', '东区', '东区DONGQU', 387, 0, 3),
(4235, 'R', '仁和区', '仁和区RENHEQU', 387, 0, 3),
(4236, 'Y', '盐边县', '盐边县YANBIANXIAN', 387, 0, 3),
(4237, 'M', '米易县', '米易县MIYIXIAN', 387, 0, 3),
(4238, 'X', '西区', '西区XIQU', 387, 0, 3),
(4239, 'X', '叙永县', '叙永县XUYONGXIAN', 388, 0, 3),
(4240, 'G', '古蔺县', '古蔺县GULINXIAN', 388, 0, 3),
(4241, 'H', '合江县', '合江县HEJIANGXIAN', 388, 0, 3),
(4242, 'J', '江阳区', '江阳区JIANGYANGQU', 388, 0, 3),
(4243, 'L', '泸县', '泸县LUXIAN', 388, 0, 3),
(4244, 'N', '纳溪区', '纳溪区NAXIQU', 388, 0, 3),
(4245, 'L', '龙马潭区', '龙马潭区LONGMATANQU', 388, 0, 3),
(4246, 'Z', '中江县', '中江县ZHONGJIANGXIAN', 389, 0, 3),
(4247, 'S', '什邡市', '什邡市SHIFANGSHI', 389, 0, 3),
(4248, 'G', '广汉市', '广汉市GUANGHANSHI', 389, 0, 3),
(4249, 'J', '旌阳区', '旌阳区JINGYANGQU', 389, 0, 3),
(4250, 'M', '绵竹市', '绵竹市MIANZHUSHI', 389, 0, 3),
(4251, 'L', '罗江县', '罗江县LUOJIANGXIAN', 389, 0, 3),
(4252, 'S', '三台县', '三台县SANTAIXIAN', 390, 0, 3),
(4253, 'B', '北川羌族自治县', '北川羌族自治县BEICHUANQIANGZUZIZHIXIAN', 390, 0, 3),
(4254, 'A', '安县', '安县ANXIAN', 390, 0, 3),
(4255, 'P', '平武县', '平武县PINGWUXIAN', 390, 0, 3),
(4256, 'Z', '梓潼县', '梓潼县ZITONGXIAN', 390, 0, 3),
(4257, 'J', '江油市', '江油市JIANGYOUSHI', 390, 0, 3),
(4258, 'F', '涪城区', '涪城区FUCHENGQU', 390, 0, 3),
(4259, 'Y', '游仙区', '游仙区YOUXIANQU', 390, 0, 3),
(4260, 'Y', '盐亭县', '盐亭县YANTINGXIAN', 390, 0, 3),
(4261, 'Y', '元坝区', '元坝区YUANBAQU', 391, 0, 3),
(4262, 'L', '利州区', '利州区LIZHOUQU', 391, 0, 3),
(4263, 'J', '剑阁县', '剑阁县JIANGEXIAN', 391, 0, 3),
(4264, 'W', '旺苍县', '旺苍县WANGCANGXIAN', 391, 0, 3),
(4265, 'C', '朝天区', '朝天区CHAOTIANQU', 391, 0, 3),
(4266, 'C', '苍溪县', '苍溪县CANGXIXIAN', 391, 0, 3),
(4267, 'Q', '青川县', '青川县QINGCHUANXIAN', 391, 0, 3),
(4268, 'D', '大英县', '大英县DAYINGXIAN', 392, 0, 3),
(4269, 'A', '安居区', '安居区ANJUQU', 392, 0, 3),
(4270, 'S', '射洪县', '射洪县SHEHONGXIAN', 392, 0, 3),
(4271, 'C', '船山区', '船山区CHUANSHANQU', 392, 0, 3),
(4272, 'P', '蓬溪县', '蓬溪县PENGXIXIAN', 392, 0, 3),
(4273, 'D', '东兴区', '东兴区DONGXINGQU', 393, 0, 3),
(4274, 'W', '威远县', '威远县WEIYUANXIAN', 393, 0, 3),
(4275, 'S', '市中区', '市中区SHIZHONGQU', 393, 0, 3),
(4276, 'Z', '资中县', '资中县ZIZHONGXIAN', 393, 0, 3),
(4277, 'L', '隆昌县', '隆昌县LONGCHANGXIAN', 393, 0, 3),
(4278, 'W', '五通桥区', '五通桥区WUTONGQIAOQU', 394, 0, 3),
(4279, 'J', '井研县', '井研县JINGYANXIAN', 394, 0, 3),
(4280, 'J', '夹江县', '夹江县JIAJIANGXIAN', 394, 0, 3),
(4281, 'E', '峨眉山市', '峨眉山市EMEISHANSHI', 394, 0, 3),
(4282, 'E', '峨边彝族自治县', '峨边彝族自治县EBIANYIZUZIZHIXIAN', 394, 0, 3),
(4283, 'S', '市中区', '市中区SHIZHONGQU', 394, 0, 3),
(4284, 'M', '沐川县', '沐川县MUCHUANXIAN', 394, 0, 3),
(4285, 'S', '沙湾区', '沙湾区SHAWANQU', 394, 0, 3),
(4286, 'Q', '犍为县', '犍为县QIANWEIXIAN', 394, 0, 3),
(4287, 'J', '金口河区', '金口河区JINKOUHEQU', 394, 0, 3),
(4288, 'M', '马边彝族自治县', '马边彝族自治县MABIANYIZUZIZHIXIAN', 394, 0, 3),
(4289, 'Y', '仪陇县', '仪陇县YILONGXIAN', 395, 0, 3),
(4290, 'N', '南充市嘉陵区', '南充市嘉陵区NANCHONGSHIJIALINGQU', 395, 0, 3),
(4291, 'N', '南部县', '南部县NANBUXIAN', 395, 0, 3),
(4292, 'J', '嘉陵区', '嘉陵区JIALINGQU', 395, 0, 3),
(4293, 'Y', '营山县', '营山县YINGSHANXIAN', 395, 0, 3),
(4294, 'P', '蓬安县', '蓬安县PENGANXIAN', 395, 0, 3),
(4295, 'X', '西充县', '西充县XICHONGXIAN', 395, 0, 3),
(4296, 'L', '阆中市', '阆中市LANGZHONGSHI', 395, 0, 3),
(4297, 'S', '顺庆区', '顺庆区SHUNQINGQU', 395, 0, 3),
(4298, 'G', '高坪区', '高坪区GAOPINGQU', 395, 0, 3),
(4299, 'D', '东坡区', '东坡区DONGPOQU', 396, 0, 3),
(4300, 'D', '丹棱县', '丹棱县DANLENGXIAN', 396, 0, 3),
(4301, 'R', '仁寿县', '仁寿县RENSHOUXIAN', 396, 0, 3),
(4302, 'P', '彭山县', '彭山县PENGSHANXIAN', 396, 0, 3),
(4303, 'H', '洪雅县', '洪雅县HONGYAXIAN', 396, 0, 3),
(4304, 'Q', '青神县', '青神县QINGSHENXIAN', 396, 0, 3),
(4305, 'X', '兴文县', '兴文县XINGWENXIAN', 397, 0, 3),
(4306, 'N', '南溪县', '南溪县NANXIXIAN', 397, 0, 3),
(4307, 'Y', '宜宾县', '宜宾县YIBINXIAN', 397, 0, 3),
(4308, 'P', '屏山县', '屏山县PINGSHANXIAN', 397, 0, 3),
(4309, 'J', '江安县', '江安县JIANGANXIAN', 397, 0, 3),
(4310, 'G', '珙县', '珙县GONGXIAN', 397, 0, 3),
(4311, 'J', '筠连县', '筠连县JUNLIANXIAN', 397, 0, 3),
(4312, 'C', '翠屏区', '翠屏区CUIPINGQU', 397, 0, 3),
(4313, 'C', '长宁县', '长宁县CHANGNINGXIAN', 397, 0, 3),
(4314, 'G', '高县', '高县GAOXIAN', 397, 0, 3),
(4315, 'H', '华蓥市', '华蓥市HUAYINGSHI', 398, 0, 3),
(4316, 'Y', '岳池县', '岳池县YUECHIXIAN', 398, 0, 3),
(4317, 'G', '广安区', '广安区GUANGANQU', 398, 0, 3),
(4318, 'W', '武胜县', '武胜县WUSHENGXIAN', 398, 0, 3),
(4319, 'L', '邻水县', '邻水县LINSHUIXIAN', 398, 0, 3),
(4320, 'W', '万源市', '万源市WANYUANSHI', 399, 0, 3),
(4321, 'D', '大竹县', '大竹县DAZHUXIAN', 399, 0, 3),
(4322, 'X', '宣汉县', '宣汉县XUANHANXIAN', 399, 0, 3),
(4323, 'K', '开江县', '开江县KAIJIANGXIAN', 399, 0, 3),
(4324, 'Q', '渠县', '渠县QUXIAN', 399, 0, 3),
(4325, 'D', '达县', '达县DAXIAN', 399, 0, 3),
(4326, 'T', '通川区', '通川区TONGCHUANQU', 399, 0, 3),
(4327, 'M', '名山县', '名山县MINGSHANXIAN', 400, 0, 3),
(4328, 'T', '天全县', '天全县TIANQUANXIAN', 400, 0, 3),
(4329, 'B', '宝兴县', '宝兴县BAOXINGXIAN', 400, 0, 3),
(4330, 'H', '汉源县', '汉源县HANYUANXIAN', 400, 0, 3),
(4331, 'S', '石棉县', '石棉县SHIMIANXIAN', 400, 0, 3),
(4332, 'L', '芦山县', '芦山县LUSHANXIAN', 400, 0, 3),
(4333, 'Y', '荥经县', '荥经县YINGJINGXIAN', 400, 0, 3),
(4334, 'Y', '雨城区', '雨城区YUCHENGQU', 400, 0, 3),
(4335, 'N', '南江县', '南江县NANJIANGXIAN', 401, 0, 3),
(4336, 'B', '巴州区', '巴州区BAZHOUQU', 401, 0, 3),
(4337, 'P', '平昌县', '平昌县PINGCHANGXIAN', 401, 0, 3),
(4338, 'T', '通江县', '通江县TONGJIANGXIAN', 401, 0, 3),
(4339, 'L', '乐至县', '乐至县LEZHIXIAN', 402, 0, 3),
(4340, 'A', '安岳县', '安岳县ANYUEXIAN', 402, 0, 3),
(4341, 'J', '简阳市', '简阳市JIANYANGSHI', 402, 0, 3),
(4342, 'Y', '雁江区', '雁江区YANJIANGQU', 402, 0, 3),
(4343, 'J', '九寨沟县', '九寨沟县JIUZHAIGOUXIAN', 403, 0, 3),
(4344, 'R', '壤塘县', '壤塘县RANGTANGXIAN', 403, 0, 3),
(4345, 'X', '小金县', '小金县XIAOJINXIAN', 403, 0, 3),
(4346, 'S', '松潘县', '松潘县SONGPANXIAN', 403, 0, 3),
(4347, 'W', '汶川县', '汶川县WENCHUANXIAN', 403, 0, 3),
(4348, 'L', '理县', '理县LIXIAN', 403, 0, 3),
(4349, 'H', '红原县', '红原县HONGYUANXIAN', 403, 0, 3),
(4350, 'R', '若尔盖县', '若尔盖县RUOERGAIXIAN', 403, 0, 3),
(4351, 'M', '茂县', '茂县MAOXIAN', 403, 0, 3),
(4352, 'J', '金川县', '金川县JINCHUANXIAN', 403, 0, 3),
(4353, 'A', '阿坝县', '阿坝县ABAXIAN', 403, 0, 3),
(4354, 'M', '马尔康县', '马尔康县MAERKANGXIAN', 403, 0, 3),
(4355, 'H', '黑水县', '黑水县HEISHUIXIAN', 403, 0, 3),
(4356, 'D', '丹巴县', '丹巴县DANBAXIAN', 404, 0, 3),
(4357, 'X', '乡城县', '乡城县XIANGCHENGXIAN', 404, 0, 3),
(4358, 'B', '巴塘县', '巴塘县BATANGXIAN', 404, 0, 3),
(4359, 'K', '康定县', '康定县KANGDINGXIAN', 404, 0, 3),
(4360, 'D', '得荣县', '得荣县DERONGXIAN', 404, 0, 3),
(4361, 'D', '德格县', '德格县DEGEXIAN', 404, 0, 3),
(4362, 'X', '新龙县', '新龙县XINLONGXIAN', 404, 0, 3),
(4363, 'L', '泸定县', '泸定县LUDINGXIAN', 404, 0, 3),
(4364, 'L', '炉霍县', '炉霍县LUHUOXIAN', 404, 0, 3),
(4365, 'L', '理塘县', '理塘县LITANGXIAN', 404, 0, 3),
(4366, 'G', '甘孜县', '甘孜县GANZIXIAN', 404, 0, 3),
(4367, 'B', '白玉县', '白玉县BAIYUXIAN', 404, 0, 3),
(4368, 'S', '石渠县', '石渠县SHIQUXIAN', 404, 0, 3),
(4369, 'D', '稻城县', '稻城县DAOCHENGXIAN', 404, 0, 3),
(4370, 'S', '色达县', '色达县SHAIDAXIAN', 404, 0, 3),
(4371, 'D', '道孚县', '道孚县DAOFUXIAN', 404, 0, 3),
(4372, 'Y', '雅江县', '雅江县YAJIANGXIAN', 404, 0, 3),
(4373, 'H', '会东县', '会东县HUIDONGXIAN', 405, 0, 3),
(4374, 'H', '会理县', '会理县HUILIXIAN', 405, 0, 3),
(4375, 'M', '冕宁县', '冕宁县MIANNINGXIAN', 405, 0, 3),
(4376, 'X', '喜德县', '喜德县XIDEXIAN', 405, 0, 3),
(4377, 'N', '宁南县', '宁南县NINGNANXIAN', 405, 0, 3),
(4378, 'B', '布拖县', '布拖县BUTUOXIAN', 405, 0, 3),
(4379, 'D', '德昌县', '德昌县DECHANGXIAN', 405, 0, 3),
(4380, 'Z', '昭觉县', '昭觉县ZHAOJUEXIAN', 405, 0, 3),
(4381, 'P', '普格县', '普格县PUGEXIAN', 405, 0, 3),
(4382, 'M', '木里藏族自治县', '木里藏族自治县MULIZANGZUZIZHIXIAN', 405, 0, 3),
(4383, 'G', '甘洛县', '甘洛县GANLUOXIAN', 405, 0, 3),
(4384, 'Y', '盐源县', '盐源县YANYUANXIAN', 405, 0, 3),
(4385, 'M', '美姑县', '美姑县MEIGUXIAN', 405, 0, 3),
(4386, 'X', '西昌', '西昌XICHANG', 405, 0, 3),
(4387, 'Y', '越西县', '越西县YUEXIXIAN', 405, 0, 3),
(4388, 'J', '金阳县', '金阳县JINYANGXIAN', 405, 0, 3),
(4389, 'L', '雷波县', '雷波县LEIBOXIAN', 405, 0, 3),
(4390, 'W', '乌当区', '乌当区WUDANGQU', 406, 0, 3),
(4391, 'Y', '云岩区', '云岩区YUNYANQU', 406, 0, 3),
(4392, 'X', '修文县', '修文县XIUWENXIAN', 406, 0, 3),
(4393, 'N', '南明区', '南明区NANMINGQU', 406, 0, 3),
(4394, 'X', '小河区', '小河区XIAOHEQU', 406, 0, 3),
(4395, 'K', '开阳县', '开阳县KAIYANGXIAN', 406, 0, 3),
(4396, 'X', '息烽县', '息烽县XIFENGXIAN', 406, 0, 3),
(4397, 'Q', '清镇市', '清镇市QINGZHENSHI', 406, 0, 3),
(4398, 'B', '白云区', '白云区BAIYUNQU', 406, 0, 3),
(4399, 'H', '花溪区', '花溪区HUAXIQU', 406, 0, 3),
(4400, 'L', '六枝特区', '六枝特区LUZHITEQU', 407, 0, 3),
(4401, 'S', '水城县', '水城县SHUICHENGXIAN', 407, 0, 3),
(4402, 'P', '盘州市', '盘州市PANZHOUSHI', 407, 0, 3),
(4403, 'Z', '钟山区', '钟山区ZHONGSHANQU', 407, 0, 3),
(4404, 'X', '习水县', '习水县XISHUIXIAN', 408, 0, 3),
(4405, 'R', '仁怀市', '仁怀市RENHUAISHI', 408, 0, 3),
(4406, 'Y', '余庆县', '余庆县YUQINGXIAN', 408, 0, 3),
(4407, 'F', '凤冈县', '凤冈县FENGGANGXIAN', 408, 0, 3),
(4408, 'W', '务川仡佬族苗族自治县', '务川仡佬族苗族自治县WUCHUANGELAOZUMIAOZUZIZHIXIAN', 408, 0, 3),
(4409, 'T', '桐梓县', '桐梓县TONGZIXIAN', 408, 0, 3),
(4410, 'Z', '正安县', '正安县ZHENGANXIAN', 408, 0, 3),
(4411, 'H', '汇川区', '汇川区HUICHUANQU', 408, 0, 3),
(4412, 'M', '湄潭县', '湄潭县MEITANXIAN', 408, 0, 3),
(4413, 'H', '红花岗区', '红花岗区HONGHUAGANGQU', 408, 0, 3),
(4414, 'S', '绥阳县', '绥阳县SUIYANGXIAN', 408, 0, 3),
(4415, 'C', '赤水市', '赤水市CHISHUISHI', 408, 0, 3),
(4416, 'D', '道真仡佬族苗族自治县', '道真仡佬族苗族自治县DAOZHENGELAOZUMIAOZUZIZHIXIAN', 408, 0, 3),
(4417, 'Z', '遵义县', '遵义县ZUNYIXIAN', 408, 0, 3),
(4418, 'G', '关岭布依族苗族自治县', '关岭布依族苗族自治县GUANLINGBUYIZUMIAOZUZIZHIXIAN', 409, 0, 3),
(4419, 'P', '平坝县', '平坝县PINGBAXIAN', 409, 0, 3),
(4420, 'P', '普定县', '普定县PUDINGXIAN', 409, 0, 3),
(4421, 'Z', '紫云苗族布依族自治县', '紫云苗族布依族自治县ZIYUNMIAOZUBUYIZUZIZHIXIAN', 409, 0, 3),
(4422, 'X', '西秀区', '西秀区XIXIUQU', 409, 0, 3),
(4423, 'Z', '镇宁布依族苗族自治县', '镇宁布依族苗族自治县ZHENNINGBUYIZUMIAOZUZIZHIXIAN', 409, 0, 3),
(4424, 'W', '万山特区', '万山特区WANSHANTEQU', 410, 0, 3),
(4425, 'Y', '印江土家族苗族自治县', '印江土家族苗族自治县YINJIANGTUJIAZUMIAOZUZIZHIXIAN', 410, 0, 3),
(4426, 'D', '德江县', '德江县DEJIANGXIAN', 410, 0, 3),
(4427, 'S', '思南县', '思南县SINANXIAN', 410, 0, 3),
(4428, 'S', '松桃苗族自治县', '松桃苗族自治县SONGTAOMIAOZUZIZHIXIAN', 410, 0, 3),
(4429, 'J', '江口县', '江口县JIANGKOUXIAN', 410, 0, 3),
(4430, 'Y', '沿河土家族自治县', '沿河土家族自治县YANHETUJIAZUZIZHIXIAN', 410, 0, 3),
(4431, 'Y', '玉屏侗族自治县', '玉屏侗族自治县YUPINGDONGZUZIZHIXIAN', 410, 0, 3),
(4432, 'S', '石阡县', '石阡县SHIQIANXIAN', 410, 0, 3),
(4433, 'T', '铜仁市', '铜仁市TONGRENSHI', 410, 0, 3),
(4434, 'X', '兴义市', '兴义市XINGYISHI', 411, 0, 3),
(4435, 'X', '兴仁县', '兴仁县XINGRENXIAN', 411, 0, 3),
(4436, 'C', '册亨县', '册亨县CEHENGXIAN', 411, 0, 3),
(4437, 'A', '安龙县', '安龙县ANLONGXIAN', 411, 0, 3),
(4438, 'P', '普安县', '普安县PUANXIAN', 411, 0, 3),
(4439, 'Q', '晴隆县', '晴隆县QINGLONGXIAN', 411, 0, 3),
(4440, 'W', '望谟县', '望谟县WANGMOXIAN', 411, 0, 3),
(4441, 'Z', '贞丰县', '贞丰县ZHENFENGXIAN', 411, 0, 3),
(4442, 'D', '大方县', '大方县DAFANGXIAN', 412, 0, 3),
(4443, 'W', '威宁彝族回族苗族自治县', '威宁彝族回族苗族自治县WEININGYIZUHUIZUMIAOZUZIZHIXIAN', 412, 0, 3),
(4444, 'B', '毕节市', '毕节市BIJIESHI', 412, 0, 3),
(4445, 'N', '纳雍县', '纳雍县NAYONGXIAN', 412, 0, 3),
(4446, 'Z', '织金县', '织金县ZHIJINXIAN', 412, 0, 3),
(4447, 'H', '赫章县', '赫章县HEZHANGXIAN', 412, 0, 3),
(4448, 'J', '金沙县', '金沙县JINSHAXIAN', 412, 0, 3),
(4449, 'Q', '黔西县', '黔西县QIANXIXIAN', 412, 0, 3),
(4450, 'S', '三穗县', '三穗县SANSUIXIAN', 413, 0, 3),
(4451, 'D', '丹寨县', '丹寨县DANZHAIXIAN', 413, 0, 3),
(4452, 'C', '从江县', '从江县CONGJIANGXIAN', 413, 0, 3),
(4453, 'K', '凯里市', '凯里市KAILISHI', 413, 0, 3),
(4454, 'J', '剑河县', '剑河县JIANHEXIAN', 413, 0, 3),
(4455, 'T', '台江县', '台江县TAIJIANGXIAN', 413, 0, 3),
(4456, 'T', '天柱县', '天柱县TIANZHUXIAN', 413, 0, 3),
(4457, 'C', '岑巩县', '岑巩县CENGONGXIAN', 413, 0, 3),
(4458, 'S', '施秉县', '施秉县SHIBINGXIAN', 413, 0, 3),
(4459, 'R', '榕江县', '榕江县RONGJIANGXIAN', 413, 0, 3),
(4460, 'J', '锦屏县', '锦屏县JINPINGXIAN', 413, 0, 3),
(4461, 'Z', '镇远县', '镇远县ZHENYUANXIAN', 413, 0, 3),
(4462, 'L', '雷山县', '雷山县LEISHANXIAN', 413, 0, 3),
(4463, 'M', '麻江县', '麻江县MAJIANGXIAN', 413, 0, 3),
(4464, 'H', '黄平县', '黄平县HUANGPINGXIAN', 413, 0, 3),
(4465, 'L', '黎平县', '黎平县LIPINGXIAN', 413, 0, 3),
(4466, 'S', '三都水族自治县', '三都水族自治县SANDUSHUIZUZIZHIXIAN', 414, 0, 3),
(4467, 'P', '平塘县', '平塘县PINGTANGXIAN', 414, 0, 3),
(4468, 'H', '惠水县', '惠水县HUISHUIXIAN', 414, 0, 3),
(4469, 'D', '独山县', '独山县DUSHANXIAN', 414, 0, 3),
(4470, 'W', '瓮安县', '瓮安县WENGANXIAN', 414, 0, 3),
(4471, 'F', '福泉市', '福泉市FUQUANSHI', 414, 0, 3),
(4472, 'L', '罗甸县', '罗甸县LUODIANXIAN', 414, 0, 3),
(4473, 'L', '荔波县', '荔波县LIBOXIAN', 414, 0, 3),
(4474, 'G', '贵定县', '贵定县GUIDINGXIAN', 414, 0, 3),
(4475, 'D', '都匀市', '都匀市DUYUNSHI', 414, 0, 3),
(4476, 'C', '长顺县', '长顺县CHANGSHUNXIAN', 414, 0, 3),
(4477, 'L', '龙里县', '龙里县LONGLIXIAN', 414, 0, 3),
(4478, 'D', '东川区', '东川区DONGCHUANQU', 415, 0, 3),
(4479, 'W', '五华区', '五华区WUHUAQU', 415, 0, 3),
(4480, 'C', '呈贡县', '呈贡县CHENGGONGXIAN', 415, 0, 3),
(4481, 'A', '安宁市', '安宁市ANNINGSHI', 415, 0, 3),
(4482, 'G', '官渡区', '官渡区GUANDUQU', 415, 0, 3),
(4483, 'Y', '宜良县', '宜良县YILIANGXIAN', 415, 0, 3),
(4484, 'F', '富民县', '富民县FUMINXIAN', 415, 0, 3),
(4485, 'X', '寻甸回族彝族自治县', '寻甸回族彝族自治县XUNDIANHUIZUYIZUZIZHIXIAN', 415, 0, 3),
(4486, 'S', '嵩明县', '嵩明县SONGMINGXIAN', 415, 0, 3),
(4487, 'J', '晋宁县', '晋宁县JINNINGXIAN', 415, 0, 3),
(4488, 'P', '盘龙区', '盘龙区PANLONGQU', 415, 0, 3),
(4489, 'S', '石林彝族自治县', '石林彝族自治县SHILINYIZUZIZHIXIAN', 415, 0, 3),
(4490, 'L', '禄劝彝族苗族自治县', '禄劝彝族苗族自治县LUQUANYIZUMIAOZUZIZHIXIAN', 415, 0, 3),
(4491, 'X', '西山区', '西山区XISHANQU', 415, 0, 3),
(4492, 'H', '会泽县', '会泽县HUIZEXIAN', 416, 0, 3),
(4493, 'X', '宣威市', '宣威市XUANWEISHI', 416, 0, 3),
(4494, 'F', '富源县', '富源县FUYUANXIAN', 416, 0, 3),
(4495, 'S', '师宗县', '师宗县SHIZONGXIAN', 416, 0, 3),
(4496, 'Z', '沾益县', '沾益县ZHANYIXIAN', 416, 0, 3),
(4497, 'L', '罗平县', '罗平县LUOPINGXIAN', 416, 0, 3),
(4498, 'L', '陆良县', '陆良县LULIANGXIAN', 416, 0, 3),
(4499, 'M', '马龙县', '马龙县MALONGXIAN', 416, 0, 3),
(4500, 'Q', '麒麟区', '麒麟区QILINQU', 416, 0, 3),
(4501, 'Y', '元江哈尼族彝族傣族自治县', '元江哈尼族彝族傣族自治县YUANJIANGHANIZUYIZUDAIZUZIZHIXIAN', 417, 0, 3),
(4502, 'H', '华宁县', '华宁县HUANINGXIAN', 417, 0, 3),
(4503, 'E', '峨山彝族自治县', '峨山彝族自治县ESHANYIZUZIZHIXIAN', 417, 0, 3),
(4504, 'X', '新平彝族傣族自治县', '新平彝族傣族自治县XINPINGYIZUDAIZUZIZHIXIAN', 417, 0, 3),
(4505, 'Y', '易门县', '易门县YIMENXIAN', 417, 0, 3),
(4506, 'J', '江川县', '江川县JIANGCHUANXIAN', 417, 0, 3),
(4507, 'C', '澄江县', '澄江县CHENGJIANGXIAN', 417, 0, 3),
(4508, 'H', '红塔区', '红塔区HONGTAQU', 417, 0, 3),
(4509, 'T', '通海县', '通海县TONGHAIXIAN', 417, 0, 3),
(4510, 'S', '施甸县', '施甸县SHIDIANXIAN', 418, 0, 3),
(4511, 'C', '昌宁县', '昌宁县CHANGNINGXIAN', 418, 0, 3),
(4512, 'T', '腾冲县', '腾冲县TENGCHONGXIAN', 418, 0, 3),
(4513, 'L', '隆阳区', '隆阳区LONGYANGQU', 418, 0, 3),
(4514, 'L', '龙陵县', '龙陵县LONGLINGXIAN', 418, 0, 3),
(4515, 'D', '大关县', '大关县DAGUANXIAN', 419, 0, 3),
(4516, 'W', '威信县', '威信县WEIXINXIAN', 419, 0, 3),
(4517, 'Q', '巧家县', '巧家县QIAOJIAXIAN', 419, 0, 3),
(4518, 'Y', '彝良县', '彝良县YILIANGXIAN', 419, 0, 3),
(4519, 'Z', '昭阳区', '昭阳区ZHAOYANGQU', 419, 0, 3),
(4520, 'S', '水富县', '水富县SHUIFUXIAN', 419, 0, 3),
(4521, 'Y', '永善县', '永善县YONGSHANXIAN', 419, 0, 3),
(4522, 'Y', '盐津县', '盐津县YANJINXIAN', 419, 0, 3),
(4523, 'S', '绥江县', '绥江县SUIJIANGXIAN', 419, 0, 3),
(4524, 'Z', '镇雄县', '镇雄县ZHENXIONGXIAN', 419, 0, 3),
(4525, 'L', '鲁甸县', '鲁甸县LUDIANXIAN', 419, 0, 3),
(4526, 'H', '华坪县', '华坪县HUAPINGXIAN', 420, 0, 3),
(4527, 'G', '古城区', '古城区GUCHENGQU', 420, 0, 3),
(4528, 'N', '宁蒗彝族自治县', '宁蒗彝族自治县NINGLANGYIZUZIZHIXIAN', 420, 0, 3),
(4529, 'Y', '永胜县', '永胜县YONGSHENGXIAN', 420, 0, 3),
(4530, 'Y', '玉龙纳西族自治县', '玉龙纳西族自治县YULONGNAXIZUZIZHIXIAN', 420, 0, 3),
(4531, 'L', '临翔区', '临翔区LINXIANGQU', 422, 0, 3),
(4532, 'Y', '云县', '云县YUNXIAN', 422, 0, 3),
(4533, 'F', '凤庆县', '凤庆县FENGQINGXIAN', 422, 0, 3),
(4534, 'S', '双江拉祜族佤族布朗族傣族自治县', '双江拉祜族佤族布朗族傣族自治县SHUANGJIANGLAHUZUWAZUBULANGZUDAIZUZIZHIXIAN', 422, 0, 3),
(4535, 'Y', '永德县', '永德县YONGDEXIAN', 422, 0, 3),
(4536, 'C', '沧源佤族自治县', '沧源佤族自治县CANGYUANWAZUZIZHIXIAN', 422, 0, 3),
(4537, 'G', '耿马傣族佤族自治县', '耿马傣族佤族自治县GENGMADAIZUWAZUZIZHIXIAN', 422, 0, 3),
(4538, 'Z', '镇康县', '镇康县ZHENKANGXIAN', 422, 0, 3),
(4539, 'Y', '元谋县', '元谋县YUANMOUXIAN', 423, 0, 3),
(4540, 'N', '南华县', '南华县NANHUAXIAN', 423, 0, 3),
(4541, 'S', '双柏县', '双柏县SHUANGBAIXIAN', 423, 0, 3),
(4542, 'D', '大姚县', '大姚县DAYAOXIAN', 423, 0, 3),
(4543, 'Y', '姚安县', '姚安县YAOANXIAN', 423, 0, 3),
(4544, 'C', '楚雄市', '楚雄市CHUXIONGSHI', 423, 0, 3),
(4545, 'W', '武定县', '武定县WUDINGXIAN', 423, 0, 3),
(4546, 'Y', '永仁县', '永仁县YONGRENXIAN', 423, 0, 3),
(4547, 'M', '牟定县', '牟定县MOUDINGXIAN', 423, 0, 3),
(4548, 'L', '禄丰县', '禄丰县LUFENGXIAN', 423, 0, 3),
(4549, 'G', '个旧市', '个旧市GEJIUSHI', 424, 0, 3),
(4550, 'Y', '元阳县', '元阳县YUANYANGXIAN', 424, 0, 3),
(4551, 'P', '屏边苗族自治县', '屏边苗族自治县PINGBIANMIAOZUZIZHIXIAN', 424, 0, 3),
(4552, 'J', '建水县', '建水县JIANSHUIXIAN', 424, 0, 3),
(4553, 'K', '开远市', '开远市KAIYUANSHI', 424, 0, 3),
(4554, 'M', '弥勒县', '弥勒县MILEXIAN', 424, 0, 3),
(4555, 'H', '河口瑶族自治县', '河口瑶族自治县HEKOUYAOZUZIZHIXIAN', 424, 0, 3),
(4556, 'L', '泸西县', '泸西县LUXIXIAN', 424, 0, 3),
(4557, 'S', '石屏县', '石屏县SHIPINGXIAN', 424, 0, 3),
(4558, 'H', '红河县', '红河县HONGHEXIAN', 424, 0, 3),
(4559, 'L', '绿春县', '绿春县LYUCHUNXIAN', 424, 0, 3),
(4560, 'M', '蒙自市', '蒙自市MENGZISHI', 424, 0, 3),
(4561, 'J', '金平苗族瑶族傣族自治县', '金平苗族瑶族傣族自治县JINPINGMIAOZUYAOZUDAIZUZIZHIXIAN', 424, 0, 3),
(4562, 'Q', '丘北县', '丘北县QIUBEIXIAN', 425, 0, 3),
(4563, 'F', '富宁县', '富宁县FUNINGXIAN', 425, 0, 3),
(4564, 'G', '广南县', '广南县GUANGNANXIAN', 425, 0, 3),
(4565, 'W', '文山县', '文山县WENSHANXIAN', 425, 0, 3),
(4566, 'Y', '砚山县', '砚山县YANSHANXIAN', 425, 0, 3),
(4567, 'X', '西畴县', '西畴县XICHOUXIAN', 425, 0, 3),
(4568, 'M', '马关县', '马关县MAGUANXIAN', 425, 0, 3),
(4569, 'M', '麻栗坡县', '麻栗坡县MALIPOXIAN', 425, 0, 3),
(4570, 'M', '勐海县', '勐海县MENGHAIXIAN', 426, 0, 3),
(4571, 'M', '勐腊县', '勐腊县MENGLAXIAN', 426, 0, 3),
(4572, 'J', '景洪市', '景洪市JINGHONGSHI', 426, 0, 3),
(4573, 'Y', '云龙县', '云龙县YUNLONGXIAN', 427, 0, 3),
(4574, 'J', '剑川县', '剑川县JIANCHUANXIAN', 427, 0, 3),
(4575, 'N', '南涧彝族自治县', '南涧彝族自治县NANJIANYIZUZIZHIXIAN', 427, 0, 3),
(4576, 'D', '大理市', '大理市DALISHI', 427, 0, 3),
(4577, 'B', '宾川县', '宾川县BINCHUANXIAN', 427, 0, 3),
(4578, 'W', '巍山彝族回族自治县', '巍山彝族回族自治县WEISHANYIZUHUIZUZIZHIXIAN', 427, 0, 3),
(4579, 'M', '弥渡县', '弥渡县MIDUXIAN', 427, 0, 3),
(4580, 'Y', '永平县', '永平县YONGPINGXIAN', 427, 0, 3),
(4581, 'E', '洱源县', '洱源县ERYUANXIAN', 427, 0, 3),
(4582, 'Y', '漾濞彝族自治县', '漾濞彝族自治县YANGBIYIZUZIZHIXIAN', 427, 0, 3),
(4583, 'X', '祥云县', '祥云县XIANGYUNXIAN', 427, 0, 3),
(4584, 'H', '鹤庆县', '鹤庆县HEQINGXIAN', 427, 0, 3),
(4585, 'L', '梁河县', '梁河县LIANGHEXIAN', 428, 0, 3),
(4586, 'L', '潞西市', '潞西市LUXISHI', 428, 0, 3),
(4587, 'R', '瑞丽市', '瑞丽市RUILISHI', 428, 0, 3),
(4588, 'Y', '盈江县', '盈江县YINGJIANGXIAN', 428, 0, 3),
(4589, 'L', '陇川县', '陇川县LONGCHUANXIAN', 428, 0, 3),
(4590, 'D', '德钦县', '德钦县DEQINXIAN', 430, 0, 3),
(4591, 'W', '维西傈僳族自治县', '维西傈僳族自治县WEIXILISUZUZIZHIXIAN', 430, 0, 3),
(4592, 'X', '香格里拉县', '香格里拉县XIANGGELILAXIAN', 430, 0, 3),
(4593, 'C', '城关区', '城关区CHENGGUANQU', 431, 0, 3),
(4594, 'D', '堆龙德庆县', '堆龙德庆县DUILONGDEQINGXIAN', 431, 0, 3),
(4595, 'M', '墨竹工卡县', '墨竹工卡县MOZHUGONGKAXIAN', 431, 0, 3),
(4596, 'N', '尼木县', '尼木县NIMUXIAN', 431, 0, 3),
(4597, 'D', '当雄县', '当雄县DANGXIONGXIAN', 431, 0, 3),
(4598, 'Q', '曲水县', '曲水县QUSHUIXIAN', 431, 0, 3),
(4599, 'L', '林周县', '林周县LINZHOUXIAN', 431, 0, 3),
(4600, 'D', '达孜县', '达孜县DAZIXIAN', 431, 0, 3),
(4601, 'D', '丁青县', '丁青县DINGQINGXIAN', 432, 0, 3),
(4602, 'B', '八宿县', '八宿县BASUXIAN', 432, 0, 3),
(4603, 'C', '察雅县', '察雅县CHAYAXIAN', 432, 0, 3),
(4604, 'Z', '左贡县', '左贡县ZUOGONGXIAN', 432, 0, 3),
(4605, 'C', '昌都县', '昌都县CHANGDUXIAN', 432, 0, 3),
(4606, 'J', '江达县', '江达县JIANGDAXIAN', 432, 0, 3),
(4607, 'L', '洛隆县', '洛隆县LUOLONGXIAN', 432, 0, 3),
(4608, 'L', '类乌齐县', '类乌齐县LEIWUQIXIAN', 432, 0, 3),
(4609, 'M', '芒康县', '芒康县MANGKANGXIAN', 432, 0, 3),
(4610, 'G', '贡觉县', '贡觉县GONGJUEXIAN', 432, 0, 3),
(4611, 'B', '边坝县', '边坝县BIANBAXIAN', 432, 0, 3),
(4612, 'N', '乃东县', '乃东县NAIDONGXIAN', 433, 0, 3),
(4613, 'J', '加查县', '加查县JIACHAXIAN', 433, 0, 3),
(4614, 'Z', '扎囊县', '扎囊县ZANANGXIAN', 433, 0, 3),
(4615, 'C', '措美县', '措美县CUOMEIXIAN', 433, 0, 3),
(4616, 'Q', '曲松县', '曲松县QUSONGXIAN', 433, 0, 3),
(4617, 'S', '桑日县', '桑日县SANGRIXIAN', 433, 0, 3),
(4618, 'L', '洛扎县', '洛扎县LUOZHAXIAN', 433, 0, 3),
(4619, 'L', '浪卡子县', '浪卡子县LANGKAZIXIAN', 433, 0, 3),
(4620, 'Q', '琼结县', '琼结县QIONGJIEXIAN', 433, 0, 3),
(4621, 'G', '贡嘎县', '贡嘎县GONGGAXIAN', 433, 0, 3),
(4622, 'C', '错那县', '错那县CUONAXIAN', 433, 0, 3),
(4623, 'L', '隆子县', '隆子县LONGZIXIAN', 433, 0, 3),
(4624, 'Y', '亚东县', '亚东县YADONGXIAN', 434, 0, 3),
(4625, 'R', '仁布县', '仁布县RENBUXIAN', 434, 0, 3),
(4626, 'Z', '仲巴县', '仲巴县ZHONGBAXIAN', 434, 0, 3),
(4627, 'N', '南木林县', '南木林县NANMULINXIAN', 434, 0, 3),
(4628, 'J', '吉隆县', '吉隆县JILONGXIAN', 434, 0, 3),
(4629, 'D', '定日县', '定日县DINGRIXIAN', 434, 0, 3),
(4630, 'D', '定结县', '定结县DINGJIEXIAN', 434, 0, 3),
(4631, 'G', '岗巴县', '岗巴县GANGBAXIAN', 434, 0, 3),
(4632, 'K', '康马县', '康马县KANGMAXIAN', 434, 0, 3),
(4633, 'L', '拉孜县', '拉孜县LAZIXIAN', 434, 0, 3),
(4634, 'R', '日喀则市', '日喀则市RIKAZESHI', 434, 0, 3),
(4635, 'A', '昂仁县', '昂仁县ANGRENXIAN', 434, 0, 3),
(4636, 'J', '江孜县', '江孜县JIANGZIXIAN', 434, 0, 3),
(4637, 'B', '白朗县', '白朗县BAILANGXIAN', 434, 0, 3),
(4638, 'N', '聂拉木县', '聂拉木县NIELAMUXIAN', 434, 0, 3),
(4639, 'S', '萨嘎县', '萨嘎县SAGAXIAN', 434, 0, 3),
(4640, 'S', '萨迦县', '萨迦县SAJIAXIAN', 434, 0, 3),
(4641, 'X', '谢通门县', '谢通门县XIETONGMENXIAN', 434, 0, 3),
(4642, 'J', '嘉黎县', '嘉黎县JIALIXIAN', 435, 0, 3),
(4643, 'A', '安多县', '安多县ANDUOXIAN', 435, 0, 3),
(4644, 'N', '尼玛县', '尼玛县NIMAXIAN', 435, 0, 3),
(4645, 'B', '巴青县', '巴青县BAQINGXIAN', 435, 0, 3),
(4646, 'B', '比如县', '比如县BIRUXIAN', 435, 0, 3),
(4647, 'B', '班戈县', '班戈县BANGEXIAN', 435, 0, 3),
(4648, 'S', '申扎县', '申扎县SHENZHAXIAN', 435, 0, 3),
(4649, 'S', '索县', '索县SUOXIAN', 435, 0, 3),
(4650, 'N', '聂荣县', '聂荣县NIERONGXIAN', 435, 0, 3),
(4651, 'N', '那曲县', '那曲县NAQUXIAN', 435, 0, 3),
(4652, 'G', '噶尔县', '噶尔县GAERXIAN', 436, 0, 3),
(4653, 'C', '措勤县', '措勤县CUOQINXIAN', 436, 0, 3),
(4654, 'G', '改则县', '改则县GAIZEXIAN', 436, 0, 3),
(4655, 'R', '日土县', '日土县RITUXIAN', 436, 0, 3),
(4656, 'P', '普兰县', '普兰县PULANXIAN', 436, 0, 3),
(4657, 'Z', '札达县', '札达县ZHADAXIAN', 436, 0, 3),
(4658, 'G', '革吉县', '革吉县GEJIXIAN', 436, 0, 3),
(4659, 'M', '墨脱县', '墨脱县MOTUOXIAN', 437, 0, 3),
(4660, 'C', '察隅县', '察隅县CHAYUXIAN', 437, 0, 3),
(4661, 'G', '工布江达县', '工布江达县GONGBUJIANGDAXIAN', 437, 0, 3),
(4662, 'L', '朗县', '朗县LANGXIAN', 437, 0, 3),
(4663, 'L', '林芝县', '林芝县LINZHIXIAN', 437, 0, 3),
(4664, 'B', '波密县', '波密县BOMIXIAN', 437, 0, 3),
(4665, 'M', '米林县', '米林县MILINXIAN', 437, 0, 3),
(4666, 'L', '临潼区', '临潼区LINTONGQU', 438, 0, 3),
(4667, 'Z', '周至县', '周至县ZHOUZHIXIAN', 438, 0, 3),
(4668, 'H', '户县', '户县HUXIAN', 438, 0, 3),
(4669, 'X', '新城区', '新城区XINCHENGQU', 438, 0, 3),
(4670, 'W', '未央区', '未央区WEIYANGQU', 438, 0, 3),
(4671, 'B', '灞桥区', '灞桥区BAQIAOQU', 438, 0, 3),
(4672, 'B', '碑林区', '碑林区BEILINQU', 438, 0, 3),
(4673, 'L', '莲湖区', '莲湖区LIANHUQU', 438, 0, 3),
(4674, 'L', '蓝田县', '蓝田县LANTIANXIAN', 438, 0, 3),
(4675, 'C', '长安区', '长安区CHANGANQU', 438, 0, 3),
(4676, 'Y', '阎良区', '阎良区YANLIANGQU', 438, 0, 3),
(4677, 'Y', '雁塔区', '雁塔区YANTAQU', 438, 0, 3),
(4678, 'G', '高陵县', '高陵县GAOLINGXIAN', 438, 0, 3),
(4679, 'Y', '印台区', '印台区YINTAIQU', 439, 0, 3),
(4680, 'Y', '宜君县', '宜君县YIJUNXIAN', 439, 0, 3),
(4681, 'W', '王益区', '王益区WANGYIQU', 439, 0, 3),
(4682, 'Y', '耀州区', '耀州区YAOZHOUQU', 439, 0, 3),
(4683, 'F', '凤县', '凤县FENGXIAN', 440, 0, 3),
(4684, 'F', '凤翔县', '凤翔县FENGXIANGXIAN', 440, 0, 3),
(4685, 'Q', '千阳县', '千阳县QIANYANGXIAN', 440, 0, 3),
(4686, 'T', '太白县', '太白县TAIBAIXIAN', 440, 0, 3),
(4687, 'Q', '岐山县', '岐山县QISHANXIAN', 440, 0, 3),
(4688, 'F', '扶风县', '扶风县FUFENGXIAN', 440, 0, 3),
(4689, 'W', '渭滨区', '渭滨区WEIBINQU', 440, 0, 3),
(4690, 'M', '眉县', '眉县MEIXIAN', 440, 0, 3),
(4691, 'J', '金台区', '金台区JINTAIQU', 440, 0, 3),
(4692, 'L', '陇县', '陇县LONGXIAN', 440, 0, 3),
(4693, 'C', '陈仓区', '陈仓区CHENCANGQU', 440, 0, 3),
(4694, 'L', '麟游县', '麟游县LINYOUXIAN', 440, 0, 3),
(4695, 'S', '三原县', '三原县SANYUANXIAN', 441, 0, 3),
(4696, 'G', '干县', '干县GANXIAN', 441, 0, 3),
(4697, 'X', '兴平市', '兴平市XINGPINGSHI', 441, 0, 3),
(4698, 'B', '彬县', '彬县BINXIAN', 441, 0, 3),
(4699, 'X', '旬邑县', '旬邑县XUNYIXIAN', 441, 0, 3),
(4700, 'Y', '杨陵区', '杨陵区YANGLINGQU', 441, 0, 3),
(4701, 'W', '武功县', '武功县WUGONGXIAN', 441, 0, 3),
(4702, 'Y', '永寿县', '永寿县YONGSHOUXIAN', 441, 0, 3),
(4703, 'J', '泾阳县', '泾阳县JINGYANGXIAN', 441, 0, 3),
(4704, 'C', '淳化县', '淳化县CHUNHUAXIAN', 441, 0, 3),
(4705, 'W', '渭城区', '渭城区WEICHENGQU', 441, 0, 3),
(4706, 'L', '礼泉县', '礼泉县LIQUANXIAN', 441, 0, 3),
(4707, 'Q', '秦都区', '秦都区QINDUQU', 441, 0, 3),
(4708, 'C', '长武县', '长武县CHANGWUXIAN', 441, 0, 3),
(4709, 'L', '临渭区', '临渭区LINWEIQU', 442, 0, 3),
(4710, 'H', '华县', '华县HUAXIAN', 442, 0, 3),
(4711, 'H', '华阴市', '华阴市HUAYINSHI', 442, 0, 3),
(4712, 'H', '合阳县', '合阳县HEYANGXIAN', 442, 0, 3),
(4713, 'D', '大荔县', '大荔县DALIXIAN', 442, 0, 3),
(4714, 'F', '富平县', '富平县FUPINGXIAN', 442, 0, 3),
(4715, 'T', '潼关县', '潼关县TONGGUANXIAN', 442, 0, 3),
(4716, 'C', '澄城县', '澄城县CHENGCHENGXIAN', 442, 0, 3),
(4717, 'B', '白水县', '白水县BAISHUIXIAN', 442, 0, 3),
(4718, 'P', '蒲城县', '蒲城县PUCHENGXIAN', 442, 0, 3),
(4719, 'H', '韩城市', '韩城市HANCHENGSHI', 442, 0, 3),
(4720, 'W', '吴起县', '吴起县WUQIXIAN', 443, 0, 3),
(4721, 'Z', '子长县', '子长县ZICHANGXIAN', 443, 0, 3),
(4722, 'A', '安塞县', '安塞县ANSAIXIAN', 443, 0, 3),
(4723, 'Y', '宜川县', '宜川县YICHUANXIAN', 443, 0, 3),
(4724, 'B', '宝塔区', '宝塔区BAOTAQU', 443, 0, 3),
(4725, 'F', '富县', '富县FUXIAN', 443, 0, 3),
(4726, 'Y', '延川县', '延川县YANCHUANXIAN', 443, 0, 3),
(4727, 'Y', '延长县', '延长县YANCHANGXIAN', 443, 0, 3),
(4728, 'Z', '志丹县', '志丹县ZHIDANXIAN', 443, 0, 3),
(4729, 'L', '洛川县', '洛川县LUOCHUANXIAN', 443, 0, 3),
(4730, 'G', '甘泉县', '甘泉县GANQUANXIAN', 443, 0, 3),
(4731, 'H', '黄陵县', '黄陵县HUANGLINGXIAN', 443, 0, 3),
(4732, 'H', '黄龙县', '黄龙县HUANGLONGXIAN', 443, 0, 3),
(4733, 'F', '佛坪县', '佛坪县FOPINGXIAN', 444, 0, 3),
(4734, 'M', '勉县', '勉县MIANXIAN', 444, 0, 3),
(4735, 'N', '南郑县', '南郑县NANZHENGXIAN', 444, 0, 3),
(4736, 'C', '城固县', '城固县CHENGGUXIAN', 444, 0, 3),
(4737, 'N', '宁强县', '宁强县NINGQIANGXIAN', 444, 0, 3),
(4738, 'H', '汉台区', '汉台区HANTAIQU', 444, 0, 3),
(4739, 'Y', '洋县', '洋县YANGXIAN', 444, 0, 3),
(4740, 'L', '留坝县', '留坝县LIUBAXIAN', 444, 0, 3),
(4741, 'L', '略阳县', '略阳县LUEYANGXIAN', 444, 0, 3),
(4742, 'X', '西乡县', '西乡县XIXIANGXIAN', 444, 0, 3),
(4743, 'Z', '镇巴县', '镇巴县ZHENBAXIAN', 444, 0, 3),
(4744, 'J', '佳县', '佳县JIAXIAN', 445, 0, 3),
(4745, 'W', '吴堡县', '吴堡县WUBUXIAN', 445, 0, 3),
(4746, 'Z', '子洲县', '子洲县ZIZHOUXIAN', 445, 0, 3),
(4747, 'D', '定边县', '定边县DINGBIANXIAN', 445, 0, 3),
(4748, 'F', '府谷县', '府谷县FUGUXIAN', 445, 0, 3),
(4749, 'Y', '榆林市榆阳区', '榆林市榆阳区YULINSHIYUYANGQU', 445, 0, 3),
(4750, 'H', '横山县', '横山县HENGSHANXIAN', 445, 0, 3),
(4751, 'Q', '清涧县', '清涧县QINGJIANXIAN', 445, 0, 3),
(4752, 'S', '神木县', '神木县SHENMUXIAN', 445, 0, 3),
(4753, 'M', '米脂县', '米脂县MIZHIXIAN', 445, 0, 3),
(4754, 'S', '绥德县', '绥德县SUIDEXIAN', 445, 0, 3),
(4755, 'J', '靖边县', '靖边县JINGBIANXIAN', 445, 0, 3),
(4756, 'N', '宁陕县', '宁陕县NINGSHANXIAN', 446, 0, 3),
(4757, 'L', '岚皋县', '岚皋县LANGAOXIAN', 446, 0, 3),
(4758, 'P', '平利县', '平利县PINGLIXIAN', 446, 0, 3),
(4759, 'X', '旬阳县', '旬阳县XUNYANGXIAN', 446, 0, 3),
(4760, 'H', '汉滨区', '汉滨区HANBINQU', 446, 0, 3),
(4761, 'H', '汉阴县', '汉阴县HANYINXIAN', 446, 0, 3),
(4762, 'B', '白河县', '白河县BAIHEXIAN', 446, 0, 3),
(4763, 'S', '石泉县', '石泉县SHIQUANXIAN', 446, 0, 3),
(4764, 'Z', '紫阳县', '紫阳县ZIYANGXIAN', 446, 0, 3),
(4765, 'Z', '镇坪县', '镇坪县ZHENPINGXIAN', 446, 0, 3),
(4766, 'D', '丹凤县', '丹凤县DANFENGXIAN', 447, 0, 3),
(4767, 'S', '商南县', '商南县SHANGNANXIAN', 447, 0, 3),
(4768, 'S', '商州区', '商州区SHANGZHOUQU', 447, 0, 3),
(4769, 'S', '山阳县', '山阳县SHANYANGXIAN', 447, 0, 3),
(4770, 'Z', '柞水县', '柞水县ZHASHUIXIAN', 447, 0, 3),
(4771, 'L', '洛南县', '洛南县LUONANXIAN', 447, 0, 3),
(4772, 'Z', '镇安县', '镇安县ZHENANXIAN', 447, 0, 3),
(4773, 'Q', '七里河区', '七里河区QILIHEQU', 448, 0, 3),
(4774, 'C', '城关区', '城关区CHENGGUANQU', 448, 0, 3),
(4775, 'A', '安宁区', '安宁区ANNINGQU', 448, 0, 3),
(4776, 'Y', '榆中县', '榆中县YUZHONGXIAN', 448, 0, 3),
(4777, 'Y', '永登县', '永登县YONGDENGXIAN', 448, 0, 3),
(4778, 'G', '皋兰县', '皋兰县GAOLANXIAN', 448, 0, 3),
(4779, 'H', '红古区', '红古区HONGGUQU', 448, 0, 3),
(4780, 'X', '西固区', '西固区XIGUQU', 448, 0, 3),
(4781, 'J', '嘉峪关市', '嘉峪关市JIAYUGUANSHI', 449, 0, 3),
(4782, 'Y', '永昌县', '永昌县YONGCHANGXIAN', 450, 0, 3),
(4783, 'J', '金川区', '金川区JINCHUANQU', 450, 0, 3),
(4784, 'H', '会宁县', '会宁县HUININGXIAN', 451, 0, 3),
(4785, 'P', '平川区', '平川区PINGCHUANQU', 451, 0, 3),
(4786, 'J', '景泰县', '景泰县JINGTAIXIAN', 451, 0, 3),
(4787, 'B', '白银区', '白银区BAIYINQU', 451, 0, 3),
(4788, 'J', '靖远县', '靖远县JINGYUANXIAN', 451, 0, 3),
(4789, 'Z', '张家川回族自治县', '张家川回族自治县ZHANGJIACHUANHUIZUZIZHIXIAN', 452, 0, 3),
(4790, 'W', '武山县', '武山县WUSHANXIAN', 452, 0, 3),
(4791, 'Q', '清水县', '清水县QINGSHUIXIAN', 452, 0, 3),
(4792, 'G', '甘谷县', '甘谷县GANGUXIAN', 452, 0, 3),
(4793, 'Q', '秦安县', '秦安县QINANXIAN', 452, 0, 3),
(4794, 'Q', '秦州区', '秦州区QINZHOUQU', 452, 0, 3),
(4795, 'M', '麦积区', '麦积区MAIJIQU', 452, 0, 3),
(4796, 'L', '凉州区', '凉州区LIANGZHOUQU', 453, 0, 3),
(4797, 'G', '古浪县', '古浪县GULANGXIAN', 453, 0, 3),
(4798, 'T', '天祝藏族自治县', '天祝藏族自治县TIANZHUZANGZUZIZHIXIAN', 453, 0, 3),
(4799, 'M', '民勤县', '民勤县MINQINXIAN', 453, 0, 3),
(4800, 'L', '临泽县', '临泽县LINZEXIAN', 454, 0, 3),
(4801, 'S', '山丹县', '山丹县SHANDANXIAN', 454, 0, 3),
(4802, 'M', '民乐县', '民乐县MINYUEXIAN', 454, 0, 3),
(4803, 'G', '甘州区', '甘州区GANZHOUQU', 454, 0, 3),
(4804, 'S', '肃南裕固族自治县', '肃南裕固族自治县SUNANYUGUZUZIZHIXIAN', 454, 0, 3),
(4805, 'G', '高台县', '高台县GAOTAIXIAN', 454, 0, 3),
(4806, 'H', '华亭县', '华亭县HUATINGXIAN', 455, 0, 3),
(4807, 'K', '崆峒区', '崆峒区KONGTONGQU', 455, 0, 3),
(4808, 'C', '崇信县', '崇信县CHONGXINXIAN', 455, 0, 3),
(4809, 'Z', '庄浪县', '庄浪县ZHUANGLANGXIAN', 455, 0, 3),
(4810, 'J', '泾川县', '泾川县JINGCHUANXIAN', 455, 0, 3),
(4811, 'L', '灵台县', '灵台县LINGTAIXIAN', 455, 0, 3),
(4812, 'J', '静宁县', '静宁县JINGNINGXIAN', 455, 0, 3),
(4813, 'D', '敦煌市', '敦煌市DUNHUANGSHI', 456, 0, 3),
(4814, 'Y', '玉门市', '玉门市YUMENSHI', 456, 0, 3),
(4815, 'G', '瓜州县（原安西县）', '瓜州县（原安西县）GUAZHOUXIANYUANANXIXIAN', 456, 0, 3),
(4816, 'S', '肃北蒙古族自治县', '肃北蒙古族自治县SUBEIMENGGUZUZIZHIXIAN', 456, 0, 3),
(4817, 'S', '肃州区', '肃州区SUZHOUQU', 456, 0, 3),
(4818, 'J', '金塔县', '金塔县JINTAXIAN', 456, 0, 3),
(4819, 'A', '阿克塞哈萨克族自治县', '阿克塞哈萨克族自治县AKESAIHASAKEZUZIZHIXIAN', 456, 0, 3),
(4820, 'H', '华池县', '华池县HUACHIXIAN', 457, 0, 3),
(4821, 'H', '合水县', '合水县HESHUIXIAN', 457, 0, 3),
(4822, 'N', '宁县', '宁县NINGXIAN', 457, 0, 3),
(4823, 'Q', '庆城县', '庆城县QINGCHENGXIAN', 457, 0, 3),
(4824, 'Z', '正宁县', '正宁县ZHENGNINGXIAN', 457, 0, 3),
(4825, 'H', '环县', '环县HUANXIAN', 457, 0, 3),
(4826, 'X', '西峰区', '西峰区XIFENGQU', 457, 0, 3),
(4827, 'Z', '镇原县', '镇原县ZHENYUANXIAN', 457, 0, 3),
(4828, 'L', '临洮县', '临洮县LINTAOXIAN', 458, 0, 3),
(4829, 'A', '安定区', '安定区ANDINGQU', 458, 0, 3),
(4830, 'M', '岷县', '岷县MINXIAN', 458, 0, 3),
(4831, 'W', '渭源县', '渭源县WEIYUANXIAN', 458, 0, 3),
(4832, 'Z', '漳县', '漳县ZHANGXIAN', 458, 0, 3),
(4833, 'T', '通渭县', '通渭县TONGWEIXIAN', 458, 0, 3),
(4834, 'L', '陇西县', '陇西县LONGXIXIAN', 458, 0, 3),
(4835, 'L', '两当县', '两当县LIANGDANGXIAN', 459, 0, 3),
(4836, 'D', '宕昌县', '宕昌县DANGCHANGXIAN', 459, 0, 3),
(4837, 'K', '康县', '康县KANGXIAN', 459, 0, 3),
(4838, 'H', '徽县', '徽县HUIXIAN', 459, 0, 3),
(4839, 'C', '成县', '成县CHENGXIAN', 459, 0, 3),
(4840, 'W', '文县', '文县WENXIAN', 459, 0, 3),
(4841, 'W', '武都区', '武都区WUDUQU', 459, 0, 3),
(4842, 'L', '礼县', '礼县LIXIAN', 459, 0, 3),
(4843, 'X', '西和县', '西和县XIHEXIAN', 459, 0, 3),
(4844, 'D', '东乡族自治县', '东乡族自治县DONGXIANGZUZIZHIXIAN', 460, 0, 3),
(4845, 'L', '临夏县', '临夏县LINXIAXIAN', 460, 0, 3),
(4846, 'L', '临夏市', '临夏市LINXIASHI', 460, 0, 3),
(4847, 'H', '和政县', '和政县HEZHENGXIAN', 460, 0, 3),
(4848, 'G', '广河县', '广河县GUANGHEXIAN', 460, 0, 3),
(4849, 'K', '康乐县', '康乐县KANGLEXIAN', 460, 0, 3),
(4850, 'Y', '永靖县', '永靖县YONGJINGXIAN', 460, 0, 3),
(4851, 'J', '积石山保安族东乡族撒拉族自治县', '积石山保安族东乡族撒拉族自治县JISHISHANBAOANZUDONGXIANGZUSALAZUZIZHIXIAN', 460, 0, 3),
(4852, 'L', '临潭县', '临潭县LINTANXIAN', 461, 0, 3),
(4853, 'Z', '卓尼县', '卓尼县ZHUONIXIAN', 461, 0, 3),
(4854, 'H', '合作市', '合作市HEZUOSHI', 461, 0, 3),
(4855, 'X', '夏河县', '夏河县XIAHEXIAN', 461, 0, 3),
(4856, 'M', '玛曲县', '玛曲县MAQUXIAN', 461, 0, 3),
(4857, 'L', '碌曲县', '碌曲县LUQUXIAN', 461, 0, 3),
(4858, 'Z', '舟曲县', '舟曲县ZHOUQUXIAN', 461, 0, 3),
(4859, 'D', '迭部县', '迭部县DIEBUXIAN', 461, 0, 3),
(4860, 'C', '城东区', '城东区CHENGDONGQU', 462, 0, 3),
(4861, 'C', '城中区', '城中区CHENGZHONGQU', 462, 0, 3),
(4862, 'C', '城北区', '城北区CHENGBEIQU', 462, 0, 3),
(4863, 'C', '城西区', '城西区CHENGXIQU', 462, 0, 3),
(4864, 'D', '大通回族土族自治县', '大通回族土族自治县DATONGHUIZUTUZUZIZHIXIAN', 462, 0, 3),
(4865, 'H', '湟中县', '湟中县HUANGZHONGXIAN', 462, 0, 3),
(4866, 'H', '湟源县', '湟源县HUANGYUANXIAN', 462, 0, 3),
(4867, 'L', '乐都县', '乐都县LEDUXIAN', 463, 0, 3),
(4868, 'H', '互助土族自治县', '互助土族自治县HUZHUTUZUZIZHIXIAN', 463, 0, 3),
(4869, 'H', '化隆回族自治县', '化隆回族自治县HUALONGHUIZUZIZHIXIAN', 463, 0, 3),
(4870, 'P', '平安县', '平安县PINGANXIAN', 463, 0, 3),
(4871, 'X', '循化撒拉族自治县', '循化撒拉族自治县XUNHUASALAZUZIZHIXIAN', 463, 0, 3),
(4872, 'M', '民和回族土族自治县', '民和回族土族自治县MINHEHUIZUTUZUZIZHIXIAN', 463, 0, 3),
(4873, 'G', '刚察县', '刚察县GANGCHAXIAN', 464, 0, 3),
(4874, 'H', '海晏县', '海晏县HAIYANXIAN', 464, 0, 3),
(4875, 'Q', '祁连县', '祁连县QILIANXIAN', 464, 0, 3),
(4876, 'M', '门源回族自治县', '门源回族自治县MENYUANHUIZUZIZHIXIAN', 464, 0, 3),
(4877, 'T', '同仁县', '同仁县TONGRENXIAN', 465, 0, 3),
(4878, 'J', '尖扎县', '尖扎县JIANZHAXIAN', 465, 0, 3),
(4879, 'H', '河南蒙古族自治县', '河南蒙古族自治县HENANMENGGUZUZIZHIXIAN', 465, 0, 3),
(4880, 'Z', '泽库县', '泽库县ZEKUXIAN', 465, 0, 3),
(4881, 'G', '共和县', '共和县GONGHEXIAN', 466, 0, 3),
(4882, 'X', '兴海县', '兴海县XINGHAIXIAN', 466, 0, 3),
(4883, 'T', '同德县', '同德县TONGDEXIAN', 466, 0, 3),
(4884, 'G', '贵南县', '贵南县GUINANXIAN', 466, 0, 3),
(4885, 'G', '贵德县', '贵德县GUIDEXIAN', 466, 0, 3),
(4886, 'J', '久治县', '久治县JIUZHIXIAN', 467, 0, 3),
(4887, 'M', '玛多县', '玛多县MADUOXIAN', 467, 0, 3),
(4888, 'M', '玛沁县', '玛沁县MAQINXIAN', 467, 0, 3),
(4889, 'B', '班玛县', '班玛县BANMAXIAN', 467, 0, 3),
(4890, 'G', '甘德县', '甘德县GANDEXIAN', 467, 0, 3),
(4891, 'D', '达日县', '达日县DARIXIAN', 467, 0, 3),
(4892, 'N', '囊谦县', '囊谦县NANGQIANXIAN', 468, 0, 3),
(4893, 'Q', '曲麻莱县', '曲麻莱县QUMALAIXIAN', 468, 0, 3),
(4894, 'Z', '杂多县', '杂多县ZADUOXIAN', 468, 0, 3),
(4895, 'Z', '治多县', '治多县ZHIDUOXIAN', 468, 0, 3),
(4896, 'Y', '玉树县', '玉树县YUSHUXIAN', 468, 0, 3),
(4897, 'C', '称多县', '称多县CHENDUOXIAN', 468, 0, 3),
(4898, 'W', '乌兰县', '乌兰县WULANXIAN', 469, 0, 3),
(4899, 'L', '冷湖行委', '冷湖行委LENGHUXINGWEI', 469, 0, 3),
(4900, 'D', '大柴旦行委', '大柴旦行委DACHAIDANXINGWEI', 469, 0, 3),
(4901, 'T', '天峻县', '天峻县TIANJUNXIAN', 469, 0, 3),
(4902, 'D', '德令哈市', '德令哈市DELINGHASHI', 469, 0, 3),
(4903, 'G', '格尔木市', '格尔木市GEERMUSHI', 469, 0, 3),
(4904, 'M', '茫崖行委', '茫崖行委MANGYAXINGWEI', 469, 0, 3),
(4905, 'D', '都兰县', '都兰县DULANXIAN', 469, 0, 3),
(4906, 'X', '兴庆区', '兴庆区XINGQINGQU', 470, 0, 3),
(4907, 'Y', '永宁县', '永宁县YONGNINGXIAN', 470, 0, 3),
(4908, 'L', '灵武市', '灵武市LINGWUSHI', 470, 0, 3),
(4909, 'X', '西夏区', '西夏区XIXIAQU', 470, 0, 3),
(4910, 'H', '贺兰县', '贺兰县HELANXIAN', 470, 0, 3),
(4911, 'J', '金凤区', '金凤区JINFENGQU', 470, 0, 3),
(4912, 'D', '大武口区', '大武口区DAWUKOUQU', 471, 0, 3),
(4913, 'P', '平罗县', '平罗县PINGLUOXIAN', 471, 0, 3),
(4914, 'H', '惠农区', '惠农区HUINONGQU', 471, 0, 3),
(4915, 'L', '利通区', '利通区LITONGQU', 472, 0, 3),
(4916, 'T', '同心县', '同心县TONGXINXIAN', 472, 0, 3),
(4917, 'Y', '盐池县', '盐池县YANCHIXIAN', 472, 0, 3),
(4918, 'Q', '青铜峡市', '青铜峡市QINGTONGXIASHI', 472, 0, 3),
(4919, 'Y', '原州区', '原州区YUANZHOUQU', 473, 0, 3),
(4920, 'P', '彭阳县', '彭阳县PENGYANGXIAN', 473, 0, 3),
(4921, 'J', '泾源县', '泾源县JINGYUANXIAN', 473, 0, 3),
(4922, 'X', '西吉县', '西吉县XIJIXIAN', 473, 0, 3),
(4923, 'L', '隆德县', '隆德县LONGDEXIAN', 473, 0, 3),
(4924, 'Z', '中宁县', '中宁县ZHONGNINGXIAN', 474, 0, 3),
(4925, 'S', '沙坡头区', '沙坡头区SHAPOTOUQU', 474, 0, 3),
(4926, 'H', '海原县', '海原县HAIYUANXIAN', 474, 0, 3),
(4927, 'D', '东山区', '东山区DONGSHANQU', 475, 0, 3),
(4928, 'W', '乌鲁木齐县', '乌鲁木齐县WULUMUQIXIAN', 475, 0, 3),
(4929, 'T', '天山区', '天山区TIANSHANQU', 475, 0, 3),
(4930, 'T', '头屯河区', '头屯河区TOUTUNHEQU', 475, 0, 3);
INSERT INTO `ims_xm_mallv3_area` (`id`, `letter`, `area_name`, `keyword`, `area_parent_id`, `area_sort`, `area_deep`) VALUES
(4931, 'X', '新市区', '新市区XINSHIQU', 475, 0, 3),
(4932, 'S', '水磨沟区', '水磨沟区SHUIMOGOUQU', 475, 0, 3),
(4933, 'S', '沙依巴克区', '沙依巴克区SHAYIBAKEQU', 475, 0, 3),
(4934, 'D', '达坂城区', '达坂城区DABANCHENGQU', 475, 0, 3),
(4935, 'W', '乌尔禾区', '乌尔禾区WUERHEQU', 476, 0, 3),
(4936, 'K', '克拉玛依区', '克拉玛依区KELAMAYIQU', 476, 0, 3),
(4937, 'D', '独山子区', '独山子区DUSHANZIQU', 476, 0, 3),
(4938, 'B', '白碱滩区', '白碱滩区BAIJIANTANQU', 476, 0, 3),
(4939, 'T', '吐鲁番市', '吐鲁番市TULUFANSHI', 477, 0, 3),
(4940, 'T', '托克逊县', '托克逊县TUOKEXUNXIAN', 477, 0, 3),
(4941, 'S', '鄯善县', '鄯善县SHANSHANXIAN', 477, 0, 3),
(4942, 'Y', '伊吾县', '伊吾县YIWUXIAN', 478, 0, 3),
(4943, 'H', '哈密市', '哈密市HAMISHI', 478, 0, 3),
(4944, 'B', '巴里坤哈萨克自治县', '巴里坤哈萨克自治县BALIKUNHASAKEZIZHIXIAN', 478, 0, 3),
(4945, 'J', '吉木萨尔县', '吉木萨尔县JIMUSAERXIAN', 479, 0, 3),
(4946, 'H', '呼图壁县', '呼图壁县HUTUBIXIAN', 479, 0, 3),
(4947, 'Q', '奇台县', '奇台县QITAIXIAN', 479, 0, 3),
(4948, 'C', '昌吉市', '昌吉市CHANGJISHI', 479, 0, 3),
(4949, 'M', '木垒哈萨克自治县', '木垒哈萨克自治县MULEIHASAKEZIZHIXIAN', 479, 0, 3),
(4950, 'M', '玛纳斯县', '玛纳斯县MANASIXIAN', 479, 0, 3),
(4951, 'M', '米泉市', '米泉市MIQUANSHI', 479, 0, 3),
(4952, 'F', '阜康市', '阜康市FUKANGSHI', 479, 0, 3),
(4953, 'B', '博乐市', '博乐市BOLESHI', 480, 0, 3),
(4954, 'W', '温泉县', '温泉县WENQUANXIAN', 480, 0, 3),
(4955, 'J', '精河县', '精河县JINGHEXIAN', 480, 0, 3),
(4956, 'B', '博湖县', '博湖县BOHUXIAN', 481, 0, 3),
(4957, 'H', '和硕县', '和硕县HESHUOXIAN', 481, 0, 3),
(4958, 'H', '和静县', '和静县HEJINGXIAN', 481, 0, 3),
(4959, 'Y', '尉犁县', '尉犁县YULIXIAN', 481, 0, 3),
(4960, 'K', '库尔勒市', '库尔勒市KUERLESHI', 481, 0, 3),
(4961, 'Y', '焉耆回族自治县', '焉耆回族自治县YANQIHUIZUZIZHIXIAN', 481, 0, 3),
(4962, 'R', '若羌县', '若羌县RUOQIANGXIAN', 481, 0, 3),
(4963, 'L', '轮台县', '轮台县LUNTAIXIAN', 481, 0, 3),
(4964, 'W', '乌什县', '乌什县WUSHIXIAN', 482, 0, 3),
(4965, 'K', '库车县', '库车县KUCHEXIAN', 482, 0, 3),
(4966, 'B', '拜城县', '拜城县BAICHENGXIAN', 482, 0, 3),
(4967, 'X', '新和县', '新和县XINHEXIAN', 482, 0, 3),
(4968, 'K', '柯坪县', '柯坪县KEPINGXIAN', 482, 0, 3),
(4969, 'S', '沙雅县', '沙雅县SHAYAXIAN', 482, 0, 3),
(4970, 'W', '温宿县', '温宿县WENSUXIAN', 482, 0, 3),
(4971, 'A', '阿克苏市', '阿克苏市AKESUSHI', 482, 0, 3),
(4972, 'A', '阿瓦提县', '阿瓦提县AWATIXIAN', 482, 0, 3),
(4973, 'W', '乌恰县', '乌恰县WUQIAXIAN', 483, 0, 3),
(4974, 'A', '阿克陶县', '阿克陶县AKETAOXIAN', 483, 0, 3),
(4975, 'A', '阿合奇县', '阿合奇县AHEQIXIAN', 483, 0, 3),
(4976, 'A', '阿图什市', '阿图什市ATUSHISHI', 483, 0, 3),
(4977, 'J', '伽师县', '伽师县JIASHIXIAN', 484, 0, 3),
(4978, 'Y', '叶城县', '叶城县YECHENGXIAN', 484, 0, 3),
(4979, 'K', '喀什市', '喀什市KASHISHI', 484, 0, 3),
(4980, 'T', '塔什库尔干塔吉克自治县', '塔什库尔干塔吉克自治县TASHIKUERGANTAJIKEZIZHIXIAN', 484, 0, 3),
(4981, 'Y', '岳普湖县', '岳普湖县YUEPUHUXIAN', 484, 0, 3),
(4982, 'B', '巴楚县', '巴楚县BACHUXIAN', 484, 0, 3),
(4983, 'Z', '泽普县', '泽普县ZEPUXIAN', 484, 0, 3),
(4984, 'S', '疏勒县', '疏勒县SHULEXIAN', 484, 0, 3),
(4985, 'S', '疏附县', '疏附县SHUFUXIAN', 484, 0, 3),
(4986, 'Y', '英吉沙县', '英吉沙县YINGJISHAXIAN', 484, 0, 3),
(4987, 'S', '莎车县', '莎车县SHACHEXIAN', 484, 0, 3),
(4988, 'M', '麦盖提县', '麦盖提县MAIGETIXIAN', 484, 0, 3),
(4989, 'Y', '于田县', '于田县YUTIANXIAN', 485, 0, 3),
(4990, 'H', '和田县', '和田县HETIANXIAN', 485, 0, 3),
(4991, 'H', '和田市', '和田市HETIANSHI', 485, 0, 3),
(4992, 'M', '墨玉县', '墨玉县MOYUXIAN', 485, 0, 3),
(4993, 'M', '民丰县', '民丰县MINFENGXIAN', 485, 0, 3),
(4994, 'L', '洛浦县', '洛浦县LUOPUXIAN', 485, 0, 3),
(4995, 'P', '皮山县', '皮山县PISHANXIAN', 485, 0, 3),
(4996, 'C', '策勒县', '策勒县CELEXIAN', 485, 0, 3),
(4997, 'Y', '伊宁县', '伊宁县YININGXIAN', 486, 0, 3),
(4998, 'Y', '伊宁市', '伊宁市YININGSHI', 486, 0, 3),
(4999, 'K', '奎屯市', '奎屯市KUITUNSHI', 486, 0, 3),
(5000, 'C', '察布查尔锡伯自治县', '察布查尔锡伯自治县CHABUCHAERXIBOZIZHIXIAN', 486, 0, 3),
(5001, 'N', '尼勒克县', '尼勒克县NILEKEXIAN', 486, 0, 3),
(5002, 'G', '巩留县', '巩留县GONGLIUXIAN', 486, 0, 3),
(5003, 'X', '新源县', '新源县XINYUANXIAN', 486, 0, 3),
(5004, 'Z', '昭苏县', '昭苏县ZHAOSUXIAN', 486, 0, 3),
(5005, 'T', '特克斯县', '特克斯县TEKESIXIAN', 486, 0, 3),
(5006, 'H', '霍城县', '霍城县HUOCHENGXIAN', 486, 0, 3),
(5007, 'W', '乌苏市', '乌苏市WUSUSHI', 487, 0, 3),
(5008, 'H', '和布克赛尔蒙古自治县', '和布克赛尔蒙古自治县HEBUKESAIERMENGGUZIZHIXIAN', 487, 0, 3),
(5009, 'T', '塔城市', '塔城市TACHENGSHI', 487, 0, 3),
(5010, 'T', '托里县', '托里县TUOLIXIAN', 487, 0, 3),
(5011, 'S', '沙湾县', '沙湾县SHAWANXIAN', 487, 0, 3),
(5012, 'Y', '裕民县', '裕民县YUMINXIAN', 487, 0, 3),
(5013, 'E', '额敏县', '额敏县EMINXIAN', 487, 0, 3),
(5014, 'J', '吉木乃县', '吉木乃县JIMUNAIXIAN', 488, 0, 3),
(5015, 'H', '哈巴河县', '哈巴河县HABAHEXIAN', 488, 0, 3),
(5016, 'F', '富蕴县', '富蕴县FUYUNXIAN', 488, 0, 3),
(5017, 'B', '布尔津县', '布尔津县BUERJINXIAN', 488, 0, 3),
(5018, 'F', '福海县', '福海县FUHAIXIAN', 488, 0, 3),
(5019, 'A', '阿勒泰市', '阿勒泰市ALETAISHI', 488, 0, 3),
(5020, 'Q', '青河县', '青河县QINGHEXIAN', 488, 0, 3),
(5021, 'S', '石河子市', '石河子市SHIHEZISHI', 489, 0, 3),
(5022, 'A', '阿拉尔市', '阿拉尔市ALAERSHI', 490, 0, 3),
(5023, 'T', '图木舒克市', '图木舒克市TUMUSHUKESHI', 491, 0, 3),
(5024, 'W', '五家渠市', '五家渠市WUJIAQUSHI', 492, 0, 3),
(45056, 'T', '台湾', '台湾TAIWAN', 32, 100, 2),
(45057, 'X', '香港特别行政区', '香港特别行政区XIANGGANGTEBIEXINGZHENGQU', 33, 100, 2),
(45058, 'H', '花地玛堂区', '花地玛堂区HUADIMATANGQU', 534, 100, 3),
(45059, 'S', '圣安多尼堂区', '圣安多尼堂区SHENGANDUONITANGQU', 534, 100, 3),
(45060, 'D', '大堂区', '大堂区DATANGQU', 534, 100, 3),
(45061, 'W', '望德堂区', '望德堂区WANGDETANGQU', 534, 100, 3),
(45062, 'F', '风顺堂区', '风顺堂区FENGSHUNTANGQU', 534, 100, 3),
(45063, 'S', '圣方济各堂区', '圣方济各堂区SHENGFANGJIGETANGQU', 534, 100, 3),
(45064, 'L', '路氹城', '路氹城LUDANGCHENG', 534, 100, 3),
(45237, 'G', '观山湖区', '观山湖区GUANSHANHUQU', 406, 100, 1),
(45238, 'G', '贵安新区', '贵安新区GUIANXINQU', 406, 100, 1),
(45239, 'L', '龙港市', '龙港市LONGGANGSHI', 177, 100, 1),
(45240, 'L', '梁溪区', '梁溪区LIANGXIQU', 163, 4, 1),
(45241, 'X', '新吴区', '新吴区XINWUQU', 163, 5, 1),
(45242, 'B', '滨海新区', '滨海新区BINHAIXINQU', 40, 100, 1);

ALTER TABLE `ims_xm_mallv3_area`
  ADD PRIMARY KEY (`id`),
  ADD KEY `area_parent_id` (`area_parent_id`);

ALTER TABLE `ims_xm_mallv3_area`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '索引ID', AUTO_INCREMENT=45243;
COMMIT;");

  

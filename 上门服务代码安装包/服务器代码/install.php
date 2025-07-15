<?php
$tablenamepre = str_replace('`', '', tablename(''));

$dbFile = __DIR__ . '/data/installdata.sql';

$sql = file_get_contents($dbFile);

pdo_run(str_replace('ims_xm_', $tablenamepre . 'xm_', $sql));

$sql = "INSERT INTO `ims_xm_mallv3_user` (`id`, `uuid`, `weid`, `lastweid`, `uid`, `w7uid`, `did`, `sid`, `username`, `password`, `salt`, `touxiang`, `qianming`, `title`, `sex`, `mobile`, `role_id`, `remark`, `px`, `time`, `role`, `create_time`, `update_time`, `status`) VALUES
(1, NULL, 0, 0, NULL, 1, NULL, 0, 'admin', '8726ea1dc08a445039f8c7e3fc05cc904a8a1e22', '30e8d1b8', '', '', 'admin', 0, NULL, 0, '', 0, 0, NULL, 0, 1654850564, 1);";
  
pdo_run(str_replace('ims_xm_', $tablenamepre . 'xm_', $sql));

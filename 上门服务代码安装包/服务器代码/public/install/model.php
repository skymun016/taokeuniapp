<?php

/** 安装界面需要的各种模块 */

class installModel
{
    private $host;
    private $name;
    private $user;
    private $encoding;
    private $password;
    private $port;
    private $prefix;
    private $successTable = [];
    /**
     * @var bool
     */
    private $allowNext = true;
    /**
     * @var PDO|string
     */
    private $dbh = null;
    /**
     * @var bool
     */
    private $clearDB = false;


    public function getPhpVersion()
    {
        return PHP_VERSION;
    }

    public function checkPHP()
    {
        return $result = version_compare(PHP_VERSION, '7.4.0') >= 0 ? 'ok' : 'fail';
    }

    public function checkPDO()
    {
        return $result = extension_loaded('pdo') ? 'ok' : 'fail';
    }


    public function checkPDOMySQL()
    {
        return $result = extension_loaded('pdo_mysql') ? 'ok' : 'fail';
    }

    public function checkJSON()
    {
        return $result = extension_loaded('json') ? 'ok' : 'fail';
    }

    public function checkOpenssl()
    {
        return $result = extension_loaded('openssl') ? 'ok' : 'fail';
    }

    public function checkMbstring()
    {
        return $result = extension_loaded('mbstring') ? 'ok' : 'fail';
    }

    public function checkZlib()
    {
        return $result = extension_loaded('zlib') ? 'ok' : 'fail';
    }

    public function checkCurl()
    {
        return $result = extension_loaded('curl') ? 'ok' : 'fail';
    }

    public function checkGd2()
    {
        return $result = extension_loaded('gd') ? 'ok' : 'fail';
    }

    public function checkDom()
    {
        return $result = extension_loaded('dom') ? 'ok' : 'fail';
    }

    public function checkFilter()
    {
        return $result = extension_loaded('filter') ? 'ok' : 'fail';
    }

    public function checkIconv()
    {
        return $result = extension_loaded('iconv') ? 'ok' : 'fail';
    }

    public function checkFileInfo()
    {
        return $result = extension_loaded('fileinfo') ? 'ok' : 'fail';
    }

    public function getSessionSavePath()
    {
        $sessionSavePath = preg_replace("/\d;/", '', session_save_path());

        return [
            'path'     => $sessionSavePath,
            'exists'   => is_dir($sessionSavePath),
            'writable' => is_writable($sessionSavePath),
        ];
    }

    public function checkSessionSavePath()
    {
        $sessionSavePath = preg_replace("/\d;/", '', session_save_path());
        $result = (is_dir($sessionSavePath) and is_writable($sessionSavePath)) ? 'ok' : 'fail';
        if ($result == 'fail') return $result;

        file_put_contents($sessionSavePath . '/zentaotest', 'zentao');
        $sessionContent = file_get_contents($sessionSavePath . '/zentaotest');
        if ($sessionContent == 'zentao') {
            unlink($sessionSavePath . '/zentaotest');
            return 'ok';
        }
        return 'fail';
    }

    public function getIniInfo()
    {
        $iniInfo = '';
        ob_start();
        phpinfo(1);
        $lines = explode("\n", strip_tags(ob_get_contents()));
        ob_end_clean();
        foreach ($lines as $line) if (strpos($line, 'ini') !== false) $iniInfo .= $line . "\n";
        return $iniInfo;
    }

    public function mkdataconfig($db)
    {
        $config = include $this->getInstallRoot() . '/data/example.php';

        $config = str_replace(array(
            '{db-server}', '{db-username}', '{db-password}', '{db-port}', '{db-name}', '{db-tablepre}'
        ), array(
            $db['server'], $db['username'], $db['password'], $db['port'], $db['name'], $db['prefix']
        ), $config);

        return file_put_contents($this->getInstallRoot() . '/data/config.php', $config);
    }

    public function mkLockFile()
    {
        return touch($this->getInstallRoot() . '/data/install.lock');
    }

    public function appIsInstalled()
    {
        return file_exists($this->getInstallRoot() . '/data/install.lock');
    }

    public function checkConfig($dbName, $connectionInfo)
    {
        $return = new stdclass();
        $return->result = 'ok';

        /* Connect to database. */
        $this->setDBParam($connectionInfo);
        $this->dbh = $this->connectDB();
        if (strpos($dbName, '.') !== false) {
            $return->result = 'fail';
            $return->error = '没有发现数据库信息';
            return $return;
        }
        if (!is_object($this->dbh)) {
            $return->result = 'fail';
            $return->error = '安装错误，请检查连接信息:' . mb_strcut($this->dbh, 0, 30) . '...';
            echo $this->dbh;
            return $return;
        }

        /* Get mysql version. */
        $version = $this->getMysqlVersion();

        /* check mysql sql_model */
        //        if(!$this->checkSqlMode($version)) {
        //            $return->result = 'fail';
        //            $return->error = '请在mysql配置文件修改sql-mode添加NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';
        //            return $return;
        //        }

        /* If database no exits, try create it. */
        if (!$this->dbExists()) {
            if (!$this->createDB($version)) {
                $return->result = 'fail';
                $return->error = '创建数据库错误';
                return $return;
            }
        } elseif ($this->tableExits() and $this->clearDB == false) {
            $return->result = 'fail';
            $return->error = '数据表已存在，您之前可能已安装本系统，如需继续安装请选择新的数据库。';
            return $return;
        } elseif ($this->dbExists() and $this->clearDB == true) {
            if (!$this->dropDb($connectionInfo['name'])) {
                $return->result = 'fail';
                $return->error = '数据表已经存在，删除已存在库错误,请手动清除';
                return $return;
            } else {
                if (!$this->createDB($version)) {
                    $return->result = 'fail';
                    $return->error = '创建数据库错误!';
                    return $return;
                }
            }
        }

        /* Create tables. */
        if (!$this->createTable($version, $connectionInfo)) {
            $return->result = 'fail';
            $return->error = '创建表格失败';
            return $return;
        }

        return $return;
    }

    public function setDBParam($post)
    {
        $this->host = $post['host'];
        $this->name = $post['name'];
        $this->user = $post['user'];
        $this->encoding = 'utf8mb4';
        $this->password = $post['password'];
        $this->port = $post['port'];
        $this->prefix = $post['prefix'];
        $this->clearDB = $post['clear_db'] == 'on';
    }

    public function connectDB()
    {
        $dsn = "mysql:host={$this->host}; port={$this->port};charset=utf8";

        //$dsn = "mysql:dbname=echarts;host=localhost;port:3306;charset=utf8";


        try {
            $dbh = new PDO($dsn, $this->user, $this->password);
            $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $dbh->exec("SET NAMES {$this->encoding}");
            $dbh->exec("SET NAMES {$this->encoding}");
            try {
                $dbh->exec("SET GLOBAL sql_mode='STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION';");
            } catch (Exception $e) {
            }
            return $dbh;
        } catch (PDOException $exception) {
            return $exception->getMessage();
        }
    }

    public function dbExists()
    {
        $sql = "SHOW DATABASES like '{$this->name}'";
        return $this->dbh->query($sql)->fetch();
    }

    public function tableExits()
    {
        $configTable = sprintf("'%s'", $this->prefix . TESTING_TABLE);
        $sql = "SHOW TABLES FROM {$this->name} like $configTable";
        return $this->dbh->query($sql)->fetch();
    }

    public function getMysqlVersion()
    {
        $sql = "SELECT VERSION() AS version";
        $result = $this->dbh->query($sql)->fetch();
        return substr($result->version, 0, 3);
    }

    public function checkSqlMode($version)
    {
        $sql = "SELECT @@global.sql_mode";
        $result = $this->dbh->query($sql)->fetch();
        $result = (array)$result;

        if ($version >= 5.7 && $version < 8.0) {
            if ((strpos($result['@@global.sql_mode'], 'NO_AUTO_CREATE_USER') !== false)
                && (strpos($result['@@global.sql_mode'], 'NO_ENGINE_SUBSTITUTION') !== false)
            ) {
                return true;
            }
            return false;
        }
        return true;
    }

    public function createDB($version)
    {
        $sql = "CREATE DATABASE `{$this->name}`";
        if ($version > 4.1) $sql .= " DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";
        return $this->dbh->query($sql);
    }

    public function createTable($version, $post)
    {
        $dbFile = $this->getInstallRoot() . '/data/installdata.sql';
        //file_put_contents($dbFile, $this->initAccount($post), FILE_APPEND);
        $content = str_replace(";\r\n", ";\n", file_get_contents($dbFile));
        $tables = explode(";\n", $content);
        $tables[] = $this->initAccount($post);
        $installTime = microtime(true) * 10000;

        foreach ($tables as $table) {
            $table = trim($table);
            if (empty($table)) continue;

            if (strpos($table, 'CREATE') !== false and $version <= 4.1) {
                $table = str_replace('DEFAULT CHARSET=utf8', '', $table);
            }
            //            elseif (strpos($table, 'DROP') !== false and $this->clearDB != false) {
            //                $table = str_replace('--', '', $table);
            //            }

            /* Skip sql that is note. */
            if (strpos($table, '--') === 0) continue;

            $table = str_replace('`ims_', $this->name . '.`ims_', $table);
            $table = str_replace('`ims_', '`' . $this->prefix, $table);

            if (strpos($table, 'CREATE') !== false) {
                $tableName = explode('`', $table)[1];
                $installTime += random_int(3000, 7000);
                $this->successTable[] = [$tableName, date('Y-m-d H:i:s', $installTime / 10000)];
            }

            //            if (strpos($table, "INSERT INTO ") !== false) {
            //                $table = str_replace('INSERT INTO ', 'INSERT INTO ' .$this->name .'.', $table);
            //            }

            try {
                if (!$this->dbh->query($table)) return false;
            } catch (Exception $e) {
                echo 'error sql: ' . $table . "<br>";
                echo $e->getMessage() . "<br>";
                return false;
            }
        }
        return true;
    }

    public function dropDb($db)
    {
        $sql = "drop database {$db};";
        return $this->dbh->query($sql);
    }

    public function getSuccessTable()
    {
        return $this->successTable;
    }

    public function importDemoData()
    {
        $demoDataFile = 'ys.sql';
        $demoDataFile = $this->getInstallRoot() . '/data/' . $demoDataFile;
        if (!is_file($demoDataFile)) {
            echo "<br>";
            echo 'no file:' . $demoDataFile;
            return false;
        }
        $content = str_replace(";\r\n", ";\n", file_get_contents($demoDataFile));
        $insertTables = explode(";\n", $content);
        foreach ($insertTables as $table) {
            $table = trim($table);
            if (empty($table)) continue;

            $table = str_replace('`ims_', $this->name . '.`ims_', $table);
            $table = str_replace('`ims_', '`' . $this->prefix, $table);
            if (!$this->dbh->query($table)) return false;
        }

        // 移动图片资源
        $this->cpFiles($this->getInstallRoot() . '/uploads', $this->getAppRoot() . '/public/uploads');

        return true;
    }

    function cpFiles($rootFrom, $rootTo)
    {

        $handle = opendir($rootFrom);
        while (false !== ($file = readdir($handle))) {
            //DIRECTORY_SEPARATOR 为系统的文件夹名称的分隔符 例如：windos为'/'; linux为'/'
            $fileFrom = $rootFrom . DIRECTORY_SEPARATOR . $file;
            $fileTo = $rootTo . DIRECTORY_SEPARATOR . $file;
            if ($file == '.' || $file == '..') {
                continue;
            }

            if (is_dir($fileFrom)) {
                if (!is_dir($fileTo)) { //目标目录不存在则创建
                    mkdir($fileTo, 0777);
                }
                $this->cpFiles($fileFrom, $fileTo);
            } else {
                if (!file_exists($fileTo)) {
                    @copy($fileFrom, $fileTo);
                    if (strstr($fileTo, "access_token.txt")) {
                        chmod($fileTo, 0777);
                    }
                }
            }
        }
    }

    public function getAppRoot()
    {
        return dirname(dirname(INSTALL_ROOT));
    }

    public function getInstallRoot()
    {
        return dirname(dirname(INSTALL_ROOT));
    }

    public function freeDiskSpace($dir)
    {
        // M
        $freeDiskSpace = disk_free_space(realpath(__DIR__)) / 1024 / 1024;

        // G
        if ($freeDiskSpace > 1024) {
            return number_format($freeDiskSpace / 1024, 2) . 'G';
        }

        return number_format($freeDiskSpace, 2) . 'M';
    }

    public function correctOrFail($statusSingle)
    {
        if ($statusSingle == 'ok')
            return '<td class="layui-icon green">&#xe605;</td>';
        $this->allowNext = false;
        return '<td class="layui-icon wrong">&#x1006;</td>';
    }

    public function getAllowNext()
    {
        return $this->allowNext;
    }

    public function checkSessionAutoStart()
    {
        return $result = ini_get('session.auto_start') == '0' ? 'ok' : 'fail';
    }

    public function checkAutoTags()
    {
        return $result = ini_get('session.auto_start') == '0' ? 'ok' : 'fail';
    }


    public function checkDirWrite($dir = '')
    {
        $route = $this->getAppRoot() . '/' . $dir;
        return $result = is_writable($route) ? 'ok' : 'fail';
    }

    public function checkInstallRootDirWrite($dir = '')
    {
        $route = $this->getInstallRoot() . '/' . $dir;
        return $result = is_writable($route) ? 'ok' : 'fail';
    }

    public function initAccount($post)
    {
        $time = time();
        $salt = substr(md5($time . $post['admin_user']), 0, 8); //随机4位密码盐

        global $uniqueSalt;
        $uniqueSalt = $salt;

        $password = $this->createPassword($post['admin_password'], $salt);

        // 超级管理员
        $sql = "INSERT INTO `ims_xm_mallv3_user` (`id`, `uuid`, `weid`, `lastweid`, `uid`, `w7uid`, `did`, `sid`, `username`, `password`, `salt`, `touxiang`, `qianming`, `title`, `sex`, `mobile`, `role_id`, `remark`, `px`, `time`, `role`, `create_time`, `update_time`, `status`) VALUES
        (1, NULL, 0, 0, NULL, 1, NULL, 0, '{$post['admin_user']}', '{$password}', '{$salt}', '', '', '{$post['admin_user']}', 0, NULL, 0, '', 0, 0, NULL, 0, {$time}, 1);";

        return $sql;
    }

    function createPassword($passwordinput, $salt)
    {
        $myconfig = include $this->getAppRoot() . '/config/my.php';
        $authkey = $myconfig['authkey'];
        $passwordinput = "{$passwordinput}-{$salt}-{$authkey}";
        return sha1($passwordinput);
    }


    public function restoreIndexLock()
    {
        $this->checkIndexFile($this->getAppRoot() . '/admin');
    }

    public function checkIndexFile($path)
    {
        if (file_exists($path . '/index_lock.html')) {
            // 删除提示文件
            unlink($path . '/index.html');
            // 恢复原入口
            rename($path . '/index_lock.html', $path . '/index.html');
        }
    }
}

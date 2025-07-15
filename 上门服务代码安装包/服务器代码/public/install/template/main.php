<?php !defined('install') && exit(); ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>新麦O2O商城安装</title>
        <link rel="stylesheet" type="text/css" href="https://www.layuicdn.com/layui/css/layui.css"/>
        <link rel="stylesheet" type="text/css" href="./css/mounted.css"/>
        <link rel="shortcut icon" href="./favicon.ico"/>
    </head>
    <body>
    <div class="header">
        <div class="logo" style="width: 220px;">
        </div>
    </div>
    <div class="mounted" id="mounted">
        <div class="mounted-box">
        <form method="post" action="#" name="main_form">
<!--<div class="mounted-title">安装步骤</div>-->
                <div class="mounted-container" id="tab">
                    <ul class="mounted-nav" id="nav">
                        <li <?php if ($step == "1") { ?>class="active"<?php } ?>>许可协议</li>
                        <li <?php if ($step == "2") { ?>class="active"<?php } ?>>环境监测</li>
                        <li <?php if ($step == "3") { ?>class="active"<?php } ?>>参数配置</li>
                        <li <?php if ($step == "4" or $step == '5') { ?>class="active"<?php } ?>>安装</li>
                    </ul>

                    <!-- 阅读许可 -->
                    <?php if ($step == '1') { ?>
                        <div class="mounted-content-item show">
                            <div class="content-header">
                                阅读许可协议
                            </div>
                            <div class="content">
                                <h2>新麦O2O商城系统授权协议</h2>
                                <div class="white-space;pre">

                                </div>
                                <p class="mt16"><span style="font-size:16px">版权所有 (c)2018-<?=date('Y')?>，佛山市新麦网络科技有限公司保留所有权利。</span></p>
                                <p class="mt16"><span style="font-size:16px">感谢您选择新麦商城系统，新麦商城系统国内稳定、强大、先进的互联网电商O2O平台解决方案之一。<br />
								本《软件授权许可协议》（以下简称&ldquo;协议&rdquo;）是你（自然人、法人或其他组织）与佛山市新麦网络科技有限公司（以下简称&ldquo;新麦网络&rdquo;）之间有关下载、安装、使用本新麦商城系统的协议，同时本协议适用于任何有关新麦商城系统的后期更新和升级。一旦下载、安装或以其他方式使用新麦商城系统，即表明你同意接受本协议各项条款的约束。</span></p>
                                <p class="mt16"><span style="font-size:16px"><span>如果你不同意本协议中的条款，请勿下载、安装或以其他方式使用新麦商城系统。</span></span></p>
                                <p class="mt16"><span style="font-size:16px">一、许可您的权利</span></p>
                                <p class="mt16"><span style="font-size:16px">
								1.你可以在协议规定的约束和限制范围内根据需要对本系统进行必要的修改和美化，以适应你的运营要求。<br />
								2.您拥有使用本系统构建的全部内容的所有权，并独立承担与内容相关的法律义务。</span></p>
                                <p class="mt16"><span style="font-size:16px">二、约束和限制</span></p>
                                <p class="mt16"><span style="font-size:16px">未经新麦网络官方许可，不得对新麦商城或与之关联的商业授权进行出租、出售、抵押或发放子许可证。</span></p>
                                <p class="mt16"><span style="font-size:16px">
								1.禁止在新麦商城的整体或任何部分基础上以发展任何派生版本、修改版本或第三方版本用于出售给第三方。<br />
								2.如果你未能遵守本协议的条款，你的授权将被终止，所被许可的权利将被收回，并承担相应法律责任。本协议适用中华人民共和国法律。如你与新麦商城就本协议的相关问题发生争议，双方均有权向佛山市新麦网络科技有限公司所在地管辖法院提起诉讼。</span></p>
                                <p class="mt16"><span style="font-size:16px">三、有限担保和免责声明</span></p>
                                <p class="mt16"><span style="font-size:16px">1.新麦商城及所附带的文件是作为不提供任何明确的或隐含的赔偿或担保的形式提供的。<br />
								2.用户出于自愿而使用新麦商城，你必须了解使用新麦商城的风险，我们不承诺提供任何形式的使用担保，一切因使用本新麦商城系统而引致之任何意外、疏忽、商品交易失误、合约毁坏、诽谤、交易商品的版权或知识产权侵犯及其所造成的损失,本新麦商城系统概不负责,与本系统开发服务商无关,亦不承担任何法律责任。<br />
								3.新麦商城不对使用新麦商城构建的任何信息内容以及导致的任何版权纠纷和法律争议及后果承担责任。<br />
								4.不得利用本新麦商城系统从事违法经营活动,我们仅作为新麦商城系统开发商,对任何经营结果不做任何连带责任,系统使用过程中产生的任何法律后果我们概不负责,亦不承担任何法律责任。<br />
                                5.为了优化新麦商城运行所需要的服务器环境，新麦网络会在用户安装本软件的过程中获取网站域名、PHP版本、数据库版本等信息。<br />
								6.电子文本形式的授权协议如同双方书面签署的协议一样，具有完全的和等同的法律效力。您一旦开始安装使用新麦商城，即被视为完全理解并接受本协议的各项条款，在享有上述条款授予的权力的同时，受到相关的约束和限制。协议许可范围以外的行为，将直接违反本授权协议并构成侵权，我们有权随时终止授权，责令停止损害，并保留追究相关责任的权力。</span></p>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- 检查信息 -->
                    <?php if ($step == '2') { ?>
                        <div class="mounted-content-item show">
                            <div class="mounted-env-container">
                                <div class="mounted-item">
                                    <div class="content-header">
                                        服务器信息
                                    </div>
                                    <div class="content-table">
                                        <table class="layui-table" lay-skin="line">
                                            <colgroup>
                                                <col width="210">
                                                <col width="730">
                                            </colgroup>
                                            <thead>
                                            <tr>
                                                <th>参数</th>
                                                <th>值</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>服务器操作系统</td>
                                                <td><?php echo PHP_OS ?></td>
                                            </tr>
                                            <tr>
                                                <td>web服务器环境</td>
                                                <td><?php echo $_SERVER['SERVER_SOFTWARE']; ?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="mounted-tips mt16">PHP环境要求必须满足下列所有条件，否则系统或系统部分功能将无法使用。</div>
                                <div class="mounted-item mt16">
                                    <div class="content-header">
                                        PHP环境要求
                                    </div>
                                    <div class="content-table">
                                        <table class="layui-table" lay-skin="line">
                                            <colgroup>
                                                <col width="210">
                                                <col width="210">
                                                <col width="120">
                                                <col width="400">
                                            </colgroup>
                                            <thead>
                                            <tr>
                                                <th>选项</th>
                                                <th>要求</th>
                                                <th>状态</th>
                                                <th>说明及帮助</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>PHP版本</td>
                                                <td>7.4以上</td>
                                                <?php echo $modelInstall->correctOrFail($modelInstall->checkPHP()) ?>
                                                <td>当前版本：<?php echo @phpversion(); ?>，建议使用PHP8.0版本</td>
                                            </tr>
                                            <tr>
                                                <td>PDO_MYSQL</td>
                                                <td>支持 (强烈建议支持)</td>
                                                <?php echo $modelInstall->correctOrFail($modelInstall->checkPDOMySQL()) ?>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>allow_url_fopen</td>
                                                <td>支持 (建议支持cURL)</td>
                                                <?php echo $modelInstall->correctOrFail($modelInstall->checkCurl()) ?>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>GD2</td>
                                                <td>支持</td>
                                                <?php echo $modelInstall->correctOrFail($modelInstall->checkGd2()) ?>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>DOM</td>
                                                <td>支持</td>
                                                <?php echo $modelInstall->correctOrFail($modelInstall->checkDom()) ?>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <td>session.auto_start</td>
                                                <td>关闭</td>
                                                <?php echo $modelInstall->correctOrFail($modelInstall->checkSessionAutoStart()) ?>
                                                <td></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="mounted-tips mt16">
                                    系统要求安装目录下的runtime和upload必须可写，才能使用的所有功能。
                                </div>
                                <div class="mounted-item mt16">
                                    <div class="content-header">
                                        目录权限监测
                                    </div>
                                    <div class="content-table">
                                        <table class="layui-table" lay-skin="line">
                                            <colgroup>
                                                <col width="210">
                                                <col width="210">
                                                <col width="120">
                                                <col width="400">
                                            </colgroup>
                                            <thead>
                                            <tr>
                                                <th>目录</th>
                                                <th>要求</th>
                                                <th>状态</th>
                                                <th>说明及帮助</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>/runtime</td>
                                                <td>runtime目录可写</td>
                                                <?php echo $modelInstall->correctOrFail($modelInstall->checkDirWrite('runtime')) ?>
                                                <td><?php if($modelInstall->checkDirWrite('runtime') =='fail') echo'请给runtime目录权限，若目录不存在先新建';?></td>
                                            </tr>
                                            <tr>
                                                <td>/public/uploads</td>
                                                <td>uploads目录可写</td>
                                                <?php echo $modelInstall->correctOrFail($modelInstall->checkDirWrite('public/uploads')) ?>
                                                <td><?php if($modelInstall->checkDirWrite('public/uploads')=='fail') echo'请给public/uploads目录权限，若目录不存在先新建';?></td>
                                            </tr>
                                            <tr>
                                                <td>/data</td>
                                                <td>data目录可写</td>
                                                <?php echo $modelInstall->correctOrFail($modelInstall->checkInstallRootDirWrite('data')) ?>
                                                <td><?php if($modelInstall->checkInstallRootDirWrite('data')=='fail') echo'请给data目录权限，若目录不存在先新建';?></td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- 数据库设置 -->
                    <?php if ($step == '3') { ?>
                        <div class="mounted-content-item show">
                            <div class="mounted-item">
                                <div class="content-header">
                                    数据库选项
                                </div>
                                <div class="content-form">

                                    <div class="form-box-item">
                                        <div class="form-desc">
                                            数据库主机
                                        </div>
                                        <div>
                                            <input type="text" name="host" value="<?= $post['host'] ?>"/>
                                        </div>
                                    </div>
                                    <div class="form-box-item">
                                        <div class="form-desc">
                                            端口号
                                        </div>
                                        <div>
                                            <input type="text" name="port" value="<?= $post['port'] ?>"/>
                                        </div>
                                    </div>
                                    <div class="form-box-item">
                                        <div class="form-desc">
                                            数据库用户
                                        </div>
                                        <div>
                                            <input type="text" name="user" value="<?= $post['user'] ?>"/>
                                        </div>
                                    </div>
                                    <div class="form-box-item">
                                        <div class="form-desc">
                                            数据库名称
                                        </div>
                                        <div>
                                            <input type="text" name="name" value="<?= $post['name'] ?>"/>
                                        </div>
                                    </div>
                                    <div class="form-box-item">
                                        <div class="form-desc">
                                            数据库密码
                                        </div>
                                        <div>
                                            <input type="text" name="password" value="<?= $post['password'] ?>"/>
                                        </div>
                                    </div>
                                    <div class="form-box-item">
                                        <div class="form-desc">
                                            数据表前缀
                                        </div>
                                        <div>
                                            <input type="text" name="prefix" value="<?= $post['prefix'] ?>"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mounted-item">
                                <div class="content-header mt16">
                                    管理选项
                                </div>
                                <div class="content-form">

                                    <div class="form-box-item">
                                        <div class="form-desc">
                                            管理员账号
                                        </div>
                                        <div>
                                            <input type="text" name="admin_user" disabled="disabled" value="<?= $post['admin_user'] ?>"/>
                                        </div>
                                    </div>
                                    <div class="form-box-item">
                                        <div class="form-desc">
                                            管理员密码
                                        </div>
                                        <div>
                                            <input type="password" name="admin_password"
                                                   value="<?= $post['admin_password'] ?>"/>
                                        </div>
                                    </div>
                                    <div class="form-box-item">
                                        <div class="form-desc">
                                            确认密码
                                        </div>
                                        <div>
                                            <input type="password" name="admin_confirm_password"
                                                   value="<?= $post['admin_confirm_password'] ?>"/>
                                        </div>
                                    </div>
                                    <!--<div class="form-box-check">
                                        <div class="form-desc"></div>
                                        <div style="display: flex;align-items: center;">
                                            <input type="checkbox" name="import_test_data"
                                                   <?php if ($post['import_test_data'] == 'on'): ?>checked<?php endif; ?>
                                                   title="导入测试数据"/>
                                            <div style="color: #666666;">&nbsp;导入测试数据</div>
                                        </div>
                                    </div>-->
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- 安装中 -->
                    <?php if ($step == '4' or $step == '5') { ?>
                        <div class="mounted-content-item show">
                            <?php if ($step == '4') { ?>
                                <div id="mounting">
                                    <div class="content-header">
                                        正在安装中
                                    </div>
                                    <div class="mounting-container " id="install_message">
                                        <?php if (count($successTables) > 0): ?>
                                            <p style="margin-bottom: 4px;">成功创建数据库：<?= $post['name'] ?></p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php } ?>

                            <?php if ($step == '5') { ?>
                                <div class="show" id="mounting-success">
                                    <div class="content-header">
                                        安装成功
                                    </div>
                                    <div class="success-content">
                                        <div style="width: 48px;height: 48px;">
                                            <img src="./images/icon_mountSuccess.png"/>
                                        </div>
                                        <div class="mt16 result">安装完成，进入管理后台</div>
                                        <div class="tips">
                                            为了您站点的安全，安装完成后即可将网站根目录下的“install”文件夹删除，或者data/install.lock/目录下创建install.lock文件防止重复安装。
                                        </div>
                                        <div class="btn-group">
                                            <a class="btn" href="/admin" style="margin-left: 20px;">进入管理平台</a>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </form>
            <?php if ($step == '1') { ?>
                <div class="item-btn-group show">
                    <button class="accept-btn" onclick="goStep(<?php echo $nextStep ?>)">我已阅读并同意</button>
                </div>
            <?php } elseif (in_array($step, ['2', "3"])) { ?>
                <div class="item-btn-group show">
                    <button class="cancel-btn" onclick="cancel()" style="padding: 7px 63px;margin-right: 16px">返回
                    </button>
                    <?php if ($modelInstall->getAllowNext()): ?>
                        <button id="nextstepbut" class="accept-btn" onclick="goStep(<?php echo $nextStep ?>)" style="padding: 7px 63px;">
                            继续
                        </button>
                    <?php else: ?>
                        <button class="accept-btn" onclick="goStep(<?php echo $step ?>)" style="padding: 7px 63px;">重新检查
                        </button>
                    <?php endif; ?>
                </div>
            <?php } elseif ($step == "4") { ?>
                <div class="item-btn-group show">
                    <button class="disabled-btn" disabled="disabled">
                        <div class="layui-icon layui-icon-loading layui-anim layui-anim-rotate layui-anim-loop"></div>
                        <div style="font-size: 14px;margin-left: 7px;">正在安装中...</div>
                    </button>
                </div>
            <?php } ?>
        </div>
    </div>
    <footer>
        Copyright © 2018-<?=date('Y')?> 佛山市新麦网络科技有限公司
    </footer>
    <script src="https://www.layuicdn.com/layui/layui.js"></script>
    <?php if (count($successTables) > 0): ?>
        <script>var successTables = eval(<?=json_encode($successTables) ?>); </script>
    <?php endif; ?>
    <script src="./js/mounted.js"></script>
    </body>
    </html>
<?php if ($message != ''): ?>
    <script>alert('<?=$message; ?>');</script>
<?php endif; ?>
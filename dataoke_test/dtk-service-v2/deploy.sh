#!/bin/bash

# 大淘客服务端部署脚本
# 用法: bash deploy.sh

echo "🚀 开始部署大淘客智能转链服务..."

# 获取当前目录
DEPLOY_DIR=$(pwd)
echo "📁 部署目录: $DEPLOY_DIR"

# 检查是否为root用户
if [ "$EUID" -eq 0 ]; then
    echo "⚠️  检测到root用户，将自动设置正确的权限"
    WEB_USER="www-data"
else
    echo "ℹ️  非root用户，某些权限操作可能需要sudo"
    WEB_USER=$(whoami)
fi

echo ""
echo "🔧 开始配置文件权限..."

# 1. 创建必要的目录
echo "📁 创建必要目录..."
mkdir -p logs
mkdir -p cache
mkdir -p uploads
mkdir -p config

# 2. 设置目录权限
echo "🔐 设置目录权限..."
chmod 755 logs cache uploads config
chmod 755 src admin api cron
chmod 644 *.php *.md *.json 2>/dev/null || true

# 3. 设置日志目录权限（重要）
echo "📝 配置日志目录..."
if [ "$EUID" -eq 0 ]; then
    # root用户可以直接设置
    chown -R $WEB_USER:$WEB_USER logs cache uploads
    chmod 755 logs cache uploads
    echo "✅ 日志目录权限设置完成"
else
    # 非root用户，尝试设置权限
    chmod 777 logs cache uploads 2>/dev/null || {
        echo "⚠️  无法设置目录权限，请手动执行:"
        echo "   sudo chown -R www-data:www-data $DEPLOY_DIR/logs"
        echo "   sudo chown -R www-data:www-data $DEPLOY_DIR/cache"
        echo "   sudo chown -R www-data:www-data $DEPLOY_DIR/uploads"
    }
fi

# 4. 检查PHP扩展
echo ""
echo "🔍 检查PHP环境..."
php -v | head -1

REQUIRED_EXTENSIONS=("curl" "json" "pdo" "pdo_mysql" "mbstring")
MISSING_EXTENSIONS=()

for ext in "${REQUIRED_EXTENSIONS[@]}"; do
    if ! php -m | grep -q "^$ext$"; then
        MISSING_EXTENSIONS+=($ext)
    fi
done

if [ ${#MISSING_EXTENSIONS[@]} -eq 0 ]; then
    echo "✅ 所有必需的PHP扩展都已安装"
else
    echo "❌ 缺少以下PHP扩展: ${MISSING_EXTENSIONS[*]}"
    echo "   请安装: sudo apt-get install php-${MISSING_EXTENSIONS[*]// / php-}"
fi

# 5. 检查配置文件
echo ""
echo "⚙️  检查配置文件..."
if [ ! -f ".env" ]; then
    if [ -f ".env.example" ]; then
        cp .env.example .env
        echo "✅ 已创建.env配置文件，请编辑配置"
    else
        echo "⚠️  未找到.env配置文件，请手动创建"
    fi
else
    echo "✅ .env配置文件已存在"
fi

# 6. 测试日志写入
echo ""
echo "🧪 测试日志功能..."
php -r "
require_once 'vendor/autoload.php';
require_once 'config/config.php';
try {
    \$logger = new \Utils\Logger();
    \$logger->info('部署测试', ['deploy_time' => date('Y-m-d H:i:s')]);
    echo '✅ 日志功能测试成功\n';
} catch (Exception \$e) {
    echo '❌ 日志功能测试失败: ' . \$e->getMessage() . '\n';
    echo 'ℹ️  日志将自动使用系统日志作为备用方案\n';
}
"

# 7. 测试数据库连接
echo ""
echo "🗄️  测试数据库连接..."
php -r "
require_once 'vendor/autoload.php';
require_once 'config/config.php';
try {
    \$db = \Services\DatabaseService::getInstance();
    \$result = \$db->fetchOne('SELECT 1 as test');
    if (\$result && \$result['test'] == 1) {
        echo '✅ 数据库连接成功\n';
    } else {
        echo '❌ 数据库连接失败\n';
    }
} catch (Exception \$e) {
    echo '❌ 数据库连接失败: ' . \$e->getMessage() . '\n';
    echo 'ℹ️  请检查.env文件中的数据库配置\n';
}
"

# 8. 创建定时任务提示
echo ""
echo "⏰ 定时任务配置提示:"
echo "   请添加以下定时任务到crontab:"
echo "   # 每小时执行智能同步"
echo "   0 * * * * /usr/bin/php $DEPLOY_DIR/cron/hourly_sync.php"
echo ""
echo "   添加方法:"
echo "   crontab -e"
echo "   然后添加上述行"

# 9. 显示访问地址
echo ""
echo "🌐 服务访问地址:"
echo "   管理后台: http://your-domain.com/admin/"
echo "   API接口: http://your-domain.com/api/sync.php"
echo "   测试页面: http://your-domain.com/test_sync_page.html"

echo ""
echo "🎉 部署完成！"
echo ""
echo "📋 后续步骤:"
echo "1. 编辑 .env 文件配置数据库和API密钥"
echo "2. 配置Web服务器指向当前目录"
echo "3. 添加定时任务"
echo "4. 访问管理后台测试功能"
echo ""
echo "❓ 如果遇到权限问题，请执行:"
echo "   sudo chown -R www-data:www-data $DEPLOY_DIR"
echo "   sudo chmod -R 755 $DEPLOY_DIR"
echo "   sudo chmod -R 777 $DEPLOY_DIR/logs $DEPLOY_DIR/cache $DEPLOY_DIR/uploads"

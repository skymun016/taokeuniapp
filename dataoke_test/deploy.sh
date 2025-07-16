#!/bin/bash

# 大淘客服务端快速部署脚本
# 使用方法: chmod +x deploy.sh && ./deploy.sh

set -e

echo "=========================================="
echo "    大淘客服务端快速部署脚本"
echo "=========================================="
echo ""

# 颜色定义
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 日志函数
log_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

log_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

log_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

log_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# 检查是否为root用户
check_root() {
    if [[ $EUID -eq 0 ]]; then
        log_warning "检测到root用户，建议使用普通用户部署"
        read -p "是否继续？(y/N): " -n 1 -r
        echo
        if [[ ! $REPLY =~ ^[Yy]$ ]]; then
            exit 1
        fi
    fi
}

# 检查系统环境
check_environment() {
    log_info "检查系统环境..."
    
    # 检查操作系统
    if [[ "$OSTYPE" == "linux-gnu"* ]]; then
        log_success "操作系统: Linux"
    elif [[ "$OSTYPE" == "darwin"* ]]; then
        log_success "操作系统: macOS"
    else
        log_error "不支持的操作系统: $OSTYPE"
        exit 1
    fi
    
    # 检查PHP
    if command -v php &> /dev/null; then
        PHP_VERSION=$(php -r "echo PHP_VERSION;")
        log_success "PHP版本: $PHP_VERSION"
        
        if php -r "exit(version_compare(PHP_VERSION, '7.4.0', '<') ? 1 : 0);"; then
            log_error "PHP版本过低，需要7.4+，当前版本: $PHP_VERSION"
            exit 1
        fi
    else
        log_error "未找到PHP，请先安装PHP 7.4+"
        exit 1
    fi
    
    # 检查MySQL
    if command -v mysql &> /dev/null; then
        MYSQL_VERSION=$(mysql --version | awk '{print $5}' | sed 's/,//')
        log_success "MySQL版本: $MYSQL_VERSION"
    else
        log_warning "未找到MySQL命令，请确保MySQL已安装"
    fi
    
    # 检查Web服务器
    if command -v apache2 &> /dev/null || command -v httpd &> /dev/null; then
        log_success "Web服务器: Apache"
        WEB_SERVER="apache"
    elif command -v nginx &> /dev/null; then
        log_success "Web服务器: Nginx"
        WEB_SERVER="nginx"
    else
        log_warning "未检测到Web服务器，请手动配置"
        WEB_SERVER="none"
    fi
}

# 检查PHP扩展
check_php_extensions() {
    log_info "检查PHP扩展..."
    
    REQUIRED_EXTENSIONS=("pdo" "pdo_mysql" "curl" "json" "openssl")
    MISSING_EXTENSIONS=()
    
    for ext in "${REQUIRED_EXTENSIONS[@]}"; do
        if php -m | grep -q "^$ext$"; then
            log_success "PHP扩展 $ext: 已安装"
        else
            log_error "PHP扩展 $ext: 未安装"
            MISSING_EXTENSIONS+=($ext)
        fi
    done
    
    if [ ${#MISSING_EXTENSIONS[@]} -ne 0 ]; then
        log_error "缺少必需的PHP扩展: ${MISSING_EXTENSIONS[*]}"
        log_info "请安装缺少的扩展后重新运行脚本"
        exit 1
    fi
}

# 设置目录权限
setup_permissions() {
    log_info "设置目录权限..."
    
    # 创建必要的目录
    mkdir -p dataoke-service/cache
    mkdir -p dataoke-service/logs
    mkdir -p dataoke-service/data
    
    # 设置权限
    chmod 755 -R dataoke-service/
    chmod 777 dataoke-service/cache/
    chmod 777 dataoke-service/logs/
    chmod 777 dataoke-service/data/
    
    # 保护敏感文件
    chmod 644 dataoke-service/config/*.php
    
    # 创建.htaccess保护敏感目录
    echo "deny from all" > dataoke-service/data/.htaccess
    echo "deny from all" > dataoke-service/logs/.htaccess
    
    log_success "目录权限设置完成"
}

# 配置数据库
configure_database() {
    log_info "配置数据库..."
    
    echo "请输入数据库配置信息："
    read -p "数据库主机 [localhost]: " DB_HOST
    DB_HOST=${DB_HOST:-localhost}
    
    read -p "数据库端口 [3306]: " DB_PORT
    DB_PORT=${DB_PORT:-3306}
    
    read -p "数据库名称 [dataoke_admin]: " DB_NAME
    DB_NAME=${DB_NAME:-dataoke_admin}
    
    read -p "数据库用户名 [root]: " DB_USER
    DB_USER=${DB_USER:-root}
    
    read -s -p "数据库密码: " DB_PASS
    echo
    
    # 生成数据库配置文件
    cat > dataoke-service/config/database.php << EOF
<?php
/**
 * 数据库配置文件
 * 自动生成于: $(date)
 */

return [
    'host' => '$DB_HOST',
    'port' => '$DB_PORT',
    'database' => '$DB_NAME',
    'username' => '$DB_USER',
    'password' => '$DB_PASS',
    'charset' => 'utf8mb4'
];
?>
EOF
    
    log_success "数据库配置文件已生成"
    
    # 测试数据库连接
    log_info "测试数据库连接..."
    if php -r "
        try {
            \$config = include 'dataoke-service/config/database.php';
            \$dsn = \"mysql:host={\$config['host']};port={\$config['port']};charset={\$config['charset']}\";
            \$pdo = new PDO(\$dsn, \$config['username'], \$config['password']);
            echo 'Database connection successful';
        } catch (Exception \$e) {
            echo 'Database connection failed: ' . \$e->getMessage();
            exit(1);
        }
    "; then
        log_success "数据库连接测试成功"
    else
        log_error "数据库连接测试失败"
        exit 1
    fi
}

# 配置大淘客API
configure_dataoke() {
    log_info "配置大淘客API..."
    
    echo "请输入大淘客API配置信息："
    read -p "App Key: " DATAOKE_APP_KEY
    read -p "App Secret: " DATAOKE_APP_SECRET
    read -p "PID: " DATAOKE_PID
    read -p "API版本 [v1.2.4]: " DATAOKE_VERSION
    DATAOKE_VERSION=${DATAOKE_VERSION:-v1.2.4}
    
    # 更新配置文件
    sed -i.bak "s/'app_key' => '.*'/'app_key' => '$DATAOKE_APP_KEY'/" dataoke-service/config/config.php
    sed -i.bak "s/'app_secret' => '.*'/'app_secret' => '$DATAOKE_APP_SECRET'/" dataoke-service/config/config.php
    sed -i.bak "s/'pid' => '.*'/'pid' => '$DATAOKE_PID'/" dataoke-service/config/config.php
    sed -i.bak "s/'version' => '.*'/'version' => '$DATAOKE_VERSION'/" dataoke-service/config/config.php
    
    log_success "大淘客API配置已更新"
}

# 运行安装脚本
run_installation() {
    log_info "运行安装脚本..."
    
    if php dataoke-service/admin/install.php; then
        log_success "安装脚本执行成功"
    else
        log_error "安装脚本执行失败"
        exit 1
    fi
}

# 生成Web服务器配置
generate_web_config() {
    if [ "$WEB_SERVER" = "apache" ]; then
        log_info "生成Apache虚拟主机配置..."
        
        read -p "请输入域名 [localhost]: " DOMAIN
        DOMAIN=${DOMAIN:-localhost}
        
        cat > apache_vhost.conf << EOF
<VirtualHost *:80>
    ServerName $DOMAIN
    DocumentRoot $(pwd)
    
    <Directory $(pwd)>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog \${APACHE_LOG_DIR}/dataoke_error.log
    CustomLog \${APACHE_LOG_DIR}/dataoke_access.log combined
</VirtualHost>
EOF
        
        log_success "Apache配置已生成: apache_vhost.conf"
        log_info "请将配置添加到Apache虚拟主机配置中"
        
    elif [ "$WEB_SERVER" = "nginx" ]; then
        log_info "生成Nginx站点配置..."
        
        read -p "请输入域名 [localhost]: " DOMAIN
        DOMAIN=${DOMAIN:-localhost}
        
        cat > nginx_site.conf << EOF
server {
    listen 80;
    server_name $DOMAIN;
    root $(pwd);
    index index.php;

    location /dataoke-service/ {
        try_files \$uri \$uri/ /dataoke-service/index.php?\$query_string;
        
        location ~ \.php$ {
            fastcgi_pass unix:/var/run/php/php-fpm.sock;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
            include fastcgi_params;
        }
    }

    location ~* \.(css|js|png|jpg|jpeg|gif|ico|svg)$ {
        expires 1M;
        add_header Cache-Control "public, immutable";
    }

    location ~ /\. {
        deny all;
    }
    
    location ~* \.(log|cache|json)$ {
        deny all;
    }
}
EOF
        
        log_success "Nginx配置已生成: nginx_site.conf"
        log_info "请将配置添加到Nginx站点配置中"
    fi
}

# 显示部署结果
show_result() {
    echo ""
    echo "=========================================="
    log_success "部署完成！"
    echo "=========================================="
    echo ""
    
    echo "📁 项目目录: $(pwd)"
    echo "🌐 API地址: http://your-domain.com/dataoke-service/"
    echo "🔧 管理后台: http://your-domain.com/dataoke-service/admin/"
    echo ""
    echo "🔑 默认登录信息:"
    echo "   用户名: admin"
    echo "   密码: admin123"
    echo ""
    echo "⚠️  重要提醒:"
    echo "   1. 请立即登录管理后台修改默认密码"
    echo "   2. 配置Web服务器虚拟主机"
    echo "   3. 测试API接口功能"
    echo "   4. 设置定时任务（如需要）"
    echo ""
    echo "📖 详细文档: DEPLOYMENT_GUIDE.md"
    echo ""
}

# 主函数
main() {
    check_root
    check_environment
    check_php_extensions
    setup_permissions
    configure_database
    configure_dataoke
    run_installation
    generate_web_config
    show_result
}

# 执行主函数
main "$@"

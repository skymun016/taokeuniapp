#!/bin/bash

# 权限修复脚本 - 专门解决日志权限问题
# 用法: bash fix_permissions.sh

echo "🔧 修复大淘客服务权限问题..."

# 获取当前目录
CURRENT_DIR=$(pwd)
echo "📁 当前目录: $CURRENT_DIR"

# 检测Web服务器用户
WEB_USER="www-data"
if id "nginx" &>/dev/null; then
    WEB_USER="nginx"
elif id "apache" &>/dev/null; then
    WEB_USER="apache"
elif id "www" &>/dev/null; then
    WEB_USER="www"
fi

echo "🌐 检测到Web服务器用户: $WEB_USER"

# 1. 创建必要目录
echo "📁 创建必要目录..."
mkdir -p logs
mkdir -p cache  
mkdir -p uploads

# 2. 修复目录权限
echo "🔐 修复目录权限..."
if [ "$EUID" -eq 0 ]; then
    # Root用户 - 完整权限修复
    echo "👑 Root用户模式 - 完整权限修复"
    
    # 设置所有者
    chown -R $WEB_USER:$WEB_USER logs cache uploads
    chown $WEB_USER:$WEB_USER . 2>/dev/null || true
    
    # 设置目录权限
    chmod 755 logs cache uploads
    chmod 755 src admin api cron vendor 2>/dev/null || true
    
    # 设置文件权限
    find . -type f -name "*.php" -exec chmod 644 {} \;
    find . -type f -name "*.log" -exec chmod 644 {} \; 2>/dev/null || true
    
    echo "✅ 权限修复完成"
    
else
    # 非Root用户 - 尽力而为
    echo "👤 普通用户模式 - 尽力修复权限"
    
    # 尝试设置权限
    chmod 755 logs cache uploads 2>/dev/null || {
        echo "⚠️  无法设置目录权限，尝试更宽松的权限..."
        chmod 777 logs cache uploads 2>/dev/null || {
            echo "❌ 无法修改权限，请使用sudo执行此脚本"
            echo "   sudo bash fix_permissions.sh"
            exit 1
        }
    }
    
    echo "✅ 基础权限修复完成"
fi

# 3. 测试日志写入
echo ""
echo "🧪 测试日志写入..."
TEST_LOG="logs/permission_test.log"
TEST_MESSAGE="Permission test at $(date)"

if echo "$TEST_MESSAGE" > "$TEST_LOG" 2>/dev/null; then
    echo "✅ 日志写入测试成功"
    rm -f "$TEST_LOG"
else
    echo "❌ 日志写入测试失败"
    
    # 尝试更宽松的权限
    echo "🔄 尝试设置更宽松的权限..."
    chmod 777 logs 2>/dev/null || true
    
    if echo "$TEST_MESSAGE" > "$TEST_LOG" 2>/dev/null; then
        echo "✅ 使用777权限后日志写入成功"
        rm -f "$TEST_LOG"
    else
        echo "❌ 仍然无法写入日志，请检查:"
        echo "   1. 磁盘空间是否充足"
        echo "   2. SELinux是否阻止写入"
        echo "   3. 文件系统是否为只读"
    fi
fi

# 4. 检查SELinux状态
if command -v getenforce &> /dev/null; then
    SELINUX_STATUS=$(getenforce 2>/dev/null || echo "Unknown")
    echo ""
    echo "🛡️  SELinux状态: $SELINUX_STATUS"
    
    if [ "$SELINUX_STATUS" = "Enforcing" ]; then
        echo "⚠️  SELinux处于强制模式，可能阻止文件写入"
        echo "   临时解决方案: sudo setenforce 0"
        echo "   永久解决方案: 配置SELinux策略"
    fi
fi

# 5. 显示当前权限状态
echo ""
echo "📊 当前权限状态:"
ls -la logs/ 2>/dev/null || echo "logs目录不存在或无法访问"
echo ""
echo "📊 目录权限:"
ls -ld logs cache uploads 2>/dev/null || echo "某些目录不存在"

# 6. 提供解决方案
echo ""
echo "🔧 如果问题仍然存在，请尝试以下解决方案:"
echo ""
echo "方案1 - 使用sudo执行:"
echo "  sudo bash fix_permissions.sh"
echo ""
echo "方案2 - 手动设置权限:"
echo "  sudo chown -R $WEB_USER:$WEB_USER $CURRENT_DIR/logs"
echo "  sudo chmod 755 $CURRENT_DIR/logs"
echo ""
echo "方案3 - 临时使用777权限:"
echo "  chmod 777 $CURRENT_DIR/logs"
echo ""
echo "方案4 - 禁用文件日志（使用系统日志）:"
echo "  在.env文件中添加: LOG_TO_FILE=false"

echo ""
echo "✨ 权限修复脚本执行完成"

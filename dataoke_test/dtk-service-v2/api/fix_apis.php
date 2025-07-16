<?php
/**
 * 临时修复脚本 - 修复API文件中的响应方法
 */

// 需要修复的文件列表
$files = [
    'miniapp_goods.php',
    'miniapp_detail.php', 
    'miniapp_search.php',
    'miniapp_convert.php'
];

foreach ($files as $file) {
    $filepath = __DIR__ . '/' . $file;
    
    if (!file_exists($filepath)) {
        echo "文件不存在: $file\n";
        continue;
    }
    
    $content = file_get_contents($filepath);
    
    // 替换Helper::jsonResponse为对应的cors.php函数
    $content = str_replace('Helper::jsonResponse(200,', 'successResponse(', $content);
    $content = str_replace('Helper::jsonResponse(404,', 'errorResponse(', $content);
    $content = str_replace('Helper::jsonResponse(400,', 'errorResponse(', $content);
    $content = str_replace('Helper::jsonResponse(405,', 'errorResponse(', $content);
    $content = str_replace('Helper::jsonResponse(500,', 'errorResponse(', $content);
    
    // 修复参数顺序
    $content = preg_replace('/successResponse\(([^,]+),\s*([^,]+),\s*\[/', 'successResponse([$1], $2)', $content);
    $content = preg_replace('/errorResponse\(([^,]+),\s*(\d+),\s*null,\s*\[([^\]]+)\]\)/', 'errorResponse($1 . \': \' . $3, $2)', $content);
    
    file_put_contents($filepath, $content);
    echo "已修复: $file\n";
}

echo "修复完成！\n";
?>

<?php
/**
 * 大淘客官方SDK测试类
 * 基于大淘客官方文档实现
 */

class DataokeSDK
{
    private $appKey;
    private $appSecret;
    private $apiUrl;
    
    public function __construct($appKey, $appSecret, $apiUrl = 'https://openapi.dataoke.com/api/')
    {
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->apiUrl = rtrim($apiUrl, '/') . '/';
    }
    
    /**
     * 生成签名 - 按照大淘客官方文档要求
     * @param array $params 请求参数
     * @return string 签名
     */
    private function generateSign($params)
    {
        // 移除sign参数
        unset($params['sign']);

        // 按key排序
        ksort($params);

        // 拼接字符串 - 按照官方文档：key1value1key2value2...
        $signStr = '';
        foreach ($params as $key => $value) {
            // 只有非空值才参与签名
            if ($value !== '' && $value !== null && $value !== false) {
                $signStr .= $key . $value;
            }
        }

        // 生成签名：md5(appSecret + signStr + appSecret)
        $sign = strtoupper(md5($this->appSecret . $signStr . $this->appSecret));

        return $sign;
    }
    
    /**
     * 发送HTTP请求
     * @param string $url 请求URL
     * @param array $params 请求参数
     * @return array 响应结果
     */
    private function httpRequest($url, $params)
    {
        // 添加签名
        $params['sign'] = $this->generateSign($params);
        
        // 初始化curl
        $ch = curl_init();
        
        // 尝试GET请求方式
        $queryString = http_build_query($params);
        $getUrl = $url . '?' . $queryString;

        // 设置curl选项 - 使用GET请求
        curl_setopt_array($ch, [
            CURLOPT_URL => $getUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_USERAGENT => 'DataokeSDK/1.0',
            CURLOPT_HTTPHEADER => [
                'Accept: application/json'
            ]
        ]);
        
        // 执行请求
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        curl_close($ch);
        
        // 检查错误
        if ($error) {
            return [
                'success' => false,
                'error' => 'CURL Error: ' . $error,
                'http_code' => $httpCode
            ];
        }
        
        // 解析响应
        $data = json_decode($response, true);
        
        return [
            'success' => true,
            'http_code' => $httpCode,
            'raw_response' => $response,
            'data' => $data,
            'request_params' => $params
        ];
    }
    
    /**
     * 获取商品列表
     * @param array $options 选项参数
     * @return array 响应结果
     */
    public function getGoodsList($options = [])
    {
        $params = array_merge([
            'appKey' => $this->appKey,
            'version' => '1.2.1',
            'pageId' => 1,
            'pageSize' => 20,
            'sort' => 'total_sales_des'
        ], $options);
        
        $url = $this->apiUrl . 'goods/get-goods-list';
        
        return $this->httpRequest($url, $params);
    }
    
    /**
     * 获取分类列表
     * @return array 响应结果
     */
    public function getCategoryList()
    {
        $params = [
            'appKey' => $this->appKey,
            'version' => '1.2.1'
        ];
        
        $url = $this->apiUrl . 'category/get-category-list';
        
        return $this->httpRequest($url, $params);
    }
    
    /**
     * 搜索商品
     * @param string $keyword 搜索关键词
     * @param array $options 选项参数
     * @return array 响应结果
     */
    public function searchGoods($keyword, $options = [])
    {
        $params = array_merge([
            'appKey' => $this->appKey,
            'version' => '1.2.1',
            'keyWords' => $keyword,
            'pageId' => 1,
            'pageSize' => 20,
            'sort' => 'total_sales_des'
        ], $options);
        
        $url = $this->apiUrl . 'goods/search-goods';
        
        return $this->httpRequest($url, $params);
    }
    
    /**
     * 获取商品详情
     * @param string $goodsId 商品ID
     * @return array 响应结果
     */
    public function getGoodsDetail($goodsId)
    {
        $params = [
            'appKey' => $this->appKey,
            'version' => '1.2.1',
            'goodsId' => $goodsId
        ];
        
        $url = $this->apiUrl . 'goods/get-goods-details';
        
        return $this->httpRequest($url, $params);
    }
    
    /**
     * 高效转链
     * @param string $goodsId 商品ID
     * @param string $pid 推广位PID
     * @param array $options 选项参数
     * @return array 响应结果
     */
    public function getPrivilegeLink($goodsId, $pid, $options = [])
    {
        $params = array_merge([
            'appKey' => $this->appKey,
            'version' => '1.2.1',
            'goodsId' => $goodsId,
            'pid' => $pid
        ], $options);
        
        $url = $this->apiUrl . 'tb-service/get-privilege-link';
        
        return $this->httpRequest($url, $params);
    }
    
    /**
     * 调试签名生成过程
     * @param array $params 请求参数
     * @return array 调试信息
     */
    public function debugSign($params)
    {
        // 移除sign参数
        unset($params['sign']);

        // 按key排序
        ksort($params);

        // 拼接字符串
        $signStr = '';
        foreach ($params as $key => $value) {
            if ($value !== '' && $value !== null && $value !== false) {
                $signStr .= $key . $value;
            }
        }

        // 生成签名
        $fullSignStr = $this->appSecret . $signStr . $this->appSecret;
        $sign = strtoupper(md5($fullSignStr));

        return [
            'params' => $params,
            'sorted_params' => $params,
            'sign_string' => $signStr,
            'full_sign_string' => $fullSignStr,
            'md5_input' => $fullSignStr,
            'final_sign' => $sign,
            'app_secret' => $this->appSecret
        ];
    }

    /**
     * 测试连接
     * @return array 测试结果
     */
    public function testConnection()
    {
        echo "=== 大淘客SDK连接测试 ===\n";
        echo "APP_KEY: " . $this->appKey . "\n";
        echo "API_URL: " . $this->apiUrl . "\n\n";
        
        $results = [];
        
        // 测试1: 获取分类列表
        echo "1. 测试获取分类列表...\n";
        $categoryResult = $this->getCategoryList();
        $results['category'] = $categoryResult;
        
        if ($categoryResult['success']) {
            echo "   HTTP状态码: " . $categoryResult['http_code'] . "\n";
            if (isset($categoryResult['data']['code'])) {
                echo "   API状态码: " . $categoryResult['data']['code'] . "\n";
                echo "   API消息: " . ($categoryResult['data']['msg'] ?? 'N/A') . "\n";
                if ($categoryResult['data']['code'] == 0) {
                    echo "   ✅ 分类列表获取成功\n";
                } else {
                    echo "   ❌ 分类列表获取失败: " . ($categoryResult['data']['msg'] ?? '未知错误') . "\n";
                }
            } else {
                echo "   ❌ 响应格式异常\n";
                echo "   原始响应: " . $categoryResult['raw_response'] . "\n";
            }
        } else {
            echo "   ❌ 请求失败: " . $categoryResult['error'] . "\n";
        }
        
        echo "\n";
        
        // 测试2: 获取商品列表
        echo "2. 测试获取商品列表...\n";
        $goodsResult = $this->getGoodsList(['pageSize' => 5]);
        $results['goods'] = $goodsResult;
        
        if ($goodsResult['success']) {
            echo "   HTTP状态码: " . $goodsResult['http_code'] . "\n";
            if (isset($goodsResult['data']['code'])) {
                echo "   API状态码: " . $goodsResult['data']['code'] . "\n";
                echo "   API消息: " . ($goodsResult['data']['msg'] ?? 'N/A') . "\n";
                if ($goodsResult['data']['code'] == 0) {
                    echo "   ✅ 商品列表获取成功\n";
                    if (isset($goodsResult['data']['data']['list'])) {
                        echo "   商品数量: " . count($goodsResult['data']['data']['list']) . "\n";
                    }
                } else {
                    echo "   ❌ 商品列表获取失败: " . ($goodsResult['data']['msg'] ?? '未知错误') . "\n";
                }
            } else {
                echo "   ❌ 响应格式异常\n";
                echo "   原始响应: " . $goodsResult['raw_response'] . "\n";
            }
        } else {
            echo "   ❌ 请求失败: " . $goodsResult['error'] . "\n";
        }
        
        echo "\n";
        
        // 测试3: 搜索商品
        echo "3. 测试搜索商品...\n";
        $searchResult = $this->searchGoods('手机', ['pageSize' => 3]);
        $results['search'] = $searchResult;
        
        if ($searchResult['success']) {
            echo "   HTTP状态码: " . $searchResult['http_code'] . "\n";
            if (isset($searchResult['data']['code'])) {
                echo "   API状态码: " . $searchResult['data']['code'] . "\n";
                echo "   API消息: " . ($searchResult['data']['msg'] ?? 'N/A') . "\n";
                if ($searchResult['data']['code'] == 0) {
                    echo "   ✅ 商品搜索成功\n";
                } else {
                    echo "   ❌ 商品搜索失败: " . ($searchResult['data']['msg'] ?? '未知错误') . "\n";
                }
            } else {
                echo "   ❌ 响应格式异常\n";
                echo "   原始响应: " . $searchResult['raw_response'] . "\n";
            }
        } else {
            echo "   ❌ 请求失败: " . $searchResult['error'] . "\n";
        }
        
        echo "\n=== 测试完成 ===\n";
        
        return $results;
    }
}

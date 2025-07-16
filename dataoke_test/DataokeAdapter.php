<?php
/**
 * 大淘客API适配器
 * 基于官方SDK的封装，提供统一的接口
 */

// 引入官方SDK
require_once __DIR__ . '/openapi-sdk-php/vendor/autoload.php';

class DataokeAdapter
{
    private $appKey;
    private $appSecret;
    private $version;
    private $pid;

    /**
     * 构造函数
     * @param string $appKey 应用Key
     * @param string $appSecret 应用Secret
     * @param string $pid 推广位ID
     * @param string $version API版本
     */
    public function __construct($appKey, $appSecret, $pid, $version = 'v1.2.4')
    {
        $this->appKey = $appKey;
        $this->appSecret = $appSecret;
        $this->pid = $pid;
        $this->version = $version; // 默认版本，各API会使用各自的版本
    }

    /**
     * 获取商品列表
     * @param array $params 查询参数
     * @return array 商品列表数据
     */
    public function getGoodsList($params = [])
    {
        try {
            $client = new GetGoodsList();
            $client->setAppKey($this->appKey);
            $client->setAppSecret($this->appSecret);
            $client->setVersion($this->version);

            // 设置默认参数
            $defaultParams = [
                'pageId' => 1,
                'pageSize' => 20
            ];
            $params = array_merge($defaultParams, $params);

            $result = $client->setParams($params)->request();
            $data = json_decode($result, true);

            if ($data && isset($data['code']) && $data['code'] == 0) {
                return [
                    'success' => true,
                    'data' => $data['data'],
                    'message' => 'success'
                ];
            } else {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => $data['msg'] ?? '获取商品列表失败'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => '请求异常: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 获取超级分类
     * @return array 分类数据
     */
    public function getSuperCategory()
    {
        try {
            $client = new GetSuperCategory();
            $client->setAppKey($this->appKey);
            $client->setAppSecret($this->appSecret);
            $client->setVersion($this->version);

            $result = $client->setParams([])->request();
            $data = json_decode($result, true);

            if ($data && isset($data['code']) && $data['code'] == 0) {
                return [
                    'success' => true,
                    'data' => $data['data'],
                    'message' => 'success'
                ];
            } else {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => $data['msg'] ?? '获取分类失败'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => '请求异常: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 获取商品详情
     * @param string $goodsId 商品ID
     * @return array 商品详情数据
     */
    public function getGoodsDetails($goodsId)
    {
        try {
            $client = new GetGoodsDetails();
            $client->setAppKey($this->appKey);
            $client->setAppSecret($this->appSecret);
            $client->setVersion($this->version);

            $params = [
                'goodsId' => $goodsId
            ];

            $result = $client->setParams($params)->request();
            $data = json_decode($result, true);

            if ($data && isset($data['code']) && $data['code'] == 0) {
                return [
                    'success' => true,
                    'data' => $data['data'],
                    'message' => 'success'
                ];
            } else {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => $data['msg'] ?? '获取商品详情失败'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => '请求异常: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 生成推广链接
     * @param string $goodsId 商品ID
     * @param string $couponId 优惠券ID（可选）
     * @return array 推广链接数据
     */
    public function getPrivilegeLink($goodsIdOrParams, $couponId = '')
    {
        try {
            $client = new GetPrivilegeLink();
            $client->setAppKey($this->appKey);
            $client->setAppSecret($this->appSecret);
            $client->setVersion('v1.3.1'); // 高效转链API使用v1.3.1

            // 兼容两种调用方式
            if (is_array($goodsIdOrParams)) {
                // 新方式：传入参数数组
                $params = $goodsIdOrParams;
            } else {
                // 旧方式：传入商品ID和优惠券ID
                $params = [
                    'goodsId' => $goodsIdOrParams,
                    'pid' => $this->pid,
                    'channelId' => 'common'
                ];

                if (!empty($couponId)) {
                    $params['couponId'] = $couponId;
                }
            }

            $result = $client->setParams($params)->request();
            $data = json_decode($result, true);

            if ($data && isset($data['code']) && $data['code'] == 0) {
                return [
                    'success' => true,
                    'data' => $data['data'],
                    'message' => 'success'
                ];
            } else {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => $data['msg'] ?? '生成推广链接失败'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => '请求异常: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 搜索商品
     * @param string $keyword 搜索关键词
     * @param array $params 其他参数
     * @return array 搜索结果
     */
    public function searchGoods($keyword, $params = [])
    {
        try {
            $client = new GetDtkSearchGoods();
            $client->setAppKey($this->appKey);
            $client->setAppSecret($this->appSecret);
            $client->setVersion($this->version);

            // 设置默认参数
            $defaultParams = [
                'keyWords' => $keyword,
                'pageId' => 1,
                'pageSize' => 20
            ];
            $params = array_merge($defaultParams, $params);

            $result = $client->setParams($params)->request();
            $data = json_decode($result, true);

            if ($data && isset($data['code']) && $data['code'] == 0) {
                return [
                    'success' => true,
                    'data' => $data['data'],
                    'message' => 'success'
                ];
            } else {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => $data['msg'] ?? '搜索商品失败'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => '请求异常: ' . $e->getMessage()
            ];
        }
    }

    /**
     * 测试连接
     * @return array 测试结果
     */
    public function testConnection()
    {
        $result = $this->getGoodsList(['pageId' => 1, 'pageSize' => 1]);

        if ($result['success']) {
            return [
                'success' => true,
                'message' => '大淘客API连接成功',
                'data' => [
                    'app_key' => $this->appKey,
                    'version' => $this->version,
                    'test_time' => date('Y-m-d H:i:s')
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => '大淘客API连接失败: ' . $result['message'],
                'data' => null
            ];
        }
    }

    /**
     * 解析淘口令
     * @param string $content 淘口令内容
     * @return array 解析结果
     */
    public function parseTpwd($content)
    {
        try {
            $client = new GetParseTpwd();
            $client->setAppKey($this->appKey);
            $client->setAppSecret($this->appSecret);
            $client->setVersion($this->version);

            $params = [
                'content' => $content
            ];

            $result = $client->setParams($params)->request();
            $data = json_decode($result, true);

            if ($data && $data['code'] == 0) {
                return [
                    'success' => true,
                    'message' => 'success',
                    'data' => $data['data']
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $data['msg'] ?? '解析淘口令失败',
                    'data' => null
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => '解析淘口令异常: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * 生成淘口令
     * @param string $text 淘口令文本
     * @param string $url 商品链接
     * @return array 生成结果
     */
    public function createTkl($text, $url)
    {
        try {
            $client = new GetCreateTkl();
            $client->setAppKey($this->appKey);
            $client->setAppSecret($this->appSecret);
            $client->setVersion($this->version);

            $params = [
                'text' => $text,
                'url' => $url
            ];

            $result = $client->setParams($params)->request();
            $data = json_decode($result, true);

            if ($data && $data['code'] == 0) {
                return [
                    'success' => true,
                    'message' => 'success',
                    'data' => $data['data']
                ];
            } else {
                return [
                    'success' => false,
                    'message' => $data['msg'] ?? '生成淘口令失败',
                    'data' => null
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => '生成淘口令异常: ' . $e->getMessage(),
                'data' => null
            ];
        }
    }

    /**
     * 定时拉取商品 - 增量更新API v1.2.3
     * @param array $params 参数数组
     * @return array 商品数据
     */
    public function pullGoodsByTime($params = [])
    {
        try {
            $client = new GetPullGoodsByTime();
            $client->setAppKey($this->appKey);
            $client->setAppSecret($this->appSecret);
            $client->setVersion('v1.2.3'); // 使用定时拉取的最新版本

            // 默认参数
            $defaultParams = [
                'pageId' => '1',
                'pageSize' => 100
            ];

            // 合并参数
            $requestParams = array_merge($defaultParams, $params);

            $result = $client->setParams($requestParams)->request();
            $data = json_decode($result, true);

            if ($data && isset($data['code']) && $data['code'] == 0) {
                return [
                    'success' => true,
                    'data' => $data['data'],
                    'message' => 'success'
                ];
            } else {
                return [
                    'success' => false,
                    'data' => null,
                    'message' => $data['msg'] ?? '定时拉取商品失败'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'data' => null,
                'message' => '请求异常: ' . $e->getMessage()
            ];
        }
    }


}

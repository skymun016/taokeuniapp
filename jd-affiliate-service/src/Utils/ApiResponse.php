<?php

namespace JdAffiliate\Utils;

/**
 * API响应格式化类
 */
class ApiResponse
{
    /**
     * 成功响应
     */
    public static function success($data = null, $message = 'success')
    {
        return self::response(1, $message, $data);
    }

    /**
     * 错误响应
     */
    public static function error($message = 'error', $code = 0, $data = null)
    {
        return self::response($code, $message, $data);
    }

    /**
     * 参数错误响应
     */
    public static function paramError($message = '参数错误', $data = null)
    {
        return self::response(ERROR_CODES['PARAM_ERROR'], $message, $data);
    }

    /**
     * API错误响应
     */
    public static function apiError($message = 'API调用失败', $data = null)
    {
        return self::response(ERROR_CODES['API_ERROR'], $message, $data);
    }

    /**
     * 数据库错误响应
     */
    public static function databaseError($message = '数据库错误', $data = null)
    {
        return self::response(ERROR_CODES['DATABASE_ERROR'], $message, $data);
    }

    /**
     * 缓存错误响应
     */
    public static function cacheError($message = '缓存错误', $data = null)
    {
        return self::response(ERROR_CODES['CACHE_ERROR'], $message, $data);
    }

    /**
     * 网络错误响应
     */
    public static function networkError($message = '网络错误', $data = null)
    {
        return self::response(ERROR_CODES['NETWORK_ERROR'], $message, $data);
    }

    /**
     * 认证错误响应
     */
    public static function authError($message = '认证失败', $data = null)
    {
        return self::response(ERROR_CODES['AUTH_ERROR'], $message, $data);
    }

    /**
     * 速率限制错误响应
     */
    public static function rateLimitError($message = '请求过于频繁', $data = null)
    {
        return self::response(ERROR_CODES['RATE_LIMIT_ERROR'], $message, $data);
    }

    /**
     * 商品未找到错误响应
     */
    public static function productNotFound($message = '商品不存在', $data = null)
    {
        return self::response(ERROR_CODES['PRODUCT_NOT_FOUND'], $message, $data);
    }

    /**
     * 链接生成错误响应
     */
    public static function linkGenerateError($message = '链接生成失败', $data = null)
    {
        return self::response(ERROR_CODES['LINK_GENERATE_ERROR'], $message, $data);
    }

    /**
     * 系统错误响应
     */
    public static function systemError($message = '系统内部错误', $data = null)
    {
        return self::response(ERROR_CODES['SYSTEM_ERROR'], $message, $data);
    }

    /**
     * 分页数据响应
     */
    public static function paginated($list, $total, $page, $pageSize, $message = 'success')
    {
        $data = [
            'list' => $list,
            'total' => (int)$total,
            'page' => (int)$page,
            'pageSize' => (int)$pageSize,
            'totalPages' => (int)ceil($total / $pageSize)
        ];

        return self::success($data, $message);
    }

    /**
     * 列表数据响应
     */
    public static function list($list, $message = 'success')
    {
        $data = [
            'list' => $list,
            'total' => count($list)
        ];

        return self::success($data, $message);
    }

    /**
     * 详情数据响应
     */
    public static function detail($item, $message = 'success')
    {
        return self::success($item, $message);
    }

    /**
     * 操作成功响应
     */
    public static function operationSuccess($message = '操作成功', $data = null)
    {
        return self::success($data, $message);
    }

    /**
     * 操作失败响应
     */
    public static function operationFailed($message = '操作失败', $data = null)
    {
        return self::error($message, 0, $data);
    }

    /**
     * 核心响应方法
     */
    private static function response($code, $message, $data = null)
    {
        $response = [
            'code' => $code,
            'msg' => $message,
            'data' => $data,
            'time' => time()
        ];

        return $response;
    }

    /**
     * 输出JSON响应并退出
     */
    public static function json($response, $httpCode = 200)
    {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');
        echo Helper::jsonEncode($response);
        exit;
    }

    /**
     * 输出成功JSON响应并退出
     */
    public static function jsonSuccess($data = null, $message = 'success')
    {
        self::json(self::success($data, $message));
    }

    /**
     * 输出错误JSON响应并退出
     */
    public static function jsonError($message = 'error', $code = 0, $data = null, $httpCode = 400)
    {
        self::json(self::error($message, $code, $data), $httpCode);
    }

    /**
     * 输出参数错误JSON响应并退出
     */
    public static function jsonParamError($message = '参数错误', $data = null)
    {
        self::json(self::paramError($message, $data), 400);
    }

    /**
     * 输出未找到JSON响应并退出
     */
    public static function jsonNotFound($message = '资源不存在', $data = null)
    {
        self::json(self::error($message, 404, $data), 404);
    }

    /**
     * 输出服务器错误JSON响应并退出
     */
    public static function jsonServerError($message = '服务器内部错误', $data = null)
    {
        self::json(self::systemError($message, $data), 500);
    }

    /**
     * 输出分页JSON响应并退出
     */
    public static function jsonPaginated($list, $total, $page, $pageSize, $message = 'success')
    {
        self::json(self::paginated($list, $total, $page, $pageSize, $message));
    }

    /**
     * 输出列表JSON响应并退出
     */
    public static function jsonList($list, $message = 'success')
    {
        self::json(self::list($list, $message));
    }

    /**
     * 输出详情JSON响应并退出
     */
    public static function jsonDetail($item, $message = 'success')
    {
        self::json(self::detail($item, $message));
    }

    /**
     * 根据异常输出错误响应
     */
    public static function fromException(\Exception $e, $debug = false)
    {
        $message = $e->getMessage();
        $data = null;

        if ($debug) {
            $data = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ];
        }

        // 根据异常类型返回不同的错误码
        if ($e instanceof \InvalidArgumentException) {
            return self::paramError($message, $data);
        } elseif ($e instanceof \RuntimeException) {
            return self::systemError($message, $data);
        } else {
            return self::error($message, 0, $data);
        }
    }

    /**
     * 根据异常输出JSON错误响应并退出
     */
    public static function jsonFromException(\Exception $e, $debug = false)
    {
        $response = self::fromException($e, $debug);
        
        // 根据错误码确定HTTP状态码
        $httpCode = 500;
        if ($response['code'] === ERROR_CODES['PARAM_ERROR']) {
            $httpCode = 400;
        } elseif ($response['code'] === ERROR_CODES['PRODUCT_NOT_FOUND']) {
            $httpCode = 404;
        } elseif ($response['code'] === ERROR_CODES['AUTH_ERROR']) {
            $httpCode = 401;
        } elseif ($response['code'] === ERROR_CODES['RATE_LIMIT_ERROR']) {
            $httpCode = 429;
        }

        self::json($response, $httpCode);
    }

    /**
     * 验证响应格式
     */
    public static function validate($response)
    {
        if (!is_array($response)) {
            return false;
        }

        $required = ['code', 'msg', 'data', 'time'];
        foreach ($required as $field) {
            if (!array_key_exists($field, $response)) {
                return false;
            }
        }

        return true;
    }

    /**
     * 格式化响应数据
     */
    public static function format($response)
    {
        if (!self::validate($response)) {
            throw new \InvalidArgumentException('Invalid response format');
        }

        // 确保时间戳是整数
        $response['time'] = (int)$response['time'];

        // 确保code是整数
        $response['code'] = (int)$response['code'];

        // 确保msg是字符串
        $response['msg'] = (string)$response['msg'];

        return $response;
    }
}
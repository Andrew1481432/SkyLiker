<?php

namespace utils;

class VKFunctions {

    private const VK_VERSION = 5.201;

    private const BASE_VK_REQUEST = 'https://api.vk.com/method/';

    private function __construct() {
        // NOOP
    }

    /**
     * @param string $method
     * @param string $token
     * @param array $params
     * @return array|null
     *
     * @throws \JsonException
     */
    public static function executeMethod(string $method, string $token, array $params = []): ?array{
        $params["v"] = self::VK_VERSION;
        $params["access_token"] = $token;
        return self::vkRequest($method, $params);
    }

    /**
     * @param string $method
     * @param array $params
     * @return array|null
     *
     * @throws \JsonException
     */
    private static function vkRequest(string $method, array $params): ?array{
        $rawData = Internet::requestURL(self::BASE_VK_REQUEST . $method,
            InternetStatics::CUSTOM_REQUEST_POST, InternetStatics::CONTENT_TYPE_URLENCODED,
            [], $params);
        if($rawData == null) {
            return null;
        }
        return json_decode($rawData, true, 512, JSON_THROW_ON_ERROR);
    }

}
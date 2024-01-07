<?php

namespace utils;

class Internet {

    private function __construct() {
        // NOOP
    }

    /**
     * @param string $address
     * @param string $custom_request
     * @param string $content_type
     * @param array  $parameters_headers_args
     * @param array  $args
     *
     * @return string
     */
    public static function requestURL(string $address, string $custom_request = InternetStatics::CUSTOM_REQUEST_POST, string $content_type = InternetStatics::CONTENT_TYPE_JSON, array $parameters_headers_args = [], array $args = []): string{
        $parameters_postField = null;

        $parameters_headers = [
                'content-type' => $content_type,
            ] + $parameters_headers_args;
        if ($custom_request == InternetStatics::CUSTOM_REQUEST_POST) {
            if ($content_type == InternetStatics::CONTENT_TYPE_URLENCODED) {
                $parameters_postField = http_build_query($args);
            } elseif ($content_type == InternetStatics::CONTENT_TYPE_JSON) {
                $parameters_postField = json_encode($args, JSON_UNESCAPED_UNICODE);
            } else {
                // todo check other rest methods

                $parameters_postField = $args;
            }
        } elseif ($custom_request == InternetStatics::CUSTOM_REQUEST_GET) {
            $address .= '?' . http_build_query($args);
        }

        $curlOptHttpHeader = [];
        foreach ($parameters_headers as $qName => $qParam) {
            $curlOptHttpHeader[] = $qName . ': ' . $qParam;
        }

        $options = [
            CURLOPT_URL => $address,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FORBID_REUSE => 1,
            CURLOPT_AUTOREFERER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $custom_request,

            CURLOPT_CONNECTTIMEOUT_MS => (int)(InternetStatics::TIMEOUT_CONNECTION_CURL * 1000),
            CURLOPT_TIMEOUT_MS => (int)(InternetStatics::TIMEOUT_CONNECTION_CURL * 1000),

            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_HTTPHEADER => $curlOptHttpHeader,
        ];

        if ($parameters_postField != null) {
            $options += [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $parameters_postField
            ];
        }

        $curl = curl_init();
        curl_setopt_array($curl, $options);
        $response = curl_exec($curl);
        curl_close($curl);
        return $response;
    }

}
<?php

namespace utils;

interface InternetStatics {

    public const TIMEOUT_CONNECTION_CURL = 20;


    public const CONTENT_TYPE_URLENCODED = "application/x-www-form-urlencoded";
    public const CONTENT_TYPE_JSON = "application/json;charset=utf-8";
    public const CONTENT_TYPE_MULTIPART_FORM_DATA = "multipart/form-data";


    public const CUSTOM_REQUEST_POST = "POST";

    public const CUSTOM_REQUEST_GET = "GET";

    public const CONTENT_REQUEST_DELETE = "DELETE";

}
<?php

namespace utils;

class Config {

    /**
     * @var mixed
     * @phpstan-var array<string, mixed>
     */
    private $config = [];

    /**
     * @throws \JsonException
     */
    public function __construct(string $path) {
        // support only json format!

        if(!file_exists($path)){
            throw new \InvalidArgumentException("File not exist!");
        }

        $this->config = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
    }

    private function getValue(string $key): string{
        if(!isset($this->config[$key])) {
            throw new \InvalidArgumentException("Value ".$key." not found in file!");
        }
        return $this->config[$key];
    }

    public function getParseGroupId(): int{
        return $this->getValue("group_id_parse");
    }

    public function getPublishGroupId(): int{
        return $this->getValue("group_id_publish");
    }

    public function getUserToken(): string{
        return $this->getValue("token_user");
    }

}
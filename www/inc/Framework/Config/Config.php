<?php

namespace Hysryt\Bookmark\Framework\Config;

class Config {
    private array $data;

    public function __construct(array $data) {
        $this->data = $data;
    }

    /**
     * 設定値を取得
     * 
     * @param string $name 設定値名
     * @param mixed $default デフォルト値
     */
    public function get(string $name, $default = '') {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
        return $default;
    }
}
<?php

namespace Hysryt\Bookmark\Framework\Router;

class NotFoundRoute extends Route {
    public function __construct(string $controllerName, string $actionName) {
        $this->controllerName = $controllerName;
        $this->actionName     = $actionName;
    }

    /**
     * メソッドが $method に一致するかどうか
     * 
     * @param string $method
     * @return bool
     */
    public function isMethod(string $method): bool {
        return true;
    }

    /**
     * パスがパスパターンに一致するかどうか
     * 
     * @param string $path
     * @return bool
     */
    public function matchPathPattern(string $path): bool {
        return true;
    }

    /**
     * パスからパスパターンをもとに情報を取得する
     * 
     * @param string $path
     * @return array
     */
    public function getAttributes(string $path): array {
        return [];
    }

    /**
     * パスパターンに $data を入れてURLのパスを生成する
     * @param array $data URL生成に必要となるデータ
     */
    public function getPermalinkPath(array $data = []) {
        return '';
    }
}
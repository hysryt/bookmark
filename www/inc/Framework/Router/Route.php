<?php

namespace Hysryt\Bookmark\Framework\Router;

class Route {
    private string $method;
    private string $pathPattern;
    protected string $controllerName;
    protected string $actionName;

    public function __construct(string $method, string $pathPattern, string $controllerName, string $actionName) {
        $this->method         = $method;
        $this->pathPattern    = $pathPattern;
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
        return $this->method === $method;
    }

    /**
     * パスがパスパターンに一致するかどうか
     * 
     * @param string $path
     * @return bool
     */
    public function matchPathPattern(string $path): bool {
        $pathNodes = explode('/', trim($path, '/'));
        $pathPatternNodes = explode('/', trim($this->pathPattern, '/'));
        if (count($pathNodes) !== count($pathPatternNodes)) {
            return false;
        }
        
        for ($i = 0; $i < count($pathNodes); $i++) {
            if (preg_match("/\A\\{.*\\}\Z/", $pathPatternNodes[$i]) === 1) {
                continue;
            }

            if ($pathNodes[$i] !== $pathPatternNodes[$i]) {
                return false;
            }
        }

        return true;
    }

    /**
     * パスからパスパターンをもとに情報を取得する
     * 
     * @param string $path
     * @return array
     */
    public function getAttributes(string $path): array {
        if (! $this->matchPathPattern($path)) {
            return [];
        }

        $attributes = [];
        $pathNodes = explode('/', trim($path, '/'));
        $pathPatternNodes = explode('/', trim($this->pathPattern, '/'));
        
        for ($i = 0; $i < count($pathNodes); $i++) {
            if (preg_match("/\A\\{.*\\}\Z/", $pathPatternNodes[$i]) === 0) {
                continue;
            }

            $attributeName = trim($pathPatternNodes[$i], '{}');
            $attributes[$attributeName] = $pathNodes[$i];
        }

        return $attributes;
    }

    /**
     * パスパターンを取得
     */
    public function getPathPattern() {
        return $this->pathPattern;
    }

    /**
     * コントローラ名を取得
     * 
     * @return string
     */
    public function getControllerName(): string {
        return $this->controllerName;
    }

    /**
     * アクション名を取得
     * 
     * @return string
     */
    public function getActionName(): string {
        return $this->actionName;
    }

    /**
     * パスパターンに $data を入れてURLのパスを生成する
     * @param array $data URL生成に必要となるデータ
     */
    public function getPermalinkPath(array $data = []) {
        return preg_replace_callback('/\{([^\}]+)\}/', function($matches) use ($data) {
            return $data[$matches[1]];
        }, $this->pathPattern);
    }
}
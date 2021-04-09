<?php

namespace Hysryt\Bookmark\Framework\Container;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface {
    private array $container;
    private array $closures;

    public function __construct() {
        $this->container = [];
        $this->closures = [];
    }

    /**
     * $idに紐づくエントリを返す
     *
     * @param string $id
     *
     * @throws NotFoundExceptionInterface  エントリが存在しない
     * @throws ContainerExceptionInterface
     *
     * @return mixed Entry.
     */
    public function get(string $id) {
        if ($this->has($id)) {
            return $this->container[$id];
        }

        // クロージャが設定されている場合は実行。
        // 生成されたエントリをコンテナに保存するとともに返り値として返す。
        if (isset($this->closures[$id])) {
            $entry = $this->closures[$id]($this);
            $this->container[$id] = $entry;
            return $entry;
        }

        throw new NotFoundException($id);
    }

    /**
     * $idに紐づくエントリが存在するかどうか
     *
     * @param string $id
     *
     * @return bool
     */
    public function has(string $id): bool {
        return isset($this->container[$id]);
    }

    /**
     * コンテナに値を設定
     * 
     * @param string $id
     * @param mixed $value
     */
    public function setValue(string $id, $value) {
        $this->container[$id] = $value;
    }

    /**
     * エントリを生成するクロージャを保存。
     * クロージャは一度のみ実行される。
     */
    public function setClosure(string $id, callable $closure) {
        $this->closures[$id] = $closure;
    }
}

<?php

namespace Hysryt\Bookmark\Framework\Router;

interface PermalinkFactoryInterface {
    /**
     * パーマリンク作成
     * @param string $name ルート名
     * @param array $data 付加情報
     */
    public function create(string $name, array $data = []);
}
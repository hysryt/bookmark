<?php

namespace Hysryt\Bookmark\Lib\Html;

use DOMXPath;

/**
 * OpenGraph
 * @see https://ogp.me/
 */
class OpenGraph implements OpenGraphInterface {
    private static array $attributes = [
        'og:title',
        'og:image',
        'og:description',
        'og:url',
    ];

    private array $data;

    public static function fromDOMXPath(DOMXPath $xpath): ?OpenGraph {
        $data = [];
        foreach($xpath->query('head/meta') as $meta) {
            if (!($meta->hasAttribute('property') && $meta->hasAttribute('content'))) {
                continue;
            }

            $propertyName = $meta->getAttribute('property');
            if (in_array($propertyName, self::$attributes)) {
                $data[$propertyName] = $meta->getAttribute('content');
            }
        }

        if ($data) {
            return new OpenGraph($data);
        }

        return null;
    }

    public function __construct(array $data) {
        $this->data = $data;
    }

    /**
     * タイトル（og:title）を返す
     * @return ?string
     */
    public function getTitle(): ?string {
        if (!isset($this->data['og:title'])) return null;
        return $this->data['og:title'];
    }

    /**
     * 画像のURL（og:image）を返す
     * @return ?string
     */
    public function getImage(): ?string {
        if (!isset($this->data['og:image'])) return null;
        return $this->data['og:image'];
    }

    /**
     * URL（og:url）を返す
     * @return ?string
     */
    public function getUrl(): ?string {
        if (!isset($this->data['og:url'])) return null;
        return $this->data['og:url'];
    }

    /**
     * ディスクリプション（og:description）を返す
     * @return ?string
     */
    public function getDescription(): ?string {
        if (!isset($this->data['og:description'])) return null;
        return $this->data['og:description'];
    }
}
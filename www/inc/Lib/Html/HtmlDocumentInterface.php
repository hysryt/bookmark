<?php

namespace Hysryt\Bookmark\Lib\Html;

interface HtmlDocumentInterface {
    public function parseTitle(): ?string;
    public function parseDescription(): ?string;
    public function parseOgp(): ?OpenGraphInterface;

    /**
     * noindex または none がある場合は false を返す
     * @return bool
     */
    public function isIndexable(): bool;
}
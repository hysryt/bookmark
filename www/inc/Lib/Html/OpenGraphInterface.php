<?php

namespace Hysryt\Bookmark\Lib\Html;

interface OpenGraphInterface {
    public function getTitle(): ?string;
    public function getImage(): ?string;
    public function getUrl(): ?string;
    public function getDescription(): ?string;
}
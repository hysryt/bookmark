<?php

namespace Hysryt\Bookmark\Framework\View;

interface TemplateEngineInterface {
	public function render(string $name, array $data = []): string;
}
<?php

namespace Hysryt\Bookmark\Test;

use Hysryt\Bookmark\Framework\Exception\NotFoundException;
use Hysryt\Bookmark\Framework\View\TemplateEngine;

require_once(__DIR__ . '/../www/inc/autoload.php');

class TemplateEngineTest {
	public function testNotFoundDirectory() {
		try {
			new TemplateEngine('not_found_dir');
			throw new \Exception();
		} catch(\Exception $e) {
			assert($e instanceof NotFoundException);
		}
	}

	public function testNotFoundFile() {
		try {
			$engine = new TemplateEngine(__DIR__ . '/template');
			$engine->render('not_found_file', []);
			throw new \Exception();
		} catch(\Exception $e) {
			assert($e instanceof NotFoundException);
		}
	}

	public function testLoadFile() {
		$engine = new TemplateEngine(__DIR__ . '/template');
		$text = $engine->render('test1.php', []);
		assert($text === 'Hello');
	}

	public function testApplyData() {
		$engine = new TemplateEngine(__DIR__ . '/template');
		$text = $engine->render('test2.php', ['myoji' => '田中', 'namae' => '太郎']);
		assert($text === "姓は田中、名は太郎");
	}

	public function testEscapeData() {
		$engine = new TemplateEngine(__DIR__ . '/template');
		$text = $engine->render('test3.php', ['dainari' => '>', 'shonari' => '<', 'quote' => '\'', 'doublequote' => '"', 'amp' => '&']);
		assert($text === "&gt;&lt;&apos;&quot;&amp;");
	}
}

$test = new TemplateEngineTest();
$test->testNotFoundDirectory();
$test->testNotFoundFile();
$test->testLoadFile();
$test->testApplyData();
$test->testEscapeData();
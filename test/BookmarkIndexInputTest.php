<?php

namespace Hysryt\Bookmark\Test;

require_once(__DIR__ . '/../www/vendor/autoload.php');

use Hysryt\Bookmark\Lib\HttpMessage\Request;
use Hysryt\Bookmark\UseCase\BookmarkIndex\BookmarkIndexInput;

class BookmarkIndexInputTest {
	public function testNormal() {
		$req = new Request([], [], [], [], [], []);
		$input = new BookmarkIndexInput($req);
		assert($input->getPage() === 0);
	}

	public function testNormalPage3() {
		$req = new Request([], [], ['page' => '3'], [], [], []);
		$input = new BookmarkIndexInput($req);
		assert($input->getPage() === 3);
	}

	public function testNormalPageText() {
		$req = new Request([], [], ['page' => 'text'], [], [], []);
		$input = new BookmarkIndexInput($req);
		assert($input->getPage() === 0);
	}
}

$test = new BookmarkIndexInputTest();
$test->testNormal();
$test->testNormalPage3();
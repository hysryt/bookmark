<?php

namespace Hysryt\Bookmark\Test;

require_once(__DIR__ . '/../www/inc/autoload.php');

use Hysryt\Bookmark\Model\HtmlParser;
use Hysryt\Bookmark\Model\OpenGraph;;

class HtmlParserTest {
	/**
	 * タイトル、ディスクリプション、OGPの設定がある場合の正常挙動
	 *
	 * @return void
	 */
	public function testNormal() {
		$html = <<<EOT
		<html>
			<head>
				<title>テストタイトル</title>
				<meta name="description" content="テストディスクリプション">
				<meta property="og:url" content="https://example.com" />
				<meta property="og:type" content="article" />
				<meta property="og:title" content="テストOGPタイトル" />
				<meta property="og:description" content="テストOGPディスクリプション" />
				<meta property="og:site_name" content="テストOGPサイト名" />
				<meta property="og:image" content="https://example.com/ogp.jpg" />
			</head>
		</html>
		EOT;
		
		$parser = new HtmlParser($html);
		assert($parser->parseTitle() === 'テストタイトル');
		assert($parser->parseMetaDescription() === 'テストディスクリプション');
		
		$ogp = $parser->parseOgp();
		$compare = new OpenGraph([
			'og:url' => 'https://example.com',
			'og:type' => 'article',
			'og:title' => 'テストOGPタイトル',
			'og:description' => 'テストOGPディスクリプション',
			'og:site_name' => 'テストOGPサイト名',
			'og:image' => 'https://example.com/ogp.jpg',
		]);
		assert($ogp == $compare);
	}

	/**
	 * タイトル、ディスクリプション、OGPの設定がない場合の正常挙動
	 *
	 * @return void
	 */
	public function testNormalWithoutData() {
		$html = <<<EOT
		<html>
			<head>
			</head>
		</html>
		EOT;
		
		$parser = new HtmlParser($html);
		assert($parser->parseTitle() === null);
		assert($parser->parseMetaDescription() === null);
		
		$ogp = $parser->parseOgp();
		$compare = new OpenGraph([]);
		assert($ogp == $compare);
	}
}

$test = new HtmlParserTest();
$test->testNormal();
$test->testNormalWithoutData();
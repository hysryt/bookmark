<?php

namespace Hysryt\Bookmark\Model;

use Hysryt\Bookmark\Log\Log;
use DOMXPath;
use DOMDocument;
use DOMElement;
use Exception;

/**
 * HTMLパーサー
 */
class HtmlParser {
	private DOMXPath $domTree;

	/**
	 * コンストラクタ
	 *
	 * @param string $html
	 */
	public function __construct(string $html) {
		$dom = new DOMDocument();
		// DOMDocument::loadHTMLは特定の条件下でしかUTF-8を認識できないため、HTMLエンティティに変換してから読み込ませる
		$html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
		if(@$dom->loadHTML($html) === false) {
			Log::warning('DOMDocument::loadHTML 失敗');
			throw new Exception('html parse error');
		}
		$this->domTree = new DOMXPath($dom);
	}

	/**
	 * <title>タグ内のテキストを取得
	 *
	 * @return ?string <title>タグのテキスト。<title>タグが存在しない場合はnull。
	 */
	public function parseTitle(): ?string {
		$matches = $this->domTree->query('head/title');
		if (count($matches) === 0) {
			return null;
		}
		
		// TODO: タイトル内に<>で囲まれた文字がある場合は正しく取得できない
		return $matches->item(0)->textContent;
	}

	/**
	 * <meta name="description">のcontent属性のテキストを取得
	 *
	 * @return ?string <meta name="description">のcontent属性のテキスト。存在しない場合はnull。
	 */
	public function parseMetaDescription() {
		$matches = $this->domTree->query('head/meta[@name="description"]');
		if (count($matches) === 0) {
			return null;
		}

		// DOMElementではない、またはcontent属性を持たない場合はnull
		$element = $matches->item(0);
		if (! $element instanceof DOMElement || ! $element->hasAttribute('content')) {
			return null;
		}

		return $element->getAttribute('content');
	}

	/**
	 * OGP情報を取得
	 *
	 * @return OpenGraph
	 */
	public function parseOgp(): OpenGraph {
		$map = array();
		foreach ($this->domTree->query('head/meta') as $meta) {
			if (! $meta->hasAttribute('property') || ! $meta->hasAttribute('content')) {
				continue;
			}

			$property = $meta->getAttribute('property');
			$content = $meta->getAttribute('content');

			if (in_array($property, OpenGraph::PROPERTIES)) {
				$map[$property] = $content;
			}
		}
		return new OpenGraph($map);
	}
}

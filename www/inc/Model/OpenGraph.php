<?php

namespace Hysryt\Bookmark\Model;

use DOMXPath;

/**
 * OGP情報
 */
class OpenGraph {
	/**
	 * OGPのプロパティ名
	 */
	const PROPERTY_NAMES = [
		'og:url',
		'og:type',
		'og:title',
		'og:description',
		'og:site_name',
		'og:image',
	];

	/**
	 * OGPの値の格納用配列
	 */
	private array $map = array();

	/**
	 * コンストラクタ
	 * @param array $map
	 */
	public function __construct(array $map) {
		$this->map = $map;
	}

	/**
	 * DOMXPathインスタンスからOpenGraphインスタンスを生成
	 */
	public static function createFromDOMXpath(DOMXPath $domTree): OpenGraph {
		$map = array();
		foreach ($domTree->query('head/meta') as $meta) {
			if (! $meta->hasAttribute('property') || ! $meta->hasAttribute('content')) {
				continue;
			}

			$property = $meta->getAttribute('property');
			$content = $meta->getAttribute('content');

			if (in_array($property, self::PROPERTY_NAMES)) {
				$map[$property] = $content;
			}
		}
		return new OpenGraph($map);
	}

	/**
	 * URLを取得
	 */
	public function getUrl(): ?string {
		return isset($this->map['og:url']) ? $this->map['og:url'] : null;
	}

	/**
	 * タイプを取得
	 */
	public function getType(): ?string {
		return isset($this->map['og:type']) ? $this->map['og:type'] : null;
	}

	/**
	 * タイトルを取得
	 */
	public function getTitle(): ?string {
		return isset($this->map['og:title']) ? $this->map['og:title'] : null;
	}

	/**
	 * ディスクリプションを取得
	 */
	public function getDescription(): ?string {
		return isset($this->map['og:description']) ? $this->map['og:description'] : null;
	}

	/**
	 * サイト名を取得
	 */
	public function getSiteName(): ?string {
		return isset($this->map['og:site_name']) ? $this->map['og:site_name'] : null;
	}

	/**
	 * 画像URLを取得
	 */
	public function getImage(): ?string {
		return isset($this->map['og:image']) ? $this->map['og:image'] : null;
	}

	public function hasImage(): bool {
		return isset($this->map['og:image']) && $this->map['og:image'];
	}
}

<?php

namespace Hysryt\Bookmark\Model;

/**
 * OGP情報
 */
class OpenGraph {
	/**
	 * OGPのプロパティ名
	 */
	const PROPERTIES = [
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

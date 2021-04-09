<?php

namespace Hysryt\Bookmark\Model;

class BookmarkList {
	private array $list;

	/**
	 * コンストラクタ
	 * 
	 * @param array<Bookmark> $list
	 */
	public function __construct(array $list = array()) {
		$this->list = $list;
	}

	/**
	 * Bookmarkを追加
	 * 
	 * @param Bookmark $bookmark
	 */
	public function append(Bookmark $bookmark) {
		$this->list[] = $bookmark;
	}

	/**
	 * 指定したインデックスのBookmarkを取得
	 *
	 * @param integer $index
	 * @return Bookmark
	 */
	public function get(int $index) {
		return $this->list[$index];
	}

	/**
	 * 指定したインデックスにBookmarkを設定
	 *
	 * @param integer $index
	 * @param Bookmark $bookmark
	 * @return void
	 */
	public function set(int $index, Bookmark $bookmark) {
		$this->list[$index] = $bookmark;
	}

	/**
	 * Bookmarkを削除
	 *
	 * @param integer $index インデックス（IDではない）
	 * @return void
	 */
	public function remove(int $index) {
		unset($this->list[$index]);
	}

	/**
	 * array型として取得
	 * 
	 * @return array
	 */
	public function toArray(): array {
		return $this->list;
	}

	/**
	 * IDで昇順に並び替え
	 *
	 * @return void
	 */
	public function sortByIdAsc() {
		usort($this->list, function($a, $b) {
			return ($a->getId() > $b->getId()) ? 1 : -1;
		});
	}

	/**
	 * Bookmarkの個数を取得
	 *
	 * @return int
	 */
	public function size() {
		return count($this->list);
	}
}
<?php

namespace Hysryt\Bookmark\Repository;

use Hysryt\Bookmark\Model\Bookmark;
use Hysryt\Bookmark\Model\BookmarkList;

interface BookmarkRepositoryInterface {
	/**
	 * IDを指定してBookmarkを取得
	 *
	 * @param integer $id
	 * @return Bookmark|null 見つからなかったときはnull
	 * @throws RepositoryException
	 */
	public function findById(int $id): ?Bookmark;

	/**
	 * ID順でBookmarkを取得
	 * 
	 * @param int|null $limit 取得するデータ数。nullの場合は全て。デフォルトはnull。主にページネーションに使用。
	 * @param int $offset 先頭からのオフセット数。デフォルトは0。主にページネーションに使用。
	 * @param bool $asc 昇順かどうか。デフォルトは降順（false）。
	 * @return BookmarkList
	 * @throws RepositoryException
	 */
	public function findAllOrderById($limit = null, int $offset = 0, bool $asc = false): BookmarkList;

	/**
	 * Bookmarkを保存
	 *
	 * @param Bookmark $bookmark
	 * @return Bookmark 保存したBookmark。IDが設定されている。
	 * @throws RepositoryException
	 */
	public function add(Bookmark $bookmark): Bookmark;

	/**
	 * Bookmarkを更新
	 *
	 * @param Bookmark $bookmark
	 * @return void
	 * @throws RepositoryException
	 */
	public function update(Bookmark $bookmark);

	/**
	 * Bookmarkを削除
	 *
	 * @param int $id
	 * @return void
	 * @throws RepositoryException
	 */
	public function deleteById(int $id);
}
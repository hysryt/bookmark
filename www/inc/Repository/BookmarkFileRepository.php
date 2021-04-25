<?php

namespace Hysryt\Bookmark\Repository;

use Hysryt\Bookmark\Model\Bookmark;
use Hysryt\Bookmark\Model\BookmarkList;
use Hysryt\Bookmark\Framework\Http\Uri;
use ErrorException;
use InvalidArgumentException;
use RuntimeException;

class BookmarkFileRepository implements BookmarkRepositoryInterface {
	private string $filepath;

	/**
	 * コンストラクタ
	 * 
	 * @throws RepositoryException
	 */
	public function __construct(string $filepath) {
		if (! file_exists($filepath)) {
			if (touch($filepath) === false) {
				throw new RepositoryException('ファイルが見つからない（' . $this->filepath . '）');
			}
		}

		$this->filepath = $filepath;
		$this->loadFile();
	}

	/**
	 * IDを指定してBookmarkを取得
	 *
	 * @param integer $id
	 * @return Bookmark|null 見つからなかったときはnull
	 * @throws RepositoryException
	 */
	public function findById(int $id): ?Bookmark {
		$list = $this->loadFile();
		foreach ($list->toArray() as $bookmark) {
			if ($bookmark->getId() === $id) {
				return $bookmark;
			}
		}
		return null;
	}

	/**
	 * ID順でBookmarkを取得
	 * 
	 * @param int|null $limit 取得するデータ数。nullの場合は全て。デフォルトはnull。主にページネーションに使用。
	 * @param int $offset 先頭からのオフセット数。デフォルトは0。主にページネーションに使用。
	 * @param bool $asc 昇順かどうか。デフォルトは昇順（true）。
	 * @return BookmarkList
	 * @throws RepositoryException
	 */
	public function findAllOrderById($limit = null, int $offset = 0, bool $asc = true): BookmarkList {
		$result = array();

		$list = $this->loadFile();
		$aryList = $list->toArray();
		$listSize = count($aryList);

		if ($limit === null) {
			$limit = $listSize;
		}

		// ファイル上では既にIDの昇順に並んでいる
		// 降順の場合は反転する
		if (!$asc) {
			$aryList = array_reverse($aryList);
		}

		for ($i = $offset; $i < $offset + $limit && $i < count($aryList); $i++) {
			$result[] = $aryList[$i];
		}

		return new BookmarkList($result);
	}

	/**
	 * Bookmarkを保存
	 *
	 * @param Bookmark $bookmark
	 * @return Bookmark 保存したBookmark。IDが設定されている。
	 * @throws RepositoryException
	 */
	public function add(Bookmark $bookmark): Bookmark {
		$list = $this->loadFile();
		if ($bookmark->getId() === null) {
			$nextId = $this->getNextId($list);
			$bookmark = $bookmark->withId($nextId);
		} else {
			// 既に同じIDを持つものが登録されている場合はRepositoryException
			foreach ($list->toArray() as $target) {
				if ($target->getId() === $bookmark->getId()) {
					throw new RepositoryException();
				}
			}
		}
		$list->append($bookmark);
		$this->writeFile($list);
		return $bookmark;
	}

	/**
	 * Bookmarkを更新
	 *
	 * @param Bookmark $bookmark
	 * @return void
	 * @throws RepositoryException
	 */
	public function update(Bookmark $bookmark) {
		$list = $this->loadFile();

		$id = $bookmark->getId();
		foreach ($list->toArray() as $key => $target) {
			if ($target->getId() === $id) {
				$list->set($key, $bookmark);
				break;
			}
		}
		
		$this->writeFile($list);
	}

	/**
	 * Bookmarkを削除
	 *
	 * @param int $id
	 * @return void
	 * @throws RepositoryException
	 */
	public function deleteById(int $id) {
		$list = $this->loadFile();

		foreach ($list->toArray() as $key => $target) {
			if ($target->getId() === $id) {
				$list->remove($key);
				break;
			}
		}
		
		$this->writeFile($list);
	}

	/**
	 * ファイルからBookmarkListを読み込み
	 * 
	 * @throws RepositoryException
	 * @return BookmarkList
	 */
	private function loadFile(): BookmarkList {
		if (! file_exists($this->filepath)) {
			throw new RepositoryException('ファイルが見つからない（' . $this->filepath . '）');
		}

		if (! is_readable($this->filepath)) {
			throw new RepositoryException('ファイルの読み込み権限がない（' . $this->filepath . '）');
		}

		$serializedData = file_get_contents($this->filepath);
		if ($serializedData === false) {
			throw new ErrorException();
		}
		
		if ($serializedData === '') {
			return new BookmarkList();
		}

		$aryList = unserialize($serializedData);
		$list = new BookmarkList();
		foreach ($aryList as $aryBookmark) {
			try {
				$bookmark = $this->createBookmarkFromArray($aryBookmark);
			} catch(InvalidArgumentException $e) {
				throw new RuntimeException('不正なデータがあります', 0, $e);
			}
			$list->append($bookmark);
		}
		return $list;
	}

	/**
	 * BookmarkListをファイルに書き込み
	 * 
	 * @throws RepositoryException
	 */
	private function writeFile(BookmarkList $list) {
		if (! file_exists($this->filepath)) {
			throw new RepositoryException('ファイルが見つからない（' . $this->filepath . '）');
		}

		if (! is_readable($this->filepath)) {
			throw new RepositoryException('ファイルの読み込み権限がない（' . $this->filepath . '）');
		}

		$list->sortByIdAsc();
		$ary = [];
		foreach ($list->toArray() as $bookmark) {
			$ary[] = $this->createArrayFromBookmark($bookmark);
		}
		$serializedData = serialize($ary);
		$result = file_put_contents($this->filepath, $serializedData);
		if ($result === false) {
			throw new ErrorException();
		}
	}

	/**
	 * 次のIDを取得
	 */
	private function getNextId(BookmarkList $list) {
		$maxId = 0;
		foreach ($list->toArray() as $bookmark) {
			if ($maxId < $bookmark->getId()) {
				$maxId = $bookmark->getId();
			}
		}
		return $maxId + 1;
	}

	/**
	 * 配列データからBookmarkインスタンスを作成
	 * 
	 * @param array $data
	 * @return Bookmark
	 */
	private function createBookmarkFromArray(array $data): Bookmark {
		if (!isset($data['id']) || !isset($data['url']) || !isset($data['title']) || !isset($data['description'])) {
			throw new InvalidArgumentException();
		}

		$id = intval($data['id']);
		$url = Uri::createFromUriString($data['url']);
		$title = $data['title'];
		$description = $data['description'];
		$thumbnailFilename = isset($data['thumbnail']) ? $data['thumbnail'] : null;
		$bookmark = new Bookmark($url, $title, $description, $thumbnailFilename, $id);
		return $bookmark;
	}

	/**
	 * Bookmarkインスタンスから配列データを作成
	 * 
	 * @param Bookmark $bookmark
	 * @return array
	 */
	private function createArrayFromBookmark(Bookmark $bookmark): array {
		return [
			'id' => $bookmark->getId(),
			'url' => (string) $bookmark->getUrl(),
			'title' => $bookmark->getTitle(),
			'description' => $bookmark->getDescription(),
			'thumbnail' => $bookmark->getThumbnail(),
		];
	}
}
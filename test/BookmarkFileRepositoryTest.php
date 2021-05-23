<?php

namespace Hysryt\Bookmark\Test;

require_once(__DIR__ . '/../www/vendor/autoload.php');

use Hysryt\Bookmark\Repository\BookmarkFileRepository;
use Hysryt\Bookmark\Repository\RepositoryException;
use Hysryt\Bookmark\Model\Bookmark;
use Hysryt\Bookmark\Model\BookmarkList;
use Exception;
use Hysryt\Bookmark\BookmarkFactory;

class BookmarkFileRepositoryTest {
	/**
	 * ファイルが存在しないときに例外が発生することをテスト
	 */
	public function testErrorNotFound() {
		try {
			new BookmarkFileRepository('not_found_file');
			throw new Exception();
		} catch (Exception $e) {
			assert($e instanceof RepositoryException);
		}
	}

	/**
	 * 読み込みできていることをテスト
	 */
	public function testFindById() {
		$filepath = __DIR__ . '/test_find_by_id';
		try {
			$base = $this->createTestFindByIdOriginalData();
			file_put_contents($filepath, $base);
			
			$repository = new BookmarkFileRepository($filepath);
			$bookmark = $repository->findById(1);
			assert($bookmark->getTitle() === 'title');
			assert($bookmark->getDescription() === 'description');
			assert($bookmark->getUrl() === 'url');
			assert($bookmark->getThumbnail() === 'thumbnail');
		} finally {
			if (file_exists($filepath)) {
				unlink($filepath);
			}
		}
	}

	/**
	 * BookingListを取得できることをテスト
	 *
	 * @return void
	 */
	public function testFindAllOrderById() {
		$filepath = __DIR__ . '/test_find_all_order_by_id';
		try {
			$base = $this->createTestFindAllOrderByIdOriginalData();
			$expected = $this->createTestFindAllOrderByIdExpectedData();

			file_put_contents($filepath, $base);

			$repository = new BookmarkFileRepository($filepath);
			$bookingList = $repository->findAllOrderById(2,3);
			
			assert($expected == $bookingList);
			
		} finally {
			if (file_exists($filepath)) {
				unlink($filepath);
			}
		}
	}

	/**
	 * 降順にBookingListを取得できることをテスト
	 *
	 * @return void
	 */
	public function testFindAllOrderByIdDesc() {
		$filepath = __DIR__ . '/test_find_all_order_by_id';
		try {
			$base = $this->createTestFindAllOrderByIdDescOriginalData();
			$expected = $this->createTestFindAllOrderByIdDescExpectedData();

			file_put_contents($filepath, $base);

			$repository = new BookmarkFileRepository($filepath);
			$bookingList = $repository->findAllOrderById(3, 2, false);
			
			assert($expected == $bookingList);
			
		} finally {
			if (file_exists($filepath)) {
				unlink($filepath);
			}
		}
	}

	/**
	 * 追加できていることをテスト
	 */
	public function testAdd() {
		$filepath = __DIR__ . '/test_add';
		try {
			$base = $this->createTestAddOriginalData();
			$expected = $this->createTestAddExpectedData();

			file_put_contents($filepath, $base);

			$repository = new BookmarkFileRepository($filepath);
			$repository->add(new Bookmark(
				'url3',
				'title3',
				'description3',
				'thumbnail3',
				'/'
			));

			$saved = file_get_contents($filepath);

			assert($expected === $saved);

		} finally {
			if (file_exists($filepath)) {
				unlink($filepath);
			}
		}
	}

	/**
	 * 削除できることをテスト
	 *
	 * @return void
	 */
	public function testDeleteById() {
		$filepath = __DIR__ . '/test_delete';

		try {
			$base = $this->createTestDeleteByIdOriginalData();
			$expected = $this->createTestDeleteByIdExpectedData();

			file_put_contents($filepath, $base);

			$repository = new BookmarkFileRepository($filepath);
			$repository->deleteById(2);

			$saved = file_get_contents($filepath);

			assert($expected === $saved);

		} finally {
			if (file_exists($filepath)) {
				unlink($filepath);
			}
		}
	}

	/**
	 * 更新できることをテスト
	 *
	 * @return void
	 */
	public function testUpdate() {
		$filepath = __DIR__ . '/test_update';

		try {
			$base = $this->createTestUpdateOriginalData();
			$expected = $this->createTestUpdateExpectedData();

			file_put_contents($filepath, $base);

			$repository = new BookmarkFileRepository($filepath);
			$repository->update(new Bookmark(
				'url_updated',
				'title_updated',
				'description_updated',
				'thumbnail_updated',
				'/',
				2
			));

			$saved = file_get_contents($filepath);

			assert($expected === $saved);

		} finally {
			if (file_exists($filepath)) {
				unlink($filepath);
			}
		}
	}

	/**
	 * findByIdのテスト前データ
	 */
	private function createTestFindByIdOriginalData() {
		$data = [
			['url', 'title', 'description', 'thumbnail', '2000-01-01', 1],
		];
		return $this->createSerializedBookmarkList($data);
	}

	/**
	 * findAllOrderByIdのテスト前データ
	 */
	private function createTestFindAllOrderByIdOriginalData() {
		$data = [
			['url1', 'title1', 'description1', 'thumbnail1', '2000-01-01', 1],
			['url2', 'title2', 'description2', 'thumbnail2', '2000-01-02', 2],
			['url3', 'title3', 'description3', 'thumbnail3', '2000-01-03', 3],
			['url4', 'title4', 'description4', 'thumbnail4', '2000-01-04', 4],
			['url5', 'title5', 'description5', 'thumbnail5', '2000-01-05', 5],
			['url6', 'title6', 'description6', 'thumbnail6', '2000-01-06', 6],
			['url7', 'title7', 'description7', 'thumbnail7', '2000-01-07', 7],
			['url8', 'title8', 'description8', 'thumbnail8', '2000-01-08', 8],
		];
		return $this->createSerializedBookmarkList($data);
	}

	/**
	 * findAllOrderByIdのテスト後データ
	 */
	private function createTestFindAllOrderByIdExpectedData() {
		$data = [
			['url4', 'title4', 'description4', 'thumbnail4', '2000-01-04', 4],
			['url5', 'title5', 'description5', 'thumbnail5', '2000-01-05', 5],
		];
		return $this->createBookmarkList($data);
	}

	/**
	 * findAllOrderByIdDescのテスト前データ
	 */
	private function createTestFindAllOrderByIdDescOriginalData() {
		$data = [
			['url1', 'title1', 'description1', 'thumbnail1', '2000-01-01', 1],
			['url2', 'title2', 'description2', 'thumbnail2', '2000-01-02', 2],
			['url3', 'title3', 'description3', 'thumbnail3', '2000-01-03', 3],
			['url4', 'title4', 'description4', 'thumbnail4', '2000-01-04', 4],
			['url5', 'title5', 'description5', 'thumbnail5', '2000-01-05', 5],
			['url6', 'title6', 'description6', 'thumbnail6', '2000-01-06', 6],
			['url7', 'title7', 'description7', 'thumbnail7', '2000-01-07', 7],
			['url8', 'title8', 'description8', 'thumbnail8', '2000-01-08', 8],
		];
		return $this->createSerializedBookmarkList($data);
	}

	/**
	 * findAllOrderByIdDescのテスト後データ
	 */
	private function createTestFindAllOrderByIdDescExpectedData() {
		$data = [
			['url6', 'title6', 'description6', 'thumbnail6', '2000-01-06', 6],
			['url5', 'title5', 'description5', 'thumbnail5', '2000-01-05', 5],
			['url4', 'title4', 'description4', 'thumbnail4', '2000-01-04', 4],
		];
		return $this->createBookmarkList($data);
	}

	/**
	 * testAddのテスト前データ
	 */
	private function createTestAddOriginalData() {
		$data = [
			['url1', 'title1', 'description1', 'thumbnail1', '2000-01-01', 1],
			['url2', 'title2', 'description2', 'thumbnail2', '2000-01-02', 2],
		];
		return $this->createSerializedBookmarkList($data);
	}

	/**
	 * testAddのテスト後データ
	 */
	private function createTestAddExpectedData() {
		$data = [
			['url1', 'title1', 'description1', 'thumbnail1', '2000-01-01', 1],
			['url2', 'title2', 'description2', 'thumbnail2', '2000-01-02', 2],
			['url3', 'title3', 'description3', 'thumbnail3', '2000-01-03', 3],
		];
		return $this->createSerializedBookmarkList($data);
	}

	/**
	 * testDeleteByIdのテスト前データ
	 */
	private function createTestDeleteByIdOriginalData() {
		$data = [
			['url1', 'title1', 'description1', 'thumbnail1', '2000-01-01', 1],
			['url2', 'title2', 'description2', 'thumbnail2', '2000-01-02', 2],
			['url3', 'title3', 'description3', 'thumbnail3', '2000-01-03', 3],
		];
		return $this->createSerializedBookmarkList($data);
	}

	/**
	 * testDeleteByIdのテスト後データ
	 */
	private function createTestDeleteByIdExpectedData() {
		$data = [
			['url1', 'title1', 'description1', 'thumbnail1', '2000-01-01', 1],
			['url3', 'title3', 'description3', 'thumbnail3', '2000-01-03', 3],
		];
		return $this->createSerializedBookmarkList($data);
	}

	/**
	 * testUpdateのテスト前データ
	 */
	private function createTestUpdateOriginalData() {
		$data = [
			['url1', 'title1', 'description1', 'thumbnail1', '2000-01-01', 1],
			['url2', 'title2', 'description2', 'thumbnail2', '2000-01-02', 2],
			['url3', 'title3', 'description3', 'thumbnail3', '2000-01-03', 3],
		];
		return $this->createSerializedBookmarkList($data);
	}

	/**
	 * testUpdateのテスト後データ
	 */
	private function createTestUpdateExpectedData() {
		$data = [
			['url1', 'title1', 'description1', 'thumbnail1', '2000-01-01', 1],
			['url_updated', 'title_updated', 'description_updated', 'thumbnail_updated', '2020-12-31', 2],
			['url3', 'title3', 'description3', 'thumbnail3', '2000-01-03', 3],
		];
		return $this->createSerializedBookmarkList($data);
	}


	/**
	 * BookingListを作るためのユーティリティメソッド
	 *
	 * @param array $ary
	 * @return void
	 */
	private function createBookmarkList($ary) {
		$bookmarks = array();
		foreach ($ary as $item) {
			$bookmarks[] = new Bookmark(
				$item[0],$item[1],$item[2],$item[3], '/', $item[5]
			);
		}
		$list = new BookmarkList($bookmarks);
		return $list;
	}


	/**
	 * シリアライズ化されたBookingListを作るためのユーティリティメソッド
	 *
	 * @param array $ary
	 * @return void
	 */
	private function createSerializedBookmarkList($ary) {
		return serialize($this->createBookmarkList($ary));
	}
}

$test = new BookmarkFileRepositoryTest();
$test->testErrorNotFound();
$test->testFindById();
$test->testFindAllOrderById();
$test->testFindAllOrderByIdDesc();
$test->testAdd();
$test->testDeleteById();
$test->testUpdate();

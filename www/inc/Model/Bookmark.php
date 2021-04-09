<?php

namespace Hysryt\Bookmark\Model;

class Bookmark {
	private ?int $id;
	private string $url;
	private string $title;
	private string $description;
	private ?string $thumbnailFilename;

	/**
	 * コンストラクタ
	 *
	 * @param string $url
	 * @param string $title
	 * @param string $description
	 * @param string $thumbnailFilename
	 * @param string $thumbnailDirUrl
	 * @param int|null $id
	 */
	public function __construct(
		string $url,
		string $title,
		string $description,
		?string $thumbnailFilename,
		?int $id = null
	) {
		$this->id = $id;
		$this->url = $url;
		$this->title = $title;
		$this->description = $description;
		$this->thumbnailFilename = $thumbnailFilename;
	}

	/**
	 * IDを取得
	 *
	 * @return int|null
	 */
	public function getId(): ?int {
		return $this->id;
	}

	/**
	 * URLを取得
	 *
	 * @return string
	 */
	public function getUrl(): string {
		return $this->url;
	}

	/**
	 * タイトルを取得
	 *
	 * @return string
	 */
	public function getTitle(): string {
		return $this->title;
	}

	/**
	 * ディスクリプションを取得
	 *
	 * @return string
	 */
	public function getDescription(): string {
		return $this->description;
	}

	/**
	 * サムネイル画像のファイル名を取得
	 */
	public function getThumbnail(): ?string {
		return $this->thumbnailFilename;
	}

	/**
	 * クローンを作成し、IDを設定する
	 *
	 * @param int $id
	 * @return void
	 */
	public function withId(int $id) {
		$newBookmark = clone $this;
		$newBookmark->id = $id;
		return $newBookmark;
	}
}
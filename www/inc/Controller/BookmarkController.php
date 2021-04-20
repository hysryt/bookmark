<?php

namespace Hysryt\Bookmark\Controller;

use Exception;
use Hysryt\Bookmark\Framework\Http\Response;
use Hysryt\Bookmark\Framework\Router\PermalinkFactory;
use Hysryt\Bookmark\Framework\View\TemplateEngineInterface;
use Hysryt\Bookmark\Repository\BookmarkRepositoryInterface;
use Hysryt\Bookmark\Repository\RepositoryException;
use Hysryt\Bookmark\Service\BookmarkService;
use Hysryt\Bookmark\UseCase\BookmarkIndex\BookmarkIndexInput;
use Hysryt\Bookmark\UseCase\BookmarkCreateSubmit\BookmarkCreateSubmitInput;
use Hysryt\Bookmark\UseCase\BookmarkShow\BookmarkShowInput;
use Hysryt\Bookmark\ViewObject\BookmarkViewFactory;
use Psr\Http\Message\ServerRequestInterface;

class BookmarkController {
	private PermalinkFactory $permalinkFactory;
	private TemplateEngineInterface $templateEngine;
	private int $numPerPage;
	private BookmarkRepositoryInterface $repository;
	private BookmarkViewFactory $bookmarkViewFactory;
	private BookmarkService $bookmarkService;

	public function __construct(PermalinkFactory $permalinkFactory, TemplateEngineInterface $templateEngine, int $numPerPage, BookmarkRepositoryInterface $repository, BookmarkViewFactory $bookmarkViewFactory, BookmarkService $bookmarkService) {
		$this->permalinkFactory = $permalinkFactory;
		$this->templateEngine = $templateEngine;
		$this->numPerPage = $numPerPage;
		$this->repository = $repository;
		$this->bookmarkViewFactory = $bookmarkViewFactory;
		$this->bookmarkService = $bookmarkService;
	}

	/**
	 * ブックマーク一覧
	 *
	 * @param ServerRequestInterface $request
	 * @return Response
	 */
	public function index(ServerRequestInterface $request) {
		$input = new BookmarkIndexInput($request);

		$offset = $input->getPage() * $this->numPerPage;
		$bookmarkList = $this->repository->findAllOrderById($this->numPerPage, $offset);

		$bookmarkViewList = array_map(function($bookmark) {
			return $this->bookmarkViewFactory->create($bookmark);
		}, $bookmarkList->toArray());

		$body = $this->templateEngine->render('bookmark.index.php', [
			'bookmarkList' => $bookmarkViewList,
		]);

		return Response::ok($body);
	}

	/**
	 * ブックマーク詳細
	 * 
	 * @param ServerRequestInterface $request
	 * @return Response
	 */
	public function show(ServerRequestInterface $request) {
		$input = new BookmarkShowInput($request);

		$bookmark = $this->repository->findById($input->getId());

		// ブックマークが存在しない
		if ($bookmark === null) {
			$body = $this->templateEngine->render('bookmark.notfound.php', []);
			return Response::notFound($body);
		}

		$body = $this->templateEngine->render('bookmark.show.php', [
			'bookmark' => $this->bookmarkViewFactory->create($bookmark),
		]);
		return Response::ok($body);
	}

	/**
	 * ブックマーク追加フォーム
	 * 
	 * @param ServerRequestInterface $request
	 * @return Response
	 */
	public function createForm(ServerRequestInterface $request) {
		$body = $this->templateEngine->render('bookmark.createForm.php', [
			'actionUrl' => $this->permalinkFactory->create('bookmark.createSubmit'),
		]);
		return Response::ok($body);
	}

	/**
	 * ブックマーク追加処理
	 * 
	 * @param ServerRequestInterface $request
	 * @return Response
	 */
	public function createSubmit(ServerRequestInterface $request) {
		$input = new BookmarkCreateSubmitInput($request);
		$validateResult = $input->validate();
		if ($validateResult->isError()) {
			// 失敗（入力値エラー）
			$body = $this->templateEngine->render('bookmark.createForm.php', [
				'actionUrl' => $this->permalinkFactory->create('bookmark.createSubmit'),
				'errors' => $validateResult->getAllErrors(),
			]);
			return Response::ok($body);
		}

		try {
			$bookmark = $this->bookmarkService->createBookmark($input->getUrl());
		} catch (Exception $e) {
			// 失敗（URLから情報を取得できない）
		}

		try {
			$bookmark = $this->repository->add($bookmark);
		} catch (RepositoryException $e) {
			// 失敗（リポジトリエラー）
		}

		$body = $this->templateEngine->render('bookmark.createSuccess.php', [
			'bookmark' => $bookmark,
		]);
		return Response::ok($body);
	}
}
# 課題

## ルート
GET  /bookmarks/             BookmarkController::index
GET  /bookmarks/create/      BookmarkController::createForm
POST /bookmarks/create/      BookmarkController::createSubmit
GET  /bookmarks/{id}/        BookmarkController::show
GET  /bookmarks/{id}/edit/   BookmarkController::editForm
POST /bookmarks/{id}/edit/   BookmarkController::editSubmit
POST /bookmarks/{id}/delete/ BookmarkController::delete


# TODO
- データベース登録前の文字数チェック
- PathAttributes
  - Input
- inc/Modelの中のものを全てフレームワークから切り離す（主にException）
- Configを設定ファイル切り離す
- データベース接続
- phpcs
- file_get_contentsをcurlに変更
  - ネットアクセス自体別途クラスをつくって隠蔽化？
- URLをstringではなくUriインスタンスにする
- BookmarkView::hasThumbnail()
- createBookmark(string) -> createBookmark(Uri)
- noindex対応
- SiteInfoScraperのgetTitle/getDescriptionはOGPから取得しないように変更
- HttpClient（PSR-18）

- HtmlHttpDownloader
  - download(): HtmlDocument
- ImageHttpDownloader
  - download(): Image
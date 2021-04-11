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
- URL登録時のバリデーション
- データベース接続
- phpcs

- HtmlHttpDownloader
  - download(): HtmlDocument
- ImageHttpDownloader
  - download(): Image
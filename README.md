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
- URLにがhttps?://で始まる物以外は拒否（重要）
- データベース登録前の文字数チェック
- PathAttributes
  - Input
- inc/Modelの中のものを全てフレームワークから切り離す（主にException）
- Configを設定ファイル切り離す
- URL登録時のバリデーション
- データベース接続
- phpcs
- file_get_contentsをcurlに変更
  - ネットアクセス自体別途クラスをつくって隠蔽化？
- URLをstringではなくUriインスタンスにする
- BookmarkView::hasThumbnail()
- OpenGraph::getImage -> OpenGraph::getImageUrl()

- HtmlHttpDownloader
  - download(): HtmlDocument
- ImageHttpDownloader
  - download(): Image
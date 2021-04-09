<?php

namespace Hysryt\Bookmark\ViewObject;

use Hysryt\Bookmark\Framework\Router\PermalinkFactory;
use Hysryt\Bookmark\Model\Bookmark;

class BookmarkViewFactory {
    private string $thumbnailDirUrl;
    private PermalinkFactory $permalinkFactory;

    /**
     * コンストラクタ
     * 
     * @param string $thumbnailDirUrl
     * @param PermalinkFactory $permalinkFactory
     */
    public function __construct(string $thumbnailDirUrl, PermalinkFactory $permalinkFactory) {
        $this->thumbnailDirUrl = rtrim($thumbnailDirUrl, '/');
        $this->permalinkFactory = $permalinkFactory;
    }

    /**
     * BookmarkViewインスタンスを生成
     * 
     * @param Bookmark $bookmark
     */
    public function create(Bookmark $bookmark): BookmarkView {
        $thumbnailUrl = '';
        if ($bookmark->getThumbnail()) {
            $thumbnailUrl = $this->thumbnailDirUrl . '/' . $bookmark->getThumbnail();
        }
        
        return new BookmarkView(
            $bookmark,
            $thumbnailUrl,
            $this->permalinkFactory->create('bookmark.show', ['id' => $bookmark->getId()])
        );
    }
}
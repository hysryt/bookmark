<?php

namespace Hysryt\Bookmark\ViewObject;

use Hysryt\Bookmark\Model\Bookmark;

class BookmarkView {
    private Bookmark $bookmark;
    private string $thumbnailUrl;
    private string $permalink;

    public function __construct(Bookmark $bookmark, string $thumbnailUrl, string $permalink) {
        $this->bookmark     = $bookmark;
        $this->thumbnailUrl = $thumbnailUrl;
        $this->permalink    = $permalink;
    }

    public function getId(): int {
        return $this->bookmark->getId();
    }

    public function getUrl(): string {
        return $this->bookmark->getUrl();
    }

    public function getTitle(): string {
        return $this->bookmark->getTitle();
    }

    public function getDescription(): string {
        return $this->bookmark->getDescription();
    }

    public function hasThumbnailUrl(): bool {
        return !!($this->thumbnailUrl);
    }

    public function getThumbnailUrl(): string {
        return $this->thumbnailUrl;
    }

    public function getPermalink(): string {
        return $this->permalink;
    }
}
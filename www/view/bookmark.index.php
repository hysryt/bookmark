<?php foreach ($bookmarkList as $bookmark) : ?>
    <section>
        <h2><a href="<?php $echo($bookmark->getPermalink()) ?>"><?php $echo($bookmark->getTitle()); ?></a></h2>
        <p><?php $echo($bookmark->getId()); ?></p>
        <p><?php $echo($bookmark->getDescription()); ?></p>
        <?php if ($bookmark->hasThumbnailUrl()) : ?>
            <img src="<?php $echo($bookmark->getThumbnailUrl()); ?>">
        <?php endif; ?>
    </section>
<?php endforeach; ?>
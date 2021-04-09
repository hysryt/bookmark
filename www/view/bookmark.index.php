<?php foreach ($bookmarkList as $bookmark) : ?>
    <section>
        <h2><a href="<?php $echo($bookmark->getPermalink()) ?>"><?php $echo($bookmark->getTitle()); ?></a></h2>
        <p><?php $echo($bookmark->getId()); ?></p>
        <p><?php $echo($bookmark->getDescription()); ?></p>
        <img src="<?php $echo($bookmark->getThumbnailUrl()); ?>">
    </section>
<?php endforeach; ?>
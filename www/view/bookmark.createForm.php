<?php if (isset($errors)) : ?>
    <?php foreach($errors as $name => $messages) : ?>
        <?php foreach($messages as $message) : ?>
            <?php $echo($message); ?>
        <?php endforeach; ?>
    <?php endforeach; ?>
<?php endif; ?>
<form action="<?php echo $echo($actionUrl); ?>" method="post">
    <input type="text" name="url">
    <input type="submit">
</form>
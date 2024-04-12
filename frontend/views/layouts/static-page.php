<?php

/**
 * @var string $content
 */

$this->beginContent('@app/views/layouts/main.php');
?>

    <div class="fixed">
        <h1><strong><?= $this->title ?></strong></h1>
        <?php echo $content; ?>
    </div>

<?php $this->endContent();

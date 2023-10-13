<?php

use frontend\widgets\search\SearchWidget;

$this->title = 'Search';
$this->registerMetaTag(['name' => 'robots', 'content' => 'noindex']);
?>
<div class="fixed">
    <h1 class="h2 pt-4"><strong><?= $this->title ?></strong></h1>

    <?= SearchWidget::widget() ?>
</div>

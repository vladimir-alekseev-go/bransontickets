<?php

use frontend\widgets\search\SearchWidget;

$this->title = 'Search';
$this->registerMetaTag(['name' => 'robots', 'content' => 'noindex']);
?>
<div class="fixed">
    <h1><strong><?= $this->title ?></strong></h1>
    <?= SearchWidget::widget() ?>
</div>

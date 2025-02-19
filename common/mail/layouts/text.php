<?php

/** @var View $this view component instance */
/** @var MessageInterface $message the message being composed */
/** @var string $content main view render result */

use yii\mail\MessageInterface;
use yii\web\View;

?>
<?php $this->beginPage() ?>
<?php $this->beginBody() ?>
<?= $content ?>
<?php $this->endBody() ?>
<?php $this->endPage() ?>

<?php

/** @var yii\web\View $this */

/** @var string $name */
/** @var string $message */

/** @var Exception $exception */

use yii\helpers\Html;

$this->title = $name;
$message = nl2br(Html::encode($message));
?>
<div class="fixed">
    <div class="mb-5 pb-5">

        <h1 class="mb-5"><?= Html::encode($this->title) ?></h1>

        <?php if ($message) { ?>
            <div class="alert alert-danger">
                <?= $message ?>
            </div>
        <?php } ?>
        <p>
            The above error occurred while the Web server was processing your request.
        </p>
        <p>
            Please contact us if you think this is a server error. Thank you.
        </p>
    </div>
</div>

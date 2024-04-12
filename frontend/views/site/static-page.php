<?php

use common\models\StaticPage;

/**
 * @var StaticPage $staticPage
 */
?>

<h1 class="mb-4 mb-md-5"><?= $staticPage->title ?></h1>
<?= $staticPage->text ?>

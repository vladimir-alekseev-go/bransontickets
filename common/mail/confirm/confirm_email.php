<?php
$domain = Yii::$app->params["domain"];
?>

Dear Customer,<br><br>
Please confirm your email to complete your <a href="<?= $params['scheme'] ?><?=$domain?>"><?= $params['scheme'] ?><?=$domain?></a> registration.<br>
Go to the link below:<br><br>
<a href="<?= $params['confirm_link'] ?>"><?= $params['confirm_link'] ?></a><br><br>
Thank you,<br>
<a href="<?= $params['scheme'] ?><?=$domain?>"><?= $params['scheme'] ?><?=$domain?></a>

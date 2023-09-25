<?php

$this->context->layout = false;
?>

<?= $this->render('@app/views/components/tickets/order.php', compact('OrderForm')) ?>

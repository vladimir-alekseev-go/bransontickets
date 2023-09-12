<?php if (!empty($this->context->getItem())) { ?>
    <?php foreach ($this->context->getItem() as $key => $item) { ?>
        <?= $this->render('item', compact('item', 'key')) ?>
    <?php } ?>
<?php } else { ?>
    <div class="items-found">
        <p>No packages found.</p>
        <p>Sorry, we can't find packages on your request, try to change filter criteria.</p>
    </div>
<?php } ?>


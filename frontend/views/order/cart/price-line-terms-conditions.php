<?php

/**
 * @var array $result Result of Tripium::getPLTermsConditions()
 */

$this->context->layout = false;

if (!empty($result)) { ?>
    <?php foreach ($result as $data) { ?>
        <h3 class="mb-0"><?= $data['title'] ?></h3>
        <p><?php foreach ($data['paragraph_data'] as $paragraph_data) { ?>
                <?= strip_tags($paragraph_data)?>
            <?php } ?></p>
    <?php } ?>
<?php } ?>
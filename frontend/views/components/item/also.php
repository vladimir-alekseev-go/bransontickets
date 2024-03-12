<?php if (!empty($showsRecommended)) { ?>
    <div class="overview-may-also-like">
        <h2 class="text-center text-uppercase fw-bold">You may also like</h2>
        <div class="line mb-4"></div>
        <div class="recommended">
            <?= $this->render(
                '@app/views/site/main-page/featured',
                [
                    'showsFeatured'   => $showsRecommended,
                    'id'              => 'recommend-id',
                    'showDescription' => false,
                ]
            ) ?>
        </div>
    </div>
<?php } ?>

<div id="packages" role="tabpanel" aria-labelledby="packages-tab" class="tab-pane">
    <div class="fixed">
        <div class="packages-panel">
            <div class="title">This show is available in the following packages</div>
            <div class="vacation-packages-list">
                <?= $VPLWidget->run() ?>
            </div>
        </div>
    </div>
</div>

<?php $this->registerJs(
    "
    $('.view-more').click(function () {
        $('.more').css('display', 'flex');
        $('.view').css('display', 'none');
    });

    $('.package-1-show').click(function () {
        $('.js-package-1').css('display', 'block');
        $('.package-1-show').css('display', 'none');
        $('.package-1-hide').css('display', 'block');
    });

    $('.package-1-hide').click(function () {
        $('.js-package-1').css('display', 'none');
        $('.package-1-hide').css('display', 'none');
        $('.package-1-show').css('display', 'block');
    });

    $('.package-2-show').click(function () {
        $('.js-package-2').css('display', 'block');
        $('.package-2-show').css('display', 'none');
        $('.package-2-hide').css('display', 'block');
    });

    $('.package-2-hide').click(function () {
        $('.js-package-2').css('display', 'none');
        $('.package-2-hide').css('display', 'none');
        $('.package-2-show').css('display', 'block');
    });
    "
);
?>

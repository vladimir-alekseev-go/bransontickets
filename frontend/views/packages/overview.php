<?php if (!empty($VacationPackage->items)) { ?>
    <h3><strong>Package content</strong></h3>
    <div class="vp-overview-list white-block shadow-block" itemprop="offers" itemscope
         itemtype="http://schema.org/AggregateOffer">
        <?php foreach ($VacationPackage->items as $vpItem) { ?>
            <?php $item = $vpItem->itemExternal; ?>
            <?php if (!empty($item)) { ?>
                <div class="it" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                    <meta itemprop="name" content="<?= $item->name ?>">
                    <?php if (!empty($item->preview_id)) { ?>
                        <a href="<?= $item->getUrl() ?>" class="img" target="_blank">
                            <img class="preview"
                                 src="<?= $item->preview->url ?>"
                                 alt="<?= $item->name ?>"
                                 itemprop="image"/></a>
                    <?php } else { ?>
                        <a href="<?= $item->getUrl() ?>" class="img img-empty" target="_blank">
                            <img class="preview" src="/img/bransontickets-noimage.png"/>
                        </a>
                    <?php } ?>
                    <a itemprop="url" class="title" href="<?= $item->getUrl() ?>" target="_blank"><?= $item->name ?></a>
                    <div class="place"><?= $item->theatre->getSearchAddress() ?></div>
                    <div class="description">
                        <?= $item->getDescriptionShort(320) ?> <a class="float-end more" href="<?= $item->getUrl() ?>" target="_blank">
                            <strong>More Details <i class="fa fa-arrow-right"></i></span></strong>
                        </a>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>
        <meta itemprop="lowPrice" content="<?= min($VacationPackage->getPrices()) ?>">
        <meta itemprop="highPrice" content="<?= max($VacationPackage->getPrices()) ?>">
        <meta itemprop="offerCount" content="<?= count($VacationPackage->getPrices()) ?>">
        <meta itemprop="priceCurrency" content="USD">
    </div>
<?php } ?>
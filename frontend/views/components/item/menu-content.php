<?php

use common\models\TrAttractions;
use common\models\TrShows;

/**
 * @var TrShows|TrAttractions $model
 */

?>

<div class="fixed">
    <div class="menu-content margin-block white-block shadow-block">
        <ul class="nav nav-tabs" role="tablist">
            <li role="presentation">
                <a href="#overview" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" role="tab"
                   aria-controls="overview" aria-selected="true" class="active">Availability</a>
            </li>
            <li role="presentation">
                <a href="#schedule" id="schedule-tab" data-bs-toggle="tab" data-bs-target="#schedule" role="tab"
                   aria-controls="schedule" aria-selected="false" class="">Schedule</a>
            </li>
            <?php if (!empty($model->vacationPackages)) { ?>
                <li role="presentation">
                    <a href="#packages" id="packages-tab" data-bs-toggle="tab" data-bs-target="#packages" role="tab"
                       aria-controls="packages" aria-selected="false" class="">Packages</a>
                </li>
            <?php } ?>
        </ul>
        <div class="tab-content">
            <div id="overview" role="tabpanel" aria-labelledby="overview-tab" class="tab-pane active">
                <?= $this->render(
                    '@app/views/components/item/menu-content/overview',
                    compact('model', 'ScheduleSlider')
                ) ?>
            </div>
            <div id="schedule" role="tabpanel" aria-labelledby="schedule-tab" class="tab-pane active opacity-0">
                <?= $this->render('@app/views/components/item/menu-content/schedule', compact('model')) ?>
            </div>
            <?php if (!empty($model->vacationPackages)) { ?>
                <div id="packages" role="tabpanel" aria-labelledby="packages-tab" class="tab-pane">
                    <?= $this->render('@app/views/components/item/menu-content/packages', compact('VPLWidget')) ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

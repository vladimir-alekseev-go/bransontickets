<?php

use yii\data\ActiveDataProvider;
use yii\grid\GridView;

/**
 * @var ActiveDataProvider $dataProviderRedirects
 */
?>

<div class="panel panel-default">
    <div class="panel-body">
        <label>Redirects</label>
        <div class="order-index">
            <?= GridView::widget(
                [
                    'id' => 'order-grid',
                    'dataProvider' => $dataProviderRedirects,
                    'layout' => "{items}\n{pager}",
                    'pager' => [
                        'options' => ['class' => 'pagination pagination-sm'],
                        'hideOnSinglePage' => true,
                        'lastPageLabel' => '>>',
                        'firstPageLabel' => '<<',
                    ],
                    'columns' => [
                        'status_code',
                        'old_url',
                        'created_at',
                    ],
                ]
            ) ?>
        </div>
    </div>
</div>
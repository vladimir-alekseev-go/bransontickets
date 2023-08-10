<?php

use common\models\TrAttractions;
use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var TrAttractions $model
 */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Attractions', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attractions-view">
    <div class="panel panel-default">
        <div class="panel-body">
            <p>
                <?= Html::a(
                    'Edit',
                    ['update', 'id' => $model->id],
                    ['class' => 'btn btn-sm btn-primary']
                ) ?>
            </p>
            <?= DetailView::widget(
                [
                    'model' => $model,
                    'attributes' => [
                        'id',
                        'id_external',
                        'code',
                        'name',
                        'description',
                        [
                            'label' => 'Theatre name',
                            'attribute' => 'theatre.name',
                        ],
                        [
                            'attribute' => 'status',
                            'value' => $model->status ? 'Active' : 'Inactive',
                        ],
                        [
                            'attribute' => 'show_in_footer',
                            'value' => $model->show_in_footer ? 'Yes' : 'No',
                        ],
                        'location_external_id',
                        [
                            'label' => 'See also',
                            'value' => implode(
                                ', ',
                                TrAttractions::find()
                                    ->select(['name'])
                                    ->where(
                                        [
                                            'id_external' => $model->getTrSimilar()->select(
                                                ['similar_external_id']
                                            )->column()
                                        ]
                                    )
                                    ->column()
                            )
                        ],
                        [
                            'label' => 'Items group order',
                            'value' => /*$model->location_item_id ? $model->locationItem->location_name : */null
                        ],
                        'rank',
                        'marketing_level',
                        'voucher_procedure',
                        'weekly_schedule',
                        'on_special_text',
                        'cast_size',
                        'seats',
                        'show_length',
                        'intermissions',
                        'cut_off',
                        'tax_rate',
                        'hash_summ',
                        'photos',
                        'tags',
                        'min_rate',
                        'min_rate_source',
                        'videos',
                        [
                            'label' => 'Preview',
                            'value' => $model->preview_id ? Html::img(
                                $model->preview->url,
                                [
                                    'class' => 'attractions-view-preview',
                                    'width' => '300px'
                                ]
                            ) : null,
                            'format' => 'html',
                        ],
                        [
                            'label' => 'Banner',
                            'value' => $model->image_id ? Html::img(
                                $model->image->url,
                                [
                                    'class' => 'attractions-view-img',
                                    'width' => '300px'
                                ]
                            ) : null,
                            'format' => 'html',
                        ],
                        [
                            'attribute' => 'Display Banner',
                            'value' => $model->display_image ? 'Yes' : 'No',
                        ],
                    ],
                ]
            ) ?>
        </div>
    </div>
</div>
<?php echo $this->render('@backend/views/redirects/items-redirects', compact('dataProviderRedirects')); ?>
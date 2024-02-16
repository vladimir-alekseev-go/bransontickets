<?php

/**
 * @var $model common\models\TrPosHotels
 */

use webvimark\modules\UserManagement\components\GhostHtml;
use webvimark\modules\UserManagement\UserManagementModule;
use yii\helpers\Html;
use yii\widgets\DetailView;

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Hotels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="shows-view">

    <div class="panel panel-default">
        <div class="panel-body">

            <p>
                <?= GhostHtml::a(
                    UserManagementModule::t('back', 'Edit'),
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
                        'rank_level',
                        'marketing_level',
                        'voucher_procedure',
                        'weekly_schedule',
                        'on_special_text',
                        'photos',
                        'amenities',
                        'tags',
                        'min_rate',
                        'min_rate_source',
                        'videos',
                        [
                            'label' => 'Preview',
                            'value' => $model->preview_id ? Html::img(
                                $model->preview->url,
                                [
                                    'class' => 'shows-view-preview',
                                    'width' => '300px'
                                ]
                            ) : null,
                            'format' => 'html',
                        ],
                        [
                            'label' => 'Banner',
                            'value' => $model->image_id ? Html::img(
                                $model->image->url,
                                ['class' => 'shows-view-img', 'width' => '300px']
                            ) : null,
                            'format' => 'html',
                        ],
                        [
                            'label' => 'Display Banner',
                            'attribute' => 'display_image',
                            'value' => $model->display_image ? 'Yes' : 'No',
                        ],
                        [
                            'label' => 'Photos',
                            'value' => static function ($model) {
                                $images = $model->getRelatedPhotos()->with(['preview'])->all();
                                $result = '';
                                foreach ($images as $image) {
                                    $result .= Html::img($image->preview->url, ['width' => '100px']);
                                }
                                return $result;
                            },
                            'format' => 'html',
                        ],
                    ],
                ]
            ) ?>
        </div>
    </div>
</div>
<?php echo $this->render('@backend/views/redirects/items-redirects', compact('dataProviderRedirects')); ?>

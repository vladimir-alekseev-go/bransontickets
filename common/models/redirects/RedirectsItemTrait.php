<?php

namespace common\models\redirects;

use common\models\SiteSection;
use common\models\SiteSettings;
/*use common\models\TrAttractions;
use common\models\TrLunchs;
use common\models\TrPosHotels;
use common\models\TrPosPlHotels;*/
use common\models\TrShows;
/*use common\models\VacationPackage;*/
use Yii;

trait RedirectsItemTrait
{
    public function afterSave($insert, $changedAttributes)
    {
        /**
         * @var SiteSection $siteSection
         */
        parent::afterSave($insert, $changedAttributes);

        if (!empty($changedAttributes['code'])) {

            $category = Redirects::getCategoryByObject($this);

            if (self::isWl()) {
                $wlSection = SiteSection::getSectionByObject($this);
                if ($wlSection) {
                    $siteSection = SiteSection::find()->where(['section' => $wlSection])->one();
                    if ($siteSection) {
                        $redirect = new Redirects(
                            [
                                'status_code' => '301',
                                'old_url' => '/' . $siteSection->url . '/' . $changedAttributes['code'] . '/',
                                'category' => $category,
                                'item_id' => $this->vp_external_id ?? $this->id_external,
                            ]
                        );
                        $redirect->save();
                    }
                }
            } else {
                $partUrl = null;
                if ($this instanceof TrShows) {
                    $partUrl = Yii::$app->params['sectionsUrl']['shows'] ?? null;
                } /*elseif ($this instanceof TrAttractions) {
                    $partUrl = Yii::$app->params['sectionsUrl']['attractions'] ?? null;
                } elseif ($this instanceof TrLunchs) {
                    $partUrl = Yii::$app->params['sectionsUrl']['lunchs'] ?? null;
                } elseif ($this instanceof TrPosHotels) {
                    $partUrl = Yii::$app->params['sectionsUrl']['hotels'] ?? null;
                } elseif ($this instanceof TrPosPlHotels) {
                    $partUrl = Yii::$app->params['sectionsUrl']['hotelsPL'] ?? null;
                } elseif ($this instanceof VacationPackage) {
                    $partUrl = 'vacation-packages';
                }*/
                $redirect = new Redirects(
                    [
                        'status_code' => '301',
                        'old_url' => '/' . $partUrl . '/' . $changedAttributes['code'] . '/',
                        'category' => $category,
                        'item_id' => $this->vp_external_id ?? $this->id_external,
                    ]
                );
                $redirect->save();
            }
        }
    }

    /**
     * @return bool
     */
    private static function isWl(): bool
    {
        return !empty(SiteSection::find()->one()) && !empty(SiteSettings::find()->one());
    }
}

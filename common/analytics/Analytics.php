<?php

namespace common\analytics;

use common\helpers\MarketingItemHelper;
use common\models\Package;
use Yii;

class Analytics
{
    public const EVENT_DETAIL = '';
    public const EVENT_ADDTOCART = 'addToCart';
    public const EVENT_REMOVEFROMCART = 'removeFromCart';
    public const EVENT_CHECKOUT = 'checkout';
    public const EVENT_PURCHASE = 'purchase';

    public static function ecommerceKey(): array
    {
        return [
            self::EVENT_DETAIL => 'detail',
            self::EVENT_ADDTOCART => 'add',
            self::EVENT_REMOVEFROMCART => 'remove',
            self::EVENT_CHECKOUT => 'checkout',
            self::EVENT_PURCHASE => 'purchase',
        ];
    }

    public static function getEcommerceKey($value)
    {
        return self::ecommerceKey()[$value] ?: null;
    }

    public static function addEvent($type, $data, $actionField = []): void
    {
        $analyticsEvent = Yii::$app->session->getFlash('analyticsEvent');

        if (!is_array($analyticsEvent)) {
            $analyticsEvent = [];
        }

        $analyticsEvent[] = ['event' => $type, 'data' => $data, 'actionField' => $actionField];
        Yii::$app->session->setFlash('analyticsEvent', $analyticsEvent);
    }

    public static function getEventData(): ?array
    {
        $analytics = Yii::$app->session->getFlash('analyticsEvent');
        Yii::$app->session->setFlash('analyticsEvent', []);

        if ($analytics) {
            $dataLayerArray = [];
            $products = [];

            foreach ($analytics as $analyticsItem) {
                if ($analyticsItem['data']) {
                    foreach ($analyticsItem['data'] as $data) {
                        if (isset($data['package']) && !($data['package'] instanceof Package)) {
                            $package = $data['package'];
                            $data['package'] = new Package;
                            $data['package']->loadData($package);
                        }

                        if (!empty($data['itemId'])) {
                            $class = MarketingItemHelper::getItemClassNames()[$data['itemType']];
                            $item = $class::findOne($data['itemId']);
                        } elseif (!empty($data['itemIdExternal'])) {
                            $class = MarketingItemHelper::getItemClassNames()[$data['itemType']];
                            $item = $class::find()->where(['id_external' => $data['itemIdExternal']])->one();
                        } elseif (isset($data['package']) && !empty($data['package']->id)) {
                            $item = $data['package']->getItem();
                        }

                        if (!isset($item) && isset($data['package']) && $data['package'] instanceof Package) {
                            $item = $data['package']->getItem();
                        }

                        if (isset($data['package']) && !empty($data['package'])) {
                            $product = [
                                'id' => $data['package']->id,
                                'name' => $data['package']->name . ' - ' . $data['package']->type_name,
                                'category' => (isset($item) ? $item::TYPE : '') . (isset($item->category[0]) ? '/' .
                                        $item->category[0]->name : ''),
                                'price' => number_format($data['package']->full_total, 2, '.', ''),
                                'quantity' => 1,
                            ];
                        } elseif (!empty($item)) {
                            $product = [
                                'id' => $item->id_external,
                                'name' => $item->name,
                                'category' => $item::TYPE . (isset($item->category[0]) ? '/' . $item->category[0]->name : ''),
                            ];
                        }

                        if ($analyticsItem['event'] === self::EVENT_ADDTOCART
                            || $analyticsItem['event'] === self::EVENT_REMOVEFROMCART) {
                            $product['quantity'] = 1;
                        }

                        if (!empty($product)) {
                            $products[$analyticsItem['event']][] = $product;
                        }
                    }
                }

                $eventParams = [];
                if (!empty($products[$analyticsItem['event']])) {
                    $eventParams['products'] = $products[$analyticsItem['event']];
                }

                if ($analyticsItem['actionField']) {
                    $eventParams['actionField'] = $analyticsItem['actionField'];
                }

                $ecommerce = [self::getEcommerceKey($analyticsItem['event']) => $eventParams];

                if ($analyticsItem['event'] === self::EVENT_ADDTOCART
                    || $analyticsItem['event'] === self::EVENT_DETAIL) {
                    $ecommerce['currencyCode'] = 'USD';
                }

                $dataLayer = [
                    'ecommerce' => $ecommerce,
                ];

                if ($analyticsItem['event']) {
                    $dataLayer['event'] = $analyticsItem['event'];
                }

                $dataLayerArray[] = $dataLayer;
            }

            return $dataLayerArray;
        }
        return null;
    }
}

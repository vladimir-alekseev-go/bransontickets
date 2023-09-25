<?php

namespace common\models;

use common\helpers\StrHelper;
use common\tripium\Tripium;
use DateInterval;
use DateTime;
use Exception;
use ReflectionClass;
use ReflectionException;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\bootstrap\ActiveField;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class OrderForm extends DynamicModel
{
    public const SEATS_FIELD_SUB_NAME = '_family_pass_seat';
    public const SEATS_4_FIELD_SUB_NAME = '_family_pass_4_seat';
    public const SEATS_8_FIELD_SUB_NAME = '_family_pass_8_seat';

    public $count;
    public $post;
    public $comments;
    public $category;
    public $result;
    public $date;
    public $date_format;
    public $available_total;

    public $package_modify;
    /**
     * @var Package $package_modify_data
     */
    public $package_modify_data;
    /**
     * @var Package $package_modify_data_order
     */
    public $package_modify_data_order;
    public $package_modify_data_basket;

    public $package_date_format;

    /**
     * @var TrShows|TrAttractions $model
     */
    public $model;

    /**
     * @var TrPrices[]|TrAttractionsPrices[] $prices
     */
    public $prices = [];

    public $isCutOff = false;
    public $messageCutOff;
    public $messageCallUsToBook;
    public $messageCallUsToBookModification;

    public $allotments = [];
    public $allotmentId;

    public $recount = false;
    public $family_pass_n_pack = false;

    public function formName()
    {
        return 'OrderForm';
    }

    public static function getFormName()
    {
        return (new self())->formName();
    }

    /**
     * @param array $values
     * @param bool  $safeOnly
     */
    public function setAttributes($values, $safeOnly = true)
    {
        parent::setAttributes($values, $safeOnly);

        if (!empty($values['date']) && $values['date'] instanceof DateTime) {
            $this->date_format = $values['date']->format('Y-m-d H:i:s');
        }
        if (!empty($values['date_format'])) {
            try {
                $this->date = new DateTime($values['date_format']);
            } catch (Exception $e) {
            }
        }
    }

    public function rules()
    {
        return [
            [['date', 'model'], 'required'],
            [['result', 'family_pass_4_open', 'family_pass_8_open', 'category', 'package_modify', 'allotmentId'], 'safe'],
            [['comments'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'], //xss protection
            ['count', 'default', 'value' => 0],
            ['count', 'isCount'],
            ['isCutOff', 'checkCutOff'],
        ];
    }

    public static function getAttributeName($item, $alternativeRate = false, $suffix = '')
    {
        return 'price_id_' . $item['id'] . ($alternativeRate ? '_alternative_rate' : '_default_rate') . $suffix;
    }

    /**
     * @return TrPrices[]|TrAttractionsPrices[]
     */
    public function getPrices(): array
    {
        return $this->prices;
    }

    /**
     * @param $item
     *
     * @return string
     * @throws Exception
     */
    public function getAllotmentHash($item)
    {
        $start = new DateTime($item['start']);
        $end = new DateTime($item['end']);
        if ($this->model->getType() === TrShows::TYPE) {
            return $item['allotment_external_id'] . '__' .
                $start->format('Y_m_d_H_i_s') . '__' . $end->format('Y_m_d_H_i_s');
        }

        return $item['id_external'] . '__' . $start->format('Y_m_d_H_i_s') .
            '__' . $end->format('Y_m_d_H_i_s');
    }

    /**
     * Return attributes of a count input
     *
     * @param OrderForm   $orderForm
     * @param ActiveField $activeField
     * @param array       $options
     * @param bool        $alternativeRate
     *
     * @return array
     */
    public function getGeneralAttributeOption($options = [], $alternativeRate = false): array
    {
//        if (empty($options['class'])) {
//            $options['class'] = '';
//        }
//        $options['class'] .= $alternativeRate ? ' js-alternative-rate' : '';

        return $options;
    }

    /**
     * Return attributes of a count input
     *
     * @param TrPrices|TrAttractionsPrices $item
     * @param bool                         $alternativeRate
     *
     * @return array
     * @throws Exception
     */
    public function getAttributeOption($item, $alternativeRate = false): array
    {
        $min = 0;
        if (!empty($this->package_modify_data_order) && $alternativeRate && $item->alternative_rate) {
            foreach ($this->package_modify_data_order->getTickets() as $ticket) {
                if ((int)$ticket->id === (int)$item->price_external_id) {
                    $min = $ticket->qty;
                }
            }
        }

        if ($item['name'] === TrPrices::PRICE_TYPE_FAMILY_PASS) {
            $classFP = 'js-family-pass';
        } elseif ($item['name'] === TrPrices::PRICE_TYPE_FAMILY_PASS_4_PACK) {
            $classFP = 'js-family-pass-4';
        } elseif ($item['name'] === TrPrices::PRICE_TYPE_FAMILY_PASS_8_PACK) {
            $classFP = 'js-family-pass-8';
        } else {
            $classFP = '';
        }

        $options = [
            'type' => 'number',
            'template' => '{input}',
            'class' => ($alternativeRate ? 'js-alternative-rate' : 'js-default-rate') . ' count ' . $classFP,
            'maxlength' => 3,
            'min' => $min,
            'max' => isset($item['free_sell']) && $item['free_sell'] ? 999 : $item['available'],
            'label' => '',
            'data-ticket-id' => $item['id'],
            'data-ticket-name' => $item['name'],
            'data-price' => $alternativeRate && !empty($item->alternative_rate)
                ? $item->alternative_rate : $item->price,
            'allotment-hash' => $this->getAllotmentHash($item),
        ];

        $options['data-max'] = $options['max'];
        $options['data-min'] = $options['min'];

        if ($this->recount) {
            $options['data-recount'] = 'yes';
        }
        if ($item['name'] === TrPrices::PRICE_TYPE_FAMILY_PASS) {
            $options['data-is-family-pass'] = 'yes';
        }
        return $options;
    }

    /**
     * @param      $item
     * @param bool $alternativeRate
     *
     * @return string[]
     */
    public function getAttributeSeatOption($item, $alternativeRate = false): array
    {
        if ($item['name'] === TrPrices::PRICE_TYPE_FAMILY_PASS) {
            $classFP = 'js-family-pass-seat';
        } elseif ($item['name'] === TrPrices::PRICE_TYPE_FAMILY_PASS_4_PACK) {
            $classFP = 'js-family-pass-4-seat';
        } elseif ($item['name'] === TrPrices::PRICE_TYPE_FAMILY_PASS_8_PACK) {
            $classFP = 'js-family-pass-8-seat';
        } else {
            $classFP = '';
        }

        return [
            'type' => 'number',
            'template' => '{input}',
            'class' => ($alternativeRate ? 'js-alternative-rate' : 'js-default-rate') . ' count-family-pass ' . $classFP,
            'label' => '',
            'max' => '99',
            'data-max' => '99',
        ];
    }

    public function getPhoneNumberCallUsToBook()
    {
        if (!empty(Yii::$app->params['phoneNumberCallUsToBook'])) {
            return Yii::$app->params['phoneNumberCallUsToBook'];
        }

        return '1-417-337-8455';
    }

    public function getPhoneNumberCutOff()
    {
        if (!empty(Yii::$app->params['phoneNumberCutOff'])) {
            return Yii::$app->params['phoneNumberCutOff'];
        }

        return '1-417-337-8455';
    }

    /**
     * @throws InvalidConfigException
     */
    public function initData()
    {
        if (!isset($this->date)) {
            throw new InvalidConfigException('The "date" not set!');
        }
        if (!($this->date instanceof DateTime)) {
            $this->date = new DateTime($this->date);
        }
        if (!isset($this->model)) {
            throw new InvalidConfigException('The "model" not set!');
        }
        if (!($this->model instanceof ActiveRecord)) {
            throw new InvalidConfigException('The type of model is wrong!');
        }

        $this->isCutOff = false;
        if ($this->model->cut_off
            && (
                $this->model instanceof TrShows
                || ($this->model instanceof TrAttractions && $this->date->format('H:i') != '00:00')
            )
        ) {
            $this->isCutOff = time() + $this->model->cut_off * 3600 > $this->date->format('U');
        } elseif ($this->model instanceof TrAttractions && $this->date->format('H:i') == '00:00') {
            $date = new DateTime();
            $date->add(new DateInterval('P1D'));
            $this->isCutOff = $date->format('H:i') === '23:59'
                && $date->format('Y-m-d') === $this->date->format('Y-m-d');
        }

        $phone = $this->getPhoneNumberCutOff();
        $phone = isset(Yii::$app->params['siteType']) && Yii::$app->params['siteType'] === 'mobile' ? '<a href="tel:' . $phone . '">' . $phone . '</a>' : $phone;
        $this->messageCutOff = 'Please call us at ' . $phone . ' and we may be able to book this for you via phone. We are unable to book this online due to time restraints.';
        $this->messageCallUsToBook = 'Thank you for your interest! Please call us at ' . $this->getPhoneNumberCallUsToBook(
            ) . ' and we will be happy to book this for you. Due to the nature of this activity, it must be reserved by phone and is not able to be booked online.';
        $this->messageCallUsToBookModification = 'Please call ' . $this->getPhoneNumberCallUsToBook(
            ) . ' and we can assist you with this item.<br>Changes and/or cancellations for this item are not able to be done online.';

        $this->category = $this->model->getType();
    }

    /**
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function initPrice()
    {
        $this->prices = $this->getPriceByDate();

        if ($this->package_modify && empty($this->prices)) {
            $price_query_near = $this->model
                ->getAvailablePrices()
                ->select('start as date')
                ->orderby('start')
                ->asArray()
                ->one();
            if (!empty($price_query_near)) {
                $this->prices = $this->getPriceByDate(new DateTime($price_query_near['date']));
            }
        }

        foreach ($this->prices as $price) {
            $this->addRules($price);
            $this->addRules($price, true);
        }

        foreach ($this->prices as $p) {
            if ($p['name'] === TrPrices::PRICE_TYPE_FAMILY_PASS_4_PACK
                || $p['name'] === TrPrices::PRICE_TYPE_FAMILY_PASS_8_PACK
                || $p['name'] === TrPrices::PRICE_TYPE_FAMILY_PASS) {
                $this->recount = true;
            }
            if ($p['name'] === TrPrices::PRICE_TYPE_FAMILY_PASS_4_PACK
                || $p['name'] === TrPrices::PRICE_TYPE_FAMILY_PASS_8_PACK) {
                $this->family_pass_n_pack = true;
            }
        }
        $this->initAllotments();
    }

    public function attributeLabels()
    {
        $labels = [];
        foreach ($this->prices as $price) {
            $labels[self::getAttributeName($price)] = $price->name;
            $labels[self::getAttributeName($price, false, self::SEATS_FIELD_SUB_NAME)] = 'Seats of ' . $price->name;
            $labels[self::getAttributeName($price, false, self::SEATS_4_FIELD_SUB_NAME)] = 'Seats of ' . $price->name;
            $labels[self::getAttributeName($price, false, self::SEATS_8_FIELD_SUB_NAME)] = 'Seats of ' . $price->name;
            $labels[self::getAttributeName($price, true)] = $price->name;
            $labels[self::getAttributeName($price, true, self::SEATS_FIELD_SUB_NAME)] = 'Seats of ' . $price->name;
            $labels[self::getAttributeName($price, true, self::SEATS_4_FIELD_SUB_NAME)] = 'Seats of ' . $price->name;
            $labels[self::getAttributeName($price, true, self::SEATS_8_FIELD_SUB_NAME)] = 'Seats of ' . $price->name;
        }
        return $labels;
    }

    private function addRules($price, $alternativeRate = false): void
    {
        $this->defineAttribute(self::getAttributeName($price, $alternativeRate));
        $this->addRule(self::getAttributeName($price, $alternativeRate), 'integer');
        if ($price->name === TrPrices::PRICE_TYPE_FAMILY_PASS) {
            $this->defineAttribute(self::getAttributeName($price, $alternativeRate, self::SEATS_FIELD_SUB_NAME));
            $this->addRule(self::getAttributeName($price, $alternativeRate, self::SEATS_FIELD_SUB_NAME), 'integer');
            $this->addRule(
                self::getAttributeName($price, $alternativeRate, self::SEATS_FIELD_SUB_NAME),
                'default',
                ['value' => 0]
            );
            $this->addRule(self::getAttributeName($price, $alternativeRate, self::SEATS_FIELD_SUB_NAME), 'familyPass');
            $this->addRule(
                self::getAttributeName($price, $alternativeRate, self::SEATS_FIELD_SUB_NAME),
                'required',
                $this->ruleFamilyPassOption($price, $alternativeRate)
            );
        } elseif ($price->name === TrPrices::PRICE_TYPE_FAMILY_PASS_4_PACK) {
            $this->defineAttribute(self::getAttributeName($price, $alternativeRate, self::SEATS_4_FIELD_SUB_NAME));
            $this->addRule(
                self::getAttributeName($price, $alternativeRate, self::SEATS_4_FIELD_SUB_NAME),
                'integer'
            );
            $this->addRule(
                self::getAttributeName($price, $alternativeRate, self::SEATS_4_FIELD_SUB_NAME),
                'default',
                ['value' => 0]
            );
            $this->addRule(
                self::getAttributeName($price, $alternativeRate, self::SEATS_4_FIELD_SUB_NAME),
                'familyPass_4'
            );
            $this->addRule(
                self::getAttributeName($price, $alternativeRate, self::SEATS_4_FIELD_SUB_NAME),
                'required',
                $this->ruleFamilyPassOption($price, $alternativeRate)
            );
        } elseif ($price->name === TrPrices::PRICE_TYPE_FAMILY_PASS_8_PACK) {
            $this->defineAttribute(self::getAttributeName($price, $alternativeRate, self::SEATS_8_FIELD_SUB_NAME));
            $this->addRule(
                self::getAttributeName($price, $alternativeRate, self::SEATS_8_FIELD_SUB_NAME),
                'integer'
            );
            $this->addRule(
                self::getAttributeName($price, $alternativeRate, self::SEATS_8_FIELD_SUB_NAME),
                'default',
                ['value' => 0]
            );
            $this->addRule(
                self::getAttributeName($price, $alternativeRate, self::SEATS_8_FIELD_SUB_NAME),
                'familyPass_8'
            );
            $this->addRule(
                self::getAttributeName($price, $alternativeRate, self::SEATS_8_FIELD_SUB_NAME),
                'required',
                $this->ruleFamilyPassOption($price, $alternativeRate)
            );
        }
    }

    private function ruleFamilyPassOption($price, $alternativeRate): array
    {
        return [
            'when'       => static function ($model) use ($price, $alternativeRate) {
                return (int)$model->{self::getAttributeName($price, $alternativeRate)} === 1;
            },
            'whenClient' => "function (attribute, value) {
    		    return $('[name=\"OrderForm[" . self::getAttributeName($price, $alternativeRate) . "]\"]').val() == 1;
    		}"
        ];
    }

    public function initAllotments()
    {
        $model = $this->model;

        try {
            $class = new ReflectionClass($model::className());
        } catch (ReflectionException $e) {
        }

        if (isset($class) && $class->hasConstant('priceGroup')) {
            $priceGroup = $model::priceGroup;
            $this->allotments = $priceGroup::find()->where(
                ['id_external' => ArrayHelper::getColumn($this->prices, 'id_external')]
            )->all();

            $pricesByAllotment = ArrayHelper::map(
                $this->prices,
                static function ($el) {
                    return 0;
                },
                static function ($el) {
                    return
                        $el;
                },
                'id_external'
            );

            $tmp = [];
            foreach ($this->allotments as $allotment) {
                foreach ($pricesByAllotment[$allotment['id_external']] as $price) {
                    $hash = $this->getAllotmentHash($price);
                    $tmp[$hash] = [
                        'hash' => $hash,
                        'name' => $allotment->name,
                        'available' => $price->available,
                        'free_sell' => isset($price->free_sell) ? $price->free_sell : 1,
                        'id_external' => $price->id_external,
                        'start' => $price->start,
                        'end' => $price->end,
                    ];
                }
            }
            $this->allotments = $tmp;
            unset($tmp);
        } elseif ($model::TYPE === TrShows::TYPE) {
            $this->allotments = ArrayHelper::map(
                $this->prices,
                function ($el) {
                    return $this->getAllotmentHash($el);
                },
                function ($el) {
                    return [
                        'hash' => $this->getAllotmentHash($el),
                        'available' => $el->available,
                        'free_sell' => $el->free_sell,
                    ];
                }
            );
        }
    }

    public function getPriceByDate(DateTime $d = null)
    {
        $date = $d ? $d : $this->date;

       if ($this->model::TYPE === TrAttractions::TYPE) {
            $price_query = $this->model->getAvailablePrices();
            $price_query->joinWith([$this->model::TYPE]);
            $price_query->andWhere(
                [
                    TrAdmissions::tableName() . '.id_external' => $this->allotmentId
                ]
            );
            $price_query->andWhere(['start' => $date->format('Y-m-d H:i:s')]);
            $price_query->orderby('id_external asc, rank, name');
        } else {
            $price_query = $this->model->getAvailablePrices();
            $price_query->andWhere(['start' => $date->format('Y-m-d H:i:s')]);
            $price_query->orderby('allotment_external_id asc, rank, name');
        }

        if (!empty($this->package_modify_data)) {
            $packageDataTime = $this->package_modify_data->getStartDataTime();

            if ($packageDataTime == $date) {
                if ($this->model::TYPE === TrAttractions::TYPE) {
                    $price_query->orOnCondition(
                        [
                            TrAttractionsPrices::tableName() . '.id_external' => $this->package_modify_data->type_id,
                            'start' => $packageDataTime->format('Y-m-d H:i:s')
                        ]
                    );
                } else {
                    $price_query->orOnCondition(
                        [
                            TrPrices::tableName() . '.id_external' => $this->package_modify_data->id,
                            'start' => $packageDataTime->format('Y-m-d H:i:s')
                        ]
                    );
                }
            }
        }

        return $price_query->all();
    }

    /**
     * @return TrOrders
     */
    public function getOrder()
    {
        /**
         * @var TrOrders $order
         */
        $order = TrOrders::find()->where(['order_number' => $this->package_modify_data->order])->one();
        return $order;
    }

    /**
     * Update price by packages
     *
     * @param Package[] $packages
     */
    public function updatePricesByPackages($packages)
    {
//        foreach ($this->prices as $price) {
//            foreach ($packages as $package) {
//                foreach ($package->getTickets() as $ticket) {
//                    if ((int)$price->price_external_id === (int)$ticket->id && $package->ignore_special_rates) {
//                        $price->special_rate = null;
//                        $price->price = $price->retail_rate;
//                    } elseif ((int)$price->price_external_id === (int)$ticket->id) {
//                        $price->retail_rate = $ticket->retail_rate;
//                        $price->special_rate = $package->ignore_special_rates ? null : $ticket->special_rate;
//                        $price->price = $price->special_rate ? $price->special_rate : $price->retail_rate;
//                    }
//                }
//            }
//        }
    }

    public function initPriceByCoupon()
    {
        $request = [
            'packages' => [$this->createRequest()],
            'transactions' => [],
        ];

        if ($coupon = $this->getCoupon()) {
            $request['transactions'][] = [
                'paymentMethod' => 'Discount Code',
                'discountCode' => $coupon->code,
                'amount' => $coupon->discount
            ];

            $Tripium = new Tripium;
            $packages = $Tripium->getRecalculatingPackagesInOrder(
                $this->package_modify_data->order,
                $coupon->code,
                $request
            );

            $this->updatePricesByPackages($packages);
        }
    }

    /**
     * Initialize Modify Package
     *
     * @return bool
     */
    public function initPackageData(): bool
    {
        if (empty($this->package_modify_data)) {
            return false;
        }

        $this->comments = $this->package_modify_data->getComments();

        if ($this->package_modify_data) {
            $this->package_date_format = $this->package_modify_data->getStartDataTime()->format('Y-m-d H:i:s');
        }
        foreach ($this->package_modify_data->getTickets() as $ticket) {
            foreach ($this->prices as &$price) {
                if ((int)$price->price_external_id === (int)$ticket['id']
                    || (
                        $ticket['name'] . $ticket['description'] === $price['name'] . $price['description'])) {
                    $this->setAttributes([self::getAttributeName($price, $ticket->non_refundable) => $ticket['qty']]);

                    if ($ticket['qty']) {
                        if ($price->name === TrPrices::PRICE_TYPE_FAMILY_PASS
                            && trim($ticket['name']) === TrPrices::PRICE_TYPE_FAMILY_PASS) {
                            $this->setAttributes(
                                [
                                    self::getAttributeName(
                                        $price,
                                        $ticket->non_refundable,
                                        self::SEATS_FIELD_SUB_NAME
                                    ) => $ticket['seats'],
                                ]
                            );
                        }
                        if ($price->name === TrPrices::PRICE_TYPE_FAMILY_PASS_4_PACK
                            && trim($ticket['name']) === TrPrices::PRICE_TYPE_FAMILY_PASS_4_PACK) {
                            $this->setAttributes(
                                [
                                    self::getAttributeName(
                                        $price,
                                        $ticket->non_refundable,
                                        self::SEATS_4_FIELD_SUB_NAME
                                    ) => $ticket['seats'],
                                ]
                            );
                        }
                        if ($price->name === TrPrices::PRICE_TYPE_FAMILY_PASS_8_PACK
                            && trim($ticket['name']) === TrPrices::PRICE_TYPE_FAMILY_PASS_8_PACK) {
                            $this->setAttributes(
                                [
                                    self::getAttributeName(
                                        $price,
                                        $ticket->non_refundable,
                                        self::SEATS_8_FIELD_SUB_NAME
                                    ) => $ticket['seats'],
                                ]
                            );
                        }
                    }
                }

                if (!empty($this->package_modify_data_order)
                    && $this->date === $this->package_modify_data_order->getStartDataTime()) {
                    $price->available += $ticket['qty'];
                }
            }
            unset($price);
        }
        return true;
    }

    public function setPackage(Package $package): void
    {
        $this->package_modify_data = $package;
        $this->allotmentId = $package->type_id;
    }

    public function setPackageOrder(Package $package): void
    {
        $this->setPackage($package);
        $this->package_modify_data_order = $package;
        if (($order = $this->getOrder()) && $coupon = $order->getCoupon()) {
            $this->coupon_code = $coupon->code;
        }
    }

    public function setPackageBasket(Package $package): void
    {
        $this->setPackage($package);
        $this->package_modify_data_basket = $package;
    }

    public function initPackage(Package $package = null): void
    {
        if ($package !== null) {
            $this->setPackage($package);
        }
        $this->initPackageData();
    }

    public function initPackageModify()
    {
        if (empty($this->package_modify)) {
            return;
        }

        $Basket = TrBasket::build();

        if (empty($Basket->getPackages())) {
            return;
        }

        foreach ($Basket->getPackages() as $package) {
            if ($this->package_modify == $package->package_id
                && $this->model->getType() === $package->category
                && $this->model->id_external == $package->id) {
                $this->setPackageBasket($package);
                $this->initPackage();
            }
        }
    }

    public function checkCutOff($attribute): void
    {
        if ($this->{$attribute}) {
            $this->addError($attribute, $this->messageCutOff);
        }
    }

    public function familyPass($attribute): void
    {
        $family_pass_seats = (int)trim($this->{$attribute});
        $family_pass = (int)trim($this->{substr($attribute, 0, -strlen(self::SEATS_FIELD_SUB_NAME))});

        if ($family_pass && !$family_pass_seats) {
            $this->addError($attribute, 'You need to enter the number of seats needed with the family pass');
        }
    }

    public function familyPass_4($attribute): void
    {
        $family_pass_seats = (int)trim($this->{$attribute});
        $family_pass = (int)trim($this->{substr($attribute, 0, -strlen(self::SEATS_4_FIELD_SUB_NAME))});

        if ($family_pass && $family_pass_seats < 3) {
            $this->addError($attribute, 'For FAMILY PASS 4 PACK you can set min 3 seats');
        }
        if ($family_pass && $family_pass_seats > 4) {
            $this->addError($attribute, 'For FAMILY PASS 4 PACK you can set max 4 seats');
        }
        if ($family_pass && !$family_pass_seats) {
            $this->addError($attribute, 'You need to enter the number of seats needed with the family pass 4 pack');
        }
    }

    public function familyPass_8($attribute): void
    {
        $family_pass_seats = (int)trim($this->{$attribute});
        $family_pass = (int)trim($this->{substr($attribute, 0, -strlen(self::SEATS_8_FIELD_SUB_NAME))});

        if ($family_pass && $family_pass_seats < 5) {
            $this->addError($attribute, 'For FAMILY PASS 8 PACK you can set min 5 seats');
        }
        if ($family_pass && $family_pass_seats > 8) {
            $this->addError($attribute, 'For FAMILY PASS 8 PACK you can set max 8 seats');
        }
        if ($family_pass && !$family_pass_seats) {
            $this->addError($attribute, 'You need to enter the number of seats needed with the family pass 8 pack');
        }
    }

    public function isCount($attribute, $params)
    {
        foreach ($this->prices as $price) {
            $count = $this->getQuantity($price);
//            $attr = $this->getAttributes([self::getAttributeName($price)])[self::getAttributeName($price)];
            if (!empty($count)) {
                $this->count += $count;
            }
        }

        if (!$this->count) {
            $this->addError($attribute, 'Please select at least 1 ticket to book');
        }
    }

    public function run()
    {
        $Basket = TrBasket::build(true);
        $result = $Basket->set($this->category, $this);

        if (isset($Basket->tripium) && (int)$Basket->tripium->errorCode === Tripium::ITINERARY_WAS_NOT_FOUND) {
            TrBasket::removeSessionID($Basket->getAttribute('session_id'));
            $Basket = TrBasket::build(true);
            $result = $Basket->set($this->category, $this);
        }

        if (!$result) {
            $this->addError('result', array_values($Basket->getFirstErrors())[0]);
        } elseif ($this->package_modify) {
            $package = $Basket->getPackage($this->package_modify);
            if ($package && $package->getStartDataTime() != $this->date) {
                $Basket->removePackage($this->package_modify);
            }
        }
        return $result;
    }

    public function isCutOff()
    {
        return $this->isCutOff;
    }

    public function getCoupons()
    {
        $Tripium = new Tripium;

        if (empty($this->package_modify_data)) {
            return null;
        }

        return $Tripium->getCouponsForOrder(
            $this->package_modify_data->order,
            $this->package_modify_data->package_id,
            $this->createRequest()
        );
    }

    /**
     * Return one of access coupon
     *
     * @param bool $onlyEnteredCoupon
     *
     * @return null|Coupon
     */
    public function getCoupon($onlyEnteredCoupon = false)
    {
        $accessCoupons = [];
        if ($this->coupon_code && $coupons = $this->getCoupons()) {
            foreach ($coupons as $coupon) {
                if (StrHelper::strtolower($coupon->code) === StrHelper::strtolower($this->coupon_code)) {
                    $accessCoupons[] = $coupon;
                }
            }
        }
        if ($onlyEnteredCoupon) {
            return !empty($accessCoupons[0]) ? $accessCoupons[0] : null;
        }
        if ($coupons = $this->getCoupons()) {
            foreach ($coupons as $coupon) {
                if ($coupon->auto === true) {
                    $accessCoupons[] = $coupon;
                }
            }
        }
        uasort(
            $accessCoupons,
            static function ($a, $b) {
                if ($a->discount == $b->discount) {
                    return 0;
                }
                return ($a->discount < $b->discount) ? 1 : -1;
            }
        );
        $accessCoupons = array_values($accessCoupons);
        if ($accessCoupons) {
            return $accessCoupons[0];
        }

        return null;
    }

    public function getTotalPrice()
    {
        $totalPrice = 0;
        foreach ($this->prices as $price) {
            $qty = $this->{self::getAttributeName($price)};
            $qty = $qty ? $qty : 0;
            $totalPrice += $price->price * $qty;
        }
        return $totalPrice;
    }

    /**
     * Have or have not Non Refundable Rates
     *
     * @return bool
     */
    public function getHaveNonRefundable()
    {
        foreach ($this->prices as $price) {
            if (!empty($price->alternative_rate)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Return Minimal Adult Price
     *
     * @return TrPrices|TrAttractionsPrices|null
     */
    public function getMinimalAdultPrice()
    {
        $p = null;
        foreach ($this->prices as $price) {
            if (mb_strtoupper($price->name) === $price::NAME_ADULT
                && (empty($p) || (!empty($p) && $price->price < $p->price))) {
                $p = $price;
            }
        }
        return $p;
    }

    /**
     * Return Non Refundable Minimal Adult Price
     *
     * @return TrPrices|TrAttractionsPrices|null
     */
    public function getNonRefundableMinimalAdultPrice()
    {
        $p = null;
        foreach ($this->prices as $price) {
            if (mb_strtoupper($price->name) === $price::NAME_ADULT
                && (empty($p) || (!empty($p) && $price->alternative_rate < $p->alternative_rate))) {
                $p = $price;
            }
        }
        return $p;
    }

    /**
     * @param TrPrices|TrAttractionsPrices $price
     *
     * @return int
     */
    public function getQuantity($price): int
    {
        $alternativeRate = $this->isAlternativeRate($price);
        $qty = $this->getAttributes(
            [self::getAttributeName($price, $alternativeRate)]
        )[self::getAttributeName($price, $alternativeRate)];

        return !empty($qty) ? (int)$qty : 0;
    }

    /**
     * @param TrPrices|TrAttractionsPrices $price
     *
     * @return int
     */
    public function getQuantitySeats($price): int
    {
        $suffix = null;
        if ($price->name === TrPrices::PRICE_TYPE_FAMILY_PASS) {
            $suffix = self::SEATS_FIELD_SUB_NAME;
        } elseif ($price->name === TrPrices::PRICE_TYPE_FAMILY_PASS_4_PACK) {
            $suffix = self::SEATS_4_FIELD_SUB_NAME;
        } elseif ($price->name === TrPrices::PRICE_TYPE_FAMILY_PASS_8_PACK) {
            $suffix = self::SEATS_8_FIELD_SUB_NAME;
        }

        if (!$suffix) {
            return 0;
        }

        $alternativeRate = $this->isAlternativeRate($price);
        $qty = $this->getAttributes(
            [self::getAttributeName($price, $alternativeRate, $suffix)]
        )[self::getAttributeName($price, $alternativeRate, $suffix)];

        return !empty($qty) ? (int)$qty : 0;
    }

    /**
     * @param TrPrices|TrAttractionsPrices $price
     *
     * @return bool
     */
    public function isAlternativeRate($price): bool
    {
        if (empty($price->alternative_rate)) {
            return false;
        }

        $qty = $this->getAttributes(
            [self::getAttributeName($price, true)]
        )[self::getAttributeName($price, true)];

        return !empty($qty);
    }

    public function setOneTicket($ticketName): void
    {
        foreach ($this->prices as $price) {
            if ($ticketName === $price->name) {
                $this->setAttributes([self::getAttributeName($price) => 1]);
            }
        }
    }

    /**
     * @return string
     */
    public function getComments(): string
    {
        return is_string($this->comments) ? $this->comments : '';
    }
}

<?php

namespace common\models\form;

use common\models\TrAttractions;
use common\models\TrAttractionsPrices;
use common\models\TrBasket;
use common\models\TrShows;
use common\models\VacationPackage;
use common\models\VacationPackageOrder;
use common\tripium\Tripium;
use DateTime;
use Exception;
use yii\base\DynamicModel;
use yii\helpers\Json;

class PackageForm extends DynamicModel
{
    public const PRICE_TYPE_FAMILY_PASS = 'FAMILY PASS';
    public const PRICE_TYPE_FAMILY_PASS_4_PACK = "FAMILY PASS 4 PACK";
    public const PRICE_TYPE_FAMILY_PASS_8_PACK = "FAMILY PASS 8 PACK";
    
    private $selectedData;
    private $selectedDataFormat;
    private $_vacationPackage;
    private $package_id;
    private $package_modify_id;
    private $_attributeLabels = [];
    
    public $count = 1;

    /**
     * @inheritdoc
     */
    public function formName()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge($this->_attributeLabels, [
            'count' => 'Quantity of Packages',
            'comments' => 'Order Comments',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        if (parent::load($data, $formName)) {
            $this->initVacationPackage();
            $this->initOrderVacationPackage();
            return parent::load($data, $formName);
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['selectedData'], 'required', 'message' => 'Please choose dates.'],
            [['package_id', 'count'], 'required'],
            [['package_id', 'package_modify_id', 'count'], 'integer'],
            [['selectedData', 'package_id'], 'trim'],
            [['selectedData', 'package_id', 'package_modify_id', 'count'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'], //xss protection
            [['package_id'], 'exist', 'skipOnError' => true, 'targetClass' => VacationPackage::class, 'targetAttribute' => ['package_id' => 'vp_external_id']],
            ['selectedDataFormat', 'checkDataFormat'],
            ['count', 'in', 'range' => range(1,9)],
        ];
    }

    /**
     * Validator of Family Pass Ticket Type
     * @param $attribute
     * @param $params
     */
    public function familyPass($attribute, $params)
    {
        if (!$this->{$attribute}) {
            $this->addError($attribute, 'You need to enter the number of seats needed with the family pass');
        }
    }

    /**
     * Validator of Family Pass 4 Pack Ticket Type
     * @param $attribute
     * @param $params
     */
    public function familyPass_4($attribute, $params)
    {
        if ($this->{$attribute} < 3) {
            $this->addError($attribute, 'For FAMILY PASS 4 PACK you can set min 3 seats');
        }
        if ($this->{$attribute} > 4) {
            $this->addError($attribute, 'For FAMILY PASS 4 PACK you can set max 4 seats');
        }
        if (!$this->{$attribute}) {
            $this->addError($attribute, 'You need to enter the number of seats needed with the family pass 4 pack');
        }
    }

    /**
     * Validator of Family Pass 8 Pack Ticket Type
     * @param $attribute
     * @param $params
     */
    public function familyPass_8($attribute, $params)
    {
        if ($this->{$attribute} < 5) {
            $this->addError($attribute, 'For FAMILY PASS 8 PACK you can set min 5 seats');
        }
        if ($this->{$attribute} > 8) {
            $this->addError($attribute, 'For FAMILY PASS 8 PACK you can set max 8 seats');
        }
        if (!$this->{$attribute}) {
            $this->addError($attribute, 'You need to enter the number of seats needed with the family pass 8 pack');
        }
    }

    /**
     * @param string|null $data
     */
    public function setSelectedData($data = null)
    {
        try {
            $this->selectedDataFormat = Json::decode($data);
            $this->selectedData = $data;
        } catch (Exception $e) {}
    }

    /**
     * @return array
     */
    public function getSelectedData()
    {
        return $this->selectedData;
    }

    /**
     * @return array
     */
    public function getSelectedDataFormat()
    {
        return $this->selectedDataFormat;
    }

    /**
     * Check DataFormat
     */
    public function checkDataFormat($attribute, $params)
    {
        if (empty($this->selectedDataFormat)) {
            $this->addError($attribute, "There aren't packages");
        }
    }

    /**
     * @return null|VacationPackageOrder
     */
    public function getVacationPackageModify()
    {
        if ($this->package_modify_id) {
            $Basket = TrBasket::build();
            return $Basket->getVacationPackage($this->package_modify_id);
        }
        return null;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function addToCart(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        $items = [];
        foreach ($this->getSelectedItems() as $itemData) {
        	$date = new DateTime($itemData['date']);
        	$item = [
                "id" => $itemData['item']->id_external,
                "category" => $itemData['itemTypeReal'],
                "date" => $date->format('m/d/Y'),
                "time" => $date->format('h:i A') == "12:00 AM" ? TrAttractionsPrices::ANY_TIME : $date->format('h:i A')
            ];
        	foreach ($this->getVacationPackage()->getTicketTypes() as $ticketType) {
        	    if ($ticketType['vendorId'] == $itemData['item']->id_external) {
        	        if (in_array($ticketType['name'], [self::PRICE_TYPE_FAMILY_PASS, self::PRICE_TYPE_FAMILY_PASS_4_PACK, self::PRICE_TYPE_FAMILY_PASS_8_PACK])) {
        	            $f = $this->getFpFieldName($itemData['item'], $ticketType['id'], $ticketType['name']);
                    	$item['tickets'][] = [
                    	    'id' => $ticketType['id'],
                    	    'seats' => $this->getAttributes([$f])[$f]
                    	];
        	        }
        	    }
        	}
            if (!empty($itemData['allotment'])) {
            	$item['typeId'] = $itemData['allotment']->id_external;
            }
        	$items[] = $item;
        }
        $Basket = TrBasket::build(true);

        if ($this->package_modify_id) {
            $Basket->removeVacationPackage($Basket->getGroupHashVacationPackageById($this->package_modify_id));
        }

        $Tripium = $this->addPackagesToCart($Basket, $items);

        if ((int)$Tripium->errorCode === Tripium::ITINERARY_WAS_NOT_FOUND) {
            TrBasket::removeSessionID($Basket->getAttribute('session_id'));
            $Basket = TrBasket::build(true);
            $Tripium = $this->addPackagesToCart($Basket, $items);
        }

        if (empty($Tripium->errors)) {
            $Basket->reset();
            return true;
        }

        $this->addErrors($Tripium->errors);
        return false;
    }

    private function addPackagesToCart($Basket, $items): Tripium
    {
        $Tripium = new Tripium();
        for ($i = 1; $i <= $this->count; $i++) {
            $Tripium->addPackageToCart($Basket->session_id, $this->package_id, $items);
        }
        return $Tripium;
    }

    /**
     * @return VacationPackage|null
     */
    public function getPackage(): ?VacationPackage
    {
        /**
         * @var VacationPackage $vacationPackage
         */
        $vacationPackage = VacationPackage::getActive()->where(['vp_external_id' => $this->package_id])->one();
        return $vacationPackage;
    }

    /**
     * @return array
     */
    public function getSelectedItems(): array
    {
        $arr = [];
        foreach ($this->selectedDataFormat as $item) {
            if (!empty($item['itemId']) && !empty($item['itemTypeReal'])) {
                if ($item['itemTypeReal'] === TrAttractions::TYPE) {
                    /**
                     * @var TrAttractions $it
                     */
                    $it = TrAttractions::find()->where(['id_external' => (int)$item['itemId']])->one();
                    if ($this->getPackage()) {
                        $allotments = $it->getAllotments()->where(
                            ['id_external' => $this->getPackage()->getTypeIdByVendorId($item['itemId'])]
                        )->all();
                        foreach ($allotments as $allotment) {
                            $item['item'] = $it;
                            $item['allotment'] = $allotment;
                            $arr[] = $item;
                        }
                    }
                } elseif ($item['itemTypeReal'] === TrShows::TYPE) {
                    $it = TrShows::find()->where(['id_external' => (int)$item['itemId']])->one();
                    $item['item'] = $it;
                    $arr[] = $item;
                }
            }
        }
        return $arr;
    }

    /**
     * Setter package_id
     * @param $package_id
     */
    public function setPackage_id($package_id)
    {
        if ($this->package_id != $package_id) {
            $this->package_id = $package_id;
            $this->initVacationPackage();
        }
    }

    /**
     * Getter $package_id
     */
    public function getPackage_id()
    {
        return $this->package_id;
    }

    /**
     * Setter package_modify_id
     * @param $package_modify_id
     */
    public function setPackage_modify_id($package_modify_id)
    {
        if ($this->package_modify_id != $package_modify_id) {
            $this->package_modify_id = $package_modify_id;
            $this->initOrderVacationPackage();
        }
    }

    /**
     * Getter $package_id
     */
    public function getPackage_modify_id()
    {
        return $this->package_modify_id;
    }

    /**
     * Setter vacationPackage
     *
     * @param VacationPackage $vacationPackage
     */
    public function setVacationPackage(VacationPackage $vacationPackage)
    {
        if (!empty($this->package_id) && !empty($this->_vacationPackage) && $this->package_id == $vacationPackage->vp_external_id) {
            return;
        }

        $this->_vacationPackage = $vacationPackage;
        $this->package_id = $this->_vacationPackage->vp_external_id;

        foreach ($this->_vacationPackage->getItems() as $item) {
            foreach ($this->_vacationPackage->getTicketTypes() as $ticketType) {
                if ($ticketType['vendorId'] == $item->itemExternal->id_external) {
                    if (in_array($ticketType['name'], [self::PRICE_TYPE_FAMILY_PASS, self::PRICE_TYPE_FAMILY_PASS_4_PACK, self::PRICE_TYPE_FAMILY_PASS_8_PACK])) {
                        $f = $this->getFpFieldName($item->itemExternal, $ticketType['id'], $ticketType['name']);
                        $this->defineAttribute($f);
                        $this->_attributeLabels[$f] = $ticketType['name'];
                        if ($ticketType['name'] == self::PRICE_TYPE_FAMILY_PASS) {
                            $this->addRule([$f], 'integer', ['min' => 1, 'max' => 99]);
                            $this->addRule($f, 'familyPass');
                            $this->setAttributes([$f => 1]);
                        }
                        if ($ticketType['name'] == self::PRICE_TYPE_FAMILY_PASS_4_PACK) {
                            $this->addRule([$f], 'integer', ['min' => 3, 'max' => 4]);
                            $this->addRule($f, 'familyPass_4');
                            $this->setAttributes([$f => 3]);
                        }
                        if ($ticketType['name'] == self::PRICE_TYPE_FAMILY_PASS_8_PACK) {
                            $this->addRule([$f], 'integer', ['min' => 5, 'max' => 8]);
                            $this->addRule($f, 'familyPass_8');
                            $this->setAttributes([$f => 5]);
                        }
                    }
                }
            }
        }
    }

    /**
     * Return Vacation Package
     *
     * @return VacationPackage
     */
    public function getVacationPackage()
    {
        if (!empty($this->_vacationPackage)) {
            return $this->_vacationPackage;
        }

        if (!empty($this->package_id)) {
            $this->initVacationPackage();
            if (!empty($this->_vacationPackage)) {
                return $this->_vacationPackage;
            }
        }
        return null;
    }

    /**
     * Initialize VacationPackage
     */
    public function initVacationPackage()
    {
        /**
         * @var VacationPackage $VacationPackage
         */
        $VacationPackage = VacationPackage::getActive()->where(['vp_external_id'=>$this->package_id])->one();
        if (!empty($VacationPackage)) {
            $this->setVacationPackage($VacationPackage);
        }
    }

    /**
     * Initialize OrderVacationPackage
     */
    public function initOrderVacationPackage()
    {
        $Basket = TrBasket::build();
        if ($this->package_modify_id && $hash = $Basket->getGroupHashVacationPackageById($this->package_modify_id)) {
            if (!empty($Basket->getUniqueVacationPackages()[$hash])) {
                $VacationPackageOrder = $Basket->getUniqueVacationPackages()[$hash];
                $this->setAttributes(['count' => $VacationPackageOrder->count]);
                foreach ($VacationPackageOrder->getPackages() as $package) {
                    foreach ($package->tickets as $ticket) {
                        $f = $this->getFpFieldName($package->item, $ticket->id, $ticket->name);
                        $this->setAttributes([$f => $ticket->seats]);
                    }
                }
            }
        }
    }

    /**
     * Get field name
     *
     * @param object $model
     * @param        $id
     * @param string $type
     *
     * @return string
     */
    public function getFpFieldName($model, $id, $type)
    {
        $s = '';
        foreach ($this->getVacationPackage()->getTicketTypes() as $ticketType) {
            if ($ticketType['vendorId'] == $model->id_external && $ticketType['name'] == $type) {
                if ($type == self::PRICE_TYPE_FAMILY_PASS) {
                    $s = 'fp';
                }
                if ($type == self::PRICE_TYPE_FAMILY_PASS_4_PACK) {
                    $s = 'fp4';
                }
                if ($type == self::PRICE_TYPE_FAMILY_PASS_8_PACK) {
                    $s = 'fp8';
                }
            }
        }
        if (empty($s)) {
            return null;
        }
        return $model->type.'_'.$model->id_external.'_'.$id.'_seat_'.$s;
    }
}

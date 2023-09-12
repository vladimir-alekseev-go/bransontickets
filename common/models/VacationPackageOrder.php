<?php

namespace common\models;

use Yii;
use yii\base\Model;

class VacationPackageOrder extends Model
{
    private $total;
    private $tax;
    private $items;
    private $tickets;
    private $_vacationPackage;

    public $name;
    public $id;
    public $config_id;
    public $cancellation_text;
    public $cancelled = false;
    public $modified = false;
    public $count = 1;

    public function loadData($data)
    {
        if (!empty($data['configId'])) {
            $this->config_id = $data['configId'];
        }
        if (!empty($data['id'])) {
            $this->id = $data['id'];
        }
        if (!empty($data['name'])) {
            $this->name = $data['name'];
        }
        if (!empty($data['total'])) {
            $this->total = $data['total'];
        }
        if (!empty($data['tax'])) {
            $this->tax = $data['tax'];
        }
        if (!empty($data['cancelled'])) {
            $this->cancelled = $data['cancelled'];
        }
        if (!empty($data['cancellationText'])) {
            $this->cancellation_text = $data['cancellationText'];
        }
        if (!empty($data['items'])) {
            foreach ($data['items'] as $item) {
                $package = new Package;
                $package->loadData($item);
                $this->items[] = $package;
            }
        }
        $this->_vacationPackage = null;
    }

    public function getTotal()
    {
        return number_format($this->total * $this->count, 2, '.', '');
    }

    public function getFullTotal()
    {
        return number_format(($this->total + $this->tax) * $this->count, 2, '.', '');
    }

    public function getTax()
    {
        return number_format($this->tax * $this->count, 2, '.', '');
    }

    /**
     * Get Packages.
     *
     * @return Package[]
     */
    public function getPackages(): array
    {
        return $this->items;
    }

    /**
     * Get Package.
     *
     * @param $id
     *
     * @return Package|null
     */
    public function getPackage($id): ?Package
    {
        foreach ($this->getPackages() as $package) {
            if ((int)$package->package_id === (int)$id) {
                $package->cancellation_policy = $this->cancellation_text;
                return $package;
            }
        }
        return null;
    }

    public function getTicketsCount()
    {
        $count = 0;
        foreach ($this->items as $package) {
            foreach ($package->tickets as $ticket) {
                $count += $ticket->qty;
            }
        }
        return $count * $this->count;
    }

    /**
     * Return cancellation policy text
     *
     * @return string
     */
    public function getCancellationPolicyText(): string
    {
        return $this->cancellation_text;
    }

    /**
     * Can you cancel VP or not
     *
     * @return bool
     */
    public function canCancel(): bool
    {
        return !Yii::$app->user->isGuest && !$this->cancelled;
    }

    /**
     * Return Vacation Package.
     *
     * @return VacationPackage|null
     */
    public function getVacationPackage(): ?VacationPackage
    {
        if (empty($this->_vacationPackage) && !empty($this->config_id)) {
            /**
             * @var VacationPackage $vacationPackage
             */
            $vacationPackage = VacationPackage::find()->where(['vp_external_id' => $this->config_id])->one();
            $this->_vacationPackage = $vacationPackage;
        }
        return $this->_vacationPackage;
    }

    /**
     * @return float
     */
    public function getSave(): float
    {
        $retailRate = 0;
        $specialRate = 0;
        foreach ($this->getPackages() as $package) {
            foreach ($package->getTickets() as $ticket) {
                $retailRate += $ticket->retail_rate;
                $specialRate += $ticket->special_rate;
            }
        }
        return ($retailRate - $specialRate) * $this->count;
    }
}

<?php

namespace common\models;

use yii\base\Model;

class Itinerary extends Model
{
    public const KEY_ITINERARY = 'itinerary';

    /**
     * @var array $data
     */
    private $data;

    public $session;

    public function loadData(array $data): Itinerary
    {
        if (!empty($data)) {
            $this->data = $data;
        }
        if (!empty($data[self::KEY_ITINERARY]['session'])) {
            $this->session = $data[self::KEY_ITINERARY]['session'];
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return Package[]
     */
    public function getPackages(): array
    {
        $packages = [];
        if (!empty($this->getData()[self::KEY_ITINERARY]['packages'])) {
            foreach ($this->getData()[self::KEY_ITINERARY]['packages'] as $packageData) {
                $package = new Package();
                $package->loadData($packageData);
                $packages[] = $package;
            }
        }
        return $packages;
    }

    /**
     * @return int
     */
    public function getTotalCount(): int
    {
        $totalCount = 0;
        foreach ($this->getPackages() as $package) {
            if ($package->category === TrPosHotels::TYPE) {
                $totalCount++;
                continue;
            }

            foreach ($package->getTickets() as $ticket) {
                if ($package->category !== TrPosHotels::TYPE) {
                    $totalCount += $ticket['qty'];
                }
//                    $ticket['resultRate'] = number_format($ticket['specialRate'] ?: $ticket['retailRate'], 2, '.', '');
            }
        }
        return $totalCount;
    }

    public function getItineraryData()
    {
        return $this->getData()[self::KEY_ITINERARY];
    }
}

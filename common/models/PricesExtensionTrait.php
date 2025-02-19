<?php

namespace common\models;

use common\helpers\General;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use yii\db\ActiveQuery;
use yii\helpers\Json;

trait PricesExtensionTrait
{
    /**
     * @var DateTime $updateStart
     */
    private $updateStart;

    /**
     * @var DateTime $updateEnd
     */
    private $updateEnd;

    public $errors_add = [];
    public $errors_update = [];
    public $errors_absent_parent_row = [];

    public $updateOnlyIdExternal;
    public $periodUpdate = 180;

    public function setUpdateStart(DateTime $date): void
    {
        $this->updateStart = $date;
    }

    public function getUpdateStart(): ?DateTime
    {
        return $this->updateStart;
    }

    public function setUpdateEnd(DateTime $date): void
    {
        $this->updateEnd = $date;
    }

    public function getUpdateEnd(): ?DateTime
    {
        return $this->updateEnd;
    }

    public function validateDates(): void
    {
        if (strtotime($this->end) < strtotime($this->start)) {
            $this->addError('end', 'The end date ('.$this->end.') has to be greater than the start date ('.$this->start.'), ID:'.$this->price_external_id.', name:'.$this->name);
        }
    }

    /**
     * @param array $params
     *
     * @return bool
     * @throws Exception
     */
    public function updateFromTripium($params=[]): bool
    {
        self::removeDuplicates();

        if (self::TYPE === TrAttractionsPrices::TYPE) {
            self::deleteAll("end < start");
            self::deleteAll("end < '" . date("Y-m-d H:i:s") . "'");
        }
        self::deleteAll("start < '" . date("Y-m-d") . "'");

        $start = !empty($params['start']) ? new DateTime($params['start']) : new DateTime();
        $end = !empty($params['end']) ? new DateTime($params['end']) : new DateTime();
        if ($this->getUpdateStart() !== null) {
            $start = $this->getUpdateStart();
        }
        if ($this->getUpdateEnd() !== null) {
            $end = $this->getUpdateEnd();
        }
        if ($start == $end) {
            $end = new DateTime($end->format('Y-m-d H:i:s'));
            $end->add(new DateInterval('P1D'));
        }

        $range = new DatePeriod($start, new DateInterval('P'.$this->periodUpdate.'D'), $end);

        $tableName = call_user_func(array(constant(get_class($this).'::MAIN_CLASS'),'tableName'));

        foreach ($range as $k => $dateFrom) {
            $dateTo = clone $dateFrom;
            $dateTo->add(new DateInterval('P'.$this->periodUpdate.'D'));
            if ($dateTo > $end) {
                $dateTo = $end;
            }
            $data = self::find()->select([self::tableName().'.id', 'hash', self::tableName().'.hash_summ'])
                ->where("start >= '".$dateFrom->format("Y-m-d 00:00:00")."' and start <= '".$dateTo->format("Y-m-d 23:59:59")."'")
            ;
            if ($this->updateOnlyIdExternal !== null) {
                $data->joinWith('main', false);
                $data->andWhere([$tableName. '.id_external' => $this->updateOnlyIdExternal]);
            }
            $data = $data->asArray()->indexBy('hash')->all();
            $tripiumData = $this->getSourceData([
                                                    "start" => $dateFrom->format("m/d/Y"),
                                                    "end" => $dateTo->format("m/d/Y"),
                                                    "ids" => $this->updateOnlyIdExternal,
                                                ]);

            if ($tripiumData !== null) {
                $this->updateData($tripiumData, $data);
            }
            unset($data, $tripiumData);
        }

        self::removeDuplicates();
        return true;
    }

    public static function removeDuplicates(): void
    {
        $hash = self::find()->select("hash")
            ->groupby(["hash"])
            ->having("count(*) > 1")
            ->asArray()->column();

        if ($hash) {
            self::deleteAll(["hash"=>$hash]);
        }
    }

    /**
     * @param $tripiumData
     * @param $data
     *
     * @return bool
     * @throws Exception
     */
    public function updateData($tripiumData, $data): bool
    {
        $classNamePath = self::class;
        $allotmentIds = [];
        $itemIds = [];

        if (new $classNamePath instanceof TrAttractionsPrices) {
            $allotmentIds = TrAdmissions::find()->select(['id_external'])->column();
            $itemIds = TrAttractions::find()->select(['id_external'])->column();
        }
        if (new $classNamePath instanceof TrPosHotelsPriceExtra || new $classNamePath instanceof TrPosHotelsPriceRoom) {
            $allotmentIds = TrPosRoomTypes::find()->select(['id_external'])->column();
            $itemIds = TrPosHotels::find()->select(['id_external'])->column();
        }

        if (!empty($tripiumData)) {

            foreach ($tripiumData as $d) {
                if (empty($d['prices'][0]['schemaId'])) {
                    continue;
                }

                if (!in_array((int)$d['vendorId'], $itemIds, false) &&
                    (new $classNamePath instanceof TrAttractionsPrices
                    )) {
                    $mess = 'absent parent row with vendorId: ' . $d["vendorId"];
                    $this->errors_absent_parent_row[$mess] = $mess;
                    continue;
                }

                $time = $d['time'] === self::ANY_TIME ? '00:00:00' : $d['time'];
                $dateStart = new DateTime($d['start'] . ' ' . $time);
                $dateEnd = $d['end']
                    ? new DateTime($d['end'] . ' ' . $time)
                    : (clone $dateStart)->add(new DateInterval('P1D'));

                foreach ($d["prices"] as $p) {
                    if (!in_array($p['schemaId'], $allotmentIds, true) &&
                        new $classNamePath instanceof TrAttractionsPrices) {
                        $mess = 'absent parent row with schemaId: ' . $p['schemaId'];
                        $this->errors_absent_parent_row[$mess] = $mess;
                        continue;
                    }
                    if (self::TYPE === TrPosHotelsPriceRoom::TYPE || self::TYPE === TrPosHotelsPriceExtra::TYPE) {
                        $hash = md5($d["id"] . "_" . $p["id"] . "_" . $dateStart->format('Y-m-d H:i:s') . $d["time"]);
                    } else {
                        $hash = md5($d["id"] . "_" . $p["schemaId"] . "_" . $dateStart->format('Y-m-d H:i:s') . "_" . $dateEnd->format('Y-m-d H:i:s') . $d["time"]);
                    }
//                    if (self::TYPE === TrPosHotelsPriceRoom::TYPE && $p['supplementary']) {
//                        continue;
//                    }
//                    if (self::TYPE === TrPosHotelsPriceExtra::TYPE && !$p['supplementary']) {
//                        continue;
//                    }
                    $dataNew = [
                        "id_external"       => $p['schemaId'],
                        "hash"              => $hash,
                        "start"             => $dateStart->format('Y-m-d H:i:s'),
                        "end"               => $dateEnd->format('Y-m-d H:i:s'),
                        "name"              => $p["name"],
                        "alternative_rate"  => !empty($p["alternative"]["rate"]) ? $p["alternative"]["rate"] : null,
                        "retail_rate"       => $p["retailRate"],
                        "special_rate"      => $p["specialRate"] === 0 || $p["retailRate"] === $p["specialRate"]
                            ? null : $p["specialRate"],
                        "tripium_rate"      => $p["tripiumRate"] === 0 ? null : $p["tripiumRate"],
                        "price"             => isset($p["specialRate"]) && $p["specialRate"] !== null && $p["specialRate"] * 1 > 0
                            ? $p["specialRate"] * 1 : $p["retailRate"] * 1,
                        "description"       => $p["description"],
                        "available"         => 9999,
                        "sold"              => $p["sold"],
                        "stop_sell"         => $p["stopSell"] ? 1 : 0,
                        "free_sell"         => $p["freeSell"] ? 1 : 0,
                        "any_time"          => $d["time"] === self::ANY_TIME ? 1 : 0,
                        "price_external_id" => $p["id"] ?: -1,
                        "rank_level"        => !empty($p["rank"]) ? $p["rank"] : 999999,
                    ];

                    if (in_array(self::TYPE, [TrPosHotelsPriceRoom::TYPE, TrPosHotelsPriceExtra::TYPE], true)) {
                        $dataNew['capacity'] = $p['capacity'];
                    }

                    $dataNew["hash_summ"] = md5(Json::encode($dataNew));

                    if (empty($data[$hash])) {
                        try {
                            $Model = new self;
                            /*if ($Model instanceof NotificationsTrAttractionsPrices
                                || $Model instanceof NotificationsTrLunchsPrices
                            ) {
                                $class = get_parent_class($Model);
                                $Model = new $class;
                            }*/
                            $Model->setAttributes($dataNew);
                            $Model->save();
                            $err = $Model->getErrors();
                            if ($err && count($this->errors_add) < 100) {
                                $err['id_external'][0] = $dataNew['id_external'] . ': ' . $err['id_external'][0];
                                $this->errors_add[] = $err;
                            }
                        } catch (Exception $e) {
                            $this->errors_add[] = $e->getMessage();
                        }
                    } else if ($dataNew["hash_summ"] !== $data[$hash]["hash_summ"]) {
                        $Model = self::find()->where(["hash" => $hash])->one();
                        /*if ($Model instanceof NotificationsTrAttractionsPrices
                            || $Model instanceof NotificationsTrLunchsPrices
                        ) {
                            $class = get_parent_class($Model);
                            $Model = $class::find()->where(["hash" => $hash])->one();
                        }*/
                        $Model->setAttributes($dataNew);
                        $Model->save();
                        $err = $Model->getErrors();
                        if ($err && count($this->errors_add) < 100) {
                            $this->errors_update[] = $err->getErrors();
                        }
                    }
                    if (!empty($data[$hash])) {
                        unset($data[$hash]);
                    }
                }
            }
        }

        self::removeByHash(array_keys($data));

        return true;
    }

    /**
     * @param $hash
     */
    public static function removeByHash($hash): void
    {
        if (!empty($hash)) {
            self::deleteAll(["hash" => $hash]);
        }
    }

    /**
     * @param bool $alternativeRate
     *
     * @return double
     */
    public function getSaved($alternativeRate = false)
    {
        if ($alternativeRate) {
            return $this->alternative_rate ? ($this->retail_rate - $this->alternative_rate) : 0;
        }
        return $this->special_rate ? ($this->retail_rate - $this->special_rate) : 0;
    }

    /**
     * @return ActiveQuery
     * @throws Exception
     */
    public static function getAvailableByRange()
    {
        return self::getAvailable()
            ->andOnCondition([
                                 '>=',
                                 self::tableName() . '.start',
                                 General::getDatePeriod()->start->format('Y-m-d')
                             ])
            ->andOnCondition([
                                 '<=',
                                 self::tableName() . '.start',
                                 General::getDatePeriod()->end->format('Y-m-d 23:59:59')
                             ]);
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    public function getStartDate(): DateTime
    {
        return new DateTime($this->start);
    }

    /**
     * @return DateTime
     * @throws Exception
     */
    public function getEndDate(): DateTime
    {
        return new DateTime($this->end);
    }
}

<?php

namespace common\models;

use common\tripium\Tripium;
use DateTime;
use Exception;
use Yii;
use yii\db\ActiveQuery;
use yii\helpers\Json;

class TrPrices extends _source_TrPrices
{
    use PricesExtensionTrait;

    public const ANY_TIME = 'Any time';

    /**
     * @deprecated Use TrPrices::TYPE
     */
    public const type = 'shows';
    public const TYPE = 'shows';
    public const TYPE_ID = 2;

    public const PRICE_TYPE_FAMILY_PASS = 'FAMILY PASS';
    public const PRICE_TYPE_FAMILY_PASS_4_PACK = 'FAMILY PASS 4 PACK';
    public const PRICE_TYPE_FAMILY_PASS_8_PACK = 'FAMILY PASS 8 PACK';

    public const MAIN_CLASS = TrShows::class;

    public const NAME_ADULT = 'ADULT';

    public function init()
    {
        parent::init();
        $this->periodUpdate = 31;
    }

    public function getType(): string
    {
        return self::TYPE;
    }

    /**
     * @return ActiveQuery
     */
    public function getShows(): ActiveQuery
    {
        return $this->getShow();
    }

    /**
     * @return ActiveQuery
     */
    public function getShow(): ActiveQuery
    {
        return $this->getExternal();
    }

    /**
     * @return ActiveQuery
     */
    public function getMain(): ActiveQuery
    {
        return $this->getExternal();
    }

    public function getSourceData($params): ?array
    {
        return (new Tripium)->getShowsPrice($params);
    }

    /**
     * @return ActiveQuery
     */
    public function getActualPrices(): ActiveQuery
    {
        // don't use this, use (main model, example: shows, attractions)::getActualPrices()
        $Shows = new TrShows;
        return self::find()
            ->select(
                [
                    self::tableName() . '.id_external',
                    "DATE_FORMAT(start, '%Y-%m-%d') as start_date",
                    "DATE_FORMAT(start, '%b%e') as date",
                    "DATE_FORMAT(start, '%h:%i%p') as time",
                    'start'
                ]
            )
            ->leftJoin($Shows::tableName() . ' as sh', 'sh.id_external = ' . self::tableName() . '.id_external')
            ->where('start > NOW( ) + INTERVAL cut_off HOUR ')
            ->andWhere(['stop_sell' => 0]);
    }

    /*
    public function	updateFromTripium($params=[])
    {
        // remove old price
//     	self::deleteAll("start < '".date("Y-m-d")."'");

        // remove duplicates price
//     	self::removeDuplicates();

        $start = !empty($params['start']) ? $params['start'] : date("m/d/Y");
        $end = !empty($params['end']) ? $params['end'] : date("m/d/Y",time()+3600*24*0);

        $mk_start = strtotime($start);
        $mk_end = strtotime($end);

        for ($i=$mk_start; $i<=$mk_end; $i=$i+3600*24*$this->periodUpdate) {
            $from = $i;
            $to = $mk_end < $i+3600*24*$this->periodUpdate ? $mk_end : $i+3600*24*$this->periodUpdate;

            $data = self::find()->select("id, hash, hash_summ")->where("start >= '".date("Y-m-d H:i:s",$from)."' and start <= '".date("Y-m-d 23:59:59",$to)."'")->asArray()->all();
            $data = ArrayHelper::index($data, 'hash');
            $tripiumData = $this->getSourceData(["start"=>date("m/d/Y",$from),"end"=>date("m/d/Y",$to),'ids'=>$this->updateOnlyIdExternal]);

            if ($this->statusCodeTripium == \common\tripium\Tripium::STATUS_CODE_SUCCESS) {
                $this->updateData($tripiumData, $data);
            }

            unset($data);
            unset($tripiumData);
        }

        // remove duplicates price
        self::removeDuplicates();
    }
     */
    public function validateDates($attribute, $params): void
    {
        if (strtotime($this->end) <= strtotime($this->start)) {
            $this->addError('end', 'The end date has to be greater than the start date.');
        }
    }

    public function updateData($tripiumData, $data): void
    {
        if (!empty($tripiumData)) {
            foreach ($tripiumData as $d) {
                if (!TrShows::find()->where(['id_external'=>$d['vendorId']])->one()) {
                    continue;
                }
                if ($d['time'] === self::ANY_TIME) {
                    $dateStart = new DateTime($d['start']);
                } else {
                    $dateStart = new DateTime($d['start'] . ' ' . $d['time']);
                }
                $dateEnd = $d['end'] ? new DateTime($d['end']) : null;

                foreach ($d['prices'] as $p) {
//                    if ($excludeIds
//                        && (in_array(
//                            $p['name'],
//                            [
//                                self::PRICE_TYPE_FAMILY_PASS,
//                                self::PRICE_TYPE_FAMILY_PASS_4_PACK,
//                                self::PRICE_TYPE_FAMILY_PASS_8_PACK,
//                            ],
//                            true
//                        ))
//                        && in_array($d['vendorId'], $excludeIds, false)) {
//                        continue;
//                    }

                    $hash = md5(
                        $d['vendorId'] . '_' .
                        $dateStart->format('Y-m-d H:i:s') . '_' .
                        $p['name'] . '_' .
                        $p['schemaId'] . '_' .
                        $p['description']
                    );

                    $dataNew = [
                        'id_external'           => $d['vendorId'],
                        'hash'                  => $hash,
                        'start'                 => $dateStart->format('Y-m-d H:i:s'),
                        'end'                   => $dateEnd ? $dateEnd->format('Y-m-d H:i:s') : null,
                        'name'                  => $p['name'],
                        'alternative_rate'      => !empty($p['alternative']['rate']) ? $p['alternative']['rate'] : null,
                        'retail_rate'           => $p['retailRate'],
                        'special_rate'          => $p['specialRate'] === 0 || $p['retailRate'] === $p['specialRate']
                            ? null : $p['specialRate'],
                        'description'           => $p['description'],
                        'tripium_rate'          => $p['tripiumRate'] === 0 ? null : $p['tripiumRate'],
                        'price'                 => $p['specialRate'] !== null && $p['specialRate'] * 1 > 0
                            ? $p['specialRate'] * 1 : $p['retailRate'] * 1,
                        'available'             => $p['allotmentId'] ? $p['available'] : 9999,
                        'sold'                  => $p['allotmentId'] ? $p['sold'] : 0,
                        'stop_sell'             => $p['stopSell'] ? 1 : 0,
                        'free_sell'             => $p['freeSell'] ? 1 : 0,
                        "any_time"              => $d['time'] === self::ANY_TIME ? 1 : 0,
                        'allotment_external_id' => $p['schemaId'] ?: $dateStart->format('Y-m-d H:i:s'),
                        'price_external_id'     => $p['id'],
                        'rank_level'            => !empty($p['rank']) ? $p['rank'] : 999999,
                    ];
                    $dataNew['hash_summ'] = md5(Json::encode($dataNew));

                    if (empty($data[$hash])) {
//                        try {
                            $Model = new self();
                            $Model->setAttributes($dataNew);
                            $Model->save();
                            $err = $Model->getErrors();
                            if ($err) {
                                $this->errors_add[] = $err;
                            }
//                        } catch (Exception $e) {
//                            $this->errors_add[] = $e->getMessage();
//                        }
                    } else if ($dataNew['hash_summ'] !== $data[$hash]['hash_summ']) {
                        try {
                            $Model = self::find()->where(['hash' => $hash])->one();
                            $Model->setAttributes($dataNew);
                            $Model->save();
                            $err = $Model->getErrors();
                            if ($err) {
                                $this->errors_update[] = $err;
                            }
                        } catch (Exception $e) {
                            $this->errors_update[] = $e->getMessage();
                        }
                    }

                    unset($data[$hash]);
                }
            }
        }
        self::removeByHash(array_keys($data));
    }

    /**
     * @return ActiveQuery
     */
    public static function getActive(): ActiveQuery
    {
        return self::find()
            ->andOnCondition(['stop_sell'=>0])
            ->andOnCondition('start >= NOW( )')
            ;
    }

    /**
     * @return ActiveQuery
     */
    public static function getAvailable(): ActiveQuery
    {
        return self::getActive()
            ->andOnCondition(['or','available > 0','free_sell=1'])
            ;
    }

    /**
     * @return ActiveQuery
     */
    public static function getAvailableSpecial(): ActiveQuery
    {
        return self::getAvailable()
            ->andOnCondition(['not', ['special_rate'=>false]])
            ;
    }

    /**
     * @param DateTime $date
     * @param array    $ids
     *
     * @return ActiveQuery
     */
    public static function getNearestAvailable(DateTime $date, array $ids): ActiveQuery
    {
        return self::getAvailable()
            ->joinWith(['show'], false)
            ->select([
                         TrShows::tableName().'.id',
                         TrShows::tableName().'.id_external',
                         self::tableName().'.price_external_id',
                         'start',
                         'code',
                         'delta' => 'ABS(UNIX_TIMESTAMP(start) - '.$date->getTimestamp().')'
                     ])
            ->distinct()
            ->orderby('delta')
            ->groupby([
                          TrShows::tableName().'.id',
                          TrShows::tableName().'.id_external',
                          self::tableName().'.price_external_id',
                          'start',
                          'delta'])
            ->where([TrShows::tableName().'.id_external' => $ids])
            ;
    }
}

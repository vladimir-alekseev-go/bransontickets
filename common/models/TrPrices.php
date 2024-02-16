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

    public const type = 'shows';
    public const TYPE = 'shows';

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

    public function getSourceData($params)
	{
	    $tripium = new Tripium;
		$res = $tripium->getShowsPrice($params);
		$this->statusCodeTripium = $tripium->statusCode;
		return $res;
	}

    public function updateData($tripiumData, $data): void
    {
        if (!empty($tripiumData)) {
			foreach ($tripiumData as $d) {
			    if (!TrShows::find()->where(['id_external'=>$d['id']])->one()) {
			        continue;
			    }

				$dateStart = date('y-m-d H:i:s', strtotime($d['start'] . ' ' . $d['time']));

                foreach ($d['prices'] as $p) {

				    $hash = md5($d['id'] . '_' . $dateStart . '_' . $p['name'] . '_' . $p['description']);

					$dataNew = [
						'id_external' => $d['id'],
						'hash' => $hash,
						'start' => $dateStart,
						'end' => $d['end'] ? date('y-m-d H:i:s', strtotime($d['end'])) : null,
					    'name' => $p['name'],
					    'alternative_rate' => !empty($p['alternative']['rate']) ? $p['alternative']['rate'] : null,
						'retail_rate' => $p['retailRate'],
					    'special_rate' => $p['specialRate'] == 0 || $p['retailRate'] == $p["specialRate"] ? null : $p['specialRate'],
						'description' => $p['description'],
						'tripium_rate' => $p['tripiumRate'] == 0 ? null : $p['tripiumRate'],
						'price' => $p['specialRate'] != null && $p['specialRate']*1 > 0 ? $p['specialRate']*1 : $p['retailRate']*1,
						'available' => $p['available'],
						'sold' => $p['sold'],
						'stop_sell' => $p['stopSell'] ? 1 : 0,
						'free_sell' => $p['freeSell'] ? 1 : 0,
						'allotment_external_id' => $p['allotmentId'],
						'price_external_id' => $p['id'],
					    'rank_level' => !empty($p['rank']) ? $p['rank'] : 999999,
					];
					$dataNew['hash_summ'] = md5(Json::encode($dataNew));

					if (empty($data[$hash])) {
						try {
						    $Model = new TrPrices();
							$Model->setAttributes($dataNew);
							$Model->save();
							$err = $Model->getErrors();
							if ($err) {
								$this->errors_add[] = $err;
							}
						} catch (Exception $e) {
						    $this->errors_add[] = $e->getMessage();
						}
					} else if ($dataNew['hash_summ'] !== $data[$hash]['hash_summ']) {
					    try {
					        $Model = TrPrices::find()->where(['hash' =>$hash])->one();
    						$Model->setAttributes($dataNew);
    						$Model->save();
    						$err = $Model->getErrors();
    						if ($err) {
    							$this->errors_update[] = $err->getErrors();
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

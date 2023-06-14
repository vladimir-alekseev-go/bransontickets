<?php 
namespace common\models;

use yii\db\Expression;
use common\helpers\General;

trait LocationTrait
{
    public abstract function getSearchAddress();
    
    public function updateLocation($default = '0')
    {
        $search = $this->getSearchAddress();
        
        $this->touch('location_updated_at');

        $geocode = General::geocode($search);
        if (!empty($geocode["lat"]) && !empty($geocode["lng"])) {
            
            $this->location_lat = (string)$geocode["lat"];
            $this->location_lng = (string)$geocode["lng"];
            
            $placeLoc = General::placeNearBySearch([
                'location' => $this->location_lat . ',' . $this->location_lng,
                'name' => $this->name,
            ]);
            
            if ($placeLoc) {
                $this->location_lat = (string)$placeLoc['lat'];
                $this->location_lng = (string)$placeLoc['lng'];
            }
        } else {
            $this->location_lat = (string)$default;
            $this->location_lng = (string)$default;
        }
        $this->save();
        if (!empty($geocode['error_message'])) {
            $this->addError('geocode', $geocode['error_message']);
        }
    }
    
    /**
     * Update locations coordinate
     * @param int $intervalHour
     * @param int $limit
     * @throws Exception
     */
    public static function setLocations($intervalHour = 24, $limit = 10)
    {
        $count = 0;
        $items = self::find()
            ->where(['status' => TrTheaters::STATUS_ACTIVE])
            ->andWhere([
                'OR',
                ['AND',['location_lat'=>null, 'location_lng'=>null]],
                ['AND',['location_updated_at' => null]]
            ])
            ->limit($limit)
            ->all()
            ;
        if (count($items) < $limit) {
            $items = array_merge($items, self::find()
                ->where(['status' => TrTheaters::STATUS_ACTIVE])
                ->andWhere([
                    'AND',
                    ['AND', ['<', 'location_updated_at', new Expression('NOW() - INTERVAL '.$intervalHour.' HOUR')]],
                    ['AND', ['location_lat'=>0, 'location_lng'=>0]],
                ])
                ->limit($limit - count($items))
                ->all()
            );
        }
        foreach ($items as $k => $item) {
            if ($k%10 == 0 && $k != 0) {
                sleep(10);
            }
            $item->updateLocation();
            $count++;
            if (!empty($item->errors['geocode'])) {
                throw new \Exception($item->errors['geocode'][0]);
            }
        }
        return $count;
    }
}
?>
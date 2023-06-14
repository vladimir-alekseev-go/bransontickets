<?php 
namespace common\models;

use common\helpers\MarketingItemHelper;
use Yii;
use yii\base\Model;
use yii\helpers\Json;
use yii\web\Cookie;

class ViewedItems extends Model
{
	public $data = [];
	public $maxItems = 6;

	public function init()
	{
	    if (!empty(Yii::$app->request->cookies->getValue('vieweditems'))) {
	        $this->data = Json::decode(Yii::$app->request->cookies->getValue('vieweditems'));
		}
		
		parent::init();
	}
	
	public function add($category, $id)
	{
		if (!$category || !$id) {
		    return false;
		}
		
		$this->data[$category][$id] = "";
		
		if (count($this->data[$category]) > $this->maxItems) {
			unset($this->data[$category][key($this->data[$category])]);
		}
		
		$cookies = Yii::$app->response->cookies;
		
		$cookies->add(new Cookie([
		    'name' => 'vieweditems',
		    'value' => Json::encode($this->data),
		]));
        return false;
	}
		
	public function getData()
	{
		$viewed = [];
    	
    	if ($this->data) {
    		foreach ($this->data as $category => $ids) {
    			$model = MarketingItemHelper::getItemClassNames()[$category];
    			if (empty($model)) {
    				continue;
    			}
    			
    			$res = $model::find()->joinWith('preview')->where([$model::tableName().".id"=>array_keys($ids)])->all();
    			$ar = array_merge($viewed,$res);
    			$viewed = $ar;
    		}
    	}
    	
		return $viewed;
	}
}

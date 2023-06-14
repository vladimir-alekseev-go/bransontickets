<?php

namespace common\models;

use common\helpers\General;
use Yii;

class ContentFiles extends _source_ContentFiles
{
    /**
     * {@inheritdoc}
     */
    public function beforeDelete()
    {
        if (!parent::beforeDelete()) {
            return false;
        }

        $this->removeFile();

        return true;
    }

    /**
     * Delete file
     * @return bool
     */
    public function removeFile(): bool
    {
        if (empty($this->path) || empty($this->file_name)) {
            return false;
        }

        $fileFullName = Yii::getAlias('@root') . "/{$this->path}{$this->file_name}";

        if (file_exists($fileFullName)) {
            unlink($fileFullName);
            return true;
        }

        return false;
    }

    /**
     * DEPRICATED
     * @deprecated
     */
    public static function getPath($data, $domain = false)
    {
    	if(!is_array($data) && (int)$data > 0)
    	{
    		$data = self::find()->where(["id" =>$data])->asArray()->one();
    	}

    	if(isset($data["path"]) && isset($data["file_name"]))
    	{
    		return ($domain ? '//'.Yii::$app->params['domainImg'] : '').'/'.$data['path'].$data['file_name'];
    	}

    	if(isset($data["id"]))
	    {
    		$res = self::find()->where($data)->asArray()->all();
    		$tmp = [];
    		foreach($res as $ob)
    		{
    			$tmp[$ob["id"]] = self::getPath($ob, $domain);
    		}
    		return $tmp;
    	}

    	if (!empty($data)) {
        	foreach ($data as &$id) {
        	    $id = (int) $id;
        	}
    	}

    	$data = self::find()->where(["id" => $data])->asArray()->all();

		$output = [];

		foreach ($data as $it)
		{
			if ($it["path"] && $it["file_name"])
			{
				$output[$it['id']] = ($domain ? '//'.Yii::$app->params['domainImg'] : '').'/'.$it['path'].$it['file_name'];
			}
		}

        return $output;

    }

    /**
     * Need remake. use delete() instead of removeFile()
     *
     * @param $id
     *
     * @return bool
     */
    public static function remove($id): bool
    {
        if (!$id) {
            return false;
        }

        $ContentFiles = self::findOne($id);
        if ($ContentFiles) {
            $ContentFiles->removeFile();
        }

        return false;
    }

    /**
     * Return url to file
     *
     * @return string
     */
    public function getUrl(): string
    {
        return (Yii::$app->params['domainImg'] ? '//' . Yii::$app->params['domainImg'] : '') . '/' . $this->path . $this->file_name;
    }

    /**
     * Return url to file
     *
     * @return string
     */
    public function getData(): string
    {
        return General::getImageAsData(Yii::getAlias('@root') . '/' . $this->path . $this->file_name);
    }
}

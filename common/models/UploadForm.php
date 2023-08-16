<?php
namespace common\models;

use Exception;
use Yii;
use yii\web\UploadedFile;

abstract class UploadForm extends ContentFiles
{
    public const IMG_EXTENSIONS = ['png', 'jpg', 'jpeg'];
    
    public $pathImage = 'upload/';
    public $file;

    public function rules()
    {
        return [
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg'],
        ];
    }

    public function upload()
    {
        $this->dir = $this->dir_name;
        $pathImage = $this->pathImage . $this->dir . '/';

        if ($this->validate()) {
            if ($this->file) {
                $i = 0;
                while (true) {
                    $dir_name = substr(md5(mt_rand()), 0, 3);
                    $this->file_source_name = $this->file->baseName . '.' . $this->file->extension;
                    $this->file_name = substr(md5(mt_rand()), 0, 16) . '.' . $this->file->extension;
                    $this->path = $pathImage . $dir_name . '/';
                    $this->source_file_time = !empty($this->source_file_time) ? $this->source_file_time : 0;
                    $full_path_file = Yii::getAlias('@root') . '/' . $this->path . $this->file_name;

                    if (!file_exists(Yii::getAlias('@root') . '/' . $this->path)) {
                        mkdir(Yii::getAlias('@root') . '/' . $this->path, 0775, true);
                    }
                    if (!file_exists($full_path_file)) {
                        try {
                            Yii::$app->imageProcessor->save(
                                ['file' => $this->file->tempName],
                                $full_path_file,
                                $this->profile
                            );
                            var_dump($this->tableName());
                            exit();
                            $this->save();
                        } catch (Exception $e) {
                            return false;
                        }
                        return true;
                    }

                    if ($i === 100) {
                        break;
                    }
                    $i++;
                }
            }

            return false;
        }

        return false;
    }
    
    public function downloadByUrl($url)
    {
        if (!$this->setFileByUrl($url)) {
            return false;
        }
        
        $output = $this->upload();
        unlink($this->file->tempName);
        
        return $output;
    }
    
    /**
     * In case of success the function create a file and save its name to $this->file->tempName.
     * You need to remove the file in case you need it no more, it is not done automatically.
     * 
     * @param string $url
     * @return boolean
     */
    protected function setFileByUrl($url)
    {
        if (!empty(Yii::$app->params['replaceDownlowUrlFile']) && is_array(Yii::$app->params['replaceDownlowUrlFile'])) {
            foreach (Yii::$app->params['replaceDownlowUrlFile'] as $search => $replace) {
                $url = str_replace($search, $replace, $url);
            }
        }
        
        $pathTmp = Yii::getAlias('@root'). '/' .$this->pathImage . 'tmp';
        
        if (!file_exists($pathTmp)) {
            mkdir($pathTmp, 0775, true);
        }
        
        $tmpFile = tempnam($pathTmp, 'img');
        $f = fopen($tmpFile, 'w');
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_FILETIME, true);
        curl_setopt($ch, CURLOPT_FILE, $f);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $redirectUrl = $url;
        $allowRedirs = 5;
        $httpCode = 0;

        try {
            do {
                curl_setopt($ch, CURLOPT_URL, $redirectUrl);
                curl_exec($ch);

                $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);
                $httpCode = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            } while ($httpCode !== 200 && $redirectUrl && --$allowRedirs > 0);
        } catch (\yii\base\ErrorException $e) {
            return false;
        }

        $fileTime = curl_getinfo($ch, CURLINFO_FILETIME);

        fclose($f);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            unlink($tmpFile);
            return false;
        }
        
        $extension = pathinfo($url, PATHINFO_EXTENSION);
        $extension = explode('?', $extension);
        $extension = $extension[0];

        $type = \finfo_file(\finfo_open(FILEINFO_MIME_TYPE), $tmpFile);

        if (!getimagesize($tmpFile)) {
            if ($extension === 'jpg' || $extension === 'jpeg') {
                file_put_contents($tmpFile, file_get_contents($url)); // fixed if a file is defined incorrect.
                $type = 'image/jpeg';
            } else {
                unlink($tmpFile);
                return false;
            }
        }

        $newName = "$tmpFile.$extension";
        if (!rename($tmpFile, $newName)) {
            unlink($tmpFile);
            return false;
        }
        $tmpFile = $newName;
        
        $this->source_url = $url;
        $this->source_file_time = $fileTime;
        
        $this->file = new UploadedFile();
        $this->file->name = pathinfo($tmpFile, PATHINFO_BASENAME);
        $this->file->tempName = $tmpFile;
        $this->file->size = filesize($tmpFile);
        $this->file->type = $type;
        
        return true;
    }
}

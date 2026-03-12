<?php
namespace common\components;

use yii\base\Component;
use yii\imagine\Image;
use Imagine\Image\Box;
use Imagine\Image\Point;

class ImageProcessor extends Component
{
    public $define = [];
    public $jpegQuality = 75;
    public $pngCompression = 9;

    public function save($source, $destination, $profileName)
    {
        $path = is_array($source) ? $source['file'] : $source;
        $profile = $this->define[$profileName] ?? ['process' => []];

        $image = Image::getImagine()->open($path);

        foreach ($profile['process'] as $step) {
            $action = $step[0];
            $params = $step;
            array_shift($params); // убираем название действия

            switch ($action) {
                case 'autorotate':
                    $image = Image::autorotate($image);
                    break;

                case 'resize':
                    $width = $params['width'] ?? null;
                    $height = $params['height'] ?? null;
                    $scaleTo = $params['scaleTo'] ?? 'cover';

                    $size = $image->getSize();
                    $ratio = $size->getWidth() / $size->getHeight();

                    if ($scaleTo === 'cover') {
                        // Логика cover: заполнить область
                        if ($width / $height > $ratio) {
                            $newHeight = $width / $ratio;
                            $image->resize(new Box($width, $newHeight));
                        } else {
                            $newWidth = $height * $ratio;
                            $image->resize(new Box($newWidth, $height));
                        }
                    } else {
                        // Логика fit: уместить в область
                        if ($params['only'] === 'down' && $size->getWidth() <= $width && $size->getHeight() <= $height) {
                            break;
                        }
                        $image = Image::resize($image, $width, $height);
                    }
                    break;

                case 'crop':
                    $width = $params['width'];
                    $height = $params['height'];
                    // Центрирование по умолчанию, как в вашем конфиге
                    $size = $image->getSize();
                    $x = max(0, floor(($size->getWidth() - $width) / 2));
                    $y = max(0, floor(($size->getHeight() - $height) / 2));

                    $image->crop(new Point($x, $y), new Box($width, $height));
                    break;
            }
        }

        return $image->save($destination, [
            'jpeg_quality' => $this->jpegQuality,
            'png_compression_level' => $this->pngCompression,
        ]);
    }
}

<?php

namespace console\controllers;

/*use common\models\Banner;
use common\models\BannerContent;
use common\models\Blog;*/
use common\models\ContentFiles;
/*use common\models\HotelsPhotoJoin;
use common\models\Restaurant;
use common\models\TrAttractions;
use common\models\TrHotels;
use common\models\TrLunchs;*/
use common\models\TrShows;
use Yii;
use yii\console\Controller;

class ItemController extends Controller
{
    use CleanControllerTrait;

    /**
     * Mark actual images
     */
    public function actionMarkActualImages(): void
    {
        $this->markActualImages();
    }

    /**
     * Unmark images and delete
     */
    public function actionUnMarkDeleteImages(): void
    {
        $this->unMarkDeleteImages();
    }

    /*public function actionDeleteHotelImages()
    {
        $result = HotelsPhotoJoin::find()->limit(1)->orderBy('id')->all();
        foreach ($result as $item) {
            $item->delete();
        }
    }*/

    /**
     * add to DB image
     */
    public function actionAddFilesToDb()
    {
        $files = $this->dirToArray('upload/');
        //$r = $this->dirToArray('upload/banners-content/');
        //$r = $this->dirToArray('upload/items-photos/');
        $infoFiles = [];
        foreach ($files as $folder => $filesAr) {
            foreach ($filesAr as $subFolder => $fileAr) {
                if (!empty($fileAr['items'])) {
                    foreach ($fileAr['items'] as $fileName) {
                        $infoFiles[] = [
                            'path' => $folder . '/' . $subFolder . '/' . $fileName,
                            'folder' => $folder,
                            'subFolder' => $subFolder,
                            'fileName' => $fileName,
                        ];
                    }
                }
            }
        }
        $counter = 0;
        foreach ($infoFiles as $file) {
            $newContentFiles = new ContentFiles(
                [
                    'old' => 1,
                    'path' => 'upload/' . $file['folder'] . '/' . $file['subFolder'] . '/',
                    'file_name' => $file['fileName'],
                    'file_source_name' => $file['fileName'],
                    'dir' => $file['folder'],
                ]
            );
            $has = (bool)ContentFiles::find()->where(
                [
                    'path' => $newContentFiles->path,
                    'file_name' => $newContentFiles->file_name,
                    'dir' => $newContentFiles->dir,
                ]
            )->one();
            if (!$has) {
                echo '<pre>';
                var_dump($newContentFiles->path);
                echo '</pre>';
                $newContentFiles->removeFile();
                /* $newContentFiles->save();
                $newContentFiles->delete();
                $counter++;
                if ($counter > 100) {
                    $this->refreshAutoIncrement();
                    $counter = 0;
                } */
            }
        }
        //$this->refreshAutoIncrement();
    }

    private function refreshAutoIncrement()
    {
        $lastID = ContentFiles::find()->orderBy('id desc')->one();
        $lastID = $lastID->id + 50;
        $query = Yii::$app->db->createCommand(
            'ALTER TABLE ' . ContentFiles::tableName() . ' AUTO_INCREMENT = ' . $lastID
        );
        $query->execute();
    }

    private function dirToArray($dir)
    {
        $result = [];

        $cdir = scandir($dir);

        $foldersNeedClean = [
            /*'banner',
            'banners-content',
            'blog',
            'our-sponsors',
            'package-usual',
            'services-banners',
            'text-page',
            'vacation-package-image',*/
            'shows-seat-map',
            'items-preview',
            /*'hotel-preview',*/
            'items-photos',
            'items-photos-preview',
        ];

        foreach ($cdir as $key => $value) {
            if (!in_array($value, array(".", ".."))) {
                if (is_dir($dir . '/' . $value)) {
                    if (in_array($value, $foldersNeedClean) || strlen($value) == 3) {
                        if ($value != 'tmp') {
                            $result[$value] = $this->dirToArray($dir . '/' . $value);
                        }
                    }
                } else {
                    $result['items'][] = $value;
                }
            }
        }
        return $result;
    }

    /**
     * Deteting inactive items
     */
    public function actionDelete()
    {
        $items = TrShows::find()->where(['status' => TrShows::STATUS_INACTIVE])->all();
        foreach ($items as $item) {
            echo '<pre>TrShows: ';
            var_dump($item->id);
            echo '</pre>';
            $item->delete();
        }
        /*$items = Restaurant::find()->where(['status' => Restaurant::STATUS_INACTIVE])->all();
        foreach ($items as $item) {
            echo '<pre>Restaurant: ';
            var_dump($item->id);
            echo '</pre>';
            $item->delete();
        }
        $items = TrAttractions::find()->where(['status' => TrAttractions::STATUS_INACTIVE])->all();
        foreach ($items as $item) {
            echo '<pre>TrAttractions: ';
            var_dump($item->id);
            echo '</pre>';
            $item->delete();
        }
        $items = TrLunchs::find()->where(['status' => TrLunchs::STATUS_INACTIVE])->all();
        foreach ($items as $item) {
            echo '<pre>TrLunchs: ';
            var_dump($item->id);
            echo '</pre>';
            $item->delete();
        }
        $items = TrHotels::find()->where(['status' => TrHotels::STATUS_INACTIVE])->all();
        foreach ($items as $item) {
            echo '<pre>TrHotels: ';
            var_dump($item->id);
            echo '</pre>';
            $item->delete();
        }
        $items = Banner::find()->where(['status' => Banner::STATUS_INACTIVE])->all();
        foreach ($items as $item) {
            echo '<pre>Banner: ';
            var_dump($item->id);
            echo '</pre>';
            $item->delete();
        }
        $items = BannerContent::find()->where(['status' => BannerContent::STATUS_INACTIVE])->all();
        foreach ($items as $item) {
            echo '<pre>BannerContent: ';
            var_dump($item->id);
            echo '</pre>';
            $item->delete();
        }
        $items = Blog::find()->where(['status' => Blog::STATUS_INACTIVE])->all();
        foreach ($items as $item) {
            echo '<pre>Blog: ';
            var_dump($item->id);
            echo '</pre>';
            $item->delete();
        }*/
    }

    /**
     * Delete old type hotels images
     */
    /*public function actionDeleteOldTypeHotelsImages()
    {
        foreach (TrHotels::find()->all() as $hotel) {
            var_dump($hotel->id . ' - ' . $hotel->name);

            if (!empty($hotel->preview)) {
                $hotel->preview->delete();
            }
            if (!empty($hotel->image)) {
                $hotel->image->delete();
            }
            if (!empty($hotel->seat_map)) {
                $hotel->seat_map->delete();
            }
            foreach ($hotel->relatedPhotos as $itemPhoto) {
                if (!empty($itemPhoto->photo)) {
                    $itemPhoto->photo->delete();
                }
                if (!empty($itemPhoto->preview)) {
                    $itemPhoto->preview->delete();
                }
                $itemPhoto->delete();
            }
            $hotel->setAttributes(
                [
                    'preview_id' => null,
                    'image_id' => null,
                ]
            );
            $hotel->save();
        }
    }*/
}

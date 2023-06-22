<?php

namespace common\models\upload;

use \common\models\UploadForm;

class UploadItemsPhotos extends UploadForm
{
    public $dir_name = 'items-photos';
    public $profile = 'itemsPhotos';
    public $files;
}

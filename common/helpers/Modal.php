<?php

namespace common\helpers;

class Modal extends \yii\bootstrap\Modal
{
    protected function initOptions()
    {
        parent::initOptions();
        if ($this->closeButton !== false) {
            unset($this->closeButton['data-dismiss']);
            $this->closeButton = array_merge(
                $this->closeButton,
                [
                    'data-bs-dismiss' => 'modal',
                ]
            );
        }
    }
}

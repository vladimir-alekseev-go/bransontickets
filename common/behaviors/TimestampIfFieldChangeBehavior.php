<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\behaviors;

use yii\base\InvalidCallException;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\db\BaseActiveRecord;

class TimestampIfFieldChangeBehavior extends AttributeBehavior
{
    public $trackAttribute = 'status';

    public $changeAttribute = 'change_status_date';

    public $value;

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->changeAttribute],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->changeAttribute,
            ];
        }
    }

    /**
     * {@inheritdoc}
     *
     * In case, when the [[value]] is `null`, the result of the PHP function [time()](https://secure.php.net/manual/en/function.time.php)
     * will be used as value.
     */
    protected function getValue($event)
    {
        /** @var ActiveRecord $owner */
        $owner = $this->owner;
        if (isset($owner->dirtyAttributes[$this->trackAttribute])
            && $owner->dirtyAttributes[$this->trackAttribute] !== null) {
            if ($this->value === null) {
                return time();
            }

            return parent::getValue($event);
        }

        return $owner->{$this->changeAttribute};
    }
}

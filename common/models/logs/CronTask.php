<?php

namespace common\models\logs;

/*use common\models\TrAttractionsPrices;
use common\models\TrLunchsPrices;
use common\models\TrPosHotelsPriceRoom;*/
use common\models\TrPrices;
use DateInterval;
use DatePeriod;
use DateTime;
use Exception;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Json;

class CronTask extends _source_CronTask
{
    public const TYPE_VENDOR = 'vendor';
    public const TYPE_VENDOR_PRICE = 'vendor-price';
    public const TYPE_VACATION_PACKAGE = 'vacation-package';
    public const TYPE_MARKUP = 'markup';

    public const STATUS_CREATED = 'created';
    public const STATUS_STARTED = 'started';
    public const STATUS_FINISHED = 'finished';

    public const MARKUP_PERIOD_UPDATE = 90;
    public const MARKUP_FULL_PERIOD_UPDATE = 450;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    public static function deleteOldItems(): void
    {
        /**
         * @var CronTask[] $tasks
         */
        $tasks = self::find()->select(['id'])
            ->where(['<', 'created_at', (new DateTime())->sub(new DateInterval('P1D'))->format('Y-m-d H:i:s')])
            ->all()
        ;
        try {
            foreach ($tasks as $task) {
                $task->delete();
            }
        } catch (Exception $e) {}
    }

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        if (empty($this->data)) {
            return null;
        }
        $data = Json::decode($this->data);
        if (empty($data)) {
            return null;
        }
        return $data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data): void
    {
        $this->setAttribute('data', Json::encode($data));
    }

    /**
     * @param string $type
     * @param string $rawBody
     */
    public static function generateTasks(string $type, string $rawBody): void
    {
        if ($type === self::TYPE_MARKUP) {
            self::generateMarkupTasks($rawBody);
        }
    }

    /**
     * @param string $rawBody
     *
     * @return array
     */
    private static function getVendorIdsWithCategories(string $rawBody): array
    {
        $data = [];
        $categories = [
            TrPrices::TYPE_ID,
            /*TrAttractionsPrices::TYPE_ID,
            TrLunchsPrices::TYPE_ID,
            TrPosHotelsPriceRoom::TYPE_ID,*/
        ];
        foreach (Json::decode($rawBody)['results'] as $item) {
            if (in_array((int)$item['category'], $categories, true)) {
                if (!isset($data[$item['category']])) {
                    $data[$item['category']] = [];
                }
                if (!in_array((int)$item['vendorId'], $data[$item['category']], true)) {
                    $data[$item['category']][] = (int)$item['vendorId'];
                }
            }
        }
        return $data;
    }

    /**
     * @param string $rawBody
     */
    private static function generateMarkupTasks(string $rawBody): void
    {
        $data = self::getVendorIdsWithCategories($rawBody);

        try {
            $range = new DatePeriod(
                new DateTime(),
                new DateInterval('P' . self::MARKUP_PERIOD_UPDATE . 'D'),
                (new DateTime())->add(new DateInterval('P' . self::MARKUP_FULL_PERIOD_UPDATE . 'D'))
            );

            foreach ($range as $k => $dateFrom) {
                $dateTo = clone $dateFrom;
                $dateTo->add(new DateInterval('P' . self::MARKUP_PERIOD_UPDATE . 'D'));

                $task = new self();
                $task->setData(
                    [
                        'dateFrom' => $dateFrom->format('Y-m-d 00:00:00'),
                        'dateTo' => $dateTo->format('Y-m-d 00:00:00'),
                        'vendorIds' => $data,
                    ]
                );
                $task->setAttribute('status', self::STATUS_CREATED);
                $task->setAttribute('type', self::TYPE_MARKUP);
                $task->save();
            }
        } catch (Exception $e) {}
    }

    /**
     * @return DateTime|null
     */
    public function getDateFrom(): ?DateTime
    {
        try {
            if (!empty($this->getData()['dateFrom'])) {
                return new DateTime($this->getData()['dateFrom']);
            }
        } catch (Exception $e) {}
        return null;
    }

    /**
     * @return DateTime|null
     */
    public function getDateTo(): ?DateTime
    {
        try {
            if (!empty($this->getData()['dateTo'])) {
                return new DateTime($this->getData()['dateTo']);
            }
        } catch (Exception $e) {}
        return null;
    }

    /**
     * @param int $typeId
     *
     * @return array|null
     */
    public function getVendorsId(int $typeId): ?array
    {
        if (!empty($this->getData()['vendorIds'][$typeId])) {
            return $this->getData()['vendorIds'][$typeId];
        }
        return null;
    }
}

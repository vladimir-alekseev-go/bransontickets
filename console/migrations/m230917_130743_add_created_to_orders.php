<?php

use common\models\TrOrders;
use yii\db\Migration;

/**
 * Class m230917_130743_add_created_to_orders
 */
class m230917_130743_add_created_to_orders extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('tr_orders', 'created', $this->dateTime()->null());

        Yii::$app->db->schema->refresh();

        /**
         * @var TrOrders[] $orders
         */
        $orders = TrOrders::find()->all();
        foreach ($orders as $order) {
            if ($order->getCreatedDate()) {
                $order->created = $order->getCreatedDate()->format('Y-m-d H:i:s');
                $order->save();
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}

<?php

use yii\db\Migration;

/**
 * Class m241213_083050_change_basket_session
 */
class m241213_083050_change_basket_session extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function up()
    {
        $this->alterColumn('tr_basket', 'session_id', $this->string(36)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function down()
    {
        echo "m241213_083050_change_basket_session cannot be reverted.\n";

        return false;
    }
}

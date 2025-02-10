<?php

use yii\db\Migration;

/**
 * Class m240321_161537_add_change_status_date
 */
class m240321_161537_add_change_status_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('tr_shows', 'change_status_date', $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('tr_attractions', 'change_status_date', $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('tr_pos_hotels', 'change_status_date', $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('tr_pos_pl_hotels', 'change_status_date', $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
        $this->addColumn('vacation_package', 'change_status_date', $this->datetime()->notNull()->defaultExpression('CURRENT_TIMESTAMP'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240321_161537_add_change_status_date cannot be reverted.\n";

        return false;
    }
}

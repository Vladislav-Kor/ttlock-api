<?php

use yii\db\Migration;

/**
 * Class m230803_080030_create_lock
 */
class m230803_080030_create_lock extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%lock}}', [
            'lockId' => $this->integer(),
            'keyId' => $this->integer(),
            'clientId' => $this->string(255),
            'lockData' => $this->text(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230803_080030_create_lock cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230803_080030_create_lock cannot be reverted.\n";

        return false;
    }
    */
}

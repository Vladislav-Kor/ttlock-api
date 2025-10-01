<?php

use yii\db\Migration;

/**
 * Class m230803_074237_create_user
 */
class m230803_074237_create_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%users}}', [
            'id' => $this->primaryKey(),
            'access_token' => $this->string(32),
            'refresh_token' => $this->string(32),
            'uid' => $this->string(32),
            'scope' => $this->string(50),
            'expires_in' => $this->string(32),
            'username' => $this->string(),
            'password' => $this->string(),
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=MyISAM');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230803_074237_create_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230803_074237_create_user cannot be reverted.\n";

        return false;
    }
    */
}

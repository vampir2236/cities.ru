<?php

use yii\db\Schema;
use yii\db\Migration;

class m150802_070349_update_table_user extends Migration
{
    public function up()
    {
        $this->dropColumn('user', 'username');
        $this->addColumn('user', 'email_confirm_token', Schema::TYPE_STRING . ' NOT NULL');
        $this->addColumn('user', 'fio', Schema::TYPE_STRING . ' NOT NULL');
        $this->addColumn('user', 'phone', Schema::TYPE_STRING . '(20) NOT NULL');

        $this->createIndex('uk_user_email', 'user', 'email', true);
    }

    public function down()
    {
        $this->addColumn('user', 'username', Schema::TYPE_STRING . ' NOT NULL');
        $this->dropColumn('user', 'fio');
        $this->dropColumn('user', 'phone');

        $this->dropIndex('uk_user_email', 'user');
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}

<?php

use yii\db\Schema;
use yii\db\Migration;

class m150802_071345_create_table_review_city extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('review_city', [
            'id' => Schema::TYPE_PK,
            'id_review' => Schema::TYPE_INTEGER . ' NOT NULL',
            'id_city' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);
        $this->addForeignKey('fk_review_id_review', 'review_city', 'id_review', 'review', 'id', 'CASCADE');
        $this->addForeignKey('fk_review_id_city', 'review_city', 'id_city', 'city', 'id', 'CASCADE');
        $this->createIndex('uk_review_id_review_id_city', 'review_city', 'id_review, id_city', true);
    }

    public function down()
    {
        $this->dropTable('review_city');
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

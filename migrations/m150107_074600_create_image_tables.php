<?php

use yii\db\Schema;
use yii\db\Migration;

class m150107_074600_create_image_tables extends Migration
{
    public function up()
    {
        $tableOptions = null;

        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        
        // Drop image table
        if ($this->db->schema->getTableSchema('image', true)) {
            $this->dropTable('image');
        }

        $this->createTable('{{%image}}', [
            'id'                    => Schema::TYPE_PK,
            'name'                  => Schema::TYPE_STRING . '(255) NOT NULL',
            'filePath'              => Schema::TYPE_STRING . '(400) NOT NULL',
            'itemId'                => Schema::TYPE_STRING . '(255) NOT NULL',
            'isMain'                => 'TINYINT(3) UNSIGNED NOT NULL DEFAULT \'1\'',
            'modelName'             => Schema::TYPE_STRING . '(255) NOT NULL',
            'urlAlias'              => Schema::TYPE_STRING . '(255) NOT NULL',
            'position'              => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'active'                => 'TINYINT(3) UNSIGNED NOT NULL DEFAULT \'1\'',
            'created_at'            => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'updated_at'            => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
        ], $tableOptions);

        $this->createIndex('itemId', '{{%image}}', 'itemId');
        
        // Create 'image_lang' table
        $this->createTable('{{%image_lang}}', [
            'image_id'              => Schema::TYPE_INTEGER . ' NOT NULL',
            'language'              => Schema::TYPE_STRING . '(2) NOT NULL',
            'alt'                   => Schema::TYPE_STRING . '(255) NOT NULL',
            'title'                 => Schema::TYPE_STRING . '(255) NOT NULL',
            'subtitle'              => Schema::TYPE_STRING . ' NOT NULL',
            'description'           => Schema::TYPE_TEXT . ' NOT NULL',
            'url'                   => Schema::TYPE_STRING . '(255) NOT NULL',
            'created_at'            => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
            'updated_at'            => Schema::TYPE_INTEGER . ' UNSIGNED NOT NULL',
        ], $tableOptions);

        $this->addPrimaryKey('image_lang_image_id_language', '{{%image_lang}}', ['image_id', 'language']);
        $this->createIndex('language', '{{%image_lang}}', 'language');
        $this->addForeignKey('FK_IMAGE_LANG_IMAGE_ID', '{{%image_lang}}', 'image_id', '{{%image}}', 'id', 'CASCADE', 'NO ACTION');
    }

    public function down()
    {
        $this->dropTable('image_lang');
        $this->dropTable('image');
    }
}

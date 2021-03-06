<?php

use yii\db\Schema;
use yii\db\Migration;

class m141001_090124_add_default_permissions extends Migration
{
    public function up()
    {
        // Create the auth items
        $this->insert('{{%auth_item}}', [
            'name'          => 'showContentModule',
            'type'          => 2,
            'description'   => 'Show content module icon in main-menu',
            'created_at'    => time(),
            'updated_at'    => time()
        ]);

        $this->insert('{{%auth_item}}', [
            'name'          => 'showModulesModule',
            'type'          => 2,
            'description'   => 'Show modules module icon in main-menu',
            'created_at'    => time(),
            'updated_at'    => time()
        ]);

        $this->insert('{{%auth_item}}', [
            'name'          => 'showRightsModule',
            'type'          => 2,
            'description'   => 'Show rights module icon in main-menu',
            'created_at'    => time(),
            'updated_at'    => time()
        ]);
        
        $this->insert('{{%auth_item}}', [
            'name'          => 'showUsersModule',
            'type'          => 2,
            'description'   => 'Show users module icon in main-menu',
            'created_at'    => time(),
            'updated_at'    => time()
        ]);

        $this->insert('{{%auth_item}}', [
            'name'          => 'showTranslationsModule',
            'type'          => 2,
            'description'   => 'Show translations module in main-menu',
            'created_at'    => time(),
            'updated_at'    => time()
        ]);

        $this->insert('{{%auth_item}}', [
            'name'          => 'showMediaModule',
            'type'          => 2,
            'description'   => 'Show media module icon in main-menu',
            'created_at'    => time(),
            'updated_at'    => time()
        ]);

        $this->insert('{{%auth_item}}', [
            'name'          => 'showAliasModule',
            'type'          => 2,
            'description'   => 'Show alias module icon in main-menu',
            'created_at'    => time(),
            'updated_at'    => time()
        ]);
        
        // Create the auth item relation
        $this->insert('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showContentModule'
        ]);

        $this->insert('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showModulesModule'
        ]);

        $this->insert('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showRightsModule'
        ]);
        
        $this->insert('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showUsersModule'
        ]);
        
        $this->insert('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showTranslationsModule'
        ]);

        $this->insert('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showMediaModule'
        ]);

        $this->insert('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showAliasModule'
        ]);

    }

    public function down()
    {
        // Delete the relations
        $this->delete('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showContentModule'
        ]);

        $this->delete('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showModulesModule'
        ]);

        $this->delete('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showRightsModule'
        ]);
        
        $this->delete('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showUsersModule'
        ]);

        $this->delete('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showTranslationsModule'
        ]);

        $this->delete('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showShopModule'
        ]);

        $this->delete('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showMediaModule'
        ]);

        $this->delete('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showCatalogueModule'
        ]);

        $this->delete('{{%auth_item_child}}', [
            'parent'        => 'Superadmin',
            'child'         => 'showAliasModule'
        ]);


        
        // Delete the auth items
        $this->delete('{{%auth_item}}', [
            'name'          => 'showContentModule',
            'type'          => 2,
        ]);

        $this->delete('{{%auth_item}}', [
            'name'          => 'showModulesModule',
            'type'          => 2,
        ]);

        $this->delete('{{%auth_item}}', [
            'name'          => 'showRightsModule',
            'type'          => 2,
        ]);
        
        $this->delete('{{%auth_item}}', [
            'name'          => 'showUsersModule',
            'type'          => 2,
        ]);
        
        $this->delete('{{%auth_item}}', [
            'name'          => 'showTranslationsModule',
            'type'          => 2,
        ]);

        $this->delete('{{%auth_item}}', [
            'name'          => 'showMediaModule',
            'type'          => 2,
        ]);

        $this->delete('{{%auth_item}}', [
            'name'          => 'showAliasModule',
            'type'          => 2,
        ]);
    }
}

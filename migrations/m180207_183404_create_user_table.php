<?php

use yii\db\Migration;

/**
 * Handles the creation of table `user`.
 */
class m180207_183404_create_user_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('user', [
            'id'      => $this->primaryKey(),
            'login'   => $this->string()->notNull(),
            'status'  => $this->boolean()->notNull()->defaultValue(0),
            'updated' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        $this->createTable('user_status_changes', [
            'id'      => $this->primaryKey(),
            'user_id' => $this->integer()->notNull(),
            'status'  => $this->boolean()->notNull(),
            'created' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        $this->addForeignKey(
            'fk-user-user_id-user-id',
            'user_status_changes',
            'user_id',
            'user',
            'id',
            'CASCADE',
            'CASCADE'
        );
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropForeignKey('fk-user-user_id-user-id', 'user_status_changes');
        $this->dropTable('user_status_changes');
        $this->dropTable('user');
    }
}

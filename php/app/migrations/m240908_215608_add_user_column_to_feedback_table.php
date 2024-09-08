<?php

use yii\db\Migration;

/**
 * yii migrate/create add_user_column_to_feedback_table --fields="user_id:integer:notNull:defaultValue(1):foreignKey(user)"
 * Handles adding columns to table `{{%feedback}}`.
 * Has foreign keys to the tables:
 *
 * - `{{%user}}`
 */
class m240908_215608_add_user_column_to_feedback_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%feedback}}', 'user_id', $this->integer()->notNull()->defaultValue(1));

        // creates index for column `user_id`
        $this->createIndex(
            '{{%idx-feedback-user_id}}',
            '{{%feedback}}',
            'user_id'
        );

        // add foreign key for table `{{%user}}`
        $this->addForeignKey(
            '{{%fk-feedback-user_id}}',
            '{{%feedback}}',
            'user_id',
            '{{%user}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // drops foreign key for table `{{%user}}`
        $this->dropForeignKey(
            '{{%fk-feedback-user_id}}',
            '{{%feedback}}'
        );

        // drops index for column `user_id`
        $this->dropIndex(
            '{{%idx-feedback-user_id}}',
            '{{%feedback}}'
        );

        $this->dropColumn('{{%feedback}}', 'user_id');
    }
}

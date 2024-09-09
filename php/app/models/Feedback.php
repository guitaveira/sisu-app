<?php

namespace app\models;

use Yii;
use yii\web\ForbiddenHttpException;
use webvimark\modules\UserManagement\models\User;
/**
 * This is the model class for table "feedback".
 *
 * @property int $id
 * @property string|null $nome
 * @property string|null $email
 * @property string|null $feedback
 * @property int|null $idade
 * @property int|null $user_id
 */
class Feedback extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'feedback';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nome', 'email', 'feedback','user_id'], 'required'],
            [['nome', 'email', 'feedback'], 'string'],
            [['idade'], 'default', 'value' => null],
            [['user_id'], 'default', 'value' => Yii::$app->user->id],
            [['idade','user_id'], 'integer'],
            [['email'], 'email','checkDNS'=> true],
            [['user_id'],'exist','skipOnError' => true, 'targetRelation'=> 'user'],
            [['user_id'],'ownerCheck'],
        ];
    }

    /**
     * @throws ForbiddenHttpException
     */
    public function ownerCheck($attribute, $params)
    {
        if ( $this->user_id !=Yii::$app->user->id && User::hasPermission("change_only_yours", $superAdminAllowed = false) ){
            throw new \yii\web\ForbiddenHttpException('Você não tem permissão para modificar este dado');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'nome' => 'Nome',
            'email' => 'E-mail',
            'feedback' => 'Feedback',
            'idade' => 'Idade',
            'user_id' => 'User',
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'user_id']);
    }
}

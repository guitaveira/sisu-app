<?php
namespace app\models;

use webvimark\modules\UserManagement\models\User;
use yii\web\ForbiddenHttpException;

class CustomUser extends  User
{
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $claims = \Yii::$app->jwt->parse($token)->claims();
        $uid = $claims->get('uid');
        if (!is_numeric($uid)) {
            throw new ForbiddenHttpException('Invalid token provided');
        }
        return static::findOne(['id' => $uid]);
    }
}

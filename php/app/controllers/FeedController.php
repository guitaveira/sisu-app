<?php

namespace app\controllers;
use yii\rest\ActiveController;

class FeedController extends ActiveController
{
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'authenticator' => [
                    'class' => \bizley\jwt\JwtHttpBearerAuth::class,
                ],
            ]
        );
    }
    public $modelClass = 'app\models\Feedback';
}
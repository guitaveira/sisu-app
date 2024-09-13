<?php

namespace app\controllers;

use yii\rest\ActiveController;

class FeedController extends ActiveController
{
    public $modelClass = 'app\models\Feedback';
}
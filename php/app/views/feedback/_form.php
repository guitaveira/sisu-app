<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/** @var yii\web\View $this */
/** @var app\models\Feedback $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="feedback-form">

    <?php
        $form = ActiveForm::begin();
        echo $form->errorSummary($model);
    ?>

    <?= $form->field($model, 'nome')->textInput() ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <?= $form->field($model, 'feedback')->textarea() ?>

    <?= $form->field($model, 'idade')->textInput() ?>

    <?= $form->field($model, 'user_id')->hiddenInput()->label(""); ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

use mootensai\enhancedgii\crud\Generator;

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\crud\Generator */

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

?>
<?= "<?php" ?>

/**
 * CREATED BY A CODE GENERATOR!!!!
 * THIS FILE WAS CREATED BY A HEAVILY MODIFIED yii2-enhanced-gii for use in GRS.
 * Hand editing this file will result in lost code.
 *
 * _data.php
 */

use yii\helpers\Html;
use kartik\tabs\TabsX;
use yii\helpers\Url;
$items = [
    [
        'label' => '<i class="glyphicon glyphicon-user"></i> '. Html::encode(<?= $generator->generateString(Inflector::camel2words($rel[Generator::REL_CLASS])) ?>),
        'content' => $this->render('_view', [
            'all' => false,
        ]),
    ],
<?php foreach ($relations as $name => $rel): ?>
    <?php if ($rel[Generator::REL_IS_MULTIPLE] && isset($rel[Generator::REL_TABLE]) && !in_array($name, $generator->skippedRelations)): ?>
    [
        'label' => '<i class="glyphicon glyphicon-user"></i> '. Html::encode(<?= $generator->generateString(Inflector::camel2words($rel[Generator::REL_CLASS])) ?>),
        'content' => $this->render('_data<?= $rel[Generator::REL_CLASS] ?>', [
            'model' => $model,
            'row' => $model-><?= $name ?>,
        ]),
    ],
    <?php endif; ?>
<?php endforeach; ?>
];
echo TabsX::widget([
    'items' => $items,
    'position' => TabsX::POS_ABOVE,
    'encodeLabels' => false,
    'class' => 'tes',
    'pluginOptions' => [
        'bordered' => true,
        'sideways' => true,
        'enableCache' => false
        //        'height' => TabsX::SIZE_TINY
    ],
    'pluginEvents' => [
        "tabsX.click" => "function(e) {setTimeout(function(e){
                if ($('.nav-tabs > .active').next('li').length == 0) {
                    $('#prev').show();
                    $('#next').hide();
                } else if($('.nav-tabs > .active').prev('li').length == 0){
                    $('#next').show();
                    $('#prev').hide();
                }else{
                    $('#next').show();
                    $('#prev').show();
                };
                console.log(JSON.stringify($('.active', '.nav-tabs').html()));
            },10)}",
    ],
]);
?>

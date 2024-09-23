<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

use mootensai\enhancedgii\crud\Generator;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();

echo "<?php\n";
?>

/**
 * CREATED BY A CODE GENERATOR!!!!
 * THIS FILE WAS CREATED BY A HEAVILY MODIFIED yii2-enhanced-gii for use in GRS.
 * Hand editing this file will result in lost code.
 *
 * update.php
 */

use yii\helpers\Inflector;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

$isManyEdit = (isset($hasManyEditClass) ? true : false);

if (! $isManyEdit) {
    $this->title = <?= $generator->generateString('Edit {modelClass}: ', ['modelClass' => Inflector::camel2words(StringHelper::basename($generator->modelClass))]) ?> . ' ' . $model-><?= $generator->getNameAttribute() ?>;
}
else {
    $this->title = 'Edit ' . Inflector::camel2words(Inflector::pluralize($hasManyEditClass));
}
$this->params['breadcrumbs'][] = ['label' => <?= ($generator->pluralize) ? $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) : $generator->generateString(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model-><?= $generator->getNameAttribute() ?>, 'url' => ['view', <?= $urlParams ?>]];
if ($isManyEdit) {
    $this->params['breadcrumbs'][] = Inflector::camel2words(Inflector::pluralize($hasManyEditClass));
}
$this->params['breadcrumbs'][] = <?= $generator->generateString('Edit') ?>;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-update">

<?php
echo "<?php\n";
echo "\t\t" . "\$updateSkipCols = array();\n";
foreach ($generator->skippedColumnsInUpdate as $colName) {
    echo "\t\t" . "array_push(\$updateSkipCols, '$colName');" . "\n";
}
echo "\t" . "?>\n";
?>

<?=
"<?php " ?>

if ($isManyEdit) {
    echo $this->render('_form', [
        'model' => $model,
        'updateSkipCols' => $updateSkipCols,
        'hasManyEditClass' => $hasManyEditClass,
        'title' => $this->title,
    ]);
}
else {
     echo $this->render('_form', [
        'model' => $model,
        'updateSkipCols' => $updateSkipCols,
        'title' => $this->title,
    ]);
}

?>

</div>

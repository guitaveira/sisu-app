<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

use mootensai\enhancedgii\crud\Generator;

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\crud\Generator */
/* @var $relations array */
/* @var $isTree boolean */

$tableSchema = $generator->getTableSchema();
$fk = $generator->generateFK($tableSchema);
$model = ($isTree) ? '$node' : '$model';
echo "<?php\n";
?>

/**
 * CREATED BY A CODE GENERATOR!!!!
 * THIS FILE WAS CREATED BY A HEAVILY MODIFIED yii2-enhanced-gii for use in GRS.
 * Hand editing this file will result in lost code.
 *
 * _formNested.php
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var <?= $model?> <?= ltrim($generator->modelClass, '\\') ?> */

?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

<?php
foreach ($tableSchema->getColumnNames() as $attribute) {
    if (!in_array($attribute, $generator->skippedColumns) && !in_array($attribute, $generator::getTreeColumns())) {
        echo "    <?= " . $generator->generateActiveField($attribute, $fk, null, null, $isTree) . " ?>\n\n";
    }
}
?>

</div>

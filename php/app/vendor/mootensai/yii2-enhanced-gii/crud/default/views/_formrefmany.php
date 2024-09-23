<?php
/* @var $generator \mootensai\enhancedgii\crud\Generator */
/* @var $relations array */

use mootensai\enhancedgii\crud\Generator;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

$tableSchema = $generator->getDbConnection()->getTableSchema($relations[Generator::REL_TABLE]);
$fk = $generator->generateFK($tableSchema);
$relID = \yii\helpers\Inflector::camel2id($relations[Generator::REL_CLASS]);

$finalName = str_replace($generator->fieldNameStrip, '', $relations[Generator::REL_CLASS]);
$humanize = ucwords(Inflector::humanize(Inflector::camel2words($finalName)));

echo "<div class=\"form-group\" id=\"add-$relID\">\n";
echo "<?php\n";


?>

/**
 * CREATED BY A CODE GENERATOR!!!!
 * THIS FILE WAS CREATED BY A HEAVILY MODIFIED yii2-enhanced-gii for use in GRS.
 * Hand editing this file will result in lost code.
 *
 * _formrefmany.php
 */
 
use kartik\grid\GridView;
use kartik\builder\TabularForm;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use yii\widgets\Pjax;

$dataProvider = new ArrayDataProvider([
    'allModels' => $row,
    'pagination' => [
        'pageSize' => -1
    ]
]);
echo TabularForm::widget([
    'dataProvider' => $dataProvider,
    'formName' => '<?= $relations[Generator::REL_CLASS]; ?>',
    'checkboxColumn' => false,
    'actionColumn' => false,
    'attributeDefaults' => [
        'type' => TabularForm::INPUT_TEXT,
    ],
    'attributes' => [

<?php

    $className = $relations[Generator::REL_CLASS];

    $hiddenFields = ArrayHelper::getValue($generator->manyRelationsHiddenFieldTweakList, $className, array());
    foreach ($hiddenFields as $fieldName => $fieldValueList) {
            $fieldValue = $fieldValueList[0];
            echo "        " . "'$fieldName' => ['columnOptions'=>['hidden'=>true],'value' =>".$fieldValue."],\n";
    }

    $allowedCols = ArrayHelper::getValue($generator->manyRelationsEditFieldList, $className, array());
    $allColsAllowed = count($allowedCols) ? false : true;
    foreach ($tableSchema->getColumnNames() as $attribute) {
        $column = $tableSchema->getColumn($attribute);
        if (!in_array($attribute, $generator->skippedColumns) &&
            $attribute != $relations[Generator::REL_FOREIGN_KEY] &&
            ($allColsAllowed || in_array($attribute, $allowedCols)))
        {
            echo "        " . $generator->generateTabularFormField($attribute, $fk, $tableSchema) . ",\n";
        }
    }

?>

        'del' => [
            'type' => 'raw',
            'label' => '',
            'value' => function($model, $key) {
                return
                    Html::hiddenInput('Children[' . $key . '][id]', (!empty($model['id'])) ? $model['id'] : "") .
                    Html::a('<i class="material-icons">delete</i>', '#', ['title' =>  <?= $generator->generateString('Delete') ?>, 'onClick' => 'delRow<?= $relations[$generator::REL_CLASS]; ?>(' . $key . '); return false;', 'id' => '<?= yii\helpers\Inflector::camel2id($relations[$generator::REL_CLASS]) ?>-del-btn']);
            },
        ],
    ],
    'gridSettings' => [
        'panel' => [
            'heading' => false,
            'type' => GridView::TYPE_DEFAULT,
            'after' => false,
            'footer' => false,
            'before' => Html::button('<i class="material-icons">add</i>' . <?= $generator->generateString('Add '.$humanize) ?>, ['type' => 'button', 'class' => 'btn btn-primary btn-round kv-batch-create', 'onClick' => 'addRow<?= $relations[$generator::REL_CLASS]; ?>()']),
        ]
    ]
]);
echo  "    </div>\n\n";
?>

<?= "<?php\n" ?>
unset($this->assetBundles['yii\bootstrap4\BootstrapAsset']);
<?= "?>\n" ?>

<style>
    .btn.btn-outline-secondary{
        padding:3px 7px
    }
    .btn.btn-outline-secondary.active.focus{
        color:#fff;
        background-color: #6c757d
    }
    textarea{
        max-height:34px
    }
    .select2-container--krajee-bs4 .select2-selection--single{
        max-height:34px
    }
</style>


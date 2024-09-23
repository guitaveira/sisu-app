<?php

use yii\helpers\Inflector;
use mootensai\enhancedgii\crud\Generator;

use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\crud\Generator */
$tableSchema = $generator->getDbConnection()->getTableSchema($relations[Generator::REL_TABLE]);
$fk = $generator->generateFK($tableSchema);

if (isset($generator->webUserColName)) {
    $classNamePlural = Inflector::pluralize($relations[Generator::REL_CLASS]);
    $modelQueryString = "get".$classNamePlural."()->andWhere( [ 'user_id'=>Yii::\$app->user->id ] )->all()";
}
else {
    $modelQueryString = $relName;
}

?>
<?= "<?php" ?>

/**
 * CREATED BY A CODE GENERATOR!!!!
 * THIS FILE WAS CREATED BY A HEAVILY MODIFIED yii2-enhanced-gii for use in GRS.
 * Hand editing this file will result in lost code.
 *
 * _datarefmany.php
 */

use kartik\grid\GridView;
use yii\data\ArrayDataProvider;

    $dataProvider = new ArrayDataProvider([
        'allModels' => $model-><?= $modelQueryString ?>,
<?php if (count($tableSchema->primaryKey) > 1):
    $key = [];
    foreach ($tableSchema->primaryKey as $pk) {
        $key[] = "'$pk' => \$model->$pk";
    }
?>
        'key' => function($model){
            return [<?= implode(', ', $key); ?>];
        }
<?php else:?>
        'key' => '<?= $tableSchema->primaryKey[0] ?>'
<?php endif; ?>
    ]);
    $gridColumns = [
        ['class' => 'yii\grid\SerialColumn'],
<?php
if ($tableSchema === false)
{
    foreach ($generator->getColumnNames() as $name) {
        if ($name == $relations[Generator::REL_PRIMARY_KEY]) {
            continue;
        }

        $count++;
        if ($count < 6) {
            echo "            '" . $name . "',\n";
        }
        else {
            echo "            // '" . $name . "',\n";
        }
    }
}
else
{
    // define what columns to show
    $className = $relations[Generator::REL_CLASS];
    $allowedColList = ArrayHelper::getValue($generator->relationsExpandableRelatedColList, $className);
    $allowedColList[] = ArrayHelper::getValue($generator->manyRelationsViewFieldList, $className);
    $allowAllCols = (isset($allowedColList) ? false : true);

    foreach ($tableSchema->getColumnNames() as $attribute)
    {
        if ($allowAllCols || in_array($attribute, $allowedColList)) {
            if (!in_array($attribute, $generator->skippedColumns) && $attribute != $relations[Generator::REL_FOREIGN_KEY])
            {
                echo "\t\t" . $generator->generateGridViewField($attribute, $fk, $tableSchema);
            }
        }
    }
}
?>
<?php
    if (isset($generator->relationsExpandableRelatedActions) && strlen($generator->relationsExpandableRelatedActions) > 0) :
?>
        [
            'class' => 'yii\grid\ActionColumn',
<?php
        echo "\t\t\t'template' => '" . $generator->relationsExpandableRelatedActions . "', \n";
?>
            'controller' => '<?= \yii\helpers\Inflector::camel2id($relations[Generator::REL_CLASS])?>'
        ],
<?php endif ?>

    ];

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'containerOptions' => ['style' => 'overflow: auto'],
        'pjax' => true,
        'beforeHeader' => [
            [
                'options' => ['class' => 'skip-export']
            ]
        ],
        'export' => [
            'fontAwesome' => true
        ],
        'bordered' => true,
        'striped' => true,
        'condensed' => true,
        'responsive' => true,
        'hover' => true,
        'showPageSummary' => false,
        'persistResize' => false,
    ]);

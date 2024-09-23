<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

use mootensai\enhancedgii\crud\Generator;

/* @var $this yii\web\View */
/* @var $generator mootensai\enhancedgii\crud\Generator */

$urlParams = $generator->generateUrlParams();
$tableSchema = $generator->getTableSchema();
$pk = empty($tableSchema->primaryKey) ? $tableSchema->getColumnNames()[0] : $tableSchema->primaryKey[0];
$fk = $generator->generateFK($tableSchema);

$simpleClassName = stripNamespaceFromClassName($generator->modelClass);

echo "<?php\n";
?>

/**
 * CREATED BY A CODE GENERATOR!!!!
 * THIS FILE WAS CREATED BY A HEAVILY MODIFIED yii2-enhanced-gii for use in GRS.
 * Hand editing this file will result in lost code.
 *
 * view.php
 */

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */

//$this->title = $model-><?= $generator->getNameAttribute() ?>;
<?php
$modelClassName = $generator->generateString(Inflector::camel2words(StringHelper::basename($generator->modelClass)));
$modelName = $generator->getNameAttribute();
?>

$this->title = <?= $modelClassName ?> . ' - ' . $model-><?= $modelName ?>;
$this->params['breadcrumbs'][] = ['label' => <?= ($generator->pluralize) ? $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) : $generator->generateString(Inflector::camel2words(StringHelper::basename($generator->modelClass))) ?>, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class=".container-fluid">
<div class="col-lg-12">

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-view">

<?php // this is the main View ?>

<div class="card">
    <div class="card-header card-header-text card-header-info">
        <h1 class="card-text"><?= "<?= " ?>Html::encode($this->title) ?></h1>
    </div>
    <div class="card-body">

        <div class="col-lg-12">

                <?php if ($generator->pdf): ?>
                <?= "<?= " ?>
                <?= "
                 Html::a('<i class=\"fa glyphicon glyphicon-hand-up\"></i> ' . " . $generator->generateString('PDF') . ",
                    ['pdf', $urlParams],
                    [
                        'class' => 'btn btn-danger btn-round float-right',
                        'target' => '_blank',
                        'data-toggle' => 'tooltip',
                        'title' => " . $generator->generateString('Will open the generated PDF file in a new window') . "
                    ]
                )?>\n"
                ?>
                <?php endif; ?>
                <?php if($generator->saveAsNew): ?>
                    <?= "            <?= Html::a(" . $generator->generateString('Save As New') . ", ['save-as-new', " . $generator->generateUrlParams() . "], ['class' => 'btn btn-info btn-round float-right']) ?>" ?>
                <?php endif;?>
                <?= "

                <?= !$generator->disableViewDelete
                    ? Html::a(" . $generator->generateString('Delete') . ", ['delete', " . $generator->generateUrlParams() . "],
                        ['class' => 'btn btn-danger btn-round float-right',
                            'data' => [
                            'confirm' => " . $generator->generateString('Are you sure you want to delete this item?') . ",
                            'method' => 'post',
                            'params' => ['id' => \$model->id],
                            ],
                        ])
                    : null
                ?>
                <?= !$generator->disableUpdate
                    ? Html::a(" . $generator->generateString('Edit') . ", ['update', " . $generator->generateUrlParams() . "],
                        ['class' => 'btn btn-primary btn-round float-right'])
                    : null
                ?>                
                
                \n" ?>
        </div>
        <div class="clearfix"></div>
        <div class="row">
            <?= "<?php \n" ?>
            $gridColumn = [
                <?php
                if ($tableSchema === false) {
                    foreach ($generator->getColumnNames() as $name) {
                        if (++$count < 6) {
                            echo "            '" . $name . "',\n";
                        } else {
                            echo "            // '" . $name . "',\n";
                        }
                    }
                } else{
                    foreach($tableSchema->getColumnNames() as $attribute){
                        if(!in_array($attribute, $generator->skippedColumns)) {
                            echo "        " . $generator->generateDetailViewField($attribute,$fk, $tableSchema);
                        }
                    }
                }?>
            ];
            echo DetailView::widget([
                'model' => $model,
                'attributes' => $gridColumn
            ]);
            ?>
        </div>
        <div class="row">
                <?= "
                <?=
                    ! empty('$generator->extraCreateButton')
                    ? Html::a(" . $generator->generateString("Create Related $generator->extraCreateButton") . ",
                        ['" . \yii\helpers\Inflector::camel2id($generator->extraCreateButton) . "/create', 'id' => \$model->id],
                        ['class' => 'btn btn-primary btn-round'])
                    : null
                ?>\n" ?>
        </div>
    </div>
</div>

<?php
// this is for sub forms for relations
foreach ($generator->manyRelationsAllowedInView as $name):
    $rel = $relations[$name];
    if (! isset($rel)) {
        echo "invalid name: $name\n";
        exit();
    }

    // check if it's also going to be something we can edit too
    $isEditRelation = in_array($name, $generator->manyRelationsAllowedInEdit);

    if ($isEditRelation) {
        $ifStmt = "true";
    }
    else {
        $ifStmt = "\$provider" . $rel[Generator::REL_CLASS] . "->totalCount";
    }

    if ($rel[Generator::REL_IS_MULTIPLE] && isset($rel[Generator::REL_TABLE]) && !in_array($name, $generator->skippedRelations)):
?>

    <div class="row-sub-relation-table">

    <?= "<?php\n" ?>

    if(<?= $ifStmt ?>)
    {
        $gridColumn<?= $rel[Generator::REL_CLASS] ?> = [
            ['class' => 'yii\grid\SerialColumn'],
            <?php
                $relTableSchema = $generator->getDbConnection()->getTableSchema($rel[Generator::REL_TABLE]);
                $fkRel = $generator->generateFK($relTableSchema);
                if ($tableSchema === false) {
                    $count = 0;
                    foreach ($relTableSchema->getColumnNames() as $attribute)
                    {
                        // xx
                        if ($generator->relationsShowSubDetailsColCount > 0 &&
                            $count >= $generator->relationsShowSubDetailsColCount)
                        {
                            continue;
                        }

                        if (!in_array($attribute, $generator->skippedColumns))
                        {
                            $count++;
                            //echo "\t\t\t" . $attribute . "',\n";
                            echo "\t\t\t" . $generator->generateColumnFormat($attribute) . "',\n";
                        }
                    }
                } else {
                    $count = 0;
                    foreach ($relTableSchema->getColumnNames() as $attribute)
                    {
                        $relClassName = $rel[Generator::REL_CLASS];
                        $allowedColList = \yii\helpers\ArrayHelper::getValue($generator->manyRelationsViewFieldList, $relClassName, array());
                        $allowColAllowed = count($allowedColList) ? false : true;

                        // xx
                        if ($generator->relationsShowSubDetailsColCount > 0 &&
                            $count >= $generator->relationsShowSubDetailsColCount)
                        {
                            continue;
                        }

                        if (!in_array($attribute, $generator->skippedColumns) &&
                            ($allowColAllowed || in_array($attribute, $allowedColList)))
                        {
                            $count++;
                            // xxx
                            echo "\t\t\t" . $generator->generateGridViewField($attribute, $fkRel, $relTableSchema);
                        }
                    }
                }
            ?>
        ];

    <?= "?>\n" ?>

    <div class="card">
        <div class="card-body">
          <div>
              <h2><?= Inflector::pluralize(Inflector::camel2words($rel[Generator::REL_CLASS])) ?></h2>
          </div>

    <?= "<?php\n" ?>

        echo Gridview::widget([
            'dataProvider' => $provider<?= $rel[Generator::REL_CLASS] ?>,
            'pjax' => true,
            'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-<?= Inflector::camel2id($rel[Generator::REL_TABLE])?>']],
            'panel' => [
                <?php
                if ($isEditRelation) {
                    echo "'type' => GridView::TYPE_PRIMARY,\n";
                    $urlParms = "'update-" . Inflector::camel2id(Inflector::singularize($name)) ."', " . $generator->generateUrlParams();
                    echo "\t\t\t\t'before' => Html::a('<i class=\"material-icons\">edit_note</i> Edit', [$urlParms], ['class' => 'btn btn-primary btn-round']),\n";
                }
                else {
                    echo "'type' => GridView::TYPE_INFO,\n";
                }
                ?>
                //'heading' => '<span class="glyphicon glyphicon-th-list"></span> ' .
                    //Html::encode(<?= $generator->generateString(Inflector::pluralize(Inflector::camel2words($rel[Generator::REL_CLASS]))) ?>),
            ],
            <?php if(!$generator->pdf): ?>
                'export' => false,
            <?php endif; ?>
            'columns' => $gridColumn<?= $rel[Generator::REL_CLASS] . "\n" ?>
        ]);
    <?= "?>\n" ?>
        </div>
    </div>
    
    <?= '<?php } ?>' ?>

<?php
// xx only show if enabled - dp
elseif(empty($rel[Generator::REL_IS_MULTIPLE]) && $generator->relationsShowSubDetailsInView): ?>
    <div class="row">
        <h4><?= $rel[Generator::REL_CLASS] ?><?= "<?= " ?>' '. Html::encode($this->title) ?></h4>
    </div>
    <?= "<?php \n" ?>
    $gridColumn<?= $rel[Generator::REL_CLASS] ?> = [
<?php
    $relTableSchema = $generator->getDbConnection()->getTableSchema($rel[Generator::REL_TABLE]);
    $fkRel = $generator->generateFK($relTableSchema);
    $i = 0;
    foreach($relTableSchema->getColumnNames() as $attribute){
        if($attribute == $rel[Generator::REL_FOREIGN_KEY]){
            continue;
        }
        if ($relTableSchema === false) {
            if (!in_array($attribute, $generator->skippedColumns)){
                //echo "        '" . $attribute . "',\n";
                echo "\t\t\t" . $generator->generateColumnFormat($attribute) . "',\n";
            }
        } else{
            if(!in_array($attribute, $generator->skippedColumns)){
                echo "        ".$generator->generateDetailViewField($attribute,$fkRel);
            }
        }

        $i++;
    }
    ?>
    ];
    echo DetailView::widget([
        'model' => $model-><?= $name ?>,
        'attributes' => $gridColumn<?= $rel[Generator::REL_CLASS] ?>
    ]);
    ?>
<?php endif; ?>
<?php endforeach; ?>
</div>
</div>
</div>
</div>

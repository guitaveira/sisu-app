<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\Html;

use mootensai\enhancedgii\crud\Generator;

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\crud\Generator */
/* @var $relations array */
$tableSchema = $generator->getTableSchema();
$fk = $generator->generateFK($tableSchema);
echo "<?php\n";
?>

/**
 * CREATED BY A CODE GENERATOR!!!!
 * THIS FILE WAS CREATED BY A HEAVILY MODIFIED yii2-enhanced-gii for use in GRS.
 * Hand editing this file will result in lost code.
 *
 * _form.php
 */

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model <?= ltrim($generator->modelClass, '\\') ?> */
/* @var $form yii\widgets\ActiveForm */

$isManyEdit = (isset($hasManyEditClass) ? true : false);

<?php
$colNames = $tableSchema->getColumnNames();
$pk = empty($tableSchema->primaryKey) ? $colNames[0] : $tableSchema->primaryKey[0];
$modelClass = StringHelper::basename($generator->modelClass);
foreach ($relations as $name => $rel) {
    $relID = Inflector::camel2id($rel[$generator::REL_CLASS]);
    if ($rel[$generator::REL_IS_MULTIPLE] && isset($rel[$generator::REL_TABLE]) && !in_array($name, $generator->skippedRelations)) {
        echo      "\\mootensai\\components\\JsBlock::widget(['viewFile' => '_script', 'pos'=> \\yii\\web\\View::POS_END, \n"
                . "    'viewParams' => [\n"
                . "        'class' => '{$rel[$generator::REL_CLASS]}', \n"
                . "        'relID' => '$relID', \n"
                . "        'value' => \\yii\\helpers\\Json::encode(\$model->$name),\n"
                . "        'isNewRecord' => (\$model->isNewRecord) ? 1 : 0\n"
                . "    ]\n"
                . "]);\n";
    }
}
?>

<?php echo '?>' . "\n" ?>

<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-form">

<?= "<?php " ?>$form = ActiveForm::begin(); ?>


   <?= "<?php\n\n " ?>
      $err = $form->errorSummary($model);
      $hasErr = str_replace('<div class="error-summary" style="display:none"><p>Please fix the following errors:</p><ul></ul></div>', '', $err);
      if (! empty($hasErr)) {
         echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
         echo '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
         echo $err;
         echo '</div>';
      }
   ?>

<div class="card">

<?php echo '<?php' . "\n" ?>

if (! $isManyEdit)
{
   ?>

      <div class="card-header card-header-text card-header-info">
          <h1 class="card-text"><?= '<?= $title ?>' ?></h1>
      </div>

      <div class="card-body">

<?php echo '<?php' . "\n" ?>


<?php
foreach ($tableSchema->getColumnNames() as $attribute) {
    if (!in_array($attribute, $generator->skippedColumns))
    {
        $initialQueryVar = null;
        if (array_key_exists($attribute, $generator->editFormDepFieldList)) {
           $childField = $attribute;

           $fromDepFieldDef = $generator->editFormDepFieldList[$attribute];
           $defExplode = explode('#', $fromDepFieldDef[0]);
           $parentField = $defExplode[0];
           $parentQuery = $defExplode[1];
           
           $childFieldFinalName = sprintf("\$%s_list", $childField);
           
           $initialQueryVar = "$childFieldFinalName = (!\$model->isNewRecord && !empty(\$model->$childField)) ? \yii\helpers\ArrayHelper::map(\$model->{$parentQuery}->all(), 'id', 'name') : [];";
           echo "\t\t // $attribute
               if ((\$model->isNewRecord && !in_array('".$attribute."', \$createSkipCols)) ||
                   (!\$model->isNewRecord && !in_array('".$attribute."', \$updateSkipCols)))
               {
                   $initialQueryVar
                   echo " . $generator->generateActiveField($attribute, $fk) . ";
               }\n\n";         
        }
        else {
        echo "\t\t // $attribute
            if ((\$model->isNewRecord && !in_array('".$attribute."', \$createSkipCols)) ||
                (!\$model->isNewRecord && !in_array('".$attribute."', \$updateSkipCols)))
            {
                echo " . $generator->generateActiveField($attribute, $fk) . ";
            }\n\n";
        }
    }
}
?>
    echo '</div>';

}

$allForms = [];

<?php
$forms = "";

// xx - only genreate forms if enabled via command-line - dp
foreach ($generator->manyRelationsAllowedInEdit as $name)
{
    $rel = $relations[$name];
    if (! isset($rel)) {
        echo "failed to find: $name\n";
        exit();
    }

    $relID = Inflector::camel2id($rel[$generator::FK_FIELD_NAME]);
    if ($rel[$generator::REL_IS_MULTIPLE] && isset($rel[$generator::REL_TABLE]) && !in_array($name, $generator->skippedRelations))
    {
        $forms .=
            "   if (\$isManyEdit && \$hasManyEditClass == '" . $rel[$generator::REL_CLASS] . "')\n" .
            "   {\n" .
            "        array_push(\$allForms, [\n".
            "            'label' => '<i class=\"material-icons\">auto_stories</i> ' . Html::encode(".$generator->generateString($rel[$generator::REL_CLASS])."),\n".
            "            'content' => \$this->render('_form".$rel[$generator::FK_FIELD_NAME]."', [\n".
            "                'row' => \\yii\\helpers\\ArrayHelper::toArray(\$model->$name),\n".
            "            ]),\n".
            "        ]);\n".
            "   }\n";
    }
    elseif(isset($rel[$generator::REL_IS_MASTER]) && !$rel[$generator::REL_IS_MASTER])
    {
        $div = '<div class="card-header card-header-text card-header-info"><h1 class="card-text"><?= \'<?= ' . $title . '?>\' ?></h1></div><div class="card-body">';

        $forms .= $div .
            "   if (\$isManyEdit && \$hasManyEditClass == '" . $rel[$generator::REL_CLASS] . "')\n" .
            "   {\n" .
            "        array_push(\$allForms, [\n".
            "            'label' => '<i class=\"material-icons\">auto_stories</i> ' . Html::encode(".$generator->generateString($rel[$generator::REL_CLASS])."),\n".
            "            'content' => \$this->render('_form".$rel[$generator::FK_FIELD_NAME]."', [\n" .
            "                'form' => \$form,\n".
            "                '".$rel[$generator::REL_CLASS]."' => is_null(\$model->$name) ? new ".$generator->nsModel."\\".$rel[$generator::REL_CLASS]."() : \$model->$name,\n".
            "            ]),\n".
            "        ]);\n".
            "   }\n";
    }
}

echo "?>\n\n";

if(! empty($forms)){
  ?>
<?php
          echo  "<?php\n";
          echo "$forms\n" .
                "\n" .
                "    echo kartik\\tabs\\TabsX::widget([\n" .
                "        'items' => \$allForms,\n" .
                "        'position' => kartik\\tabs\\TabsX::POS_ABOVE,\n" .
                "        'encodeLabels' => false,\n" .
                "        'pluginOptions' => [\n" .
                "            'bordered' => true,\n" .
                "            'sideways' => true,\n" .
                "            'enableCache' => false,\n" .
                "        ],\n" .
                "    ]);\n" .
                "?>\n";
  ?>
        </div>
<?php
}
?>

    <div class="form-group">
      <div class="card-body">

<?php if($generator->saveAsNew): ?>
<?= "    <?php if(Yii::\$app->controller->action->id != 'save-as-new'): ?>\n" ?>
<?= "        <?= " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Save New Entry') ?> : <?= $generator->generateString('Save Updated Entry') ?>, ['class' => $model->isNewRecord ? 'btn btn-primary btn-round' : 'btn btn-primary btn-round']) ?>
<?= "    <?php endif; ?>\n" ?>
<?= "    <?php if(Yii::\$app->controller->action->id != 'create'): ?>\n" ?>
<?= "        <?= " ?>Html::submitButton(<?=$generator->generateString('Save As New')?>, ['class' => 'btn btn-info btn-round', 'value' => '1', 'name' => '_asnew']) ?>
<?= "    <?php endif; ?>\n" ?>
<?php else: ?>
<?= "        <?= " ?>Html::submitButton($model->isNewRecord ? <?= $generator->generateString('Save New Entry') ?> : <?= $generator->generateString('Save Updated Entry') ?>, ['class' => $model->isNewRecord ? 'btn btn-primary btn-round' : 'btn btn-primary btn-round']) ?>
<?php endif; ?>
<?php if ($generator->cancelable): ?>
        <?= "<?= " ?>Html::a(Yii::t('app', 'Cancel'), Yii::$app->request->referrer , ['class'=> 'btn btn-danger btn-round']) ?>
<?php endif; ?>

      </div>
    </div>

    <?= "<?php " ?>ActiveForm::end(); ?>

</div>
</div>

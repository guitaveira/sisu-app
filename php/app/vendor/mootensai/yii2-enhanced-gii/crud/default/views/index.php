<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

use mootensai\enhancedgii\crud\Generator;

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
$tableSchema = $generator->getTableSchema();
$baseModelClass = StringHelper::basename($generator->modelClass);
$fk = $generator->generateFK($tableSchema);
echo "<?php\n";
?>

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

/**
 * CREATED BY A CODE GENERATOR!!!!
 * THIS FILE WAS CREATED BY A HEAVILY MODIFIED yii2-enhanced-gii for use in GRS.
 * Hand editing this file will result in lost code.
 *
 * index.php
 */

use yii\helpers\Html;
use kartik\export\ExportMenu;
use <?= $generator->indexWidgetType === 'grid' ? "kartik\\grid\\GridView;" : "yii\\widgets\\ListView;" ?>


$this->title = <?= ($generator->pluralize) ? $generator->generateString(Inflector::pluralize(Inflector::camel2words($baseModelClass))) : $generator->generateString(Inflector::camel2words($baseModelClass)) ?>;
$this->params['breadcrumbs'][] = $this->title;
$search = "$('.search-button').click(function(){
	$('.search-form').toggle(1000);
	return false;
});";
$this->registerJs($search);
?>
<div class="<?= Inflector::camel2id($baseModelClass) ?>-index">

<div class="col-lg-12">
<div class="card">
<div class="card-header card-header-text card-header-info">
	<h1 class="card-text"><?= "<?= " ?>Html::encode($this->title) ?></h1>
</div>

<div class="card-body">

	<?php if (!empty($generator->searchModelClass) && ! $generator->disableAdvancedSearch): ?>
	<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
	<?php endif; ?>

    <p>
	<?php if (!empty($generator->searchModelClass) && ! $generator->disableAdvancedSearch): ?>
        <?= "<?= " ?>Html::a(<?= $generator->generateString('Advance Search')?>, '#', ['class' => 'btn btn-primary btn-round search-button']) ?>
	<?php endif; ?>

    </p>

	<?php if (!empty($generator->searchModelClass) && ! $generator->disableAdvancedSearch): ?>
    <div class="search-form" style="display:none">
        <?= "<?= " ?> $this->render('_search', ['model' => $searchModel]); ?>
    </div>
    <?php endif; ?>

	<?php if ($generator->indexWidgetType === 'grid'): ?>
	<?= "<?php \n" ?>
		$gridColumn = [
			<?php if ($generator->expandable && !empty($fk)): ?>
				[
					'class' => 'kartik\grid\ExpandRowColumn',
					'width' => '50px',
					'value' => function ($model, $key, $index, $column) {
						return GridView::ROW_COLLAPSED;
					},
					'detail' => function ($model, $key, $index, $column) {
						return Yii::$app->controller->renderPartial('_expand', ['model' => $model]);
					},
					'headerOptions' => ['class' => 'kartik-sheet-style'],
					'expandOneOnly' => true
				],
			<?php endif; ?>
			['class' => 'yii\grid\SerialColumn'],

		<?php if ($tableSchema === false) :
			foreach ($generator->getColumnNames() as $name) {
				$count++;
				if ($count < 6) {
					echo "\t\t\t\t\t" . "'" . $name . "'," . "\n";
				} else {
					echo "\t\t\t\t\t" ."//'" . $name . "'," . "\n";
				}
			}
		else :
			foreach ($tableSchema->getColumnNames() as $attribute):
				if (!in_array($attribute, $generator->skippedColumns) && !in_array($attribute, $generator->skippedIndexColumns)) :
		?>
				<?= $generator->generateGridViewFieldIndex($attribute, $fk, $tableSchema)?>
			<?php
				endif;
			endforeach;
			?>
			[
				'class' => 'yii\grid\ActionColumn',
				<?php
					$actionsAllowedList = array();
					if (!$generator->disableSaveAsNew) {
						array_push($actionsAllowedList, '{save-as-new}');
					}
					if (!$generator->disableView) {
						array_push($actionsAllowedList, '{view}');
					}
					if (!$generator->disableUpdate) {
						array_push($actionsAllowedList, '{update}');
					}
					if (!$generator->disableDelete) {
						array_push($actionsAllowedList, '{delete}');
					}

					if (count($actionsAllowedList)):
						$actionTemplate = join(' ', $actionsAllowedList);
				?>
					'template' => '<?= $actionTemplate ?>',
					'icons' => [
						'eye-open' => Html::tag('span', 'visibility', ['class' => 'material-icons']),
						'pencil' => Html::tag('span', 'edit_note', ['class' => 'material-icons']),
						'trash' => Html::tag('span', 'delete', ['class' => 'material-icons']),
					],
					'options'=> ['width'=>'100px',],
					/*
					'buttons' => [
						'save-as-new' => function ($url) {
							return Html::a('<span class="glyphicon glyphicon-copy"></span>', $url, ['title' => 'Save As New']);
						},
					],*/
				<?php endif; ?>
			],
		];
		<?php endif; ?>

    ?>

	<?php
	$createButton = "Html::a(" . $generator->generateString('Create ' . Inflector::camel2words($baseModelClass)) . ",['create'], ['class' => 'btn btn-primary btn-round'])";
	?>

    <?= "<?= " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => \$gridColumn,\n" : "'columns' => \$gridColumn,\n"; ?>
        'pjax' => true,
		'resizableColumns'=>true,
		//'persistResize'=>true,
        'pjaxSettings' => ['options' => ['id' => 'kv-pjax-container-<?= Inflector::camel2id(StringHelper::basename($generator->modelClass))?>']],
        'panel' => [
            'type' => GridView::TYPE_PRIMARY,
			<?= !$generator->disableCreate ? "'before' => $createButton," : null ?>
            //'heading' => '<span class="glyphicon glyphicon-book"></span>  ' . Html::encode($this->title),
        ],


		<?php if(!$generator->pdf) : ?>
			'export' => false,
		<?php endif; ?>
		<?php
		/*
        // your toolbar can include the additional full export menu
        'toolbar' => [
            '{export}',
            ExportMenu::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumn,
                'target' => ExportMenu::TARGET_BLANK,
                'fontAwesome' => true,
                'dropdownOptions' => [
                    'label' => 'Full',
                    'class' => 'btn btn-primary btn-round',
                    'itemsBefore' => [
                        '<li class="dropdown-header">Export All Data</li>',
                    ],
                ],
		<?php if(!$generator->pdf):?>
                'exportConfig' => [
                    ExportMenu::FORMAT_PDF => false
                ],
		<?php endif;?>
            ]),
        ],
        */
		?>
    ]);
	?>

	<?php else: ?>
	<?= "<?= " ?>ListView::widget([
		'dataProvider' => $dataProvider,
		'itemOptions' => ['class' => 'item'],
		'itemView' => function ($model, $key, $index, $widget) {
			return $this->render('_index',['model' => $model, 'key' => $key, 'index' => $index, 'widget' => $widget, 'view' => $this]);
		},
	]) ?>
	<?php endif; ?>

</div>
</div>
</div>
</div>
<?php
/* @var $generator \mootensai\enhancedgii\crud\Generator */
$tableSchema = $generator->getTableSchema();
$fk = $generator->generateFK($tableSchema);
$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();
?>
<?= "<?php \n" ?>

/**
 * CREATED BY A CODE GENERATOR!!!!
 * THIS FILE WAS CREATED BY A HEAVILY MODIFIED yii2-enhanced-gii for use in GRS.
 * Hand editing this file will result in lost code.
 *
 * _index.php
 */
 
use \yii\helpers\Html;
use yii\widgets\DetailView;
?>

<div class="row">
    <div class="<?= ($generator->saveAsNew) ? "col-lg-7" : "col-lg-9";?>">
        <h2><?= "<?= " ?>Html::a(Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]) ?></h2>
    </div>
    <div class="<?= ($generator->saveAsNew) ? "col-lg-5" : "col-lg-3";?>" style="margin-top: 15px">
<?php if($generator->pdf): ?>
        <?= "<?= " ?>Html::a('<i class="fa glyphicon glyphicon-hand-up"></i> ' . <?= $generator->generateString('Print PDF')?>,
            ['pdf', <?= $urlParams ?>],
            [
                'class' => 'btn btn-danger btn-round',
                'target' => '_blank',
                'data-toggle' => 'tooltip',
                'title' => Yii::t('app', 'Will open the generated PDF file in a new window')
            ]
        )?>
<?php endif; ?>
<?php if($generator->saveAsNew): ?>
        <?= "<?= " ?>Html::a('<i class="fa glyphicon glyphicon-hand-up"></i> ' . <?= $generator->generateString('Save As New')?>,
            ['save-as-new', <?= $urlParams ?>], ['class' => 'btn btn-info btn-round'])?>
<?php endif; ?>
        <?= "<?=" ?> Html::a(Yii::t('app', 'Edit'), ['update', <?= $urlParams ?>], ['class' => 'btn btn-primary btn-round']) ?>
        <?= "<?=" ?> Html::a(Yii::t('app', 'Delete'), ['delete', <?= $urlParams ?>], [
            'class' => 'btn btn-danger btn-round',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ])
        ?>
    </div>
    <?= "<?php \n" ?>
    $gridColumn = [
<?php
    $count = 0;
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
            if (++$count < 6) {
                if (!in_array($attribute, $generator->skippedColumns)) {
                    echo "        " . $generator->generateDetailViewField($attribute, $fk, $tableSchema);

                }
            }else{
                if (!in_array($attribute, $generator->skippedColumns)) {
                    echo "        //" . $generator->generateDetailViewField($attribute, $fk, $tableSchema);

                }
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

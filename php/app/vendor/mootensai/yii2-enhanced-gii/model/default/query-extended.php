<?php
/**
     * This is the template for generating the ActiveQuery class.
     */

/* @var $this yii\web\View */
/* @var $generator mootensai\enhancedgii\crud\Generator */
/* @var $className string class name */
/* @var $modelClassName string related model class name */

$modelFullClassName = $modelClassName;
if ($generator->nsModel !== $generator->queryNs) {
    $modelFullClassName = '\\' . $generator->queryNs . '\\' . $modelFullClassName;
}

echo "<?php\n";
?>

namespace <?= $generator->queryNs ?>;

use \<?= $generator->nsModel ?>\base\<?= $className ?> as Base<?= $className ?>;

/**
* This is the ActiveQuery class for [[<?= $modelFullClassName ?>]].
*
* @see <?= $modelFullClassName . "\n" ?>
*/
class <?= $className ?> extends Base<?= $className . "\n" ?>
{
}

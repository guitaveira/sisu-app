<?php

/**
 * This is the template for generating a CRUD controller class file.
 */
use yii\helpers\StringHelper;
use yii\helpers\Inflector;
use yii\helpers\ArrayHelper;

use mootensai\enhancedgii\crud\Generator;

/* @var $this yii\web\View */
/* @var $generator \mootensai\enhancedgii\crud\Generator */

$controllerClass = StringHelper::basename($generator->controllerClass);
$modelClass = StringHelper::basename($generator->modelClass);
$searchModelClass = StringHelper::basename($generator->searchModelClass);
if ($modelClass === $searchModelClass) {
	$searchModelAlias = $searchModelClass . 'Search';
}
$pks = $generator->tableSchema->primaryKey;
$urlParams = $generator->generateUrlParams();
$actionParams = $generator->generateActionParams();
$actionParamComments = $generator->generateActionParamComments();
$skippedRelations = array_map(function($value){
	return "'$value'";
},$generator->skippedRelations);
echo "<?php\n";
?>

namespace <?= StringHelper::dirname(ltrim($generator->controllerClass, '\\')) ?>;

/**
 * CREATED BY A CODE GENERATOR!!!!
 * THIS FILE WAS CREATED BY A HEAVILY MODIFIED yii2-enhanced-gii for use in GRS.
 * Hand editing this file will result in lost code.
 */

use Yii;
use <?= ltrim($generator->modelClass, '\\') ?>;
<?php if (!empty($generator->searchModelClass)): ?>
use <?= ltrim($generator->searchModelClass, '\\') . (isset($searchModelAlias) ? " as $searchModelAlias" : "") ?>;
<?php else : ?>
use yii\data\ActiveDataProvider;
<?php endif; ?>
use <?= ltrim($generator->baseControllerClass, '\\') ?>;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Inflector;

use yii\helpers\ArrayHelper;

/**
 * <?= $controllerClass ?> implements the CRUD actions for <?= $modelClass ?> model.
 */
class <?= $controllerClass ?> extends <?= StringHelper::basename($generator->baseControllerClass) . "\n" ?>
{
	public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'delete' => ['post', 'get'],
				],
			],
<?php if ($generator->loggedUserOnly):
  $actions = array();

  if (! $generator->disableIndex) {
   array_push($actions, "'index'");
  }

  if (! $generator->disableView) {
   array_push($actions, "'view'");
  }

  if (! $generator->disableUpdate) {
   array_push($actions, "'update'");
  }

  if (! $generator->disableDelete || ! $generator->disableViewDelete) {
   array_push($actions, "'delete'");
  }

  if (! $generator->disableCreate) {
   array_push($actions, "'create'");
  }

	if($generator->pdf){
		array_push($actions,"'pdf'");
	}
	if($generator->saveAsNew){
		array_push($actions,"'save-as-new'");
	}
	foreach ($relations as $name => $rel){
		if ($rel[Generator::REL_IS_MULTIPLE] && isset($rel[Generator::REL_TABLE]) && !in_array($name, $generator->skippedRelations)){
			array_push($actions,"'".\yii\helpers\Inflector::camel2id('add'.$rel[Generator::REL_CLASS])."'");
		}
	}
	foreach ($relations as $name => $rel){
		if ($rel[Generator::REL_IS_MULTIPLE] && isset($rel[Generator::REL_TABLE]) && in_array($name, $generator->manyRelationsAllowedInEdit)){
			array_push($actions,"'".\yii\helpers\Inflector::camel2id('update'.$rel[Generator::REL_CLASS])."'");
		}
	}
	
foreach ($generator->editFormDepFieldList as $formDepField => $fromDepFieldDef) {
		$childField = $formDepField;
		$defExplode = explode('#', $fromDepFieldDef[0]);
		$parentField = $defExplode[0];
		$parentQuery = $defExplode[1];
		
		$parentFieldCamel = \yii\helpers\Inflector::camelize($childField);
		$functionName = $parentFieldCamel . "List";
		$actions[] = sprintf("'%s'", \yii\helpers\Inflector::camel2id($functionName));
}

?>
			'access' => [
				'class' => \yii\filters\AccessControl::className(),
				'rules' => [
					[
						'allow' => true,
						'actions' => [<?= implode(', ',$actions)?>],
						'roles' => ['@']
					],
					[
						'allow' => false
					]
				]
			]
<?php endif; ?>
		];
	}

	/**
	 * Lists all <?= $modelClass ?> models.
	 * @return mixed
	 */
	public function actionIndex()
	{
<?php
   if (isset($generator->webUserColName)) {
	   $user_col_name = $generator->webUserColName;
	   $queryString = $modelClass . "::find()->andWhere( [ '$user_col_name'=>Yii::\$app->user->id ] )";
   }
   else {
	   $queryString = $modelClass . "::find()";
   }
?>
<?php if (!empty($generator->searchModelClass)): ?>
		$searchModel = new <?= isset($searchModelAlias) ? $searchModelAlias : $searchModelClass ?>();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

		$this->showPageNotifications();
		
		//prevents bootstrap css from loading again on pjax, which was causing font changes #100
		Yii::$app->assetManager->bundles = [
            'yii\bootstrap4\BootstrapAsset' => false
		];

		return $this->render('index', [
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
		]);
<?php else : ?>
		$dataProvider = new ActiveDataProvider([
			'query' => <?= $queryString ?>,
		]);

		$this->showPageNotifications();
		
		//prevents bootstrap css from loading again on pjax, which was causing font changes #100
		Yii::$app->assetManager->bundles = [
            'yii\bootstrap4\BootstrapAsset' => false
		];

		return $this->render('index', [
			'dataProvider' => $dataProvider,
		]);
<?php endif; ?>
	}

	/**
	 * Displays a single <?= $modelClass ?> model.
	 * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
	 * @return mixed
	 */
	public function actionView(<?= $actionParams ?>)
	{
		$model = $this->findModel(<?= $actionParams ?>);
<?php foreach ($relations as $name => $rel): ?>
<?php if ($rel[Generator::REL_IS_MULTIPLE] && isset($rel[Generator::REL_TABLE]) && !in_array($name, $generator->skippedRelations)): ?>
		$provider<?= $rel[Generator::REL_CLASS]?> = new \yii\data\ArrayDataProvider([
			'allModels' => $model-><?= $name ?>,
			'sort' => [
			 'attributes' => [ '<?= $generator->getNameAttribute() ?>' ],
			 'defaultOrder' => [ '<?= $generator->getNameAttribute() ?>' => SORT_ASC ],
			],
		]);
<?php endif; ?>
<?php endforeach; ?>

		$this->showPageNotifications($id);

		return $this->render('view', [
			'model' => $this->findModel(<?= $actionParams ?>),
<?php foreach ($relations as $name => $rel): ?>
<?php if ($rel[Generator::REL_IS_MULTIPLE] && isset($rel[Generator::REL_TABLE]) && !in_array($name, $generator->skippedRelations)): ?>
			'provider<?= $rel[Generator::REL_CLASS]?>' => $provider<?= $rel[Generator::REL_CLASS]?>,
<?php endif; ?>
<?php endforeach; ?>
		]);
	}

	<?php
	///////////
	// setup actionCreate()
	///////////
	
	// create a list of relations to intentionally skip!
	$excludesList = array();
	foreach ($relations as $nameToExclude => $rel)
	{
	 if (! $rel[Generator::REL_IS_MULTIPLE]) {
	  continue;
	 }

	 array_push($excludesList, $nameToExclude);
	}

	$excludeListWithSingle = array_map(function($value) { return "'$value'"; }, $excludesList);

	$excludeLoadAll = (!empty($excludesList) ? ", [ " . implode(", ", $excludeListWithSingle) . " ]" : "");
	$excludeSaveAll = (!empty($excludesList) ? "[ " . implode(", ", $excludeListWithSingle) . " ]" : "");
	?>

	/**
	 * Creates a new <?= $modelClass ?> model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 * @return mixed
	 */
	public function actionCreate(int $id = null)
	{
		$model = new <?= $modelClass ?>();
		if (method_exists($model, "prepareForCreate")) {
			<?php
			if (isset($generator->webUserColName)):
			?>
			$model->prepareForCreate(Yii::$app->user->id, $id);
			<?php
			else:
			?>
			$model->prepareForCreate(null, $id);
			<?php
			endif;
			?>
		}

			<?php
			if (isset($generator->webUserColName)) {
			  echo '$model->'.$generator->webUserColName.' = Yii::$app->user->id;' . "\n";
			}
			?>

		// saveAll() will delete existing related records, so we exclude those for loadAll() and saveAll()
		if ($model->loadAll(Yii::$app->request->post()<?= $excludeLoadAll ?>) && $model->saveAll(<?= $excludeSaveAll ?>)) {
			<?php
			if (isset($generator->webUserColName)) {
			  echo '$model->'.$generator->webUserColName.' = Yii::$app->user->id;' . "\n";
			}
			?>
			return $this->redirect( ['view', <?= $urlParams ?>] );
		} else {
			return $this->render('create', [ 'model' => $model, ]);
		}
	}

	<?php
	///////////
	// setup actionUpdate()
	///////////
	
	// create a list of relations to intentionally skip!
	$excludesList = array();
	foreach ($relations as $nameToExclude => $rel)
	{
	 if (! $rel[Generator::REL_IS_MULTIPLE]) {
	  continue;
	 }

	 array_push($excludesList, $nameToExclude);
	}

	$excludeListWithSingle = array_map(function($value) { return "'$value'"; }, $excludesList);

	$excludeLoadAll = (!empty($excludesList) ? ", [" . implode(", ", $excludeListWithSingle) . "]" : "");
	$excludeSaveAll = (!empty($excludesList) ? "[" . implode(", ", $excludeListWithSingle) . "]" : "");
	?>

	/**
	 * Updates an existing <?= $modelClass ?> model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
	 * @return mixed
	 */
	public function actionUpdate(<?= $actionParams ?>)
	{
<?php if($generator->saveAsNew) : ?>
		if (Yii::$app->request->post('_asnew') == '1') {
			$model = new <?= $modelClass ?>();
		}else{
			$model = $this->findModel(<?= $actionParams ?>);
		}

<?php else: ?>
		$model = $this->findModel(<?= $actionParams ?>);

<?php endif; ?>
		$this->showPageNotifications();

		// saveAll() will delete existing related records, so we exclude those for loadAll() and saveAll()
		if ($model->loadAll(Yii::$app->request->post()<?= $excludeLoadAll ?>) && $model->saveAll(<?= $excludeSaveAll ?>)) {
			return $this->redirect(['view', <?= $urlParams ?>]);
		} else {
			return $this->render('update', [ 'model' => $model, ]);
		}
	}

<?php foreach ($relations as $name => $rel): ?>
<?php if ($rel[Generator::REL_IS_MULTIPLE] && isset($rel[Generator::REL_TABLE]) && in_array($name, $generator->manyRelationsAllowedInEdit)): ?>
	<?php
	// create a list of relations to intentionally skip!
	$excludesList = array();
	foreach ($relations as $nameToExclude => $rel)
	{
	 if ($nameToExclude == $name) {
	  continue;
	 }
	 if (! $rel[Generator::REL_IS_MULTIPLE]) {
	  continue;
	 }

	 array_push($excludesList, $nameToExclude);
	}

	$excludeListWithSingle = array_map(function($value) { return "'$value'"; }, $excludesList);

	$excludeLoadAll = (!empty($excludesList) ? ", [" . implode(", ", $excludeListWithSingle) . "]" : "");
	$excludeSaveAll = (!empty($excludesList) ? "[" . implode(", ", $excludeListWithSingle) . "]" : "");
	?>
 
	/**
	 * Updates a model related to <?= $modelClass ?>. These are in a subform but the form
	 * returns to this Controller.
	 * @return mixed
	 */
	public function actionUpdate<?= Inflector::camelize(Inflector::singularize($name)) ?>(<?= $actionParams ?>)
	{
		$model = $this->findModel(<?= $actionParams ?>);

		$post = Yii::$app->request->post();
		if (count($post)) {
			$post['<?= $modelClass ?>'] = $model->attributes;
		}

		// saveAll() will delete existing related records, so we exclude those for loadAll() and saveAll()
		if ($model->loadAll($post<?= $excludeLoadAll ?>) && $model->saveAll(<?= $excludeSaveAll ?>)) {
			return $this->redirect(['view', <?= $urlParams ?>]);
		} else {
			return $this->render('update', [
				'model' => $model,
				'hasManyEditClass' => '<?= Inflector::camelize(Inflector::singularize($name)) ?>',
			]);
		}
	}
<?php endif; ?>
<?php endforeach; ?>

<?php
$excludesList = array();
foreach ($relations as $name => $rel) {
 if ($rel[Generator::REL_IS_MULTIPLE] && isset($rel[Generator::REL_TABLE])) {

	 if (! $rel[Generator::REL_IS_MULTIPLE]) {
	  continue;
	 }

	 if (in_array($name, $generator->allowDeleteWithRelatedList)) {
	  continue;
	 }

	 $excludesList[] = $name;
 }
}

$excludeListWithSingle = array_map(function($value) { return "'$value'"; }, $excludesList);
$deleteExcludes = "[ " . implode(", ", $excludeListWithSingle) . " ]";
 ?>
 
	/**
	 * Deletes an existing <?= $modelClass ?> model.
	 * If deletion is successful, the browser will be redirected to the 'index' page.
	 * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
	 * @return mixed
	 */
	public function actionDelete($id)
	{
		$model = $this->findModel(<?= $actionParams ?>);
		
		// the model will tell us if it can be deleted. if not, that's usually because
		// there are other records that depend on this one and the User can't just delete
		// it without handling the other related items first
		if (method_exists($model, "canBeDeleted")) {
			$e = $model->canBeDeleted();
			if (! empty($e)) {
				throw new \yii\web\BadRequestHttpException(Yii::t('app', $e));
			}
		}


		$excludeList = <?= $deleteExcludes ?>;
		$model->deleteWithRelated($excludeList);

		return $this->redirect(['index']);
	}


<?php if ($generator->pdf):?>
	/**
	 *
	 * Export <?= $modelClass ?> information into PDF format.
	 * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
	 * @return mixed
	 */
	public function actionPdf(<?= $actionParams ?>) {
		$model = $this->findModel(<?= $actionParams ?>);
<?php foreach ($relations as $name => $rel): ?>
<?php if ($rel[Generator::REL_IS_MULTIPLE] && isset($rel[Generator::REL_TABLE]) && !in_array($name, $generator->skippedRelations)): ?>
		$provider<?= $rel[Generator::REL_CLASS] ?> = new \yii\data\ArrayDataProvider([
			'allModels' => $model-><?= $name; ?>,
		]);
<?php endif; ?>
<?php endforeach; ?>

		$content = $this->renderAjax('_pdf', [
			'model' => $model,
<?php foreach ($relations as $name => $rel): ?>
<?php if ($rel[Generator::REL_IS_MULTIPLE] && isset($rel[Generator::REL_TABLE]) && !in_array($name, $generator->skippedRelations)): ?>
			'provider<?= $rel[Generator::REL_CLASS]?>' => $provider<?= $rel[Generator::REL_CLASS] ?>,
<?php endif; ?>
<?php endforeach; ?>
		]);

		$pdf = new \kartik\mpdf\Pdf([
			'mode' => \kartik\mpdf\Pdf::MODE_CORE,
			'format' => \kartik\mpdf\Pdf::FORMAT_A4,
			'orientation' => \kartik\mpdf\Pdf::ORIENT_PORTRAIT,
			'destination' => \kartik\mpdf\Pdf::DEST_BROWSER,
			'content' => $content,
			'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
			'cssInline' => '.kv-heading-1{font-size:18px}',
			'options' => ['title' => \Yii::$app->name],
			'methods' => [
				'SetHeader' => [\Yii::$app->name],
				'SetFooter' => ['{PAGENO}'],
			]
		]);

		return $pdf->render();
	}
<?php endif; ?>

<?php if($generator->saveAsNew):?>
	/**
	* Creates a new <?= $modelClass ?> model by another data,
	* so user don't need to input all field from scratch.
	* If creation is successful, the browser will be redirected to the 'view' page.
	*
	* @param mixed $id
	* @return mixed
	*/
	public function actionSaveAsNew(<?= $actionParams; ?>) {
		$model = new <?= $modelClass ?>();

		if (Yii::$app->request->post('_asnew') != '1') {
			$model = $this->findModel(<?= $actionParams; ?>);
		}

		// saveAll() will delete existing related records, so we exclude those for loadAll() and saveAll()
		if ($model->loadAll(Yii::$app->request->post()<?= !empty($generator->skippedRelations) ? ", [".implode(", ", $skippedRelations)."]" : ""; ?>) && $model->saveAll(<?= !empty($generator->skippedRelations) ? "[".implode(", ", $skippedRelations)."]" : ""; ?>)) {
			return $this->redirect(['view', <?= $urlParams ?>]);
		} else {
			return $this->render('saveAsNew', [
				'model' => $model,
			]);
		}
	}
<?php endif; ?>

	/**
	 * Finds the <?= $modelClass ?> model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 * <?= implode("\n     * ", $actionParamComments) . "\n" ?>
	 * @return <?=                   $modelClass ?> the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	protected function findModel(<?= $actionParams ?>)
	{
<?php
if (count($pks) === 1) {
   if (isset($generator->webUserColName)) {
	   $user_col_name = $generator->webUserColName;
	   $condition = "['id'=>\$id, '$user_col_name'=>Yii::\$app->user->id ]";
   }
   else {
	   $condition = '$id';
   }
} else {
	$condition = [];
	foreach ($pks as $pk) {
		// xx i don't know how to handle this yet - dp
		echo "failed!\n";
		exit();
		$condition[] = "'$pk' => \$$pk";
	}


	$condition = '[' . implode(', ', $condition) . ']';
}
?>
		if (($model = <?= $modelClass ?>::findOne(<?= $condition ?>)) !== null) {
			return $model;
		} else {
			throw new NotFoundHttpException(<?= $generator->generateString('The requested page does not exist.')?>);
		}
	}
<?php foreach ($relations as $name => $rel): ?>
<?php if ($rel[Generator::REL_IS_MULTIPLE] && isset($rel[Generator::REL_TABLE]) && !in_array($name, $generator->skippedRelations)): ?>

	/**
	* Action to load a tabular form grid
	* for <?= $rel[Generator::REL_CLASS] . "\n" ?>
	* @author Yohanes Candrajaya <moo.tensai@gmail.com>
	* @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
	*
	* @return mixed
	*/
	public function actionAdd<?= $rel[Generator::REL_CLASS] ?>()
	{
		if (Yii::$app->request->isAjax) {
			$row = Yii::$app->request->post('<?= $rel[Generator::REL_CLASS] ?>');
			if (!empty($row)) {
				$row = array_values($row);
			}
			if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add') {
			   $row[] = [
			<?php
			   $className = $rel[Generator::REL_CLASS];
			   $hiddenFields = ArrayHelper::getValue($generator->manyRelationsHiddenFieldTweakList, $className, array());
			   foreach ($hiddenFields as $fieldName => $fieldValueList) {
				echo "\t\t'$fieldName' => " . $fieldValueList[0] . ",\n";
			   }
			 ?>
			   ];
			}
			return $this->renderAjax('_form<?= $rel[Generator::REL_CLASS] ?>', ['row' => $row]);
		} else {
			throw new NotFoundHttpException(<?= $generator->generateString('The requested page does not exist.')?>);
		}
	}
<?php endif; ?>
<?php endforeach; ?>

<?php
foreach ($generator->editFormDepFieldList as $formDepField => $formDepFieldDef) {
		$childField = $formDepField;
		$defExplode = explode('#', $fromDepFieldDef[0]);
		$parentField = $defExplode[0];
		$parentQuery = $defExplode[2];
		
		$parentFieldCamel = \yii\helpers\Inflector::camelize($childField);
		$functionName = $parentFieldCamel . "List";
?>
	
	/**
	 * Called by our form to get the cild list based on the parent field.
	 */
	public function action<?= $functionName ?>()
	{
		Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		
		$out = [];
		if (isset($_POST['depdrop_parents'])) {
			$parents = $_POST['depdrop_parents'];
			if ($parents != null) {
				$parentID = $parents[0];
				$out = self::get<?= $functionName ?>($parentID);
				return ['output'=>$out, 'selected'=>''];
			}
		}
		
		return ['output'=>'', 'selected'=>''];
	}
	
	private function get<?= $functionName ?>(int $parentID)
	{
		$user = \common\models\User::findOne(Yii::$app->user->id);
		dassert(isset($user));
		
		$result = array();
		$childQuery = $<?= $parentQuery ?>;
		foreach ($childQuery->all() as $childItem) {
			$row['id'] = $childItem->id;
			$row['name'] = $childItem->name;
			$result[] = $row;
		}

		return $result;
	}

<?php	
}
?>

	private function showPageNotifications(int $id = null)
	{
		$this->showUserNotifications();

		if (isset($id)) {
			$this->showModelNotifications($id);
		}
	}

	private function showUserNotifications()
	{
		$notificationsList = \common\models\User::getUserNotifications(Yii::$app->user->id);

		$flashAlertsList = [ 'danger', 'warning' ];
		foreach ($notificationsList as $nLevel => $nMsgList) {
			if (! in_array($nLevel, $flashAlertsList)) {
			 continue;
			}

			foreach ($nMsgList as $nMsg) {
				Yii::$app->session->addFlash($nLevel, $nMsg);
			}
		}
	}

	private function showModelNotifications($id)
	{
		$model = $this->findModel($id);
		$notificationsList = method_exists($model, 'getModelNotifications') ? $model->getModelNotifications($id) : array();

		$flashAlertsList = [ 'info', 'danger', 'warning' ];
		foreach ($notificationsList as $nLevel => $nMsgList) {
			if (! in_array($nLevel, $flashAlertsList)) {
			 continue;
			}

			foreach ($nMsgList as $nMsg) {
				Yii::$app->session->addFlash($nLevel, $nMsg);
			}
		}
	}
}

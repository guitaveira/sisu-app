<?php
namespace mootensai\enhancedgii\crud;

use Yii;
use yii\db\ActiveRecord;
use yii\db\ColumnSchema;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\helpers\ArrayHelper;

/**
 * Generates Relational CRUD
 *
 *
 * @author Yohanes Candrajaya <moo.tensai@gmail.com>
 * @since 2.0
 */
class Generator extends \mootensai\enhancedgii\BaseGenerator
{

    public $nameAttribute = 'name, title, username';
    public $hiddenColumns = 'id, lock';

    public $theme_select2 = '\\kartik\\widgets\\Select2::THEME_MATERIAL';

    public $skippedColumns = 'created_at, updated_at, created_by, updated_by, deleted_at, deleted_by, created, modified, deleted';
    public $skippedIndexColumns;
    public $skippedColumnsInCreate;
    public $skippedColumnsInUpdate;

    public $nsModel = 'app\models';
    public $nsSearchModel = 'app\models';

    public $generateSearchModel;
    public $disableAdvancedSearch = false;
    public $searchModelClass;
    
    public $editFormDepFields;
    public $editFormDepFieldList;

    public $allowDeleteWithRelated;
    public $allowDeleteWithRelatedList;

    public $fieldNameStrip = "psa";

    // defines the default User that owns a record; not the same as created_by, updated_by, etc..
    public $webUserColName;

    public $generateQuery = true;
    public $queryNs = 'app\models';
    public $queryClass;
    public $queryBaseClass = 'yii\db\ActiveQuery';
    public $generateLabelsFromComments = false;
    public $useTablePrefix = false;
    public $generateRelations = true;
    
    public $detailViewFormatCode;
    public $detailViewValueCode;
    public $detailViewFormatCodeList;
    public $detailViewValueCodeList;

    // will we show 1-to-1 relation details in the View view??
    public $relationsShowSubDetailsInView = true;
    // if 0, show as many as we can
    public $relationsShowSubDetailsColCount = 0;

    public $manyRelationsHiddenFieldTweak;
    public $manyRelationsHiddenFieldTweakList;

    // if 0, show as many as we can
    public $relationsExpandableRelatedColumns;
    public $relationsExpandableRelatedColList;

    public $relationsExpandableRelatedActions = "{view} {update} {delete}";
    public $indexAllowedActions = "{view} {update}";

    // can we edit relations during the Update view??
    public $manyRelationsAllowedInView;
    public $manyRelationsAllowedInEdit;

    public $manyRelationsEditQueryTweak;
    public $tweakRules;

    public $disableCreate = false;
    public $disableView = false;
    public $disableDelete = false;
    public $disableUpdate = false;
    public $disableIndex = false;
    public $disableSaveAsNew = true;
    public $disableViewDelete = false;

    // overrides default columns for index View
    public $indexViewColumns = [];

    public $manyRelationsEditFields = "";
    public $manyRelationsViewFields = "";
    public $manyRelationsEditFieldList;
    public $manyRelationsViewFieldList;

    public $extraCreateButton;

    public $generateMigrations = true;
    public $optimisticLock = 'lock';
    public $createdAt = 'created_at';
    public $updatedAt = 'updated_at';
    public $timestampValue = "new Expression('NOW()')";
    public $createdBy = 'created_by';
    public $updatedBy = 'updated_by';
    public $blameableValue = 'Yii::\$app->user->id';
    public $UUIDColumn = 'id';
    public $deletedBy = 'deleted_by';
    public $deletedAt = 'deleted_at';
    public $nsController = 'app\controllers';
    public $controllerClass;
    public $pluralize = true;
    public $loggedUserOnly;
    public $expandable;
    public $cancelable;
    public $saveAsNew;
    public $pdf;
    public $viewPath = '@app/views';
    public $baseControllerClass = 'yii\web\Controller';
    public $indexWidgetType = 'grid';
    public $relations;


    /**
     * @inheritdoc
     */
    public function getName()
    {
        return 'I/O Generator (CRUD)';
    }

    /**
     * @inheritdoc
     */
    public function getDescription()
    {
        return 'This generator generates controller and views that implement CRUD (Create, Read, Update, Delete)
            operations for the database.';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), [
            [['db', 'nsModel', 'viewPath', 'queryNs', 'nsController', 'nsSearchModel', 'tableName', 'modelClass', 'searchModelClass', 'baseControllerClass'], 'filter', 'filter' => 'trim'],
            [['tableName', 'baseControllerClass', 'indexWidgetType', 'db'], 'required'],
            [['tableName'], 'match', 'pattern' => '/^(\w+\.)?([\w\*]+)$/', 'message' => 'Only word characters, and optionally an asterisk and/or a dot are allowed.'],
            [['tableName'], 'validateTableName'],
//            [['searchModelClass'], 'compare', 'compareAttribute' => 'modelClass', 'operator' => '!==', 'message' => 'Search Model Class must not be equal to Model Class.'],
            [['modelClass', 'baseControllerClass', 'searchModelClass', 'db', 'queryClass'], 'match', 'pattern' => '/^[\w\\\\]*$/', 'message' => 'Only word characters and backslashes are allowed.'],
//            [['modelClass'], 'validateClass', 'params' => ['extends' => BaseActiveRecord::className()]],
            [['baseControllerClass'], 'validateClass', 'params' => ['extends' => Controller::className()]],
            [['db'], 'validateDb'],
            [['controllerClass'], 'match', 'pattern' => '/Controller$/', 'message' => 'Controller class name must be suffixed with "Controller".'],
            [['controllerClass'], 'match', 'pattern' => '/(^|\\\\)[A-Z][^\\\\]+Controller$/', 'message' => 'Controller class name must start with an uppercase letter.'],
//            [['searchModelClass'], 'validateNewClass'],
            [['indexWidgetType'], 'in', 'range' => ['grid', 'list']],
//            [['modelClass'], 'validateModelClass'],
            [['enableI18N', 'generateRelations', 'generateSearchModel', 'pluralize', 'expandable', 'cancelable', 'pdf', 'loggedUserOnly'], 'boolean'],
            [['relationsShowSubDetailsInView'], 'boolean'],
            [['disableAdvancedSearch'], 'boolean'],
            [['relationsShowSubDetailsColCount'], 'integer'],
            [['relationsExpandableRelatedColumns', 'manyRelationsHiddenFieldTweak'], 'string'],
            [['relationsExpandableRelatedActions'], 'string'],
            [['manyRelationsAllowedInEdit'], 'string'],
            [['extraCreateButton'], 'string'],
            [['manyRelationsAllowedInView'], 'string'],
            [['manyRelationsEditQueryTweak'], 'string'],
            [['detailViewFormatCode', 'detailViewValueCode'], 'string'],
            [['indexViewColumns'], 'string'],
            [['skippedIndexColumns'], 'string'],
            [['allowDeleteWithRelated'], 'string'],
            [['manyRelationsEditFields', 'manyRelationsViewFields'], 'string'],
            [['disableCreate', 'disableUpdate', 'disableView', 'disableDelete', 'disableIndex', 'disableViewDelete'], 'boolean'],
            [['skippedColumnsInCreate'], 'string'],
            [['skippedColumnsInUpdate'], 'string'],
            [['editFormDepFields'], 'string'],
            [['webUserColName'], 'string'],
            [['messageCategory'], 'validateMessageCategory', 'skipOnEmpty' => false],
            [['viewPath', 'skippedRelations', 'skippedColumns',
                'controllerClass', 'blameableValue', 'nameAttribute',
                'hiddenColumns', 'createdAt', 'updatedAt', 'createdBy', 'updatedBy',
                'UUIDColumn', 'saveAsNew'], 'safe'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'db' => 'Database Connection ID',
            'modelClass' => 'Model Class',
            'generateQuery' => 'Generate ActiveQuery',
            'queryNs' => 'ActiveQuery Namespace',
            'queryClass' => 'ActiveQuery Class',
            'nsModel' => 'Model Namespace',
            'nsSearchModel' => 'Search Model Namespace',
            'UUIDColumn' => 'UUID Column',
            'nsController' => 'Controller Namespace',
            'viewPath' => 'View Path',
            'baseControllerClass' => 'Base Controller Class',
            'indexWidgetType' => 'Widget Used in Index Page',
            'searchModelClass' => 'Search Model Class',
            'expandable' => 'Expandable Index Grid View',
            'cancelable' => 'Add Cancel Button On Form',
            'pdf' => 'PDF Printable View'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints()
    {
        return array_merge(parent::hints(), [
            'db' => 'This is the ID of the DB application component.',
            'tableName' => 'This is the name of the DB table that the new ActiveRecord class is associated with, e.g. <code>post</code>.
                The table name may consist of the DB schema part if needed, e.g. <code>public.post</code>.
                The table name may end with asterisk to match multiple table names, e.g. <code>tbl_*</code>
                will match tables who name starts with <code>tbl_</code>. In this case, multiple ActiveRecord classes
                will be generated, one for each matching table name; and the class names will be generated from
                the matching characters. For example, table <code>tbl_post</code> will generate <code>Post</code>
                class.',
            'nameAttribute' => 'This is the (set) of name column that you use to show as label, '
                . 'separated by comma (,) for multiple table(asterisk on Table Name).',
            'skippedColumns' => 'Fill this field with the column name that you dont want to generate form & labels for the table.
                You can fill multiple columns, separated by comma (,). You may specify the column name
                although "Table Name" ends with asterisk, in which case all columns will not be generated at all models & CRUD.',
            'hiddenColumns' => 'Fill this field with the column name that you want to generate form with the hidden field of the table.
                You can fill multiple columns, separated by comma (,). You may specify the column name
                although "Table Name" ends with asterisk, in which case all columns will be generated with hidden field at the forms',
            'nsModel' => 'This is the namespace of the ActiveRecord class to be generated, e.g., <code>app\models</code>',
            'modelClass' => 'This is the name of the Model class to be generated. The class name should not contain
                the namespace part as it is specified in "Model Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple ActiveRecord classes will be generated.',
            'baseModelClass' => 'This is the base class of the new ActiveRecord class. It should be a fully qualified namespaced class name.',
            'nsSearchModel' => 'This is the namespace of the search model class to be generated, e.g., <code>app\models</code>',
            'searchModelClass' => 'This is the name of the search class to be generated. The class name should not contain
                the namespace part as it is specified in "Search Model Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple search model classes will be generated.',
            'generateQuery' => 'This indicates whether to generate ActiveQuery for the ActiveRecord class.',
            'generateLabelsFromComments' => 'This indicates whether the generator should generate attribute labels
                by using the comments of the corresponding DB columns.',
            'useTablePrefix' => 'This indicates whether the table name returned by the generated ActiveRecord class
                should consider the <code>tablePrefix</code> setting of the DB connection. For example, if the
                table name is <code>tbl_post</code> and <code>tablePrefix=tbl_</code>, the ActiveRecord class
                will return the table name as <code>{{%post}}</code>.',
            'generateSearchModel' => 'This indicates whether the generator should generate search model based on
                columns it detects in the database.',
            'generateRelations' => 'This indicates whether the generator should generate relations based on
                foreign key constraints it detects in the database. Note that if your database contains too many tables,
                you may want to uncheck this option to accelerate the code generation process.',
            'generateMigrations' => 'This indicates whether the generator should generate migrations based on
                table structure.',
            'optimisticLock' => 'This indicates whether the generator should generate optimistic lock feature for Model. '
                . 'Enter this field with optimistic lock column name. '
                . 'Empty this field if you want to disable this feature.',
            'createdAt' => 'This indicates whether the generator should generate Timestamp Behaviors feature for Model. '
                . 'Enter this field with Created At column name. '
                . 'Empty "Created At" & "Updated At" field if you want to disable this feature.',
            'updatedAt' => 'This indicates whether the generator should generate Timestamp Behaviors feature for Model. '
                . 'Enter this field with Updated At column name. '
                . 'Empty "Created At" & "Updated At" field if you want to disable this feature.',
            'timestampValue' => 'This will generate the </code>value</code> configuration entry for Timestamp Behaviors.  e.g., <code>new Expression(\'NOW()\')</code>',
            'createdBy' => 'This indicates whether the generator should generate Blameable Behaviors feature for Model. '
                . 'Enter this field with Created By column name. '
                . 'Empty "Created By" & "Updated By" field if you want to disable this feature.',
            'updatedBy' => 'This indicates whether the generator should generate Blameable Behaviors feature for Model. '
                . 'Enter this field with Updated By column name. '
                . 'Empty "Created By" & "Updated By" field if you want to disable this feature.',
            'blameableValue' => 'This will generate the </code>value</code> configuration entry for Blameable Behaviors.  e.g., <code>new Expression(\'NOW()\')</code>',
            'UUIDColumn' => 'This indicates whether the generator should generate UUID Behaviors feature for Model. '
                . 'Enter this field with UUID column name. '
                . 'Empty "UUID Column" field if you want to disable this feature.',
            'deletedBy' => 'This indicates whether the generator should generate Soft Delete feature for Model. '
                . 'Enter this field with Deleted By column name. '
                . 'Empty "Deleted By" & "Deleted At" field if you want to disable this feature.',
            'deletedAt' => 'This indicates whether the generator should generate Soft Delete feature for Model. '
                . 'Enter this field with Updated By column name. '
                . 'Empty "Deleted By" & "Deleted At" field if you want to disable this feature.',
            'nsController' => 'This is the namespace of the Controller class to be generated, e.g., <code>app\controllers</code>',
            'controllerClass' => 'This is the name of the Controller class to be generated. The class name should not contain
                the namespace part as it is specified in "Controller Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple Controller classes will be generated.',
            'nsModel' => 'This is the namespace of the ActiveRecord class to be generated, e.g., <code>app\models</code>',
            'pluralize' => 'Set the generator to generate pluralize for label',
            'expandable' => 'Set the generator to generate expandable/collapsible row for related at index',
            'cancelable' => 'Set the generator to generate cancel button to return to grid view at form',
            'pdf' => 'Set the generator to generate printable PDF generator at view',
            'viewPath' => 'Specify the directory for storing the view scripts for the controller. You may use path alias here, e.g.,
                <code>/var/www/basic/controllers/views/post</code>, <code>@app/views/post</code>. If not set, it will default
                to <code>@app/views/ControllerID</code>',
            'baseControllerClass' => 'This is the class that the new CRUD controller class will extend from.
                You should provide a fully qualified class name, e.g., <code>yii\web\Controller</code>.',
            'skippedRelations' => 'Fill this field with the relation name that you dont want to generate CRUD for the table.
                You can fill multiple relations, separated by comma (,). You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case all relations will be generated.',
            'indexWidgetType' => 'This is the widget type to be used in the index page to display list of the models.
                You may choose either <code>GridView</code> or <code>ListView</code>',
            'modelClass' => 'This is the name of the Model class to be generated. The class name should not contain
                the namespace part as it is specified in "Model Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple ActiveRecord classes will be generated.',
            'queryNs' => 'This is the namespace of the ActiveQuery class to be generated, e.g., <code>app\models</code>',
            'queryClass' => 'This is the name of the ActiveQuery class to be generated. The class name should not contain
                the namespace part as it is specified in "ActiveQuery Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple ActiveQuery classes will be generated.',
            'queryBaseClass' => 'This is the base class of the new ActiveQuery class. It should be a fully qualified namespaced class name.',
            'saveAsNew' => 'Creates a new model by another data, so user don\'t need to input all field from scratch.'
        ]);
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes()
    {
        return array_merge(parent::stickyAttributes(), [
            'db',
            'skippedColumns',
            'hiddenColumns',
            'nameAttribute',
            'nsModel',
            'nsSearchModel',
            'nsController',
            'baseModelClass',
            'queryNs',
            'queryBaseClass',
            'optimisticLock',
            'createdBy',
            'updatedBy',
            'deletedBy',
            'createdAt',
            'timestampValue',
            'updatedAt',
            'deletedAt',
            'blameableValue',
            'UUIDColumn',
            'baseControllerClass',
            'indexWidgetType',
            'viewPath']);
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates()
    {
        return ['controller.php'];
    }

    private function explodeCmdLineParameter(string $value, int $expectedGroups, string $rowSep = ";", string $groupSep = ":", $colSep = ",")
    {
        if (isset($value) && strlen($value)) {
            $rowData = explode($rowSep, $value);
            $rowData = array_filter($rowData);
        }
        else {
            return array();
        }

        $finalArray = array();
        for ($curRow = 0; $curRow < count($rowData); $curRow++) {
            $row = $rowData[$curRow];

            $groups = explode($groupSep, $row);
            $groupsCount = count($groups);
            if ($groupsCount != $expectedGroups) {
                echo "Bad row, bad group count ($groupsCount != $expectedGroups) : $row\n";
                exit(1);
            }

            $namingList = array();
            for ($curCol = 0; $curCol < $groupsCount - 1; $curCol++) {
                array_push($namingList, $groups[$curCol]);
            }

            $dataCol = $groups[$groupsCount - 1];
            $dataList = explode($colSep, $dataCol);

            ArrayHelper::setValue($finalArray, $namingList, $dataList);
        }

        return array_filter($finalArray);
    }

    /**
     * @inheritdoc
     */

    public function generate()
    {
        $files = [];
        $relations = $this->generateRelations();
        $this->relations = $relations;
        $db = $this->getDbConnection();

        $this->nameAttribute = ($this->nameAttribute) ? explode(',', str_replace(' ', '', $this->nameAttribute)) : [$this->nameAttribute];
        $this->hiddenColumns = ($this->hiddenColumns) ? explode(',', str_replace(' ', '', $this->hiddenColumns)) : [$this->hiddenColumns];

        $this->skippedColumns = ($this->skippedColumns) ? explode(',', str_replace(' ', '', $this->skippedColumns)) : [$this->skippedColumns];
        $this->skippedColumnsInCreate = ($this->skippedColumnsInCreate) ? explode(',', str_replace(' ', '', $this->skippedColumnsInCreate)) : [$this->skippedColumnsInCreate];
        $this->skippedColumnsInUpdate = ($this->skippedColumnsInUpdate) ? explode(',', str_replace(' ', '', $this->skippedColumnsInUpdate)) : [$this->skippedColumnsInUpdate];
        $this->skippedIndexColumns = ($this->skippedIndexColumns) ? explode(',', str_replace(' ', '', $this->skippedIndexColumns)) : [$this->skippedIndexColumns];
        $this->skippedRelations = ($this->skippedRelations) ? explode(',', str_replace(' ', '', $this->skippedRelations)) : [$this->skippedRelations];
        $this->manyRelationsAllowedInEdit = ($this->manyRelationsAllowedInEdit) ? explode(',', str_replace(' ', '', $this->manyRelationsAllowedInEdit)) : [$this->manyRelationsAllowedInEdit];
        $this->manyRelationsAllowedInView = ($this->manyRelationsAllowedInView) ? explode(',', str_replace(' ', '', $this->manyRelationsAllowedInView)) : [$this->manyRelationsAllowedInView];

        $this->allowDeleteWithRelatedList = ($this->allowDeleteWithRelated) ? explode(',', str_replace(' ', '', $this->allowDeleteWithRelated)) : [$this->allowDeleteWithRelated];
        $this->allowDeleteWithRelatedList = array_filter($this->allowDeleteWithRelatedList);
        
        $this->detailViewFormatCodeList = $this->explodeCmdLineParameter($this->detailViewFormatCode, 2);
        $this->detailViewValueCodeList = $this->explodeCmdLineParameter($this->detailViewValueCode, 2, '!', '#', '?');

        $this->tweakRules = $this->explodeCmdLineParameter($this->manyRelationsEditQueryTweak, 3);
        $this->relationsExpandableRelatedColList = $this->explodeCmdLineParameter($this->relationsExpandableRelatedColumns, 2);
        $this->manyRelationsHiddenFieldTweakList = $this->explodeCmdLineParameter($this->manyRelationsHiddenFieldTweak, 3, ';', '#');

        $this->manyRelationsEditFieldList = $this->explodeCmdLineParameter($this->manyRelationsEditFields, 2);
        $this->manyRelationsViewFieldList = $this->explodeCmdLineParameter($this->manyRelationsViewFields, 2);

        $this->skippedColumns = array_filter($this->skippedColumns);
        $this->skippedColumnsInCreate = array_filter($this->skippedColumnsInCreate);
        $this->skippedColumnsInUpdate = array_filter($this->skippedColumnsInUpdate);
        $this->skippedIndexColumns = array_filter($this->skippedIndexColumns);
        $this->skippedRelations = array_filter($this->skippedRelations);
        $this->manyRelationsAllowedInEdit = array_filter($this->manyRelationsAllowedInEdit);
        $this->manyRelationsAllowedInView = array_filter($this->manyRelationsAllowedInView);
        
        $this->editFormDepFieldList = $this->explodeCmdLineParameter($this->editFormDepFields, 2);

        if (isset($this->webUserColName) && ! strlen($this->webUserColName)) {
            $this->webUserColName = null;
        }

        foreach ($this->getTableNames() as $tableName) {
            // model :
            if (strpos($this->tableName, '*') !== false) {
                $modelClassName = $this->generateClassName($tableName);
                $controllerClassName = $modelClassName . 'Controller';
            } else {
                $modelClassName = (!empty($this->modelClass)) ? $this->modelClass : Inflector::id2camel($tableName, '_');
                $controllerClassName = (!empty($this->controllerClass)) ? $this->controllerClass : $modelClassName . 'Controller';
            }
//            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $tableSchema = $db->getTableSchema($tableName);
            $this->modelClass = "{$this->nsModel}\\{$modelClassName}";
            $this->tableSchema = $tableSchema;
//            $this->relations = isset($relations[$tableName]) ? $relations[$tableName] : [];
            $this->controllerClass = $this->nsController . '\\' . $controllerClassName;
            $isTree = !array_diff(self::getTreeColumns(), $tableSchema->columnNames);

            // search model :
            if ($this->generateSearchModel && !$isTree) {
                if (empty($this->searchModelClass) || strpos($this->tableName, '*') !== false) {
                    $searchModelClassName = $modelClassName . 'Search';
                } else {
                    if ($this->nsSearchModel === $this->nsModel && $this->searchModelClass === $modelClassName) {
                        $searchModelClassName = $this->searchModelClass . 'Search';
                    } else {
                        $searchModelClassName = $this->searchModelClass;
                    }
                }
                $this->searchModelClass = $this->nsSearchModel . '\\' . $searchModelClassName;
                $searchModel = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->searchModelClass, '\\') . '.php'));
                $files[] = new CodeFile($searchModel, $this->render('search.php',
                    ['relations' => isset($relations[$tableName]) ? $relations[$tableName] : []]));
            }

            //controller
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->nsController)) . '/' . $controllerClassName . '.php',
                ($isTree) ?
                    $this->render('controllerNested.php', [
                        'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                    ])
                    :
                    $this->render('controller.php', [
                        'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                    ])
            );

            // views :
            $viewPath = $this->getViewPath();
            $templatePath = $this->getTemplatePath() . '/views';
            foreach (scandir($templatePath) as $file) {
//                if($file === '_formNested.php')
//                    echo  $file;
                if (empty($this->searchModelClass) && $file === '_search.php') {
                    continue;
                }
                if ($file === '_formrefone.php' || $file === '_formrefmany.php' || $file === '_datarefone.php'
                    || $file === '_datarefmany.php' || $file === '_expand.php' || $file === '_detail.php'
                    || $file === '_data.php' || $file === 'saveAsNew.php' || $file === '_pdf.php') {
                    continue;
                }
                if($this->indexWidgetType != 'list' && $file === '_index.php') {
                    continue;
                }
                if($isTree && ($file === 'index.php' || $file === 'view.php' || $file === '_form.php'
                    || $file === 'create.php' || $file === 'update.php'
                    )){
                    continue;
                }
                if(!$isTree && ($file === 'indexNested.php' || $file === '_formNested.php')){
                    continue;
                }
                if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $fileName = ($isTree) ? str_replace('Nested','',$file) : $file;
                    $files[] = new CodeFile("$viewPath/$fileName", $this->render("views/$file", [
                        'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                        'isTree' => $isTree
                    ]));
                }
            }
            if ($this->expandable) {
                $files[] = new CodeFile("$viewPath/_expand.php", $this->render("views/_expand.php", [
                    'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                ]));
            }

            if ($this->pdf) {
                $files[] = new CodeFile("$viewPath/_pdf.php", $this->render("views/_pdf.php", [
                    'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                ]));
            }

            if($this->saveAsNew){
                $files[] = new CodeFile("$viewPath/saveAsNew.php", $this->render("views/saveAsNew.php", [
                    'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                ]));
            }

            if (isset($relations[$tableName]) && !$isTree) {
                if ($this->expandable) {
                    $files[] = new CodeFile("$viewPath/_detail.php", $this->render("views/_detail.php", [
                        'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                    ]));
                }
                foreach ($relations[$tableName] as $name => $rel) {
                    if ($rel[self::REL_IS_MULTIPLE] && isset($rel[self::REL_TABLE]) && !in_array($name, $this->skippedRelations)) {
                        $files[] = new CodeFile("$viewPath/_form{$rel[self::REL_CLASS]}.php", $this->render("views/_formrefmany.php", [
                            'relations' => isset($relations[$tableName]) ? $relations[$tableName][$name] : [],
                        ]));
                        if ($this->expandable) {
                            $files[] = new CodeFile("$viewPath/_data{$rel[self::REL_CLASS]}.php", $this->render("views/_datarefmany.php", [
                                'relName' => $name,
                                'relations' => isset($relations[$tableName]) ? $relations[$tableName][$name] : [],
                            ]));
                        }
                    }else if(isset($rel[self::REL_IS_MASTER]) && !$rel[self::REL_IS_MASTER] && !in_array($name, $this->skippedRelations)){
                        $files[] = new CodeFile("$viewPath/_form{$rel[self::REL_CLASS]}.php", $this->render("views/_formrefone.php", [
                            'relName' => $name,
                            'relations' => isset($relations[$tableName]) ? $relations[$tableName][$name] : [],
                        ]));
                        if ($this->expandable) {
                            $files[] = new CodeFile("$viewPath/_data{$rel[self::REL_CLASS]}.php", $this->render("views/_datarefone.php", [
                                'relName' => $name,
                                'relations' => isset($relations[$tableName]) ? $relations[$tableName][$name] : [],
                            ]));
                        }
                    }
                }
            }

            if (strpos($this->tableName, '*') !== false || $isTree) {
                $this->modelClass = '';
                $this->controllerClass = '';
                $this->searchModelClass = '';
            } else {
                $this->modelClass = $modelClassName;
                $this->controllerClass = $controllerClassName;
                if ($this->generateSearchModel) {
                    $this->searchModelClass = $searchModelClassName;
                }
            }
        }
        $this->nameAttribute = (is_array($this->nameAttribute)) ? implode(', ', $this->nameAttribute) : '';
        $this->hiddenColumns = (is_array($this->hiddenColumns)) ? implode(', ', $this->hiddenColumns) : '';
        $this->skippedColumns = (is_array($this->skippedColumns)) ? implode(', ', $this->skippedColumns) : '';
        $this->skippedRelations = (is_array($this->skippedRelations)) ? implode(', ', $this->skippedRelations) : '';

        return $files;
    }

    /**
     * @return string the controller ID (without the module ID prefix)
     */
    public function getControllerID()
    {
        $pos = strrpos($this->controllerClass, '\\');
        $class = substr(substr($this->controllerClass, $pos + 1), 0, -10);

        return Inflector::camel2id($class);
    }

    /**
     * @return string the controller view path
     */
    public function getViewPath()
    {
        if (empty($this->viewPath)) {
            return Yii::getAlias('@app/views/' . $this->getControllerID());
        } else {
            return Yii::getAlias($this->viewPath . '/' . $this->getControllerID());
        }
    }

    public function getNameAttribute()
    {
        foreach ($this->tableSchema->getColumnNames() as $name) {
            foreach ($this->nameAttribute as $nameAttr) {
                if (!strcasecmp($name, $nameAttr) || !strcasecmp($name, $this->tableSchema->fullName)) {
                    return $name;
                }
            }
        }
        /* @var $class ActiveRecord */
//        $class = $this->modelClass;
        $pk = empty($this->tableSchema->primaryKey) ? $this->tableSchema->getColumnNames()[0] : $this->tableSchema->primaryKey[0];

        return $pk;
    }

    public function getNameAttributeFK($tableName)
    {
        $tableSchema = $this->getDbConnection()->getTableSchema($tableName);
        foreach ($tableSchema->getColumnNames() as $name) {
            if (in_array($name, $this->nameAttribute) || $name === $tableName) {
                return $name;
            }
        }
        $pk = empty($tableSchema->primaryKey) ? $tableSchema->getColumnNames()[0] : $tableSchema->primaryKey[0];

        return $pk;
    }

    public function generateFK($tableSchema = null)
    {
        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }
        $fk = [];
        if (isset($this->relations[$tableSchema->fullName])) {
            foreach ($this->relations[$tableSchema->fullName] as $name => $relations) {
                foreach ($tableSchema->foreignKeys as $value) {
                    if (isset($relations[self::REL_FOREIGN_KEY]) && $relations[self::REL_TABLE] == $value[self::FK_TABLE_NAME]) {
                        if ($tableSchema->fullName == $value[self::FK_TABLE_NAME] && $relations[self::REL_IS_MULTIPLE]) { // In case of self-referenced tables (credit to : github.com/iurijacob)

                        } else {
                            $fk[$relations[5]] = $relations;
                            $fk[$relations[5]][] = $name;
                        }

                    }
                }
            }
        }
        return $fk;
    }

    /**
     * Generates code for Grid View field
     * @param string $attribute
     * @param TableSchema $tableSchema
     * @return string
     */
    public function generateDetailViewField($attribute, $fk, $tableSchema = null)
    {
        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }
        if (in_array($attribute, $this->hiddenColumns)) {
            return "['attribute' => '$attribute', 'visible' => false],\n";
        }

        $column = $tableSchema->columns[$attribute];
        //$humanize = Inflector::humanize($rel[Generator::REL_TABLE]);
        $humanize = (! $column->allowNull ? "-- required --" : "");

        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "";
            } else {
                return "'$attribute',\n";
            }
        }

        $format = $this->generateColumnFormat($column);
//        if($column->autoIncrement){
//            return "";
//        } else
        if (array_key_exists($attribute, $fk)) {
            $rel = $fk[$attribute];
            $labelCol = $this->getNameAttributeFK($rel[Generator::REL_TABLE]);
//            $humanize = Inflector::humanize($rel[Generator::REL_TABLE]);
//            $id = 'grid-' . Inflector::camel2id(StringHelper::basename($this->searchModelClass)) . '-' . $attribute;
//            $modelRel = $rel[2] ? lcfirst(Inflector::pluralize($rel[1])) : lcfirst($rel[1]);

            $finalName = str_replace($this->fieldNameStrip, '', $rel[Generator::REL_FOREIGN_KEY]);
            $humanize = ucwords(Inflector::humanize($finalName));

            //if (in_array("rule_id", $this->detailViewFormatCodeList)) {
            if (ArrayHelper::keyExists($attribute, $this->detailViewFormatCodeList)) {
                $formatValue = $this->detailViewFormatCodeList[$attribute][0];
            }
            else {
                $formatValue = "text";
            }
            //if (in_array("rule_id", $this->detailViewFormatCodeList)) {
            if (ArrayHelper::keyExists($attribute, $this->detailViewValueCodeList)) {
                $valueLine = "'value' => " . $this->detailViewValueCodeList[$attribute][0] . ",";
            }
            else {
                $valueLine = null;
            }
            
            $output = "
                [
                    'attribute' => '$rel[7].$labelCol',
                    'label' => '$humanize',
                    'format' => '$formatValue',
                    $valueLine
                ],\n";

            return $output;
        }
        elseif ($column->phpType === 'boolean' || $column->dbType === 'tinyint(1)') {
            //return "\$form->field($model, '$attribute')->checkbox()";
                $output = "
                [
                    'attribute' => '$attribute',
                    'format' => 'boolean'
                ],";
            return $output;
        } else {
            return "'$attribute" . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }

    /**
     * Generates code for Grid View field
     * @param string $attribute
     * @param array $fk
     * @param TableSchema $tableSchema
     * @return string
     */
    public function generateGridViewField($attribute, $fk, $tableSchema = null)
    {
        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }

        if (in_array($attribute, $this->hiddenColumns)) {
            return "['attribute' => '$attribute', 'visible' => false],\n";
        }
        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "";
            } else {
                return "'$attribute',\n";
            }
        }
        $column = $tableSchema->columns[$attribute];
        $format = $this->generateColumnFormat($column);
        $baseClass = StringHelper::basename($this->modelClass);

        if (array_key_exists($attribute, $fk)) {
            $rel = $fk[$attribute];
            if ($rel[self::REL_CLASS] == $baseClass) {
                return "";
            }
            $labelCol = $this->getNameAttributeFK($rel[Generator::REL_TABLE]);
//            $modelRel = $rel[2] ? lcfirst(Inflector::pluralize($rel[1])) : lcfirst($rel[1]);

            $finalName = str_replace($this->fieldNameStrip, '', $rel[Generator::REL_FOREIGN_KEY]);
            $humanize = ucwords(Inflector::humanize($finalName));

            $output = "[
                'attribute' => '$rel[7].$labelCol',
                'label' => '$humanize',
            ],";
            return $output;
        } else {
            return "'$attribute" . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }

    /**
     * Generates code for Grid View field
     * @param string $attribute
     * @param array $fk
     * @param TableSchema $tableSchema
     * @return string
     */
    public function generateGridViewFieldIndex($attribute, $fk, $tableSchema = null)
    {
        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }
        if (in_array($attribute, $this->hiddenColumns)) {
            return "['attribute' => '$attribute', 'visible' => false],\n";
        }
//        $humanize = Inflector::humanize($attribute, true);

        $column = $tableSchema->columns[$attribute];

        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "";
            }
            else
            {
                return "'$attribute',\n";
            }
        }

        $format = $this->generateColumnFormat($column);

//        if($column->autoIncrement){
//            return "";
//        } else
        if (array_key_exists($attribute, $fk) && $attribute)
        {
            $rel = $fk[$attribute];
            $labelCol = $this->getNameAttributeFK($rel[Generator::REL_TABLE]);

            // dp xx
            //$humanize = Inflector::humanize($rel[Generator::REL_TABLE]);
            $humanize = "--select--";

            $id = 'grid-' . Inflector::camel2id(StringHelper::basename($this->searchModelClass)) . '-' . $attribute;
//            $modelRel = $rel[2] ? lcfirst(Inflector::pluralize($rel[1])) : lcfirst($rel[1]);
           if ($column->allowNull)
           {
                $finalName = str_replace($this->fieldNameStrip, '', $rel[Generator::REL_FOREIGN_KEY]);
                $humanize = Inflector::humanize($finalName);

                if ($this->generateSearchModel && isset($this->webUserColName)) {
                    $webUserColNameText = "andWhere(['" . $this->webUserColName . "'=>Yii::\$app->user->id])->";
                }
                else {
                    $webUserColNameText = null;
                }

            $output = "[
                'attribute' => '$attribute',
                'label' => '$humanize',
                'value' => function(\$model){
                    if (\$model->$rel[7])
                    {return \$model->$rel[7]->$labelCol;}
                    else
                    {return NULL;}
                },
                'filterType' => GridView::FILTER_SELECT2,
                'filter' => \\yii\\helpers\\ArrayHelper::map(\\$this->nsModel\\$rel[1]::find()->".$webUserColNameText."asArray()->all(), '{$rel[self::REL_PRIMARY_KEY]}', '$labelCol'),
                'filterWidgetOptions' => [
                    'pluginOptions' => ['allowClear' => true],
                ],
                'filterInputOptions' => ['placeholder' => '--INPUT--', 'id' => '$id']
            ],\n";
           return $output;
           }
           else
           {
                if ($column->phpType === 'boolean' || $column->dbType === 'tinyint(1)')
                {
                    return "\$form->field($model, '$attribute')->checkbox()";
                }
                else
                {
                        $finalName = str_replace($this->fieldNameStrip, '', $rel[Generator::REL_FOREIGN_KEY]);
                        $humanize = ucwords(Inflector::humanize($finalName));

                if ($this->generateSearchModel && isset($this->webUserColName)) {
                    $webUserColNameText = "andWhere(['" . $this->webUserColName . "'=>Yii::\$app->user->id])->";
                }
                else {
                    $webUserColNameText = null;
                }


                    $output = "[
                        'attribute' => '$attribute',
                        'label' => '$humanize',
                        'value' => function(\$model){
                            return isset(\$model->$rel[7]) ? \$model->$rel[7]->$labelCol : null;
                        },
                        'filterType' => GridView::FILTER_SELECT2,
                        'filter' => \\yii\\helpers\\ArrayHelper::map(\\$this->nsModel\\$rel[1]::find()->".$webUserColNameText."asArray()->all(), '{$rel[self::REL_PRIMARY_KEY]}', '$labelCol'),
                        'filterWidgetOptions' => [
                            'pluginOptions' => ['allowClear' => true],
                        ],
                        'filterInputOptions' => ['placeholder' => '--INPUT--', 'id' => '$id']
                    ],\n";
                }
               return $output;
           }
        } else {
            // this is the header
            return "'$attribute" . ($format === 'text' ? "" : ":" . $format) . "',\n";
        }
    }

    /**
     * Generates code for Kartik Tabular Form field
     * @param string $attribute
     * @return string
     */
    public function generateTabularFormField($attribute, $fk, $tableSchema = null)
    {
        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }
        if (in_array($attribute, $this->hiddenColumns)) {
            return "\"$attribute\" => ['type' => TabularForm::INPUT_HIDDEN, 'columnOptions' => ['hidden'=>true]]"; //fixes #91 https://github.com/mootensai/yii2-enhanced-gii/issues/91
        }

        $column = $tableSchema->columns[$attribute];
        $finalName = str_replace($this->fieldNameStrip, '', $column->name);
        $humanize = ucwords(Inflector::humanize($finalName));

        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "\"$attribute\" => ['type' => TabularForm::INPUT_PASSWORD]";
            } else {
                return "\"$attribute\" => ['type' => TabularForm::INPUT_TEXT]";
            }
        }
        if ($column->autoIncrement) {
            return "'$attribute' => ['type' => TabularForm::INPUT_HIDDEN]";
        } elseif ($column->phpType === 'boolean' || $column->dbType === 'tinyint(1)') {
            return "'$attribute' =>
            //['type' => TabularForm::INPUT_CHECKBOX,
            //'options' => [
            //    'style' => 'position : relative; margin-top : -9px'
            //]
            [
                'type'=>TabularForm::INPUT_WIDGET,
                'widgetClass'=>\kartik\widgets\SwitchInput::classname(),
            ]";
        } elseif ($column->type === 'text' || $column->dbType === 'tinytext') {
            return "'$attribute' => ['type' => TabularForm::INPUT_TEXTAREA]";
        } elseif ($column->dbType === 'date') {
            return "'$attribute' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \\kartik\\datecontrol\\DateControl::classname(),
            'options' => [
                'type' => \\kartik\\datecontrol\\DateControl::FORMAT_DATE,
                'saveFormat' => 'php:Y-m-d',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        // dp
                        // 'placeholder' => " . $this->generateString('' . $humanize) . ",
                        'placeholder' => '--INPUT--',
                        'autoclose' => true
                    ]
                ],
            ]
        ]";
        } elseif ($column->dbType === 'time') {
            return "'$attribute' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \\kartik\\datecontrol\\DateControl::classname(),
            'options' => [
                'type' => \\kartik\\datecontrol\\DateControl::FORMAT_TIME,
                'saveFormat' => 'php:H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        // dp
                        // 'placeholder' => " . $this->generateString('' . $humanize) . ",
                        'placeholder' => '--INPUT--',
                        'autoclose' => true
                    ]
                ]
            ]
        ]";
        } elseif ($column->dbType === 'datetime') {
            return "'$attribute' => ['type' => TabularForm::INPUT_WIDGET,
            'widgetClass' => \\kartik\\datecontrol\\DateControl::classname(),
            'options' => [
                'type' => \\kartik\\datecontrol\\DateControl::FORMAT_DATETIME,
                'saveFormat' => 'php:Y-m-d H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'pluginOptions' => [
                        // dp
                        // 'placeholder' => " . $this->generateString('' . $humanize) . ",
                        'placeholder' => '--INPUT--',
                        'autoclose' => true,
                    ]
                ],
            ]
        ]";
        } elseif (array_key_exists($column->name, $fk)) {
            $rel = $fk[$column->name];
            $labelCol = $this->getNameAttributeFK($rel[self::REL_TABLE]);

            // xx - dp this is for our lablels on foreign key fields

//            $pk = empty($this->tableSchema->primaryKey) ? $this->tableSchema->getColumnNames()[0] : $this->tableSchema->primaryKey[0];
            $fkClassFQ = "\\" . $this->nsModel . "\\" . $rel[self::REL_CLASS];

            // xx - if the webUserColName is not specified on command-line, or if it doesn't exist for this model (may not be relevant)
            // then just return everything
            $fkClass = $rel[1];
            $fkTableName = $rel[Generator::REL_TABLE];
            $db = $this->getDbConnection();
            $fkTableSchema = $db->getTableSchema($fkTableName);
            $hasWebUserColColumn = array_key_exists($this->webUserColName, $fkTableSchema->columns);

            if (! isset($this->webUserColName) || ! $hasWebUserColColumn) {
                $webUserColNameText = "";
            }
            else {
                $webUserColNameText = "andWhere(['" . $this->webUserColName . "'=>Yii::\$app->user->id])->";
            }

            $tweakFilter = null;
            if (array_key_exists($fkClass, $this->tweakRules)) {
                $classRule = $this->tweakRules[$fkClass];
                if (array_key_exists($attribute, $classRule)) {
                    $tweakFilter = $classRule[$attribute][0] . '->';
                }
            }
            else {
                $tweakFilter = "orderBy('$labelCol')->";
            }

            $output = "'$attribute' => [
                'label' => '$humanize',
                'type' => TabularForm::INPUT_WIDGET,
                'widgetClass' => \\kartik\\widgets\\Select2::className(),
                'options' => [
                    'data' => \\yii\\helpers\\ArrayHelper::map($fkClassFQ::find()->" . $webUserColNameText . $tweakFilter ."asArray()->all(), '{$rel[self::REL_PRIMARY_KEY]}', '$labelCol'),
                    'options' => ['placeholder' => '--INPUT--'],
                ],
                'columnOptions' => ['width' => '200px']
            ]";
            return $output;
        }

        else {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
                $input = 'INPUT_PASSWORD';
            } else {
                $input = 'INPUT_TEXT';
            }
            if (is_array($column->enumValues) && count($column->enumValues) > 0) {
                $dropDownOptions = [];
                foreach ($column->enumValues as $enumValue) {
                    $dropDownOptions[$enumValue] = Inflector::humanize($enumValue);
                }
                return "'$attribute' => ['type' => TabularForm::INPUT_DROPDOWN_LIST,
                    'items' => " . preg_replace("/\n\s*/", ' ', VarDumper::export($dropDownOptions)) . ",
                    'options' => [
                        'columnOptions' => ['width' => '185px'],
                        // dp
                        // 'options' => ['placeholder' => " . $this->generateString('' . $humanize) . "],
                        'options' => ['placeholder' => '--INPUT--'],
                    ]
        ]";
            } elseif ($column->phpType !== 'string' || $column->size === null) {
                return "'$attribute' => ['type' => TabularForm::$input]";
            } else {
                return "'$attribute' => ['type' => TabularForm::$input]"; //max length??
            }
        }
    }

    /**
     * Generates code for active field
     * @param string $attribute
     * @return string
     */
    public function generateActiveField($attribute, $fk, $tableSchema = null, $relations = null, $isTree = false)
    {
        if ($isTree){
            $model = "\$node";
        } else if (is_null($relations)){
            $model = "\$model";
        }else{
            $model = '$'.$relations[self::REL_CLASS];
        }

        if (is_null($tableSchema)) {
            $tableSchema = $this->getTableSchema();
        }
        if (in_array($attribute, $this->hiddenColumns)) {
            return "\$form->field($model, '$attribute', ['template' => '{input}'])->textInput(['style' => 'display:none']);";
        }

        $column = $tableSchema->columns[$attribute];
        $finalName = str_replace($this->fieldNameStrip, '', $attribute);
        $humanize = ucwords(Inflector::humanize($finalName));
        $placeholder = $humanize;

        if ($tableSchema === false || !isset($tableSchema->columns[$attribute])) {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $attribute)) {
                return "\$form->field($model, '$attribute')->passwordInput()";
            } else if (in_array($attribute, $this->hiddenColumns)) {
                return "\$form->field($model, '$attribute')->hiddenInput()";
            } else {
                return "\$form->field($model, '$attribute')";
            }
        }
        if ($column->phpType === 'boolean' || $column->dbType === 'tinyint(1)') {
            //return "\$form->field($model, '$attribute')->checkbox()";
            return "\$form->field($model, '$attribute')->widget(\\kartik\\widgets\\SwitchInput::className(), [])";
        } elseif ($column->type === 'text' || $column->dbType === 'tinytext') {
            return "\$form->field($model, '$attribute')->textarea(['rows' => 6])";
        } elseif ($column->dbType === 'date') {
            return "\$form->field($model, '$attribute')->widget(\\kartik\\datecontrol\\DateControl::classname(), [
        'type' => \\kartik\\datecontrol\\DateControl::FORMAT_DATE,
        'saveFormat' => 'php:Y-m-d',
        'ajaxConversion' => true,
        'options' => [
            'id' => 'field_$attribute',
            'pluginOptions' => [
                'placeholder' => '--INPUT--',
                'autoclose' => true
            ]
        ],
    ]);";
        } elseif ($column->dbType === 'time') {
            return "\$form->field($model, '$attribute')->widget(\\kartik\\datecontrol\\DateControl::className(), [
                'type' => \\kartik\\datecontrol\\DateControl::FORMAT_TIME,
                'saveFormat' => 'php:H:i:s',
                'ajaxConversion' => true,
                'options' => [
                    'id' => 'field_$attribute',
                    'pluginOptions' => [
                        'placeholder' => '--INPUT--',
                        'autoclose' => true
                    ]
                ]
            ]);";
        } elseif ($column->dbType === 'datetime') {
            return "\$form->field($model, '$attribute')->widget(\\kartik\\datecontrol\\DateControl::classname(), [
        'type' => \\kartik\\datecontrol\\DateControl::FORMAT_DATETIME,
        'saveFormat' => 'php:Y-m-d H:i:s',
        'ajaxConversion' => true,
        'options' => [
            'id' => 'field_$attribute',
            'pluginOptions' => [
                'placeholder' => '--INPUT--',
                'autoclose' => true,
            ]
        ],
    ]);";
        } elseif (array_key_exists($column->name, $fk)) {
            $rel = $fk[$column->name];

            $finalName = str_replace($this->fieldNameStrip, '', $rel[Generator::REL_TABLE]);
            $humanize = ucwords(Inflector::humanize($finalName));

            $labelCol = $this->getNameAttributeFK($rel[Generator::REL_TABLE]);
            $finalName = str_replace($this->fieldNameStrip, '', $rel[Generator::REL_TABLE]);
            $humanize = ucwords(Inflector::humanize($finalName));

//            $pk = empty($this->tableSchema->primaryKey) ? $this->tableSchema->getColumnNames()[0] : $this->tableSchema->primaryKey[0];
            $fkClassFQ = "\\" . $this->nsModel . "\\" . $rel[1];

            // xx - if the webUserColName is not specified on command-line, or if it doesn't exist for this model (may not be relevant)
            // then just return everything
            $fkClass = $rel[1];
            $fkTableName = $rel[Generator::REL_TABLE];
            $db = $this->getDbConnection();
            $fkTableSchema = $db->getTableSchema($fkTableName);
            $hasWebUserColColumn = array_key_exists($this->webUserColName, $fkTableSchema->columns);

            if (! isset($this->webUserColName) || ! $hasWebUserColColumn) {
                $webUserColNameText = "";
            }
            else {
                $webUserColNameText = "andWhere(['" . $this->webUserColName . "'=>Yii::\$app->user->id])->";
            }

            $tweakFilter = null;
            if (array_key_exists($fkClass, $this->tweakRules)) {
                $classRule = $this->tweakRules[$fkClass];
                if (array_key_exists($attribute, $classRule)) {
                    $tweakFilter = $classRule[$attribute][0] . '->';
                }
            }
            else {
                $tweakFilter = "orderBy('$rel[4]')->";
            }

            if (array_key_exists($attribute, $this->editFormDepFieldList)) {
                //$parentAttrName = $this->editFormDepFieldList[$attribute][0];
                
                
                $childField = $attribute;

                $fromDepFieldDef = $this->editFormDepFieldList[$attribute];
                $defExplode = explode('#', $fromDepFieldDef[0]);
                $parentField = $defExplode[0];
                $parentQuery = $defExplode[1];
                
                $childFieldFinalName = sprintf("\$%s_list", $childField);
                $fieldParentFieldID = "field_$parentField";
                
                $controllerActionName = \yii\helpers\Inflector::camel2id($attribute) . "-list";
                
                $output = "\$form->field($model, '$attribute')->widget(\\kartik\\widgets\\DepDrop::classname(), [
                    'data'=> $childFieldFinalName,
                    'type' => \kartik\depdrop\DepDrop::TYPE_SELECT2,
                    'options' => [
                        'placeholder' => '--INPUT--',
                        'id' => 'field_$attribute',
                    ],
                    'pluginOptions' => [
                        'select2Options' => ['pluginOptions' => ['allowClear' => true]],
                        'placeholder'=>'Select...',
                        'depends' => [ '$fieldParentFieldID' ],
                        'url'=>\\yii\\helpers\\Url::to(['$controllerActionName']),
                    ],
                ]);";
            }
            else {
                $output = "\$form->field($model, '$attribute')->widget(\\kartik\\widgets\\Select2::classname(), [
                    'data' => \\yii\\helpers\\ArrayHelper::map($fkClassFQ::find()->". $webUserColNameText . $tweakFilter . "asArray()->all(), '$rel[4]', '$labelCol'),
                    'options' => [
                        'placeholder' => '--INPUT--',
                        'id' => 'field_$attribute',
                    ],
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]);";
            }

            return $output;
        } else {
            if (preg_match('/^(password|pass|passwd|passcode)$/i', $column->name)) {
                $input = 'passwordInput';
            } else {
                $input = 'textInput';
            }
            if (is_array($column->enumValues) && count($column->enumValues) > 0) {
                $dropDownOptions = [];
                foreach ($column->enumValues as $enumValue) {
                    $dropDownOptions[$enumValue] = Inflector::humanize($enumValue);
                }
                return "\$form->field($model, '$attribute')->dropDownList("
                . preg_replace("/\n\s*/", ' ', VarDumper::export($dropDownOptions)) . ", ['prompt' => ''])";
            } elseif ($column->phpType !== 'string' || $column->size === null) {
                // dp
                // return "\$form->field($model, '$attribute')->$input(['placeholder' => '$placeholder'])";
                return "\$form->field($model, '$attribute')->$input(['placeholder' => '--INPUT--'])";
            } else {
                // dp
                // return "\$form->field($model, '$attribute')->$input(['maxlength' => true, 'placeholder' => '$placeholder'])";
                return "\$form->field($model, '$attribute')->$input(['maxlength' => true, 'placeholder' => '--INPUT--'])";
            }
        }
    }

    /**
     * Generates column format
     * @param ColumnSchema $column
     * @return string
     */
    public function generateColumnFormat($column)
    {
        if ($column->type === 'boolean' || $column->type === 'tinyint') {
            return 'boolean';
        } elseif ($column->type === 'text') {
            return 'ntext';
        } elseif (stripos($column->name, 'time') !== false && $column->phpType === 'integer') {
            return 'datetime';
        } elseif ($column->type === 'datetime') {
            return 'datetime';
        } elseif ($column->type === 'date') {
            return 'date';
        } elseif ($column->type === 'time') {
            return 'time';
        } elseif (stripos($column->name, 'email') !== false) {
            return 'email';
        } elseif (stripos($column->name, 'url') !== false) {
            return 'url';
        } else {
            return 'text';
        }
    }

    /**
     * Generates URL parameters
     * @return string
     */
    public function generateUrlParams()
    {
        $pks = $this->tableSchema->primaryKey;
        if (count($pks) === 1) {
            if (is_subclass_of($this->modelClass, 'yii\mongodb\ActiveRecord')) {
                return "'id' => (string)\$model->{$pks[0]}";
            } else {
                return "'id' => \$model->{$pks[0]}";
            }
        } else {
            $params = [];
            foreach ($pks as $pk) {
                if (is_subclass_of($this->modelClass, 'yii\mongodb\ActiveRecord')) {
                    $params[] = "'$pk' => (string)\$model->$pk";
                } else {
                    $params[] = "'$pk' => \$model->$pk";
                }
            }

            return implode(', ', $params);
        }
    }

    /**
     * Generates action parameters
     * @return string
     */
    public function generateActionParams()
    {
        $pks = $this->tableSchema->primaryKey;
        if (count($pks) === 1) {
            return '$id';
        } else {
            return '$' . implode(', $', $pks);
        }
    }

    /**
     * Generates parameter tags for phpdoc
     * @return array parameter tags for phpdoc
     */
    public function generateActionParamComments()
    {
        /* @var $class ActiveRecord */
        $pks = $this->tableSchema->primaryKey;
        if (($table = $this->getTableSchema()) === false) {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . (substr(strtolower($pk), -2) == 'id' ? 'integer' : 'string') . ' $' . $pk;
            }

            return $params;
        }
        if (count($pks) === 1) {
            return ['@param ' . $table->columns[$pks[0]]->phpType . ' $id'];
        } else {
            $params = [];
            foreach ($pks as $pk) {
                $params[] = '@param ' . $table->columns[$pk]->phpType . ' $' . $pk;
            }

            return $params;
        }
    }

    /**
     * Generates validation rules for the search model.
     * @return array the generated validation rules
     */
    public function generateSearchRules() {
        if (($table = $this->getTableSchema()) === false) {
            return ["[['" . implode("', '", $this->getColumnNames()) . "'], 'safe']"];
        }
        $types = [];
        foreach ($table->columns as $column) {
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                default:
                    $types['safe'][] = $column->name;
                    break;
            }
        }

        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
        }

        return $rules;
    }

    /**
     * Generates the attribute labels for the search model.
     * @return array the generated attribute labels (name => label)
     */
    public function generateSearchLabels() {
        /* @var $model Model */
        $model = new $this->modelClass();
        $attributeLabels = $model->attributeLabels();
        $labels = [];
        foreach ($this->getColumnNames() as $name) {
            if (isset($attributeLabels[$name])) {
                $labels[$name] = $attributeLabels[$name];
            } else {
                if (!strcasecmp($name, 'id')) {
                    $labels[$name] = 'ID';
                } else {
                    $label = Inflector::camel2words($name);
                    if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                        $label = substr($label, 0, -3) . ' ID';
                    }
                    $labels[$name] = $label;
                }
            }
        }

        return $labels;
    }

    /**
     * @return array searchable attributes
     */
    public function getSearchAttributes() {
        return $this->getColumnNames();
    }

    /**
     * Generates search conditions
     * @return array
     */
    public function generateSearchConditions() {
        $columns = [];
        if (($table = $this->getTableSchema()) === false) {
            $class = $this->modelClass;
            /* @var $model Model */
            $model = new $class();
            foreach ($model->attributes() as $attribute) {
                $columns[$attribute] = 'unknown';
            }
        } else {
            foreach ($table->columns as $column) {
                $columns[$column->name] = $column->type;
            }
        }

        $likeConditions = [];
        $hashConditions = [];
        foreach ($columns as $column => $type) {
            switch ($type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $hashConditions[] = "'{$column}' => \$this->{$column},";
                    break;
                default:
                    $likeConditions[] = "->andFilterWhere(['like', '{$column}', \$this->{$column}])";
                    break;
            }
        }

        $conditions = [];
        if (!empty($hashConditions)) {
            $conditions[] = "\$query->andFilterWhere([\n"
                . str_repeat(' ', 12) . implode("\n" . str_repeat(' ', 12), $hashConditions)
                . "\n" . str_repeat(' ', 8) . "]);\n";
        }
        if (!empty($likeConditions)) {
            $conditions[] = "\$query" . implode("\n" . str_repeat(' ', 12), $likeConditions) . ";\n";
        }

        return $conditions;
    }

}

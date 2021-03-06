<?php
/**
 * This is the template for generating the model class of a specified table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 */
?>
<?php echo "<?php\n"; ?>

class <?php echo $modelClass; ?> extends <?php echo $this->baseClass."\n"; ?>
{

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
<?php if($connectionId!='db'):?>

	public function getDbConnection()
	{
		return Yii::app()-><?php echo $connectionId ?>;
	}
<?php endif?>

	public function tableName()
	{
		return '<?php echo $tableName; ?>';
	}

	public static function modelTitle() {
		return '<?php echo $modelTitle; ?>';
	}

	public function rules()
	{
		return array(
<?php foreach($rules as $rule): ?>
			<?php echo $rule.",\n"; ?>
<?php endforeach; ?>
			array('<?php echo implode(', ', array_keys($columns)); ?>', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
<?php foreach($relations as $name=>$relation): ?>
			<?php echo "'$name' => $relation,\n"; ?>
<?php endforeach; ?>
		);
	}

	public function attributeLabels()
	{
		return array(
<?php foreach($labels as $name=>$label): ?>
			<?php echo "'$name' => '$label',\n"; ?>
<?php endforeach; ?>
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

<?php
foreach($columns as $name=>$column){
	if($column->type==='string')
	{
		echo "\t\t\$criteria->compare('$name',\$this->$name,true);\n";
	}
	else
	{
		echo "\t\t\$criteria->compare('$name',\$this->$name);\n";
	}
}
?>

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

<?php
foreach($columns as $name=>$column):
$langPos = strpos($name, '_ru');
if($langPos!==FALSE):
$word = substr($name, 0, $langPos);
$modifiedName = mb_strtoupper( mb_substr( $word, 0, 1, 'UTF-8' ), 'UTF-8' ).mb_strtolower( mb_substr( $word, 1, mb_strlen( $word, 'UTF-8' ), 'UTF-8' ), 'UTF-8' );
?>
	public function get<?=$modifiedName?>(){
		$lang=Yii::app()->language;
		$f='<?=$word?>_'.$lang;
		return $this->$f;
	}

<?
endif;
endforeach;
?>

	public function options(){
		return array(

		);
	}

}
<?php

class NewsController extends AdminController
{
	public $targetModel = 'News';

	public function getTableAttributes(){
		return array(
			'parent_NewsCategory_id', 'title_ru', 'is_visible', 		);
	}
	public function getDefaultTableAttributes(){
		return array();
	}

	public function actions()
	{
		return array(
			'toggle' => array(
				'class'=>'bootstrap.actions.TbToggleAction',
				'modelName' => $this->targetModel,
			)
		);
	}

	public $defaultAction = "list";

	public function actionCreate()
	{
		$model=new $this->targetModel;

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->targetModel])){
			$model->attributes=$_POST[$this->targetModel];
			if($model->save()){
			 	Yii::app()->user->setFlash('success', 'Сохранено');
				$this->redirect(array('list'));
			}else{
				 Yii::app()->user->setFlash('error', 'Ошибка при сохранении!');
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);

		$this->performAjaxValidation($model);

		if(isset($_POST[$this->targetModel])){
			$model->attributes=$_POST[$this->targetModel];
			if($model->save()){
			 	Yii::app()->user->setFlash('success', 'Сохранено');
				$this->redirect(array('list'));
			}else{
				 Yii::app()->user->setFlash('error', 'Ошибка при сохранении!');
			}
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionDelete($id)
	{
		if(Yii::app()->request->isPostRequest){
			// we only allow deletion via POST request
			$this->loadModel($id)->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}else{
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
	}

	public function actionIndex()
	{
		$this->redirect("list");
	}

	public function actionList()
	{
		$model=new $this->targetModel('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET[$this->targetModel]))
			$model->attributes=$_GET[$this->targetModel];
		if(Yii::app()->request->isAjaxRequest){
			$this->renderPartial('_list',array(
				'model'=>$model,
			),false,true);
		}else{
			$this->render('list',array(
				'model'=>$model,
			));
		}
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id)
	{
		$model=CActiveRecord::model($this->targetModel)->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']===$this->targetModel.'-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}

	public function actionEditable(){
		if(Yii::app()->request->isAjaxRequest){
			if($pk = Yii::app()->request->getPost("pk")){
				$model = $this->loadModel($pk);
				if($model){
					if($name = Yii::app()->request->getPost("name")){
						$value = Yii::app()->request->getPost("value");
						$model->{$name} = $value;
						$model->save();

					}
				}
			}
		}
	}

	public function actionTableSetup(){
		if($data = Yii::app()->request->getPost("Table")){
			Yii::app()->user->setState('newsTable',$data);
			echo "1";
		}
	}
}

<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/21
 * Time: 16:44
 */

namespace app\modules\controllers;

use app\modules\models\Category;
use yii\web\Controller;

class CategoryController extends Controller
{
    public function actionList()
    {
        $this->layout = 'main';
        $model = new Category();
        return $this->render('cates',['model'=>$model]);
    }

    public function actionAdd()
    {
        $this->layout = 'main';
        $model = new Category();
        $list = ['请选择'];
        if(\Yii::$app->request->isPost){
            $post = \Yii::$app->request->post();
            if($model->add($post)){
                \Yii::$app->session->setFlash('info','添加成功');
            }
        }
        return $this->render('add',['model'=>$model,'list'=>$list]);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/18
 * Time: 14:16
 */

namespace app\modules\controllers;


use app\modules\models\Admin;
use yii\web\Controller;
use Yii;

class PublicController extends Controller
{
    public function actionLogin()
    {
        if(isset(Yii::$app->session['admin']['isLogin'])){
            $this->redirect(['default/index']);
            Yii::$app->end();
        }
        $this->layout = false;
        $model = new Admin();
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if($model->login($post)){
                $this->redirect(['default/index']);
                Yii::$app->end();
            }
        }
        return $this->render('login',['model'=>$model]);
    }

    public function actionLogout()
    {
        Yii::$app->session->removeAll();
        if(!isset(Yii::$app->session['admin']['isLogin'])){
            $this->redirect(['public/login']);
            Yii::$app->end();
        }
        $this->goBack();
    }

    public function actionSeekpassword()
    {
        $this->layout = false;
        $model = new Admin();
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if($model->seekPass($post)){
                $this->redirect(['public/login']);
                Yii::$app->end();
            }
        }
        
        return $this->render('seekpassword',['model'=>$model]);
    }
}
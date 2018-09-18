<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/18
 * Time: 17:57
 */

namespace app\modules\controllers;
use app\modules\models\Admin;
use Yii;

class ManageController
{
    public function actionMailChangepass()
    {
        $time = Yii::$app->request->get('timestamp');
        $adminuser = Yii::$app->request->get('adminuser');
        $token = Yii::$app->request->get('token');
        $model = new Admin();
        $myToken = $model->createToken($adminuser,$time);
        if(($token != $myToken) || (time()-$time>300)){
            $this->redirect(['public/login']);
            Yii::$app->end();
        }
        return $this->render('mailchangepass',['model'=>$model]);
    }
}
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
use yii\web\Controller;

class ManageController extends Controller
{
    public function actionMailchangepass()
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
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if($model->changePass($post)){
                Yii::$app->session->setFlash('info','密码修改成功');
            }
        }
        $this->layout = false;
        $model->adminuser = $adminuser;
        $model->adminpass = '';
        $model->repass = '';
        return $this->render('mailchangepass',['model'=>$model]);
    }

    public function actionManages()
    {
        $this->layout = 'main';
        $model = new Admin();
        $manages = $model->getList();
        return $this->render('manages',$manages);
    }

    public function actionReg()
    {
        $this->layout = 'main';
        $model = new Admin();
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $result = $model->reg($post);
            Yii::$app->session->setFlash('info',$result?'添加成功':'添加失败');
        }
        $model->adminpass = '';
        $model->repass = '';
        return $this->render('reg',['model'=>$model]);
    }

    public function actionDel()
    {
        $adminid = (int)Yii::$app->request->get('adminid');
        if(!$adminid){
            $this->redirect(['manage/manages']);
            Yii::$app->end();
        }
        if(Admin::deleteAll('adminid = :id',[':id'=>$adminid])){
            Yii::$app->session->setFlash('info','删除成功!');
            $this->redirect(['manage/manages']);
            Yii::$app->end();
        }
    }

    public function actionChangemail()
    {
        $this->layout = 'main';
        $model = Admin::find() -> where('adminuser = :user',[':user' => Yii::$app->session['admin']['adminuser']])->one();
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if($model->changemail($post)){
                Yii::$app->session->setFlash('info','修改邮箱地址成功！');
            }
        }
        $model -> adminemail = '';
        $model -> adminpass = '';
        return $this->render('changemail',['model'=>$model]);
    }

    public function actionChangepass()
    {
        $this->layout = 'main';
        $model = Admin::find()->where('adminuser = :user',[':user'=>Yii::$app->session['admin']['adminuser']])->one();
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if($model->changePass($post)){
                Yii::$app->session->setFlash('info','修改密码成功');
            }
        }
        $model ->adminpass = '';
        $model ->repass = '';
        return $this->render('changepass',['model'=>$model]);
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/14
 * Time: 15:28
 */

namespace app\controllers;


use app\models\User;
use yii\web\Controller;
use Yii;

class MemberController extends Controller
{
    public $layout = false;

    public function actionAuth()
    {
        $this->layout = 'layout_2';
        $model = new User();
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if($model->mailLogin($post)){
                return $this->redirect(['index/index']);
            }
        }
        $model->userpass = '';
        return $this->render('auth',['model'=>$model]);
    }

    public function actionReg()
    {
        $this->layout = 'layout_2';
        $model = new User();
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            if($model->regByMail($post)){
                Yii::$app->session->setFlash('info_reg','邮件发送成功！');
                return $this->redirect(['member/auth']);
            }
        }
        return $this->render('auth',['model'=>$model]);
    }

    public function actionQqlogin()
    {
        require_once ('../vendor/qqlogin/API/qqConnectAPI.php');
        $qc = new \QC();
        $qc->qq_login();
    }

    public function actionQqcallback()
    {
        require_once ('../vendor/qqlogin/API/qqConnectAPI.php');
        $auth = new \Oauth();
        $accessToken = $auth->qq_callback();
        $openId = $auth->get_openid();
        $qc = new \QC($accessToken,$openId);
        $user = $qc->get_user_info();
        $session = Yii::$app->session;
        $session['userinfo'] = $user;
        if(User::find()->where('openid = :openid',[':openid'=>$openId])->one()){
            $session['loginname'] = $user['nickname'];
            $session['isLogin'] = 1;
            return $this->redirect(['index/index']);
        }else{
            return $this->redirect(['member/qqreg']); 
        }
    }

    public function actionQqreg()
    {
        $this->layout = 'layout_2';
        $model = new User();
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $session = Yii::$app->session;
            $post['User']['openid'] = $session['userinfo']['openid'];
            if($model->reg($post,'qqreg')){
                $session['loginname'] = $session['userinfo']['nickname'];
                $session['isLogin'] = 1;
                return $this->redirect(['index/index']);
            }
        }
        return $this->render('qqreg',['model'=>$model]);
    }
}
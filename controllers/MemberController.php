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
                $this->redirect(['index/index']);
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
                $this->redirect(['member/auth']);
                Yii::$app->end();
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
        $user = $qc->get_user_info();var_dump($user);

    }
}
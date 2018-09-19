<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/18
 * Time: 17:57
 */

namespace app\modules\controllers;
use app\modules\models\Admin;
use app\modules\models\User;
use Yii;
use yii\db\Exception;
use yii\web\Controller;

class UserController extends Controller
{

    public function actionUsers()
    {
        $this->layout = 'main';
        $model = new User();
        $users = $model->getList();
        return $this->render('users',$users);
    }

    public function actionReg()
    {
        $this->layout = 'main';
        $model = new User();
        if(Yii::$app->request->isPost){
            $post = Yii::$app->request->post();
            $result = $model->reg($post);
            Yii::$app->session->setFlash('info',$result?'添加成功':'添加失败');
        }
        $model->userpass = '';
        $model->repass = '';
        return $this->render('reg',['model'=>$model]);
    }

    public function actionDel()
    {
        $userid = (int)Yii::$app->request->get('userid');
        if(!$userid){
            throw new Exception();
        }
        
        if((new User())->del($userid)===true){
            Yii::$app->session->setFlash('info','删除成功');
            $this->redirect(['user/users']);
            Yii::$app->end();
        }
    }


}
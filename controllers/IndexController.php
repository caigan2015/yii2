<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/14
 * Time: 11:33
 */
namespace app\controllers;

use app\models\Test;
use yii\web\Controller;

class IndexController extends Controller
{
    public function actionIndex()
    {
        $this->layout = 'layout_1';
        return $this->render('index');
    }
}
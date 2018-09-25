<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/9/14
 * Time: 14:45
 */

namespace app\controllers;


use app\modules\models\Product;
use yii\web\Controller;

class ProductController extends Controller
{
    public $layout = false;
    public function actionIndex()
    {
        $this->layout = 'layout_2';
        return $this->render('index');
    }

    public function actionDetail()
    {
        $this->layout = 'layout_2';

        return $this->render('detail');
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: caigan
 * Date: 2018-09-22
 * Time: 14:23
 */

namespace app\modules\controllers;


use app\modules\models\Category;
use app\modules\models\Product;
use crazyfd\qiniu\Qiniu;
use yii\data\Pagination;
use yii\web\Controller;

class ProductController extends Controller
{
    public $enableCsrfValidation=false;
    public function actionList()
    {
        $this->layout = 'main';
        $model = Product::find();
        $count = $model->count();
        $pageSize = \Yii::$app->params['pageSize']['product'];
        $pager = new Pagination(
            ['totalCount'=>$count,'pageSize'=>$pageSize]
        );
        $products = $model->offset($pager->offset)->limit($pager->limit)->all();
        return $this->render('products',['products'=>$products,'pager'=>$pager]);
    }

    public function actionAdd()
    {
        $this->layout = 'main';
        $model = new Product();
        if(\Yii::$app->request->isPost){
            $post = \Yii::$app->request->post();
            $pics = $this->upload();
            if(!$pics){
                $model->addError('cover','封面不能为空');
            }else{
                $post['Product']['cover'] = $pics['cover'];
                $post['Product']['pics'] = $pics['pics'];
                if($model->add($post)){
                    \Yii::$app->session->setFlash('info','添加成功');
                }
            }
        }

        $list = (new Category())->getOptions();
        unset($list[0]);
        return $this->render('add',['model'=>$model,'opts'=>$list]);
    }

    public function actionMod()
    {
        $productid = \Yii::$app->request->get('productid');
        $model = Product::find()->where('productid = :id',[':id'=>$productid])->one();
        if(\Yii::$app->request->isPost){
            $post = \Yii::$app->request->post();
            $qiniu = new Qiniu(Product::AK,Product::SK,Product::DOMAIN,Product::BUCKET);
            $post['Product']['cover'] = $model->cover;
            if($_FILES['Product']['error']['cover']==0){
                $key = uniqid();
                $qiniu->uploadFile($_FILES['Product']['tmp_name']['cover'],$key);
                $post['Product']['cover'] = $qiniu->getLink($key);
                $qiniu->delete(basename($model->cover));
            }

            $pics = [];
            foreach ($_FILES['Product']['tmp_name']['pics'] as $k => $file) {
                if($_FILES['Product']['error']['pics'][$k] >0 ){continue;}
                $key = uniqid();
                $qiniu->uploadFile($file,$key);
                $pics[$key] = $qiniu->getLink($key);
            }
            $post['Product']['pics'] = json_encode(array_merge(json_decode($model->pics),true),$pics);
            if($model->load($post) && $model->validate()){
                \Yii::$app->session->setFlash('info','修改成功！');
            }
        }
        $this->layout = 'main';
        $cates = (new Category())->getOptions();
        array_shift($cates);
        $this->render('add',['model',$model,'opts'=>$cates]);
    }
    private function upload()
    {
        if($_FILES['Product']['error']['cover'] > 0){
            return false;
        }
        $qiniu = new Qiniu(Product::AK,Product::SK,Product::DOMAIN,Product::BUCKET);
        $key = uniqid();
        $qiniu->uploadFile($_FILES['Product']['tmp_name']['cover'],$key);
        $cover = $qiniu->getLink($key);
        $pics = [];
        foreach ($_FILES['Product']['tmp_name']['pics'] as $k=> $file) {
            if($_FILES['Product']['error']['pics'][$k] >0 ){
                continue;
            }
            $key = uniqid();
            $qiniu->uploadFile($file,$key);
            $pics[$key] = $qiniu->getLink($key);

        }

        return ['cover'=>$cover,'pics'=>json_encode($pics)];
    }

    public function actionRemovePics()
    {
        $key = \Yii::$app->request->get('key');
        $productid = \Yii::$app->request->get('productid');
        $model = Product::find()->where('productid = :id',[':id'=>$productid])->one();
        $qiniu = new Qiniu(Product::AK,Product::SK,Product::DOMAIN,Product::BUCKET);
        $qiniu->delete($key);
        $pics = json_decode($model->pics,true);
        unset($pics[$key]);
        Product::updateAll(['pics'=>json_encode($pics)],'productid = :id',[':id'=>$productid]);
        return $this->redirect(['product/mod','productid'=>$productid]);
    }

    public function actionDel()
    {
        $productid = \Yii::$app->request->get('productid');
        $model = Product::find()->where('productid = :id',[':id'=>$productid])->one();
        $qiniu = new Qiniu(Product::AK,Product::SK,Product::DOMAIN,Product::BUCKET);
        if($model->cover){
            $key = basename($model->cover);
            $qiniu->delete($key);
        }
        if($model->pics){
            $pics = json_decode($model->pics,true);
            foreach ( $pics as $k => $pic) {
                $qiniu->delete($k);
            }
        }

        Product::deleteAll('productid = :id',[':id'=>$productid]);
        return $this->redirect(['product/list']);
    }

    public function actionOn()
    {
        $productid = \Yii::$app->request->get('productid');
        Product::updateAll(['ison'=>1],'productid = :id',[':id'=>$productid]);
        return $this->redirect(['product/list']);
    }
    public function actionOff()
    {
        $productid = \Yii::$app->request->get('productid');
        Product::updateAll(['ison'=>0],'productid = :id',[':id'=>$productid]);
        return $this->redirect(['product/list']);
    }
}
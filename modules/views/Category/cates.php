<!-- main container -->
<div class="content">

    <div class="container-fluid">
        <div id="pad-wrapper" class="users-list">
            <div class="row-fluid header">
                <h3>Users</h3>
                <div class="span10 pull-right">
                    <input type="text" class="span5 search" placeholder="Type a user's name..." />

                    <!-- custom popup filter -->
                    <!-- styles are located in css/elements.css -->
                    <!-- script that enables this dropdown is located in js/theme.js -->
                    <div class="ui-dropdown">
                        <div class="head" data-toggle="tooltip" title="Click me!">
                            Filter users
                            <i class="arrow-down"></i>
                        </div>
                        <div class="dialog">
                            <div class="pointer">
                                <div class="arrow"></div>
                                <div class="arrow_border"></div>
                            </div>
                            <div class="body">
                                <p class="title">
                                    Show users where:
                                </p>
                                <div class="form">
                                    <select>
                                        <option />Name
                                        <option />Email
                                        <option />Number of orders
                                        <option />Signed up
                                        <option />Last seen
                                    </select>
                                    <select>
                                        <option />is equal to
                                        <option />is not equal to
                                        <option />is greater than
                                        <option />starts with
                                        <option />contains
                                    </select>
                                    <input type="text" />
                                    <a class="btn-flat small">Add filter</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="<?php echo \yii\helpers\Url::to(['category/add']); ?>" class="btn-flat success pull-right">
                        <span>&#43;</span>
                        NEW CATEGORY
                    </a>
                </div>
            </div>
            <!-- Users table -->
            <div class="row-fluid table">
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th class="span4 sortable">
                            分类ID
                        </th>
                        <th class="span3">
                            <span class="line"></span>分类标题
                        </th>
                        <th class="span2">
                            <span class="line"></span>上级分类
                        </th>
                        <th class="span3">
                            <span class="line"></span>添加时间
                        </th>
                        <th class="span3">
                            <span class="line"></span>操作
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($cates as $cate) { ?>
                        
                    <!-- row -->
                    <tr class="first">
                        <td>
                            <?php echo $cate['cateid'] ?>
                        </td>
                        <td>
                            <?php echo isset($cate['title'])?$cate['title']:'未填写'; ?>
                        </td>
                        <td>
                            <?php echo isset($cate['parentid'])?$cate['parentid']:'未填写'; ?>
                        </td>
                        <td>
                            <?php echo date('Y-m-d H:i',$cate['createtime']); ?>
                        </td>
                        <td class="align-right">
                            <a href="<?php echo \yii\helpers\Url::to(['category/mod','cateid'=>$cate['cateid']])?>">编辑</a>
                            <a href="<?php echo \yii\helpers\Url::to(['category/del','cateid'=>$cate['cateid']])?>">删除</a>
                        </td>
                    </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <?php
                    if(Yii::$app->session->hasFlash('info')){                
                        echo '<p>'.Yii::$app->session->getFlash('info').'</p>';

                    }
                ?>
            </div>

            <!-- end users table -->
        </div>
    </div>
</div>
<!-- end main container -->
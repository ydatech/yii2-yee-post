<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yeesoft\grid\GridView;
use yeesoft\post\models\Post;
use yeesoft\gridquicklinks\GridQuickLinks;
use yeesoft\usermanagement\components\GhostHtml;
use webvimark\extensions\GridPageSize\GridPageSize;

/* @var $this yii\web\View */
/* @var $searchModel yeesoft\post\models\search\PostSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title                   = 'Posts';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-index">

    <div class="row">
        <div class="col-sm-12">
            <h3 class="lte-hide-title page-title"><?= Html::encode($this->title) ?></h3>
            <?=
            GhostHtml::a('Add New', ['create'],
                ['class' => 'btn btn-sm btn-primary'])
            ?>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">

            <div class="row">
                <div class="col-sm-6">
                    <?=
                    GridQuickLinks::widget([
                        'model' => Post::class,
                        'searchModel' => $searchModel,
                        'labels' => [
                            'all' => 'All',
                            'active' => 'Published',
                            'inactive' => 'Pending',
                        ]
                    ])
                    ?>
                </div>

                <div class="col-sm-6 text-right">
                    <?= GridPageSize::widget(['pjaxId' => 'post-grid-pjax']) ?>
                </div>
            </div>

            <?php
            Pjax::begin([
                'id' => 'post-grid-pjax',
            ])
            ?>

            <?=
            GridView::widget([
                'id' => 'post-grid',
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'bulkActionOptions' => [
                    'gridId' => 'post-grid',
                    'actions' => [
                        Url::to(['bulk-activate']) => 'Publish',
                        Url::to(['bulk-deactivate']) => 'Unpublish',
                        Url::to(['bulk-delete']) => 'Delete',
                    ]
                ],
                'columns' => [
                    ['class' => 'yii\grid\CheckboxColumn', 'options' => ['style' => 'width:10px']],
                    [
                        'class' => 'yeesoft\grid\columns\TitleActionColumn',
                        'title' => function(Post $model) {
                        return Html::a($model->title,
                                Url::to('../'.$model->slug), ['data-pjax' => 0]);
                    },
                    ],
                    [
                        'attribute' => 'author_id',
                        'filter' => yeesoft\usermanagement\models\User::getUsersList(),
                        'filterInputOptions' => [],
                        'value' => function(Post $model) {
                        return Html::a($model->author->username,
                                ['user/view', 'id' => $model->author_id],
                                ['data-pjax' => 0]);
                    },
                        'format' => 'raw',
                        'options' => ['style' => 'width:180px'],
                    ],
                    [
                        'class' => 'yeesoft\grid\columns\StatusColumn',
                        'attribute' => 'status',
                        'optionsArray' => Post::getStatusOptionsList(),
                        'options' => ['style' => 'width:60px'],
                    ],
                    [
                        'class' => 'yeesoft\grid\columns\DateFilterColumn',
                        'attribute' => 'published_at',
                        'value' => function(Post $model) {
                        return '<span style="font-size:85%;" class="label label-'
                            .((time() >= $model->published_at) ? 'primary' : 'default').'">'
                            .$model->publishedDate.'</span>';
                    },
                        'format' => 'raw',
                        'options' => ['style' => 'width:150px'],
                    ],
                ],
            ]);
            ?>

            <?php Pjax::end() ?>
        </div>
    </div>
</div>



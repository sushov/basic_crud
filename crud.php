<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use common\components\Query;
use common\models\Menu;

class MenuController extends Controller {

    protected $critical = array('superadmin', 'admin');
    protected $casual   = array('superadmin', 'admin', 'operator');

    public function behaviors() 
    {
        return [];
    }

    public function actions() 
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function beforeAction($action) 
    {
        if ($action->id == 'error')
            $this->layout = 'error';
        
        if (!isset(Yii::$app->session['user_ZXEsrMwoAMv0xV2xGLy12UEpC4In0h'])
            || !in_array(Yii::$app->params['role_user'][Yii::$app->session['user_ZXEsrMwoAMv0xV2xGLy12UEpC4In0h']->role], $this->casual) ) {
            return $this->redirect(Yii::$app->request->baseUrl . '/site/logout');
        }
        return parent::beforeAction($action);
    }

    public function actionIndex() 
    {
        $data = Query::queryAll("SELECT * FROM `menu` ORDER BY display_order ASC");
        return $this->render('index', array('data' => $data));
    }

    public function actionAdd()
    { 
        $pages = Query::queryAll("SELECT slug, name FROM `page` ORDER BY created_at DESC");
        return $this->render('add', array('pages' => $pages));
    }

    public function actionCreate() 
    {
        if (isset($_POST) && $_POST != null) {

            if ($order_data = Query::queryOne("SELECT MAX(display_order) as max FROM menu")) {
                $order = $order_data['max'] + 1;
            }
            else {
                $order = 1;
            }

            $model = new Menu();
            $model->name                = $_POST['name'];
            $model->title               = $_POST['title'];
            $model->link                = $_POST['link'];
            $model->target              = $_POST['target'];
            $model->display_order       = $order;
            $model->is_active           = isset($_POST['is_active']) ? 1 : 0;
            $model->created_at          = date('Y-m-d H:i:s');
            $model->created_by          = Yii::$app->session['user_ZXEsrMwoAMv0xV2xGLy12UEpC4In0h']->id;
            $model->last_edited_at      = date('Y-m-d H:i:s');
            $model->last_edited_by      = Yii::$app->session['user_ZXEsrMwoAMv0xV2xGLy12UEpC4In0h']->id;
            if ($model->save()) {
                Yii::$app->session->setFlash('info-bar', "New Menu has been created.");
            }
            else {
                Yii::$app->session->setFlash('danger-bar', "Menu was not created. Please try again later.");
            }

            $_POST = array();
            unset($_POST);
            return $this->redirect(Yii::$app->request->baseUrl . '/menu');
        }
        throw new HttpException(404, 'Page not found.');
    }

    public function actionEdit($slug)
    { 
        if ($data = Menu::find()->where(['slug' => $slug])->one()) {
            $pages = Query::queryAll("SELECT slug, name FROM `page` WHERE is_active = 1 ORDER BY created_at DESC");
            return $this->render('edit', array('data' => $data, 'pages' => $pages));
        }
        throw new HttpException(404, 'Page not found.');
    }

    public function actionUpdate() 
    {
        if (isset($_POST) && $_POST != null) {
            $model = Menu::findOne($_POST['id']);
            if ($model) {
                $model->title               = $_POST['title'];
                $model->link                = $_POST['link'];
                $model->target              = $_POST['target'];
                $model->is_active           = isset($_POST['is_active']) ? 1 : 0;
                $model->last_edited_at      = date('Y-m-d H:i:s');
                $model->last_edited_by      = Yii::$app->session['user_ZXEsrMwoAMv0xV2xGLy12UEpC4In0h']->id;
                if ($model->update()) {
                    Yii::$app->session->setFlash('info-bar', "Menu has been updated.");
                }
                else {
                    Yii::$app->session->setFlash('danger-bar', "Menu was not updated. Please try again later.");
                }
            }
            else {
                Yii::$app->session->setFlash('danger-bar', "Menu was not updated. Please try again later.");
            }

            $_POST = array();
            unset($_POST);
            return $this->redirect(Yii::$app->request->baseUrl . '/menu');
        }
        throw new HttpException(404, 'Page not found.');
    }

    public function actionDelete() 
    {
        if (Yii::$app->request->isAjax) {
            $model = Menu::findOne($_POST['id']);
            if ($model && $model->delete()) {
                echo json_encode(TRUE); die;
            } 
            echo json_encode(FALSE); die;
        }
        throw new HttpException(404, 'Page not found.');
    }

    public function actionChangeStatus() 
    {
        if (Yii::$app->request->isAjax) {
            $model = Menu::findOne($_POST['id']);
            $model->is_active = ($model->is_active == 0) ? 1 : 0;
            if ($model->update()) {
                echo json_encode(TRUE); die;
            }
            echo json_encode(FALSE); die;
        }
        throw new HttpException(404, 'Page not found.');
    }

    public function actionCheckMenu()
    {
        if (Yii::$app->request->isAjax) {
            if (isset($_POST['name']) && $_POST['name'] != '') {
                $data = Query::queryOneSecure("SELECT id FROM `menu` WHERE `name` = :value ", [':value' => $_POST['name']]);
                if (!$data) {
                    echo json_encode(TRUE); die;
                }
            }
            echo json_encode(FALSE); die;
        }
        throw new HttpException(404, 'Page not found.');
    }

    public function actionSort() 
    {
        $data = Query::queryAll("SELECT id, name, title FROM `menu` ORDER BY display_order ASC");
        return $this->render('sort', array('data' => $data));
    }

    public function actionManageOrder()
    {
        if (Yii::$app->request->isAjax) {
            $data = $_GET['menu']; $i=1;
            foreach($data as $id) {
                $item = Menu::findOne($id);
                if ($item) {
                    $item->display_order=$i;
                    $item->update();
                }
                $i++;
            }
            echo json_encode(TRUE); die;
        }
        throw new HttpException(404, 'Page not found.');
    }
}

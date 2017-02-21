<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use linslin\yii2\curl\curl;

class SiteController extends Controller
{
    public $defaultAction = 'chart';
    public $filteredData;
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }


    public function actionData()
    {
	    $curl = new Curl;
        $url = 'http://127.0.0.1:8085/data.json';
        $response = $curl->get($url);
        $response = \yii\helpers\Json::decode($response);
        echo $response;
    }

    public function recursiveFind($array, $needle)
    {
        $results = array();
        $iterator  = new \RecursiveArrayIterator($array);
        $recursive = new \RecursiveIteratorIterator(
            $iterator,
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($recursive as $key => $value) {
            if (isset($value["Text"]) && $value["Text"]  == $needle) {
                $results[] = $recursive->current();
            }
        }
        return $results;
    }
    
    public function removeSuffix(&$item, &$key, $suffix)
    {
        $item = stristr($item, $suffix) ? floatval($item) : $item;
    }
    
    
    public function actionTemps()
    {
        $time = time() * 1000;
        $curl = new Curl;
        $url = 'http://127.0.0.1:8085/data.json';
        $data = $curl->get($url);
        $json = \yii\helpers\Json::decode($data);
        array_walk_recursive($json, 'self::removeSuffix', '°C');
        
        //getting sensors
        $temperatures = $this->recursiveFind($json, 'Temperatures');
        $cpu = $this->recursiveFind($temperatures, 'CPU Package');
        $gpu = $this->recursiveFind($temperatures, 'GPU Core');

        //another example of how to get sensors
        /* echo $json["Children"][0]["Children"][1]["Children"][1]["Children"][8]["Value"]; */
        
        $ret = array("time"=>$time, "cpu"=>$cpu[0], "gpu1"=>$gpu[0], "gpu2"=>$gpu[1]);
        echo json_encode($ret);
    }        
    
        
    public function actionChart()
    {
        $this->layout = 'chart';
        return $this->render('chart');
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}

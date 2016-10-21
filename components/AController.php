<?php

namespace app\components;

use Yii;
use yii\web\Controller;

use app\models\AuthUrls;
use app\models\AuthUrlsAssignment;

/**
 * PostController implements the CRUD actions for Post model.
 */
class AController extends Controller
{
    //@todo review this, possibly not the best solution, rather something inspired from Yii1
    public $registerJs = [
        'jquery'=>'/web/js/jquery-2.1.4.min.js',
        //'jquery-migrate'=>'/web/js/jquery-migrate-1.2.1.min.js',
        'jquery-ui'=>'/web/js/jquery-ui.min.js',
        'bootstrap'=>'/web/js/bootstrap.min.js',

        'excanvas'=>'/web/js/excanvas.min.js',
        'jquery-flot'=>'/web/js/jquery.flot.min.js',
        'jquery-flot-resize'=>'/web/js/jquery.flot.resize.min.js',
        'jquery-sparkline'=>'/web/js/jquery.sparkline.min.js',
        'jquery-fullcanedar'=>'/web/js/fullcalendar.min.js',
        'jquery-nicescroll'=>'/web/js/jquery.nicescroll.js',
        //'jquery-blockUI'=>'/web/js/jquery.blockUI.js',
        'bootbox'=>'/web/js/bootbox.js',
        'unicorn'=>'/web/js/unicorn.js',

        'custom'=>'/web/js/custom.js'
    ];

    public function behaviors()
    {
        $controller = Yii::$app->controller->id;
        $auth = new Yii::$app->authManager;
        $roles = $auth->getRoles();
        $permissions = $auth->getPermissions();

        $rules = [];

        foreach($roles as $role){
            if ($role->name == Yii::$app->params['defaultRole'])
            {
                $default_rule = [];
                $default_rule['allow'] = true;
                $default_rule['actions'] = [];
                $default_rule['roles'] = [Yii::$app->params['defaultRole']];
                $rules[] = $default_rule;
            }
            else
            {
                $auth_url_ids = AuthUrlsAssignment::find()->where(['role' => $role->name])->all();

                if (!empty($auth_url_ids))
                {
                    $rule = [];
                    $rule['allow'] = true;
                    $rule['actions'] = [];
                    $rule['roles'] = [$role->name];
                    $should_add = 0;
                    foreach($auth_url_ids as $url_id)
                    {
                        $auth_urls = AuthUrls::find()->where(['id' => $url_id->auth_urls_id, 'controller' => $controller])->all();
                        foreach($auth_urls as $url)
                        {
                            $should_add = 1;
                            if (!($url->action == "*" || $url->action == ""))
                                $rule['actions'][] = $url->action;
                        }
                    }

                    if ($should_add == 1)
                        $rules[] = $rule;
                }
            }
        }
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => $rules,
            ],
        ];

        /*return [
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => [],
                        'roles' => ['admin'],
                    ],
                ],
            ],
        ];*/
    }
}
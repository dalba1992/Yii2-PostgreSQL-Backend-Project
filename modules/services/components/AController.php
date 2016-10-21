<?php

namespace app\modules\services\components;

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
                $defaultRule = [];
                $defaultRule['allow'] = true;
                $defaultRule['actions'] = [];
                $defaultRule['roles'] = [Yii::$app->params['defaultRole']];
                $rules[] = $defaultRule;
            }
            else
            {
                $authUrlIds = AuthUrlsAssignment::find()->where(['role' => $role->name])->all();

                if (!empty($authUrlIds))
                {
                    $rule = [];
                    $rule['allow'] = true;
                    $rule['actions'] = [];
                    $rule['roles'] = [$role->name];
                    $shouldAdd = 0;
                    foreach($authUrlIds as $urlId)
                    {
                        $auth_urls = AuthUrls::find()->where(['id' => $urlId->auth_urls_id, 'controller' => $controller])->all();
                        foreach($auth_urls as $url)
                        {
                            $shouldAdd = 1;
                            if (!($url->action == "*" || $url->action == ""))
                                $rule['actions'][] = $url->action;
                        }
                    }

                    if ($shouldAdd == 1)
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
    }
}
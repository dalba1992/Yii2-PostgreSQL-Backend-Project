<?php
namespace app\commands;

use Yii;
use yii\base\Exception;
use yii\base\ExitException;
use yii\base\InvalidParamException;
use yii\console\Controller;
use yii\base\ErrorException;

use app\components;
use yii\base\Component;

use yii\helpers\ArrayHelper;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

use app\models\AuthUrls;
use app\models\AuthUrlsAssignment;

class RbacController extends Controller
{
    /**
     * This command echoes the list of functions.
     */
    public function actionIndex()
    {
        echo "\n\n";
        echo "-------------------List of commands--------------------\n\n";
        echo "1. index\n\tLists all available commands\n";
        echo "2. init\n\tClears a rbac-db and creates a default role\n";
        echo "3. clear\n\tClears a rbac-db\n";
        echo "4. add-role(Rolename)\n\tAdds a new role\n";
        echo "5. delete-role(Rolename)\n\tDeletes a role\n";
        echo "5. add-operation(Operationname)\n\tAdds a new operation\n";
        echo "6. delete-operation(Operationname)\n\tDeletes an operation\n";
        echo "7. assign-operation(Rolename, Operationname)\n\tAssigns an operation to a role\n";
        echo "8. unassign-operation(Rolename, Operationname)\n\tUnassigns an operation from a role\n";
    }

    /**
     * This command inits db.
     */
    public function actionInit()
    {
        Yii::$app->db->createCommand('delete from auth_item_child')->query();
        Yii::$app->db->createCommand('delete from auth_item')->query();
        Yii::$app->db->createCommand('delete from auth_rule')->query();
        Yii::$app->db->createCommand('delete from auth_assignment')->query();
        Yii::$app->db->createCommand('truncate auth_urls_assignment')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE auth_urls_assignment_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('truncate auth_urls')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE auth_urls_id_seq RESTART WITH 1')->query();

        //2 - Permission, 1 - Role

        $auth = Yii::$app->authManager;

        $defaultRole = $auth->createRole(Yii::$app->params['defaultRole']);
        $defaultRole->description = 'Default Role';
        $auth->add($defaultRole);

        Yii::$app->utils->logToConsole("Default Role was created successfully.");
    }

    /**
     * This command clears db.
     */
    public function actionClear()
    {
        Yii::$app->db->createCommand('delete from auth_item_child')->query();
        Yii::$app->db->createCommand('delete from auth_item')->query();
        Yii::$app->db->createCommand('delete from auth_rule')->query();
        Yii::$app->db->createCommand('delete from auth_assignment')->query();
        Yii::$app->db->createCommand('truncate auth_urls_assignment')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE auth_urls_assignment_id_seq RESTART WITH 1')->query();
        Yii::$app->db->createCommand('truncate auth_urls')->query();
        Yii::$app->db->createCommand('ALTER SEQUENCE auth_urls_id_seq RESTART WITH 1')->query();
    }

    /**
     * This command adds a role 
     * @param string $role_name the name of a role
     */
    public function actionAddRole($role_name)
    {
        if ($role_name == "")
        {
            Yii::$app->utils->logToConsole("Invalid Parameters");
            exit;
        }
        else
        {
            $auth = Yii::$app->authManager;
            if ($auth->getRole($role_name) == null)
            {
                $role = $auth->createRole($role_name);
                $auth->add($role);

                Yii::$app->utils->logToConsole("New Role was created successfully.");
            }
            else
                Yii::$app->utils->logToConsole("Role already exists.");
        }
    }

    /**
     * This command deletes a role 
     * @param string $role_name the role name of a role to be removed
     */
    public function actionDeleteRole($role_name)
    {
        if ($role_name == "")
        {
            Yii::$app->utils->logToConsole("Invalid Parameters");
            exit;
        }
        else
        {
            $auth = Yii::$app->authManager;
            if ($auth->getRole($role_name) != null)
            {
                $role = $auth->getRole($role_name);
                $auth->remove($role);

                Yii::$app->utils->logToConsole("Role was removed successfully.");
            }
            else
                Yii::$app->utils->logToConsole("Role Not Found.");
        }
    }

    /**
     * This command adds an operation 
     * @param string $operation_name the name of an operation to be added
     */
    public function actionAddOperation($operation_name)
    {
        $model = new AuthUrls();
        if ($operation_name == "")
        {
            Yii::$app->utils->logToConsole("Invalid Parameters");
            exit;
        }
        else
        {
            $name = $operation_name;
            $pieces = explode('/', $name);
            $controller = '';
            $action = '';
            if (count($pieces) >= 3)
            {
                $controller = $pieces[1];
                $action = $pieces[2];
            }

            $name = '/'.$controller.'/'.$action;

            if ($controller == '' || $action == '')
            {
                Yii::$app->utils->logToConsole("Operation name is incorrect.");
                exit;            
            }
            else
            {
                $model = AuthUrls::find()->where(['controller' => $controller, 'action' => $action])->one();
                if ($model == null)
                {
                    $model = new AuthUrls();
                    $model->controller = $controller;
                    $model->action = $action;
                    if ($model->save()){
                        Yii::$app->utils->logToConsole("New Operation was created successfully.");
                        exit;
                    }
                }
                else
                {
                    Yii::$app->utils->logToConsole("That Operation already exists.");
                    exit;
                }
            }
        }
    }

    /**
     * This command deletes an operation 
     * @param string $operation_name the name of an operation to be removed
     */
    public function actionDeleteOperation($operation_name)
    {
        $model = new AuthUrls();
        if ($operation_name == "")
        {
            Yii::$app->utils->logToConsole("Invalid Parameters");
            exit;
        }
        else
        {
            $name = $operation_name;
            $pieces = explode('/', $name);
            $controller = '';
            $action = '';
            if (count($pieces) >= 3)
            {
                $controller = $pieces[1];
                $action = $pieces[2];
            }

            $model = AuthUrls::find()->where(['controller' => $controller, 'action' => $action])->one();
            if ($model == null)
            {
                Yii::$app->utils->logToConsole("That Operation doesn't exist.");
                exit;
            }
            else
            {
                $id = $model->id;
                $model->delete();
                $models = AuthUrlsAssignment::find()->where(['auth_urls_id' => $id])->all();
                if ($models != null)
                {
                    foreach($models as $mod)
                        $mod->delete();
                }
                Yii::$app->utils->logToConsole("Operation was successfully removed.");
                exit;
            }
        }
    }

    /**
     * This command assigns an operation to a role
     * @param string $role_name the name of a role
     * @param string $operation_name the name of an operation
     */
    public function actionAssignOperation($role_name, $operation_name)
    {
        if ($role_name == "")
        {
            Yii::$app->utils->logToConsole("Invalid Parameters");
            exit;
        }
        else if ($operation_name == "")
        {
            Yii::$app->utils->logToConsole("Invalid Parameters");
            exit;
        }
        else
        {
            $auth = Yii::$app->authManager;
            $role = $auth->getRole($role_name);
            if ($role == null)
            {
                Yii::$app->utils->logToConsole("Role not found.");
                exit;   
            }
            $name = $operation_name;
            $pieces = explode('/', $name);
            $controller = '';
            $action = '';
            if (count($pieces) >= 3)
            {
                $controller = $pieces[1];
                $action = $pieces[2];
            }

            $model = AuthUrls::find()->where(['controller' => $controller, 'action' => $action])->one();
            if ($model == null)
            {
                Yii::$app->utils->logToConsole("That Operation doesn't exist.");
                exit;
            }
            else
            {
                $assignmentModel = AuthUrlsAssignment::find()->where(['role' => $role_name, 'auth_urls_id' => $model->id])->one();
                if ($assignmentModel == null)
                {
                    $model1 = new AuthUrlsAssignment;
                    $model1->role = $role_name;
                    $model1->auth_urls_id = $model->id;
                    $model1->save();

                    Yii::$app->utils->logToConsole("Operation was successfully assigned.");
                    exit;
                }
                else
                {
                    Yii::$app->utils->logToConsole("Operation was already assigned.");
                    exit;
                }
            }
        }
    }

    /**
     * This command unassigns an operation from a role
     * @param string $role_name the name of a role
     * @param string $operation_name the name of an operation
     */
    public function actionUnassignOperation($role_name, $operation_name)
    {
        if ($role_name == "")
        {
            exit;
        }
        else if ($operation_name == "")
        {
            exit;   
        }
        else
        {
            $name = $operation_name;
            $pieces = explode('/', $name);
            $controller = '';
            $action = '';
            if (count($pieces) >= 3)
            {
                $controller = $pieces[1];
                $action = $pieces[2];
            }

            $model = AuthUrls::find()->where(['controller' => $controller, 'action' => $action])->one();
            if ($model == null)
            {
                Yii::$app->utils->logToConsole("That Operation doesn't exist.");
                exit;
            }
            else
            {
                $model1 = AuthUrlsAssignment::find()->where(['role' => $role_name, 'auth_urls_id' => $model->id])->one();
                if ($model1 == null)
                {
                    Yii::$app->utils->logToConsole("That Operation was not assigned to this role.");
                    exit;       
                }
                $model1->delete();

                Yii::$app->utils->logToConsole("Operation was successfully unassigned.");
                exit;
            }
        }
    }

    /**
     * This command shows all operations
     */
    public function actionViewOperations()
    {
        $models = AuthUrls::find()->all();

        echo "\n\n--------------------List Of Operations--------------------\n\n";
        if (count($models) == 0)
            echo "No Operation \n\n";
        else
        {
            foreach($models as $model)
            {
                echo "/".$model->controller."/".$model->action."\n";
            }
        }
    }

    /**
     * This command shows all roles
     */
    public function actionViewRoles()
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRoles();

        echo "\n\n--------------------List Of Roles--------------------\n\n";
        if (count($roles) == 0)
            echo "No Role \n\n";
        else
        {
            foreach($roles as $role)
            {
                if ($role->name == Yii::$app->params['defaultRole'])
                {
                    echo $role->name."\n\tDEFAULT ROLE\n";
                }
                else
                {
                    $models = AuthUrlsAssignment::find()->where(['role' => $role->name])->all();
                    if (count($models) == 0)
                        echo $role->name."\n\tNo Role"."\n";
                    else
                    {
                        echo $role->name."\n";
                        foreach($models as $model)
                        {
                            $model1 = AuthUrls::find()->where(['id' => $model->auth_urls_id])->one();
                            echo "\t"."/".$model1->controller."/".$model1->action."\n";
                        }
                    }
                }
            }
        }
    }

}
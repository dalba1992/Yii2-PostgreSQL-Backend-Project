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

use app\models\UserEntity;

class UserController extends Controller
{
    /**
     * This command echoes the list of commands.
     */
    public function actionIndex()
    {
        echo "\n\n";
        echo "-------------------List of commands--------------------\n\n";
        echo "1. index\t\t\t\t:\tLists all available commands\n";
        echo "2. add(Username, Email, Password)\t:\tAdds a new user\n";
        echo "3. edit(Username, Attr, Value)\t\t:\tEdits a user\n";
        echo "4. delete(Username)\t\t\t:\tDeletes a user\n";
        echo "5. assign(Username, Rolename)\t\t:\tAssigns a role to a user\n";
        echo "6. unassign(Username, Rolename)\t\t:\tUnassigns a role from a user\n";
        echo "7. view(Attr = '', Value = '')\t\t:\tDisplays all users or a specific user\n";
    }

    /**
     * This command echoes user list or a specific user, according to parameters
     * @param string $attr the attribute of a specific user to be shown, can be blank
     * @param string $value the attribute value of a specific user to be shown, can be blank
     * @return 0
     */
    public function actionView($attr = '', $value = '')
    {
        if ($attr == "" and $value == "")
        {
            $models = UserEntity::find()->all();
            if ($models == null)
            {
                echo "Users Not Found"."\n";
                exit;
            }
            else
            {
                echo "\n\n";
                echo "-------------------Users Found--------------------\n\n";

                foreach($models as $model) {
                    echo "ID\t\t:\t".$model->id."\n";
                    echo "Username\t:\t".$model->username."\n";
                    echo "Email\t\t:\t".$model->email."\n";
                    echo "Name\t\t:\t".$model->firstName." ".$model->lastName."\n";

                    $auth = Yii::$app->authManager;
                    $roles = $auth->getRolesByUser($model->id);
                    if ($roles == null)
                        echo "Roles\t\t:\tNo assigned Roles\n";
                    else
                    {
                        echo "Roles\t\t:\t";
                        foreach($roles as $role)
                            echo $role->name.' ';
                        echo "\n";
                    }

                    echo "============================================\n";
                }

                echo "\n--------------------------------------------------\n\n";
            }
        }
        else if ($value == "")
        {
            Yii::$app->utils->logToConsole("Invalid Parameters");
            exit;
        }
        else
        {
            $table = Yii::$app->db->schema->getTableSchema(UserEntity::tableName());
            if(!isset($table->columns[$attr]))
            {
                Yii::$app->utils->logToConsole("Attribute \"".$attr."\" doesn't exist");
                exit;      
            }
            $models = UserEntity::find()->where([$attr => $value])->all();
            if ($models == null)
            {
                echo "Users Not Found"."\n";
                exit;
            }
            else
            {
                echo "\n\n";
                echo "-------------------Users Found--------------------\n\n";

                foreach($models as $model) {
                    echo "ID\t\t:\t".$model->id."\n";
                    echo "Username\t:\t".$model->username."\n";
                    echo "Email\t\t:\t".$model->email."\n";
                    echo "Name\t\t:\t".$model->firstName." ".$model->lastName."\n";

                    $auth = Yii::$app->authManager;
                    $roles = $auth->getRolesByUser($model->id);
                    if ($roles == null)
                        echo "Roles\t\t:\tNo assigned Roles\n";
                    else
                    {
                        echo "Roles\t\t:\t";
                        foreach($roles as $role)
                            echo $role->name.' ';
                        echo "\n";
                    }

                    echo "============================================\n";
                }

                echo "\n--------------------------------------------------\n\n";
            }
        }

        return 0;
    }

    /**
     * This command adds a user
     * @param string $username the username of a user to be added
     * @param string $email the email of a user to be added
     * @param string $password the password of a user
     */
    public function actionAdd($username, $email, $password)
    {
        if ($username == "")
        {
            exit;
        }
        else if ($email == "")
        {
            exit;
        }
        else if ($password == "")
        {
            exit;
        }
        else
        {
            $model = UserEntity::find()->where(['email' => $email])->one();
            if ($model == null)
            {
                $model = UserEntity::find()->where(['username' => $username])->one();
                if ($model == null)
                {
                    $model = new UserEntity();
                    $model->username = $username;
                    $model->email = $email;
                    $model->password = md5($password);
                    $model->regDate = strtotime(date('Y-m-d H:i:s'));
                    $model->updateDate = strtotime(date('Y-m-d H:i:s'));

                    if ($model->save()){
                        /*$auth = Yii::$app->authManager;
                        $defaultRole = $auth->getRole(Yii::$app->params['defaultRole']);
                        $auth->assign($defaultRole, $model->id);*/

                        echo "User registration was done\n";
                        exit;
                    }
                }
                else
                {
                    Yii::$app->utils->logToConsole("Username already exists.");
                    exit;
                }
            }
            else
            {
                Yii::$app->utils->logToConsole("Email already exists.");
                exit;       
            }
        }
    }

    /**
     * This command edits user 
     * @param string $username the username of a user
     * @param string $attr the attribute to be updated
     * @param string $val the value of attribute to be updated
     */
    public function actionEdit($username, $attr, $value)
    {
        if ($username == "")
        {
            exit;
        }
        else if ($attr == "")
        {
            exit;
        }
        else if ($value == "")
        {
            exit;
        }
        else
        {
            $model = UserEntity::find()->where(['username' => $username])->one();
            if ($model == null)
            {
                Yii::$app->utils->logToConsole("User Not Found.");
                exit;
            }
            else
            {
                $table = Yii::$app->db->schema->getTableSchema(UserEntity::tableName());
                if(!isset($table->columns[$attr]))
                {
                    Yii::$app->utils->logToConsole("Attribute \"".$attr."\" doesn't exist");
                    exit;
                }

                $item = [];
                if ($attr == 'password')
                    $item[$attr] = md5($value);
                else
                    $item[$attr] = $value;
                $item['updateDate'] = strtotime(date('Y-m-d H:i:s'));

                $model->attributes = $item;
                if ($model->save()){
                    echo "User update was done successfully\n";
                    exit;
                }
            }
        }
    }

    /**
     * This command deletes a user 
     * @param string $username the name of a user to be removed
     */
    public function actionDelete($username)
    {
        if ($username == "")
        {
            exit;
        }
        else
        {
            $model = UserEntity::find()->where(['username' => $username])->one();
            if ($model == null)
            {
                Yii::$app->utils->logToConsole("User Not Found.");
                exit;
            }
            else
            {
                $auth = Yii::$app->authManager;
                $auth->revokeAll($model->id);
                if ($model->delete()){
                    echo "User was deleted successfully\n";
                    exit;
                }
            }
        }
    }

    /**
     * This command assigns a specific role to a user
     * @param string $id the id of a user, 
     * @param string $role_name the name of a role
     */
    public function actionAssign($username, $role_name)
    {
        if ($username == "")
        {
            exit;
        }
        else if ($role_name == "")
        {
            exit;
        }
        else
        {
            $model = UserEntity::find()->where(['username' => $username])->one();
            if ($model == null)
            {
                Yii::$app->utils->logToConsole("User Not Found.");
                exit;
            }
            else
            {
                $auth = Yii::$app->authManager;
                $role = $auth->getRole($role_name);
                if ($role == null)
                {
                    Yii::$app->utils->logToConsole("Role Not Found.");
                    exit;       
                }
                $assigned_roles = $auth->getRolesByUser($model->id);
                foreach($assigned_roles as $key=>$val)
                {
                    if ($key == $role_name)
                    {
                        Yii::$app->utils->logToConsole("Role was assigned already.");
                        exit;
                    }
                }
                $auth->assign($role, $model->id);
                Yii::$app->utils->logToConsole("Role was assigned correctly.");
            }
        }
    }

    /**
     * This command unassign a role from a user
     * @param string $username the username of a user
     * @param string $role_name the name of a role
     */
    public function actionUnassign($username, $role_name)
    {
        if ($username == "")
        {
            exit;
        }
        else if ($role_name == "")
        {
            exit;
        }
        else
        {
            $model = UserEntity::find()->where(['username' => $username])->one();
            if ($model == null)
            {
                Yii::$app->utils->logToConsole("User Not Found.");
                exit;
            }
            else
            {
                $auth = Yii::$app->authManager;
                $role = $auth->getRole($role_name);
                if ($role == null)
                {
                    Yii::$app->utils->logToConsole("Role Not Found.");
                    exit;       
                }
                $assigned_roles = $auth->getRolesByUser($model->id);
                foreach($assigned_roles as $key=>$val)
                {
                    if ($key == $role_name)
                    {
                        $auth->revoke($role, $model->id);
                        Yii::$app->utils->logToConsole("Role was unassigned correctly.");
                        exit;
                    }
                }
                Yii::$app->utils->logToConsole("Role was not assigned yet.");                
            }
        }
    }
}
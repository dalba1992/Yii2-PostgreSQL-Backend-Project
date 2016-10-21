<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;


use app\components\AController;
use app\models\UserEntity;
use app\models\UserEntitySearch;
use app\models\AuthUrls;
use app\models\AuthUrlsAssignment;

class UserManagementController extends AController
{

    /**
     * @return string|\yii\web\Response
     */
    public function actionIndex()
    {
        return $this->render('index', []);
    }

    /**
     * This function displays all users.
     */
    public function actionUser()
    { 
        $query = UserEntity::find();

        $searchModel = new UserEntitySearch();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams)->dataProvider;

        return $this->render('user', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    /**
     * This function creates a specific user.
     * @param array $_POST['UserEntity'] the array of items including email, username, password and so on
     */
    public function actionCreate()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new UserEntity();
        if(isset($_POST['UserEntity']) && sizeof($_POST['UserEntity'])>0){
            $email = $_POST['UserEntity']['email'];

            $model = UserEntity::find()->where(['email' => $email])->one();
            if ($model == null)
            {
                $model = new UserEntity();
                $model->attributes = $_POST['UserEntity'];
                $model->regDate = strtotime(Date('m/d/Y H:i:s'));
                $model->updateDate = strtotime(Date('m/d/Y H:i:s'));
                $model->password = md5($_POST['UserEntity']['password']);
                if ($model->save()){
                    return $this->redirect(['user']);
                }
            }
            else
            {
                $model->attributes = $_POST['UserEntity'];
                return $this->render('create', [
                    'model' => $model,
                    'error' => '1',
                ]);
            }
        }
        else{
            return $this->render('create', [
                'model' => $model,
                'error' => '0',
            ]);
        }
    }

    /**
     * This function deletes a specific user.
     * @param string $_GET['id'] the id of a user to be removed.
     */
    public function actionDelete()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if(isset($_GET['id'])>0){
            $model = UserEntity::find()->where(['id' => $_GET['id']])->one();
            if ($model != null)
            {
                $auth = Yii::$app->authManager;
                $auth->revokeAll($_GET['id']);
                $model->delete();
                return $this->redirect(['user']);                
            }
        }
    }

    /**
     * This function shows details of a specific user to be updated in edit page.
     * @param string $id the id of a user to be updated
     */
    public function actionUpdate($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = UserEntity::find()->where(['id' => $id])->one();
        if ($model != null)
        {
            return $this->render('edit', [
                'model' => $model,
                'error' => '0',
                'id' => $id,
            ]);
        }
    }

    /**
     * This function updates details of a specific user.
     * @param array $_POST['UserEntity'] the array of details of a user to be updated including email, password and so on
     */
    public function actionEdit()
    {
        if(isset($_POST['UserEntity']) && sizeof($_POST['UserEntity'])>0){
            $model = UserEntity::find()->where(['id' => $_POST['id']])->one();

            if ($_POST['UserEntity']['email'] == "")
                $_POST['UserEntity']['email'] = $model->email;

            if ($_POST['UserEntity']['password'] == "")
                $_POST['UserEntity']['password'] = $model->password;

            $email = $_POST['UserEntity']['email'];
            $model1 = UserEntity::find()->where(['email' => $email])->one();
            if ($model1 == null)
            {
                $model->attributes = $_POST['UserEntity'];
                $model->updateDate = strtotime(Date('m/d/Y H:i:s'));
                $model->password = md5($_POST['UserEntity']['password']);
                if ($model->save()){
                    return $this->redirect(['user']);
                }
            }
            else
            {
                if ($model1->id == $_POST['id'])
                {
                    $model->attributes = $_POST['UserEntity'];
                    $model->updateDate = strtotime(Date('m/d/Y H:i:s'));
                    $model->password = md5($_POST['UserEntity']['password']);
                    if ($model->save()){
                        return $this->redirect(['user']);
                    }
                }
                else
                {
                    $model->attributes = $_POST['UserEntity'];
                    return $this->render('edit', [
                        'model' => $model,
                        'error' => '1',
                        'id' => $_POST['id'],
                    ]);
                }
            }
        }
    }

    /**
     * This function displays assigned and unassigned roles of a specific user
     * @param string $_GET['id'] the id of a user
     */
    public function actionAssign()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if(isset($_GET['id'])>0){
            $model = UserEntity::find()->where(['id' => $_GET['id']])->one();
            if ($model != null)
            {
                $auth = new Yii::$app->authManager;
                $assigned_roles = $auth->getRolesByUser($_GET['id']);

                $unassigned_roles = $auth->getRoles();
                foreach($unassigned_roles as $key=>$role)
                {
                    foreach($assigned_roles as $key1=>$role1)
                    {
                        if ($key == $key1)
                            unset($unassigned_roles[$key]);
                    }
                }

                return $this->render('assign', [
                    'assigned' => $assigned_roles,
                    'unassigned' => $unassigned_roles,
                    'id' => $_GET['id'],
                ]);
            }
        }
    }

    /**
     * This function processes assign or unassign role for a user by ajax call request
     * @param string $_POST['id'] the id of a user
     * @param string $_POST['role'] the name of a role to be processed
     * @param string $_POST['assignFlag'] the flag to indicate whether role will be assigned or unassigned , 1: ASSIGN, 0: UNASSIGN
     */
    public function actionProcessAssign()
    {
        $user_id = $_POST['user_id'];
        $role = $_POST['role'];
        $assignFlag = $_POST['assignFlag'];

        $auth = new Yii::$app->authManager;

        if ($assignFlag == '1') //ASSIGN
        {
            $role_obj = $auth->getRole($role);
            $auth->assign($role_obj, $user_id);
        }
        else if ($assignFlag == '0') //UNASSIGN
        {
            $role_obj = $auth->getRole($role);
            $auth->revoke($role_obj, $user_id);
        }
    }

    /**
     * This function shows roles, operations, tasks on a rbac page
     */
    public function actionRbac()
    {
        $auth = new Yii::$app->authManager;

        $roles = $auth->getRoles();
        $permissions = $auth->getPermissions();
        $operations = AuthUrls::find()->all();

        return $this->render('rbac', [
            'roles' => $roles,
            'tasks' => $permissions,
            'operations' => $operations,
        ]);
    }

    /**
     * This function creates a specific operation
     * @param array $_POST['Operation'] the array of values of operation, including name, description and bizrule
     */
    public function actionCreateOperation()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new AuthUrls();
        if(isset($_POST['Operation']) && sizeof($_POST['Operation'])>0){
            $name = $_POST['Operation']['name'];
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
                $model->attributes = $_POST['Operation'];
                return $this->render('operationcreate', [
                    'model' => $model,
                    'error' => '2',
                    'name' => $name,
                ]);                
            }
            else
            {
                $model = AuthUrls::find()->where(['controller' => $controller, 'action' => $action])->one();
                if ($model == null)
                {
                    $model = new AuthUrls();
                    $model->controller = $controller;
                    $model->action = $action;
                    $model->description = $_POST['Operation']['description'];
                    if ($model->save()){
                        return $this->redirect(['rbac']);
                    }
                }
                else
                {
                    $model->attributes = $_POST['Operation'];
                    return $this->render('operationcreate', [
                        'model' => $model,
                        'error' => '1',
                        'name' => $name,
                    ]);
                }
            }
        }
        else{
            return $this->render('operationcreate', [
                'model' => $model,
                'error' => '0',
                'name' => '',
            ]);
        }
    }

    /**
     * This function shows details of a specific operation to be updated in operationedit page.
     * @param string $id the id of an operation to be updated
     */
    public function actionEditOperation($id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = AuthUrls::find()->where(['id' => $id])->one();
        if ($model != null)
        {
            $name = '/'.$model->controller.'/'.$model->action;
            return $this->render('operationedit', [
                'model' => $model,
                'error' => '0',
                'id' => $id,
                'name' => $name,
            ]);
        }
    }

    /**
     * This function updates an operation.
     * @param string $_POST['id'] the id of an operation to be updated
     * @param array $_POST['Operation'] the array of details of an operation
     */
    public function actionUpdateOperation()
    {
        if(isset($_POST['Operation']) && sizeof($_POST['Operation'])>0){
            $model = AuthUrls::find()->where(['id' => $_POST['id']])->one();

            $name = $_POST['Operation']['name'];
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
                $model->attributes = $_POST['Operation'];
                return $this->render('operationedit', [
                    'model' => $model,
                    'error' => '2',
                    'id' => $_POST['id'],
                    'name' => $name,
                ]);                
            }
            else
            {
                $model1 = AuthUrls::find()->where(['controller' => $controller, 'action' => $action])->one();
                if ($model1 == null)
                {
                    $model->controller = $controller;
                    $model->action = $action;
                    $model->description = $_POST['Operation']['description'];
                    if ($model->save()){
                        return $this->redirect(['rbac']);
                    }
                }
                else
                {
                    if ($model1->id == $_POST['id'])
                    {
                        $model->controller = $controller;
                        $model->action = $action;
                        $model->description = $_POST['Operation']['description'];
                        if ($model->save()){
                            return $this->redirect(['rbac']);
                        }
                    }
                    else
                    {
                        $model->attributes = $_POST['Operation'];
                        return $this->render('operationedit', [
                            'model' => $model,
                            'error' => '1',
                            'id' => $_POST['id'],
                            'name' => $name,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * This function removes an operation
     * @param string $_GET['id'] the id of an operation to be removed
     */
    public function actionRemoveOperation()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if(isset($_GET['id'])>0){
            $model = AuthUrls::find()->where(['id' => $_GET['id']])->one();
            if ($model != null)
                $model->delete();

            $models = AuthUrlsAssignment::find()->where(['auth_urls_id' => $_GET['id']])->all();
            if ($models != null)
            {
                foreach($models as $mod)
                    $mod->delete();
            }
            return ($this->redirect(['rbac']));
        }
    }

    /**
     * This function creates a new role
     * @param array $_POST['Role'] the array of details of a role to be created, including name, description and so on
     */
    public function actionCreateRole()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $Role = [];
        $Role['name'] = '';
        $Role['description'] = '';

        if(isset($_POST['Role']) && sizeof($_POST['Role'])>0){
            $auth = Yii::$app->authManager;

            $role = $auth->getRole($_POST['Role']['name']);
            if ($role == null)
            {
                $role = $auth->createRole($_POST['Role']['name']);
                $role->description = $_POST['Role']['description'];
                $auth->add($role);
                return $this->redirect(['rbac']);
            }
            else
            {
                return $this->render('rolecreate', [
                    'Role' => $_POST['Role'],
                    'error' => '1',
                ]);
            }
        }
        else{
            return $this->render('rolecreate', [
                'Role' => $Role,
                'error' => '0',
            ]);
        }
    }

    /**
     * This function shows details of a specific role to be updated in roleedit page.
     * @param string $name the name of a role to be updated
     */
    public function actionEditRole($name)
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $auth = Yii::$app->authManager;
        $role = $auth->getRole($name);
        if ($role != null)
        {
            $Role = [];
            $Role['old_name'] = $role->name;
            $Role['name'] = $role->name;
            $Role['description'] = $role->description;

            return $this->render('roleedit', [
                'Role' => $Role,
                'error' => '0',
            ]);
        }
    }

    /**
     * This function updates a role
     * @param array $_POST['Role'] the array of details of a role to be updated
     */
    public function actionUpdateRole()
    {
        if(isset($_POST['Role']) && sizeof($_POST['Role'])>0){

            $auth = Yii::$app->authManager;
            $role = $auth->getRole($_POST['Role']['old_name']);

            $role1 = $auth->getRole($_POST['Role']['name']);
            if ($role1 == null)
            {
                $auth->remove($role);
                $role1 = $auth->createRole($_POST['Role']['name']);
                $role1->description = $_POST['Role']['description'];
                $auth->add($role1);
                return $this->redirect(['rbac']);
            }
            else
            {
                if ($role->name != $role1->name)
                {
                    return $this->render('roleedit', [
                        'Role' => $_POST['Role'],
                        'error' => '1',
                    ]);
                }
                else
                {
                    $auth->remove($role);
                    $role1 = $auth->createRole($_POST['Role']['name']);
                    $role1->description = $_POST['Role']['description'];
                    $auth->add($role1);
                    return $this->redirect(['rbac']);
                }
            }
        }
    }

    /**
     * This function removes a role.
     * @param string $_GET['name'] the name of a role to be removed
     */
    public function actionRemoveRole()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if(isset($_GET['name'])>0){
            $auth = Yii::$app->authManager;
            $role = $auth->getRole($_GET['name']);
            $auth->remove($role);
            return $this->redirect(['rbac']);
        }
    }

    /**
     * This function shows assigned and unassigned operations of a specific role on assignoperations page
     * @param string $_GET['name'] the name of a role to be processed
     */
    public function actionAssignOperations()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if(isset($_GET['name'])>0){
            $auth = Yii::$app->authManager;
            $role = $auth->getRole($_GET['name']);
            if ($role != null)
            {
                $unassigned_urls = AuthUrls::find()->all();
                usort($unassigned_urls, function($a, $b) {
                    if (strcmp($a->controller, $b->controller) != 0)
                        return strcmp($a->controller, $b->controller);
                    else
                        return strcmp($a->action, $b->action);
                });

                $assigned_ids = AuthUrlsAssignment::find()->where(['role'=>$_GET['name']])->all();
                $assigned_urls = [];

                foreach($unassigned_urls as $key=>$url)
                {
                    foreach($assigned_ids as $key1=>$url1)
                    {
                        if ($url1->auth_urls_id == $url->id)
                        {
                            $assigned_urls[$key] = $unassigned_urls[$key];
                            unset($unassigned_urls[$key]);
                        }
                    }
                }

                return $this->render('assignoperations', [
                    'assigned' => $assigned_urls,
                    'unassigned' => $unassigned_urls,
                    'name' => $_GET['name'],
                ]);
            }
        }
    }

    /**
     * This function processes assign or unassign operation for a role by ajax call request
     * @param string $_POST['role'] the name of a role
     * @param string $_POST['auth_urls_id'] the id of an operation to be processed
     * @param string $_POST['assignFlag'] the flag to indicate whether operation will be assigned or unassigned , 1: ASSIGN, 0: UNASSIGN
     */
    public function actionProcessAssignOperations()
    {
        $auth_urls_id = $_POST['auth_urls_id'];
        $role = $_POST['role'];
        $assignFlag = $_POST['assignFlag'];

        if ($assignFlag == '1') //ASSIGN
        {
            $model = new AuthUrlsAssignment;
            $model->role = $role;
            $model->auth_urls_id = $auth_urls_id;
            $model->save();
        }
        else if ($assignFlag == '0') //UNASSIGN
        {
            $model = AuthUrlsAssignment::find()->where(['role' => $role, 'auth_urls_id' => $auth_urls_id])->one();
            $model->delete();
        }
    }

    /**
     * This function creates a new task
     * @param array $_POST['Task'] the array of details of a task to be created, including name and description and so on
     */
    public function actionCreateTask()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $Task = [];
        $Task['name'] = '';
        $Task['description'] = '';

        if(isset($_POST['Task']) && sizeof($_POST['Task'])>0){
            $auth = Yii::$app->authManager;

            $task = $auth->getPermission($_POST['Task']['name']);
            if ($task == null)
            {
                $task = $auth->createPermission($_POST['Task']['name']);
                $task->description = $_POST['Task']['description'];
                $auth->add($task);
                return $this->redirect(['rbac']);
            }
            else
            {
                return $this->render('taskcreate', [
                    'Task' => $_POST['Task'],
                    'error' => '1',
                ]);
            }
        }
        else{
            return $this->render('taskcreate', [
                'Task' => $Task,
                'error' => '0',
            ]);
        }
    }

    /**
     * This function shows details of a task on taskedit page
     * @param string $name the name of a task to be edited
     */
    public function actionEditTask($name)
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $auth = Yii::$app->authManager;
        $task = $auth->getPermission($name);
        if ($task != null)
        {
            $Task = [];
            $Task['old_name'] = $task->name;
            $Task['name'] = $task->name;
            $Task['description'] = $task->description;

            return $this->render('taskedit', [
                'Task' => $Task,
                'error' => '0',
            ]);
        }
    }

    /**
     * This function updates a task
     * @param array $_POST['Task'] the array of details of a task to be updated, including name and description and so on
     */
    public function actionUpdateTask()
    {
        if(isset($_POST['Task']) && sizeof($_POST['Task'])>0){

            $auth = Yii::$app->authManager;
            $task = $auth->getPermission($_POST['Task']['old_name']);

            $task1 = $auth->getPermission($_POST['Task']['name']);
            if ($task1 == null)
            {
                $auth->remove($task);
                $task1 = $auth->createPermission($_POST['Task']['name']);
                $task1->description = $_POST['Task']['description'];
                $auth->add($task1);
                return $this->redirect(['rbac']);
            }
            else
            {
                if ($task->name != $task1->name)
                {
                    return $this->render('taskedit', [
                        'Task' => $_POST['Task'],
                        'error' => '1',
                    ]);
                }
                else
                {
                    $auth->remove($task);
                    $task1 = $auth->createPermission($_POST['Task']['name']);
                    $task1->description = $_POST['Task']['description'];
                    $auth->add($task1);
                    return $this->redirect(['rbac']);
                }
            }
        }
    }

    /**
     * This function removes a task
     * @param string $_GET['name'] the name of a task to be removed
     */
    public function actionRemoveTask()
    {
        if (Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        if(isset($_GET['name'])>0){
            $auth = Yii::$app->authManager;
            $task = $auth->getPermission($_GET['name']);
            $auth->remove($task);
            return $this->redirect(['rbac']);
        }
    }

    /**
     * This function uploads an profile image
     * @param string $flag the flag to indicate whether image will be uploaded or removed. 0 : upload, 1 : remove
     */
    public function actionUpload($flag)
    {
        $destination = Yii::$app->params['staticPath'] . Yii::$app->params['avatarPath'];
        if ($flag == '0')
        {
            $image = addslashes(file_get_contents($_FILES['UserEntity']['tmp_name']['profilePic'])); //SQL Injection defence!
            $image_name = addslashes($_FILES['UserEntity']['name']['profilePic']);

            $temp = explode(".", $_FILES['UserEntity']["name"]['profilePic']);
            $extension = end($temp);

            $random_bytes = mcrypt_create_iv(12, MCRYPT_DEV_URANDOM);
            $strrandomname = base64_encode($random_bytes).'.'.$extension;
            $strrandomname = str_replace('/', '_', $strrandomname);
            $destination = $destination . $strrandomname;
            if(move_uploaded_file($_FILES['UserEntity']['tmp_name']['profilePic'], $destination))
            {
                echo $strrandomname; exit;
            }
        }
        else if ($flag == '1')
        {
            /*unlink($target.$pic);
            $model = UserEntity::find()->where(['id' => $id])->one();
            if ($model != null)
            {
                $model->profilePic = '';
                $model->save();
            }*/
        }
    }    
}

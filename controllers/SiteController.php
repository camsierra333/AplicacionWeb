<?php

namespace app\controllers;

use Yii;
use yii\db\Connection;
use yii\db\ActiveRecord;
use yii\db\mssql;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\FormRegister;
use app\models\Users;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\data\Pagination;
use yii\data\LinkPager;
use yii\widgets\ActiveForm;
use yii\web\Session;
use app\models\FormRecoverPass;
use app\models\FormResetPass;
use app\models\User;
use app\models\FormUpload;
use yii\web\UploadedFile;
use app\models\FormSearch;
use app\models\FormActualizar; 


class SiteController extends Controller
{
    public $serverName = 'direccion_servidor';
    public $connectionInfo=array('Database'=>'nombre_base_de_datos','UID'=>'usuario', 'PWD'=>"contrasenia");
    
    public function actionRecoverpass()
    {
        $model = new FormRecoverPass;

        $msg = null;

        if ($model->load(Yii::$app->request->post()))
        {
          if ($model->validate())
          {
            $table = Users::find()->where("email=:email", [":email" => $model->email]);

            if ($table->count() == 1)
            {
                $session = new Session;
                $session->open();
                
                $session["recover"] = $this->randKey("abcdef0123456789", 200);
                $recover = $session["recover"];

                $table = Users::find()->where("email=:email", [":email" => $model->email])->one();
                $session["id_recover"] = $table->id;

                $verification_code = $this->randKey("abcdef0123456789", 8);

                $table->verification_code = $verification_code;

                $table->save();

                $subject = "Recuperar password";
                $body = "<p>Copie el siguiente código de verificación para restablecer su password ... ";
                $body .= "<strong>".$verification_code."</strong></p>";
                $body .= "<p><a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/resetpass'>Recuperar password</a></p>";

                Yii::$app->mailer->compose()
                ->setTo($model->email)
                ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                ->setSubject($subject)
                ->setHtmlBody($body)
                ->send();

                $model->email = null;

                $msg = "Le enviamos un e-mail para cambiar su contraseña";
            }
            else
            {
                $msg = "Ha ocurrido un error";
            }
          }
          else
          {
            $model->getErrors();
          }
         }
         return $this->render("recoverpass", ["model" => $model, "msg" => $msg]);
    }
    
    public function actionUser()
    {
       return $this->render("user");
    }
    
    public function actionAdmin()
    {
       return $this->render("admin");
    }
    public function actionResetpass()
    {
        $model = new FormResetPass;

        $msg = null;

        $session = new Session;
        $session->open();

        if (empty($session["recover"]) || empty($session["id_recover"]))
        {
            return $this->redirect(["site/index"]);
        }
        else
        {

            $recover = $session["recover"];
            $model->recover = $recover;
            $id_recover = $session["id_recover"];
        }

        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->validate())
            {
                if ($recover == $model->recover)
                {
                    $table = Users::findOne(["email" => $model->email, "id" => $id_recover, "verification_code" => $model->verification_code]);

                    $table->password = crypt($model->password, Yii::$app->params["salt"]);

                    if ($table->save())
                    {
                        $session->destroy();

                        $model->email = null;
                        $model->password = null;
                        $model->password_repeat = null;
                        $model->recover = null;
                        $model->verification_code = null;

                        $msg = "El password se modificó correctamente, espere 5 segundos ...";
                        $msg .= "<meta http-equiv='refresh' content='5; ".Url::toRoute("site/login")."'>";
                    }
                    else
                    {
                        $msg = "Ha ocurrido un error";
                    }

                }
                else
                {
                    $model->getErrors();
                }
            }
        }
     return $this->render("resetpass", ["model" => $model, "msg" => $msg]);

    }
    private function randKey($str='', $long=0)
    {
        $key = null;
        $str = str_split($str);
        $start = 0;
        $limit = count($str)-1;
        for($x=0; $x<$long; $x++)
        {
            $key .= $str[rand($start, $limit)];
        }
        return $key;
    }
  
    public function actionConfirm()
    {
        $table = new Users;
        if (Yii::$app->request->get())
        {
            $id = Html::encode($_GET["id"]);
            $authKey = $_GET["authKey"];

            if ((int) $id)
            {
                $model = $table
                ->find()
                ->where("id=:id", [":id" => $id])
                ->andWhere("authKey=:authKey", [":authKey" => $authKey]);

                if ($model->count() == 1)
                {
                    $activar = Users::findOne($id);
                    $activar->activate = 1;
                    if ($activar->update())
                    {
                        echo "El registro fue exitoso.";
                        echo "<meta http-equiv='refresh' content='5; ".Url::toRoute("site/login")."'>";
                    }
                    else
                    {
                        echo "Existe un error al registrar un usuario. Intentelo de nuevo";
                        echo "<meta http-equiv='refresh' content='5; ".Url::toRoute("site/login")."'>";
                    }
                 }
                else
                {
                    return $this->redirect(["site/login"]);
                }
            }
            else
            {
                return $this->redirect(["site/login"]);
            }
        }
    }
 
    public function actionRegister()
    {
        $model = new FormRegister;
        $msg = null;

        if ($model->load(Yii::$app->request->post()) && Yii::$app->request->isAjax)
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ActiveForm::validate($model);
        }

        if ($model->load(Yii::$app->request->post()))
        {
         if($model->validate())
         {
            $table = new Users;
            $table->username = $model->username;
            $table->email = $model->email;
            if ($model->role == 2) // 2 es admin
            {
               $table->role = $model->role;
            }else{
                
               $table->role = 1; // 1 es guest
            }
            $table->password = crypt($model->password, Yii::$app->params["salt"]);
            $table->authKey = $this->randKey("abcdef0123456789", 200);
            $table->accessToken = $this->randKey("abcdef0123456789", 200);

            if ($table->insert())
            {
                $user = $table->find()->where(["email" => $model->email])->one();
                $id = urlencode($user->id);
                $authKey = urlencode($user->authKey);
                
                $subject = "Confirmar registro del Administrador";
                $body = "<h1>Señor usuario: El administrador del sistema de ayuda a la toma de decisiones en la "
                        . "farmacia del HUSI, lo ha creado a usted como usuario de este sistema. Para terminar esta solicitud"
                        . "del administrador es necesario que usted confirme su cuenta por medio de este link: </h1>";
                $body .= "<a href='http://localhost/appwebtgfarmacia/web/index.php?r=site/confirm&id=".$id."&authKey=".$authKey."'>confirmar</a>";

                Yii::$app->mailer->compose()
                ->setTo($user->email)
                ->setFrom([Yii::$app->params["adminEmail"] => Yii::$app->params["title"]])
                ->setSubject($subject)
                ->setHtmlBody($body)
                ->send();

                $model->username = null;
                $model->email = null;
                $model->password = null;
                $model->password_repeat = null;

                $msg = "Muchas Gracias! El correo de confirmación ya fue enviado al usuario inscrito."
                        . " Despues de confirmado ya podrá acceder al sistema";
            }
            else
            {
                $msg = "Existe un error al momento del registro. Por favor intentalo de nuevo!";
            }

         }
         else
         {
          $model->getErrors();
         }
        }
        return $this->render("register", ["model" => $model, "msg" => $msg]);
    }
    
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'user', 'admin','admonusuarios','importar','historicos','vencimiento','inventario',
                    'importpatrones', 'importreglas', 'importseries',  'listarusuarios','recoverpass',
                    'register','resetpass','update','vencimiento','vencimiento3meses','vencimiento6meses'],
                'rules' => [
                    [
                        'actions' => ['logout', 'user', 'admin','admonusuarios','importar','historicos','vencimiento','inventario',
                                       'importpatrones', 'importreglas', 'importseries', 'listarusuarios','recoverpass',
                                       'register','resetpass','update','vencimiento','vencimiento3meses','vencimiento6meses'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return User::isUserAdmin(Yii::$app->user->identity->id);
                        },
                    ],
                    [
                       'actions' => ['logout', 'user','historicos','vencimiento','inventario',
                                     'recoverpass',
                                     'resetpass','vencimiento','vencimiento3meses','vencimiento6meses'],
                       'allow' => true,
                       'roles' => ['@'],
 
                       'matchCallback' => function ($rule, $action) {

                          return User::isUserSimple(Yii::$app->user->identity->id);
                      },
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

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionLogin()
    {
       if (!\Yii::$app->user->isGuest) 
       {
            if (User::isUserAdmin(Yii::$app->user->identity->id))
            {
             return $this->redirect(["site/admonusuarios"]);
            }
            else
            {
             return $this->redirect(["site/historicos"]);
            }
       }

       $model = new LoginForm();
       
       if ($model->load(Yii::$app->request->post()) && $model->login()) 
       {
           if (User::isUserAdmin(Yii::$app->user->identity->id))
           {
            return $this->redirect(["site/admonusuarios"]);
           }
           else
           {
            return $this->redirect(["site/historicos"]);
           }

       } else {
                return $this->render('login', ['model' => $model,]);
              }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

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
    
    public function actionAdmonusuarios()
    {
        return $this->render('admonusuarios');
    }

    public function actionImportar()
    {
        return $this->render('importar');
    }
    
    public function actionHistoricos()
    {
        return $this->render('historicos');
    }
    
    public function actionVencimiento()
    {
        return $this->render('vencimiento');
    }

    public function actionInventario()
    {
        return $this->render('inventario');
    }
    
    public function actionImportreglas()
    {
        $model = new FormUpload;
        $msg = null;
 
        if ($model->load(Yii::$app->request->post()))
        {
            $model->file = UploadedFile::getInstances($model, 'file');

            if ($model->file && $model->validate()) 
            {
                foreach ($model->file as $file) 
                {
                    $file->saveAs('archivos/' . $file->baseName . '.' . $file->extension);
                    $msg = "<p><strong class='label label-info'>Felicitaciones! El archivo fue cargado con exito</strong></p>";
                }
            }
        }
        return $this->render("importreglas", ["model" => $model, "msg" => $msg]);
    }
    
    public function actionImportpatrones()
    {
        $model = new FormUpload;
        $msg = null;
 
        if ($model->load(Yii::$app->request->post()))
        {
            $model->file = UploadedFile::getInstances($model, 'file');

            if ($model->file && $model->validate()) 
            {
                foreach ($model->file as $file) 
                {
                    $file->saveAs('archivos/' . $file->baseName . '.' . $file->extension);
                    $msg = "<p><strong class='label label-info'>Felicitaciones! El archivo fue cargado con exito</strong></p>";
                }
            }
        }
        return $this->render("importpatrones", ["model" => $model, "msg" => $msg]);
    }
    
    public function actionImportseries()
    {
        $model = new FormUpload;
        $msg = null;
 
        if ($model->load(Yii::$app->request->post()))
        {
            $model->file = UploadedFile::getInstances($model, 'file');

            if ($model->file && $model->validate()) 
            {
                foreach ($model->file as $file) 
                {
                    $file->saveAs('archivos/' . $file->baseName . '.' . $file->extension);
                    $msg = "<p><strong class='label label-info'>Felicitaciones! El archivo fue cargado con exito</strong></p>";
                }
            }
        }
        return $this->render("importseries", ["model" => $model, "msg" => $msg]);
    }
    
    public function actionListarusuarios()
    { 
        $form = new FormSearch;
        $search = null;
        if($form->load(Yii::$app->request->get()))
        {
            if ($form->validate())
            {
                $search = Html::encode($form->q);
                $table = Users::find()
                        ->where(["like", "id", $search])
                        ->orWhere(["like", "username", $search])
                        ->orWhere(["like", "email", $search]);
                $count = clone $table;
                $pages = new Pagination([
                    "pageSize" => 1,
                    "totalCount" => $count->count()
                ]);
                $model = $table
                        ->offset($pages->offset)
                        ->limit($pages->limit)
                        ->all();
            }
            else
            {
                $form->getErrors();
            }
        }
        else
        {
            $table = Users::find();
            $count = clone $table;
            $pages = new Pagination([
                "pageSize" => 4,
                "totalCount" => $count->count(),
            ]);
            $model = $table
                    ->offset($pages->offset)
                    ->limit($pages->limit)
                    ->all();
        }    
        
        return $this->render("listarusuarios", ["model" => $model, "form" => $form, "search" => $search, "pages" => $pages]);
        /*
        $table = new Users;
        $model = $table->find()->all();
        
        $form = new FormSearch;
        $search = null;
        if($form->load(Yii::$app->request->get()))
        {
            if ($form->validate())
            {
                $search = Html::encode($form->q);
                $query = "SELECT * FROM users WHERE id LIKE '%$search%' OR ";
                $query .= "username LIKE '%$search%' OR email LIKE '%$search%'";
                $model = $table->findBySql($query)->all();
            }
            else
            {
                $form->getErrors();
            }
        }
        return $this->render("listarusuarios", ["model" => $model, "form" => $form, "search" => $search]);
         */
    }
    
    public function actionDelete()
    {
        if(Yii::$app->request->post())
        {
            $id = Html::encode($_POST["id"]);
            if((int) $id)
            {
                if(Users::deleteAll("id=:id", [":id" => $id]))
                {
                    echo "Usuario con id $id eliminado con éxito, redireccionando ...";
                    echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("site/listarusuarios")."'>";
                }
                else
                {
                    echo "Ha ocurrido un error al eliminar el usuario, redireccionando ...";
                    echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("site/listarusuarios")."'>"; 
                }
            }
            else
            {
                echo "Ha ocurrido un error al eliminar el usuario, redireccionando ...";
                echo "<meta http-equiv='refresh' content='3; ".Url::toRoute("site/listarusuarios")."'>";
            }
        }
        else
        {
            return $this->redirect(["site/listarusuarios"]);
        }
    }
    
    
    public function actionUpdate()
    {
        $model = new FormActualizar;
        $msg = null;
        
        if($model->load(Yii::$app->request->post()))
        {
            if($model->validate())
            {
                $table = Users::findOne($model->id);
                if($table)
                {
                    $table->username = $model->username;
                    $table->email = $model->email;
                    $table->role = $model->role;
                    if ($table->update())
                    {
                        $msg = "El Usuario ha sido actualizado correctamente";
                    }
                    else
                    {
                        $msg = "El Usuario no ha podido ser actualizado";
                    }
                }
                else
                {
                    $msg = "El Usuario seleccionado no ha sido encontrado";
                }
            }
            else
            {
                $model->getErrors();
            }
        }
        
        
        if (Yii::$app->request->get("id"))
        {
            $id = Html::encode($_GET["id"]);
            if ((int) $id)
            {
                $table = Users::findOne($id);
                if($table)
                {
                    $model->id = $table->id;
                    $model->username= $table->username;
                    $model->email = $table->email;
                    $model->role = $table->role;
                }
                else
                {
                    return $this->redirect(["site/listarusuarios"]);
                }
            }
            else
            {
                return $this->redirect(["site/listarusuarios"]);
            }
        }
        else
        {
            return $this->redirect(["site/listarusuarios"]);
        }
        return $this->render("update", ["model" => $model, "msg" => $msg]);
    }
     
    public function actionVencimiento3meses()
    { 
       
        $conn = sqlsrv_connect($this->serverName,$this->connectionInfo);
        
        if($conn){
            $msg = 'Connection establised<br />';
        }else{
            $msg = 'Connection failure<br />';
            die(print_r(sqlsrv_errors(),TRUE));
        }
        
        $sql = 'With t1 as (

                SELECT pp.IdProducto,pp.CodProducto, pp.NomProducto, plote.fecInvima, plote.idTercero
                FROM [dbo].[proProducto] as pp join [dbo].[proLotePlantilla] as plote
                ON pp.IdProducto = plote.idProducto

                ), t2 as (

                Select t1.*, ik.canCorte, ik.IdKardexCorte, ik.IdUbicacion, ik.IdProductoLote
                from t1 join [dbo].[invKardexCorteDet] as ik
                ON  t1.IdProducto = ik.idProducto 
                where ik.idProductoTipo=1

                ), t3 as (

                Select t2.*, invk.FecCorte,datediff(MONTH, invk.FecCorte,t2.fecInvima) as MesesFaltantes
                from t2 join [dbo].[invKardexCorte] as invk
                ON t2.IdKardexCorte = invk.IdKardexCorte
                ), t4 as (

                select *
                from t3
                where MesesFaltantes between 0 and 3 AND FecCorte in (select max (FecCorte) from invKardexCorte)
                ), t5 as (

                select t4.*, ge.IdBodega
                from t4 join [dbo].[genUbicacion] as ge
                on t4.IdUbicacion = ge.IdUbicacion

                ), t6 as (

                select t5.*, gb.NomBodega
                from t5 join [dbo].[genBodega] as gb
                on t5.IdBodega = gb.IdBodega
                ), t7 as (

                select t6.*,ppl.idLote ,ppl.Lote
                from t6 join [dbo].[proProductoLote] as ppl
                on t6.IdProductoLote = ppl.IdProductoLote
                )

                select t7.idProducto, t7.CodProducto, t7.Nomproducto, cast(t7.fecInvima as varchar) as fecInvima,g.NomTercero, cast(t7.FecCorte as varchar) as FecCorte, t7.MesesFaltantes,
                t7.idBodega, t7.NomBodega, t7.idLote, t7.Lote
                from t7 join [dbo].[genTercero] as g
                on t7.idTercero = g.idTercero
                group by t7.idProducto, t7.CodProducto, t7.Nomproducto, t7.fecInvima,g.NomTercero, t7.FecCorte, t7.MesesFaltantes,
                t7.idBodega, t7.NomBodega, t7.idLote, t7.Lote
                order by Nomproducto';
        
        $stmt = sqlsrv_query($conn,$sql,array(),array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        $msg2 = 'a';
        if($stmt==false){
            $msg2 = 'Error to retrieve info <br />';
            die(print_r(sqlsrv_errors(),TRUE));
        }

        $arreglo = array(
            "idProducto" => null,
            "CodProducto" => null,
            "Nomproducto" => null,
            "fecInvima" => null,
            "NomTercero" => null,
            "FecCorte" => null,
            "MesesFaltantes" => null,
            "idBodega" => null,
            "NomBodega" => null,
            "idLote" => null,
            "Lote" => null
        );
        
        $row_count = sqlsrv_num_rows( $stmt );
        $a=0;
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            $arreglo["idProducto"][$a] = $row['idProducto'];
            $arreglo["CodProducto"][$a] = $row['CodProducto'];
            $arreglo["Nomproducto"][$a] = $row['Nomproducto'];
            $arreglo["fecInvima"][$a] = $row['fecInvima'];
            $arreglo["NomTercero"][$a] = $row['NomTercero'];
            $arreglo["FecCorte"][$a] = $row['FecCorte'];
            $arreglo["MesesFaltantes"][$a] = $row['MesesFaltantes'];
            $arreglo["idBodega"][$a] = $row['idBodega'];
            $arreglo["NomBodega"][$a] = $row['NomBodega'];
            $arreglo["idLote"][$a] = $row['idLote'];
            $arreglo["Lote"][$a] = $row['Lote'];

            $a++;
        }

        sqlsrv_close($conn);
        return $this->render('vencimiento3meses',["msg" => $msg, "msg2" => $msg2, "a"=> $a, "arreglo"=>$arreglo]);
    }
    
    
    public function actionVencimiento6meses()
    { 
       
        $conn = sqlsrv_connect($this->serverName,$this->connectionInfo);
        
        if($conn){
            $msg = 'Connection establised<br />';
        }else{
            $msg = 'Connection failure<br />';
            die(print_r(sqlsrv_errors(),TRUE));
        }
        
        $sql = 'With t1 as (

                SELECT pp.IdProducto,pp.CodProducto, pp.NomProducto, plote.fecInvima, plote.idTercero
                FROM [dbo].[proProducto] as pp join [dbo].[proLotePlantilla] as plote
                ON pp.IdProducto = plote.idProducto

                ), t2 as (

                Select t1.*, ik.canCorte, ik.IdKardexCorte, ik.IdUbicacion, ik.IdProductoLote
                from t1 join [dbo].[invKardexCorteDet] as ik
                ON  t1.IdProducto = ik.idProducto 
                where ik.idProductoTipo=1

                ), t3 as (

                Select t2.*, invk.FecCorte,datediff(MONTH, invk.FecCorte,t2.fecInvima) as MesesFaltantes
                from t2 join [dbo].[invKardexCorte] as invk
                ON t2.IdKardexCorte = invk.IdKardexCorte
                ), t4 as (

                select *
                from t3
                where MesesFaltantes between 4 and 6 AND FecCorte in (select max (FecCorte) from invKardexCorte)
                ), t5 as (

                select t4.*, ge.IdBodega
                from t4 join [dbo].[genUbicacion] as ge
                on t4.IdUbicacion = ge.IdUbicacion

                ), t6 as (

                select t5.*, gb.NomBodega
                from t5 join [dbo].[genBodega] as gb
                on t5.IdBodega = gb.IdBodega
                ), t7 as (

                select t6.*,ppl.idLote ,ppl.Lote
                from t6 join [dbo].[proProductoLote] as ppl
                on t6.IdProductoLote = ppl.IdProductoLote
                )

                select t7.idProducto, t7.CodProducto, t7.Nomproducto, cast(t7.fecInvima as varchar) as fecInvima,g.NomTercero, cast(t7.FecCorte as varchar) as FecCorte, t7.MesesFaltantes,
                t7.idBodega, t7.NomBodega, t7.idLote, t7.Lote
                from t7 join [dbo].[genTercero] as g
                on t7.idTercero = g.idTercero
                group by t7.idProducto, t7.CodProducto, t7.Nomproducto, t7.fecInvima,g.NomTercero, t7.FecCorte, t7.MesesFaltantes,
                t7.idBodega, t7.NomBodega, t7.idLote, t7.Lote
                order by Nomproducto';
        
        $stmt = sqlsrv_query($conn,$sql,array(),array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        $msg2 = 'a';
        if($stmt==false){
            $msg2 = 'Error to retrieve info <br />';
            die(print_r(sqlsrv_errors(),TRUE));
        }

        $arreglo = array(
            "idProducto" => null,
            "CodProducto" => null,
            "Nomproducto" => null,
            "fecInvima" => null,
            "NomTercero" => null,
            "FecCorte" => null,
            "MesesFaltantes" => null,
            "idBodega" => null,
            "NomBodega" => null,
            "idLote" => null,
            "Lote" => null
        );
        
        $row_count = sqlsrv_num_rows( $stmt );
        $a=0;
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            $arreglo["idProducto"][$a] = $row['idProducto'];
            $arreglo["CodProducto"][$a] = $row['CodProducto'];
            $arreglo["Nomproducto"][$a] = $row['Nomproducto'];
            $arreglo["fecInvima"][$a] = $row['fecInvima'];
            $arreglo["NomTercero"][$a] = $row['NomTercero'];
            $arreglo["FecCorte"][$a] = $row['FecCorte'];
            $arreglo["MesesFaltantes"][$a] = $row['MesesFaltantes'];
            $arreglo["idBodega"][$a] = $row['idBodega'];
            $arreglo["NomBodega"][$a] = $row['NomBodega'];
            $arreglo["idLote"][$a] = $row['idLote'];
            $arreglo["Lote"][$a] = $row['Lote'];

            $a++;
        }

        sqlsrv_close($conn);
        return $this->render('vencimiento6meses',["msg" => $msg, "msg2" => $msg2, "a"=> $a, "arreglo"=>$arreglo]);
    }

    public function actionEjemplo()
    {

        $conn = sqlsrv_connect($this->serverName,$this->connectionInfo);
        
        if($conn){
            $msg = 'Connection establised<br />';
        }else{
            $msg = 'Connection failure<br />';
            die(print_r(sqlsrv_errors(),TRUE));
        }
        
        $sql = 'SELECT IdBodega, CodBodega, NomBodega
                FROM genBodega';
        
        $stmt = sqlsrv_query($conn,$sql,array(),array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        $msg2 = 'a';
        if($stmt==false){
            $msg2 = 'Error to retrieve info <br />';
            die(print_r(sqlsrv_errors(),TRUE));
        }

        $arreglo = array(
            "IdBodega" => null,
            "CodBodega" => null,
            "NomBodega" => null
        );
        
        $row_count = sqlsrv_num_rows( $stmt );
        $a=0;
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            $arreglo["IdBodega"][$a] = $row['IdBodega'];
            $arreglo["CodBodega"][$a] = $row['CodBodega'];
            $arreglo["NomBodega"][$a] = $row['NomBodega'];
        
            $a++;
        }

        sqlsrv_close($conn);
        return $this->render('ejemplo',["msg" => $msg, "msg2" => $msg2, "row_count"=> $row_count, "arreglo"=>$arreglo]);    
    }
    
    public function actionPronosticos1()
    {
        $model = simplexml_load_file('C:\xampp\htdocs\AppWebTGFarmacia\web\archivos\students.xml');
        $total_alumnos = count($model->student);
        $pages = new Pagination([
            "pageSize" =>1,
            "totalCount" => $total_alumnos,
        ]);
        return $this->render('pronosticos1',["model" => $model, "pages"=>$pages]);
    }
    
    
    public function actionRotacioninv()
    { 
       
        $conn = sqlsrv_connect($this->serverName,$this->connectionInfo);
        
        if($conn){
            $msg = 'Connection establised<br />';
        }else{
            $msg = 'Connection failure<br />';
            die(print_r(sqlsrv_errors(),TRUE));
        }
        
        $sql = 'with t1 as (
                select pp.IdProducto, pp.CodProducto, rtrim(ltrim(pp.NomProducto)) as NomProducto, ceiling(ventas.cantidad) as Ventas, ventas.Annio, ventas.mes
                from [dbo].[invSISMEDVentas] as ventas join  [dbo].[proProducto] as pp
                on ventas.Codigo = pp.CodProducto
                WHERE ventas.Annio between 2012 and year(getdate())
                ), t2 as (

                select t1.*, (t1.ventas * cum.FactorEmpaque) as unidades 
                from t1 join [dbo].[proMedicamentoCUM] as cum
                on t1.CodProducto = cum.CodProducto
                ), t3 as (

                select t2.NomProducto, sum(t2.unidades) as ventas, avg(i.ValCosto) as ValCosto, sum((unidades*i.ValCosto)) as CostoTotal, t2.Annio, t2.mes
                from t2 join [dbo].[invBodegaProducto] as i
                on t2.IdProducto = i.IdProducto
                group by NomProducto, t2.Annio, t2.mes
                ), t4 as (

                select t3.Annio, t3.Mes, isnull(sum(CostoTotal),0) as CostoTotal
                from t3
                group by t3.Annio, t3.mes

                ), t5 as (

                SELECT avg(CanCorte*ValCosto) as promMedInv, year(ik.FecCorte) as anio, month(ik.FecCorte) as mes
                FROM [dbo].[invKardexCorteDet] as i join [dbo].[invKardexCorte] as ik
                on i.IdKardexCorte = ik.IdKardexCorte
                where (year(ik.FecCorte) between 2012 and year(getdate())) AND (month(ik.FecCorte) between 1 and 12)
                group by year(ik.FecCorte),month(ik.FecCorte)

                )

                select t4.Annio, t4.Mes, 
                CAST(CONVERT(varchar, CAST(round(t4.CostoTotal,2) AS money), 1) AS varchar) as CostoTotalVendido, 
                CAST(CONVERT(varchar, CAST(round(t5.promMedInv,2) AS money), 1) AS varchar) as promMedInv, 
                CAST(CONVERT(varchar, CAST(round((t4.CostoTotal/ t5.promMedInv),2) AS money), 1) AS varchar) as IndiceRotacionMedicamentos
                from t4 join t5
                on t4.Annio = t5.anio AND t4.Mes = t5.mes
                order by t4.Annio, t4.Mes';
        
        $stmt = sqlsrv_query($conn,$sql,array(),array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        $msg2 = 'a';
        if($stmt==false){
            $msg2 = 'Error to retrieve info <br />';
            die(print_r(sqlsrv_errors(),TRUE));
        }

        $arreglo = array(
            "Annio" => null,
            "Mes" => null,
            "CostoTotalVendido" => null,
            "promMedInv" => null,
            "IndiceRotacionMedicamentos" => null,
        );
        
        $row_count = sqlsrv_num_rows( $stmt );
        $a=0;
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            $arreglo["Annio"][$a] = $row['Annio'];
            $arreglo["Mes"][$a] = $row['Mes'];
            $arreglo["CostoTotalVendido"][$a] = $row['CostoTotalVendido'];
            $arreglo["promMedInv"][$a] = $row['promMedInv'];
            $arreglo["IndiceRotacionMedicamentos"][$a] = $row['IndiceRotacionMedicamentos'];

            $a++;
        }

        sqlsrv_close($conn);
        return $this->render('rotacioninv',["msg" => $msg, "msg2" => $msg2, "a"=> $a, "arreglo"=>$arreglo]);
    }
    
    public function actionCostoalmacenamiento()
    { 
      
        $conn = sqlsrv_connect($this->serverName,$this->connectionInfo);
        
        if($conn){
            $msg = 'Connection establised<br />';
        }else{
            $msg = 'Connection failure<br />';
            die(print_r(sqlsrv_errors(),TRUE));
        }
        
        $sql = 'with t1 as (
                select pp.IdProducto, pp.CodProducto, rtrim(ltrim(pp.NomProducto)) as NomProducto, ceiling(ventas.cantidad) as Ventas, ventas.Annio, ventas.mes
                from [dbo].[invSISMEDVentas] as ventas join  [dbo].[proProducto] as pp
                on ventas.Codigo = pp.CodProducto
                WHERE ventas.Annio between 2012 and year(getdate())
                ), t2 as (

                select t1.*, (t1.ventas * cum.FactorEmpaque) as unidades 
                from t1 join [dbo].[proMedicamentoCUM] as cum
                on t1.CodProducto = cum.CodProducto
                ), t3 as (

                select t2.NomProducto, sum(t2.unidades) as ventas, avg(i.ValCosto) as ValCosto, sum((unidades*i.ValCosto)) as CostoTotal, t2.Annio, t2.mes
                from t2 join [dbo].[invBodegaProducto] as i
                on t2.IdProducto = i.IdProducto
                group by NomProducto, t2.Annio, t2.mes
                ), t4 as (

                select t3.Annio, t3.Mes, isnull(sum(CostoTotal),0) as CostoTotal
                from t3
                group by t3.Annio, t3.mes

                ), t5 as (

                SELECT avg(CanCorte*ValCosto) as promInvDinero,avg(CanCorte) as UniAlmacenadasProm, year(ik.FecCorte) as anio, month(ik.FecCorte) as mes
                FROM [dbo].[invKardexCorteDet] as i join [dbo].[invKardexCorte] as ik
                on i.IdKardexCorte = ik.IdKardexCorte
                where (year(ik.FecCorte) between 2012 and year(getdate())) AND (month(ik.FecCorte) between 1 and 12)
                group by year(ik.FecCorte),month(ik.FecCorte)

                )


                select t4.Annio, t4.Mes, 
                CAST(CONVERT(varchar, CAST(round(t5.promInvDinero,2) AS money), 1) AS varchar) as promInvDinero, 
                CAST(CONVERT(varchar, CAST(round(t5.UniAlmacenadasProm,2) AS money), 1) AS varchar) as UniAlmacenadasProm, 
                CAST(CONVERT(varchar, CAST(round((t5.promInvDinero/ t5.UniAlmacenadasProm),2) AS money), 1) AS varchar) as CostoAlmaUni
                from t4 join t5
                on t4.Annio = t5.anio AND t4.Mes = t5.mes
                order by t4.Annio, t4.Mes';
        
        $stmt = sqlsrv_query($conn,$sql,array(),array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        $msg2 = 'a';
        if($stmt==false){
            $msg2 = 'Error to retrieve info <br />';
            die(print_r(sqlsrv_errors(),TRUE));
        }

        $arreglo = array(
            "Annio" => null,
            "Mes" => null,
            "promInvDinero" => null,
            "UniAlmacenadasProm" => null,
            "CostoAlmaUni" => null,
        );
        
        $row_count = sqlsrv_num_rows( $stmt );
        $a=0;
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            $arreglo["Annio"][$a] = $row['Annio'];
            $arreglo["Mes"][$a] = $row['Mes'];
            $arreglo["promInvDinero"][$a] = $row['promInvDinero'];
            $arreglo["UniAlmacenadasProm"][$a] = $row['UniAlmacenadasProm'];
            $arreglo["CostoAlmaUni"][$a] = $row['CostoAlmaUni'];

            $a++;
        }

        sqlsrv_close($conn);
        return $this->render('costoalmacenamiento',["msg" => $msg, "msg2" => $msg2, "a"=> $a, "arreglo"=>$arreglo]);
    }
    private function downloadFile($dir, $file, $extensions=[])
    {
     if (is_dir($dir))
     {
      $path = $dir.$file;
      if (is_file($path))
      {
       $file_info = pathinfo($path);
       $extension = $file_info["extension"];

       if (is_array($extensions))
       {
        foreach($extensions as $e)
        {
         if ($e === $extension)
         {
          $size = filesize($path);
          header("Content-Type: application/force-download");
          header("Content-Disposition: attachment; filename=$file");
          header("Content-Transfer-Encoding: binary");
          header("Content-Length: " . $size);
          readfile($path);
          return true;
         }
        }
       }

      }
     }
     return false;
    }
    
    public function actionDescargar()
    {
        if (Yii::$app->request->get("file"))
        {
         if (!$this->downloadFile("archivos/", Html::encode($_GET["file"]), ["xml", "XML", "xls", "xlsx"]))
         {
          Yii::$app->session->setFlash("errordownload");
         }
        } 
        return $this->render('descargar');
    }
    
    public function actionForecasting()
    {
        require_once 'C:/xampp/php/ext/PHPExcel-1.8.0/Classes/PHPExcel/IOFactory.php';
        $objPHPExcel = \PHPExcel_IOFactory::load('archivos/Forecasting.xlsx');
        $objHoja = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        
        return $this->render('forecasting',["objHoja"=>$objHoja]);
    }
    
    public function actionReglas()
    {
        require_once 'C:/xampp/php/ext/PHPExcel-1.8.0/Classes/PHPExcel/IOFactory.php';
        $objPHPExcel = \PHPExcel_IOFactory::load('archivos/ItemSetReglas.xls');
        $objHoja = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
        
        return $this->render('reglas',["objHoja"=>$objHoja]);
    }
    
    public function actionPareto()
    {
        $conn = sqlsrv_connect($this->serverName,$this->connectionInfo);
        
        if($conn){
            $msg = 'Connection establised<br />';
        }else{
            $msg = 'Connection failure<br />';
            die(print_r(sqlsrv_errors(),TRUE));
        }
        
        $sql = 'with t1 as (
                select pp.IdProducto, pp.CodProducto, rtrim(ltrim(pp.NomProducto)) as NomProducto, ceiling(ventas.cantidad) as Ventas
                from [dbo].[invSISMEDVentas] as ventas join  [dbo].[proProducto] as pp
                on ventas.Codigo = pp.CodProducto
                WHERE ventas.Annio = 2012 OR  ventas.Annio = 2013 OR ventas.Annio = 2014
                ), t2 as (

                select t1.*, (t1.ventas * cum.FactorEmpaque) as unidades 
                from t1 join [dbo].[proMedicamentoCUM] as cum
                on t1.CodProducto = cum.CodProducto
                ), t3 as (

                        select t2.NomProducto, sum(t2.unidades) as ventas, avg(i.ValCosto) as ValCosto, sum((unidades*i.ValCosto)) as CostoTotal
                        from t2 join [dbo].[invBodegaProducto] as i
                        on t2.IdProducto = i.IdProducto
                        group by NomProducto
                ), t4 as (

                        select t3.NomProducto, t3.ventas, t3.ValCosto, t3.CostoTotal, (select sum(t3.CostoTotal) from t3) as suma
                        from t3

                )

                select t4.NomProducto,
                CAST(CONVERT(varchar,CAST(isnull(t4.ventas,0)AS money),1)AS varchar) as ventas,
                CAST(CONVERT(varchar,CAST(isnull(t4.ValCosto, 0)AS money),1)AS varchar) as costoU,
                CAST(CONVERT(varchar,CAST(isnull(t4.CostoTotal,0)AS money),1)AS varchar) as CostoTotal,
                isnull(round(((t4.CostoTotal/t4.suma)*100),6),0)as PorcentajeValorTotal
                from t4
                order by PorcentajeValorTotal desc';
        
        $stmt = sqlsrv_query($conn,$sql,array(),array( "Scrollable" => SQLSRV_CURSOR_KEYSET ));
        $msg2 = 'a';
        if($stmt==false){
            $msg2 = 'Error to retrieve info <br />';
            die(print_r(sqlsrv_errors(),TRUE));
        }

        $arreglo = array(
            "NomProducto" => null,
            "ventas" => null,
            "CostoU" => null,
            "CostoTotal" => null,
            "PorcentajeValorTotal" => null,
        );
        
        $row_count = sqlsrv_num_rows( $stmt );
        $a=0;
        while( $row = sqlsrv_fetch_array( $stmt, SQLSRV_FETCH_ASSOC) ) {
            $arreglo["NomProducto"][$a] = $row['NomProducto'];
            $arreglo["ventas"][$a] = $row['ventas'];
            $arreglo["costoU"][$a] = $row['costoU'];
            $arreglo["CostoTotal"][$a] = $row['CostoTotal'];
            $arreglo["PorcentajeValorTotal"][$a] = $row['PorcentajeValorTotal'];

            $a++;
        }

        sqlsrv_close($conn);
        return $this->render('pareto',["msg" => $msg, "msg2" => $msg2, "a"=> $a, "arreglo"=>$arreglo]);
    }
}

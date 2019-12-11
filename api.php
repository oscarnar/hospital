<?php
require 'connection.php';
//define('PATH', 'http://'.$_SERVER['HTTP_HOST'].'/test/images/');
//define('PATH', '/home/damon/');
//define('NO_IMAGE_PATH', PATH.'no_image.png');
$action = $_REQUEST['action'];
/*$header = apache_request_headers();
if ($header['user']=='admin' && $header['password']=='admin') {
        
}else{
        $jsonOutput['meta']['status']  = 'error';
        $jsonOutput['meta']['code']    = '200';
        $jsonOutput['meta']['message'] = 'You are not authorized to do.';        
        header('Content-type: application/json');
        echo json_encode($jsonOutput);
        exit;
}*/

switch ($action) {
case 'login':
    //login($_REQUEST,$con);
         if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                 login($_REQUEST,$con);
         }else{
                $jsonOutput['meta']['status']  = 'error';
                $jsonOutput['meta']['code']    = '200';
                $jsonOutput['meta']['message'] = 'Invalid Action';        
                header('Content-type: application/json');
                echo json_encode($jsonOutput);
         }
		
	break;

case 'register':
    register($_REQUEST,$con);
        /*if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                 register($_REQUEST,$con);
         }else{
                $jsonOutput['meta']['status']  = 'error';
                $jsonOutput['meta']['code']    = '200';
                $jsonOutput['meta']['message'] = 'Invalid Action';        
                header('Content-type: application/json');
                echo json_encode($jsonOutput);
         }*/
		
        break;
case 'historial':
    historial($_REQUEST,$con);
    /*if($_SERVER['REQUEST_METHOD'] == 'POST'){
        historial($_REQUEST,$con);
    }else{
        $jsonOutput['meta']['status']  = 'error';
        $jsonOutput['meta']['code']    = '200';
        $jsonOutput['meta']['message'] = 'Invalid Action';        
        header('Content-type: application/json');
        echo json_encode($jsonOutput);
    }*/
    break;
case 'cita':
    cita($_REQUEST,$con);
    /*if($_SERVER['REQUEST_METHOD'] == 'POST'){
        cita($_REQUEST,$con);
    }else{
        $jsonOutput['meta']['status']  = 'error';
        $jsonOutput['meta']['code']    = '200';
        $jsonOutput['meta']['message'] = 'Invalid Action';        
        header('Content-type: application/json');
        echo json_encode($jsonOutput);
    }*/
    break;
case 'addhistorial':
    addhistorial($_REQUEST,$con);
    /*if($_SERVER['REQUEST_METHOD'] == 'POST'){
        addhistorial($_REQUEST,$con);
    }else{
        $jsonOutput['meta']['status']  = 'error';
        $jsonOutput['meta']['code']    = '200';
        $jsonOutput['meta']['message'] = 'Invalid Action';        
        header('Content-type: application/json');
        echo json_encode($jsonOutput);
    }*/
    break;
			
	default:	
		$jsonOutput['meta']['status']  = 'error';
                $jsonOutput['meta']['code']    = '200';
                $jsonOutput['meta']['message'] = 'Invalid Action';        
                header('Content-type: application/json');
                echo json_encode($jsonOutput);
                exit;
        break;
}
function addhistorial($request,$con){
    $dni = $request['dni'];
    $dolencia = $request['dolencia'];
    $fecha = $request['fecha'];
    $descripcion = $request['descripcion'];
    $msg = "";
    if(empty($dni)){
        $msg = 'Por favor vuelva a iniciar secion.';
    }
    if(empty($msg)){
        $result = mysqli_query($con,"SELECT dni FROM usuario where dni='$dni'");
        if($result->num_rows > 0){
            $insert = mysqli_query($con,"INSERT INTO historial values (null,'$dni','$dolencia','$fecha','$descripcion')");
            $histo = mysqli_query($con,"select * from historial where dni='$dni' and fecha='$fecha'");
            if ($histo->num_rows == 0) {
                $msg = "Registro fallido al insertar: ".mysqli_error($con);
                $jsonOutput['meta']['status']  = 'error';
                $jsonOutput['meta']['code']    = '200';
                $jsonOutput['meta']['message'] = $msg;
                $jsonOutput['data'] = (object)array();
                header('Content-type: application/json');
                echo json_encode($jsonOutput);
                exit;
            }
            $row = mysqli_fetch_assoc($histo);       
            $jsonOutput['meta']['status']  = 'success';
            $jsonOutput['meta']['code']    = '200';
            $jsonOutput['meta']['message'] = 'Registro de historial exitoso.';
            $jsonOutput['data'] = (object)$row;
            header('Content-type: application/json');
            echo json_encode($jsonOutput);
            exit;
        }
    }else{
        $jsonOutput['meta']['status']  = 'error';
        $jsonOutput['meta']['code']    = '200';
        $jsonOutput['meta']['message'] = $msg;
        $jsonOutput['data'] = (object)array();
        header('Content-type: application/json');
        echo json_encode($jsonOutput);
        exit;
    }
}
function historial($request,$con){
    $dni = $request['dni'];
    $msg = '';
    if(empty($dni)){
        $msg = 'Por favor debe ingresar el DNI del paciente.';
    }
    if(empty($msg)){
        $result = mysqli_query($con,"SELECT * FROM usuario where dni=$dni");
        if($result->num_rows > 0){
            $histo = mysqli_query($con,"SELECT * FROM historial where dni=$dni");
            if($histo->num_rows > 0){
                $jsonOutput['meta']['status']  = 'success';
                $jsonOutput['meta']['code']    = '200';
                $jsonOutput['meta']['message'] = 'Paciente encontrado.';
                $conta = 0;
                while($row = mysqli_fetch_assoc($histo)){
                    $jsonOutput[$conta]=$row;
                    $conta++;
                }
                //$jsonOutput['data'] = $row;

                header('Content-type: application/json');
                echo json_encode($jsonOutput);
                exit;
            }else{
                $jsonOutput['meta']['status']  = 'success';
                $jsonOutput['meta']['code']    = '200';
                $jsonOutput['meta']['message'] = 'El paciente tiene el historial vacio.';
                $jsonOutput['data'] = $row;

                header('Content-type: application/json');
                echo json_encode($jsonOutput);
                exit;
            }
        }else{
            $jsonOutput['meta']['status']  = 'error';
            $jsonOutput['meta']['code']    = '200';
            $jsonOutput['meta']['message'] = 'Paciente no encontrado';
            $jsonOutput['data'] = $row;

            header('Content-type: application/json');
            echo json_encode($jsonOutput);
            exit;
        }
    }else{
        $jsonOutput['meta']['status']  = 'error';
        $jsonOutput['meta']['code']    = '200';
        $jsonOutput['meta']['message'] = $msg;
        $jsonOutput['data'] = (object)array();
        header('Content-type: application/json');
        echo json_encode($jsonOutput);
        exit;
    }
}
function cita($request,$con){
    $dni = $request['dni'];
    $fecha = $request['fecha'];
    $msg = "";
    if(empty($dni)){
        $msg = 'Por favor vuelva a iniciar secion.';
    }
    if(empty($msg)){
        $result = mysqli_query($con,"SELECT dni FROM usuario where dni='$dni'");
        if($result->num_rows > 0){
            $insert = mysqli_query($con,"INSERT INTO cita values (null,0,0,'$dni','$fecha',00-00-00)");
            $histo = mysqli_query($con,"select paciente_id from cita where paciente_id='$dni' and fecha='$fecha'");
            if ($histo->num_rows == 0) {
                $msg = "Registro fallido al insertar: ".mysqli_error($con);
                $jsonOutput['meta']['status']  = 'error';
                $jsonOutput['meta']['code']    = '200';
                $jsonOutput['meta']['message'] = $msg;
                $jsonOutput['data'] = (object)array();
                header('Content-type: application/json');
                echo json_encode($jsonOutput);
                exit;
            }
            $row = mysqli_fetch_assoc($histo);       
            $jsonOutput['meta']['status']  = 'success';
            $jsonOutput['meta']['code']    = '200';
            $jsonOutput['meta']['message'] = 'Su cita se genero exitosamente.';
            $jsonOutput['data'] = (object)$row;
            header('Content-type: application/json');
            echo json_encode($jsonOutput);
            exit;
        }
    }else{
        $jsonOutput['meta']['status']  = 'error';
        $jsonOutput['meta']['code']    = '200';
        $jsonOutput['meta']['message'] = $msg;
        $jsonOutput['data'] = (object)array();
        header('Content-type: application/json');
        echo json_encode($jsonOutput);
        exit;
    }
}
function login($request,$con){
        $usuario = $request['nick'];
        $password = $request['pass'];

        $msg = '';
        if (empty($usuario)) {
                $msg='Por favor ingrese usuario.';
        }elseif (empty($password)) {
                $msg='Por favor ingrese password.';
        }
        if (empty($msg)) {
                $password = md5($password);
                $result = mysqli_query($con,"SELECT * FROM usuario where nick='$usuario' and pass='$password'");
                if ($result->num_rows>0) {
                        $row = mysqli_fetch_assoc($result);
                        /*if(file_exists("images/".$row['user_image'])){
                                $row['user_image'] = PATH.$row['user_image'];
                        }else{
                              $row['user_image'] = NO_IMAGE_PATH;  
                        }*/
                        $jsonOutput['meta']['status']  = 'success';
                        $jsonOutput['meta']['code']    = '200';
                        $jsonOutput['meta']['message'] = 'Login exitoso';
                        $jsonOutput['data'] = $row;

                        header('Content-type: application/json');
                        echo json_encode($jsonOutput);
                        exit;
                }
                else{
                        $resultUsuario = mysqli_query($con,"SELECT id_usr FROM usuario where nick='$usuario'");
                        $resultPassword = mysqli_query($con,"SELECT id_usr FROM usuario where pass='$password'");
                        if ($resultUsuario->num_rows <= 0 && $resultPassword->num_rows <= 0) {
                                $msg = "Usuario y password invalidos.";
                        }elseif ($resultUsuario->num_rows <= 0) {
                                $msg = "Usuario no correcto.";
                        }elseif ($resultPassword->num_rows <= 0) {
                                $msg = "Password incorrecto.";
                        }
                        $jsonOutput['meta']['status']  = 'error';
                        $jsonOutput['meta']['code']    = '200';
                        $jsonOutput['meta']['message'] = $msg;
                        $jsonOutput['data'] = (object)array();

                        header('Content-type: application/json');
                        echo json_encode($jsonOutput);
                        exit;
                }

        }else{
                $jsonOutput['meta']['status']  = 'error';
                $jsonOutput['meta']['code']    = '200';
                $jsonOutput['meta']['message'] = $msg;
                $jsonOutput['data'] = (object)array();
                header('Content-type: application/json');
                echo json_encode($jsonOutput);
                exit;   
        }
 }
function register($request,$con){
        $nick = $request['nick'];
        $pass = $request['pass'];
        $nombre = $request['nombre'];
        $departamento = $request['departamento'];
        $direccion = $request['direccion'];
        $telefono = $request['telefono'];
        $status = $request['status'];
        $dni = $request['dni'];
        //$file = $_FILES['user_image'];
        $msg = '';
        /*if (empty($file)) {
                $msg='Please upload your image.';
        }else*/if (empty($nick)) {
                $msg='Debe ingresar un usuario.';
        }elseif (empty($pass)) {
                $msg='Debe ingresar un password.';
        }elseif (empty($nombre)) {
                $msg='Debe ingresar un nombre.';
        }elseif (empty($dni)) {
                $msg='Debe ingresar su DNI.';
        }
        if (empty($msg)) {
                $resultNick = mysqli_query($con,"SELECT id_usr FROM usuario WHERE nick='$nick' or dni='$dni'");
                //$resultPass = mysqli_query($con,"SELECT id_usr FROM usuario WHERE email='".$pass."'");
                $msg = '';
                if ($resultNick->num_rows > 0) {
                        $msg = "El usuario ya existe.";
                }
                if (empty($msg)) {
                        //$user_image = $file['name'];
                        //$ext = explode('.', $user_image);
                        //$extention = $ext[count($ext)-1];

                        $pass = md5($pass);
                        $query = "INSERT INTO usuario VALUES(NULL,'".$nick."','".$pass."','".$status."')";
                        $insert = $con->query("insert into usuario values (null,'$nick','$pass','$status','$dni','$departamento','$nombre','$direccion','$telefono')") or die("database error: ".mysqli_error($con));//mysqli_query($con,$query);
                        $result = mysqli_query($con,"select * from usuario where nick='$nick'");
                        if ($result->num_rows == 0) {
                            $msg = "Registro fallido al insertar: ".mysqli_error($con);
                            $jsonOutput['meta']['status']  = 'error';
                            $jsonOutput['meta']['code']    = '200';
                            $jsonOutput['meta']['message'] = $msg;
                            $jsonOutput['data'] = (object)array();
                            header('Content-type: application/json');
                            echo json_encode($jsonOutput);
                            exit;
                        }
                        //$insert_id = $con->insert_id;
                        //$file_name = "user_image_".$insert_id.".".$extention;
                        //$target = "/home/damon/".$file_name;
                        //move_uploaded_file($_FILES['user_image']['tmp_name'], $target);
                        //$update = mysqli_query($con,"UPDATE users SET user_image='".$file_name."' where id=".$insert_id); 
                        $row = mysqli_fetch_assoc($result);       
                        $jsonOutput['meta']['status']  = 'success';
                        $jsonOutput['meta']['code']    = '200';
                        $jsonOutput['meta']['message'] = 'Registro exitoso.';
                        $jsonOutput['data'] = (object)$row;
                        header('Content-type: application/json');
                        echo json_encode($jsonOutput);
                        exit;
                }else{
                        $jsonOutput['meta']['status']  = 'error';
                        $jsonOutput['meta']['code']    = '200';
                        $jsonOutput['meta']['message'] = $msg;
                        $jsonOutput['data'] = (object)array();
                        header('Content-type: application/json');
                        echo json_encode($jsonOutput);
                        exit;
                }
	}else{
                $jsonOutput['meta']['status']  = 'error';
                $jsonOutput['meta']['code']    = '200';
                $jsonOutput['meta']['message'] = $msg;
                $jsonOutput['data'] = (object)array();
                header('Content-type: application/json');
                echo json_encode($jsonOutput);
                exit;   
        }
}
?>

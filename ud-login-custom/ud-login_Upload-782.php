<!DOCTYPE html>
<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link crossorigin="anonymous" media="all" rel="stylesheet" href="css/styles.css" />
<title>Sube tu archivo</title>
</head>
<body>
    <div class="form-container">
        <div class="form">
            <a href="./"><img src="https://www.caravaning.es/images/logotipo_caravaning-la-manga.png" class="logo" /></a>
            <div class="form-card">

<?php
//╔══════════════════════════════════════════════════════════════════════════════════════════════════════════╗
//║  Verificamos la IP de acesso
//║  La IP del Cliente
//╚══════════════════════════════════════════════════════════════════════════════════════════════════════════╝

function getUserIP()
{
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if (isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if (isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    return $ipaddress;
}

//╔══════════════════════════════════════════════════════════════════════════════════════════════════════════╗
//║  Protegemos la API verificando el Methodo utilizado y desde donde viene, impedimos que se aceda desde la
//║  URL de su sitio web
//╚══════════════════════════════════════════════════════════════════════════════════════════════════════════╝

$error_string = $error = $fileName = $error_string_logs = '';

if ( $_SERVER['REQUEST_METHOD'] != 'POST' ) {
    $error = 1;
    //echo "\n Error 1 - This method is not allowed or you are not authorized to access this API. Please refer to the documentation or contact the administrator.";
    $error_string = 'Este método no está permitido o no tiene autorización para acceder a esta API. Consulte la documentación o póngase en contacto con el administrador.';
    $error_string_logs = '405 Method Not Allowed';
    header("HTTP/1.0 405 Method Not Allowed");
} elseif ( !isset($_POST['Authorization-ud157']) ) {
    $error = 2;
    //echo "\n Error 2 - You do not have permission to access this API or your call is missing parameters.";
    $error_string = 'No tiene permiso para acceder a esta API o faltan parámetros en su llamada.';
    header("HTTP/1.0 203 Non-Authoritative Information");
    $error_string_logs = '203 Non-Authoritative Information';
} elseif ( $_POST['Authorization-ud157'] != 'AdminUser-ud_878' ) {
    $error = 3;
    //echo "\n Error 3 - You do not have permission to access this API, your username or password is not allowed from where the call to the API came from, please contact Admin for access.";
    $error_string = 'No tiene permiso para acceder a esta API, no se permite su nombre de usuario o contraseña desde donde provino la llamada a la API, comuníquese con el administrador para obtener acceso.';
    header("HTTP/1.0 401 Unauthorized");
    $error_string_logs = '401 Unauthorized';
} elseif ( empty( $_FILES['uploaded_file'] ) ) {
    $error = 4;
    $error_string = 'No se ha especificado ningún archivo. Vuelve a intentarlo.';
    $error_string_logs = 'File not found';
} else {
    $fileName = $_FILES['uploaded_file']['name'];
    $extension = strtolower ( pathinfo( $fileName , PATHINFO_EXTENSION ) );
    $filename_without_extension = str_replace( '.'.$extension, '', $fileName );
    $error = 0;
    $error_string = '';
    $error_string_logs = 'Success';
    if ( !$fileName ) {
        $error = 5;
        $error_string = 'No se ha seleccionado ningún archivo. Vuelve a intentarlo.';
        $error_string_logs = 'Filename empty';
    } else if ( $extension != 'pdf' || !strpos ( $_FILES['uploaded_file']['type'], 'pdf' ) ) {
        $error = 6;
        $error_string = 'Solamente se pueden subir archivos PDF. Vuelve a intentarlo.';
        $error_string_logs = 'File extension not allowed';
    } else if ( $_FILES['uploaded_file']['size'] > 10485760 ) {
        $error = 7;
        $error_string = 'Solamente se pueden subir archivos cuyo tamaño no supere los 10M. Vuelve a intentarlo.';
        $error_string_logs = 'File size not allowed';
    }
    /*
    echo 'extension: '.$extension.'<br />';
    echo 'filename_without_extension: '.$filename_without_extension.'<br />';
    echo 'name: '.$fileName.'<br />';
    echo 'size: '.$_FILES['uploaded_file']['size'].' - 10485760 = 10 MB (size is also in bytes)<br />';
    echo 'tmp_name: '.$_FILES['uploaded_file']['tmp_name'].'<br />';
    echo 'error: '.$_FILES['uploaded_file']['error'].'<br />';
    echo '<pre>';print_r($_FILES);echo '</pre>';
    */
    if (  function_exists ( 'mime_content_type' ) && $_FILES['uploaded_file']['tmp_name'] ) {
        $mimetype = mime_content_type( $_FILES['uploaded_file']['tmp_name'] );
        if ( !in_array( $mimetype, array( 'application/pdf' ) ) ) {
            $error = 8;
            $error_string = 'El tipo MIME del archivo seleccionado no está permitido. Vuelve a intentarlo.';
            $error_string_logs = 'Mime type not allowed';
        }
    }
    if ( !$error ) {
        $fileNameUpload = 'menu.'.$extension;
        $path = '../';
        $path = $path . basename( $fileNameUpload );
        if ( move_uploaded_file( $_FILES['uploaded_file']['tmp_name'], $path ) ) {
            $url = explode('/', $_SERVER['HTTP_REFERER']);
            array_pop($url);array_pop($url);
            $url = implode('/', $url).'/'; 
            $error_string = 'Se ha actualizado el archivo: <a href="'.$url.basename( $fileNameUpload ).'" target="_blank">'.$url.basename( $fileNameUpload ).'</a>.';
        } else{
            $error = 9;
            $error_string = 'Se produjo un error al cargar el archivo. Vuelve a intentarlo.';
            $error_string_logs = 'Upload error';
        }
    }
}
/*** LOGS ***/
$file = 'logs-Ud-687.txt';
$current = '';
$path = explode('/', $_SERVER['SCRIPT_FILENAME']);
array_pop($path);
$path = implode('/', $path).'/';
$path = $path.'logs_uD-815/';
if ( file_exists ( $path.$file ) ) {
    $current = file_get_contents( $path.$file );
}
$current .= date('Y-m-d H:i:s').",";
$current .= getUserIP() .",";
$current .= $fileName .",";
$current .= $error .",";
$current .= $error_string_logs;
$current .= "\r\n";
file_put_contents( $path.$file, $current );
?>
                <h2><?php if ( $error ) { echo ' Página de error'; } else { echo 'Sube tu archivo'; }?></h2>
                <?php if ( $error_string ) { ?>
                    <div class="dialog<?php if ( $error ) { echo ' warning'; }?>">
                        <?php echo $error_string;?>
                    </div><!-- .dialog -->
                <?php } ?>
                <a href="./">Volver a la página de carga</a>
            </div><!-- .form-card -->
        </div><!-- .form -->
    </div><!-- .form-container -->
</body>
</html>
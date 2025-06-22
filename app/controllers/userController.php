<?php
namespace app\controllers;
use app\models\mainModel;

class userController extends mainModel {
    # Controlador para registrar usuario #

    public function registrarUsuarioControlador() {
        # Almacenando Datos #
        $nombre = $this->limpiarCadena( $_POST[ 'usuario_nombre' ] );
        $apellido = $this->limpiarCadena( $_POST[ 'usuario_apellido' ] );
        $usuario = $this->limpiarCadena( $_POST[ 'usuario_usuario' ] );
        $email = $this->limpiarCadena( $_POST[ 'usuario_email' ] );
        $clave1 = $this->limpiarCadena( $_POST[ 'usuario_clave_1' ] );
        $clave2 = $this->limpiarCadena( $_POST[ 'usuario_clave_2' ] );

        #Verificando campos obligatorios#
        if ( $nombre == '' || $apellido == '' || $usuario == '' || $clave1 == '' || $clave2 == '' ) {
            $alerta = [
                'tipo' => 'simple',
                'titulo' => 'Error!',
                'texto'=>'Hay campos incompletos y son obligatorios',
                'icono'=>'error'
            ];
            return json_encode( $alerta );
            exit();
        }

        # Verificando los datos #

        if ( $this->verificarDatos( '[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}', $nombre ) ) {
            $alerta = [
                'tipo' => 'simple',
                'titulo' => 'Error!',
                'texto'=>'Error en formato',
                'icono'=>'error'
            ];
            return json_encode( $alerta );
            exit();
        }

        if ( $this->verificarDatos( '[a-zA-ZáéíóúÁÉÍÓÚñÑ ]{3,40}', $apellido ) ) {
            $alerta = [
                'tipo' => 'simple',
                'titulo' => 'Error!',
                'texto'=>'Error en formato',
                'icono'=>'error'
            ];
            return json_encode( $alerta );
            exit();
        }
        if ( $this->verificarDatos( '[a-zA-Z0-9]{4,20}', $usuario ) ) {
            $alerta = [
                'tipo' => 'simple',
                'titulo' => 'Error!',
                'texto'=>'Error en formato del campo usuario',
                'icono'=>'error'
            ];
            return json_encode( $alerta );
            exit();
        }

        if ( $this->verificarDatos( '[a-zA-Z0-9$@.-]{7,100}', $clave1 ) || $this->verificarDatos( '[a-zA-Z0-9$@.-]{7,100}', $clave2 ) ) {
            $alerta = [
                'tipo' => 'simple',
                'titulo' => 'Error!',
                'texto'=>'Las claves no respetan el formato solicitado',
                'icono'=>'error'
            ];
            return json_encode( $alerta );
            exit();
        }

        # Verificando Email #

        if ( $email != '' ) {
            if ( filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
                $check_email = $this->ejecutarConsulta( "SELECT usuario_email FROM usuario WHERE usuario_email='$email'" );
                if ( $check_email->rowCount()>0 ) {
                    $alerta = [
                        'tipo' => 'simple',
                        'titulo' => 'Error!',
                        'texto'=>'El email ya exixte!',
                        'icono'=>'error'
                    ];
                    return json_encode( $alerta );
                    exit();
                }
            } else {
                $alerta = [
                    'tipo' => 'simple',
                    'titulo' => 'Error!',
                    'texto'=>'Se ingresó un email inválido',
                    'icono'=>'error'
                ];
                return json_encode( $alerta );
                exit();
            }

        }

        #Verificando Claves #

        if ( $clave1 != $clave2 ) {
            $alerta = [
                'tipo' => 'simple',
                'titulo' => 'Error!',
                'texto'=>'Las claves no coinciden',
                'icono'=>'error'
            ];
            return json_encode( $alerta );
            exit();
        } else {
            $clave = password_hash( $clave1, PASSWORD_BCRYPT, [ 'cost'=>10 ] );
        }

        #Verificando Usuario

        $check_user = $this->ejecutarConsulta( "SELECT usuario_usuario FROM usuario WHERE usuario_usuario='$usuario'" );
        if ( $check_user->rowCount()>0 ) {
            $alerta = [
                'tipo' => 'simple',
                'titulo' => 'Error!',
                'texto'=>'El usuario ya exixte!',
                'icono'=>'error'
            ];
            return json_encode( $alerta );
            exit();
        }

        #Directorio de imágenes#

        $img_dir = '../views/fotos/';

        # Comprobar si hay imagenes para subir al servidor #

        if ( $_FILES[ 'usuario_foto' ][ 'name' ] = !'' && $_FILES[ 'usuario_foto' ][ 'size' ]>0 ) {
            #Crar directorio #
            if ( !file_exists( $img_dir ) ) {
                if ( !mkdir( $img_dir, 0777 ) ) {
                    $alerta = [
                        'tipo' => 'simple',
                        'titulo' => 'Error!',
                        'texto'=>'Error al crear el directorio!',
                        'icono'=>'error'
                    ];
                    return json_encode( $alerta );
                    exit();

                }
            }
            # Verificando formato de imagenes #

            if ( mime_content_type( $_FILES[ 'usuario_foto' ][ 'tmp_name' ] ) != 'image/jpeg' &&
            mime_content_type( $_FILES[ 'usuario_foto' ][ 'tmp_name' ] ) != 'image/jpg' &&
            mime_content_type( $_FILES[ 'usuario_foto' ][ 'tmp_name' ] ) != 'image/png' ) {
                $alerta = [
                    'tipo' => 'simple',
                    'titulo' => 'Error!',
                    'texto'=>'La imagen no tiene un formato permitido!',
                    'icono'=>'error'
                ];
                return json_encode( $alerta );
                exit();
            }

            # Verificando peso de la imágen #

            if ( ( $_FILES[ 'usuario_foto' ][ 'size' ] /1024 )>5120 ) {
                $alerta = [
                    'tipo' => 'simple',
                    'titulo' => 'Error!',
                    'texto'=>'La imagen tiene un peso superior al permitido',
                    'icono'=>'error'
                ];
                return json_encode( $alerta );
                exit();
            }

            #Nombre de la imagen#

            $foto=str_ireplace(" ","_",$nombre);
            $foto=$foto."_".rand(0,100);

            #Extensión de la imagen#

            switch (mime_content_type( $_FILES[ 'usuario_foto' ][ 'tmp_name' ] )) {
                case 'image/jpeg':
                    $foto=$foto.".jpeg";
                    break;
                case 'image/jpg':
                    $foto=$foto.".jpg";
                    break;
                case 'image/png':
                    $foto=$foto.".png";
                    break;
            }
            chmod($img_dir,0777);

            #Moviendo imagen al directorio #
            if(!move_uploaded_file($_FILES[ 'usuario_foto' ][ 'tmp_name' ],$img_dir.$foto)){
                $alerta = [
                    'tipo' => 'simple',
                    'titulo' => 'Error!',
                    'texto'=>'La imagen no se pudo guardar',
                    'icono'=>'error'
                ];
                return json_encode( $alerta );
                exit();
            }

        } else {
            $foto = '';
        }

        $usuario_datos_reg=[
            ["campo_nombre"=>"nombreUsuario", "campo_marcador"=>":Nombre", "campo_valor"=>$nombre],
            ["campo_nombre"=>"apellidoUsuario", "campo_marcador"=>":Apellido", "campo_valor"=>$apellido],
            ["campo_nombre"=>"emailUsuario", "campo_marcador"=>":Email", "campo_valor"=>$email],
            ["campo_nombre"=>"usuarioUsuario", "campo_marcador"=>":Usuario", "campo_valor"=>$usuario],
            ["campo_nombre"=>"usuarioClave", "campo_marcador"=>":Clave", "campo_valor"=>$clave],
            ["campo_nombre"=>"usuarioFoto", "campo_marcador"=>":Foto", "campo_valor"=>$foto],
            ["campo_nombre"=>"created_at", "campo_marcador"=>":Creado", "campo_valor"=>date("Y-m-d H:i:s")],
            ["campo_nombre"=>"updated_at", "campo_marcador"=>":Actualizado", "campo_valor"=>date("Y-m-d H:i:s")],
        ];

        $registrar_usuario=$this->guardarDatos("usuario",$usuario_datos_reg);

        if ($registrar_usuario->rowCount()==1) {
              $alerta = [
                    'tipo' => 'limpiar',
                    'titulo' => 'Usuario registrado',
                    'texto'=>'El usuario '.$nombre." ".$apellido." se registró con éxito",
                    'icono'=>'success'
                ];
        } else {
            if(is_file($img_dir.$foto)){
                chmod($img_dir.$foto,0777);
                unlink($img_dir.$foto);
            }
              $alerta = [
                    'tipo' => 'simple',
                    'titulo' => 'Error!',
                    'texto'=>'No se pudo guardar el usuario',
                    'icono'=>'error'
                ];
        }
        return json_encode($alerta);
        
    }
}

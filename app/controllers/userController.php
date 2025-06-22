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

        $img_dir ="../views/fotos/";

    }
}

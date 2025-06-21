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

           if ( $this->verificarDatos( '[a-zA-Z0-9$@.-]{7,100}', $clave1 ) || $this->verificarDatos( '[a-zA-Z0-9$@.-]{7,100}', $clave2 )  ) {
            $alerta = [
                'tipo' => 'simple',
                'titulo' => 'Error!',
                'texto'=>'Las claves no respetan el formato solicitado',
                'icono'=>'error'
            ];
            return json_encode( $alerta );
            exit();
        }

    }
}

<?php
namespace app\controllers;
use app\models\mainModel;

class loginController extends mainModel {

    # Controlador para iniciar sesion #

    public function iniciarSesionControlador() {

        #Almacenando datos#
        $usuario = $this->limpiarCadena( $_POST[ 'login_usuario' ] );
        $clave = $this->limpiarCadena( $_POST[ 'login_clave' ] );

        #Verificando campos obligatorios#
        if ( $usuario == '' || $clave == '' ) {
            echo "
            <script>
	            Swal.fire({
                    icon: 'error',
                    title: 'ERROR!!',
                    text: 'Los campos son obligatotios',
                    confirmButtonText: 'Aceptar',
                });
            </script>";
        } else {
            #verificar integridad#
            if ( $this->verificarDatos( '[a-zA-Z0-9]{4,20}', $usuario ) ) {
                echo "
            <script>
	            Swal.fire({
                    icon: 'error',
                    title: 'ERROR!!',
                    text: 'El USUARIO no respeta el formato solicitado',
                    confirmButtonText: 'Aceptar',
                });
            </script>";
            } else {
                if ( $this->verificarDatos( '[a-zA-Z0-9$@.-]{7,100}', $clave ) ) {
                    echo "
            <script>
	            Swal.fire({
                    icon: 'error',
                    title: 'ERROR!!',
                    text: 'La CLAVE no respeta el formato solicitado',
                    confirmButtonText: 'Aceptar',
                });
            </script>";
                } else {
                    $check_usuario = $this->ejecutarConsulta( "SELECT * FROM usuario WHERE nick_usuario='$usuario'" );

                    if ( $check_usuario->rowCount() == 1 ) {
                        $check_usuario = $check_usuario->fetch();
                        
                        if ( $check_usuario[ 'nick_usuario' ] && password_verify( $clave, $check_usuario[ 'clave_usuario' ] ) ) {
                            $_SESSION['id'] = $check_usuario['id_usuario'];
                            $_SESSION['nombre'] = $check_usuario['nombre_usuario'];
                            $_SESSION['apellido'] = $check_usuario['apellido_usuario'];
                            $_SESSION['nick'] = $check_usuario['nick_usuario'];
                            $_SESSION['foto'] = $check_usuario['avatar_usuario'];
                            

                            if ( headers_sent() ) {
                                echo "<script>
                                        window.location.href='".APP_URL."dashboard/';
                                        </script>";
                            } else {
                                header( 'Location: '.APP_URL.'dashboard/' );
                            }

                        } else {
                            echo "
                            <script>
	                            Swal.fire({
                                    icon: 'error',
                                    title: 'ERROR!!',
                                    text: 'Usuario o Clave incorrectos',
                                    confirmButtonText: 'Aceptar',
                                });
                            </script>";

                        }
                    } else {
                        echo "
                            <script>
	                            Swal.fire({
                                    icon: 'error',
                                    title: 'ERROR!!',
                                    text: 'Usuario o Clave incorrectos',
                                    confirmButtonText: 'Aceptar',
                                });
                            </script>";
                    }

                }

            }
        }

    }

    # Cerrar sesion #

    public function cerrarSesionControlador() {
        session_destroy();

        if ( headers_sent() ) {
            echo "<script>
                    window.location.href='".APP_URL."login/';
                </script>";
        } else {
            header( 'Location: '.APP_URL.'login/' );
        }
    }
}
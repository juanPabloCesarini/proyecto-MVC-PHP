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
                $check_email = $this->ejecutarConsulta( "SELECT email_usuario FROM usuario WHERE email_usuario='$email'" );
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

        $check_user = $this->ejecutarConsulta( "SELECT nick_usuario FROM usuario WHERE nick_usuario='$usuario'" );
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

            $foto = str_ireplace( ' ', '_', $nombre );
            $foto = $foto.'_'.rand( 0, 100 );

            #Extensión de la imagen#

            switch ( mime_content_type( $_FILES[ 'usuario_foto' ][ 'tmp_name' ] ) ) {
                case 'image/jpeg':
                $foto = $foto.'.jpeg';
                break;
                case 'image/jpg':
                $foto = $foto.'.jpg';
                break;
                case 'image/png':
                $foto = $foto.'.png';
                break;
            }
            chmod( $img_dir, 0777 );

            #Moviendo imagen al directorio #
            if ( !move_uploaded_file( $_FILES[ 'usuario_foto' ][ 'tmp_name' ], $img_dir.$foto ) ) {
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

        $usuario_datos_reg = [
            [ 'campo_nombre'=>'nombre_usuario', 'campo_marcador'=>':Nombre', 'campo_valor'=>$nombre ],
            [ 'campo_nombre'=>'apellido_usuario', 'campo_marcador'=>':Apellido', 'campo_valor'=>$apellido ],
            [ 'campo_nombre'=>'email_usuario', 'campo_marcador'=>':Email', 'campo_valor'=>$email ],
            [ 'campo_nombre'=>'nick_usuario', 'campo_marcador'=>':Usuario', 'campo_valor'=>$usuario ],
            [ 'campo_nombre'=>'clave_usuario', 'campo_marcador'=>':Clave', 'campo_valor'=>$clave ],
            [ 'campo_nombre'=>'avatar_usuario', 'campo_marcador'=>':Foto', 'campo_valor'=>$foto ],
            [ 'campo_nombre'=>'created_at', 'campo_marcador'=>':Creado', 'campo_valor'=>date( 'Y-m-d H:i:s' ) ],
            [ 'campo_nombre'=>'updated_at', 'campo_marcador'=>':Actualizado', 'campo_valor'=>date( 'Y-m-d H:i:s' ) ],
        ];

        $registrar_usuario = $this->guardarDatos( 'usuario', $usuario_datos_reg );

        if ( $registrar_usuario->rowCount() == 1 ) {
            $alerta = [
                'tipo' => 'limpiar',
                'titulo' => 'Usuario registrado',
                'texto'=>'El usuario '.$nombre.' '.$apellido.' se registró con éxito',
                'icono'=>'success'
            ];
        } else {
            if ( is_file( $img_dir.$foto ) ) {
                chmod( $img_dir.$foto, 0777 );
                unlink( $img_dir.$foto );
            }
            $alerta = [
                'tipo' => 'simple',
                'titulo' => 'Error!',
                'texto'=>'No se pudo guardar el usuario',
                'icono'=>'error'
            ];
        }
        return json_encode( $alerta );

    }

    public function listarUsuarioControlador( $pagina, $registro, $url, $busqueda ) {
        $pagina = $this->limpiarCadena( $pagina );
        $registro = $this->limpiarCadena( $registro );

        $url = $this->limpiarCadena( $url );
        $url = APP_URL.$url.'/';

        $busqueda = $this->limpiarCadena( $busqueda );
        $tabla = '';

        $pagina = ( isset( $pagina ) && $pagina>0 ) ? ( int ) $pagina : 1 ;

        $inicio = ( $pagina>0 ) ? ( ( $pagina*$registro )-$registro ) : 0;

        if ( isset( $busqueda ) && $busqueda != '' ) {
            $consulta_datos = "SELECT * FROM usuario
                             WHERE 
                             id_usuario !='".$_SESSION[ 'id' ]."' AND id_usuario != 1
                             AND (
                             nombre_usuario LIKE '%$busqueda%' OR
                             apellido_usuario LIKE '%$busqueda%' OR 
                             nick_usuario LIKE '%$busqueda%'
                             )
                             ORDER BY apellido_usuario 
                             LIMIT $inicio, $registro";

            $consulta_total = "SELECT COUNT(id_usuario) FROM usuario
                             WHERE 
                             id_usuario !='".$_SESSION[ 'id' ]."' AND id_usuario != 1
                             AND (
                             nombre_usuario LIKE '%$busqueda%' OR 
                             apellido_usuario LIKE '%$busqueda%' OR 
                             nick_usuario LIKE '%$busqueda%'
                             )";
        } else {
            $consulta_datos = "SELECT * FROM usuario WHERE id_usuario !='".$_SESSION[ 'id' ]."' AND id_usuario != 1 ORDER BY apellido_usuario LIMIT $inicio, $registro";

            $consulta_total = "SELECT COUNT(id_usuario) FROM usuario WHERE id_usuario !='".$_SESSION[ 'id' ]."' AND id_usuario != 1";
        }

        $datos = $this->ejecutarConsulta( $consulta_datos );
        
        $datos = $datos->fetchAll();
        
        $total = $this->ejecutarConsulta( $consulta_total );
        $total = ( int ) $total->fetchColumn();

        $nro_paginas = ceil( $total/$registro );

        $tabla .= '
        <div class="table-container">
        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
            <thead>
                <tr>
                    <th class="has-text-centered">#</th>
                    <th class="has-text-centered">Nombre</th>
                    <th class="has-text-centered">Usuario</th>
                    <th class="has-text-centered">Email</th>
                    <th class="has-text-centered">Creado</th>
                    <th class="has-text-centered">Actualizado</th>
                    <th class="has-text-centered" colspan="3">Opciones</th>
                </tr>
            </thead>
            <tbody>';
        if ( $total >= 1 && $pagina <= $nro_paginas ) {
            $contador = $inicio+1;
            $pag_inicio = $inicio+1;
            foreach ( $datos as $rows ) {
                $tabla .= '
            	<tr class="has-text-centered">
					<td>'.$contador.'</td>
					<td>'.$rows['apellido_usuario'].' '.$rows['nombre_usuario'].'</td>
					<td>'.$rows['nick_usuario'].'</td>
					<td>'.$rows['email_usuario'].'</td>
					<td>'.date("d-m-Y H:i:s", strtotime($rows['created_at'])).'</td>
					<td>'.date("d-m-Y H:i:s", strtotime($rows['updated_at'])).'</td>
					<td>
	                    <a href="'.APP_URL.'userFoto/'.$rows['id_usuario'].'/" class="button is-info is-rounded is-small">Foto</a>
	                </td>
	                <td>
	                    <a href="'.APP_URL.'userUpdate/'.$rows['id_usuario'].'/" class="button is-success is-rounded is-small">Actualizar</a>
	                </td>
	                <td>
	                	<form class="FormularioAjax" action="'.APP_URL.'app/ajax/usuarioAjax.php" method="POST" autocomplete="off">

	                		<input type="hidden" name="modulo_usuario" value="eliminar">
	                		<input type="hidden" name="usuario_id" value="'.$rows['id_usuario'].'">

	                    	<button type="submit" class="button is-danger is-rounded is-small">Eliminar</button>
	                    </form>
	                </td>
				</tr>';
                $contador++;
            }
            $pag_final = $contador-1;
        } else {
            if ( $total >= 1 ) {
                $tabla .= '<tr class="has-text-centered" >
	                <td colspan="7">
	                    <a href="'.$url.'1/" class="button is-link is-rounded is-small mt-4 mb-4">
	                        Haga clic acá para recargar el listado
	                    </a>
	                </td>
	            </tr>';
            } else {
                $tabla .= '<tr class="has-text-centered" >
	                <td colspan="7">
	                    No hay registros en el sistema
	                </td>
	            </tr>';
            }

        }

        $tabla .= '</tbody></table></div>';

        if($total>=1 && $pagina<= $nro_paginas){
            $tabla.='<p class="has-text-right">Mostrando usuarios <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';
            $tabla.=$this->paginadorTablas($pagina,$nro_paginas,$url,1);
        }
        return $tabla;

    }
}
<?php
    namespace app\models;
    use \PDO;

    if (file_exists(__DIR__."/../../config/server.php")){
        require_once __DIR__."/../../config/server.php";
    }

    class mainModel{
        private $server = DB_SERVER;
        private $db = DB_NAME;
        private $user = DB_USER;
        private $pass = DB_PASS;

        protected function conectar(){
            $conexion = new PDO("mysql:host=".$this->server.";dbname=".$this->db,$this->user,$this->pass);
            $conexion->exec("SET CHARACTER utf8");
            return $conexion;
        }

        protected function ejecutarConsulta($consulta){
            $sql = $this->conectar()->prepare($consulta);
            $sql->execute();
            return $sql;
        }

        public function limpiarCadena($cadena){

            $palabras=["<script>","</script>","<script src","<script type=","SELECT * FROM","SELECT "," SELECT ","DELETE FROM","INSERT INTO","DROP TABLE","DROP DATABASE","TRUNCATE TABLE","SHOW TABLES","SHOW DATABASES","<?php","?>","--","^","<",">","==","=",";","::"];

            $cadena = trim($cadena);

            $cadena = striolashes($cadena);

            foreach ($$palabras as $palabra) {
                $cadena = str_ireplace($palabra, "",$cadena);
                $cadena = trim($cadena);
                $cadena = striolashes($cadena);
            }
            return $cadenas;
        }

        protected function verificarDatos($filtro, $cadena){
            if (preg_match("/^".$filtro."$/",$cadena)) {
                return false;
            } else {
                return true;
            }
            
        }

        protected function guardarDatos($tabla,$datos){
            $query = "INSERT INTO $tabla (";
            $c = 0;
            foreach($datos as $clave){
                if ($c>=1) {
                   $query.=",";
                }
                $query.=$clave["campo_nombre"];
                $c++;
            }
            $query.=") VALUES (";

             $c = 0;
            foreach($datos as $clave){
                if ($c>=1) {
                   $query.=",";
                }
                $query.=$clave["campo_marcador"];
                $c++;
            }
            $query.=")";
            $sql = $this->conectar()->prepare($query);

            foreach ($datos as $clave) {
                $sql->bindParam($clave['campo_marcador'],$clave['campo_valor']);
            }
            $sql->execute();

            return $sql;
        }

        public function seleccionarDatos($tipo,$tabla,$campo,$id){
            $tipo = $this->limpiarCadena($tipo);
            $tabla = $this->limpiarCadena($tabla);
            $campo = $this->limpiarCadena($campo);
            $id = $this->limpiarCadena($id);

            if ($tipo=="Unico") {
                $sql = $this->conectar()->prepare("SELECT * FROM $tabla WHERE $campo =:ID");
                $sql->bindParam(":ID",$id);
            } elseif($tipo=="Normal") {
                $sql = $this->conectar()->prepare("SELECT $campo FROM $tabla ");
            }

             $sql->execute();

            return $sql;        
        }

          protected function actualizarDatos($tabla,$datos,$condicion){
            $query = "UPDATE $tabla SET ";
            $c = 0;
            foreach($datos as $clave){
                if ($c>=1) {
                   $query.=",";
                }
                $query.=$clave["campo_nombre"]."=".$clave["campo_marcador"];
                $c++;
            }
            $query.=" WHWRE ".$condicion["condicion_campo"]."=".$condicion["condición_marcador"];
            
            $sql = $this->conectar()->prepare($query);

            foreach ($datos as $clave) {
                $sql->bindParam($clave['campo_marcador'],$clave['campo_valor']);
            }
            
            $sql->bindParam($condicion['condicion_marcador'],$condicion['condicion_valor']);

            $sql->execute();

            return $sql;
        }

        protected function eliminarRegistro($tabla,$campo,$id){
            $sql = $this->conectar()->prepare("DELETE FROM $tabla WHERE $campo=:id");
            $sql->bindParam(":id",$id);
            $sql->execute();

            return $sql;

        }
    }
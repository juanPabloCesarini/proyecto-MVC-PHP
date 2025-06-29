<div class="container is-fluid mb-6">
	<h1 class="title">Usuarios</h1>
	<h2 class="subtitle">Lista de usuario</h2>
</div>
<div class="container pb-6 pt-6">


	<!-- <div class="table-container">
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
            <tbody> -->
    
	        <?php
                use app\controllers\userController;
                $insUsuario = new userController();

                echo $insUsuario->listarUsuarioControlador($url[1],3,$url[0],"");
            ?>
			
		

	

	

</div>
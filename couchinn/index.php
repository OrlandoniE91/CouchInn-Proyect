<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="css/styles.css">
	<title>Couch Inn</title>
</head>
<body>
	<header>
		<?php include("barra.php"); 
		$conexion = conectardb();?>	
	</header>	

	<div class="main container">
		<?php 
		if (isset($_SESSION['estado'])){
			$idUs = $_SESSION['id'];
			$calificaciones = mysqli_query($conexion, "SELECT * FROM calificacion WHERE idCalifica = '$idUs' AND estado = 'pendiente' ");
			if(mysqli_num_rows($calificaciones) != 0){ ?>			
			<div id="mensaje" class="bg-primary alertaC">
				<h3>¡Tienes calificaciones pendientes! Ingresa a tu perfil para calificar. <br><button id="ocultar" class="btn btn-sm btn-warning">En otro momento</button></h3>

			</div>
			<?php } 
		} ?>
		<button id="buscar" role="button" class="btn btn-primary">Buscar</button>
		
		<div class="filtro">
			<form class="form-horizontal" method="post" action="filtro.php">
				<div class="form-group">
					<h3 class="col-md-7 col-md-offset-3">Buscar por uno o más criterios.</h3>
				</div>
				<div class="form-group">
					<label for="titulo" class="control-label col-md-3">Titulo</label>
					<div class="col-md-7">
						<input type="text" class="form-control" name="titulo" id="titulo">
					</div>
				</div>

				<div class="form-group">
					<label for="ciudad" class="control-label col-md-3">Ciudad</label>
					<div class="col-md-7">
						<input type="text" class="form-control" name="ciudad" id="ciudad">
					</div>
				</div>

				<div class="form-group">
					<label for="capacidad" class="control-label col-md-3">Capacidad</label>
					<div class="col-md-7">
						<input type="number" class="form-control" name="capacidad" id="capacidad">
					</div>
				</div>

				<div class="form-group">
					<label for="descripcion" class="control-label col-md-3">Descripcion</label>
					<div class="col-md-7">
						<input type="text" class="form-control" name="descripcion" id="descripcion">
					</div>
				</div>

				<div class="form-group">
					<label for="thospedaje" class="control-label col-md-3">Tipo Hospedaje</label>
					<div class="col-md-7">
						<select class="form-control" name="thospedaje" id="thospedaje">
							<?php
							$registros=mysqli_query($conexion,"SELECT * FROM tipohospedaje WHERE enUso = 1")
							or die("Problemas en el select:".mysqli_error($conexion));
							echo "<option value=\" \"> Ninguno </option>";
							while ($reg=mysqli_fetch_array($registros))
							{
								echo "<option value=\"$reg[0]\"> $reg[1] </option>";
							}
							?>
						</select>
					</div>
				</div>

				<hr>

				<div class="form-group">
					<h3 class="col-md-7 col-md-offset-3">Disponibilidad de los hospedajes.</h3>
				</div>

				<div class="form-group">
					<label for="inicio" class="control-label col-md-3">Fecha de inicio</label>
					<div class="col-md-7">
						<input type="date" class="form-control" name="fechaInicio" min="<?php echo date('Y-m-d') ?>" id="inicio">
					</div>
				</div>
				<div class="form-group">
					<label for="fin" class="control-label col-md-3">Fecha de fin</label>
					<div class="col-md-7">
						<input type="date" class="form-control" name="fechaFin" min="<?php echo date('Y-m-d') ?>" id="fin">
					</div>
				</div>

				<div class="form-group">
					<div class="col-md-1 col-md-offset-5">
						<button type="submit" class="btn btn-primary" name="aceptar" onClick="return validarFecha();" >Buscar</button>
					</div>
				</div>
				<div id="errors" class="alert alert-danger" hidden></div>
			</form>
			
		</div>
		<?php include("loginModal.php");
		
		// Indicador de la página actual. Límite = número de publicaciones por página
		$limite = 6;  
		if (isset($_GET["pagina"])){ 
			$pagina  = $_GET["pagina"];
		} else { 
			$pagina = 1; 
		};  
		$inicio = ($pagina-1) * $limite;
		// ----------------------

		$resultH=mysqli_query($conexion,"SELECT * FROM hospedaje ORDER BY id LIMIT $inicio, $limite ");
		?>



		<div class="row">
			<?php while($filasH = mysqli_fetch_array($resultH)) { 
				$resultU=mysqli_query($conexion,"SELECT * FROM usuario WHERE id = '$filasH[6]' ");
				$filasU = mysqli_fetch_array($resultU);
				?>
				<div class="col-sm-4">
					<div class="box">
						<?php
						if(isset($_SESSION['estado'])){
							$usuario = $_SESSION['usuario'];
							$conexion = conectardb();
							$result = mysqli_query($conexion, "SELECT * FROM premium WHERE usuario = '$usuario'");
							$premium = mysqli_fetch_array($result);
							if ($premium != 0) {
								$consulta = mysqli_query($conexion,"SELECT * FROM imagen WHERE idHospedaje = $filasH[0] AND destacada = 1");	
								$imagen = mysqli_fetch_array($consulta);
								if ($imagen != 0){?>
								<img class="img-responsive" src="img/<?php echo $imagen['nombre'] ?>">
								<?php } else { ?>
								<img src="img/logo.png" alt="Logo">
								<?php } 
							} else{ ?>
							<img src="img/logo.png" alt="Logo">
							<?php }
						}else{?>
						<img src="img/logo.png" alt="Logo">
						<?php } ?>
						<h3><?php echo $filasH[1]; ?></h3>
						<p><?php echo $filasU[3] ?> <?php echo $filasU[4]; ?></p>
						<p>Valoración: <?php echo $filasH['calificacion']; ?></p>
						<a type="button" class="btn btn-sm btn-primary" href="post.php?id=<?php echo $filasH[0] ?>" style="margin-bottom:5px;" >Ver detalles</a>
					</div>
				</div>
				<?php } ?>
			</div>

			<!-- Constructor de la paginación -->
			<?php 
			$rs_result = mysqli_query($conexion, "SELECT COUNT(id) FROM hospedaje");  
			$row = mysqli_fetch_row($rs_result);  
			$total_records = $row[0];  
			$total_paginas = ceil($total_records / $limite);  
			$pagLink = "<nav class='paginacion'><ul class='pagination pagination-lg'>";  
			for ($i=1; $i<=$total_paginas; $i++) {  
				$pagLink .= "<li class='page-item'><a class='page-link' href='index.php?pagina=".$i."'>".$i."</a></li>";  
			};  
			echo $pagLink . "</ul></nav>"; ?>


			<?php mysqli_close($conexion);
			?>
		</div>

		<footer>
			
		</footer>
		<script type="text/javascript" src="js/jquery-1.12.4.min.js"></script>
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
		<script type="text/javascript" src="js/vdatos.js"></script>
		<script>
		$(document).ready(function(){
			$("#buscar").click(function(){
				$(".filtro").slideToggle();
			});

			$("#ocultar").click(function(){
				$(".alertaC").hide();
			});
		});
		</script>
	</body>
	</html>


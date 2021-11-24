<?php
 
// array for JSON response
$response = array();

// conecta ao BD
$con = pg_connect(getenv("DATABASE_URL"));

$username = NULL;
$password = NULL;

$isAuth = false;

// Método para mod_php (Apache)
if(isset( $_SERVER['PHP_AUTH_USER'])) {
    $username = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW'];
} // Método para demais servers
elseif(isset( $_SERVER['HTTP_AUTHORIZATION'])) {
    if(preg_match( '/^basic/i', $_SERVER['HTTP_AUTHORIZATION']))
		list($username, $password) = explode(':', base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
}

// Se a autenticação não foi enviada
if(!is_null($username)){
    $query = pg_query($con, "SELECT senha FROM usuario WHERE email='$username'");

	if(pg_num_rows($query) > 0){
		$row = pg_fetch_array($query);
		if($password == $row["senha"]){
			$isAuth = true;
		}
	}
}

if(isset($_POST["idusuario"]) && isset($_POST["iddata"]) && isset($_POST["idevento"])){ 
	$idusuario = $_POST["idusuario"];
	$iddata = $_POST["iddata"];
	$idevento = $_POST["idevento"];
	if($isAuth) {
		$result = pg_query($con, "SELECT idagenda FROM agenda_do_evento WHERE fk_evento_codigo = $idevento and fk_datas_codigo = $iddata");
		if(pg_num_rows($result) > 0){
			$row = pg_fetch_array($result);
			$idagenda = $row["idagenda"];
			$resultvota = pg_query($con, "SELECT * FROM vota WHERE fk_usuario_codigo = $idusuario AND fk_idagenda = $idagenda");
			if(pg_num_rows($resultvota) > 0){
				$delete = pg_query($con, "DELETE FROM vota WHERE fk_usuario_codigo = $idusuario AND fk_idagenda = $idagenda");
				if($delete){
					$response["success"] = 1;
				}
				else{
					$response["success"] = 0;
				}
			}
			else{
				$insert = pg_query($con, "INSERT INTO vota(fk_usuario_codigo,fk_idagenda) VALUES ($idusuario, $idagenda)");
				if($insert){
					$response["success"] = 1;
				}
				else{
					$response["success"] = 0;
				}
			}
			
		}
		else{
			$response["success"] = 0;
			$response["error"] = "data não encontrada";
		}
	
	}
	else {
		$response["success"] = 0;
		$response["error"] = "falha de autenticação";
	}
}
else{
	$response["success"] = 0;
	$response["error"] = "faltam parametros";
}

pg_close($con);
echo json_encode($response);
?>
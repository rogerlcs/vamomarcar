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

if(isset($_POST["data"]) && isset($_POST["idevento"])){ 
	$data = $_POST["data"];
	$idevento = $_POST["idevento"];
	if($isAuth) {
		$result = pg_query($con, "SELECT * FROM datas WHERE datas.data = '$data'");
		if(pg_num_rows($result) > 0){
			$row = pg_fetch_array($result);
			$iddata = $row["codigo"];
			$existe = pq_query($con, "SELECT * FROM agenda_do_evento AS agenda WHERE agenda.fk_datas_codigo = $iddata AND agenda.fk_evento_codigo = $idevento");
			if(pg_num_rows($existe) > 0){
				$response["success"] = 0;
				$response["error"] = "Data já sugerida";
			}
			else{
				$insert = pg_query($con, "INSERT INTO agenda_do_evento(fk_evento_codigo, fk_datas_codigo) VALUES($idevento,$iddata)");
				if($insert){
					$response["success"] = 1;
				}
				else{
					$response["success"] = 0;
				}
			}
		}
		else{
			$insertdata = pg_query($con, "INSERT INTO datas(data) VALUES ('$data')");
				if($insertdata){
					$resultcodigo = pg_query($con, "SELECT MAX(codigo) FROM datas");
					if(pg_num_rows($resultcodigo) > 0){
						$row = pg_fetch_array($resultcodigo);
						$codigodata = $row["max"];
						$insertagenda = pg_query($con, "INSERT INTO agenda_do_evento(fk_evento_codigo, fk_datas_codigo) VALUES($idevento, $codigodata)");
						if($insertdata){
							$response["success"] = 1;
						}
						else{
							$response["success"] = 0;
						}
					}
				}	
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

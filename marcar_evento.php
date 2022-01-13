<?php
 
// array for JSON response
$response = array();

// conecta ao BD
//$con = pg_connect(getenv("DATABASE_URL"));
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

if(isset($_POST["idevento"]) && isset($_POST["iddata"])){ 
	$idevento = $_POST["idevento"];
	$iddata = $_POST["iddata"];
	if($isAuth) {
		$result = pg_query($con, "SELECT * FROM administra adm join usuario us on(adm.fk_usuario_codigo = us.codigo) where us.email='$username' AND adm.fk_evento_codigo=$idevento");
		if(pg_num_rows($result) > 0){
			$result = pg_query($con, "SELECT * FROM agenda_do_evento WHERE fk_evento_codigo = $idevento AND fk_datas_codigo = $iddata");
			if(pg_num_rows($result) > 0){
				$update = pg_query($con, "UPDATE evento SET data_marcada = (SELECT data FROM datas dt WHERE dt.codigo = $iddata) WHERE codigo = $idevento");
				if($update){
					$update = pg_query($con, "UPDATE evento SET status_evento = 3 WHERE codigo = $idevento");
					if($update){
						$response["success"] = 1;
					}
					else{
						$response["success"] = 0;
					}
				}
				else{
					$response["success"] = 0;
					$response["error"] = "";
				}
			}
			else{
				$response["success"] = 0;
				$response["error"] = "data não encontrada";
			}
		}
		else{
			$response["success"] = 0;
			$response["error"] = "Permissão negada.";
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
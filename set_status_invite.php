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

if(isset($_POST["idusuario"]) && isset($_POST["status"]) && isset($_POST["idevento"])){ 
	$idusuario = $_POST["idusuario"];
	$idevento = $_POST["idevento"];
	$status = $_POST["status"];
	if($isAuth) {
		if($status == 1){
			$result = pg_query($con, "UPDATE participa SET status_convite = $status WHERE participa.fk_usuario_codigo = $idusuario AND participa.fk_evento_codigo = $idevento");}
		else{
			$result = pg_query($con, "DELETE FROM participa WHERE participa.fk_usuario_codigo = $idusuario AND participa.fk_evento_codigo = $idevento");}
		}
		if($result){
			$response["success"] = 1;
		}
		else{
			$response["success"] = 0;
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
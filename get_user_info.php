<?php
 
// array for JSON response
$response = array();

// conecta ao BD
//$con = pg_connect(getenv("DATABASE_URL"));
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

if(isset($_GET["idusuario"])){ 
	$idusuario = $_GET["idusuario"];
	if($isAuth) {
		$result = pg_query($con, "SELECT USUARIO.codigo, USUARIO.nome, USUARIO.bio, USUARIO.data_nascimento, ESTADO.nome as estado from USUARIO JOIN ESTADO ON(USUARIO.FK_ESTADO_id = ESTADO.id) where USUARIO.codigo = $idusuario");
		if(pg_num_rows($result) > 0){
			$row = pg_fetch_array($result);
			$response['user'] = array();
			$user = array();
			$user["codigo"] = $row["codigo"];
			$user["nome"] = $row["nome"];
			$user["bio"] = $row["bio"];
			$user["data_nascimento"] = $row["data_nascimento"];
			$user["estado"] = $row["estado"];
			array_push($response["user"], $user);
			$response["success"] = 1;
		}
		else {
			$response["success"] = 0;
			$response["error"] = "usuário não encontrado";
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
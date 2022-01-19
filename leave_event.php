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

if(isset($_POST["idevento"])){ 
	$idevento = $_POST["idevento"];
	if($isAuth) {
		$result = pg_query($con, "SELECT codigo FROM usuario WHERE email = 'roger@gmail.com'");
		if(pg_num_rows($result) > 0){
			$row = pg_fetch_array($result);
			$idusuario = $row["codigo"];
			$result = pg_query($con, "SELECT * FROM participa WHERE fk_evento_codigo = $idevento AND fk_usuario_codigo = $idusuario");
			if(pg_num_rows($result) > 0){
				$delete = pg_query($con, "DELETE FROM participa WHERE fk_usuario_codigo = $idusuario");
				if($delete){
					$delete = pg_query($con, "DELETE FROM administra WHERE fk_usuario_codigo = $idusuario AND fk_evento_codigo = $idevento");
					$response["success"] = 1;
				}
				else{
					$response["success"] = 0;
					$response["error"] = "";
				}
			}
			else{
				$response["success"] = 0;
				$response["error"] = "Usuário não está no evento";
			}
		}
		else{
			$response["success"] = 0;
			$response["error"] = "Usuario não encontrado";
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
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

if(isset($_POST["idevento"]) && isset($_POST['idparticipante'])){ 
	$idevento = $_POST["idevento"];
	$idparticipante = $_POST['idparticipante'];
	if($isAuth) {
		$result = pg_query($con, "SELECT codigo FROM usuario WHERE email = '$username'");
		if(pg_num_rows($result) > 0){
			$row = pg_fetch_array($result);
			$idusuario = $row["codigo"];
			$result = pg_query($con, "SELECT * FROM administra WHERE fk_evento_codigo = $idevento AND fk_usuario_codigo = $idusuario");
			if(pg_num_rows($result) > 0){
				$result = pg_query($con, "SELECT * FROM administra WHERE fk_evento_codigo = $idevento AND fk_usuario_codigo = $idparticipante");
				if(!pg_num_rows($result) > 0){
					$update = pg_query($con, "INSERT INTO administra(fk_usuario_codigo, fk_evento_codigo) VALUES($idparticipante, $idevento)");
					if ($update) {
						$response["success"] = 1;
					}
					else{
						$response["success"] = 0;
						$response["error"] = "Erro desconhecido.";
					}
				}
				else{
					$response["success"] = 0;
					$response["error"] = "Este participante já é um administrador";
				}
			}
			else{
				$response["success"] = 0;
				$response["error"] = "Permissão negada.";
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
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

if(isset($_GET["nome"])){ 
	$nome = $_GET["nome"];
	if($isAuth) {
		$result = pg_query($con, "SELECT * FROM usuario WHERE nome like '%$nome%'");
		if(pg_num_rows($result) > 0){
			$response['users'] = array();
			while($row = pg_fetch_array($result)){
				$user = array();
				$user["id"] = $row["codigo"];
				$user["nome"] = $row["nome"];
				array_push($response['users'], $user);
			}
			$response["success"] = 1;
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
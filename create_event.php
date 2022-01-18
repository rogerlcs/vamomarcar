<?php
ini_set('display_errors', 1);
error_reporting(E_ALL); 
$response = array();
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

if(isset($_POST["nome"]) && isset($_POST["local"]) && isset($_POST["prazov"]) && isset($_POST["prazos"]) && isset($_POST["descricao"]) && isset($_POST["ids"]) && isset($_POST["apelidolocal"]) && isset($_FILES['img'])){ 
	$nome = $_POST["nome"];
	$local = $_POST["local"];
	$prazov = $_POST["prazov"];
	$prazos = $_POST["prazos"];
	$descricao = $_POST["descricao"];
	$apelido = $_POST["apelidolocal"];
	$ids = $_POST["ids"];
	$imageFileType = strtolower(pathinfo(basename($_FILES["img"]["name"]),PATHINFO_EXTENSION));
	$image_base64 = base64_encode(file_get_contents($_FILES['img']['tmp_name']));
	$img = 'data:image/'.$imageFileType.';base64,'.$image_base64;
	$arrayid = explode(",", $ids);
	if($isAuth) {
		$result = pg_query($con, "INSERT INTO evento(nome, descricao, prazo_votacao,prazo_sugestao,status_evento,endereco, nome_local,img) VALUES ('$nome', '$descricao', '$prazov', '$prazos', 0, '$local', '$apelido','$img')");
		if($result){
			$result = pg_query($con, "SELECT MAX(codigo) FROM evento");
			if (pg_num_rows($result) > 0) {
				$row = pg_fetch_array($result);
				$eventoid = $row["max"];
				$query1 = pg_query($con, "SELECT codigo FROM usuario WHERE email='$username'");
				if(pg_num_rows($query1) > 0){
					$row = pg_fetch_array($query1);
					$idusuario = $row["codigo"];
					$query = "INSERT INTO participa(fk_usuario_codigo, fk_evento_codigo, status_convite) VALUES ($idusuario, $eventoid, 1),($arrayid[0], $eventoid, 0)";
					for ($i=1; $i < sizeof($arrayid); $i++) {
						$query .= ",($arrayid[$i],$eventoid, 0)";
					}
					$result1 = pg_query($con, $query);
					if($result1){
						$insertadmin = "INSERT INTO administra(fk_usuario_codigo, fk_evento_codigo) VALUES ($idusuario, $eventoid)";
						$resultadmin = pg_query($con, $insertadmin);
						if($resultadmin){
							$response["success"] = 1;
						}
						else{
							$response["success"] = 0;
						}
					}
					else{
						$response["success"] = 0;
					}
				}
				else{
					$response["success"] = 0;
				}
			}
			else{
				$response["success"] = 0;
				}
			
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
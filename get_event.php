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

if(isset($_GET["id"]) && isset($_GET['filter'])){ 
	$id = $_GET["id"];
	$filter = $_GET['filter'];
	if($isAuth) {
		if($filter == "allevents"){
			$result = pg_query($con, "SELECT participa.status_convite, evento.* FROM participa join evento on (participa.fk_evento_codigo = evento.codigo) join usuario on (participa.fk_usuario_codigo = usuario.codigo) where usuario.codigo = $id");}
		elseif ($filter == "inviteevents") {
			$result = pg_query($con, "SELECT participa.status_convite, evento.* FROM participa join evento on (participa.fk_evento_codigo = evento.codigo) join usuario on (participa.fk_usuario_codigo = usuario.codigo) where usuario.codigo = $id AND participa.status_convite = 0");}
		elseif ($filter == "myevents") {
			$result = pg_query($con, "SELECT participa.status_convite, evento.* FROM participa join evento on (participa.fk_evento_codigo = evento.codigo) join usuario on (participa.fk_usuario_codigo = usuario.codigo) where usuario.codigo = $id AND evento.id_criador = $id");
		}
		if(pg_num_rows($result) > 0){
			$response['events'] = array();
			while($row = pg_fetch_array($result)){
				$event = array();
				$event["id"] = $row["codigo"];
				$event["nome"] = $row["nome"];
				$event["status_convite"] = $row["status_convite"];
				$event["nome_local"] = $row["nome_local"];
				$event["prazo_sugestao"] = $row["prazo_sugestao"];
				$event["prazo_votacao"] = $row["prazo_votacao"];
				$event["status_evento"] = $row["status_evento"];
				$event["data_marcada"] = $row["data_marcada"];
				$event["endereco"] = $row["endereco"];
				$event["descricao"] = $row["descricao"];
				$event["img"] = $row["img"];
				$eid = $event["id"];
				$result1 = pg_query($con, "SELECT COUNT(*) FROM participa JOIN evento on (participa.fk_evento_codigo = evento.codigo) where evento.codigo = $eid");
				if(pg_num_rows($result1) > 0){
					$row = pg_fetch_array($result1);
					$event["total"] = $row["count"];
				}
				array_push($response["events"], $event);
			}
					$response["success"] = 1;
		}
		else{
			$response["success"] = 1;
			$response["msg"] = "Nenhum Evento";
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
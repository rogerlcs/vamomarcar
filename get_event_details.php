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

if(isset($_GET["idevento"]) && isset($_GET["idusuario"])){ 
	$idevento = $_GET["idevento"];
	$idusuario = $_GET["idusuario"];
	if($isAuth) {
		$result = pg_query($con, "SELECT * FROM evento where codigo = $idevento");
		if(pg_num_rows($result) > 0){
			$row = pg_fetch_array($result);
			$response['event'] = array();
			$event = array();
			$event["id"] = $row["codigo"];
			$event["nome"] = $row["nome"];
			$event["nome_local"] = $row["nome_local"];
			$event["prazo_sugestao"] = $row["prazo_sugestao"];
			$event["prazo_votacao"] = $row["prazo_votacao"];
			$event["status_evento"] = $row["status_evento"];
			$event["data_marcada"] = $row["data_marcada"];
			$event["endereco"] = $row["endereco"];
			$event["descricao"] = $row["descricao"];
			$event["datas"] = array();
			$resultdata = pg_query($con, "SELECT dt.*,(SELECT count(*) as votos FROM vota join agenda_do_evento as agenda on(vota.fk_idagenda = agenda.idagenda) where agenda.fk_datas_codigo = dt.codigo) FROM datas as dt join agenda_do_evento as agenda on(dt.codigo = agenda.fk_datas_codigo) where agenda.fk_evento_codigo = $idevento");
			if(pg_num_rows($resultdata) > 0){
				while ($row = pg_fetch_array($resultdata)) {
					$data = array();
					$data["codigo"] = $row["codigo"];
					$data["data"] = $row["data"];
					$data["votos"] = $row["votos"];
					array_push($event["datas"], $data);
				}
			$event["usuarios"] = array();
			$resultuser = pg_query($con, "SELECT codigo, nome FROM usuario JOIN participa ON(usuario.codigo = participa.fk_usuario_codigo) WHERE participa.fk_evento_codigo = $idevento");
			if(pg_num_rows($resultuser) > 0){
				while ($row = pg_fetch_array($resultuser)) {
					$user = array();
					$user["codigo"] = $row["codigo"];
					$user["nome"] = $row["nome"];
					array_push($event["usuarios"], $user);
				}
			}
			array_push($response["event"], $event);
			$response["success"] = 1;
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
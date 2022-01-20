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

if(isset($_POST["bio"]) && isset($_POST["name"]) && isset($_POST["birthDate"]) && isset($_POST["idestado"])){ 
	$bio = $_POST["bio"];
	$nome = $_POST["name"];
	$dtNasc = $_POST["birthDate"];
	$idestado = $_POST["idestado"];

	if($isAuth) {
		if($bio == "" && !isset($_FILES['img'])){
			$update = pg_query($con, "UPDATE usuario SET nome='$nome', data_nascimento='$dtNasc', fk_estado_id=$idestado WHERE email = '$username'");
		}
		elseif ($bio != "" && !isset($_FILES['img'])) {
			$update = pg_query($con, "UPDATE usuario SET nome='$nome', data_nascimento='$dtNasc', fk_estado_id=$idestado, bio='$bio' WHERE email = '$username'");
		}
		elseif($bio == "" && isset($_FILES['img'])){
			$imageFileType = strtolower(pathinfo(basename($_FILES["img"]["name"]),PATHINFO_EXTENSION));
			$image_base64 = base64_encode(file_get_contents($_FILES['img']['tmp_name']));
			$img = 'data:image/'.$imageFileType.';base64,'.$image_base64;
			$update =  pg_query($con, "UPDATE usuario SET nome='$nome', data_nascimento='$dtNasc', fk_estado_id=$idestado, img='$img' WHERE email = '$username'");
		}
		else{
			$imageFileType = strtolower(pathinfo(basename($_FILES["img"]["name"]),PATHINFO_EXTENSION));
			$image_base64 = base64_encode(file_get_contents($_FILES['img']['tmp_name']));
			$img = 'data:image/'.$imageFileType.';base64,'.$image_base64;
			 pg_query($con, "UPDATE usuario SET nome='$nome', data_nascimento='$dtNasc', fk_estado_id=$idestado, bio='$bio', img='$img' WHERE email = '$username'");
		}
		if($update){
			$response["success"] = 1;
		}
		else{
			$response["success"] = 0;
			$response["error"] = var_dump($_POST);
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
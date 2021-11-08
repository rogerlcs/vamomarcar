<?php
 
/*
 * Following code will create a new product row
 * All product details are read from HTTP Post Request
 */
 
// connecting to db
//$con = pg_connect(getenv("DATABASE_URL"));
 $con = pg_connect("host=localhost port=5432 dbname=teste user=postgres password=301224");
// array for JSON response
$response = array();
 
// check for required fields
if (isset($_POST['newLogin']) && isset($_POST['newPassword']) && isset($_POST['name']) && isset($_POST['birthDate'])) {
 
	$newLogin = trim($_POST['newLogin']);
	$newPassword = trim($_POST['newPassword']);
	$name = trim($_POST['name']);
	$birthDate = trim($_POST['birthDate']);
		
	$usuario_existe = pg_query($con, "SELECT email FROM usuario WHERE email='$newLogin'");
	// check for empty result
	if (pg_num_rows($usuario_existe) > 0) {
		$response["success"] = 0;
		$response["error"] = "usuario ja cadastrado";
	}
	else {
		// mysql inserting a new row
		$result = pg_query($con, "INSERT INTO usuario(email, senha, nome, data_nascimento) VALUES('$newLogin', '$newPassword', '$name', '$birthDate')");
	 
		if ($result) {
			$response["success"] = 1;
		}
		else {
			$response["success"] = 0;
			$response["error"] = "Error BD: ".pg_last_error($con);
		}
	}
}
else {
    $response["success"] = 0;
	$response["error"] = "faltam parametros";
}

pg_close($con);
echo json_encode($response);
?>
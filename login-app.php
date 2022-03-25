<?php
  require("connection.php");

  // The global $_POST variable allows you to access the data sent with the POST method by name
  // To access the data sent with the GET method, you can use $_GET
  $username = htmlspecialchars($_POST['username']);
  $password  = htmlspecialchars($_POST['password']);

  // Comprueba que el nombre de usuario contenga unicamente numeros, letras minusculas, guiones medios y guiones bajos
  if (!preg_match("/^[0-9a-z-_]{4,8}$/", $username)) {
    exit("Se permiten unicamente numeros, letras minusculas sin tildes ni dieresis, guiones y guiones bajos. El nombre de usuario tiene que contener entre 4 y 8 de los caracteres permitidos.");
  }

  if (empty($password)) {
    exit("La clave no debe estar vacia.");
  }

  main($username, $password);

  function main($username, $password) {
    $connection = getDataBaseConnection();

    if (!$connection) {
      exit("Error al conectar a la base de datos.");
    }

    $password_hash = getPasswordHash($username, $connection);

    /* Si el valor hash de la clave introducida no es igual al valor hash de
    la clave almacenada y asociada al nombre de usuario introducido, no se
    debe realizar el inicio de sesion */
    if (!password_verify($password, $password_hash)) {
      exit("Contraseña incorrecta.");
    }

    echo "Inicio de sesion satisfactorio.";
    mysqli_close($connection);
  }

  /**
  * Obtiene la clave en formato hash asociada al nombre de usuario introducido
  *
  * @param string $username
  * @param $connection
  * @return string clave en formato hash asociada al nombre de usuario introducido
  */
  function getPasswordHash($username, $connection) {
    $selection_query = "SELECT * FROM user_account WHERE username = '" . $username . "'";
    $query_results = mysqli_query($connection, $selection_query);

    if (!$query_results) {
      exit($query_results);
    }

    /* Si la cantidad de filas devuelta por la consulta es igual cero, la cuenta de usuario
    no esta registrada en la base de datos subyacente */
    if (mysqli_num_rows($query_results) == 0) {
      exit("Usuario no registrado.");
    }

    $table_record = mysqli_fetch_array($query_results);
    return $table_record['password'];
  }

?>

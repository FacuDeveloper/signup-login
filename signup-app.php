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

    if (!usernameAvailable($username, $connection)) {
      exit("Nombre de usuario no disponible, elija otro.");
    }

    persistUser($username, $password, $connection);
    echo "Usuario registrado exitosamente!";
    mysqli_close($connection);
  }

  /**
  * Comprueba que el nombr de usuario introducido no exista en la base de datos
  *
  * @param string $username
  * @param $connection
  * @return bool true si el nombre de usuario introducido no existe en la base
  * de batos, en caso contrario false
  */
  function usernameAvailable($username, $connection) {
    $selection_query = "SELECT * FROM user_account";
    $query_results = mysqli_query($connection, $selection_query);

    if (!$query_results) {
      exit("Error al comprobar la disponibilidad del nombre de usuario.");
    }

    /* Si el numero de filas devuelto por la consulta es cero, no existe en
    la base de datos subyacente el nombre de usuario introducida, por lo tanto,
    se debe retornar verdadero en señal de que el nombre de usuario introducido
    esta disponible */
    if (mysqli_num_rows($query_results) == 0) {
      return true;
    }

    // Obtiene cada uno de los registros de la base de datos user_db
    while ($table_record = mysqli_fetch_array($query_results)) {

      /* Si el nombre de usuario introducido es igual a uno de los nombres
      de usuario existentes en la base de datos, se debe retornar falso */
      if (strcmp($username, $table_record['username']) == 0) {
        return false;
      }

    }

    return true;
  }

  /**
  * Persiste en la base de datos el nombre de usuario y la clave ingresados
  *
  * @param string $username
  * @param string $password
  * @param $connection
  */
  function persistUser($username, $password, $connection) {
    $hashed_password = getPasswordHash($password);
    $insert = "INSERT INTO user_account(username, password) VALUES ('$username', '$hashed_password')";

    $insert_result = mysqli_query($connection, $insert);

    if (!$insert_result) {
      exit("Error al crear el usuario.");
    }

  }

  /**
  * Realiza el hash de la clave introducida
  *
  * @param string $password
  * @return string valor hash con salt de la clave introducida
  */
  function getPasswordHash($password) {
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    if (!$hashed_password) {
      exit($hashed_password);
    }

    return $hashed_password;
  }

?>

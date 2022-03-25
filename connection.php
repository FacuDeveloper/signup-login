<?php

  /**
  * Establece una conexion con una base de datos MySQL
  */
  function getDataBaseConnection() {
    $server_name = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name = "user_db";
    return mysqli_connect($server_name, $db_username, $db_password, $db_name);
  }

?>

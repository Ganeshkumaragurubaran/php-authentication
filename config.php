<?php
define("DB_SERVER", "localhost");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");
define("DB_NAME", "registered");

# Connection
$link = mysqli_connect("localhost", "root", " ", "afynd");

# Check connection
if (!$link) {
  die("Connection failed: " . mysqli_connect_error());
}

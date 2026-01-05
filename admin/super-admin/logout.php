<?php
session_start();

unset($_SESSION["user_id"]);
unset($_SESSION["role"]);
header("location: ../../Login/admin/login_admin.php");

?>
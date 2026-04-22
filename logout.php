<?php

require_once "includes/config_session.php";

session_unset();
session_destroy();

header("Location: login.php");
exit();

?>

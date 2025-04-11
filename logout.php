<?php
session_start();
session_destroy();
header("Location: loginreg.html");
exit();
?>

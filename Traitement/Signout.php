<?php
session_start();

session_destroy();

header('Location: ../IHM/login.php');
exit();

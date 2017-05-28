<?php
session_start();
Auth::logout();
header("Location: /index.php");


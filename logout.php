<?php
// не понимаю тут почем-то null приходит
unset($_SESSION['user']);
header("Location: /index.php");
exit();

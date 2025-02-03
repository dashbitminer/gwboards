<?php
$password = '123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hash generado para la contraseña '$password': " . $hash;
?> 
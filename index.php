<?php
$page = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/pages/index.html');

print($page);
?>
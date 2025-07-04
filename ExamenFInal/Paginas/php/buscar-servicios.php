<?php
$q = isset($_GET['q']) ? urlencode($_GET['q']) : '';
$location = isset($_GET['location']) ? urlencode($_GET['location']) : '';
$params = [];
if ($q !== '') $params[] = "q=$q";
if ($location !== '') $params[] = "location=$location";
$queryString = implode('&', $params);
header("Location: ../html/buscar-servicios.php" . ($queryString ? "?$queryString" : ""));
exit; 
<?php
session_start();

include '../db.php';

$params = get_params();
$json = json_encode($params);
echo $json;
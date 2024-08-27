<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/Classes/DB.php';

use Classes\DB;

$db = new DB();
$db->migrate();

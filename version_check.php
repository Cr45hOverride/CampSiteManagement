<?php
require 'db_config.php';
echo 'PHP: ' . phpversion() . "\n";
echo 'MySQL: ' . $pdo->getAttribute(PDO::ATTR_SERVER_VERSION) . "\n";

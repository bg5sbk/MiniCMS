<?php
ini_set("display_errors", "On"); error_reporting(E_ALL);

require_once 'mc-tags.php';
require_once 'mc-conf.php';

function mc_404()
{
  header('HTTP/1.0 404 Not Found');
  echo "<h1>404 Not Found</h1>";
  echo "The page that you have requested could not be found.";
  exit();
}
?>

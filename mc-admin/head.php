<?php
ini_set("display_errors", "On"); error_reporting(E_ALL);
require_once '../mc-files/mc-conf.php';

if (isset($_COOKIE['mc_token'])) {
  $token = $_COOKIE['mc_token'];

  if ($token != md5($mc_config['user_name'].'_'.$mc_config['user_pass'])) {
    Header("Location:index.php");
    exit;
  }
} else {
  Header("Location:index.php");
  exit;
}

$page_file = basename($_SERVER['PHP_SELF']);
  
function shorturl($input) {
  $base32 = array (
    'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h',
    'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p',
    'q', 'r', 's', 't', 'u', 'v', 'w', 'x',
    'y', 'z', '0', '1', '2', '3', '4', '5'
  );
  
  $hex = md5('prefix'.$input.'surfix'.time());
  $hexLen = strlen($hex);
  $subHexLen = $hexLen / 8;
  $output = array();
  
  for ($i = 0; $i < $subHexLen; $i++) {
    $subHex = substr ($hex, $i * 8, 8);
    $int = 0x3FFFFFFF & (1 * ('0x'.$subHex));
    $out = '';
    for ($j = 0; $j < 6; $j++) {
      $val = 0x0000001F & $int;
      $out .= $base32[$val];
      $int = $int >> 5;
    }
    $output[] = $out;
  }
  return $output;
}

function post_sort($a, $b) {
  $a_date = $a['date'];
  $b_date = $b['date'];

  if ($a_date != $b_date)
    return $a_date > $b_date ? -1 : 1;

  return $a['time'] > $b['time'] ? -1 : 1;
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
  <meta charset="UTF-8" />
  <title>MiniCMS</title>
  <link style="text/css" rel="stylesheet" href="style.css" />
</head>
<body>
  <div id="menu">
    <h3 id="menu_title"><a href="<?php echo $mc_config['site_link'] != '' ? $mc_config['site_link'] : '/'; ?>"><?php echo htmlspecialchars($mc_config['site_name']); ?></a></h3>
    <ul>
      <li <?php echo $page_file == 'post.php' || $page_file == 'post-edit.php' ? 'class="current"' : ''; ?>><a href="post.php">文章</a></li>
      <li <?php echo $page_file == 'page.php' || $page_file == 'page-edit.php' ? 'class="current"' : ''; ?>><a href="page.php">页面</a></li>
      <li <?php echo $page_file == 'conf.php' ? 'class="current"' : ''; ?>><a href="conf.php">设置</a></li>
    </ul>
    <div class="clear"></div>
  </div>
  <div id="content">
    <div id="content_box">

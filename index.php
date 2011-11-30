<?php
require_once dirname(__FILE__).'/mc-files/mc-core.php';

$mc_post_per_page = 10;

$qs = $_SERVER['QUERY_STRING'];

if (preg_match('|^post/([a-z0-5]{6})$|', $qs, $matches)) {
  $mc_get_type = 'post';
  $mc_get_name = $matches[1];
}
else if (preg_match('|^tag/([^/]+)/(\?page=([0-9]+)){0,1}$|', $qs, $matches)) {
  $mc_get_type = 'tag';
  $mc_get_name = urldecode($matches[1]);
  $mc_page_num = isset($matches[2]) ? $matches[3] : 1;
}
else if (preg_match('|^(([-a-zA-Z0-5]+/)+)$|', $qs, $matches)) {
  $mc_get_type = 'page';
  $mc_get_name = substr($matches[1], 0, -1);
}
else {
  $mc_get_type = 'index';
  $mc_get_name = '';
  $mc_page_num = isset($_GET['page']) ? $_GET['page'] : 1;
}


if ($mc_get_type == 'post') {
  require 'mc-files/posts/index/publish.php';
  
  if (array_key_exists($mc_get_name, $mc_posts)) {
    $mc_post_id = $mc_get_name;
    $mc_post = $mc_posts[$mc_post_id];
    
    $mc_data = unserialize(file_get_contents('mc-files/posts/data/'.$mc_post_id.'.dat'));
  }
  else {
    mc_404();
  }
}
else if ($mc_get_type == 'tag') {
  require 'mc-files/posts/index/publish.php';
  
  $mc_post_ids = array_keys($mc_posts);
  $mc_post_count = count($mc_post_ids);
  
  $mc_tag_posts = array();
  
  for ($i = 0; $i < $mc_post_count; $i ++) {
    $id = $mc_post_ids[$i];
    $post = $mc_posts[$id];
    if (in_array($mc_get_name, $post['tags'])) {
      $mc_tag_posts[$id] = $post;
    }
  }
  
  $mc_posts = $mc_tag_posts;
  
  $mc_post_ids = array_keys($mc_posts);
  $mc_post_count = count($mc_post_ids);
}
else if ($mc_get_type == 'page') {
  require 'mc-files/pages/index/publish.php';
  
  if (array_key_exists($mc_get_name, $mc_pages)) {
    $mc_post_id = $mc_get_name;
    $mc_post = $mc_pages[$mc_post_id];
    
    $mc_data = unserialize(file_get_contents('mc-files/pages/data/'.$mc_post['file'].'.dat'));
  }
  else {
    mc_404();
  }
}
else {
  require 'mc-files/posts/index/publish.php';
  
  $mc_post_ids = array_keys($mc_posts);
  $mc_post_count = count($mc_post_ids);
}

require 'mc-files/theme/index.php';
?>

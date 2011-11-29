<?php
function mc_site_name($print = true) {
  global $mc_config;

  $site_name = $mc_config['site_name'];

  if ($print) {
    echo $site_name;
    return;
  }

  return $site_name;
}

function mc_site_desc($print = true) {
  global $mc_config;

  $site_desc = $mc_config['site_desc'];

  if ($print) {
    echo $site_desc;
    return;
  }

  return $site_desc;
}

function mc_nick_name($print = true) {
  global $mc_config;

  $nick_name = $mc_config['user_nick'];

  if ($print) {
    echo $nick_name;
    return;
  }

  return $nick_name;
}

function mc_theme_url($path, $print = true) {
  global $mc_config;

  $url = $mc_config['site_link'].'/mc-files/theme/'.$path;

  if ($print) {
    echo $url;
    return;
  }

  return $url;
}

function mc_is_post() {
  global $mc_get_type;

  return $mc_get_type == 'post';
}

function mc_is_page() {
  global $mc_get_type;

  return $mc_get_type == 'page';
}

function mc_is_tag() {
  global $mc_get_type;
  return $mc_get_type == 'tag';
}

function mc_tag_name($print=true) {
  global $mc_get_name;

  if ($print) {
    echo $mc_get_name;
    return;
  }

  return $mc_get_name;
}

function mc_next_post() {
  global $mc_posts, $mc_post_ids, $mc_post_count, $mc_post_i, $mc_post_id, $mc_post;

  if (!isset($mc_posts))
    return false;

  if (!isset($mc_post_i))
    $mc_post_i = 0;

  if ($mc_post_i == $mc_post_count)
    return false;

  $mc_post_id = $mc_post_ids[$mc_post_i];
  
  $mc_post = $mc_posts[$mc_post_id];

  $mc_post_i += 1;

  return true;
}

function mc_the_title($print = true) {
  global $mc_post;

  if ($print) {
    echo $mc_post['title'];
    return;
  }

  return $mc_post['title'];
}

function mc_the_date($print = true) {
  global $mc_post;

  if ($print) {
    echo $mc_post['date'];
    return;
  }

  return $mc_post['date'];
}

function mc_the_time($print = true) {
  global $mc_post;

  if ($print) {
    echo $mc_post['time'];
    return;
  }

  return $mc_post['time'];
}

function mc_the_tags($item_begin='', $item_gap=', ', $item_end='') {
  global $mc_post;

  $tags = $mc_post['tags'];
  
  $count = count($tags);

  for ($i = 0; $i < $count; $i ++) {
    $tag = $tags[$i];
    
    echo $item_begin.'<a href="/?tag/'.urlencode($tag).'/">'.$tag.'</a>'.$item_end;

    if ($i < $count - 1)
      echo $item_gap;
  }
}

function mc_the_content($print = true) {
  global $mc_data;
  
  $html = Markdown($mc_data['content']);

  if ($print) {
    echo $html;
    return;
  }
  
  return $html;
}

function mc_the_link() {
  global $mc_post_id, $mc_post;

  echo '<a href="/?post/'.$mc_post_id.'">'.$mc_post['title'].'</a>';
}
?>

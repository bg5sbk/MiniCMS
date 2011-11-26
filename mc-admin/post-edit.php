<?php require 'head.php' ?>
<?php
$display_info = false;

if (isset($_POST['save_draft']) || isset($_POST['save_publish'])) {
  $post_title = $_POST['title'];
  $post_content = $_POST['content'];
  $post_tags = explode(',', $_POST['tags']);
  
  $post_tags_count = count($post_tags);
  
  for ($i = 0; $i < $post_tags_count; $i ++) {
    if (trim($post_tags[$i]) == '') {
      unset($post_tags[$i]);
      $i --;
      $post_tags_count --;
    }
  }
  
  $data = array(
    'title' => $post_title,
    'content' => $post_content,
    'tags' => $post_tags
  );
  
  if (isset($_POST['save_publish'])) {
    $index_file = dirname(dirname(__FILE__)).'/mc-files/posts/index/publish.php';
  }
  else {
    $index_file = dirname(dirname(__FILE__)).'/mc-files/posts/index/draft.php';
  }
  
  if (!isset($_GET['id'])) {
    require $index_file;
  
    $file_names = shorturl($_POST['title']);

    foreach ($file_names as $file_name) {
      $file_path = dirname(dirname(__FILE__)).'/mc-files/posts/data/'.$file_name.'.dat';
        
      if (!is_file($file_path)) {
        $post_id = $file_name;
        
        $data['id'] = $file_name;
        $data['date'] = date("Y-m-d");
        $data['time'] = date("H:i:s");
        
        $mc_posts = array_merge(
          array($post_id => array(
          'title' => $post_title,
          'tags' => $post_tags,
          'date' => $data['date'],
          'time' => $data['time']
          )),
          $mc_posts
        );
        
        file_put_contents($index_file, "<?php\n\$mc_posts=".var_export($mc_posts, true)."\n?>");
        file_put_contents($file_path, serialize($data));
        break;
      }
    }
  } else {
    if ($_POST['state'] == 'publish') {
      $state = 'publish';
      $index_file2 = dirname(dirname(__FILE__)).'/mc-files/posts/index/publish.php';
    } else if ($_POST['state'] == 'delete') {
      $state = 'draft';
      $index_file2 = dirname(dirname(__FILE__)).'/mc-files/posts/index/delete.php';
    } else {
      $state = 'draft';
      $index_file2 = dirname(dirname(__FILE__)).'/mc-files/posts/index/draft.php';
    }
    
    require $index_file2;
    
    $post_id = $_POST['id'];
  
    $file_path = dirname(dirname(__FILE__)).'/mc-files/posts/data/'.$_POST['id'].'.dat';
    
    if (is_file($file_path)) {
      if (isset($_POST['save_publish']) && $state != 'publish') {
        $need_delete = true;
      }
      else if (isset($_POST['save_draft']) && $state != 'draft'){
        $need_delete = true;
      }
      
      $post = $mc_posts[$post_id];
      
      if ($need_delete) {
        unset($mc_posts[$post_id]);
        
        file_put_contents($index_file2, "<?php\n\$mc_posts=".var_export($mc_posts, true)."\n?>");
      }
      
      require $index_file;
    
      $post['title'] = $post_title;
      $post['tags']  = $post_tags;
      
      $mc_posts[$post_id] = $post;
    
      file_put_contents($index_file, "<?php\n\$mc_posts=".var_export($mc_posts, true)."\n?>");
      file_put_contents($file_path, serialize($data));
    }
  }
  
  $display_info = true;
} else if (isset($_GET['id'])) {
  $post_id = $_GET['id'];
  $post_state = $_GET['state'];
  
  $file_path = dirname(dirname(__FILE__)).'/mc-files/posts/data/'.$_GET['id'].'.dat';
  
  $data = unserialize(file_get_contents($file_path));

  $post_title = $data['title'];
  $post_content = $data['content'];
  $post_tags = $data['tags'];
} else {
  $post_title = "在此输入标题";
  $post_content = "";
  $post_tags = array();
}
?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
<?php if ($display_info) { ?>
<div class="updated">保存成功！ <a href="#">查看</a></div>
<?php } ?>
<div class="admin_page_name">撰写新文章</div>
<div style="margin-bottom:20px;">
<input name="title" type="text"
value="<?php echo htmlspecialchars($post_title); ?>"
style="width:99%;border:solid 1px #ccc; font-size:20px; padding:3px 4px; border-radius:3px; <?php if (!isset($post_id)) {?>color:#888;<?php } ?>"
<?php if (!isset($post_id)) {?>
onfocus="if (this.temp_value != undefined && this.value != this.temp_value) return; this.temp_value = this.value; this.value=''; this.style.color='#000';" onblur="if (this.value == '') { this.style.color='#888'; this.value = this.temp_value; }"
<?php } ?>/>
</div>
<div style="margin-bottom:20px;">
<textarea name="content" style="height:400px;width:99%;border:solid 1px #ccc;padding:3px 4px; border-radius:3px; resize:vertical;"><?php echo htmlspecialchars($post_content); ?></textarea>
</div>
<div style="margin-bottom:20px;">
<input name="tags" type="text"
value="<?php if (count($post_tags) == 0) { echo "在此输入标签，多个标签用英语逗号(,)分隔"; } else { echo htmlspecialchars(implode(',', $post_tags)); } ?>"
style="width:99%;border:solid 1px #ccc; font-size:20px; padding:3px 4px; border-radius:3px; <?php if (!isset($post_id)) {?>color:#888;<?php } ?>" 
<?php if (!isset($post_id)) { ?>
onfocus="if (this.temp_value != undefined && this.value != this.temp_value) return; this.temp_value = this.value; this.value=''; this.style.color='#000';" onblur="if (this.value == '') { this.style.color='#888'; this.value = this.temp_value; }"
<?php } ?>/>
</div>
<div style="text-align:right">
<?php if (isset($post_id)) { ?>
<input type="hidden" name="id" value="<?php echo $post_id; ?>"/>
<input type="hidden" name="state" value="<?php echo $post_state; ?>"/>
<?php } ?>
<input type="submit" name="save_draft" value="保存草稿" style="padding:6px 20px; margin-right:8px;"/>
<input type="submit" name="save_publish" value="发布" style="padding:6px 20px;"/>
</div>
</form>
<?php require 'foot.php' ?>

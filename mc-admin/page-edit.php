<?php require 'head.php' ?>
<?php
$display_info = false;

if (isset($_POST['save_draft']) || isset($_POST['save_publish'])) {
  $page_title = $_POST['title'];
  $page_content = $_POST['content'];
  $page_id = $_POST['id'];
  
  $data = array(
    'id' => $page_id,
    'title' => $page_title,
    'content' => $page_content,
  );
  
  if (isset($_POST['save_publish'])) {
    $index_file = dirname(dirname(__FILE__)).'/mc-files/pages/index/publish.php';
  }
  else {
    $index_file = dirname(dirname(__FILE__)).'/mc-files/pages/index/draft.php';
  }
  
  if (!isset($_GET['id'])) {
    require $index_file;
  
    $file_names = shorturl($_POST['title']);

    foreach ($file_names as $file_name) {
      $file_path = dirname(dirname(__FILE__)).'/mc-files/pages/data/'.$file_name.'.dat';
        
      if (!is_file($file_path)) {
        $data['date'] = date("Y-m-d");
        $data['time'] = date("H:i:s");
        
        $mc_pages[$page_id] = array(
          'title' => $page_title,
          'tags' => $page_tags,
          'date' => $data['date'],
          'time' => $data['time'],
          'file' => $file_name,
        );
        
        file_put_contents($index_file, "<?php\n\$mc_pages=".var_export($mc_pages, true)."\n?>");
        file_put_contents($file_path, serialize($data));
        break;
      }
    }
  } else {
    if ($_POST['state'] == 'publish') {
      $state = 'publish';
      $index_file2 = dirname(dirname(__FILE__)).'/mc-files/pages/index/publish.php';
    } else if ($_POST['state'] == 'delete') {
      $state = 'delete';
      $index_file2 = dirname(dirname(__FILE__)).'/mc-files/pages/index/delete.php';
    } else {
      $state = 'draft';
      $index_file2 = dirname(dirname(__FILE__)).'/mc-files/pages/index/draft.php';
    }
    
    require $index_file2;
    
    if (isset($mc_pages[$_GET['id']])) {
      if (isset($_POST['save_publish']) && $state != 'publish') {
        $need_delete = true;
      }
      else if (isset($_POST['save_draft']) && $state != 'draft'){
        $need_delete = true;
      }
      
      $page = $mc_pages[$_GET['id']];
      
      if ($need_delete) {
        unset($mc_pages[$_GET['id']]);
        
        file_put_contents($index_file2, "<?php\n\$mc_pages=".var_export($mc_pages, true)."\n?>");
      }
      
      require $index_file;
    
      if ($page_id != $_GET['id']) {
        unset($mc_pages[$_GET['id']]);
      }
      
      $page['title'] = $page_title;
      
      $mc_pages[$page_id] = $page;
    
      file_put_contents($index_file, "<?php\n\$mc_pages=".var_export($mc_pages, true)."\n?>");
      
      $file_path = dirname(dirname(__FILE__)).'/mc-files/pages/data/'.$page['file'].'.dat';
    
      file_put_contents($file_path, serialize($data));
    }
  }
  
  $display_info = true;
} else if (isset($_GET['id']) && isset($_GET['state'])) {
  if ($_GET['state'] == 'publish') {
    $state = 'publish';
    $index_file2 = dirname(dirname(__FILE__)).'/mc-files/pages/index/publish.php';
  } else if ($_GET['state'] == 'delete') {
    $state = 'delete';
    $index_file2 = dirname(dirname(__FILE__)).'/mc-files/pages/index/delete.php';
  } else {
    $state = 'draft';
    $index_file2 = dirname(dirname(__FILE__)).'/mc-files/pages/index/draft.php';
  }
  
  require $index_file2;
  
  $page = $mc_pages[$_GET['id']];
  
  $page_id = $_GET['id'];
  $page_state = $_GET['state'];
  
  $file_path = dirname(dirname(__FILE__)).'/mc-files/pages/data/'.$page['file'].'.dat';
  
  $data = unserialize(file_get_contents($file_path));

  $page_title = $data['title'];
  $page_content = $data['content'];
  $page_path = $data['path'];
} else {
  $page_title = "在此输入标题";
  $page_id = "在此输入页面路径，多级路径用英语斜杠(/)分割";
  $page_content = "";
  $page_tags = array();
}
?>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
<?php if ($display_info) { ?>
<div class="updated">保存成功！ <a href="#">查看</a></div>
<?php } ?>
<div class="admin_page_name">创建页面</div>
<div style="margin-bottom:20px;">
<input name="title" type="text"
value="<?php echo htmlspecialchars($page_title); ?>"
style="width:99%;border:solid 1px #ccc; font-size:20px; padding:3px 4px; border-radius:3px; <?php if (!isset($page_state)) {?>color:#888;<?php } ?>"
<?php if (!isset($page_state)) {?>
onfocus="if (this.temp_value != undefined && this.value != this.temp_value) return; this.temp_value = this.value; this.value=''; this.style.color='#000';" onblur="if (this.value == '') { this.style.color='#888'; this.value = this.temp_value; }"
<?php } ?>/>
</div>
<div style="margin-bottom:20px;">
<textarea name="content" style="height:400px;width:99%;border:solid 1px #ccc;padding:3px 4px; border-radius:3px; resize:vertical;"><?php echo htmlspecialchars($page_content); ?></textarea>
</div>
<div style="margin-bottom:20px;">
<input name="id" type="text"
value="<?php echo htmlspecialchars($page_id); ?>"
style="width:99%;border:solid 1px #ccc; font-size:20px; padding:3px 4px; border-radius:3px; <?php if (!isset($page_state)) {?>color:#888;<?php } ?>"
<?php if (!isset($page_state)) {?>
onfocus="if (this.temp_value != undefined && this.value != this.temp_value) return; this.temp_value = this.value; this.value=''; this.style.color='#000';" onblur="if (this.value == '') { this.style.color='#888'; this.value = this.temp_value; }"
<?php } ?>/>
</div>
<div style="text-align:right">
<?php if (isset($page_state)) { ?>
<input type="hidden" name="state" value="<?php echo $page_state; ?>"/>
<?php } ?>
<input type="submit" name="save_draft" value="保存草稿" style="padding:6px 20px; margin-right:8px;"/>
<input type="submit" name="save_publish" value="发布" style="padding:6px 20px;"/>
</div>
</form>
<?php require 'foot.php' ?>

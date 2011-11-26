<?php
require 'head.php';

$page_file    = '';
$page_path    = '';
$page_state   = '';
$page_title   = '';
$page_content = '';
$error_msg    = '';
$succeed      = false;
  
if (isset($_POST['_IS_POST_BACK_'])) {
  $page_file    = $_POST['file'];
  $page_path    = $_POST['path'];
  $page_state   = $_POST['state'];
  $page_title   = trim($_POST['title']);
  $page_content = trim($_POST['content']);
  $page_date    = date("Y-m-d");
  $page_time    = date("H:i:s");
  
  if ($page_title == '') {
    $error_msg = '页面标题不能为空';
  }
  else if ($page_path == '') {
    $error_msg = '页面路径不能为空';
  }
  else {
    if ($page_file == '') {
      $file_names = shorturl($page_title);
      
      foreach ($file_names as $file_name) {
        $file_path = '../mc-files/pages/data/'.$file_name.'.dat';
        
        if (!is_file($file_path)) {
          $page_file = $file_name;
          break;
        }
      }
    }
    else {
      $file_path = '../mc-files/pages/data/'.$page_file.'.dat';
  
      $data = unserialize(file_get_contents($file_path));
      
      $page_old_path  = $data['path'];
      $page_old_state = $data['state'];
      
      if ($page_old_state != $page_state || $page_old_path != $page_path) {
        $index_file = '../mc-files/pages/index/'.$page_old_state.'.php';
        
        require $index_file;
        
        unset($mc_pages[$page_old_path]);
        
        file_put_contents($index_file,
          "<?php\n\$mc_pages=".var_export($mc_pages, true)."\n?>"
        );
      }
    }
    
    $data = array(
      'file'  => $page_file,
      'path'  => $page_path,
      'state' => $page_state,
      'title' => $page_title,
      'date'  => $page_date,
      'time'  => $page_time,
    );
    
    $index_file = '../mc-files/pages/index/'.$page_state.'.php';
    
    require $index_file;
    
    $mc_pages[$page_path] = $data;
    
    file_put_contents($index_file,
      "<?php\n\$mc_pages=".var_export($mc_pages, true)."\n?>"
    );
    
    $data['content'] = $page_content;
    
    file_put_contents($file_path, serialize($data));
  }
} else if (isset($_GET['file'])) {
  $file_path = '../mc-files/pages/data/'.$_GET['file'].'.dat';
  
  $data = unserialize(file_get_contents($file_path));
  
  $page_file    = $data['file'];
  $page_path    = $data['path'];
  $page_state   = $data['state'];
  $page_title   = $data['title'];
  $page_content = $data['content'];
}
?>
<script type="text/javascript">
function empty_textbox_focus(target){
  if (target.temp_value != undefined && target.value != target.temp_value)
    return;
  
  target.temp_value = target.value;
  target.value='';
  target.style.color='#000';
}

function empty_textbox_blur(target) {
  if (target.value == '') {
    target.style.color='#888';
    target.value = target.temp_value;
  }
}
</script>
<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
  <input type="hidden" name="_IS_POST_BACK_" value=""/>
  <?php if ($succeed) { ?>
  <div class="updated">页面已保存！ <a href="#">查看</a></div>
  <?php } ?>
  <div class="admin_page_name">
  <?php if ($page_path == '') echo "创建页面"; else echo "编辑页面"; ?>
  </div>
  <div style="margin-bottom:20px;">
    <input name="title" type="text" class="edit_textbox" value="<?php
    if ($page_title == "") {
      echo '在此输入标题" " style="color:#888;" onfocus="empty_textbox_focus(this)" onblur="empty_textbox_blur(this)';
    }
    else {
      echo htmlspecialchars($page_title);
    }
    ?>"/>
  </div>
  <div style="margin-bottom:20px;">
    <textarea name="content" class="edit_textarea"><?php echo htmlspecialchars($page_content); ?></textarea>
  </div>
  <div style="margin-bottom:20px;">
    <input name="path" type="text" class="edit_textbox" value="<?php
    if ($page_path == '') {
      echo '在此输入页面路径，多级路径用英语斜杠(/)分割" " style="color:#888;" onfocus="empty_textbox_focus(this)" onblur="empty_textbox_blur(this)';
    }
    else {
      echo htmlspecialchars($page_path);
    }
    ?>"/>
  </div>
  <div style="margin-bottom:20px;text-align:right">
    状态：
    <select name="state" style="width:100px;">
      <option value="draft" <?php if ($page_state == 'draft') echo 'selected="selected"'; ?>>草稿</option>
      <option value="publish" <?php if ($page_state == 'publish') echo 'selected="selected"'; ?>>发布</option>
    </select>
  </div>
  <div style="text-align:right">
    <input type="hidden" name="file" value="<?php echo $page_file; ?>"/>
    <input type="submit" name="save" value="保存" style="padding:6px 20px;"/>
  </div>
</form>
<?php require 'foot.php' ?>
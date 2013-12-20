<?php
require 'head.php';

$page_file        = '';
$page_path        = '';
$page_state       = '';
$page_title       = '';
$page_content     = '';
$page_date        = '';
$page_time        = '';
$page_can_comment = '';
$error_msg        = '';
$succeed          = false;
  
if (isset($_POST['_IS_POST_BACK_'])) {
  $page_file        = $_POST['file'];
  $page_path        = $_POST['path'];
  $page_state       = $_POST['state'];
  $page_title       = trim($_POST['title']);
  $page_content     = get_magic_quotes_gpc() ? stripslashes(trim($_POST['content'])) : trim($_POST['content']);;
  $page_date        = date("Y-m-d");
  $page_time        = date("H:i:s");
  $page_can_comment = $_POST['can_comment'];

  if ($_POST['year'] != '')
    $page_date = substr_replace($page_date, $_POST['year'], 0, 4);

  if ($_POST['month'] != '')
    $page_date = substr_replace($page_date, $_POST['month'], 5, 2);

  if ($_POST['day'] != '')
    $page_date = substr_replace($page_date, $_POST['day'], 8, 2);

  if ($_POST['hourse'] != '')
    $page_time = substr_replace($page_time, $_POST['hourse'], 0, 2);

  if ($_POST['minute'] != '')
    $page_time = substr_replace($page_time, $_POST['minute'], 3, 2);

  if ($_POST['second'] != '')
    $page_time = substr_replace($page_time, $_POST['second'], 6, 2);
  
  $page_path_part  = explode('/', $page_path);
  $page_path_count = count($page_path_part);
  
  for ($i = 0; $i < $page_path_count; $i ++) {
    $trim = trim($page_path_part[$i]);
    if ($trim == '') {
      unset($page_path_part[$i]);
    } else {
      $page_path_part[$i] = $trim;
    }
  }
  
  reset($page_path_part);
  
  $page_path = implode('/', $page_path_part);
  
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
      'file'        => $page_file,
      'path'        => $page_path,
      'state'       => $page_state,
      'title'       => $page_title,
      'date'        => $page_date,
      'time'        => $page_time,
      'can_comment' => $page_can_comment,
    );
    
    $index_file = '../mc-files/pages/index/'.$page_state.'.php';
    
    require $index_file;
    
    $mc_pages[$page_path] = $data;

    ksort($mc_pages);   
 
    file_put_contents($index_file,
      "<?php\n\$mc_pages=".var_export($mc_pages, true)."\n?>"
    );
    
    $data['content'] = $page_content;
    
    file_put_contents($file_path, serialize($data));
    
    $succeed = true;
  }
} else if (isset($_GET['file'])) {
  $file_path = '../mc-files/pages/data/'.$_GET['file'].'.dat';
  
  $data = unserialize(file_get_contents($file_path));
  
  $page_file        = $data['file'];
  $page_path        = $data['path'];
  $page_state       = $data['state'];
  $page_title       = $data['title'];
  $page_content     = $data['content'];
  $page_date        = $data['date'];
  $page_time        = $data['time'];
  $page_can_comment = isset($data['can_comment']) ? $data['can_comment'] : '1';
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
  <?php if ($page_state == 'publish') { ?>
  <div class="updated">页面已发布。 <a href="<?php echo $mc_config['site_link']; ?>/?<?php echo $page_path; ?>/" target="_blank">查看页面</a></div>
  <?php } else { ?>
  <div class="updated">页面已保存到“草稿箱”。 <a href="page.php?state=draft">打开草稿箱</a></div>
  <?php } ?>
  <?php } ?>
  <div class="admin_page_name">
  <?php if ($page_path == '') echo "创建页面"; else echo "编辑页面"; ?>
  </div>
  <div style="margin-bottom:20px;">
    <input name="title" type="text" class="edit_textbox" placeholder="在此输入标题" value="<?php echo htmlspecialchars($page_title); ?>"/>
  </div>
  <div style="margin-bottom:20px;">
    <?php require 'editor.php'; ?>
    <?php editor($page_content); ?>
  </div>
  <div style="margin-bottom:20px;">
    <input name="path" type="text" class="edit_textbox" placeholder="在此输入页面路径，多级路径用半角斜杠(/)分割" value="<?php echo htmlspecialchars($page_path); ?>"/>
  </div>
  <div style="margin-bottom:20px;text-align:right">
    <div style="float:left">
    时间：
    <select name="year">
      <option value=""></option>
<?php $year = substr($page_date, 0, 4); for ($i = 1990; $i <= 2030; $i ++) { ?>
      <option value="<?php echo $i; ?>" <?php if ($year == $i) echo 'selected="selected";' ?>><?php echo $i; ?></option>
<?php } ?>
    </select> -
    <select name="month">
      <option value=""></option>
<?php $month = substr($page_date, 5, 2); for ($i = 1; $i <= 12; $i ++) { $m = sprintf("%02d", $i); ?>
      <option value="<?php echo $m; ?>" <?php if ($month == $m) echo 'selected="selected";' ?>><?php echo $m; ?></option>
<?php } ?>
    </select> -
    <select name="day">
      <option value=""></option>
<?php $day = substr($page_date, 8, 2); for ($i = 1; $i <= 31; $i ++) { $m = sprintf("%02d", $i); ?>
      <option value="<?php echo $m; ?>" <?php if ($day == $m) echo 'selected="selected";' ?>><?php echo $m; ?></option>
<?php } ?>
    </select>&nbsp;
    <select name="hourse">
      <option value=""></option>
<?php $hourse = substr($page_time, 0, 2); for ($i = 0; $i <= 23; $i ++) { $m = sprintf("%02d", $i); ?>
      <option value="<?php echo $m; ?>" <?php if ($hourse == $m) echo 'selected="selected";' ?>><?php echo $m; ?></option>
<?php } ?>
    </select> :
    <select name="minute">
      <option value=""></option>
<?php $minute = substr($page_time, 3, 2); for ($i = 0; $i <= 59; $i ++) { $m = sprintf("%02d", $i); ?>
      <option value="<?php echo $m; ?>" <?php if ($minute == $m) echo 'selected="selected";' ?>><?php echo $m; ?></option>
<?php } ?>
    </select> :
    <select name="second">
      <option value=""></option>
<?php $second = substr($page_time, 6, 2); for ($i = 0; $i <= 59; $i ++) { $m = sprintf("%02d", $i); ?>
      <option value="<?php echo $m; ?>" <?php if ($second == $m) echo 'selected="selected";' ?>><?php echo $m; ?></option>
<?php } ?>
    </select>
    </div>
    评论：
    <select name="can_comment" style="margin-right:16px;">
      <option value="1" <?php if ($page_can_comment == '1') echo 'selected="selected";'; ?>>允许</option>
      <option value="0" <?php if ($page_can_comment == '0') echo 'selected="selected";'; ?>>禁用</option>
    </select>
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

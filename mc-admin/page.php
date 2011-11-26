<?php
require_once '../mc-files/mc-conf.php';

function load_pages() {
  global $state, $index_file, $mc_pages;
  
  if ($_GET['state'] == 'draft') {
    $state = 'draft';
    $index_file = dirname(dirname(__FILE__)).'/mc-files/pages/index/draft.php';
  }
  else if ($_GET['state'] == 'delete'){
    $state = 'delete';
    $index_file = dirname(dirname(__FILE__)).'/mc-files/pages/index/delete.php';
  }
  else {
    $state = 'publish';
    $index_file = dirname(dirname(__FILE__)).'/mc-files/pages/index/publish.php';
  }

  require $index_file;
}

function delete_page($id) {
  global $state, $index_file, $mc_pages;
  
  $page = $mc_pages[$id];
  
  $page['prev_state'] = $state;
  
  unset($mc_pages[$id]);
  
  file_put_contents($index_file, "<?php\n\$mc_pages=".var_export($mc_pages, true)."\n?>");
  
  if ($state != 'delete') {
    $index_file2 = dirname(dirname(__FILE__)).'/mc-files/pages/index/delete.php';
    
    require $index_file2;
  
    $mc_pages[$id] = $page;
  
    file_put_contents($index_file2, "<?php\n\$mc_pages=".var_export($mc_pages, true)."\n?>");
  } else {
    unlink(dirname(dirname(__FILE__)).'/mc-files/pages/data/'.$page['file'].'.dat');
  }
}

function revert_page($id) {
  global $state, $index_file, $mc_pages;
  
  $page = $mc_pages[$id];
  
  $prev_state = $page['prev_state'];
  
  unset($page['prev_state']);
  
  unset($mc_pages[$id]);
  
  file_put_contents($index_file, "<?php\n\$mc_pages=".var_export($mc_pages, true)."\n?>");
  
  $index_file2 = dirname(dirname(__FILE__)).'/mc-files/pages/index/'.$prev_state.'.php';
    
  require $index_file2;
  
  $mc_pages[$id] = $page;
  
  file_put_contents($index_file2, "<?php\n\$mc_pages=".var_export($mc_pages, true)."\n?>");
}

load_pages();

if (isset($_GET['delete']) || (isset($_GET['apply']) && $_GET['apply'] == 'delete')) {
  if (isset($_GET['apply']) && $_GET['apply'] == 'delete') {
    $ids = explode(',', $_GET['ids']);
    foreach ($ids as $id) {
      if (trim($id) == '')
        continue;
      delete_page($id);
      load_pages();
    }
  } else {
    delete_page($_GET['delete']);
  }
  //load_posts();
  Header('Location:page.php?done=true&state='.$state);
  exit();
}

if (isset($_GET['revert']) || (isset($_GET['apply']) && $_GET['apply'] == 'revert')) {
  if (isset($_GET['apply']) && $_GET['apply'] == 'revert') {
    $ids = explode(',', $_GET['ids']);
    foreach ($ids as $id) {
      if (trim($id) == '')
        continue;
      revert_page($id);
      load_pages();
    }
  } else {
    revert_page($_GET['revert']);
  }
  //load_posts();
  Header('Location:page.php?done=true&state='.$state);
  exit();
}

$page_ids = array_keys($mc_pages);

if (isset($_GET['done'])) {
  $message = '操作成功';
}

$page_count = count($page_ids);

$date_array = array();

for ($i = $page_count - 1; $i >= 0; $i --) {
  $page_id = $page_ids[$i];
  $page = $mc_pages[$page_id];
  $date_array[] = $page['date'];
}

$date_array = array_unique($date_array);
?>
<?php require 'head.php' ?>
<script type="text/javascript">
function check_all(name)
{
  var el  = document.getElementsByTagName('input');
  var len = el.length;
  
  for(var i=0; i<len; i++) {
    if((el[i].type=="checkbox") && (el[i].name==name)) {
      el[i].checked = true;
    }
  }
}
function clear_all(name)
{
  var el  = document.getElementsByTagName('input');
  var len = el.length;
  
  for(var i=0; i<len; i++) {
    if((el[i].type=="checkbox") && (el[i].name==name)) {
    el[i].checked = false;
    }
  }
}
function apply_all(opid, name)
{
  var el  = document.getElementsByTagName('input');
  var len = el.length;
  var ids = '';
  
  for(var i=0; i<len; i++) {
    if((el[i].type=="checkbox") &&
       (el[i].name==name) &&
       el[i].checked == true &&
       el[i].value != '') {
      ids += el[i].value + ',';
    }
  }
  
  var op = document.getElementById(opid);
  
  if (ids != '')
    location.href = '?state=<?php echo $state; ?>&apply=' + op.value + '&ids=' + ids;
}
function do_filter()
{
  var date = document.getElementById('date');
  
  location.href = '?state=<?php echo $state; ?>&date=' + date.value;
}
</script>
<?php if (isset($message)) { ?>
<div class="updated"><?php echo $message; ?></div>
<?php } ?>
<div class="admin_page_name">管理页面<a href="page-edit.php">创建页面</a></div>
<div class="post_mode_link">
<?php if ($state == 'publish') { ?><b><?php } else { ?><a href="?state=publish"><?php } ?>已发布<?php if ($state == 'publish') { ?></b><?php } else { ?></a><?php } ?>&nbsp;|&nbsp;
<?php if ($state == 'draft') { ?><b><?php } else { ?><a href="?state=draft"><?php } ?>草稿箱<?php if ($state == 'draft') { ?></b><?php } else { ?></a><?php } ?>&nbsp;|&nbsp;
<?php if ($state == 'delete') { ?><b><?php } else { ?><a href="?state=delete"><?php } ?>回收站<?php if ($state == 'delete') { ?></b><?php } else { ?></a><?php } ?>
</div>
<div class="table_list_tool">
  <span>
    <select style="width:100px" id="op1">
      <option value="">批量操作</option>
      <?php if ($state == 'delete') { ?>
      <option value="revert">还原</option>
      <option value="delete">删除</option>
      <?php } else { ?>
      <option value="delete">回收</option>
      <?php } ?>
    </select>
    <input type="button" value="应用" onclick="apply_all('op1','ids');"/>
  </span>
  <span>
    <select style="width:130px" id="date">
      <option value="">显示所有日期</option>
      <?php foreach ($date_array as $date_name) { ?>
      <option value="<?php echo $date_name; ?>"><?php echo $date_name; ?></option>
      <?php } ?>
    </select>
    <input type="submit" value="筛选" onclick="do_filter();"/>
  </span>
</div>
<div class="table_list post_list">
<table colspan="0" rowspan="0" cellpadding="0" cellspacing="0" id="list">
  <thead>
    <tr>
    <td style="width:20px"><input type="checkbox" name="ids" onclick="if(this.checked==true) { check_all('ids'); } else { clear_all('ids'); }" value=""/></td>
    <td>标题</td><td style="width:25%">路径</td><td style="width:15%">日期</td>
    </tr>
  </thead>
  <tbody>
  <?php sort($page_ids); for ($i = 0; $i < $page_count; $i ++) { $page_id = $page_ids[$i]; $page = $mc_pages[$page_id]; ?>
  <?php if (isset($_GET['date']) && $_GET['date'] != '' && $_GET['date'] != $page['date']) continue; ?>
    <tr<?php if ($i % 2 == 0) echo ' class="alt"'; ?>>
      <td><input type="checkbox" name="ids" value="<?php echo $page_id; ?>"/></td>
      <td>
        <a href="page-edit.php?id=<?php echo $page_id; ?>"><?php echo htmlspecialchars($page['title']);?></a>
        <div>
          <a href="page-edit.php?id=<?php echo $page_id; ?>&state=<?php echo $state; ?>">编辑</a>&nbsp;|&nbsp;
          <?php if ($state == 'delete') { ?>
          <a href="?revert=<?php echo $page_id; ?>&state=<?php echo $state; ?>">还原</a>&nbsp;|&nbsp;
          <a href="?delete=<?php echo $page_id; ?>&state=<?php echo $state; ?>">删除</a>&nbsp;|&nbsp;
          <?php } else { ?>
          <a href="?delete=<?php echo $page_id; ?>&state=<?php echo $state; ?>">回收</a>&nbsp;|&nbsp;
          <?php } ?>
          <a href="#">查看</a>
        </div>
      </td>
      <td><?php
        $paths = explode('/', $page_id);
        $paths_count = count($paths);
        for ($j = 0; $j < $paths_count - 1; $j ++) {
          echo '－';
        }
        echo $paths[$paths_count - 1]; ?></td>
      <td><?php echo htmlspecialchars($page['date']);?><br/>已发布</td>
    </tr>
  <?php } ?>
  </tbody>
  <tfoot>
    <tr>
    <td><input type="checkbox" name="ids" onclick="if(this.checked==true) { check_all('ids'); } else { clear_all('ids'); }" value=""/></td><td>标题</td><td>路径</td><td>日期</td>
    </tr>
  </tfoot>
</table>
</div>
<div class="table_list_tool">
  <span>
    <select style="width:100px" id="op2">
      <option>批量操作</option>
      <option value="delete">回收</option>
    </select>
    <input type="button" name="apply" value="应用" onclick="apply_all('op2','ids');"/>
  </span>
</div>
<?php require 'foot.php' ?>

<?php
require_once '../mc-files/mc-conf.php';

if (!is_dir('../mc-files/posts/data/'))
  mkdir('../mc-files/posts/data/');

function load_posts() {
  global $state, $index_file, $mc_posts;
  
  if (isset($_GET['state'])) {
    if ($_GET['state'] == 'draft') {
      $state = 'draft';
      $index_file = '../mc-files/posts/index/draft.php';
    }
    else if ($_GET['state'] == 'delete'){
      $state = 'delete';
      $index_file = '../mc-files/posts/index/delete.php';
    }
    else {
      $state = 'publish';
      $index_file = '../mc-files/posts/index/publish.php';
    }
  }
  else {
    $state = 'publish';
    $index_file = '../mc-files/posts/index/publish.php';
  }

  require $index_file;
}

function delete_post($id) {
  global $state, $index_file, $mc_posts;
  
  $post = $mc_posts[$id];
  
  $post['prev_state'] = $state;
  
  unset($mc_posts[$id]);
  
  file_put_contents($index_file, "<?php\n\$mc_posts=".var_export($mc_posts, true)."\n?>");
  
  if ($state != 'delete') {
    $index_file2 = '../mc-files/posts/index/delete.php';
    
    require $index_file2;
  
    $mc_posts[$id] = $post;
  
    file_put_contents($index_file2, "<?php\n\$mc_posts=".var_export($mc_posts, true)."\n?>");
  } else {
    unlink('../mc-files/posts/data/'.$id.'.dat');
  }
}

function revert_post($id) {
  global $state, $index_file, $mc_posts;
  
  $post = $mc_posts[$id];
  
  $prev_state = $post['prev_state'];
  
  unset($post['prev_state']);
  
  unset($mc_posts[$id]);
  
  file_put_contents($index_file, "<?php\n\$mc_posts=".var_export($mc_posts, true)."\n?>");
  
  $index_file2 = '../mc-files/posts/index/'.$prev_state.'.php';
    
  require $index_file2;
  
  $mc_posts[$id] = $post;

  uasort($mc_posts, "post_sort");
  
  file_put_contents($index_file2, "<?php\n\$mc_posts=".var_export($mc_posts, true)."\n?>");
}

load_posts();

if (isset($_GET['delete']) || (isset($_GET['apply']) && $_GET['apply'] == 'delete')) {
  if (isset($_GET['apply']) && $_GET['apply'] == 'delete') {
    $ids = explode(',', $_GET['ids']);
    foreach ($ids as $id) {
      if (trim($id) == '')
        continue;
      delete_post($id);
      load_posts();
    }
  } else {
    delete_post($_GET['delete']);
  }
  //load_posts();
  Header('Location:post.php?done=true&state='.$state);
  exit();
}

if (isset($_GET['revert']) || (isset($_GET['apply']) && $_GET['apply'] == 'revert')) {
  if (isset($_GET['apply']) && $_GET['apply'] == 'revert') {
    $ids = explode(',', $_GET['ids']);
    foreach ($ids as $id) {
      if (trim($id) == '')
        continue;
      revert_post($id);
      load_posts();
    }
  } else {
    revert_post($_GET['revert']);
  }
  //load_posts();
  Header('Location:post.php?done=true&state='.$state);
  exit();
}

$post_ids = array_keys($mc_posts);

if (isset($_GET['done'])) {
  $message = '操作成功';
}

$post_count = count($post_ids);

$date_array = array();
$tags_array = array();

for ($i = 0; $i < $post_count; $i ++) {
  $post_id = $post_ids[$i];
  $post = $mc_posts[$post_id];
  $date_array[] = $post['date'];
  $tags_array = array_merge($tags_array, $post['tags']);
}

$date_array = array_unique($date_array);
$tags_array = array_unique($tags_array);

if (isset($_GET['tag']))
  $filter_tag = $_GET['tag'];
else
  $filter_tag = '';

if (isset($_GET['date']))
  $filter_date = $_GET['date'];
else
  $filter_date = '';
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
  var tag = document.getElementById('tag');
  var date = document.getElementById('date');
  
  location.href = '?state=<?php echo $state; ?>&tag=' + tag.value + '&date=' + date.value;
}
</script>
<?php if (isset($message)) { ?>
<div class="updated"><?php echo $message; ?></div>
<?php } ?>
<div class="admin_page_name">管理文章<a href="post-edit.php">撰写文章</a></div>
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
      <option value="<?php echo $date_name; ?>" <?php if ($filter_date == $date_name) echo ' selected="selected"'; ?>><?php echo $date_name; ?></option>
      <?php } ?>
    </select>
    <select style="width:130px" id="tag">
      <option value="">显示所有标签</option>
      <?php foreach ($tags_array as $tag_name) { ?>
      <option value="<?php echo $tag_name; ?>" <?php if ($filter_tag == $tag_name) echo ' selected="selected"'; ?>><?php echo $tag_name; ?></option>
      <?php } ?>
    <select>
    <input type="submit" value="筛选" onclick="do_filter();"/>
  </span>
</div>
<div class="table_list post_list">
<table colspan="0" rowspan="0" cellpadding="0" cellspacing="0" id="list">
  <thead>
    <tr>
    <td style="width:20px"><input type="checkbox" name="ids" onclick="if(this.checked==true) { check_all('ids'); } else { clear_all('ids'); }" value=""/></td>
    <td>标题</td><td style="width:25%">标签</td><td style="width:15%">日期</td>
    </tr>
  </thead>
  <tbody>
  <?php for ($i = 0; $i < $post_count; $i ++) { $post_id = $post_ids[$i]; $post = $mc_posts[$post_id]; ?>
  <?php if ($filter_tag != '' && !in_array($filter_tag, $post['tags'])) continue; ?>
  <?php if ($filter_date != '' && $filter_date != $post['date']) continue; ?>
    <tr<?php if ($i % 2 == 0) echo ' class="alt"'; ?>>
      <td><input type="checkbox" name="ids" value="<?php echo $post_id; ?>"/></td>
      <td>
        <a href="post-edit.php?id=<?php echo $post_id; ?>"><?php echo htmlspecialchars($post['title']);?></a>
        <div>
          <a href="post-edit.php?id=<?php echo $post_id; ?>">编辑</a>&nbsp;|&nbsp;
          <?php if ($state == 'delete') { ?>
          <a href="?revert=<?php echo $post_id; ?>&state=<?php echo $state; ?>">还原</a>&nbsp;|&nbsp;
          <a href="?delete=<?php echo $post_id; ?>&state=<?php echo $state; ?>">删除</a>&nbsp;|&nbsp;
          <?php } else { ?>
          <a href="?delete=<?php echo $post_id; ?>&state=<?php echo $state; ?>">回收</a>&nbsp;|&nbsp;
          <?php } ?>
          <a href="#">查看</a>
        </div>
      </td>
      <td>
<?php 
  $tags = $post['tags']; 
  $tag_count = count($tags); 
  for ($j = 0; $j < $tag_count; $j ++) { 
    $tag = $tags[$j]; 
?>
      <a href="?state=<?php echo $state; ?>&date=<?php echo $filter_date; ?>&tag=<?php echo htmlspecialchars($tag); ?>"><?php echo htmlspecialchars($tag); ?></a>
<?php 
    if ($j < $tag_count - 1)
      echo ',&nbsp;'; 
  }
?>
      </td>
      <td><?php echo htmlspecialchars($post['date']);?><br/>已发布</td>
    </tr>
  <?php } ?>
  </tbody>
  <tfoot>
    <tr>
    <td><input type="checkbox" name="ids" onclick="if(this.checked==true) { check_all('ids'); } else { clear_all('ids'); }" value=""/></td><td>标题</td><td>标签</td><td>日期</td>
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

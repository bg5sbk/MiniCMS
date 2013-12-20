<?php
require_once dirname(dirname(__FILE__)).'/mc-files/mc-conf.php';

if (isset($_COOKIE['mc_token'])) {
  $token = $_COOKIE['mc_token'];

  if ($token == md5($mc_config['user_name'].'_'.$mc_config['user_pass'])) {
    Header("Location:{$mc_config['site_link']}/mc-admin/post.php");
  }
}

if (isset($_POST['login'])) {
  if ($_POST['user'] == $mc_config['user_name'] 
  && $_POST['pass'] == $mc_config['user_pass']) {
    setcookie('mc_token', md5($mc_config['user_name'].'_'.$mc_config['user_pass']));
    Header("Location:{$mc_config['site_link']}/mc-admin/post.php");
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>MiniCMS</title>
  <style type="text/css">
* { font-family:"Microsoft YaHei",Segoe UI,Tahoma,Arial,Verdana,sans-serif; }
body { background:#f9f9f9; font-size:14px; }
#login_title { text-align:center; width:360px; margin:120px auto; margin-bottom:0px; font-size:32px; color:#333;  text-shadow: 0 2px 0 #FFFFFF;}
#login_form { width:360px; margin:0 auto; margin-top:20px; border:solid 1px #e0e0e0; background:#fff; border-radius:3px 3px 3px 3px;}
#login_form_box { padding:16px; }
#login_form .label { font-weight:bold; padding-bottom:6px; color:#333; }
#login_form .textbox input { border:none; padding:0; font-size:24px; width:312px; color:#333; }
#login_form .textbox { border:1px solid #e0e0e0; padding:6px; margin-bottom:20px; border-radius:3px 3px 3px 3px; }
#login_form .bottom { text-align:right; }
#login_form .button { padding:8px 16px; font-size:14px; }
  </style>
</head>
<body>
  <form action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post">
  <div id="login_title">MiniCMS</div>
  <div id="login_form">
    <div id="login_form_box">
      <div class="label">帐号</div>
      <div class="textbox"><input name="user" type="text" /></div>
      <div class="label">密码</div>
      <div class="textbox"><input name="pass" type="password" /></div>
      <div class="bottom"><input name="login" type="submit" value="登录" class="button" /></div>
    </div>
  </div>
  </form>
</body>
</html>

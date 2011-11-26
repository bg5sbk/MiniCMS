<?php if (!isset($mc_config)) exit; ?>
<!DOCTYPE html>
<html dir="ltr" lang="zh-CN">
<head>
<meta charset="UTF-8" />
<title><?php mc_site_name(); ?> - <?php mc_site_desc(); ?></title>
<link href="<?php mc_theme_url('style.css'); ?>" type="text/css" rel="stylesheet"/>
<link href="<?php mc_theme_url('prettify/prettify.css'); ?>" type="text/css" rel="stylesheet"/>
<script type="text/javascript" src="<?php mc_theme_url('prettify/prettify.js'); ?>"></script>
</head>
<body onload="prettyPrint()">
  <div id="header">
    <div id="sitename"><?php mc_site_name(); ?></div>
    <div id="navbar">
      <ul>
        <li><a href="/">首页</a></li>
        <li><a href="/?tag/<?php echo urlencode("分享");?>/">分享</a></li>
        <li><a href="/?tag/<?php echo urlencode("吹牛");?>/">吹牛</a></li>
        <li><a href="/?projects/">项目</a></li>
        <li><a href="/?about/">关于</a></li>
      </ul>
    </div>
  </div>
  <div class="clearer"></div>
  <div id="content">
<?php if (mc_is_post()) { ?>
    <h1><?php mc_the_title(); ?></h1>
    <p class="info">发表于: <?php mc_the_date(); ?>&nbsp;<?php mc_the_time(); ?>, 标签: <?php mc_the_tags(); ?></p>
    <div><?php mc_the_content(); ?></div>
<?php } else if (mc_is_page()) { ?>
    <h1><?php mc_the_title(); ?></h1>
    <div><?php mc_the_content(); ?></div>
<?php } else { ?>
<?php   if (mc_is_tag()) { ?>
    <div id="page_info">标签：<?php mc_tag_name(); ?></div>
<?php   } ?>
<?php   while (mc_next_post()) { ?>
    <p class="post_link"><span class="date"><?php mc_the_date(); ?></span><?php mc_the_link(); ?></p>
<?php   } ?>
<?php } ?>
  </div>
  <div id="footer">本站由 <a href="http://20bit.com/?projects/minicms/" target="_blank">MiniCMS</a> 提供动力</div>
</body>
</html>

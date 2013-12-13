<?php if (!isset($mc_config)) exit; ?>
<!DOCTYPE html>
<html dir="ltr" lang="zh-CN">
<head>
<meta charset="UTF-8" />
<title><?php if (mc_is_post() || mc_is_page()) { mc_the_title(); ?> | <?php mc_site_name(); } else { mc_site_name(); ?> | <?php mc_site_desc(); }?></title>
<link href="<?php mc_theme_url('style.css'); ?>" type="text/css" rel="stylesheet"/>
</head>
<body>
  <div id="main">
  <div id="header">
    <div id="sitename"><a href="<?php mc_site_link(); ?>"><?php mc_site_name(); ?></a></div>
  </div>
  <div id="content">
  <div id="content_box">
<?php if (mc_is_post()) { ?>
    <div class="post">
      <h1 class="title"><?php mc_the_link(); ?></h1>
      <div class="tags"><?php mc_the_tags('','',''); ?> by <?php mc_nick_name(); ?> at <?php mc_the_date(); ?></div>
      <div class="content"><?php mc_the_content(); ?></div>
    </div>
    <?php if (mc_can_comment()) { ?>
    <?php mc_comment_code(); ?>
    <?php } ?>
<?php } else if (mc_is_page()) { ?>
    <div class="post">
      <?php /*<h1 class="title"><?php mc_the_link(); ?></h1>
      <div class="tags">by <?php mc_nick_name(); ?> at <?php mc_the_date(); ?></div> */ ?>
      <div class="content"><?php mc_the_content(); ?></div>
    </div>
    <?php if (mc_can_comment()) { ?>
    <?php mc_comment_code(); ?>
    <?php } ?>
<?php } else if (mc_is_archive()) { ?>
    <div class="date_list">
    <h1>月份</h1>
    <ul>
<?php mc_date_list(); ?>
    </ul>
    </div>
    <div class="tag_list">
    <h1>标签</h1>
    <ul>
<?php mc_tag_list(); ?>
    </ul>
    </div>
    <div class="clearer"></div>
<?php } else { ?>
<?php   if (mc_is_tag()) { ?>
    <div id="page_info"><span><?php mc_tag_name(); ?></span></div>
<?php   } else if (mc_is_date()) { ?>
    <div id="page_info"><span><?php mc_date_name(); ?></span></div>
<?php   } ?>
    <div class="post_list">
<?php   while (mc_next_post()) { ?>
    <div class="post">
      <h1 class="title"><?php mc_the_link(); ?></h1>
      <div class="tags"><?php mc_the_tags('','',''); ?> by <?php mc_nick_name(); ?> at <?php mc_the_date(); ?></div>
      <div class="clearer"></div>
    </div>
<?php   } ?>
    <div id="page_bar">
<?php   if (mc_has_new()) { ?>
      <span class="prev" style="float:left;"><?php mc_goto_new('&larr;较新文章'); ?></span>
<?php   } ?>
<?php   if (mc_has_old()) { ?>
      <span class="next" style="float:right;"><?php mc_goto_old('早期文章&rarr;'); ?></span>
<?php   } ?>
      <div class="clearer"></div>
    </div>
    <div class="clearer"></div>
    </div>
<?php } ?>
  </div>
  </div>
  <div id="side">
    <div id="navbar">
      <ul>
        <li><a href="<?php mc_site_link(); ?>/">首页</a></li>
        <li><a href="<?php mc_site_link(); ?>/?archive/">存档</a></li>
        <li><a href="<?php mc_site_link(); ?>/?rss/">订阅</a></li>
      </ul>
    </div>
  </div>
  <div class="clearer"></div>
  <div id="footer">本站由 <a href="http://1234n.com/?projects/minicms/" target="_blank">MiniCMS</a> 提供动力</div>
  </div>
</body>
</html>

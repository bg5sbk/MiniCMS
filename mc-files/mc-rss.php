<?php header("Content-Type: application/rss+xml"); ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
<rss version="2.0"
  xmlns:content="http://purl.org/rss/1.0/modules/content/"
  xmlns:wfw="http://wellformedweb.org/CommentAPI/"
  xmlns:dc="http://purl.org/dc/elements/1.1/"
  xmlns:atom="http://www.w3.org/2005/Atom"
  xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
  xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
>
<channel>
  <title><?php mc_site_name(); ?></title>
  <link><?php mc_site_link(); ?></link>
  <description><?php mc_site_desc(); ?></description>
  <language>zh_CN</language>
  <sy:updatePeriod>hourly</sy:updatePeriod>
  <sy:updateFrequency>1</sy:updateFrequency>
  <generator>http://1234n.com/?projects/minicms/</generator>
<?php while (mc_next_post()) { ?>
    <item>
      <title><?php mc_the_title(); ?></title>
      <link><?php mc_the_url(); ?></link>
      <guid><?php mc_the_url(); ?></guid>
      <dc:creator><?php mc_nick_name(); ?></dc:creator>
      <pubDate><?php mc_the_date(); ?> <?php mc_the_time(); ?></pubDate>
<?php mc_the_tags("      <category><![CDATA[", "\n", "]]></category>"); echo "\n"; ?>
      <content:encoded><![CDATA[<?php mc_the_content();?>]]></content:encoded>
    </item>
<?php   } ?>
</channel>
</rss>

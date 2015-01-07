<?php include 'core.php'; ?>
<!DOCTYPE html>
<!--
  The sites linked from this page are copyright their respective owners. Not me.
  Plus I take no responsibility for their content, etc.
-->
<html lang="en">
<head>
  <title>PHP Feed Reader</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <meta name="mobile-web-app-capable" content="yes">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div id="heading">
    <h1>PHP Feed Reader</h1>
  </div>
  <div id="container">
<?php foreach ($BLOGARRAY as $BLOGITEM): ?>
    <div id="<?=$BLOGITEM->getBlogMd5()?>" class="post <?=$BLOGITEM->getPostDateTime()?>">
      <div class="inner">
        <div class="post_header" style="background-image:url(https://www.google.com/s2/favicons?domain_url=<?=$BLOGITEM->getBlogURL()?>)">             
          <h2><a href="<?=$BLOGITEM->getBlogURL()?>" target="_blank"><?=htmlspecialchars($BLOGITEM->getBlogTitle())?></a></h2>
          <span class="post_date"><i>- <?=$BLOGITEM->getPostRelativeDate()?> </i></span>
        </div>
        <div class="post_contents">
          <p class="post_title"> <a href="<?=$BLOGITEM->getPostURL()?>" target="_blank"><?=$BLOGITEM->getPostTitle()?></a> </p>
          <p class="post_description"> <?=$BLOGITEM->getPostDescription($CHARLENGTH)?> </p>
        </div>  
      </div>
    </div>
<?php endforeach; ?>
  </div>
  <div id="footer">
    <span id="updated">Updated <?=time_elapsed_string(filemtime("/tmp/" . $BLOGITEM->getBlogMd5()))?></span> (<a href="index.php?mode=update">update now</a>) - <a href="index.php?mode=json">JSON</a>
    <span class="right">Content and linked pages are copyright their respective owners</span>
  </div>
</body>
</html>

<?php include 'core.php'; ?>
<!DOCTYPE html>
<!--
  The sites linked from this page are copyright their respective owners. Not me.
  Plus I take no responsibility for their content, etc.
-->
<html lang="en">
<head>
  <title>Urbex Feed Reader</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <meta name="mobile-web-app-capable" content="yes">
  <meta name="robots" content="noindex">
  <meta name="msapplication-TileColor" content="#da532c">
  <meta name="msapplication-TileImage" content="/mstile-144x144.png">
  <link rel="icon" type="image/png" href="/favicon-196x196.png" sizes="196x196">
  <link rel="icon" type="image/png" href="/favicon-160x160.png" sizes="160x160">
  <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96">
  <link rel="icon" type="image/png" href="/favicon-16x16.png" sizes="16x16">
  <link rel="icon" type="image/png" href="/favicon-32x32.png" sizes="32x32">
  <style>
    body{padding-top:3em;margin:0;background-color:#E5E5E5}
    h1,h2,p{margin:0}
    h2{font-size:1.3em;}
    a,h1{font-family:Arial,Helvetica,sans-serif}
    a{text-decoration:none;color:#0b67cd;}
    #heading{overflow:hidden;background:rgba(53,53,53,0.8);color:#fff;height:3em;line-height:3em;padding-left:1em;border-bottom:solid 4px rgba(4,151,205,0.5);top:0;position:fixed;width:100%;z-index:20;}
    #container{padding:10px 50px;max-width:1297px;margin:0 auto;z-index:10;}
    .post{width:33.3%;float:left;padding:0}
    .inner{box-shadow:0px 1px 5px rgba(0,0,0,.5);margin:0 8px 16px 8px;background-color:#fff;}
    .post_header{min-height:24px;background-color:#366CCC;background-repeat:no-repeat;background-position:right 15px center;position:relative;padding:0.5em 2em 1.3em 1em;}
    .post_header a{white-space:nowrap;display:block;overflow:hidden;text-overflow:ellipsis;color:#FAFAFA}
    .post_date{position:absolute;bottom:0.2em;left:1em;padding:1px 10px;color:#fafafa}
    .post_contents{padding:0.5em 1em 1em 1em;height:6em;overflow:auto;}
    #container:after{clear: both;content: "";display: block;}
    #footer{overflow:hidden;padding:1em;background-color:#ccc;box-shadow:0 -1px 5px #555;}
    #footer #notice{float:right}
    @media(max-width:1380px){.post{width:50%}}
    @media(max-width:880px){#container{padding:10px;}}
    @media(max-width:660px){#container{padding:10px 0} .post{float:inherit;width:inherit;} .inner{margin-bottom:8px} .post_contents{height:inherit;}}
  </style>
<?php include "/share/Web/site_template/analyticstracking.php"; ?>
</head>
<body>
  <div id=heading>
    <h1>Urbex Feed Reader</h1>
  </div>
  <div id=container>
<?php foreach ($BLOGARRAY as $BLOGITEM): ?>
    <div id="<?=$BLOGITEM->getBlogMd5()?>" class="post <?=$BLOGITEM->getPostDateTime()?>">
      <div class="inner">
        <div class="post_header" style="background-image:url('https://www.google.com/s2/favicons?domain_url=<?=$BLOGITEM->getBlogURL()?>')">             
          <h2><a href="<?=$BLOGITEM->getBlogURL()?>" target="_blank"><?=htmlspecialchars($BLOGITEM->getBlogTitle())?></a></h2>
          <span class=post_date><i>- <?=$BLOGITEM->getPostRelativeDate()?> </i></span>
        </div>
        <div class=post_contents>
          <p class=post_title> <a href="<?=$BLOGITEM->getPostURL()?>" target="_blank"><?=$BLOGITEM->getPostTitle()?></a> </p>
          <p class=post_description> <?=$BLOGITEM->getPostDescription($CHARLENGTH)?> </p>
        </div>  
      </div>
    </div>
<?php endforeach; ?>
  </div>
  <div id=footer>
    <span id=updated>Updated <?=time_elapsed_string(filemtime("/tmp/" . $BLOGITEM->getBlogMd5()))?> (<a href="index.php?mode=update">update now</a>) - <a href="index.php?mode=json">JSON</a></span>
    <span id=notice>Content and linked pages are copyright their respective owners</span>
  </div>
</body>
</html>
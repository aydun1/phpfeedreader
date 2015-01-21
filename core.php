<?PHP
$CACHETIME = 720; # minutes
$CHARLENGTH = 170; # number of characters to trim description to
$BLOGURLS = array( "http://www.abc.net.au/news/feed/51120/rss.xml",
                   "http://d.yimg.com/am/top_stories.xml",
                   "http://feeds.news.com.au/heraldsun/rss/heraldsun_news_breakingnews_2800.xml",
                   "http://www.smh.com.au/rssheadlines/top.xml",
                   "http://feeds.news.com.au/public/rss/2.0/news_national_3354.xml",
                   "http://sbs.feedsportal.com/c/34692/f/637303/index.rss",
                   "http://feeds.feedburner.com/TheAustralianNewsNDM?format=xml",
                   "http://feeds.feedburner.com/dailytelegraphbreakingnewsndm?format=xml",
                   "http://feeds.reuters.com/reuters/topNews?format=xml",
                   "http://feeds.bbci.co.uk/news/rss.xml",
                 ); 


function time_elapsed_string($ptime) {
  $etime = time() - $ptime;
  if ($etime < 1) {
    return 'Now';
  }
  $a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
              30 * 24 * 60 * 60       =>  'month',
               7 * 24 * 60 * 60       =>  'week',
              24 * 60 * 60            =>  'day',
              60 * 60                 =>  'hour',
              60                      =>  'minute',
              1                       =>  'second'
            );
  foreach ($a as $secs => $str) {
    $d = $etime / $secs;
    if ($d >= 1) {
      $r = round($d);
      return $r . ' ' . $str . ($r > 1 ? 's' : '') . " ago";
    }
  }
}



function compareDateTime($a, $b) {
  return strcmp($b->getPostDateTime(), $a->getPostDateTime());
}



function returnJSON($posts) {
  header('Content-Type: application/json');
  $json_data = array();
  foreach($posts as $myObj) {
    $ref = new ReflectionClass($myObj);
    $data = array();
    foreach (array_values($ref->getMethods()) as $method) {
      if ((0 === strpos($method->name, "get"))
            && $method->isPublic()) {
        $name = substr($method->name, 3);
        $name[0] = strtolower($name[0]);
        $value = $method->invoke($myObj);
        $data[$name] = $value;
      }
    }
    $json_data[] = $data;
  }  
  print json_encode($json_data);
}

function returnUpdated($posts) {
  header('Content-Type: application/json');
  $json_data = array();
  foreach($posts as $myObj) {
    $data = array();
    $data["blogMd5"] = $myObj->getBlogMD5();
	$data["postDateTime"] = $myObj->getPostDateTime();
    $json_data[] = $data;
  }  
  print json_encode($json_data);
}

function lastRefreshTime($BLOGURLS) {
  $refreshed;
  foreach($BLOGURLS as $BLOGURL) {
    $CACHEFILE  = "/tmp/" . md5($BLOGURL);
    if(file_exists($CACHEFILE)) {
      $file_time = time() - filemtime($CACHEFILE);
      if(empty($refreshed) || $refreshed < $file_time) {
        $refreshed = $file_time;
      }    
    }
  }
  if (empty($refreshed)) {
    return 9999;
  } else {
    return $refreshed; 
  }
}

function requestUpdate($BLOGURLS, $CACHETIME) {
  foreach($BLOGURLS as $BLOGURL) {
    $CACHEFILE  = "/tmp/" . md5($BLOGURL);
    if(!file_exists($CACHEFILE) || ((time() - filemtime($CACHEFILE)) > 60 * $CACHETIME)) {
      if($feed_contents = @file_get_contents($BLOGURL)) {
        $fp = fopen($CACHEFILE, 'w');
        fwrite($fp, $feed_contents);
        fclose($fp);
      }
    }
  }
}


function filesToArray($BLOGURLS) {
  foreach($BLOGURLS as $BLOGURL) {
    $CACHEFILE  = "/tmp/" . md5($BLOGURL);
    if ($rss = simplexml_load_file($CACHEFILE)) {
      $channel = $rss->channel;
      $item = $channel->item[0];
      $blogPost = new BlogDetails();
      $blogPost->setBlogMd5(md5($BLOGURL));
      $blogPost->setBlogTitle($channel->title);
      $blogPost->setBlogUrl($channel->link);
      $blogPost->setPostTitle($item->title);
      $blogPost->setPostUrl($item->link);
      $blogPost->setPostDescription($item->description);
      $blogPost->setPostDate($item->pubDate);
      $BLOGARRAY[] = $blogPost;
    }
  }
  usort($BLOGARRAY, "compareDateTime");
  return $BLOGARRAY;
}


class BlogDetails {
  private $blog_MD5;
  private $blog_TITLE;
  private $blog_URL;
  private $post_TITLE;
  private $post_URL;
  private $post_DESCRIPTION;
  private $post_DATE;

  public function setBlogMd5($blog_MD5){
    $this->blog_MD5=(string)$blog_MD5;
  }
  public function setBlogTitle($blog_TITLE){
    $this->blog_TITLE=(string)$blog_TITLE;
  }
  public function setBlogUrl($blog_URL){
    $this->blog_URL=(string)$blog_URL;
  }
  public function setPostTitle($post_TITLE){
    $this->post_TITLE=(string)$post_TITLE;
  }
  public function setPostUrl($post_URL){
    $this->post_URL=(string)$post_URL;
  }
  public function setPostDate($post_DATE){
    $this->post_DATE=strtotime($post_DATE);
  }
  public function setPostDescription($post_DESCRIPTION){
    $this->post_DESCRIPTION=(string)$post_DESCRIPTION;
  }

  public function getBlogMd5(){
    return $this->blog_MD5;
  }
  public function getBlogTitle(){
    return $this->blog_TITLE;
  }
  public function getBlogUrl(){
    return $this->blog_URL;
  }
  public function getPostTitle(){
    return $this->post_TITLE;
  }
  public function getPostUrl(){
    return $this->post_URL;
  }
  public function getPostDescription($CHARLENGTH){
    $CHARLENGTH = 170;
    $trimmedDesc = strip_tags($this->post_DESCRIPTION);
    $trimmedDesc = trim($trimmedDesc, " \r\n");
    if (strlen($trimmedDesc) > $CHARLENGTH and preg_match("/^.{1,$CHARLENGTH}\b/u", $trimmedDesc, $match)){
      $trimmedDesc=trim($match[0], " ") . "...";
    }
    return $trimmedDesc;
  }
  public function getPostDateTime(){
    return $this->post_DATE;
  }
  public function getPostRelativeDate(){
    $TIMEFORMAT = "j F Y, g:ia";
    return time_elapsed_string($this->post_DATE);
  }
}

class Database extends SQLite3
{
    function __construct($dbName)
    {
        $this->enableExceptions(true);

        try
        {
            parent::__construct($dbName, SQLITE3_OPEN_READWRITE );
        }
        catch(Exception $ex) { die( $ex->getMessage() ); }
    }
}




if(isset($_GET["mode"])){
  if(trim($_GET["mode"]) == 'json') {
    requestUpdate($BLOGURLS, $CACHETIME);
    $BLOGARRAY = filesToArray($BLOGURLS);
    returnJSON($BLOGARRAY);
    exit();
  }
  elseif(trim($_GET["mode"]) == 'update') {
    requestUpdate($BLOGURLS, 1);
    $BLOGARRAY = filesToArray($BLOGURLS);
    returnJSON($BLOGARRAY);
    exit();
  }
  elseif(trim($_GET["mode"]) == 'last-updated') {
    $BLOGARRAY = filesToArray($BLOGURLS);
    returnUpdated($BLOGARRAY);
    exit();
  }
}


$BLOGARRAY = filesToArray($BLOGURLS);
$REFRESHTIME = lastRefreshTime($BLOGURLS);


?>

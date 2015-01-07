<?PHP
$CACHETIME = 720; # minutes
$CHARLENGTH = 170; # number of characters to trim description to
$BLOGURLS = array( "http://urbex.aydun.net/rss/",
                   "http://sync-below.com/feed/",
                   "http://obscurepieces.com/feed/",
                   "http://lost-photons.blogspot.com/feeds/posts/default?alt=rss",
                   "http://brisbane-urbex.blogspot.com.au/feeds/posts/default?alt=rss",
                   "http://www.longexposure.net/?feed=rss2",
                   "http://www.the-f-stop.com/feed/",
                   "http://om2photo.com/feed/",
                   "http://saoirse-2010.livejournal.com/data/rss",
                   "http://www.abandonedjourney.com/feed/",
                   "http://undermontreal.com/feed/",
                   "http://uexplorer.wordpress.com/feed/",
                   "http://urbanexplorationsydney.blogspot.com.au/feeds/posts/default?alt=rss",
                   "http://urbexbayarea.tumblr.com/rss",
                   "http://urbexzone.wordpress.com/feed/",
                   "http://siologen.livejournal.com/data/rss",
                   "http://sectionsix.net/feed/",
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
  public function getPostDescription($numberChars){
    $trimmedDesc = strip_tags($this->post_DESCRIPTION);
    $trimmedDesc = trim($trimmedDesc, " \r\n");
    if (strlen($trimmedDesc) > $numberChars and preg_match("/^.{1,$numberChars}\b/u", $trimmedDesc, $match)){
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




if(php_sapi_name() == 'cli') {
requestUpdate($BLOGURLS, 1);
return;
}


elseif(isset($_GET["mode"])){
  if(trim($_GET["mode"]) == 'json') {
    requestUpdate($BLOGURLS, $CACHETIME);
    $BLOGARRAY = filesToArray($BLOGURLS);
    returnJSON($BLOGARRAY);
  }
  elseif(trim($_GET["mode"]) == 'update') {
    header('Location: ./');
    requestUpdate($BLOGURLS, 1);
  }
}

else {
  $BLOGARRAY = filesToArray($BLOGURLS);
  requestUpdate($BLOGURLS, $CACHETIME);

}

?>

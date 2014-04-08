<?

include("functions.php");

$mysqli = new mysqli("localhost", "root", "Jonathan3", "reader");

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    //exit();
}

if(isset($_GET["itemid"])) {

	$id = $_GET["itemid"]; 
	$result = $mysqli->query("SELECT * FROM news WHERE `id` = $id");
	
} else {
	
	$result = $mysqli->query("SELECT * FROM news ORDER BY `timePosted` DESC LIMIT 0,1");
	
}
//echo $id;


$row = $result->fetch_assoc();
$feedid = $row["feedId"];
$query = "SELECT * FROM feeds WHERE `id` = $feedid";
//echo $query;
$result2 = $mysqli->query($query);
$row2 = $result2->fetch_assoc();

?>


<div class="info">
	<div class="feed"><a href="<? echo $row["permalink"]; ?>"><? echo $row2["title"]; ?> &#10148;</a></div>
	<div class="date"><? echo date("l, F jS Y",strtotime($row["timePosted"])); ?></div>
</div>
<div class="title">
	<h1><?
		$title = str_replace("&lt;/i&gt;","",str_replace("&lt;i&gt;","",$row["title"]));
		$title = str_replace("&lt;/em&gt;","",str_replace("&lt;em&gt;","",$title)); 
		echo $title; 
	?></h1>
</div>
<div class="body">
	<?
  				if($row["htmlBody"] != NULL || $row["htmlBody"] != "") {
		  			
		  			$body = base64_decode($row["htmlBody"]);
		  			
	  			} else {
	  			
  					$body = base64_decode($row["body"]);
  					
  				}
  				$pos = strpos($body, "<p>");
  				$body = str_replace("<br clear=\"all\">", "", $body);
				$body = str_replace("<br>", "", $body);
				if($row["feedId"] == 4 || $row["feedId"] == 5) {
					$body = str_replace("<table", "<table style=\"display: none\"", $body);
				}
				$body = str_replace("<div>", "", $body);
				$body = str_replace("</div>", "", $body);
				//$body = str_replace("<a href=\"http://feeds", "<a style=\"display: none\" href=\"http://feeds", $body);
				$body = str_replace("<a href=\"http://da.feedsportal.com", "<a style=\"display: none\" href=\"http://da.feedsportal.com", $body);
				$body = str_replace("<p></p>", "<p style=\"display: none\"></p>", $body);
				$body = str_replace("<img width=\"1\" height=\"1\"", "<img width=\"1\" height=\"1\" style=\"display: none\"", $body);
				$body = str_replace("src=\"http://feeds.", "style=\"display: none\" src=\"http://feeds.", $body);
  				
  				if($pos == FALSE) {
		  			echo "<p class=\"added\">".$body."</p>";
  				} else {
	  				echo $body;
  				}  
	  			?>
</div>
<?php


?>

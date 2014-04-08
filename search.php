<?php

include("functions.php");

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

// Include SimplePie
// Located in the parent directory

include("simple_html_dom.php");

$mysqli = new mysqli("localhost", "root", "Jonathan3", "reader");

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

$text =  "\"".$mysqli->escape_string( $_GET["text"] )."\"";

$query = "SELECT * FROM `news` WHERE MATCH (`title`) AGAINST($text)";
	
$result = $mysqli->query($query);	

while ($row = $result->fetch_assoc()) {
    ?>
    <div class="item" id="<? echo $row["id"]; ?>">
	<?
	$title = str_replace("&lt;/i&gt;","",str_replace("&lt;i&gt;","",$row["title"]));
	$title = str_replace("&lt;/em&gt;","",str_replace("&lt;em&gt;","",$title)); 
	$title = (strlen($title) > 155) ? substr($title,0,152).'...' : $title;
	?>
	<div class="itemtitle">
		 <? echo $title; ?>
	</div>
	<div class="info">
		<?
		
			$feedid = $row["feedId"];
			$result2 = $mysqli->query("SELECT * FROM feeds WHERE `id` = $feedid");
			$row2 = $result2->fetch_assoc();
		
		?>
		<div class="feed"><? echo $row2["title"]; ?></div>
		<div class="date"><? echo doRelativeDate(date("YmdHis",strtotime($row["timePosted"]))); ?></div>
	</div>
	
	<?
  				$snip = strip_tags(base64_decode($row["body"]));
  				$snip = (strlen($snip) > 200) ? substr($snip,0,200)."..." : $snip;
  				if(strlen($snip) > 0) {
  				?>
  				
  				<div class="snippet">
  					<p>
  						<?
  							echo $snip;
  						?>
  					</p>
  				</div>
  				<? } ?>
  	</div>
    <?
    
}



?>
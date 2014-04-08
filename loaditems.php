<?

include("functions.php");

$mysqli = new mysqli("localhost", "root", "Jonathan3", "reader");

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

$id = 0;
if(isset($_GET["feedid"])) {
	
	$id = $_GET["feedid"];
	
}

 
$index = 0;
if(isset($_GET["theindex"])) {
	$index = $_GET["theindex"];
}
$offset = $index+20;

if($id == 0) {

	$result = $mysqli->query("SELECT * FROM news ORDER BY `timePosted` DESC, `id` DESC LIMIT $index,20");

} else {
	
	$result = $mysqli->query("SELECT * FROM news WHERE `feedId` = $id ORDER BY `timePosted` DESC, `id` DESC LIMIT $index,20");
	
}

if($id != 0) {
	$result2 = $mysqli->query("SELECT * FROM feeds WHERE `id` = $id");
	$row2 = $result2->fetch_assoc();
}
while($row = $result->fetch_assoc()) {
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
		if($id == 0) {
			$feedid = $row["feedId"];
			$result2 = $mysqli->query("SELECT * FROM feeds WHERE `id` = $feedid");
			$row2 = $result2->fetch_assoc();
		}
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
<?php
}
?>
<? if($result->num_rows == 20) { ?>

<div class="button" id="<? echo $offset; ?>">
	<div class="thefeedid" id="<? echo $id; ?>" style="display: none"></div>
  	Load More
</div>

<? } ?>
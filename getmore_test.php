<?php

ini_set('display_startup_errors',1);
ini_set('display_errors',1);
error_reporting(-1);

// Include SimplePie
// Located in the parent directory

include("simple_html_dom.php");

// Include SimplePie
// Located in the parent directory
include_once('autoloader.php');
include_once('idn/idna_convert.class.php');

// Create a new instance of the SimplePie object
$feed = new SimplePie();


$mysqli = new mysqli("localhost", "root", "Jonathan3", "reader");

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

$result = $mysqli->query("SELECT * FROM feeds WHERE `active` = 1");

while($row = $result->fetch_assoc()) {
	
	echo $row["id"].". ".$row["title"]." ".$row["lastUpdated"]."\n";
	
	$feedId = $row["id"];
	
	$url = $row["url"];
	
	$feed->set_feed_url($url);
	$feed->encode_instead_of_strip(false);
	$feed->enable_cache(false);
	$success = $feed->init();
	
	if($success) {
		$i = 0;
		foreach($feed->get_items() as $item) {
			
			if ($item->get_permalink()) {
				$permalink = "\"".$mysqli->real_escape_string($item->get_permalink())."\"";
				$permalinkNoQ = $mysqli->real_escape_string($item->get_permalink());
			} else {
				$permalink = "none";
			}
			
			if ($authorR = $feed->get_author())
			{
				$author = "\"".$mysqli->real_escape_string($authorR->get_name())."\"";
			} else {
				$author = "NULL";
			}
			
			$title = "\"".$mysqli->real_escape_string($item->get_title())."\"";
			if($item->get_content()) {
				$body = "\"". base64_encode($item->get_content())."\"";
			} else {
				$body = "NULL";
			}
			
			$timePosted = "\"".$item->get_date("Y-m-d H:i:s")."\"";
			//echo "$timePosted\n";
			$thumbnail = "NULL";
			
			if ($enclosure = $item->get_enclosure(0)) {
				
				if ($enclosure->get_thumbnail()) {
					$thumbnail = "\"".$mysqli->real_escape_string($enclosure->get_thumbnail())."\"";
				}
				
			}
			
			$result2 = $mysqli->query("SELECT id FROM news WHERE `permalink` = $permalink");
			if($row["parseHTML"] == 1 && $result2->num_rows == 0){
				
				
				//print "Parsing html...";
				$html = file_get_html($permalinkNoQ);
				$fullstory = "";
				
				
				if($row["title"] == "Politico Magazine") {
					
					foreach($html->find(".story-text") as $story) {

						foreach($story->find("*") as $tag) {
							
							if($tag->tag == "p") {
								
								$fullstory .= $tag->outertext;
								
							}
							
						}
						
					}
					
				}
				
				
				if($row["title"] == "Reuters World") {
					
					foreach($html->find(".focusParagraph") as $story) {
	
						foreach($story->find("*") as $tag) {
							
							if($tag->tag == "p") {
								
								$fullstory .= $tag->outertext;
								
							}
							
						}
						
					}
				
				
					foreach($html->find("#articleText") as $story) {
	
						foreach($story->find("*") as $tag) {
							
							if($tag->tag == "p") {
								
								$fullstory .= $tag->outertext;
								
							}
							
						}
						
					}
				
				}
				
				if($row["title"] == "BBC News") {
				
					foreach($html->find(".story-body") as $story) {
						foreach($story->find("*") as $tag) {
							
							foreach($tag->find("img") as $img) {
				
								$image = $img->outertext;
								$fullstory .= $image;
								
							}
							
							if($tag->tag == "div img") {
								
								$fullstory .= $tag->outertext;
								
							}	
							
							if($tag->tag == "span.cross-head") {
								
								$fullstory .= $tag->outertext;
								
							}
							
							if($tag->tag == "p") {
								
								$fullstory .= $tag->outertext;
								
							}
							
						}
						
					}
				
				}
				
				$fullstory = "\"".base64_encode($fullstory)."\"";
				
			}
			
			if($result2->num_rows == 0) {
			
				$mysqli->query("UPDATE `feeds` SET `lastUpdated`= CURRENT_TIMESTAMP WHERE `id` = $feedId");
				
				if($row["parseHTML"] == 0) {
					$query = "INSERT INTO `news`(`feedId`, `timePosted`, `title`, `body`, `author`, `permalink`, `thumbnail`) VALUES ($feedId,$timePosted,$title,$body,$author,$permalink,$thumbnail)";
				} else {
					$query = "INSERT INTO `news`(`feedId`, `timePosted`, `title`, `body`, `author`, `permalink`, `thumbnail`, `htmlBody`) VALUES ($feedId,$timePosted,$title,$body,$author,$permalink,$thumbnail,$fullstory)";
				}
				if($mysqli->query($query) == FALSE) {
					echo "\n\nBad Insert...\n$query\n\n";
				} else {
					echo "Added: ".$title."\n";
				}
				
				$i++;
				
			}
			
			
			
		}
		
		if($i == 0) {
			echo "No new news to add...\n";
		}
		echo "\n";
		
	} else {
		
		echo "Feed Failed to init...\n\n";
		
	}
	
}

?>
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
$log = "";

while($row = $result->fetch_assoc()) {
	
	$log .= $row["id"].". ".$row["title"]." ".$row["lastUpdated"]."\n";
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
			
			$today = date("Y-m-d");
			$date = strtotime("+1 day", strtotime($today));
			$date = date("Ymd", $date);
			if($item->get_date("Ymd") <= $date) {
				$timePosted = "\"".$item->get_date("Y-m-d H:i:s")."\"";
			} else {
				$timePosted = date("Y-m-d H:i:s");
			}
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
				
				if($row["title"] == "ScienceNewsline") {
				
					foreach($html->find("font.text") as $story) {
	
						foreach($story->find("*") as $tag) {
							
							if($tag->tag == "figure") {
								
								$fullstory .= $tag->outertext;
								
							}
							
							if($tag->tag == "p") {
								
								$fullstory .= $tag->outertext;
								
							}
							
							if($tag->tag == "blockquote") {
								
								$fullstory .= $tag->outertext;
								
							}
							
						}
						
					}
				
				}
				
				if($row["title"] == "XKCD") {
					
					$title = "\"".$html->find("div#ctitle", 0)->innertext."\"";
					$comic = $html->find("div#comic", 0);
					$fullstory .= "<p>".$comic->innertext."</p>";
					$body = $comic->find("img",0)->title;
					$fullstory .= "<p>".$body."</p>";
					$body = "\"". base64_encode($body)."\"";
					
				}
				
				if($row["title"] == "Ars Technica") {
					
					foreach($html->find(".article-content") as $story) {

						foreach($story->find("*") as $tag) {
							
							if($tag->tag == "figure") {
								
								$fullstory .= $tag->outertext;
								
							}
							
							if($tag->tag == "p") {
								
								$fullstory .= $tag->outertext;
								
							}
							
							if($tag->tag == "blockquote") {
			
								$fullstory .= $tag->outertext;
								
							}
							
						}
						
					}
					
				}
				
				if($row["title"] == "engadget") {
					
					foreach($html->find(".post-body") as $story) {

						foreach($story->find("*") as $tag) {
							
							if($tag->tag == "p" && $tag->tag != "p.read-more") {
								
								$fullstory .= $tag->outertext;
								
							}
							
							if($tag->tag == "img") {
								
								$fullstory .= $tag->outertext;
								
							}
							
							if($tag->tag == "blockquote") {
			
								$fullstory .= $tag->outertext;
								
							}
							
						}
						
					}
					
				}
				
				if($row["title"] == "Scientific American") {
					
					foreach($html->find(".article-content") as $story) {

						foreach($story->find("*") as $tag) {
							
							if($tag->tag == "p") {
								
								$fullstory .= $tag->outertext;
								
							}
							
							if($tag->tag == "img") {
								
								$fullstory .= $tag->outertext;
								
								
							}
							
							if($tag->tag == "blockquote") {
			
								$fullstory .= $tag->outertext;
								
							}
							
						}
						
					}
					
				}
				
				
				if($row["title"] == "Tech Crunch") {
					
					foreach($html->find(".article-entry") as $story) {

						foreach($story->find("*") as $tag) {
							
							if($tag->tag == "p") {
								
								$fullstory .= $tag->outertext;
								
							}
							
							if($tag->tag == "img") {
								
								$fullstory .= $tag->outertext;
								
							}
							
							if($tag->tag == "blockquote") {
			
								$fullstory .= $tag->outertext;
								
							}
							
						}
						
					}
					
				}
				
				
				if($row["title"] == "Wired") {
					
					foreach($html->find(".mainCopy") as $story) {

						foreach($story->find("*") as $tag) {
							
							if($tag->tag == "p") {
								
								$fullstory .= $tag->outertext;
								
							}
							
							if($tag->tag == "blockquote") {
			
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
							
							if($tag->tag == "blockquote") {
			
								$fullstory .= $tag->outertext;
								
							}
							
						}
						
					}
				
				
					foreach($html->find("#articleText") as $story) {
	
						foreach($story->find("*") as $tag) {
							
							if($tag->tag == "p") {
								
								$fullstory .= $tag->outertext;
								
							}
							
							if($tag->tag == "blockquote") {
			
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
							
							if($tag->tag == "blockquote") {
			
								$fullstory .= $tag->outertext;
								
							}
							
						}
						
					}
					
					foreach($html->find(".article") as $story) {
						foreach($story->find("*") as $tag) {
							
							foreach($tag->find("img") as $img) {
				
								$image = $img->outertext;
								$fullstory .= $image;
								
							}
							
							if($tag->tag == "span.cross-head") {
								
								$fullstory .= $tag->outertext;
								
							}
							
							if($tag->tag == "p") {
								
								$fullstory .= $tag->outertext;
								
							}
							
							if($tag->tag == "blockquote") {
			
								$fullstory .= $tag->outertext;
								
							}
							
						}
						
					}
					
					/*
					foreach($html->find(".extra-content") as $story) {
						foreach($story->find("*") as $tag) {
							
							foreach($tag->find("img") as $img) {
				
								$image = $img->outertext;
								$fullstory .= $image;
								
							}
							
							if($tag->tag == "span.cross-head") {
								
								$fullstory .= $tag->outertext;
								
							}
							
							if($tag->tag == "p") {
								
								$fullstory .= $tag->outertext;
								
							}
							
							if($tag->tag == "blockquote") {
			
								$fullstory .= $tag->outertext;
								
							}
							
						}
						
					}
					*/
				
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
					$log .= "\n\nBad Insert...\n$query\n\n";
					echo "\n\nBad Insert...\n$query\n\n";
				} else {
					$log .= "Added: ".$title."\n";
					echo "Added: ".$title."\n";
				}
				
				$i++;
				
			}
			
			
			
		}
		
		if($i == 0) {
			$log .= "No new news to add...\n";
			echo "No new news to add...\n";
		}
		$log .= "\n";
		echo "\n";
		
	} else {
		$log .= "Feed Failed to init...\n\n";
		echo "Feed Failed to init...\n\n";
		
	}
	
}

$log = "\"".base64_encode($log)."\"";

$query2 = "INSERT INTO `updateLog`(`timeRun`, `log`) VALUES (CURRENT_TIMESTAMP,$log)";
if($mysqli->query($query2) == FALSE) {
	
	echo "\n\nDid not update log D:\n\n";
	
}

?>
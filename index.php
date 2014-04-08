<?php

include("functions.php");

$mysqli = new mysqli("localhost", "root", "Jonathan3", "reader");

if ($mysqli->connect_errno) {
    printf("Connect failed: %s\n", $mysqli->connect_error);
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <title>rssrdr</title>
    <link rel="stylesheet" href="style.css" />
    <link rel="shortcut icon" href="favicon.ico" />
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.9.1.js"></script>
        <script type="text/javascript" language="javascript">
    
	    $(document).ready(function(){
			$( ".searcher" ).keyup(function( event ) {
				var thefeedid = 0; 
				if ($(".feed.selected").length > 0) { 
					var thefeedid = $(".feed.selected").attr("id"); 
				}
			
			  	var thetext = this.value;
			  	if(thetext.length > 3) {
			  	
					$("#items").empty();
					$("#items").addClass("spin");
					$.get( "search.php", { text: thetext} ).done(function( data ) {
						$("#items").removeClass("spin");
						$("#items").append(data);
						$(".itemtitle").css({'width':($(window).width() * .30)-25+'px'});
					});
					
				} else if(thetext.length > 0 && thetext.length <= 3) {		
					
					$("#items").empty();
					$("#items").addClass("spin");
					
				} else {
					
					$("#items").empty();
					$("#items").addClass("spin");
					$.get( "loaditems.php", { feedid: thefeedid} ).done(function( data ) {
	    				$("#items").removeClass("spin");
						$("#items").append(data);
						$(".itemtitle").css({'width':($(window).width() * .30)-25+'px'});
					});
					
					
				}
			  
			}).keydown(function( event ) {
				if ( event.which == 13 ) {
					event.preventDefault();
				}
			});
	    	
	    	$("#items").addClass("spin");
	    	$("#content").addClass("spin");
	    	
	    	var contentwidth = ($(window).width() * .55) - 2;
	    	$(".itemtitle").css({'width':($(window).width() * .30)-25+'px'});
	    
	        $('.fullheight').css({'height':(($(window).height())-36)+'px'});
			$('#content').css({'width':contentwidth+'px'});        
	
	        $(window).resize(function() {
	        	$('.fullheight').css({'height':(($(window).height())-36)+'px'});
	        	contentwidth = ($(window).width() * .55) - 2;
	        	$('#content').css({'width':contentwidth+'px'});
	        	$(".itemtitle").css({'width':($(window).width() * .30)-25+'px'});
	        });
	        
	        var hash = window.location.hash.substr(5);
	    	if(hash != "") {
		    	
		    	$("#content").empty();
				$.get( "loadcontent.php", { itemid: hash} ).done(function( data ) {
					$("#content").removeClass("spin");
					$("#content").append(data);
					//$(".itemtitle").css({'width':($(window).width() * .30)-25+'px'});
				});
		    	
	    	} else {
		    	
		    	$.get( "loadcontent.php" ).done(function( data ) {
					$("#content").removeClass("spin");
					$("#content").append(data);
					//$(".itemtitle").css({'width':($(window).width() * .30)-25+'px'});
				});
		    	
	    	}
	    	
	    	
	    	$.get( "loaditems.php" ).done(function( data ) {
	    			$("#items").removeClass("spin");
					$("#items").append(data);
					$(".itemtitle").css({'width':($(window).width() * .30)-25+'px'});
			});
	        
	        
	        
	        $(document).on("click", "li.feed", function(){
		        
				var theid = $(this).attr("id"); 
				$("#items").empty();
				$("#items").addClass("spin");
				
				//setTimeout(function (){
				
					$.get( "loaditems.php", { feedid: theid} ).done(function( data ) {
						$("#items").removeClass("spin");
						$("#items").append(data);
						$(".itemtitle").css({'width':($(window).width() * .30)-25+'px'});
					});
				
				//}, 500);
		        
	        });
	        
	        $(document).on("click", ".feed", function(){
	        	$(this).siblings().removeClass("selected");
		        $(this).addClass("selected");
	        });
	        
	        $(document).on("click", ".hfeeds", function(){
		        
		        $(".feed.selected").removeClass("selected");
				$("#items").empty();
				$("#items").addClass("spin");
				//setTimeout(function (){
					$.get( "loaditems.php" ).done(function( data ) {
						$("#items").removeClass("spin");
						$("#items").append(data);
						$(".itemtitle").css({'width':($(window).width() * .30)-25+'px'});
					});
				//}, 500);
		        
	        });
	        
	        $(document).on("click", "#thefeeds", function(){
		        
				$(".feed.selected").removeClass("selected");
				$("#items").empty();
				$("#items").addClass("spin");
				//setTimeout(function (){
					$.get( "loaditems.php" ).done(function( data ) {
						$("#items").removeClass("spin");
						$("#items").append(data);
						$(".itemtitle").css({'width':($(window).width() * .30)-25+'px'});
					});
				//}, 500);
		        
	        });
	        
	        $(document).on("click", ".item", function(){
		        
		        var theid = $(this).attr("id"); 
				location.hash = "item"+theid;
		        
		        $(this).siblings().removeClass("selected");
		        $(this).addClass("selected");
				
				$("#content").empty();
				$("#content").addClass("spin");
				//setTimeout(function (){
					$.get( "loadcontent.php", { itemid: theid} ).done(function( data ) {
						$("#content").removeClass("spin");
						$("#content").append(data);
						//$(".itemtitle").css({'width':($(window).width() * .30)-25+'px'});
					});
				//}, 500);
				
				
				
		        
	        });
	        
	        
	        $(document).on("click", ".button", function(){
		        
				var Aindex = $(this).attr("id"); 
				var theid = $(".thefeedid").attr("id");
				//alert("Index: "+Aindex+" FeedID: "+theid);
				$(this).empty();
				$(this).addClass("spin");
				
				$.get( "loaditems.php", { feedid: theid, theindex: Aindex} ).done(function( data ) {
					$("#items").append(data);
					$(".itemtitle").css({'width':($(window).width() * .30)-25+'px'});
					
				});
				
				$(this).slideUp();
	        });
	      
	        
	    });
	    
	</script>
    
  </head>
  
  <body>
  	
  	<div id="header">
  		<div class="hfeeds">
  			<h1>rssrdr</h1>
  		</div>
  		<div class="hitems">
  			<form id="search">
	  			<input name="q" class="searcher" type="text" size="40" placeholder="Search..." />
	  		</form>
  		</div>
  	</div>
  	
  	<div id="feeds" class="fullheight">
  		<div class="title" id="thefeeds">
  			All Feeds
  		</div>
  		<ul class="feeds">
  		<?php
  		$result = $mysqli->query("SELECT * FROM feeds ORDER BY `title` ASC");
  		$feedArray = array();
  		while($row = $result->fetch_assoc()) {
  			?>
  			<li class="feed" id="<? echo $row["id"]; ?>"><? echo $row["title"]; ?></li>
  		<?php
  			$feedArray[$row["id"]] = $row["title"];
  		}
  		?>
  		</ul>
  	</div>
  	<div id="items" class="fullheight">
  	 	
  	</div>
  	<div id="content" class="fullheight">
  		
  	</div>
  
  </body>
  
</html>
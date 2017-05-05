<?php
session_save_path("sessions");
session_start();
//ini_set('display_errors', 1);
require_once('getWordClouds.php');
 
?>
<html>

<head>
<meta charset="UTF-8">
<title>Twitter Word Cloud</title>

<link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="style.css">


<script src=jquery-1.12.1.js type="text/javascript"></script>
</head>

<body class = "container">
<?php if( isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn'] == true) : ?>


<h1>Welcome, <?php echo strtoupper($_SESSION['user']) ?>!</h1>

<img class="img-circle" src =<?php echo $_SESSION['imageLink'] ?> alt="images/default" style="width:128px;height:128px;">

<table style="width:100%">
<tr>
<td>Search Term:<input id="searchTerm"/></td>
</tr>
<tr>
<td>
 <form>
  <input type="radio" name="searchBy" value="keyword" checked="checked"> Search By Key Word/Phrase<br>
  <input type="radio" name="searchBy" value="userNameSearch"> Search By UserName<br>
</form>
</td> 
</tr>

<tr>
<td><button id = "search" >Search</button></td>
</tr>


</table>



<div id = "latest_wcloud" style="visibility:hidden;"> </div>
<br> 
<button id="shareButton" style="visibility:hidden;"> Share cloud</button>
<br> <br>



<h1>Top Five Liked Clouds</h1>
<div class = "likedCloud" data-sid=""></div>
<div class="wordCloudLikes" data-sid=""> </div><button class = "likeButton" data-sid="" >Like</button></td> <br>

<div class = "likedCloud" data-sid="">  </div>
<div class="wordCloudLikes" data-sid=""> </div><button class = "likeButton" data-sid="" >Like</button></td> <br>

<div class = "likedCloud" data-sid="">  </div>
<div class="wordCloudLikes" data-sid=""> </div><button class = "likeButton" data-sid="" >Like</button></td> <br>

<div class = "likedCloud" data-sid="">  </div>
<div class="wordCloudLikes" data-sid=""> </div><button class = "likeButton" data-sid="" >Like</button></td><br>

<div class = "likedCloud" data-sid="">  </div>
<div class="wordCloudLikes" data-sid=""> </div><button class = "likeButton" data-sid="" >Like</button></td><br>

<h1>Five Newest Shared Posts</h1>

<div class = "likedCloud" data-sid="">  </div>
<div class="wordCloudLikes" data-sid=""> </div><button class = "likeButton" data-sid="" >Like</button></td><br>

<div class = "likedCloud" data-sid="">  </div>
<div class="wordCloudLikes" data-sid=""> </div><button class = "likeButton" data-sid="" >Like</button></td><br>

<div class = "likedCloud" data-sid="">  </div>
<div class="wordCloudLikes" data-sid=""> </div><button class = "likeButton" data-sid="" >Like</button></td><br>

<div class = "likedCloud" data-sid="">  </div>
<div class="wordCloudLikes" data-sid=""> </div><button class = "likeButton" data-sid="" >Like</button></td><br>

<div class = "likedCloud" data-sid="">  </div>
<div class="wordCloudLikes" data-sid=""> </div><button class = "likeButton" data-sid="" >Like</button></td><br>

<br> <br>





<script>
$(document).ready(function(){

// populate the word cloud list

	$.ajax({type: "POST",
			dataType : 'json',
			url: 'getWordClouds.php',
			data:{
				getCloud: "true"
			},
			success: function(data){
			//var data = jQuery.parseJSON(wordClouds);	
			 for (i = 0; i < data.length; i++) { 
				//Set search-ids for the cloud and the like button
				$(document.getElementsByClassName("likedCloud")[i]).attr("data-sid", data[i][0]);
				$(document.getElementsByClassName("wordCloudLikes")[i]).attr("data-sid", data[i][0]);
				$(document.getElementsByClassName("likeButton")[i]).attr("data-sid", data[i][0]);
				//Set Html for the cloud and likes
				document.getElementsByClassName("likedCloud")[i].innerHTML= data[i][1];
				document.getElementsByClassName("wordCloudLikes")[i].innerHTML=data[i][2];
				}	
			}
		});	
		
		
	//set up long polling	
setInterval(function(){
		$.ajax({
			type: "POST",
			dataType : 'json',
			cache: false,
			url: 'getWordClouds.php',
			data:{
				getCloud: "true"
			},
			success: function(data){
			//var data = jQuery.parseJSON(wordClouds);	
			 for (i = 0; i < data.length; i++) { 
			 //$("#latest_wcloud").html(wordClouds);
			 //Set search-ids for the cloud and the like button
				$(document.getElementsByClassName("likedCloud")[i]).attr("data-sid", data[i][0]);
				$(document.getElementsByClassName("wordCloudLikes")[i]).attr("data-sid", data[i][0]);
				$(document.getElementsByClassName("likeButton")[i]).attr("data-sid", data[i][0]);
				//console.log(wordClouds);
				document.getElementsByClassName("likedCloud")[i].innerHTML = data[i][1];
				document.getElementsByClassName("wordCloudLikes")[i].innerHTML = data[i][2];
				}
			} 
		});
  }, 60000);	


//Ajax handler for search button
$("#search").click(function(){
        $.ajax({type: 'post',
			dataType : 'html',
			url: "showCloud.php",
			data:{
				searchType:$("input:radio[name=searchBy]:checked").val(),
				searchTerm: $("#searchTerm").val()
			},
			success: function(wordCloud){
			$("#latest_wcloud").css("visibility" , "visible");
			$("#latest_wcloud").html(wordCloud);
			$("#shareButton").css("visibility" , "visible");
			$("html, body").animate({ 
			scrollTop: $("#latest_wcloud").offset().top}, 1000);
			}
		});
    });
	
//Ajax handler for share button
$("#shareButton").click(function(){
        $.ajax({type: 'post',
			dataType : 'html',
			url: 'shareCloud.php',
			data:{
				searchTerm: $("#searchTerm").val(),
				wordCloud: $("#latest_wcloud").html()								
			},
			success: function(wordCloud){
			//$(".likedCloud")[0].html(wordCloud);
			
			
			
			$("#latest_wcloud").html("");
			$("#latest_wcloud").css("visibility" , "hidden");
			$("#shareButton").css("visibility" , "hidden");	
			$("html, body").animate({ scrollTop: 0 }, "fast");
			}
		});
    });
	

//Ajax handler for like button
$(".likeButton").click(function(){
		var element = this;
        $.ajax({type: 'post',
			dataType : 'html',
			url: 'likeCloud.php',
			data:{
				searchID: $(this).attr("data-sid"),							
			},
			success: function(updateLike){
				
			for (i = 0; i < 10; i++) { 
				
				if( $(document.getElementsByClassName("wordCloudLikes")[i]).attr("data-sid") 
					== $(element).attr("data-sid"))
					
				{
					console.log("here at ", i);
					val =parseInt($(document.getElementsByClassName("wordCloudLikes")[i]).html()) + 1;
					$(document.getElementsByClassName("wordCloudLikes")[i]).html(val);
				}
				}
				
				
			}
		});
    });
		

		
});	

</script>




<?php else: 
header('Refresh:1; url=login.php');
echo 'Not Logged in, Redirecing';
		?>


		


<?php endif; ?>
</body>
</html>




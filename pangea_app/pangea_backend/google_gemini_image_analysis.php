
<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
// temporarly extend time limit
set_time_limit(300);
?>

<style>
.css_ai{
background:#ddd;color:black;padding:6px;border:none;border-radius:25%;
}

.css_ai:hover{
background: orange;
color:black;

}
</style>


<?php

include ('settings.php');


if (isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {

$file_name= strip_tags($_POST['file_name']);
$image_prompt= strip_tags($_POST['image_prompt']);

if($google_gimini_apikey ==''){
echo "<div  style='background:red;color:white;padding:10px;border:none;'>Google Gemini Model API Key is Empty... Please Ask Admin to set it at <b>settings.php</b> File..</div><br>";
exit();
}

// Get the image and convert into string 
$img = file_get_contents("uploads/$file_name"); 
  
// Encode the image string data into base64 
$data = base64_encode($img); 
  
// Display the output 
//echo $data; 



$payload ='{
"contents":[
    {
      "parts":[
        {"text": "'.$image_prompt.'"},
        {
          "inline_data": {
            "mime_type":"image/png",
            "data": "'.$data.'"
          }
        }
      ]
    }
  ]
}';



$url ="https://generativelanguage.googleapis.com/v1beta/models/gemini-pro-vision:generateContent?key=$google_gimini_apikey";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch);
$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);


// catch error message before closing
if (curl_errno($ch)) {

 $error_msg = curl_error($ch);

echo "<div style='color:white;background:red;padding:10px;'><b>Curl Error: $error_msg</b></div>";
exit();
}

curl_close($ch);

$json = json_decode($output, true);



if($http_status==200){

echo "<div style='color:white;background:green;padding:10px;'><h4> Image Analysis Successful.</h4></div><br>";

$result_gemini = $json['candidates'][0]['content']['parts'][0]['text'];


echo "<span class='css_ai'><b>Google Gemini Response: </b> $result_gemini</span><br>";


// remove or unlink uploaded files once Gemini AI Analysis is completed

unlink("uploads/$file_name");

echo "
<script>
$(document).ready(function(){
$('#clear_btn').click(function () {

//$('#file_content_image').val('');
$('#image_prompt').val('');
 $('#result_im').empty(); 
 $('#result_image').empty(); 
 $('#up2').empty(); 
 $('#imageupload_preview').empty();			
		});				
	});
</script>

 <div class='form-group col-sm-12'>
                          
<input type='button' id='clear_btn' class='pull-right btn btn-danger' value='Clear Result' />
 	
                    </div>
";

}


elseif($http_status==0){
echo "<div style='color:white;background:red;padding:10px;'>There is an Issue Making API Call to Google Gemini API. Please Check Internet Connections</div>";
}

else{

echo "<div style='color:white;background:red;padding:10px;'>There is an Issue Making API Call to Google Gemini API. Error. Please Check Internet Connections</div>";


}   







}
else{
echo "<div id='' style='background:red;color:white;padding:10px;border:none;'>
Direct Page Access not Allowed<br></div>";
}


?>

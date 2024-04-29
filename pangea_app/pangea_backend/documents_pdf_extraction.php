
<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
ini_set('max_execution_time', 300); //300 seconds = 5 minutes
// temporarly extend time limit
set_time_limit(300);



if (isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {

include('settings.php');

$file_name= strip_tags($_POST['file_name']);
$backend_site_url= strip_tags($_POST['backend_site_urlx']);
$ajax_loading_image= strip_tags($_POST['ajax_loading_imagex']);

// Include Composer autoloader if not already done.
include './pdfparser/vendor/autoload.php';

// Parse pdf file and build necessary objects.
$parser = new \Smalot\PdfParser\Parser();
$pdf    = $parser->parseFile("uploads/$file_name");
$text = $pdf->getText();

if($text !=''){

// remove or unlink uploaded files once extraction is completed

unlink("uploads/$file_name");

echo "<div style='color:white;background:green;padding:10px;'>Step 2.) PDF File Documents Extraction Successful. You can now Run AI Analysis on the Text Documents..</div>";
//echo  "<script>alert('PDF File Documents Extraction Successful...');</script>";


echo "<div class='col-sm-12 form-group'>
      <textarea class='form-control redact_text' name='redact_text' rows='3'>$text</textarea><br>
</div>
<script>
$(document).ready(function(){
$('.result_all_hide').show();
});
</script>
";




echo "
<script>
$(document).ready(function(){

var redact_text = $('.redact_text').val();
var backend_site_url= '$backend_site_url';
var ajax_loading_image= '$backend_site_url';


if(redact_text==''){
alert('Text to be Redacted Cannot be Empty');

}

else{

$('#loader_redact').fadeIn(400).html('<br><div style=color:black;background:#ddd;padding:10px;><img src=$ajax_loading_image style=font-size:20px> &nbsp;Please Wait!.  Data Redaction in Progress..</div>');
var datasend = {redact_text:redact_text, backend_site_url:backend_site_url, ajax_loading_image:ajax_loading_image};


$.ajax({
			
			type:'POST',
			url:'$backend_site_url/redact.php',
			data:datasend,
                        crossDomain: true,
			cache:false,
			success:function(msg){

                        $('#loader_redact').hide();
				//$('#result_redact').fadeIn('slow').prepend(msg);
$('#result_redact').html(msg);



			
			}
			
		});
		
		}
		
					
	});


</script>




<br>
<div class='well'>
<div id='loader_redact'></div>
<div id='result_redact'></div>
</div>

";





echo "
<script>
$(document).ready(function(){
$('#clear_btnx').click(function () {

$('#file_content').val('');
 $('#result_ex').empty(); 
 $('#result_pdf').empty(); 
 $('#up').empty(); 			
		});				
	});
</script>

 <div class='form-group col-sm-12'>
                          
<input type='button' id='clear_btnx' class='pull-right btn btn-danger' value='Clear Result' />
 	
                    </div>
";




}
else {
echo "<div style='color:white;background:red;padding:10px;'>PDF File Documents Extraction Failed. Please Check Internet Connections</div>";
}   


}
else{
echo "<div id='' style='background:red;color:white;padding:10px;border:none;'>
Direct Page Access not Allowed<br></div>";
}


?>

<?php
error_reporting(0);
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");


if (isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {

include('setting.php');


$docs = strip_tags($_POST['redact_text']);
$backend_site_url= strip_tags($_POST['backend_site_url']);
$ajax_loading_image= strip_tags($_POST['ajax_loading_image']);


// sanitize Extracted Text documents

// Remove special characters except comma fullstop and space
$text_check = preg_replace('/[^A-Za-z0-9,. ]/', '', $docs);




if($text_check ==''){

echo "<div  style='background:red;color:white;padding:10px;border:none;'>Data to be Redacted Cannot be Empty</div><br>";
exit();
}



if($redact_access_token ==''){
echo "<div  style='background:red;color:white;padding:10px;border:none;'>Redact Access Token is Empty</div><br>";
exit();
}

 $data_param= '{"return_result":true,"debug":true,
"rules":["DATE_TIME","IP_ADDRESS","URL","EMAIL_ADDRESS","NRP","LOCATION","PERSON","PHONE_NUMBER","US_DRIVER_LICENSE","US_ITIN","US_PASSPORT","US_SSN","CREDIT_CARD","CRYPTO","IBAN_CODE","US_BANK_NUMBER"],
"text": "'.$text_check.'"}';




$url ="https://redact.aws.us.pangea.cloud/v1/redact";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, TRUE);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', "Authorization: Bearer $redact_access_token"));  
curl_setopt($ch, CURLOPT_POSTFIELDS, $data_param);
//curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);  // Note: set to true in production for better security
$output = curl_exec($ch); 


$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// catch error message before closing
if (curl_errno($ch)) {
    //echo $error_msg = curl_error($ch);
}
curl_close($ch); 





if($output ==''){

echo "<div style='background:red;color:white;padding:10px;border:none;'>
 Please Ensure there is Internet Connections and Try Again</div><br>";
exit();
}


$json = json_decode($output, true);

$redacted_text = $json['result']['redacted_text'];
$request_id = $json['request_id'];
$summary = $json['summary'];
$status = $json['status'];




if($request_id !='' && $status != 'Success'){
echo "<div style='background:red;color:white;padding:10px;border:none;'>
 Sensitive Data Redaction Failed. Try Again.  Error: $summary  </div><br>";
exit();

}








if($request_id !='' && $status == 'Success' ){
echo "<div style='background:green;color:white;padding:10px;border:none;'>Step 3.) Sensitive Data Redaction Successful</div><br>";
}
echo "<div class='well'>

<h4>Original Text Content Before Redaction: </h4>$text_check</br><br>
<h4>Redacted Text:</h4> $redacted_text<br>                              
</div>

<br>
<center><h4>Redacted Parameters:</h4></center><br>

";

           
foreach($json['result']['report']['recognizer_results'] as $row){

$field_type = $row['field_type'];
$score = $row['score'];
$percent =  $score * 100;
$percent2 ="$percent %";
$text = $row['text'];
$redacted = $row['redacted'];
if($redacted ==1){
$r = "<span style='color:green;font-size:16px'><b>1(True)</b></span>";
}else{

$r = "<span style='color:red;font-size:16px'><b>0(False)</b></span>";
}

$start = $row['start'];
$end = $row['end'];



echo "<div style='' class='xcx1 col-sm-12'>


<br>
<b style='font-size:16px;'>Field Type: $field_type</b> <br>
<b >Score:</b>   $score &nbsp;&nbsp;&nbsp;&nbsp;($percent2)<br>
<b >Text:</b>   $text<br>
<b >Redacted Status:</b>   $r<br>

<br>
</div>";



}



}
else{
echo "<div id='' style='background:red;color:white;padding:10px;border:none;'>
Direct Page Access not Allowed<br></div>";
}

?>







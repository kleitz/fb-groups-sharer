<?php
if(isset($_GET['iframe'])){die("");}
echo '<html><head><link href="css/bootstrap.min.css" rel="stylesheet"><style>body {padding-top: 40px;padding-bottom: 40px;background-color: #eee;}</style></head><body>';
function kill($msg){
	logPosting($msg . " at: " . date("F j, Y, g:i a"));
	if(strpos($msg, 'LOGIN ERROR') !== false){
		die("<div class=\"alert alert-danger\">Worng username or password , if you wanna try the service Try username 'demo' , Password 'demo'. :)</div></body></html>");
	}else{
		die("<div class=\"alert alert-danger\">".$msg."</div></body></html>");
	};
}
function logPosting($action){
	file_put_contents("log.txt",$action . "\n", FILE_APPEND | LOCK_EX);
}

$USER=isset($_POST['username'])? $_POST['username']: "";
switch($USER){
		//user: demo , pass: demo
	case "demo":
                        if($_POST['password'] == "d0b610c894eae2eaffa0f47bcb9c5511"){logPosting("User: demo Start Session: " . date("F j, Y, g:i a"));main();}else{kill("LOGIN ERROR USER DEMO");}
			braek;
	default:
		kill("LOGIN ERROR");
}


@header('Content-Type: text/html; charset=utf-8');

function get_groups(){
	global $AT;
        $graph_url="https://graph.facebook.com/v2.2/me/groups?access_token=" . $AT;
        $ch = curl_init();
        $data=array('q' => base64_encode($graph_url));
        $querystring=http_build_query($data);
        curl_setopt($ch, CURLOPT_URL,'http://showvision.info/index.php' . '?' . $querystring);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
//        curl_setopt($ch,CURLOPT_FOLLOWLOCATION,true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

        $json= curl_exec($ch);
        $json = str_replace('\n',"<br/>",$json);


        $data = json_decode($json,TRUE,JSON_UNESCAPED_UNICODE);
        $groups=array('ids' => array(),'names' => array());
        foreach($data['data'] as $d){
                array_push($groups['ids'],$d['id']);
                array_push($groups['names'],$d['name']);
        }
        return $groups;

}


function post_Group($gid,$message="",$pic="",$link=""){
	global $AT;
	if($pic!=""){$op="photos";}else{$op="feed";}
        $graph_url="https://graph.facebook.com/v2.2/$gid/$op?access_token=" . $AT;
        $data=array('q' => base64_encode($graph_url));
        $querystring1=http_build_query ($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,'http://showvision.info/index.php' . '?' . $querystring1);
        $post=array('message' => $message,'link' => $link,'url'=>$pic);
        $querystring=http_build_query ($post);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$querystring);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

        $json= curl_exec($ch);
        $json = str_replace('\n',"<br/>",$json);
        $data = json_decode($json,TRUE,JSON_UNESCAPED_UNICODE);
	return $data;
}

function uploadImg($img){
	$target_dir = "uploads/";
	$target_file = $target_dir . basename($img["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	// Check if image file is a actual image or fake image
	$check = getimagesize($img["tmp_name"]);
	if($check !== false) {
	   $uploadOk = 1;
	} else {
	   $reason="File is not an image.";
	   $uploadOk = 0;
	}
	// Check if file already exists
	if (file_exists($target_file)) {
		return $target_file;
	}
	// Check file size
	if ($img["size"] > 500000) {
	    $reason="Sorry, your file is too large.";
           $uploadOk = 0;
	}
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
	    $reason="Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
           $uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
	    kill("Sorry, your file was not uploaded due to: " . $reason);
	// if everything is ok, try to upload file
	} else {
	    if (move_uploaded_file($img["tmp_name"], $target_file)) {
		return $target_file;
	    } else {
	       	 kill("Sorry, there was an error uploading your file.");
	    }
	}
	return $target_file;
}

function decrypt_AT($cc){
	$key = '7asreh';
	$iv = '12345678';
	$cipher = mcrypt_module_open(MCRYPT_BLOWFISH,'','cbc','');

	mcrypt_generic_init($cipher, $key, $iv);
	$decrypted = mdecrypt_generic($cipher,$cc);
	mcrypt_generic_deinit($cipher);
	return $decrypted;
}
function validate_AT($AT){
        $graph_url="https://graph.facebook.com/v2.2/me/permissions?access_token=" . $AT;
        $ch = curl_init();
        $data=array('q' => base64_encode($graph_url));
        $querystring=http_build_query($data);
        curl_setopt($ch, CURLOPT_URL,'http://showvision.info/index.php' . '?' . $querystring);
        curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

        $json= curl_exec($ch);
        $json = str_replace('\n',"<br/>",$json);
        $data = json_decode($json,TRUE,JSON_UNESCAPED_UNICODE);

	if(isset($data['error'])){
		return false;
	}
	$ug=$pa=1;
	foreach($data['data'] as $perm){
		if($perm['permission'] == "user_groups" && $perm['status'] == "granted"){
			$ug=0;	
		}elseif($perm['permission'] == "publish_actions" && $perm['status'] == "granted"){
			$pa=0;
		}
	}
	if($ug == 0 && $pa ==0){
		return true;
	}else{
		return false;
	}
}

function main(){
	if(!isset($_POST['atoken'])){kill("Please enter An access token");}
	global $AT;
	$AT=$_POST['atoken'];
	if(!validate_AT($AT)){kill("ERROR, NOT VALID ACCESS TOKEN.");}


	$post=empty($_POST['message']) ? kill("NO MESSAGE SPECIFIED"): $_POST['message'];

	$link=isset($_POST['link']) ? $_POST['link'] : "";
	//upload image if available
	$pic= empty($_FILES['pic']['size']) ? "" : uploadImg($_FILES['pic']);
	$groups=get_groups();
	logPosting("start posting: " . $post . " " . $link ." " . $pic . " To all Groups ...");

	$i=0;
	$err=0;
	foreach ($groups['ids'] as $id){
		$PID=post_Group($id,$post,$pic,$link);
	        if(isset($PID['id'])){
	                echo "<br/><div class=\"alert alert-success\">Done : <a target=\"_blank\" href='https://www.facebook.com/" . $PID['id'] . "'>". $groups['names'][$i] ."</a></div>";
	        }else{
	                echo "<br/><br/><div class=\"alert alert-warning\"> ERROR: [" . $PID['error']['message'] . "] , ".  $PID['error']['error_user_msg'] . "<a href='https://www.facebook.com/groups/" .$id.">". $groups['names'][$i] ."</a></div>";
			$err++;
	        }
		$i++;
		sleep(1);
	}
	echo "</body></html>";
	logPosting("Ending " .$_POST['username'] ."'s session with " . $err . "ERRORS , at " . date("F j, Y, g:i a") . "\n\n");
}

?>


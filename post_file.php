<?php

// If you want to ignore the uploaded files, 
// set $demo_mode to true;
$demo_mode = false;
$upload_dir = 'uploads/';
$allowed_ext = array('jpg','jpeg','png','gif');


if(strtolower($_SERVER['REQUEST_METHOD']) != 'post'){
	exit_status(['status'=>'Error! Wrong HTTP method!',
			'code'=>'403']);
}


if(array_key_exists('dropbox',$_FILES) && $_FILES['dropbox']['error'] == 0 ){
	
	$pic = $_FILES['dropbox'];
	$pic['name'] = urldecode($pic['name']);//russian names quickfix

	if(!in_array(get_extension($pic['name']),$allowed_ext)){
		exit_status(['status'=>'Only '.implode(',',$allowed_ext).' files are allowed!',
			'code'=>'403']);
	}	

	if($demo_mode){
		
		// File uploads are ignored. We only log them.
		
		$line = implode('		', array( date('r'), $_SERVER['REMOTE_ADDR'], $pic['size'], $pic['name']));
		file_put_contents('log.txt', $line.PHP_EOL, FILE_APPEND);
		
		exit_status(['status'=>'Uploads are ignored in demo mode.',
			'code'=>'403']);
	}
	
	
	// Move the uploaded file from the temporary 
	// directory to the uploads folder:
	
	if(move_uploaded_file($pic['tmp_name'], $upload_dir.$pic['name'])){//$upload_dir.'1.'.get_extension($pic['name'])
		exit_status(['status'=>'File was uploaded successfuly!',
			'code'=>'200',
			'file'=>$upload_dir.$pic['name'],
			'filename'=>$pic['name'],
			]);
	}
	
}

exit_status('Something went wrong with your upload!');


// Helper functions

function exit_status($str){
	if (is_string($str)) {echo json_encode(array('status'=>$str));}
	elseif (is_array($str)){echo json_encode($str);}
	exit;
}

function get_extension($file_name){
	$ext = explode('.', $file_name);
	$ext = array_pop($ext);
	return strtolower($ext);
}
?>
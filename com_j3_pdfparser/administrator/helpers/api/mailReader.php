<?php

require_once "Mail/mimeDecode.php";


/*
 * @class mailReader.php

 */
class mailReader {
    var $saved_files = Array();
    var $send_email = FALSE; // Send confirmation e-mail back to sender?
    var $save_msg_to_db = FALSE; // Save e-mail message and file list to DB?
    var $save_directory; // A safe place for files. Malicious users could upload a php or executable file, so keep this out of your web root
    var $allowed_senders = Array(); // Allowed senders is just the email part of the sender (no name part)
    var $allowed_mime_types = Array(
        'application/pdf'
    );
    var $debug = FALSE;

    var $raw = '';
    var $decoded;
    var $from;
	var $to;
    var $subject;
    var $body;
    var $username;
	Var $order_id;
    var $joom_user_email;
	var $hika_order_id;
    /**
     * @param $save_directory (required) A path to a directory where files will be saved
     * @param $pdo (optional) A PDO connection to a database for saving emails 
     */
    public function __construct($save_directory,$pdo = NULL){
        if(!preg_match('|/$|',$save_directory)){ $save_directory .= '/'; } // add trailing slash if needed
            $this->save_directory = $save_directory;
        $this->pdo = $pdo;
    }

    /**
     * @brief Read an email message
     *
     * @param $src (optional) Which file to read the email from. Default is php://stdin for use as a pipe email handler
     *
     * @return An associative array of files saved. The key is the file name, the value is an associative array with size and mime type as keys.
     */
    public function readEmail($src = 'php://stdin'){
        // Process the e-mail from stdin
        $fd = fopen($src,'r');
        while(!feof($fd)){ $this->raw .= fread($fd,1024); }

        // Now decode it!
        // http://pear.php.net/manual/en/package.mail.mail-mimedecode.decode.php
        $decoder = new Mail_mimeDecode($this->raw);
        $this->decoded = $decoder->decode(
            Array(
                'decode_headers' => TRUE,
                'include_bodies' => TRUE,
                'decode_bodies' => TRUE,
            )
        );


        // Set $this->from_email and check if it's allowed
        $this->from = $this->decoded->headers['from'];
		 $this->to = $this->decoded->headers['to'];

        $this->from_email = preg_replace('/.*<(.*)>.*/',"$1",$this->from);
     

        // Set the $this->subject
        $this->subject = $this->decoded->headers['subject'];

/////////////////////////////////////////////////////



//$findme    = 'Requested Reports';

$params = &JComponentHelper::getParams('com_lpdfparser');
$findme=$params->get('search_subject');
$pos1 = stripos($this->subject, $findme);

if ($pos1 === false) {
	//mail subject does not match
//return false;
}


//get order id from subject....

$temp_str=explode("/",$this->subject);



$order_id=$temp_str[count($temp_str)-1];

$this->order_id=trim($order_id);

$order_id=trim($order_id);

 $query = "SELECT order_user_id FROM  #__hikashop_order  WHERE order_number = '".$order_id."'";




$this->pdo->setQuery($query);
  $hika_user_id = $this->pdo->loadResult();


if(empty($hika_user_id)) return;

 $query = "SELECT user_cms_id FROM  #__hikashop_user  WHERE user_id = '".$hika_user_id."'";
$this->pdo->setQuery($query);
  $joom_user_id = $this->pdo->loadResult();



if(empty($joom_user_id)) return;


 $query = "SELECT name FROM  #__users  WHERE id = '".$joom_user_id."'";
$this->pdo->setQuery($query);
  $username = $this->pdo->loadResult();

$this->username=$username;


$query = "SELECT email FROM  #__users  WHERE id = '".$joom_user_id."'";
$this->pdo->setQuery($query);
  $email = $this->pdo->loadResult();

$this->joom_user_email=$email;


 $query = "SELECT order_id FROM  #__hikashop_order  WHERE order_number = '".$order_id."'";
$this->pdo->setQuery($query);
  $hika_order_id = $this->pdo->loadResult();

$this->hika_order_id=$hika_order_id;

///////////////////////////////////////////


        // Find the email body, and any attachments
        // $body_part->ctype_primary and $body_part->ctype_secondary make up the mime type eg. text/plain or text/html
        if(isset($this->decoded->parts) && is_array($this->decoded->parts)){
            foreach($this->decoded->parts as $idx => $body_part){
                $this->decodePart($body_part);
            }
        }

        if(isset($this->decoded->disposition) && $this->decoded->disposition == 'inline'){
            $mimeType = "{$this->decoded->ctype_primary}/{$this->decoded->ctype_secondary}"; 

            if(isset($this->decoded->d_parameters) &&  array_key_exists('filename',$this->decoded->d_parameters)){
                $filename = $this->decoded->d_parameters['filename'];
            }else{
                $filename = 'file';
            }

            $this->saveFile($filename,$this->decoded->body,$mimeType);
            $this->body = "Body was a binary";
        }

        // We might also have uuencoded files. Check for those.
        if(!isset($this->body)){
            if(isset($this->decoded->body)){
                $this->body = $this->decoded->body;
            }else{
                $this->body = "No plain text body found";
            }
        }

        if(preg_match("/begin ([0-7]{3}) (.+)\r?\n(.+)\r?\nend/Us", $this->body) > 0){
            foreach($decoder->uudecode($this->body) as $file){
                // file = Array('filename' => $filename, 'fileperm' => $fileperm, 'filedata' => $filedata)
                $this->saveFile($file['filename'],$file['filedata']);
            }
            // Strip out all the uuencoded attachments from the body
            while(preg_match("/begin ([0-7]{3}) (.+)\r?\n(.+)\r?\nend/Us", $this->body) > 0){
                $this->body = preg_replace("/begin ([0-7]{3}) (.+)\r?\n(.+)\r?\nend/Us", "\n",$this->body);
            }
        }


        // Put the results in the database if needed
        if($this->save_msg_to_db && !is_null($this->pdo)){
            $this->saveToDb();
        }

        // Send response e-mail if needed
        if($this->send_email && $this->from_email != ""){
            $this->sendEmail();
        }

        // Print messages
        if($this->debug){
            $this->debugMsg();
        }

        return $this->saved_files;
    }

    /**
     * @brief Decode a single body part of an email message
     *
     * @note Recursive if nested body parts are found
     *
     * @note This is the meat of the script.
     *
     * @param $body_part (required) The body part of the email message, as parsed by Mail_mimeDecode
     */
    private function decodePart($body_part){
        if(array_key_exists('name',$body_part->ctype_parameters)){ // everyone else I've tried
            $filename = $body_part->ctype_parameters['name'];
        }else if($body_part->ctype_parameters && array_key_exists('filename',$body_part->ctype_parameters)){ // hotmail
            $filename = $body_part->ctype_parameters['filename'];
        }else{
            $filename = "file";
        }

        $mimeType = "{$body_part->ctype_primary}/{$body_part->ctype_secondary}"; 

        if($this->debug){
            print "Found body part type $mimeType\n";
        }

        if($body_part->ctype_primary == 'multipart') {
            if(is_array($body_part->parts)){
                foreach($body_part->parts as $ix => $sub_part){
                    $this->decodePart($sub_part);
                }
            }
        } else if($mimeType == 'text/plain'){
            if(!isset($body_part->disposition)){
                $this->body .= $body_part->body . "\n"; // Gather all plain/text which doesn't have an inline or attachment disposition
            }
        } else if(in_array($mimeType,$this->allowed_mime_types)){
            $this->saveFile($filename,$body_part->body,$mimeType);
        }
    }

    /**
     * @brief Save off a single file
     *
     * @param $filename (required) The filename to use for this file
     * @param $contents (required) The contents of the file we will save
     * @param $mimeType (required) The mime-type of the file
     */
    private function saveFile($filename,$contents,$mimeType = 'unknown'){
       // $filename = preg_replace('/[^a-zA-Z0-9_-]/','_',$filename);

        $unlocked_and_unique = FALSE;
        while(!$unlocked_and_unique){
            // Find unique
         //   $name = time() . "_" . $filename;

           $name =$this->order_id.'  '.$this->username.'  '.$filename;

           // while(file_exists($this->save_directory . $name)) {
           ///     $name = time() . "_" . $filename;
           // }

            // Attempt to lock
            $outfile = fopen($this->save_directory.$name,'w');
            if(flock($outfile,LOCK_EX)){
                $unlocked_and_unique = TRUE;
            }else{
                flock($outfile,LOCK_UN);
                fclose($outfile);
            }
        }

        fwrite($outfile,$contents);
        fclose($outfile);

        // This is for readability for the return e-mail and in the DB
        $this->saved_files[$name] = Array(
            'size' => $this->formatBytes(filesize($this->save_directory.$name)),
            'mime' => $mimeType
        );
    }

    /**
     * @brief Format Bytes into human-friendly sizes
     *
     * @return A string with the number of bytes in the largest applicable unit (eg. KB, MB, GB, TB)
     */
    private function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    } 

    /**
     * @brief Save the plain text, subject and sender of an email to the database
     */
    private function saveToDb(){


$params = &JComponentHelper::getParams('com_lpdfparser');
$allowed_temp=$params->get('allowed_domains');
$hikaorderstatus=$params->get('hikaorderstatus');


$allowed_temp=strtolower($allowed_temp);
$allowed=explode(",",$allowed_temp);

    $explodedEmail = explode('@', $this->from_email);
    $domain = array_pop($explodedEmail);
 $domain=strtolower( $domain);
    if ( ! in_array($domain, $allowed))
    {
        die('Not Allowed');
    }

 
    }

    /**
     * @brief Send the sender a response email with a summary of what was saved
     */
    private function sendEmail(){
    
       $params = &JComponentHelper::getParams('com_lpdfparser');
       $notify_address=$params->get('notify_address');
       $notify_address_name=$params->get('notify_address_name');
       $mail_subject=$params->get('mail_subject');
       $mail_content=$params->get('mail_content');
     $hikaorderstatus=$params->get('hikaorderstatus');


$mail_subject=str_replace('{ORDER_NO}',$this->order_id,$mail_subject);
$mail_content=str_replace('{ORDER_NO}',$this->order_id,$mail_content);


		require 'PHPMailerAutoload.php';

		$mail = new PHPMailer;
		$mail->CharSet = 'UTF-8';

		$mail->setFrom($notify_address, $notify_address_name);
		$mail->addAddress($this->joom_user_email, $this->joom_user_email);
		$mail->Subject  = $mail_subject;
		$mail->Body     = $mail_content;
        $mail->IsHTML(true);       // <=== call IsHTML() after $mail->Body has been set.



		foreach($this->saved_files as $f => $data){
		$filename=mb_convert_encoding($f,'UTF-8','UTF-8');
		$mail->AddAttachment($this->save_directory.'/'.$filename, $name = $filename,  $encoding = 'base64', $type = 'application/pdf');
		}


//////////////////////////////////

$attachments='';


$acount=0;

		foreach($this->saved_files as $f => $s){

		if($acount ==0)
			$attachments = $f;
		else
			$attachments = $attachments.'|'. $f;

		$acount++;

		}

      $created_date=date('Y-m-d H:i:s');

        $insert_query = "INSERT INTO #__lpdfparser_ ( `order_number`, `username`, `from_address`,to_address, `subject`, `mail_body`,created_at,downloadlink,hika_order_id) VALUES ( '$this->order_id', '$this->username', '$this->from_email','$this->joom_user_email', '$mail_subject', '$mail_content','$created_date','$attachments','$this->hika_order_id'); ";
		$this->pdo->setQuery($insert_query);
		$this->pdo->query();



     $query = "UPDATE  #__hikashop_order  SET order_status='$hikaorderstatus' WHERE order_id = '".$this->hika_order_id."'";


     $this->pdo->setQuery($query);
	$this->pdo->query();

///////////////////////////////////////////

		if(!$mail->send()) {
	//	echo 'Message was not sent.';
		} else {
		//echo 'Message has been sent.';
		}



    }

    /**
     * @brief Print a summary of the most recent email read
     */
    private function debugMsg(){
        print "From : $this->from_email\n";
        print "Subject : $this->subject\n";
        print "Body : $this->body\n";
        print "Saved Files : \n";
        print_r($this->saved_files);
    }
}

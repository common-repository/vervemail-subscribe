<?php
//Send document to https://email.vervemail.com/api/xmlrpc/index.php?data=<XMLMessage>

/*
 * Developed by Marvin Chacon  Last edit: Jan-26-2016
 */
class XMLVervemail{

    
public $xml;
public $api;
public $shared_secret;
private $data;
private $root;
private $methodCall ;
private $methodNameSuscriber;
private $methodActualName="";
private $emailSuscriptor="";
private $tagEmail = true;
public $response ;

function __construct($api_key,$shared_secret){
            $this->api = $api_key;
            $this->shared_secret= $shared_secret;
            
             $this->xml= new DomDocument('1.0', 'UTF-8');
    

            $this->root = $this->xml->createElement('api');
           $this->root = $this->xml->appendChild($this->root);

            $authentication=$this->xml->createElement('authentication');
            $authentication =$this->root->appendChild($authentication);

            $api_key=$this->xml->createElement('api_key',$this->api);
            $api_key=$authentication->appendChild($api_key);

            $shared_secret=$this->xml->createElement('shared_secret',$this->shared_secret);
            $shared_secret=$authentication->appendChild($shared_secret);

            $response_type = $this->xml->createElement('response_type','xml');
            $response_type = $authentication->appendChild($response_type);

            $this->data = $this->xml->createElement('data');
           $this->data = $this->root->appendChild($this->data);
            $this->createTagMethodCall();
      
           $this->methodNameSuscriber= $this->xml->createElement('methodName');
      $this->methodNameSuscriber= $this->methodCall->appendChild($this->methodNameSuscriber);
}



private function createTagMethodCall(){
    $this->methodCall = $this->xml->createElement('methodCall');
    $this->methodCall = $this->data->appendChild($this->methodCall);
}

private function createTagMethodName($methodName){
  if( $this->methodActualName != $methodName){
      /*$oldMethod= $this->xml->documentElement;
      $olderMethod = $oldMethod->getElementsByTagName('methodName')->item(0);*/
      $this->methodCall->removeChild($this->methodNameSuscriber);
      $this->methodNameSuscriber= $this->xml->createElement('methodName',$methodName);
      $this->methodNameSuscriber= $this->methodCall->appendChild($this->methodNameSuscriber);
      $this->methodActualName = $methodName;
      $this->createTagEmail();
  }

 
}

private function createTagMethodNameNoEmail($methodName){ //Just to use without email tag
  if( $this->methodActualName != $methodName){
      /*$oldMethod= $this->xml->documentElement;
      $olderMethod = $oldMethod->getElementsByTagName('methodName')->item(0);*/
      $this->methodCall->removeChild($this->methodNameSuscriber);
      $this->methodNameSuscriber= $this->xml->createElement('methodName',$methodName);
      $this->methodNameSuscriber= $this->methodCall->appendChild($this->methodNameSuscriber);
      $this->methodActualName = $methodName;
     
  }

 
}

private function createTagEmail(){
    if( $this->tagEmail==true){
    $emailNode= $this->xml->createElement('email',$this->emailSuscriptor);
    $this->methodCall->appendChild($emailNode);
    $this->tagEmail= false;
    }
}

function setEmailSuscriber($emailSusc){
    $this->emailSuscriptor= $emailSusc;
}



function updateSuscriber( $firstnameSusc , $lastnameSusc){
 
    $this->createTagMethodSuscriber("legacy.manage_subscriber");
    $firstnameNode= $this->xml->createElement('firstname',$firstnameSusc);
    $this->methodCall->appendChild($firstnameNode);
     
     $lastnameNode= $this->xml->createElement('lastname',$lastnameSusc);
     $this->methodCall->appendChild($lastnameNode);
    }
    
function updateSuscriberFirstName( $firstnameSusc ){
    
    $this->createTagMethodName("legacy.manage_subscriber");
    
    $firstnameNode= $this->xml->createElement('firstname',"$firstnameSusc");
     $this->methodCall->appendChild($firstnameNode);
}

function updateSuscriberLastName($lastnameSusc ){
    
    $this->createTagMethodName("legacy.manage_subscriber");
    
    $lastnameNode= $this->xml->createElement('lastname',"$lastnameSusc");
    $this->methodCall->appendChild($lastnameNode);
 
}
function updateSuscriberEmail($newEmail){
      
    $this->createTagMethodName("legacy.manage_subscriber");
    $newEmail= $this->xml->createElement('newemail',"$newEmail");
    $this->methodCall->appendChild($newEmail);
 
}

function updateSuscriberZipCode($newZip){
      
    $this->createTagMethodName("legacy.manage_subscriber");
    $newZip= str_replace("'","\'",$newZip);
    $newZip= $this->xml->createElement('postal_code',"$newZip");
    $this->methodCall->appendChild($newZip);
 
}

function updateSuscriberCity( $newCity ){
    
    $this->createTagMethodName("legacy.manage_subscriber");
    
    $newCity= $this->xml->createElement('city',"$newCity");
    $this->methodCall->appendChild($newCity);
 
}

function updateSuscriberAddress( $newAddress ){
    
    $this->createTagMethodName("legacy.manage_subscriber");
    
    $newAddress= $this->xml->createElement('address',"$newAddress");
    $this->methodCall->appendChild($newAddress);
 
}

function updateSuscriberState($newState){
    
    $this->createTagMethodName("legacy.manage_subscriber");
    
    $newState= $this->xml->createElement('state',"$newState");
    $this->methodCall->appendChild($newState);
 
}

function updateSuscriberHomePhone($homePhone){
    
    $this->createTagMethodName("legacy.manage_subscriber");
    
    $homePhone = $this->xml->createElement('phone_hm',"$homePhone");
    $this->methodCall->appendChild($homePhone);
 
}

function deleteSuscriberAllSegments()
{
    $this->createTagMethodName("legacy.manage_subscriber");
    $optOut= $this->xml->createElement('optout',"Y");
    $this->methodCall->appendChild($optOut);
}

function deleteSuscriberSpecificSegment($segmentID)
{
    $this->createTagMethodName("legacy.manage_subscriber");
    $grpremove= $this->xml->createElement('grpremove',$segmentID);
    $this->methodCall->appendChild($grpremove);
    $optOut= $this->xml->createElement('optout',"Y");
    $this->methodCall->appendChild($optOut);
}

function addSuscriberThirdPartyNoEmail($segmentIdThirdPartyNoEmail){
    $this->createTagMethodName("legacy.manage_subscriber");
    $Node=$this->xml->createElement('grp',$segmentIdThirdPartyNoEmail);
    $this->methodCall->appendChild($Node);
}

function documentToString(){
   return $this->xml->saveHTML();
}

function documentToFile($nameFile,$route){
    $this->xml->save($route.$nameFile.".xml");
}

function getSegments(){
    $this->createTagMethodName("account.getStaticSegments");
}

function setRetrieveFieldSuscriber($Fieldname ){
    $this->createTagMethodName("legacy.retrieve_active");
     $fieldRetrieve= $this->xml->createElement($Fieldname);
    $this->methodCall->appendChild($fieldRetrieve);
    if($Fieldname=="optin_date")
         $this->setRetrieveFieldSendSuscriber();
    $basic= $this->xml->createElement("basic",1);
    $this->methodCall->appendChild($basic);
        
}

private function setRetrieveFieldSendSuscriber(){
    $this->createTagMethodName("legacy.retrieve_active");
     $fieldRetrieve= $this->xml->createElement('send','on');
    $this->methodCall->appendChild($fieldRetrieve);
}

function checkEmailSuscriber(){
     $this->createTagMethodName("legacy.retrieve_active");
      $basic= $this->xml->createElement("basic",1);
    $this->methodCall->appendChild($basic); 
    $response=$this->sendToVerveMail();
    $response = new SimpleXMLElement($response);
    if(isset($response->item[0]->responseData[0]->manifest[0]->contact_data))
        return true;
    else
        return false;
}


function sendToVerveMail(){
       $ch = curl_init (); 
       curl_setopt ($ch, CURLOPT_POST, true);
       curl_setopt($ch, CURLOPT_URL, "https://email.vervemail.com/api/xmlrpc/index.php");
       $data = array ("data"=>$this->documentToString());
       curl_setopt ($ch, CURLOPT_POSTFIELDS,$data);
       curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt( $ch , CURLOPT_SSL_VERIFYPEER , false );
       curl_setopt( $ch , CURLOPT_SSL_VERIFYHOST , false ); 
       curl_setopt($ch, CURLOPT_LOW_SPEED_TIME, 1800);   // for a period of 180
       $response = curl_exec ($ch);
       if ($response != false)
          return ($response);
       else
           echo "Error curl :" .curl_error ($ch); 
}

function updateOrAddCustomField($IDCustomField,$value){ //Creates a custom field
      
    $this->createTagMethodName("legacy.manage_subscriber");
    $value= str_replace("'","\'",$value);
    $IDCustomField= $this->xml->createElement("custval"."$IDCustomField",$value);
    $this->methodCall->appendChild($IDCustomField);
 
}

function addSuscribertToSegment($idSegment){ // Adds the tag <grp>. Example <grp>123</grp>
    $this->createTagMethodName("legacy.manage_subscriber");
    $segment= $this->xml->createElement("grp","$idSegment");
    $this->methodCall->appendChild($segment);
}



function retrieveSegmentsOfTheSuscriber(){//It gets all segments of a specific subscriber
    $this->createTagMethodName("legacy.retrieve_active");
    $includeSegments = $this->xml->createElement("return_groups","1");
    $this->methodCall->appendChild($includeSegments);
}

function removeSuscriberFromSegments($listIDs){ 
    $this->createTagMethodName("legacy.manage_subscriber");
    $nodeListIDs = $this->xml->createElement("grpremove","$listIDs");
    $this->methodCall->appendChild($nodeListIDs);
}

function getAllSegmentsAccount(){ // It retrieves all segments of the Client's Acoount
    $this->createTagMethodNameNoEmail("legacy.retrieve_segment");
    $node = $this->xml->createElement("return_group_data","1");
    $this->methodCall->appendChild($node);
}

function getSuscribersAccount($offset){// Specifies just one segment or many segments
    $this->createTagMethodNameNoEmail("legacy.retrieve_active");
    $includeSegments = $this->xml->createElement("valid","1");
    $this->methodCall->appendChild($includeSegments);
    $node = $this->xml->createElement("limit_rows","10000");
    $this->methodCall->appendChild($node);
    $node = $this->xml->createElement("limit_offset","$offset");
    $this->methodCall->appendChild($node);
}

function getSuscriberMesssages( $email ,$days ){// Range of days  is 1-90 
    $this->createTagMethodNameNoEmail("subscribers.getSubscriber");
    $node = $this->xml->createElement("email",$email);
    $this->methodCall->appendChild($node);
    $node = $this->xml->createElement("message_history_days_prior",$days);
    $this->methodCall->appendChild($node);
}

function getSuscriberMesssagDetail($mes_cont_id){// Range of days  is 1-90 
    $this->createTagMethodNameNoEmail("subscribers.getMessageDetail");
    $node = $this->xml->createElement("mes_cont_id",$mes_cont_id);
    $this->methodCall->appendChild($node);
}

function getMessageStats($mess_id){
    $this->createTagMethodNameNoEmail("legacy.message_stats");
    $node = $this->xml->createElement("mess_id",$mess_id);
    $this->methodCall->appendChild($node);
}

function legacy_message_stats( $start_date,$end_date ){//To get account messages 
    $this->createTagMethodNameNoEmail("legacy.message_stats");
    $node = $this->xml->createElement("start_date","$start_date");
    $this->methodCall->appendChild($node);
    $node = $this->xml->createElement("end_date","$end_date");
    $this->methodCall->appendChild($node);
}

function getSuscribersClickID($link_id_clicked){// Gets subscribers according to the link_id clicked
    $this->createTagMethodNameNoEmail("legacy.retrieve_active");
    $includeSegments = $this->xml->createElement("link_id_clicked","$link_id_clicked");
    $this->methodCall->appendChild($includeSegments);
 
}

function getSuscribersOpenMessage($id_message){ //Returns all subscribers that opened the specified message.
    $this->createTagMethodNameNoEmail("legacy.retrieve_active");
    $include= $this->xml->createElement("mess_id_opened","$id_message");
    $this->methodCall->appendChild($include);
}


function statistics_getMessageSubscriberData( $id_message , $action_types ){ //Gets asynchronous statistics of a message,openers , clickers
    $this->createTagMethodNameNoEmail("statistics.getMessageSubscriberData");
    $node = $this->xml->createElement("mess_id","$id_message");
    $this->methodCall->appendChild($node);
    $node = $this->xml->createElement("action_types","$action_types");
    $this->methodCall->appendChild($node);
    
}

function utilities_getTasks( $id_tasks){ //to verify the status  of a asynchronous method
    $this->createTagMethodNameNoEmail("utilities.getTasks");
    $node = $this->xml->createElement("task_id","$id_tasks");
    $this->methodCall->appendChild($node);
}

function utilities_getFile( $file_url ){ //to get the file of a asynchronous method
    $this->createTagMethodNameNoEmail("utilities.getFile");
    $node = $this->xml->createElement("file","$file_url");
    $this->methodCall->appendChild($node);
}

function getSubscribers( $date_joined_start = null ,$date_joined_end =null ,$only_segments_of = null){ // It uses an asynchronous mode , so the response must be gotten using utilities_getTasks
    $this->createTagMethodNameNoEmail("legacy.retrieve_active");
     
    $node = $this->xml->createElement("asynchronous","1");
    $this->methodCall->appendChild($node);
     
    if( $date_joined_start!= null ){
        $node = $this->xml->createElement("date_joined_start","$date_joined_start");
        $this->methodCall->appendChild($node);
    }
     
    if( $date_joined_end!= null ){
        $node = $this->xml->createElement("date_joined_end","$date_joined_start");
        $this->methodCall->appendChild($node);
    }

     
     if( $only_segments_of != null ){
        $node = $this->xml->createElement("groups","$only_segments_of");
        $this->methodCall->appendChild($node);
    }

}

function legacy_send_campaign( $segments ,$from_email , $from_name,$subject ,$html_message ,$plain_message ){
    //Use it to store draft messages
    $this->createTagMethodNameNoEmail("legacy.send_campaign");
    $node = $this->xml->createElement("grp","$segments");
    $this->methodCall->appendChild($node);
    $node = $this->xml->createElement("from_email","$from_email");
    $this->methodCall->appendChild($node);
    $node = $this->xml->createElement("reply_email","$from_email");
    $this->methodCall->appendChild($node);
    $node = $this->xml->createElement("fromdesc","$from_name");
    $this->methodCall->appendChild($node);
    $node = $this->xml->createElement("msubject","$subject");
    $this->methodCall->appendChild($node);
    $node = $this->xml->createCDATASection("<rich_mbody><![CDATA[$html_message]]></rich_mbody>");
    $this->methodCall->appendChild($node);
    $node = $this->xml->createCDATASection ("<text_mbody><![CDATA[$plain_message]]></text_mbody>");
    $this->methodCall->appendChild($node);
    $node = $this->xml->createElement("send","Y");
    $this->methodCall->appendChild($node);
    
    $response = $this ->sendToVerveMail();
    if( strpos($response, "<responseCode><![CDATA[202]]></responseCode>") !== false ){
        $xml_response = simplexml_load_string(  $response ,'SimpleXMLElement',LIBXML_NOCDATA );
        $message_key = $xml_response->item->responseData->message_key;
        if( $this->legacy_send_campaign_send( $message_key ) )
            return true;
        else {
            return false;
        }
    }
    else 
        return false;
}

function legacy_send_campaign_send( $message_key ){
    // Use it to send the draft message by email
    
    $verve = new XMLVervemail( $this->api , $this->shared_secret);
    $verve->createTagMethodNameNoEmail("legacy.send_campaign");
    $node = $verve->xml->createElement("message_key","$message_key");
    $verve->methodCall->appendChild($node);
    $response = $verve->sendToVerveMail();
    
    $xml_response = simplexml_load_string(  $response ,'SimpleXMLElement',LIBXML_NOCDATA );
    if( $xml_response->item->responseCode == 201)
        return true;
    else 
        return false;
    
}

}

?>
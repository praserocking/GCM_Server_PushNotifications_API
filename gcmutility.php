<?php

class GCMUtility{

	/***********************************************************************

	Google Cloud Messenger Utility Class

	Class to send Push Notifications for Android Powered Devices

	@author Shenbaga Prasanna,S
	@date 2nd February, 2014

	Sample Usage
	------------
	$gcm= new GCMUtility('<your api key>');
	$gcm->set_time_to_live(<seconds>); //*** optional parameter : int
	$gcm->set_restricted_package_name(<restricted package name>); //*** optional parameter: string
	$gcm->set_device_ids(<device IDs given by GCM>); // mandatory parameter: either array of strings or a string
	$gcm->set_message(<message to be sent>); // mandatory parameter: either a string or array of string with string keys ;example: array("number":"345");
	if($gcm->send())echo "Message Sent";
	else echo "Message Sending not Successful";

	************************************************************************/

	//URL Constant
	define(URL,"https://android.googleapis.com/gcm/send");

	//Variables
	private $time_to_live;
	private $restricted_package_name;
	private $device_ids=array();
	private $messages=array();

	//Constructor with Google API Browser Key as Parameter
	//Parameter Type: String
	function __contruct($apiKey){
		define(API_KEY,$apiKey);
	}

	//Function to set Time to Live (TTL)
	//Parameter Type: Int (seconds)
	//Default Value : 300 seconds
	public function set_time_to_live($seconds){
		$this->time_to_live=$seconds;
	}

	//Function to set the restricted package name. GCM Messages are bound to be recieved by this package only.
	// Parameter type : String
	//This option is not used unless specified
	public function set_restricted_package_name($packageName){
		$this->restricted_package_name=$packageName;
	}

	//Function to set Device IDs to which the Message is gonna be sent
	//It can be a single string or an array of strings
	public function set_device_ids($devid){
		if(is_array($devid))
			$this->device_ids=$devid;
		else
			$this->device_ids=array($devid);
	}

	//Function to set Messages to be sent. 
	//Messages can be sent as a array of strings with string keys or single string to be sent with key "message"
	// parameter : Array of strings or a string
	public function set_message($message){
		if(is_array($message))
			$this->messages=$message;
		else
			$this->messages=array("message"=>$message);
	}

	// FUNCTION TO SEND MESSAGES
	public function send(){
		$post_data=array(
					'registration_ids' => $this->device_ids,
					'data' => $this->messages
					);
		if(isset($this->time_to_live))
			$post_data['time_to_live']=$this->time_to_live;
		if(isset($this->restricted_package_name))
			$post_data['restricted_package_name']=$this->restricted_package_name;
		$headers=array(
					'Authorization: key='.API_KEY,
					'Content-Type: application/json'
					);
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_URL,URL);
		curl_setopt($ch,CURLOPT_POST,true);
		curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($post_data));
		$res=curl_exec($ch);
		curl_close($ch);
		$result=json_decode($res);
		return $result->{'success'};		// 0 if it fails and 1 if its success
	}
}

<?php
	class SalesModel {
	 
	  static public function getData()
	  {
	    return 'Model data put me in a view please.';
	  } 

	public function store(){
		$input = Input::all();
		//$auth_token = "a34b73b9d71f6ac2efeb7b5082550a47"; //himali test account
		$auth_token = "94fed615de4374d70928b580f434c793";
		$validator = $this->validate($input);
		if( $validator->passes() ) {
			//need to enable this code after testing
			//$this->insertToZoho();
			return $validator;
		}else{
			return $validator;
		}
	}

	public function validateAddress(){
		$input = Input::all();
		$addressError = array();
		$wsdl="http://demo1.intechiq.com/iqoffice2.wsdl";
		$username = "MyConnect01";
		$password = "xx2mj6g2";
		$maxAddrReturned = "100";
		$delimiter = "|";
		$error = "";
		$flag = 0;
		# init SoapClient
		$client=new SoapClient($wsdl);
		$searchaddress = "";
		if(isset($input['searchaddress']) && $input['searchaddress']){
			$searchaddress = trim($input['searchaddress']);
		}else{
			$unit_number = "";
			if(isset($input['unit_number']) && $input['unit_number'])
				$unit_number = $input['unit_number']."/";
			$txtAddr1 = $unit_number.$input['street_number']." ".$input['street_name']." ".$input['street_type'];
		    $txtAddr2 = "";
		    $txtAddr3 = "";
		    $txtLocality = $input['suburb'];
		    $txtState = $input['state'];
		    $txtPostCode = $input['postcode'];
		    $searchaddress = trim($txtAddr1).' '.trim($txtAddr2).' '.trim($txtAddr3).' '.trim($txtLocality).' '.trim($txtState).' '.trim($txtPostCode);
		}

	    $n1 = new SoapVar("UserId",XSD_STRING,"string","http://www.w3.org/2001/XMLSchema");
		$v1 = new SoapVar($username,XSD_STRING,"string","http://www.w3.org/2001/XMLSchema");
		$n2 = new SoapVar("Password",XSD_STRING,"string","http://www.w3.org/2001/XMLSchema");
		$v2 = new SoapVar($password,XSD_STRING,"string","http://www.w3.org/2001/XMLSchema");
		$n3 = new SoapVar("Delimiter",XSD_STRING,"string","http://www.w3.org/2001/XMLSchema");
		$v3 = new SoapVar($delimiter,XSD_STRING,"string","http://www.w3.org/2001/XMLSchema");
		$n4 = new SoapVar("MaxAddressesReturned",XSD_STRING,"string","http://www.w3.org/2001/XMLSchema");
		$v4 = new SoapVar($maxAddrReturned,XSD_STRING,"string","http://www.w3.org/2001/XMLSchema");
		$item = array(
		new SoapVar(array('Name'=>$n1, 'Value'=>$v1),SOAP_ENC_OBJECT,'NameValuePair',"http://intechsolutions.com.au/soap2/" ),
		new SoapVar(array('Name'=>$n2, 'Value'=>$v2),SOAP_ENC_OBJECT,'NameValuePair',"http://intechsolutions.com.au/soap2/" ),
		new SoapVar(array('Name'=>$n3, 'Value'=>$v3),SOAP_ENC_OBJECT,'NameValuePair',"http://intechsolutions.com.au/soap2/" ),
		new SoapVar(array('Name'=>$n4, 'Value'=>$v4),SOAP_ENC_OBJECT,'NameValuePair',"http://intechsolutions.com.au/soap2/" ),
		);
		$prop = new SoapVar($item,SOAP_ENC_OBJECT,'NameValueArray',"http://intechsolutions.com.au/soap2/" );
		$para = array(  'Database' => 'PAF',
		        //'LocalityLine' => trim($txtLocality).' '.trim($txtState).' '.trim($txtPostCode),
		        'AddressLine' => $searchaddress,
		        'Options' => '1',
		        'GenerateAddress' => 'IfNeeded',
		        'DrillDown' => '',
		        'StartFrom' => '0',
		        'Properties' => $prop,
		);

		$result=$client->ValidateAddressWithOptions($para);
		#set validation result
		if(!empty($result->Address))
		{
		    $error = $this->getFlagString($result->Address);
		    $flag = $result->AddressQualityFlag;
		}
		$addresses = '<select name="addresses" id="addresses" multiple class="form-control" ondblclick="selectAddress()">';
		$addressVals = array();
		$isAddressSugested = false;
		if(!empty($result->Addresses->item)){
			$isAddressSugested = true;
		    if(is_array($result->Addresses->item)){
		        foreach ($result->Addresses->item as $value){
		        	$addressVals[$value] = $this->getAddressText($value);
		            $addresses .= '<option value="' . $value . '"'.'>' . $this->getAddressText($value) . '</option>' . "\n";
		          }
		    }
		    else{
		    	$addressVals[$result->Addresses->item] = $this->getAddressText($result->Addresses->item);
		        $addresses .= '<option value="' . $result->Addresses->item . '"'.'>' . $this->getAddressText($result->Addresses->item) . '</option>' . "\n";
		       }
		}
		if($isAddressSugested)
			$addresses .= '</select>';
		else
			$addresses = "";
		$correctAddress = false;
		if($error == "correct" || $flag == 0){
			$addressError['success'] =  true;
			$addressError['msg'] =  "correct";
			return $addressError;
			$correctAddress = true;
			return Redirect::to('/sales/store')->with('correctAddress',$correctAddress)->withInput();
		}else{
			$addressError['success'] =  false;
			$addressError['msg'] =  $error;
			$addressError['addresses'] =  $addresses;
			return $addressError;
			//Input::set('addresses',$addressVals);
			$success = false;
			return Redirect::to('/sales/store')->with('success',$success)->with('correctAddress',$correctAddress)->with('addresses',$addresses)->withInput()->with('addressError',$error);
		}
	}

	function displayAddress($value)
	{
	    global $txtAddr1;
	    global $txtAddr2;
	    global $txtAddr3;
	    global $txtLocality;
	    global $txtState;
	    global $txtPostCode;
	    global $delimiter;

	    $addrarray = explode("|",$value);
	    if($addrarray[35]=="")
	    {
	        $txtAddr1 = $addrarray[39];
	        $txtAddr2 = $addrarray[40];
	        $txtAddr3 = $addrarray[36];
	    }
	    else
	    {
	        $txtAddr1 = $addrarray[35];
	        $txtAddr2 = $addrarray[39];
	        $txtAddr3 = $addrarray[40];
	    }
	    $txtLocality = $addrarray[2];
	    $txtState = $addrarray[0];
	    $txtPostCode = $addrarray[1];
	}

	function getFlagString($value)
	{
	    global $delimiter;
	    $addrarray = explode("|",$value);
	    return $addrarray[38];
	}

	function getAddressText($value)
	{
	    global $delimiter;
	    $addrarray = explode("|",$value);
	    $addr = $addrarray[39]." ".$addrarray[40]."\n".$addrarray[2]." ".$addrarray[0]
	        ." ".$addrarray[1];
	    return $addr;
	}

	public static function validate($input) {
		///
	    $rules = array(
	    		'title' => 'Required',
		        'first_name' => 'Required|Min:1|Max:80|regex:/[a-zA-Z]/',
		        'last_name' => 'Min:1|Max:80|regex:/[a-zA-Z]/',
		        'mobile' => 'Required|Numeric|regex:/[0-9]{8,12}/',
		        'other_phone' => 'Numeric|regex:/[0-9]{8,12}/',
		        //'email'     => 'Between:3,64|Email|Unique:connection_confirmations',
		        'email'     => 'Between:3,64|Email',
		        'dob' => 'date_format:d/m/Y',
		        'first_nameAdditional' => 'regex:/[a-zA-Z]/',
		        'last_nameAdditional' => 'regex:/[a-zA-Z]/',
		        'dobAdditional' => 'date_format:d/m/Y',
		        'mobileAdditional' => 'Numeric|regex:/[0-9]{8,12}/',
		        //'id_type' => 'Required',
		       // 'id_number' => 'Required|',
		        //'id_location' => 'Required',
		        //'unit_number' => 'Integer|regex:/[0-9]/',
		        'street_number' => 'Required',
		        'street_name' => 'Required',
		        'street_type' => 'Required',
		        'postcode' => 'Required|regex:/[0-9]/',
		        'state' => 'Required|Alpha',
		        'suburb' => 'Required',
		        'closing_date' => 'Required|date_format:d/m/Y'
		);
	    $validator = Validator::make($input, $rules);
	    return $validator;
	}

	function requestGetApi($apiRequestURLforAccounts){
		$ch=curl_init();
		curl_setopt($ch,CURLOPT_HEADER,0);
		curl_setopt($ch,CURLOPT_VERBOSE,0);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch,CURLOPT_URL,$apiRequestURLforAccounts);
		curl_setopt($ch,CURLOPT_POST,FALSE);
		$response = curl_exec($ch);
		$err = curl_error($ch);
		return $response;
	}

	public function insertToZoho(){
		$input = Input::all();
		$mycurl = new Mycurl();
		$myxml = new Myxml();
		//$auth_token = "a34b73b9d71f6ac2efeb7b5082550a47";
		$auth_token = "94fed615de4374d70928b580f434c793";
		$connectionResponse = $this->insertConnectionToZoho();
		return $connectionResponse;
		//$accountResponse = $this->insertAccountToZoho($auth_token);
		//$contactResponse = $this->insertContactToZoho($auth_token);
	}


	public function insertConnectionToZoho(){
		$input = Input::all();
		$mycurl = new Mycurl();
		$myxml = new Myxml();
		$error = array();
		$auth_token = "94fed615de4374d70928b580f434c793";
		//$accountResponse = $this->insertAccountToZoho($auth_token);
		//$contactResponse = $this->insertContactToZoho($auth_token);
		
		//--------------------------------------
		$ownername = "Jayesh";
		$first_name = $input['first_name'];
		$titleAdditional = $input['titleAdditional'];
		$first_nameAdditional = $input['first_nameAdditional'];
		$last_nameAdditional = $input['last_nameAdditional'];
		$dobAdditional = $input['dobAdditional'];
		$mobileAdditional = $input['mobileAdditional'];

		$closing_date_input = $input['closing_date'];
		$date_array = explode('/',$closing_date_input);
		$closing_date_input = $date_array[1].'/'.$date_array[0].'/'.$date_array[2]; 

		$closing_date = date("Y-m-d",strtotime($closing_date_input));
		//$account_id = $accountResponse['accountId'];
		$account_id = "1273885000000437005";
		$created_time = date("Y-m-d H:i:s");
		$contact_id = "1273885000000619007";
		//$contact_id = $contactResponse['contactId'];
		$suburb = $input['suburb'];
		$postcode = $input['postcode'];
		$state = $input['state'];
		$unit_number = "";
		$isUnit = "";
		if(isset($input['unit_number']) && $input['unit_number']){
			$unit_number = $input['unit_number'];
			$isUnit = "Unit";
		}
		
		$street_type = $input['street_type'];
		$street_number = $input['street_number'];
		$street_name = $input['street_name'];
		$mailingStreet = $street_number." ".$street_name;
		//--------------------------------------
		$last_name = $input['last_name'];
		$title = $input['title'];

		$mobile = $input['mobile'];
		$other_phone = $input['other_phone'];
		$email = $input['email'];
		$id_type = $input['id_type'];
		$id_number = $input['id_number'];
		$id_location = $input['id_location'];
		$how_did_u_hear = isset($input['how_did_u_hear']) ? $input['how_did_u_hear'] : "";

		$below60 = "true";
		$dob = "";
		if(isset($input['dob']) && $input['dob']){
			$dob_input = $input['dob'];
			$dob_array = explode('/',$dob_input);
			$dob_input = $dob_array[1].'/'.$dob_array[0].'/'.$dob_array[2];
			$dob = date("Y-m-d",strtotime($dob_input));
			$datetime1 = new DateTime(date("Y-m-d"));
			$datetime2 = new DateTime($dob);
			$interval = $datetime2->diff($datetime1);
			$years = (int)$interval->format('%R%y');
			if($years < 60){
				$below60 = "true";
			}else{
				$below60 = "false";
			}
		}

		//--------------------------------------
		$xmlData   = "<Potentials><row no='2'>";
		$xmlData   .= "<FL val='First Name'>$first_name</FL>";
		$xmlData   .= "<FL val='Last Name'>$last_name</FL>";
		$xmlData   .= "<FL val='Title'>$title</FL>";
		$xmlData   .= "<FL val='DOB'>$dob</FL>";
		$xmlData   .= "<FL val='Mobile'>$mobile</FL>";

		$xmlData   .= "<FL val='Title First Additional'>$titleAdditional</FL>";
		$xmlData   .= "<FL val='First Name First Additional'>$first_nameAdditional</FL>";
		$xmlData   .= "<FL val='Last Name First Additional'>$last_nameAdditional</FL>";
		$xmlData   .= "<FL val='DOB First Additional'>$dobAdditional</FL>";
		$xmlData   .= "<FL val='Mobile First Additional'>$mobileAdditional</FL>";

		$xmlData   .= "<FL val='Other Phone'>$other_phone</FL>";
		$xmlData   .= "<FL val='Email'>$email</FL>";
		$xmlData   .= "<FL val='Below60'>$below60</FL>";
		$xmlData   .= "<FL val='ID Type'>$id_type</FL>";
		$xmlData   .= "<FL val='ID Number'>$id_number</FL>";
		$xmlData   .= "<FL val='ID Location'>$id_location</FL>";
		$xmlData   .= "<FL val='How did you hear about us'>$how_did_u_hear</FL>";
	    $xmlData   .= "<FL val='SMOWNERID'>1273885000000071001</FL>";
	    $xmlData   .= "<FL val='Potential Owner'>$ownername</FL>";
	    $xmlData   .= "<FL val='Potential Name'>$street_name</FL>";
	    $xmlData   .= "<FL val='Closing Date'>$closing_date</FL>";
	    $xmlData   .= "<FL val='ACCOUNTID'>$account_id</FL>";
	    $xmlData   .= "<FL val='Account Name'>Systango</FL>";
	    $xmlData   .= "<FL val='Company Name'>Systango</FL>";
	    $xmlData   .= "<FL val='Stage'>New</FL>";
	    $xmlData   .= "<FL val='SMCREATORID'>1273885000000071001</FL>";
	    $xmlData   .= "<FL val='Created By'>$ownername</FL>";
	    $xmlData   .= "<FL val='MODIFIEDBY'>1273885000000071001</FL>";
	    $xmlData   .= "<FL val='Modified By'>$ownername</FL>";
	    $xmlData   .= "<FL val='Created Time'>$created_time</FL>";
	    $xmlData   .= "<FL val='Modified Time'>$created_time</FL>";
	    $xmlData   .= "<FL val='CONTACTID'>$contact_id</FL>";
	    $xmlData   .= "<FL val='Contact Name'>NuancedIT</FL>";
	    $xmlData   .= "<FL val='Last Activity Time'>$created_time</FL>";
	    $xmlData   .= "<FL val='Sales Cycle Duration'></FL>";
	    $xmlData   .= "<FL val='Overall Sales Duration'></FL>";
	    $xmlData   .= "<FL val='Suburb'>$suburb</FL>";
	    $xmlData   .= "<FL val='Post Code'>$postcode</FL>";
	    $xmlData   .= "<FL val='State'>$state</FL>";
	    $xmlData   .= "<FL val='Reference_Number'></FL>";
	    $xmlData   .= "<FL val='IsUnit'>$isUnit</FL>";
	    $xmlData   .= "<FL val='Unit Number'>$unit_number</FL>";
	    $xmlData   .= "<FL val='Street Type'>$street_type</FL>";
	    $xmlData   .= "<FL val='Complaint'>false</FL>";
	    $xmlData   .= "<FL val='Street Number'>$street_number</FL>";
	    $xmlData   .= "<FL val='AGL Confirmation'>false</FL>";
	    $xmlData   .= "<FL val='Meter Number Issue'>false</FL>";
	    $xmlData   .= "</row></Potentials>";
	    $apiRequest = "https://crm.zoho.com/crm/private/xml/Potentials/insertRecords?authtoken=94fed615de4374d70928b580f434c793&scope=crmapi&newFormat=1&wfTrigger=true";
		$crmResponseInvoice=$mycurl->post($apiRequest,array('xmlData'=>$xmlData));
		$crmResponseInvoiceArray =$myxml->parseString($crmResponseInvoice, false);
		
			if(array_key_exists("error",$crmResponseInvoiceArray['response'][0])){
				$error['code'] = $crmResponseInvoiceArray['response'][0]["error"]['code'][0];
				$error['message'] = $crmResponseInvoiceArray['response'][0]["error"]['message'][0];
			}
			if(array_key_exists("result",$crmResponseInvoiceArray['response'][0])){
				$error['code'] = "0000";
				$error['message'] = $crmResponseInvoiceArray['response'][0]['result']['message'][0];
				$referenceNumber = "";
				$connectionId = $crmResponseInvoiceArray['response'][0]['result']['recorddetail']['FL'][0][0];
				//Fine Reference number

				$apiRequest = "https://crm.zoho.com/crm/private/xml/Potentials/getRecordById?authtoken=$auth_token&scope=crmapi&id=".$connectionId;
			    $crmResponseInvoice=$mycurl->post($apiRequest,array('xmlData'=>$xmlData));
				$crmResponseInvoiceArray =$myxml->parseString($crmResponseInvoice, false);
			    $FLarray = $crmResponseInvoiceArray['response'][0]['result']['Potentials']['row'][0]['FL'] ;
			    foreach ($FLarray as $key => $value) {
			        if($value['val']=='Reference_Number'){
			            $referenceNumber = $value[0];
			            break;
			        }
			    }
			    $error['ref_number'] = $referenceNumber;
			    $error['customer_number'] = $connectionId;
			}
			return $error;
	}

	public function insertContactToZoho($auth_token){
		$input = Input::all();
		$mycurl = new Mycurl();
		$myxml = new Myxml();
		$error = array();

		$first_name = $input['first_name']."-Testing";
		$last_name = $input['last_name'];
		$email = $input['email'];
		$other_phone = $input['other_phone'];
		$mobile = $input['mobile'];
		$street_number = $input['street_number'];
		$street_name = $input['street_name'];
		$mailingStreet = $street_number." ".$street_name;
		$street_type = $input['street_type'];
		$state = $input['state'];
		$suburb = $input['suburb'];
		$postcode = $input['postcode'];
		$id_location = $input['id_location'];

		$xmlData =  "<Contacts><row no='1'>";
		$xmlData .=	"<FL val='First Name'>$first_name</FL>";
		$xmlData .=	"<FL val='Last Name'>$last_name</FL>";
		$xmlData .=	"<FL val='Email'>$email</FL>";
		$xmlData .=	"<FL val='Department'></FL>";
		$xmlData .=	"<FL val='Phone'>$other_phone</FL>";
		$xmlData .=	"<FL val='Fax'></FL>";
		$xmlData .=	"<FL val='Mobile'>$mobile</FL>";
		$xmlData .=	"<FL val='Assistant'></FL>";
		$xmlData .=	"<FL val='Mailing Street'>$mailingStreet</FL>";
		$xmlData .=	"<FL val='Street Type'>$street_type</FL>";
		$xmlData .=	"<FL val='Mailing State'>$state</FL>";
		$xmlData .=	"<FL val='Suburb'>$suburb</FL>";
		$xmlData .=	"<FL val='Mailing Zip'>$postcode</FL>";
		$xmlData .=	"<FL val='Mailing Country'>Australia</FL>";
		//-------------------------------------------------

		$xmlData .=	"</row></Contacts>";
		$apiRequest = "https://crm.zoho.com/crm/private/xml/Contacts/insertRecords?authtoken=94fed615de4374d70928b580f434c793&scope=crmapi&newFormat=1&wfTrigger=true";
		$crmResponseInvoice=$mycurl->post($apiRequest,array('xmlData'=>$xmlData));
		$crmResponseInvoiceArray =$myxml->parseString($crmResponseInvoice, false);
			if(array_key_exists("error",$crmResponseInvoiceArray['response'][0])){
				$error['code'] = $crmResponseInvoiceArray['response'][0]["error"]['code'][0];
				$error['message'] = $crmResponseInvoiceArray['response'][0]["error"]['message'][0];
			}
			if(array_key_exists("result",$crmResponseInvoiceArray['response'][0])){
				$error['code'] = "0000";
			if(isset($crmResponseInvoiceArray['response'][0]['result']['message'][0]))
				$error['contactId'] = $crmResponseInvoiceArray['response'][0]['result']['message'][0];
			else
				$error['accountId'] = "";
			$error['contactId'] = $crmResponseInvoiceArray['response'][0]['result']['recorddetail']['FL'][0][0];
			foreach ($crmResponseInvoiceArray['response'][0]['result']['recorddetail']['FL'] as $key => $value) {
				if($value['val']=='Id')
					$error['contactId'] = $value[0];
			}
			}
			return $error;
	}

	public function insertAccountToZoho($auth_token){
		$input = Input::all();
		$mycurl = new Mycurl();
		$myxml = new Myxml();
		$error = array();
		$first_name = $input['first_name']."-Testing";
		$last_name = $input['last_name'];
		$xmlData = "<Accounts><row no='1'>";
		$xmlData .=	"<FL val='Account Name'>$first_name $last_name</FL>";
		$xmlData .=	"<FL val='Website'></FL>";
		$xmlData .=	"<FL val='Employees'></FL>";
		$xmlData .=	"<FL val='Ownership'></FL>";
		$xmlData .=	"<FL val='Industry'></FL>";
		$xmlData .=	"<FL val='Fax'></FL>";
		$xmlData .=	"<FL val='Annual Revenue'></FL>";
		$xmlData .=	"</row></Accounts>";
		$apiRequest = "https://crm.zoho.com/crm/private/xml/Accounts/insertRecords?authtoken=94fed615de4374d70928b580f434c793&scope=crmapi&newFormat=1&wfTrigger=true";
		$crmResponseInvoice=$mycurl->post($apiRequest,array('xmlData'=>$xmlData));
		$crmResponseInvoiceArray =$myxml->parseString($crmResponseInvoice, false);
		if(array_key_exists("error",$crmResponseInvoiceArray['response'][0])){
			$error['code'] = $crmResponseInvoiceArray['response'][0]['code'][0];
			$error['message'] = $crmResponseInvoiceArray['response'][0]['message'][0];
		}
		if(array_key_exists("result",$crmResponseInvoiceArray['response'][0])){
			$error['code'] = "0000";
			if(isset($crmResponseInvoiceArray['response'][0]['result']['message'][0]))
				$error['accountId'] = $crmResponseInvoiceArray['response'][0]['result']['message'][0];
			else
				$error['accountId'] = "";
			$error['accountId'] = $crmResponseInvoiceArray['response'][0]['result']['recorddetail']['FL'][0][0];
			foreach ($crmResponseInvoiceArray['response'][0]['result']['recorddetail']['FL'] as $key => $value) {
				if($value['val']=='Id')
					$error['accountId'] = $value[0];
			}
		}
		return $error;
	}

}

class NameValuePair
{
    function NameValuePair($n, $v)
    {
        $this->Name = $n;
        $this->Value = $v;
    }
}

class Mycurl {
	
	var $callback = false;	
	function __construct(){
		//parent::Model();
	}	
    
	function setCallback($func_name) {
	    $this->callback = $func_name;
	}
	
	function doRequest($method, $url, $vars) {
		$ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_HEADER, 0); // 0 for headerdata fdalse, 1 for header data true.
	    curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
	    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
	    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
	    if ($method == 'POST') {
	        curl_setopt($ch, CURLOPT_POST, 1);
	        curl_setopt($ch, CURLOPT_POSTFIELDS, $vars);
	    }
	    $data = curl_exec($ch);
	  	curl_close($ch);
	    if ($data) {
	        if ($this->callback)
	        {
	            $callback = $this->callback;
	            $this->callback = false;
	            return call_user_func($callback, $data);
	        } else {
	            return $data;
	        }
	    } else {
	        return curl_error($ch);
	    }
	}
	
	function get($url) {
	    return $this->doRequest('GET', $url, 'NULL');
	}
	
	function post($url, $vars) {
	    return $this->doRequest('POST', $url, $vars);
	}
}

class Myxml {
	function __construct(){
		//parent::Model();
	}
    /**
     * Optimization Enabled / Disabled
     *
     * @var bool
     */
    protected $bOptimize = false;
    /**
     * Method for loading XML Data from String
     *
     * @param string $sXml
     * @param bool $bOptimize
     */
    public function parseString( $sXml , $bOptimize = false) {
        $oXml = new XMLReader();
        $this -> bOptimize = (bool) $bOptimize;
        try {
            // Set String Containing XML data
            $oXml->XML($sXml);
            // Parse Xml and return result
            return $this->parseXml($oXml);
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
    /**
     * Method for loading Xml Data from file
     *
     * @param string $sXmlFilePath
     * @param bool $bOptimize
     */
    public function parseFile( $sXmlFilePath , $bOptimize = false ) {
        $oXml = new XMLReader();
        $this -> bOptimize = (bool) $bOptimize;
        try {
            // Open XML file
            $oXml->open($sXmlFilePath);
            // // Parse Xml and return result
            return $this->parseXml($oXml);
        } catch (Exception $e) {
            echo $e->getMessage(). ' | Try open file: '.$sXmlFilePath;
        }
    }
    /**
     * XML Parser
     *
     * @param XMLReader $oXml
     * @return array
     */
    protected function parseXml( XMLReader $oXml ) {
        $aAssocXML = null;
        $iDc = -1;
        while($oXml->read()){
            switch ($oXml->nodeType) {
                case XMLReader::END_ELEMENT:
                    if ($this->bOptimize) {
                        $this->optXml($aAssocXML);
                    }
                    return $aAssocXML;
                case XMLReader::ELEMENT:
                    if(!isset($aAssocXML[$oXml->name])) {
                        if($oXml->hasAttributes) {
                            $aAssocXML[$oXml->name][] = $oXml->isEmptyElement ? '' : $this->parseXML($oXml);
                        } else {
                            if($oXml->isEmptyElement) {
                                $aAssocXML[$oXml->name] = '';
                            } else {
                                $aAssocXML[$oXml->name] = $this->parseXML($oXml);
                            }
                        }
                    } elseif (is_array($aAssocXML[$oXml->name])) {
                        if (!isset($aAssocXML[$oXml->name][0]))
                        {
                            $temp = $aAssocXML[$oXml->name];
                            foreach ($temp as $sKey=>$sValue)
                            unset($aAssocXML[$oXml->name][$sKey]);
                            $aAssocXML[$oXml->name][] = $temp;
                        }
                        if($oXml->hasAttributes) {
                            $aAssocXML[$oXml->name][] = $oXml->isEmptyElement ? '' : $this->parseXML($oXml);
                        } else {
                            if($oXml->isEmptyElement) {
                                $aAssocXML[$oXml->name][] = '';
                            } else {
                                $aAssocXML[$oXml->name][] = $this->parseXML($oXml);
                            }
                        }
                    } else {
                        $mOldVar = $aAssocXML[$oXml->name];
                        $aAssocXML[$oXml->name] = array($mOldVar);
                        if($oXml->hasAttributes) {
                            $aAssocXML[$oXml->name][] = $oXml->isEmptyElement ? '' : $this->parseXML($oXml);
                        } else {
                            if($oXml->isEmptyElement) {
                                $aAssocXML[$oXml->name][] = '';
                            } else {
                                $aAssocXML[$oXml->name][] = $this->parseXML($oXml);
                            }
                        }
                    }
                    if($oXml->hasAttributes) {
                        $mElement =& $aAssocXML[$oXml->name][count($aAssocXML[$oXml->name]) - 1];
                        while($oXml->moveToNextAttribute()) {
                            $mElement[$oXml->name] = $oXml->value;
                        }
                    }
                    break;
                case XMLReader::TEXT:
                case XMLReader::CDATA:
                    $aAssocXML[++$iDc] = $oXml->value;
            }
        }
        return $aAssocXML;
    }
    /**
     * Method to optimize assoc tree.
     * ( Deleting 0 index when element
     *  have one attribute / value )
     *
     * @param array $mData
     */
    public function optXml(&$mData) {
        if (is_array($mData)) {
            if (isset($mData[0]) && count($mData) == 1 ) {
                $mData = $mData[0];
                if (is_array($mData)) {
                    foreach ($mData as &$aSub) {
                        $this->optXml($aSub);
                    }
                }
            } else {
                foreach ($mData as &$aSub) {
                    $this->optXml($aSub);
                }
            }
        }
    }
}


?>
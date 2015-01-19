<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="Stylesheet" type="text/css" href="css/style.css" />
<title>IQ Office</title>
<script type="text/javascript">

 function selectAddress()
 {
     document.forms["form1"].submit();
 }
</script>
</head>
<body>

<h1>PHP Soap Demo</h1>
<?php

# WSDL address and Credentials
$wsdl="http://demo1.intechiq.com/iqoffice2.wsdl";
$username = "MyConnect01";
$password = "xx2mj6g2";

$maxAddrReturned = "100";
$delimiter = "|";

# init SoapClient
$client=new SoapClient($wsdl);

# vars for textboxes
$txtAddr1 = "";
$txtAddr2 = "";
$txtAddr3 = "";
$txtLocality = "";
$txtState = "";
$txtPostCode = "";

$error = "";
$flag = 0;

#---------------------------------------------------
if(isset($_POST['btnClear']))
{
    $txtAddr1 = "";
    $txtAddr2 = "";
    $txtAddr3 = "";
    $txtLocality = "";
    $txtState = "";
    $txtPostCode = "";
}

#---------------------------------------------------
if(isset($_POST['btnSubmit']))
{
    $txtAddr1 = $_POST['txtAddr1'];
    $txtAddr2 = $_POST['txtAddr2'];
    $txtAddr3 = $_POST['txtAddr3'];
    $txtLocality = $_POST['txtLocality'];
    $txtState = $_POST['txtState'];
    $txtPostCode = $_POST['txtPostCode'];

class NameValuePair
{
    function NameValuePair($n, $v)
    {
        $this->Name = $n;
        $this->Value = $v;
    }
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
        'LocalityLine' => trim($txtLocality).' '.trim($txtState).' '.trim($txtPostCode),
        'AddressLine' => trim($txtAddr1).' '.trim($txtAddr2).' '.trim($txtAddr3),
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
    $error = getFlagString($result->Address);
    $flag = $result->AddressQualityFlag;
}

# display result
//print_r($result);

}

#---------------------------------------------------
function displayAddress($value)
{
    global $txtAddr1;
    global $txtAddr2;
    global $txtAddr3;
    global $txtLocality;
    global $txtState;
    global $txtPostCode;
    global $delimiter;

    $addrarray = explode($delimiter,$value);
    //print(count($addrarray));
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

#---------------------------------------------------
function getFlagString($value)
{
    global $delimiter;
    $addrarray = explode($delimiter,$value);
    return $addrarray[38];
}

#---------------------------------------------------
if(isset($_POST['addresses']))
{

class NameValuePair
{
    function NameValuePair($n, $v)
    {
        $this->Name = $n;
        $this->Value = $v;
    }
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
        'InputFields' => $_POST['addresses'],
        'StartFrom' => '0',
        'Properties' => $prop,
);

$result=$client->GetAddresses($para);

#set input
if(!empty($result) && count($result->Addresses->item)>0)
{
    if(is_array($result->Addresses->item))
    {
        displayAddress($result->Addresses->item[0]);
    }
    else
    {
        displayAddress($result->Addresses->item);
    }
}

# display result
//print_r($result);
}

#---------------------------------------------------
function getAddressText($value)
{
    global $delimiter;
    $addrarray = explode($delimiter,$value);
    $addr = $addrarray[39]." ".$addrarray[40]."\n".$addrarray[2]." ".$addrarray[0]
        ." ".$addrarray[1];
    return $addr;
}

?>
<hr />
<?php
    if(isset($isFormValidate) && !$isFormValidate){
      echo "Something went wrong.";
    }
  ?>
{{ Form::open(array('url' => 'address/test')) }}
<table border="0" width="525px" style="margin-right: 0px">
    <tr>
        <td width="180px">Address:</td>
        <td colspan="4" width="345px">
            <INPUT TYPE = "Text" VALUE="<?php print $txtAddr1; ?>" NAME = "txtAddr1" size="58">
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="4" width="345px">
            <INPUT TYPE = "Text" VALUE ="<?php print $txtAddr2; ?>" NAME = "txtAddr2" size="58">
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="3" width="275px">
            <INPUT TYPE = "Text" VALUE ="<?php print $txtAddr3; ?>" NAME = "txtAddr3" size="45">
        </td>
        <td width="70px">
            <INPUT TYPE = "Submit" Name = "btnSubmit" VALUE = "Validate" style="width:70px">
        </td>
    </tr>
    <tr>
        <td>Suburb/State/Pcode:</td>
        <td Width="120px">
            <INPUT TYPE = "Text" VALUE ="<?php print $txtLocality; ?>" NAME = "txtLocality" size="16">
        </td>
        <td Width = "90px">
            <INPUT TYPE = "Text" VALUE ="<?php print $txtState; ?>" NAME = "txtState" size="11">
        </td>
        <td Width = "65px">
            <INPUT TYPE = "Text" VALUE ="<?php print $txtPostCode; ?>" NAME = "txtPostCode" size="4">
        </td>
        <td width="70px">
            <INPUT TYPE = "Submit" Name = "btnClear" VALUE = "Clear" style="width:70px">
        </td>
    </tr>
    <tr>
        <td width="180px"></td>
        <td width="275px"  colspan="3">
            <?php if($error <> ""):
                if($flag == 0)
                {
                    echo '<font color=green>';
                    print($error);
                    echo '</font>';
                }
                else if ($flag == 1)
                {
                    echo '<font color=orange>';
                    print($error);
                    echo '</font>';
                }
                else //$flag == 2
                {
                    echo '<font color=red>';
                    print($error);
                    echo '</font>';
                }
            ?>
            <?php endif;?>
        </td>
        <td align="left" width="70px">
        </td>
    </tr>
    <tr>
        <td></td>
        <td colspan="4">
            <select name="addresses" id="addresses" size="10" style="width:345px" ondblclick="selectAddress()">
<?php
if(!empty($result->Addresses->item))
{
    if(is_array($result->Addresses->item))
    {
        foreach ($result->Addresses->item as $value)
        {
            echo '<option value="' . $value . '"'.'>' . getAddressText($value) . '</option>' . "\n";
        }
    }
    else
    {
        echo '<option value="' . $result->Addresses->item . '"'.'>' . getAddressText($result->Addresses->item) . '</option>' . "\n";
    }
}
?>
            </select>
        </td>
    </tr>
</table>
{{ Form::close() }}
<!-- </Form> -->
</body>
<?
session_start();
if(!isset($_SESSION['loggedin']))
{
  header('Location: http://www.myconnect.com.au/processing/av_login.php');
}
?>
  {{HTML::script(asset('js/jquery-1.8.3.js'))}}
  {{HTML::script(asset('js/jquery-ui.js'))}}
  {{HTML::style(asset('css/bootstrap.css'))}}
  {{HTML::style(asset('css/template.css'))}}
  {{HTML::style(asset('css/jquery-ui.css'))}}
  <style>
    input[type='number'] {
        -moz-appearance:textfield;
      }
  </style>
  <script>

  var typingTimer;                //timer identifier
  var doneTypingInterval = 200;  //time in ms, 5 second for example

  //on keyup, start the countdown
  $( document ).ready(function() {
    $('#searchaddress').keyup(function(){
        clearTimeout(typingTimer);
        typingTimer = setTimeout(call_live_search, doneTypingInterval);
    });

    //on keydown, clear the countdown 
    $('#searchaddress').keydown(function(){
        clearTimeout(typingTimer);
    });
  });

  //user is "finished typing," do something
  function call_live_search() {
      if($.trim($("#searchaddress").val().length) > 5)
        live_search();
      //do something
  }

  function live_search(){
    var token =  $("input[name=_token]").val();
    var searchaddress =  $("#searchaddress").val();
    var dataString = 'token='+token+"&searchaddress="+searchaddress;
    var l = window.location;
    var base_url = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1];
    var request_url = base_url+"/public/sales/addressValidation";
    $("#loader_area").removeClass("hide");
    $.ajax({
          type: "POST",
          url : request_url,
          data : dataString,
          dataType: "json",
          async: true,
          success : function(data){
            var addresses = data.addresses;
            if(addresses == ""){
              $("#suggestAddresses").html("No Addresses Found");
              $("#suggestAddresses").removeClass("hide");
            }else{
              $("#suggestAddresses").html(addresses);
              $("#suggestAddresses").removeClass("hide");
            }
          } 
      });
    $("#loader_area").addClass("hide");
  }

  function validate_address(){
      var token =  $("input[name=_token]").val();
      var unit_number =  $("#unit_number").val();
      var street_number =  $("#street_number").val();
      var street_name =  $("#street_name").val();
      var street_type =  $("#street_type").val();
      var state =  $("#state").val();
      var suburb =  $("#suburb").val();
      var postcode =  $("#postcode").val();
      var dataString = 'token='+token+"&unit_number="+unit_number+"&street_number="+street_number+"&street_name="+street_name+"&street_type="+street_type+"&state="+state+"&suburb="+suburb+"&postcode="+postcode;
      var l = window.location;
      var base_url = l.protocol + "//" + l.host + "/" + l.pathname.split('/')[1];
      var request_url = base_url+"/public/sales/addressValidation";
      var submit = false;
      $.ajax({
          type: "POST",
          url : request_url,
          data : dataString,
          dataType: "json",
          async: false,
          success : function(data){
              var message = String(data.msg);
              var addresses = data.addresses;
              if(message != "correct"){
                $("#success_msg").addClass("hide");
                if(message == "amended Street Type"){
                  message = "Wrong street type";
                }
                if(message == "cannot match locality info"){
                  message = "Address not found";
                }
                if(message == "No house number in that street"){
                  message = "Wrong house number";
                }
                $("#error_msg").html(capitaliseFirstLetter(message));
                $("#error_msg").removeClass("hide");
                $("#suggestAddresses").html(addresses);
                $("#suggestAddresses").removeClass("hide");
                $("#error_msg").attr("tabindex",-1).focus();

              }else{
                $("#error_msg").addClass("hide");
                //$("#success_msg").removeClass("hide");
                //$("#success_msg").attr("tabindex",-1).focus();
                submit = true;
              }
          }
      });
      return submit;
  }

  function toTitleCase(str)
  {
      return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
  }
  function capitaliseFirstLetter(string)
  {
      return string.charAt(0).toUpperCase() + string.slice(1);
  }
  function selectAddress(){
    var getSelected = String($("#addresses").val());
    var addressArray = getSelected.split('|');
    if(addressArray[1]!="")
      $("#postcode").val(addressArray[1]);
    if(addressArray[0]!="")
      $("#state").val(addressArray[0]);
    if(addressArray[2]!="")
      $("#suburb").val(addressArray[2]);
    if(addressArray[13]!="")
      $("#unit_number").val(toTitleCase(addressArray[13]));
    if(addressArray[8]!="")
      $("#street_number").val(toTitleCase(addressArray[8]));
    if(addressArray[10]!="")
      $("#street_number").val($("#street_number").val()+"-"+toTitleCase(addressArray[10]));
    if(addressArray[3]!="")
      $("#street_name").val(toTitleCase(addressArray[3]));
    if(addressArray[4]!="")
      $("#street_type").val(toTitleCase(addressArray[4]));
    if(addressArray[35]=="")
      $("#addressStreet").html(addressArray[39]);
    else
      $("#addressStreet").html(addressArray[35]);
  }
  $(function() {
    var dateToday = new Date();
    var dateToday2 = new Date();
    var year;
    $( ".datepicker" ).datepicker({
      minDate: dateToday2,
      dateFormat:'dd/mm/yy',
      changeYear: true,
      yearRange: "1950:<?php echo date('Y');?>"
    });
    year = dateToday.getFullYear();
    year -= 10;
    dateToday.setFullYear(year);
    $( ".datepickerDOB" ).datepicker({
      maxDate: dateToday,
      dateFormat:'dd/mm/yy',
      changeYear: true,
      yearRange: "1950:<?php echo date('Y');?>"
    });
  });
  </script>
<div class="text-center container">
  <div>
      <a href="/processing/av_login.php?logout=1" style="float: right; font-size: 14px;">Logout</a>
    </div>
  <div id="header">
  @include('includes.head')
  </div>
<?php
  $success = Session::get('success') ? true : false;
  $correctAddress = Session::get('correctAddress') ? true : false;
  $customer_number = Session::get('customer_number') ? Session::get('customer_number') : "";
  $ref_number = Session::get('ref_number') ? Session::get('ref_number') : "";
  $customer_url = "";
  if($customer_number)
    $customer_url = "<a href='https://crm.zoho.com/crm/EntityInfo.do?module=Potentials&id=$customer_number'>$ref_number</a>";
  if($correctAddress && $success){ ?>
  <div class="alert alert-success text-center" role="alert">
    Address validation done & Connection added successfully. Customer Number : <?php echo $customer_url;?>
  </div>
<?php } ?>

<?php
  $displayError = Session::get('addressError');
  if (strpos(trim(strtolower($displayError)),trim(strtolower("cannot match locality info"))) !== false) {
      $displayError = "Address not found";
  }
  if (strpos(trim(strtolower($displayError)),trim(strtolower("amended street type"))) !== false) {
      $displayError = "Wrong street type";
  }
  if (strpos(trim(strtolower($displayError)),trim(strtolower("No house number in that street"))) !== false) {
      $displayError = "Wrong house number";
  }

  if(Session::get('addressError') && Session::get('addressError')!="correct"){
    echo '<div class="alert alert-warning text-center" role="alert">';
    echo ucfirst($displayError);
    echo "</div>";
  }
?>
<div id="error_msg" class="alert alert-warning text-center hide" role="alert">
</div>
<div id="success_msg" class="alert alert-success text-center hide" role="alert">
  Address validation done & Connection added successfully.
</div>



 {{ Form::open(array("onSubmit"=>"return validate_address()", "id"=>"salesForm",'url' => 'sales/store','class' => 'form-horizontal', 'role' => 'form')) }}
 <div id='content' class="col-sm-6 col-sm-offset-3 form-box">
 <title>MyConnect: New Connection Form</title>
  <?php
    $title_msg = "";
    $firstName_msg = "";
    $lastName_msg = "";
    $mobile_msg = "";
    $otherPhone_msg = "";
    $email_msg = "";
    $dob_msg = "";
    $titleAdditional_msg = "" ;
    $firstNameAdditional_msg = "" ;
    $lastNameAdditional_msg = "" ;
    $dobAdditional_msg  = "" ;
    $mobileAdditional_msg = "" ;

    $idType_msg = "";
    $idNumber_msg = "";
    $idLocation_msg = "";
    $unitNumber_msg = "";
    $streetNumber_msg = "";
    $streetName_msg = "";
    $streetType_msg = "";
    $postcode_msg = "";
    $state_msg = "";
    $suburb_msg = "";
    $closingDate_msg = "";
      
    if(Session::get('messages')){
      $messages = Session::get('messages');
      $title_msg = $messages->has('title') ? $messages->first('title') : "" ;
      $firstName_msg = $messages->has('first_name') ? $messages->first('first_name') : "" ;
      $lastName_msg = $messages->has('last_name') ? $messages->first('last_name') : "" ;
      $mobile_msg = $messages->has('mobile') ? $messages->first('mobile') : "" ;
      $otherPhone_msg = $messages->has('other_phone') ? $messages->first('other_phone') : "" ;
      $email_msg = $messages->has('email') ? $messages->first('email') : "" ;
      $dob_msg = $messages->has('dob') ? $messages->first('dob') : "" ;
      $titleAdditional_msg = $messages->has('titleAdditional') ? $messages->first('titleAdditional') : "" ;
      $firstNameAdditional_msg = $messages->has('first_nameAdditional') ? $messages->first('first_nameAdditional') : "" ;
      $lastNameAdditional_msg = $messages->has('last_nameAdditional') ? $messages->first('last_nameAdditional') : "" ;
      $dobAdditional_msg  = $messages->has('dobAdditional') ? $messages->first('dobAdditional') : "" ;
      $mobileAdditional_msg = $messages->has('mobileAdditional') ? $messages->first('mobileAdditional') : "" ;

      $idType_msg = $messages->has('id_type') ? $messages->first('id_type') : "" ;
      $idNumber_msg = $messages->has('id_number') ? $messages->first('id_number') : "" ;
      $idLocation_msg = $messages->has('id_location') ? $messages->first('id_location') : "" ;
      $unitNumber_msg = $messages->has('unit_number') ? $messages->first('unit_number') : "" ;
      $streetNumber_msg = $messages->has('street_number') ? $messages->first('street_number') : "" ;
      $streetName_msg = $messages->has('street_name') ? $messages->first('street_name') : "" ;
      $streetType_msg = $messages->has('street_type') ? $messages->first('street_type') : "" ;
      $postcode_msg = $messages->has('postcode') ? $messages->first('postcode') : "" ;
      $state_msg = $messages->has('state') ? $messages->first('state') : "" ;
      $suburb_msg = $messages->has('suburb') ? $messages->first('suburb') : "" ;
      $closingDate_msg = $messages->has('closing_date') ? $messages->first('closing_date') : "" ;
    }

    $titleList = array(""=>"None","Mr"=>"Mr","Mrs"=>"Mrs","Miss"=>"Miss","Ms"=>"Ms","Dr"=>"Dr");
    $idList = array(""=>"None","Drivers License"=>"Drivers License","Passport"=>"Passport","Medicare"=>"Medicare","Concession"=>"Concession","ABN"=>"ABN");
    $idLocation = array(""=>"None","ACT" => "ACT","NSW" => "NSW","QLD" => "QLD","SA" => "SA","TAS" => "TAS","VIC" => "VIC","WA" => "WA","Australia" => "Australia","Afghanistan" => "Afghanistan","Albania" => "Albania","Algeria" => "Algeria","Amer Virgin Is" => "Amer Virgin Is","Andorra" => "Andorra","Angola" => "Angola","Anguilla" => "Anguilla","Antarctica" => "Antarctica","Antigua Barbuda" => "Antigua Barbuda","Argentina" => "Argentina","Armenia" => "Armenia","Aruba" => "Aruba","Austria" => "Austria","Azerbaijan" => "Azerbaijan","Bahamas" => "Bahamas","Bahrain" => "Bahrain","Bangladesh" => "Bangladesh","Barbados" => "Barbados","Belarus" => "Belarus","Belgium" => "Belgium","Belize" => "Belize","Benin" => "Benin","Bermuda" => "Bermuda","Bhutan" => "Bhutan","Blue" => "Blue","Bolivia" => "Bolivia","Bosnia Herz" => "Bosnia Herz","Botswana" => "Botswana","Bouvet Islands" => "Bouvet Islands","Brazil" => "Brazil","Brit.Ind.Oc.Ter" => "Brit.Ind.Oc.Ter","Brit.Virgin Is." => "Brit.Virgin Is.","Brunei Daruss." => "Brunei Daruss.","Bulgaria" => "Bulgaria","Burkina Faso" => "Burkina Faso","Burma" => "Burma","Burundi" => "Burundi","Cambodia" => "Cambodia","Cameroon" => "Cameroon","Canada" => "Canada","Cape Verde" => "Cape Verde","CAR" => "CAR","Cayman Islands" => "Cayman Islands","Chad" => "Chad","Chile" => "Chile","China" => "China","Christmas Islnd" => "Christmas Islnd","Coconut Islands" => "Coconut Islands","Colombia" => "Colombia","Comoros" => "Comoros","Cook Islands" => "Cook Islands","Costa Rica" => "Costa Rica","Cote d Ivoire" => "Cote d Ivoire","Croatia" => "Croatia","Cuba" => "Cuba","Cyprus" => "Cyprus","Czech Republic" => "Czech Republic","Dem. Rep. Congo" => "Dem. Rep. Congo","Denmark" => "Denmark","Djibouti" => "Djibouti","Dominica" => "Dominica","Dominican Rep." => "Dominican Rep.","Dutch Antilles" => "Dutch Antilles","East Timor" => "East Timor","Ecuador" => "Ecuador","Egypt" => "Egypt","El Salvador" => "El Salvador","Equatorial Guin" => "Equatorial Guin","Eritrea" => "Eritrea","Estonia" => "Estonia","Ethiopia" => "Ethiopia","European Union" => "European Union","Falkland Islnds" => "Falkland Islnds","Faroe Islands" => "Faroe Islands","Fiji" => "Fiji","Finland" => "Finland","France" => "France","Frenc.Polynesia" => "Frenc.Polynesia","French Guayana" => "French Guayana","French S.Territ" => "French S.Territ","Gabon" => "Gabon","Gambia" => "Gambia","Georgia" => "Georgia","Germany" => "Germany","Ghana" => "Ghana","Gibraltar" => "Gibraltar","Greece" => "Greece","Greenland" => "Greenland","Grenada" => "Grenada","Guadeloupe" => "Guadeloupe","Guam" => "Guam","Guatemala" => "Guatemala","Guinea" => "Guinea","Guinea-Bissau" => "Guinea-Bissau","Guyana" => "Guyana","Haiti" => "Haiti","Heard/McDon.Isl" => "Heard/McDon.Isl","Honduras" => "Honduras","Hong Kong" => "Hong Kong","Hungary" => "Hungary","Iceland" => "Iceland","India" => "India","Indonesia" => "Indonesia","Iran" => "Iran","Iraq" => "Iraq","Ireland" => "Ireland","Israel" => "Israel","Italy" => "Italy","Jamaica" => "Jamaica","Japan" => "Japan","Jordan" => "Jordan","Kazakhstan" => "Kazakhstan","Kenya" => "Kenya","Kiribati" => "Kiribati","Kuwait" => "Kuwait","Kyrgyzstan" => "Kyrgyzstan","Laos" => "Laos","Latvia" => "Latvia","Lebanon" => "Lebanon","Lesotho" => "Lesotho","Liberia" => "Liberia","Libya" => "Libya","Liechtenstein" => "Liechtenstein","Lithuania" => "Lithuania","Luxembourg" => "Luxembourg","Macau" => "Macau","Macedonia" => "Macedonia","Madagascar" => "Madagascar","Malawi" => "Malawi","Malaysia" => "Malaysia","Maldives" => "Maldives","Mali" => "Mali","Malta" => "Malta","Marshall Islnds" => "Marshall Islnds","Martinique" => "Martinique","Mauretania" => "Mauretania","Mauritius" => "Mauritius","Mayotte" => "Mayotte","Mexico" => "Mexico","Micronesia" => "Micronesia","Minor Outl.Isl." => "Minor Outl.Isl.","Moldova" => "Moldova","Monaco" => "Monaco","Mongolia" => "Mongolia","Montserrat" => "Montserrat","Morocco" => "Morocco","Mozambique" => "Mozambique","N.Mariana Islnd" => "N.Mariana Islnd","Namibia" => "Namibia","NATO" => "NATO","Nauru" => "Nauru","Nepal" => "Nepal","Netherlands" => "Netherlands","New Caledonia" => "New Caledonia","New Zealand" => "New Zealand","Nicaragua" => "Nicaragua","Niger" => "Niger","Nigeria" => "Nigeria","Niue" => "Niue","Norfolk Islands" => "Norfolk Islands","North Korea" => "North Korea","Norway" => "Norway","Oman" => "Oman","Orange" => "Orange","Pakistan" => "Pakistan","Palau" => "Palau","Palestine" => "Palestine","Panama" => "Panama","Pap. New Guinea" => "Pap. New Guinea","Paraguay" => "Paraguay","Peru" => "Peru","Philippines" => "Philippines","Pitcairn Islnds" => "Pitcairn Islnds","Poland" => "Poland","Portugal" => "Portugal","Puerto Rico" => "Puerto Rico","Qatar" => "Qatar","Rep.of Congo" => "Rep.of Congo","Reunion" => "Reunion","Romania" => "Romania","Russian Fed." => "Russian Fed.","Rwanda" => "Rwanda","S. Sandwich Ins" => "S. Sandwich Ins","S.Tome,Principe" => "S.Tome,Principe","Saint Helena" => "Saint Helena","Samoa" => "Samoa","Samoa, America" => "Samoa, America","San Marino" => "San Marino","Saudi Arabia" => "Saudi Arabia","Senegal" => "Senegal","Serbia/Monten." => "Serbia/Monten.","Seychelles" => "Seychelles","Sierra Leone" => "Sierra Leone","Singapore" => "Singapore","Slovakia" => "Slovakia","Slovenia" => "Slovenia","Solomon Islands" => "Solomon Islands","Somalia" => "Somalia","South Africa" => "South Africa","South Korea" => "South Korea","Spain" => "Spain","Sri Lanka" => "Sri Lanka","St Kitts&amp;Nevis" => "St Kitts&amp;Nevis","St. Lucia" => "St. Lucia","St. Vincent" => "St. Vincent","St.Pier,Miquel." => "St.Pier,Miquel.","Sudan" => "Sudan","Suriname" => "Suriname","Svalbard" => "Svalbard","Swaziland" => "Swaziland","Sweden" => "Sweden","Switzerland" => "Switzerland","Syria" => "Syria","Taiwan" => "Taiwan","Tajikistan" => "Tajikistan","Tanzania" => "Tanzania","Thailand" => "Thailand","Togo" => "Togo","Tokelau Islands" => "Tokelau Islands","Tonga" => "Tonga","Trinidad,Tobago" => "Trinidad,Tobago","Tunisia" => "Tunisia","Turkey" => "Turkey","Turkmenistan" => "Turkmenistan","Turksh Caicosin" => "Turksh Caicosin","Tuvalu" => "Tuvalu","Uganda" => "Uganda","Ukraine" => "Ukraine","United Kingdom" => "United Kingdom","United Nations" => "United Nations","Uruguay" => "Uruguay","USA" => "USA","Utd.Arab Emir." => "Utd.Arab Emir.","Uzbekistan" => "Uzbekistan","Vanuatu" => "Vanuatu","Vatican City" => "Vatican City","Venezuela" => "Venezuela","Vietnam" => "Vietnam","Wallis,Futuna" => "Wallis,Futuna","West Sahara" => "West Sahara","Yemen" => "Yemen","Zambia" => "Zambia","Zimbabwe" => "Zimbabwe");
    $streetTypeList = array(""=>"None","Access" => "Access","Acre" => "Acre","Alley" => "Alley","Alleyway" => "Alleyway","Amble" => "Amble","Anchorage" => "Anchorage","Approach" => "Approach","Arcade" => "Arcade","ARTERIAL" => "ARTERIAL","Artery" => "Artery","Avenue" => "Avenue","banan" => "banan","Bank" => "Bank","Basin" => "Basin","Beach" => "Beach","Bend" => "Bend","Block" => "Block","BOARDWALK" => "BOARDWALK","Boulevard" => "Boulevard","Boulevarde" => "Boulevarde","Brace" => "Brace","Brae" => "Brae","Branch" => "Branch","Break" => "Break","Brett" => "Brett","Bridge" => "Bridge","Broadwalk" => "Broadwalk","Broadway" => "Broadway","Brow" => "Brow","Bypass" => "Bypass","Byway" => "Byway","Causeway" => "Causeway","Centre" => "Centre","Centreway" => "Centreway","Chase" => "Chase","Circle" => "Circle","Circlet" => "Circlet","Circuit" => "Circuit","Circus" => "Circus","Close" => "Close","Cluster" => "Cluster","Colonnade" => "Colonnade","Common" => "Common","Concord" => "Concord","Concourse" => "Concourse","Connection" => "Connection","Copse" => "Copse","Corner" => "Corner","Corso" => "Corso","Court" => "Court","Courtyard" => "Courtyard","Cove" => "Cove","Crescent" => "Crescent","Crest" => "Crest","Crief" => "Crief","Crook" => "Crook","Cross" => "Cross","Crossing" => "Crossing","Crossroad" => "Crossroad","Crossway" => "Crossway","Cruiseway" => "Cruiseway","Cul De Sac" => "Cul De Sac","Cul-De-Sac" => "Cul-De-Sac","Cut" => "Cut","Cutting" => "Cutting","Dale" => "Dale","Dash" => "Dash","Dell" => "Dell","Dene" => "Dene","Deviation" => "Deviation","Dip" => "Dip","Distributor" => "Distributor","Divide" => "Divide","Dock" => "Dock","Domain" => "Domain","Down" => "Down","Downs" => "Downs","Drive" => "Drive","Driveway" => "Driveway","Easement" => "Easement","Edge" => "Edge","Elbow" => "Elbow","End" => "End","Entrance" => "Entrance","Esplanade" => "Esplanade","Estate" => "Estate","Expressway" => "Expressway","Extension" => "Extension","Fairway" => "Fairway","Fire Track" => "Fire Track","Firebreak" => "Firebreak","Fireline" => "Fireline","Firetrail" => "Firetrail","Flat" => "Flat","Flats" => "Flats","Follow" => "Follow","Footway" => "Footway","Ford" => "Ford","Foreshore" => "Foreshore","Fork" => "Fork","Formation" => "Formation","Freeway" => "Freeway","Front" => "Front","Frontage" => "Frontage","Gap" => "Gap","Gardens" => "Gardens","Gates" => "Gates","Gateway" => "Gateway","Glade" => "Glade","Glen" => "Glen","Grange" => "Grange","Green" => "Green","Ground" => "Ground","Grove" => "Grove","Gully" => "Gully","Harbour" => "Harbour","Haven" => "Haven","Heath" => "Heath","Heights" => "Heights","Highroad" => "Highroad","Highway" => "Highway","Hill" => "Hill","Hollow" => "Hollow","Inlet" => "Inlet","Interchange" => "Interchange","Intersection" => "Intersection","Island" => "Island","Juction" => "Juction","Key" => "Key","Keys" => "Keys","Knoll" => "Knoll","Ladder" => "Ladder","Landing" => "Landing","Lane" => "Lane","Laneway" => "Laneway","Lead" => "Lead","Leader" => "Leader","Lees" => "Lees","Line" => "Line","Link" => "Link","Little" => "Little","Lookout" => "Lookout","Loop" => "Loop","Lower" => "Lower","Lynne" => "Lynne","Mall" => "Mall","Manor" => "Manor","Mart" => "Mart","Mead" => "Mead","Meander" => "Meander","Mew" => "Mew","Mews" => "Mews","Mile" => "Mile","Motorway" => "Motorway","Mount" => "Mount","Nook" => "Nook","North" => "North","Null" => "Null","Outlet" => "Outlet","Outlook" => "Outlook","Oval" => "Oval","Palms" => "Palms","Parade" => "Parade","Paradise" => "Paradise","Park" => "Park","Parklands" => "Parklands","Parkway" => "Parkway","Part" => "Part","Pass" => "Pass","Path" => "Path","Pathway" => "Pathway","PENINSULA" => "PENINSULA","Penninsula" => "Penninsula","Piazza" => "Piazza","Place" => "Place","Plateau" => "Plateau","Plaza" => "Plaza","Pocket" => "Pocket","Point" => "Point","Port" => "Port","Precinct" => "Precinct","Promenade" => "Promenade","Quad" => "Quad","Quadrangle" => "Quadrangle","Quadrant" => "Quadrant","Quays" => "Quays","Ramble" => "Ramble","Ramp" => "Ramp","Range" => "Range","Reach" => "Reach","Reef" => "Reef","Reserve" => "Reserve","Rest" => "Rest","Retreat" => "Retreat","Return" => "Return","Reviera" => "Reviera","Ride" => "Ride","Ridge" => "Ridge","Ridgeway" => "Ridgeway","Right of Way" => "Right of Way","Ring" => "Ring","Rise" => "Rise","Rising" => "Rising","River" => "River","Riverway" => "Riverway","Road" => "Road","Roads" => "Roads","Roadside" => "Roadside","Roadway" => "Roadway","Ronde" => "Ronde","Rosebowl" => "Rosebowl","Rotary" => "Rotary","Round" => "Round","Route" => "Route","Row" => "Row","Rowe" => "Rowe","Rue" => "Rue","Run" => "Run","Service Way" => "Service Way","Serviceway" => "Serviceway","Shunt" => "Shunt","Siding" => "Siding","Skyline" => "Skyline","Slope" => "Slope","Sound" => "Sound","South" => "South","Spur" => "Spur","Square" => "Square","Stairs" => "Stairs","State Highway" => "State Highway","Steps" => "Steps","STRAIGHT" => "STRAIGHT","Strait" => "Strait","Strand" => "Strand","Street" => "Street","Stright" => "Stright","Strip" => "Strip","Subway" => "Subway","Tarn" => "Tarn","Terrace" => "Terrace","Thoroughfare" => "Thoroughfare","Throughway" => "Throughway","Tollway" => "Tollway","Top" => "Top","Tor" => "Tor","Towers" => "Towers","Track" => "Track","Trail" => "Trail","Trailer" => "Trailer","Tramway" => "Tramway","Traverse" => "Traverse","Triangle" => "Triangle","Trunkway" => "Trunkway","Tunnel" => "Tunnel","Turn" => "Turn","Underpass" => "Underpass","Upper" => "Upper","Vale" => "Vale","Valley" => "Valley","Viaduct" => "Viaduct","View" => "View","Views" => "Views","VILLA" => "VILLA","Village" => "Village","Villas" => "Villas","Vista" => "Vista","Vue" => "Vue","Wade" => "Wade","Walk" => "Walk","Walkway" => "Walkway","Waterway" => "Waterway","Way" => "Way","Wharf" => "Wharf","Wood" => "Wood","Woods" => "Woods","Wynd" => "Wynd","Yard" => "Yard");
    $stateList = array(""=>"None","ACT"=>"ACT","NSW"=>"NSW","NT"=>"NT","QLD"=>"QLD","SA"=>"SA","VIC"=>"VIC","WA"=>"WA");
    $hearaboutus  = array(""=>"None",'You contacted me!' => 'You contacted me!', 'Search Engine' => 'Search Engine', 'Social Network' => 'Social Network', 'Advertisement'=>'Advertisement', 'Friend'=>'Friend', 'Event'=>'Event','Forum or Blog'=>'Forum or Blog', 'Other'=>'Other');

  ?>
  <div><h4 class="box-bg">Connection Address</h4></div>
  <div class="form-horizontal" role="form">
    <div class="form-group">
      <label class="col-sm-4 control-label">Search Address</label>
      <div class="col-sm-8">
        {{ Form::text('searchaddress',Input::old('searchaddress'),array('autocomplete'=>'off', 'id'=>'searchaddress','class'=>'form-control')) }}
        <div class="alert-danger text-left" role="alert">{{ $suburb_msg }}</div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label"></label>
      <div id="suggestAddresses" class="col-sm-8 hide">
      </div>
      <div id="loader_area" class="hide"><img src="<?php echo "http://" . $_SERVER['SERVER_NAME'];?>/laravel/public/images/loader.gif" height="50"></div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">Unit #</label>
      <div class="col-sm-8">
        {{ Form::text('unit_number',Input::old('unit_number'),array('autocomplete'=>'off','id'=>'unit_number', 'class'=>'form-control','min'=>'0','oninput'=>"setCustomValidity('')",'oninvalid' => "this.setCustomValidity('Unit number must be number Ex: 8')")) }}
        <div class="alert-danger text-left" role="alert">{{ $unitNumber_msg }}</div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">Street #*</label>
      <div class="col-sm-8">
        {{ Form::text('street_number',Input::old('street_number'),array('autocomplete'=>'off','id'=>'street_number','pattern'=>'^[0-9/ -]+$', 'required'=>'required','class'=>'form-control','oninput'=>"setCustomValidity('')",'oninvalid' => "this.setCustomValidity('Enter Street Number Ex: 43-48')")) }}
        <div class="alert-danger text-left" role="alert">{{ $streetNumber_msg }}</div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">Street Name*</label>
      <div class="col-sm-8">
        {{ Form::text('street_name', Input::old('street_name'),  array('autocomplete'=>'off','id'=>'street_name','required'=>'required','class'=>'form-control','placeholder'=>'is used as the connection name')) }}
        <div class="alert-danger text-left" role="alert">{{ $streetName_msg }}</div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">Street Type*</label>
      <div class="col-sm-8">
        {{ Form::select('street_type', $streetTypeList, Input::old('street_type'), array('autocomplete'=>'off','id'=>'street_type','class' => 'form-control','required'=>'required')) }}
        <div class="alert-danger text-left" role="alert">{{ $streetType_msg }}</div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">Suburb*</label>
      <div class="col-sm-8">
        {{ Form::text('suburb',Input::old('suburb'),array('autocomplete'=>'off','id'=>'suburb', 'required'=>'required','class'=>'form-control')) }}
        <div class="alert-danger text-left" role="alert">{{ $suburb_msg }}</div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">State*</label>
      <div class="col-sm-8">
         {{ Form::select('state', $stateList, Input::old('state'), array('autocomplete'=>'off','id'=>'state', 'class' => 'form-control','required'=>'required')) }}
         <div class="alert-danger text-left" role="alert">{{ $state_msg }}</div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">Postcode*</label>
      <div class="col-sm-8">
        {{ Form::text('postcode',Input::old('postcode'),array('autocomplete'=>'off','pattern'=>'[0-9]{4,8}','id'=>'postcode' ,'required'=>'required','class'=>'form-control','oninput'=>"setCustomValidity('')",'oninvalid' => "this.setCustomValidity('Enter postcode value Ex: 4606')")) }}
        <div class="alert-danger text-left" role="alert">{{ $postcode_msg }}</div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">Closing Date*</label>
      <div class="col-sm-8">
        {{ Form::text('closing_date', Input::old('closing_date'),array('autocomplete'=>'off','required'=>'required','class'=>'datepicker form-control','placeholder'=>'the date when the new connection should be completed')) }}
        <div class="alert-danger text-left" role="alert">{{ $closingDate_msg }}</div>
      </div>
    </div>
    
   <div><br><h4 class="box-bg">Primary Customer Details </h4></div>
  	<div class="form-group"> 
      <label class="col-sm-4 control-label">Title*</label>
      <div class="col-sm-8">
        {{ Form::select('title', $titleList, Input::old('title'), array('autocomplete'=>'off','class' => 'form-control','required'=>'required')) }}
        <div class="alert-danger text-left" role="alert">{{ $title_msg }}</div>
      </div>
    </div>
  	<div class="form-group">
      <label class="col-sm-4 control-label">First Name*</label>
      <div class="col-sm-8">
        {{ Form::text('first_name',Input::old('first_name'),array('autocomplete'=>'off','required'=>'required','class'=>'form-control')) }}
        <div class="alert-danger text-left" role="alert">{{ $firstName_msg }}</div>
      </div>
    </div>
  	<div class="form-group">
      <label class="col-sm-4 control-label">Last Name</label>
      <div class="col-sm-8">
        {{ Form::text('last_name',Input::old('last_name'),array('autocomplete'=>'off','class'=>'form-control')) }}
        <div class="alert-danger text-left" role="alert">{{ $lastName_msg }}</div>
      </div>
    </div>
  	<div class="form-group">
      <label class="col-sm-4 control-label">Mobile*</label> 
      <div class="col-sm-8">
        {{ Form::text('mobile',Input::old('mobile'),array('autocomplete'=>'off','pattern'=>'[0-9]{8,12}','required'=>'required','class'=>'form-control','oninput'=>"setCustomValidity('')",'oninvalid' => "this.setCustomValidity('Enter Mobile Number Ex: +61xxxxxxxxx')")) }}
        <div class="alert-danger text-left" role="alert">{{ $mobile_msg }}</div>
      </div>
    </div>
  	<div class="form-group">
      <label class="col-sm-4 control-label">Other Phone</label>
      <div class="col-sm-8">
        {{ Form::text('other_phone',Input::old('other_phone'),array('autocomplete'=>'off','pattern'=>'[0-9]{8,12}','class'=>'form-control','oninput'=>"setCustomValidity('')",'oninvalid' => "this.setCustomValidity('Other phone must be number Ex: +61xxxxxxxxx')")) }}
        <div class="alert-danger text-left" role="alert">{{ $otherPhone_msg }}</div>
      </div>
    </div>
  	<div class="form-group">
      <label class="col-sm-4 control-label">Email</label>
      <div class="col-sm-8">
        {{ Form::input('email','email',Input::old('email'),array('autocomplete'=>'off','class'=>'form-control','oninput'=>"setCustomValidity('')",'oninvalid' => "this.setCustomValidity('Incorrect email address format. Ex: ray.m@gmail.com')")) }}
        <div class="alert-danger text-left" role="alert">{{ $email_msg }}</div>
      </div>
    </div>
  	<div class="form-group">
      <label class="col-sm-4 control-label">DOB</label>
      <div class="col-sm-8">
        {{ Form::text('dob',Input::old('dob'),array('autocomplete'=>'off','class'=>'datepickerDOB form-control')) }}
        <div class="alert-danger text-left" role="alert">{{ $dob_msg }}</div>
      </div>
    </div>
  	<div class="form-group">
      <label class="col-sm-4 control-label">ID Type</label>
      <div class="col-sm-8">
        {{ Form::select('id_type', $idList, Input::old('id_type'), array('autocomplete'=>'off','class' => 'form-control')) }}
        <div class="alert-danger text-left" role="alert">{{ $idType_msg }}</div>
      </div>
    </div>
  	<div class="form-group">
      <label class="col-sm-4 control-label">ID #</label>
      <div class="col-sm-8">
        {{ Form::text('id_number',Input::old('id_number'),array('autocomplete'=>'off','class'=>'form-control','oninput'=>"setCustomValidity('')",'oninvalid' => "this.setCustomValidity('Enter Id Number Ex: XNNNXXX')")) }}
        <div class="alert-danger text-left" role="alert">{{ $idNumber_msg }}</div>
      </div>
    </div>
  	<div class="form-group">
      <label class="col-sm-4 control-label">ID Location</label>
      <div class="col-sm-8">
        {{ Form::select('id_location', $idLocation, Input::old('id_location'), array('autocomplete'=>'off','class' => 'form-control')) }}
        <div class="alert-danger text-left" role="alert">{{ $idLocation_msg }}</div>
      </div>
    </div>

    <div class="form-group">
      <label class="col-sm-4 control-label">How did you hear about us </label>
      <div class="col-sm-8">
        {{  Form::select('how_did_u_hear', $hearaboutus, Input::old('how_did_u_hear'),  array('class' => 'form-control')) }}
        <div class="alert-danger text-left" role="alert">{{ $idLocation_msg }}</div>
      </div>
    </div>

    <div><br><h4 class="box-bg">Additional Customer Details</h4></div>
    <div class="form-group"> 
      <label class="col-sm-4 control-label">Title</label>
      <div class="col-sm-8">
        {{ Form::select('titleAdditional', $titleList, Input::old('titleAdditional'), array('autocomplete'=>'off','class' => 'form-control')) }}
        <div class="alert-danger text-left" role="alert">{{ $titleAdditional_msg }}</div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">First Name</label>
      <div class="col-sm-8">
        {{ Form::text('first_nameAdditional',Input::old('first_nameAdditional'),array('autocomplete'=>'off','class'=>'form-control')) }}
        <div class="alert-danger text-left" role="alert">{{ $firstNameAdditional_msg }}</div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">Last Name</label>
      <div class="col-sm-8">
        {{ Form::text('last_nameAdditional',Input::old('last_nameAdditional'),array('autocomplete'=>'off','class'=>'form-control')) }}
        <div class="alert-danger text-left" role="alert">{{ $lastNameAdditional_msg }}</div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">DOB</label>
      <div class="col-sm-8">
        {{ Form::text('dobAdditional',Input::old('dobAdditional'),array('autocomplete'=>'off','class'=>'datepickerDOB form-control')) }}
        <div class="alert-danger text-left" role="alert">{{ $dobAdditional_msg }}</div>
      </div>
    </div>
    <div class="form-group">
      <label class="col-sm-4 control-label">Mobile</label> 
      <div class="col-sm-8">
        {{ Form::text('mobileAdditional',Input::old('mobileAdditional'),array('autocomplete'=>'off','pattern'=>'[0-9]{8,12}','class'=>'form-control','oninput'=>"setCustomValidity('')",'oninvalid' => "this.setCustomValidity('Enter Mobile Number Ex: +61xxxxxxxxx')")) }}
        <div class="alert-danger text-left" role="alert">{{ $mobileAdditional_msg }}</div>
      </div>
    </div>
   
  	<div class="form-group">
      <div class="col-sm-12">{{ Form::submit('Submit',array('class'=>'btn btn-default btn-black') )}}</div>
    </div>
  </form>
 </div>
{{ Form::close() }}
</div>

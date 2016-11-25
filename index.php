
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../favicon.ico">

    <title>Facebook Login & Map Search</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/style.css" rel="stylesheet">
  </head>

  <body>

    <div class="container">
      
<?php
include_once("config.php");
include_once("includes/functions.php");
//destroy facebook session if user clicks reset
if(!$fbuser){  


$fbuser = null;
	$loginUrl = $facebook->getLoginUrl(array('redirect_uri'=>$homeurl,'scope'=>$fbPermissions));
	?>
	<div class="jumbotron">
        <h1 class="display-3">Facebook With Login</h1>
        <p><?php $output = '<a href="'.$loginUrl.'"><img src="images/fb_login.png"></a>'; ?></p>
      </div>
	 	<?php
}else{
	$user_profile = $facebook->api('/me?fields=id,first_name,last_name,email,gender,locale,picture');
	$user = new Users();
	$user_data = $user->checkUser('facebook',$user_profile['id'],$user_profile['first_name'],$user_profile['last_name'],$user_profile['email'],$user_profile['gender'],$user_profile['locale'],$user_profile['picture']['data']['url']);
	if(!empty($user_data)){
		$output = '';
		
		?>
		   <h1>Facebook Profile Details</h1>
		
		<div class="table-responsive">
		<table class="table table-striped">
		<thead>
		<tr>
		<td>Profile Picture</td>
        <td>Facebook ID </td>
        <td>Name : </td>
        <td>Email : </td>
        <td>Gender :  </td>
       <td> Locale :  </td>
       <td> You are login with :</td>
        <td>Logout</td></tr></thead>
		<tbody>
		<tr>
		<td><img src="<?php echo $user_data['picture']; ?>"></td>
        <td><?php echo $user_data['oauth_uid']; ?></td>
        <td><?php echo $user_data['fname'].' '.$user_data['lname']; ?></td>
        <td><?php echo $user_data['email']; ?> </td>
        <td><?php  echo $user_data['gender']; ?> </td>
       <td> <?php echo $user_data['locale']; ?> </td>
       <td> <?php  echo "Facebook"; ?> </td>
        <td>Logout from <a href="logout.php?logout">Facebook</a> </td></tr></table></div>
	<?php
	}else{
		$output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
	}
}
?>
<script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCuvPsS0H5YUYhjntFtf9Vazf6TJM2gprk&sensor=false&libraries=places"></script>
        <style type="text/css">
            #map {
                height: 400px;
                width: 1100px;
                border: 1px solid #333;
                margin-top: 0.6em;
            }
        </style>

        <script type="text/javascript">
		
            $(function(){
                $('.chkbox').click(function(){
                    $(':checkbox').attr('checked',false);
                    $('#'+$(this).attr('id')).attr('checked',true);
                    search_types(map.getCenter());
                });
                
            });     
            
            var map;
            var infowindow;
            var markersArray = [];
            var pyrmont = new google.maps.LatLng(18.629782, 73.799706);
            var marker;
            var geocoder = new google.maps.Geocoder();
            var infowindow = new google.maps.InfoWindow();
            // var waypoints = [];                  
            function initialize() {
                map = new google.maps.Map(document.getElementById('map'), {
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    center: pyrmont,
                    zoom: 14
                });
                infowindow = new google.maps.InfoWindow();
                //document.getElementById('directionsPanel').innerHTML='';
                search_types();
				showMap();
               }

            function createMarker(place,icon) {
                var placeLoc = place.geometry.location;
                var marker = new google.maps.Marker({
                    map: map,
                    position: place.geometry.location,
                    icon: icon,
                    visible:true  
                    
                });
                
                markersArray.push(marker);
                google.maps.event.addListener(marker, 'click', function() {
                    infowindow.setContent("<b>Name:</b>"+place.name+"<br><b>Address:</b>"+place.vicinity+"<br><b>Reference:</b>"+place.reference+"<br><b>Rating:</b>"+place.rating+"<br><b>Id:</b>"+place.id);
                    infowindow.open(map, this);
                });
               
            }
            var source="";
            var dest='';
			
		   function search_types(latLng){
                clearOverlays(); 
              
                if(!latLng){
                    var latLng = pyrmont;
                }
                var type = $('.chkbox:checked').val();
                var icon = "images/"+type+".png";
              //  var rad= $('#radius').val();
            // $('#radius').change(function() {
				//var rad = $('#radius').val();
			// });
				var rad1 = setRadiusInput();
                var request = {
                    location: latLng,
                    radius: rad1,
                    types: [type] //e.g. school, restaurant,bank,bar,city_hall,gym,night_club,park,zoo
                };
               
                var service = new google.maps.places.PlacesService(map);
                service.search(request, function(results, status) {
                    map.setZoom(14);
                    if (status == google.maps.places.PlacesServiceStatus.OK) {
                        for (var i = 0; i < results.length; i++) {
                            results[i].html_attributions='';
                            createMarker(results[i],icon);
                        }
                    }
                });
                
             }
            
            
            // Deletes all markers in the array by removing references to them
            function clearOverlays() {
                if (markersArray) {
                    for (i in markersArray) {
                        markersArray[i].setVisible(false)
                    }
                    //markersArray.length = 0;
                }
            }
            google.maps.event.addDomListener(window, 'load', initialize);
            
            function clearMarkers(){
                $('#show_btn').show();
                $('#hide_btn').hide();
                clearOverlays()
            }
            function showMarkers(){
                $('#show_btn').hide();
                $('#hide_btn').show();
                if (markersArray) {
                    for (i in markersArray) {
                        markersArray[i].setVisible(true)
                    }
                     
                }
            }
           
            function showMap(){
                
                var imageUrl = 'http://chart.apis.google.com/chart?cht=mm&chs=24x32&chco=FFFFFF,008CFF,000000&ext=.png';
                var markerImage = new google.maps.MarkerImage(imageUrl,new google.maps.Size(24, 32));
                var input_addr=$('#address').val();
                
				geocoder.geocode({address: input_addr}, function(results, status) {
                    if (status == google.maps.GeocoderStatus.OK) {
                        var latitude = results[0].geometry.location.lat();
                        var longitude = results[0].geometry.location.lng();
                        var latlng = new google.maps.LatLng(latitude, longitude);
                        if (results[0]) {
                            map.setZoom(14);
                            map.setCenter(latlng);
                            marker = new google.maps.Marker({
                                position: latlng, 
                                map: map,
                                icon: markerImage,
                                draggable: true 
                                
                            }); 
                          //  $('#btn').hide();
                            $('#latitude,#longitude').show();
                            $('#address').val(results[0].formatted_address);
                            $('#latitude').val(marker.getPosition().lat());
                            $('#longitude').val(marker.getPosition().lng());
                            infowindow.setContent(results[0].formatted_address);
                            infowindow.open(map, marker);
                            search_types(marker.getPosition());
                            google.maps.event.addListener(marker, 'click', function() {
                                infowindow.open(map,marker);
                                
                            });
                        
                        
                            google.maps.event.addListener(marker, 'dragend', function() {
                              
                                geocoder.geocode({'latLng': marker.getPosition()}, function(results, status) {
                                    if (status == google.maps.GeocoderStatus.OK) {
                                        if (results[0]) {
                                            $('#btn').hide();
                                            $('#latitude,#longitude').show();
                                            $('#address').val(results[0].formatted_address);
                                            $('#latitude').val(marker.getPosition().lat());
                                            $('#longitude').val(marker.getPosition().lng());
                                        }
                                        
                                        infowindow.setContent(results[0].formatted_address);
                                        var centralLatLng = marker.getPosition();
                                        search_types(centralLatLng);
                                        infowindow.open(map, marker);
                                    }
                                });
                            });
                            
                        
                        } else {
                            alert("No results found");
                        }
                    } else {
                        alert("Geocoder failed due to: " + status);
                    }
                });
                
		}
		function setRadiusInput(){
			
		var rad=	$('#radius').val();
		return rad;
		}
  
       //    window.onload = geolocateUser;
        </script>
 <?php    echo $output;
if($fbuser){  ?>
	  
		<div class="table-responsive">
		
            <table class="table table-striped">
			
                <tr>
                    <td> <input type="checkbox" name="mytype" class="chkbox" id="school"  value="school" /><label for="school">School</label><br/></td>
                    <td><input type="checkbox" name="mytype" class="chkbox" id="restaurant" checked="checked" value="restaurant"/><label for="restaurant" >Restaurant</label></td>
                </tr>
                <tr>
                    <td> <input type="checkbox" name="mytype" class="chkbox"  id="hospital"  value="hospital"/><label for="hospital" >Hospital</label></td>
                    <td><input type="checkbox" name="mytype"  class="chkbox" id="bus_station"  value="bus_station"/><label for="bus_station" >Bus Stopedge</label></td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="mytype"  class="chkbox" id="park"  value="park"/><label for="park" >Park</label></td>
                    <td><input type="checkbox" name="mytype"  class="chkbox" id="bank"  value="bank"/><label for="bank" >Bank</label></td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="mytype"  class="chkbox" id="bar"  value="bar"/><label for="bar" >Bar</label></td>
                    <td><input type="checkbox" name="mytype"  class="chkbox" id="movie_theater"  value="movie_theater"/><label for="movie_theater" >Movie Theater</label></td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="mytype"  class="chkbox" id="night_club"  value="night_club"/><label for="night_club" >Night Club</label></td>
                    <td><input type="checkbox" name="mytype"  class="chkbox" id="zoo"  value="zoo"/><label for="zoo" >Zoo</label><br/></td>
                </tr>

                <tr>
                    <td><input type="checkbox" name="mytype"  class="chkbox" id="gym"  value="gym"/><label for="gym" >Gym</label></td>
                    <td><input type="checkbox" name="mytype"  class="chkbox" id="atm"  value="atm"/><label for="atm" >ATM</label></td>
                </tr>
                <tr>
                    <td><input type="checkbox" name="mytype"  class="chkbox" id="spa"  value="spa"/><label for="spa" >Spa</label></td>

                </tr>
            </table>
			</div>
       
        <label>Address: </label><input id="address" class="form-control" type="text" style="width:400px;" value="Pune,Maharastra,india"/>  
		 
	 <label>Radius: </label><input id="radius" class="form-control" type="text" style="width:400px;" value="2000"/> 
	  <input type="button" value="submit" id="btn" onClick="showMap();"/> 
	
       
        <br/>
        <div id="map"></div>
      <!--  <input type="text" id="latitude" style="display:none;" placeholder="Latitude"/>
        <input type="text" id="longitude" style="display:none;" placeholder="Longitude"/>
       <!-- <input type="button"  id="hide_btn" value="hide markers" onClick="clearMarkers();" />-->
        <input type="button" id="show_btn" value="show  markers" onClick="showMarkers();" style="display:none;" />

        <div id="test"></div>
<?php } ?>
    </body>












     

    </div> <!-- /container -->

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
  </body>
</html>
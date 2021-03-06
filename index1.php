<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<style type="text/css">
h1{font-family:Arial, Helvetica, sans-serif;color:#999999;}
</style>
<?php
include_once("config.php");
include_once("includes/functions.php");
//destroy facebook session if user clicks reset
if(!$fbuser){
	$fbuser = null;
	$loginUrl = $facebook->getLoginUrl(array('redirect_uri'=>$homeurl,'scope'=>$fbPermissions));
	$output = '<a href="'.$loginUrl.'"><img src="images/fb_login.png"></a>'; 	
}else{
	$user_profile = $facebook->api('/me?fields=id,first_name,last_name,email,gender,locale,picture');
	$user = new Users();
	$user_data = $user->checkUser('facebook',$user_profile['id'],$user_profile['first_name'],$user_profile['last_name'],$user_profile['email'],$user_profile['gender'],$user_profile['locale'],$user_profile['picture']['data']['url']);
	if(!empty($user_data)){
		$output = '';
		
		?>
		   <h1>Facebook Profile Details</h1>
		
		<table border="1" style="center">
		<tr>
		<td>Profile Picture</td>
        <td>Facebook ID </td>
        <td>Name : </td>
        <td>Email : </td>
        <td>Gender :  </td>
       <td> Locale :  </td>
       <td> You are login with :</td>
        <td>Logout</td></tr>
		<tr>
		<td><img src="<?php echo $user_data['picture']; ?>"></td>
        <td><?php echo $user_data['oauth_uid']; ?></td>
        <td><?php echo $user_data['fname'].' '.$user_data['lname']; ?></td>
        <td><?php echo $user_data['email']; ?> </td>
        <td><?php  echo $user_data['gender']; ?> </td>
       <td> <?php echo $user_data['locale']; ?> </td>
       <td> <?php  echo "Facebook"; ?> </td>
        <td>Logout from <a href="logout.php?logout">Facebook</a> </td></tr></table>
	<?php
	}else{
		$output = '<h3 style="color:red">Some problem occurred, please try again.</h3>';
	}
}







?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<style type="text/css">
h1{font-family:Arial, Helvetica, sans-serif;color:#999999;}
</style>





        
      <title>Login with Facebook </title>
        <script type="text/javascript" src="js/jquery.js"></script>
        <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?key=AIzaSyCuvPsS0H5YUYhjntFtf9Vazf6TJM2gprk&sensor=false&libraries=places"></script>
        <style type="text/css">
            #map {
                height: 400px;
                width: 1000px;
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
    </head>
    <body>
<?php  ?>

   <?php    echo $output;
if($fbuser){  ?>
	  
		<div id="container" class="container" >
		<div>
            <table border="0" cellspacing="0" cellpadding="3">
			
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
        </div>
        <label>Address: </label><input id="address"  type="text" style="width:400px;" value="Pune,Maharastra,india"/>  
		 <input type="button" value="submit" id="btn" onClick="showMap();"/>  
	 <label>Radius: </label><input id="radius"  type="text" style="width:400px;" value="2000"/> 
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










</html>
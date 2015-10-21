<html>
<head>
<style>
#bd {
    background-color: LightSkyBlue;
}
#header1 {  
    font-family: "Arial";
    font-weight: bold;
    font-size: 30px;
    color: DimGray;
    padding-top: 35px;
    text-align: center;
}
#inframe {
    padding-top: 20px;
    font-family: "Arial";
    font-weight: bold;
    font-size: 15px;
    line-height: 30px;
    text-align: center;
}
#result {
    background-color: LightGreen;
    text-align: center;
    font-family: "Arial";
}
#title {
    padding-top: 10px;
    font-size: 25px;
    font-weight: bold;
    color: DimGray;
}
#image {
    padding-top: 20px;
}
#content {
    padding-top: 20px;
    font-family: "Arial";
    font-weight: bold;
    font-size: 15px;
    line-height: 25px;
}
</style>
</head>
<body id = "bd">
<?php
//define variables and set to empty values
$streetAddress = $city = $state = $degree = "";
$addressErr = $cityErr = $stateErr = $degreeErr = "";
$wholeAddress = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (empty($_POST["streetAddress"])) {
		$addressErr = "Street address is required";
		echo "<script type = 'text/javascript'>";
		echo "window.alert('Street address is required')";
		echo "</script>";
	} else {
		$streetAddress = test_input($_POST["streetAddress"]);
	}
	
	if (empty($_POST["city"])) {
		$cityErr = "City is required";
		echo "<script type = 'text/javascript'>";
		echo "window.alert('City is required')";
		echo "</script>";
	} else {
		$city = test_input($_POST["city"]);
	}
	
	if (empty($_POST["state"])) {
		$stateErr = "State is required";
		echo "<script type = 'text/javascript'>";
		echo "window.alert('State is required')";
		echo "</script>";
	} else {
		$state = test_input($_POST["state"]);
	}
	
	if (empty($_POST["degree"])) {
		$degreeErr = "Degree is required";
		echo "<script type = 'text/javascript'>";
		echo "window.alert('Degree is required')";
		echo "</script>";
	} else {
		$degree = test_input($_POST["degree"]);
	}
	$stateArray = array("Alabama", "Alaska", "Arizona", "Arkansas", "California",
			"Colorado", "Connecticut", "Delaware", "District Of Columbia", "Florida",
			"Georgia", "Hawaii", "Idaho", "Illinois", "Indiana",
			"Iowa", "Kansas", "Kentucky", "Louisiana", "Maine",
			"Maryland", "Massachusetts", "Michigan", "Minnesota", "Mississippi",
			"Missouri", "Montana", "Nebraska", "Nevada", "New Hampshire",
			"New Jersey", "New Mexico", "New York", "North Carolina", "North Dakota",
			"Ohio", "Oklahoma", "Oregon", "Pennsylvania", "Rhode Island",
			"South Carolina", "South Dakota", "Tennessee", "Texas", "Utah",
			"Vermont", "Virginia", "Washington", "West Virginia", "Wisconsin",
			"Wyoming");
	$stateAbbr = array("AL", "AK", "AZ", "AR", "CA",
			"CO", "CT", "DE", "DC", "FL",
			"GA", "HI", "ID", "IL", "IN",
			"IA", "KS", "KY", "LA", "ME",
			"MD", "MA", "MI", "MN", "MS",
			"MO", "MT", "NE", "NV", "NH",
			"NJ", "NM", "NY", "NC", "ND",
			"OH", "OK", "OR", "PA", "RI",
			"SC", "SD", "TN", "TX", "UT",
			"VT", "VA", "WA", "WV", "WI", "WY");
	$url1 = "";
	$url1 .= "https://maps.googleapis.com/maps/api/geocode/xml?address=";
	$url1 .= word_space($streetAddress);
	$url1 .= ",";
	$url1 .= word_space($city);
	$url1 .= ",";
	for ($i = 0; $i < count($stateArray); $i++) {
		if (strcmp($stateArray[$i], $state) == 0) {
			$url1.= $stateAbbr[$i];
			break;
		}
	}
	$url1 .= "&key=AIzaSyD91K7up4FLbt5z82AMWkRzo5HRzqtSu4M";
	$xml = simplexml_load_file("$url1") or die("Error: Cannot create object");
	$lat = $xml->result[0]->geometry->location->lat;
	$lng = $xml->result[0]->geometry->location->lng;
	$url2 .= "https://api.forecast.io/forecast/969c9088f39293890daea2462e43b158/";
	$url2 .= $lat;
	$url2 .= ",";
	$url2 .= $lng;
	$url2 .= "?units=";
	if ($degree == "Celsius") {
		$url2 .= "si";
	} else {
		$url2 .= "us";
	}
	$url2.= "&exclude=flags";
	$json = file_get_contents("$url2");
	$jsonContent = json_decode($json, true);
	$summary = $icon = $image = $temprature = $precipitation
	= $windSpeed = $dewPoint = $humidity = $visibility
	= $sunset = "";
	$summary = $jsonContent['currently']['summary'];
	$icon = $jsonContent['currently']['icon'];
	if (strcmp($icon, "clear-day") == 0) {
		$image = "clear.png";
	} else if (strcmp($icon, "clear-night") == 0) {
		$image = "clear_night.png";
	} else if (strcmp($icon, "rain") == 0) {
		$image = "rain.png";
	} else if (strcmp($icon, "snow") == 0) {
		$image = "snow.png";
	} else if (strcmp($icon, "sleet") == 0) {
		$image = "sleet.png";
	} else if (strcmp($icon, "wind") == 0) {
		$image = "wind.png";
	} else if (strcmp($icon, "fog") == 0) {
		$image = "fog.png";
	} else if (strcmp($icon, "cloudy") == 0) {
		$image = "cloudy.png";
	} else if (strcmp($icon, "partly-cloudy-day") == 0) {
		$image = "cloud_day.png";
	} else if (strcmp($icon,"partly-cloudy-night") == 0) {
		$image = "cloud_night.png";
	} 
	$temprature = $jsonContent['currently']['temperature'];
	$pre = $jsonContent['currently']['precipIntensity'];
	if ($pre == 0) {
		$precipitation = "None";
	} else if ($pre == 0.002) {
		$precipitation = "Very Light";
	} else if ($pre == 0.017) {
		$precipitation = "Light";
	} else if ($pre == 0.1) {
		$precipitation = "Moderate";
	} else if ($pre == 0.4) {
		$precipitation = "Heavy";
	}
	$windSpeed = $jsonContent['currently']['windSpeed'];
	$windSpeed.= "mph";
	$dewPoint = $jsonContent['currently']['dewPoint'];
	$hum = $jsonContent['currently']['humidity'] * 100;
	$humidity.= $hum;
	$humidity.= "%";
	$vis = $jsonContent['currently']['visibility'];
	$visibility.= $vis;
	$visibility.= "m"; 
	$rise = $jsonContent['daily']['data'][0]['sunriseTime'];
	date_default_timezone_set('America/Los Angeles');
}

function test_input($data) {
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

function word_space($data) {
	$array = explode(" ", $data);
	$result = $array[0];
	for ($i = 1; $i < count($array); $i++) {
		$result.="+";
		$result.= $array[$i];
	}
	return $result;
}?>
<div id = "header1"> Forecast Search </div>
<div id = "inframe">
<form method = "post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
    Street Address: <input type = "text" name = "streetAddress">
    <span class = "error"> * <?php echo $addressErr?></span><br>
    City: <input type = "text" name = "city">
    <span class = "error"> * <?php echo $cityErr?></span><br>
    State: <select name = "state">
    <option selected = selected value = ""> Select a state</option>
    <option value = "Alabama">Alabama</option>
    <option value = "Alaska">Alaska</option>
    <option value = "Arizona">Arizona</option>
    <option value = "Arkansa">Arkansa</option>
    <option value = "California">California</option>
    <option value = "Colorado">Colorado</option>
    <option value = "Connecticut">Connecticut</option>
    <option value = "Delaware">Delaware</option>
    <option value = "District Of Columbia">District Of Columbia</option>
    <option value = "Florida">Florida</option>
    <option value = "Georgia">Georgia</option>
    <option value = "Hawaii">Hawaii</option>
    <option value = "Idaho">Idaho</option>
    <option value = "Illinois">Illinois</option>
    <option value = "Indiana">Indiana</option>
    <option value = "Iowa">Iowa</option>
    <option value = "Kansas">Kansas</option>
    <option value = "Kentucky">Kentucky</option>
    <option value = "Louisiana">Louisiana</option>
    <option value = "Maine">Maine</option>
    <option value = "Maryland">Maryland</option>
    <option value = "Massachusetts">Massachusetts</option>
    <option value = "Michigan">Michigan</option>
    <option value = "Minnesota">Minnesota</option>
    <option value = "Mississippi">Mississippi</option>
    <option value = "Missouri">Missouri</option>
    <option value = "Montana">Montana</option>
    <option value = "Nebraska">Nebraska</option>
    <option value = "Nevada">Nevada</option>
    <option value = "New Hampshire">New Hampshire</option>
    <option value = "New Jersey">New Jersey</option>
    <option value = "New Mexico">New Mexico</option>
    <option value = "New York">New York</option>
    <option value = "North Carolina">North Carolina</option>
    <option value = "North Dakota">North Dakota</option>
    <option value = "Ohio">Ohio</option>
    <option value = "Oklahoma">Oklahoma</option>
    <option value = "Oregon">Oregon</option>
    <option value = "Pennsylvania">Pennsylvania</option>
    <option value = "Rhode Island">Rhode Island</option>
    <option value = "South Carolina">South Carolina</option>
    <option value = "South Dakota">South Dakota</option>
    <option value = "Tennessee">Tennessee</option>
    <option value = "Texas">Texas</option>
    <option value = "Utah">Utah</option>
    <option value = "Vermont">Vermont</option>
    <option value = "Virginia">Virginia</option>
    <option value = "Washington">Washington</option>
    <option value = "West Virginia">West Virginia</option>
    <option value = "Wisconsin">Wisconsin</option>
    <option value = "Wyoming">Wyoming</option>
    </select><br>
    Degree: 
    <input type="radio" name="degree" value="fehrenheit">Fehrenheit
    <input type="radio" name="degree" value="celsius">Celsius
    <span class="error">* <?php echo $degreeErr?></span><br>
    <input type="submit" value="Search">
    <input type ="reset" value="Clear">
</form>   
<p id = "note"><span class = "error"> * Required Field</span></p>
</div>
<div id = "result">
<div id = "title">
<?php 
if (strcmp($summary, "") != 0) {
    echo "$summary<br>";
    if (strcmp($degree, "fehrenheit") == 0) {
	    $temprature .= " F";	
    } else if(strcmp($degree, "celsius") == 0){
	    $temprature .= " C";
    }
    echo "$temprature<br>";
}?>
</div>
<div id = "image">
<?php 
//function startPrint2(){
if (strcmp($image, "") != 0) {
    echo "<img src = '$image'><br>";
   // echo ("<script>location.href = 'image/png?msg=$msg';</script>");
   // echo "$image";
   // $im = imagecreatefrompng("$image");
   // imagepng($im);
   // imagedestroy($im);
}
?>
</div>
<div id = "content">
<?php 
//function startPrint3() {
if (strcmp($summary, "") != 0) {
    echo "Precipitation: ";
    echo "   ";
    echo "$precipitation<br>";
    echo "Wind Speed:     ";
    echo "$windSpeed<br>";
    echo "Dew Point: ";
    echo "    ";
    echo "$dewPoint<br>";
    echo "Humidity:     ";
    echo "      ";
    echo "$humidity<br>";
    echo "Visibility:     ";
    echo "     ";
    echo "$visibility<br>";
}
?>
</div>
</div>
</body>
</html>



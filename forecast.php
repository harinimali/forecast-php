<html>
<head>
<title> Weather Forecast</title>
<style>
    table{
        border:2px;
        border-style:solid;
    }
    </style>

<script type="text/javascript">
    
    function erase() 
    {
        document.getElementById("display").innerHTML="";
        
    if(document.getElementById('address').value!="")
        {
         document.getElementById('address').value="";   
        }
    if(document.getElementById('city').value!= null)
        {
         document.getElementById('city').value="";   
        }
    if(document.getElementById('state').value!= null)
        {
         document.getElementById('state').value="x";   
        }
    
    document.forms["forecast"]["degree"].value="fahrenheit";
        
    }
 function validate()
    {
      var blank="";
      var reg=/^[a-zA-Z]+(?:[\s-][a-zA-Z]+)*$/;
      var degree = document.getElementsByName("degree");
      var x;
                if(document.getElementById('address').value.trim()=="")
                {
                    alert("Please enter a value for Street Address.");
                    return false;
                }
               if(document.getElementById('city').value.trim()=="")
                {
                    alert("Please enter a value for City.");
                    return false;
                }
                
               if(document.getElementById('state').value=="x")
                {
                    alert("Please choose a state.");
                    return false;
                }
               if(!reg.test(document.getElementById('city').value))
                {
                    alert("City name should not contain numbers or special characters.")
                    return false;
                }
        for (var i = 0; i < degree.length; i++) 
        {
                if (degree[i].checked == false) 
                {
                    x++;
                }
        }
            if (x == degree.length) 
            {
                alert("Please select a value for degree.");
                return false;
            }
        
    }   
    
    </script>
</head>

<body>
    <?php
        if(isset($_POST["submit"]))
            {
                $addr=$_POST["stradd"];
                $city=$_POST["city"];
                $state=$_POST["state"];
                $degree=$_POST["degree"];
                $api="601851d44f01ef11bfe74a356c93f917";
                if(isset($_POST["stradd"]) && isset($_POST["city"]))
                {
                    $map="https://maps.googleapis.com/maps/api/geocode/xml?address=". rawurlencode($addr). ",". rawurlencode($city). ",". rawurlencode($state)."&key=AIzaSyCHHj0xlmttTEACk9H0klxCIku2I1Aih1M";
                    $resp = file_get_contents($map);
                    $xml_resp = simplexml_load_string($resp);
                    if($xml_resp->status[0] == "OK"){
                    $latitude= (string) $xml_resp->result[0]->geometry[0]->location[0]->lat;
                    $longitude=(string) $xml_resp->result[0]->geometry[0]->location[0]->lng;
                    
                    if($_POST["degree"]=="fahrenheit")
                    {
                        $unit="us";
                    }
                    else
                    {
                        $unit="si";
                    }
                   
                    $weather="https://api.forecast.io/forecast/". $api . "/". $latitude ."," .$longitude. "?units=". $unit ."&exclude=flags";
                    $tab = json_decode(file_get_contents($weather), true);
                    $pic=$tab["currently"]["icon"];
                    $icon=$tab["currently"]["icon"];
                    $wea=$tab["currently"];
                    date_default_timezone_set("America/New_York");
                
                  if($wea['icon']=="partly-cloudy-night")
                    {
                        $icon="http://cs-server.usc.edu:45678/hw/hw6/images/cloud_night.png";
                    }
                    else if($wea['icon']=="partly-cloudy-day")
                    {
                        $icon="http://cs-server.usc.edu:45678/hw/hw6/images/cloud_day.png";
                    }
                    else if($wea['icon']=="clear-day")
                    {
                        $icon="http://cs-server.usc.edu:45678/hw/hw6/images/clear.png";
                    }
                    else if($wea['icon']=="clear-night")
                    {
                        $icon="http://cs-server.usc.edu:45678/hw/hw6/images/clear_night.png";
                    }
                    else if($wea['icon']=="rain")
                    {
                        $icon="http://cs-server.usc.edu:45678/hw/hw6/images/rain.png";
                    }
                    else if($wea['icon']=="snow")
                    {
                        $icon="http://cs-server.usc.edu:45678/hw/hw6/images/snow.png";
                    }
                    else if($wea['icon']=="sleet")
                    {
                        $icon="http://cs-server.usc.edu:45678/hw/hw6/images/sleet.png";
                    }
                    else if($wea['icon']=="wind")
                    {
                        $icon="http://cs-server.usc.edu:45678/hw/hw6/images/wind.png";
                    }
                    else if($wea['icon']=="fog")
                    {
                        $icon="http://cs-server.usc.edu:45678/hw/hw6/images/fog.png";
                    }
                    else if($wea['icon']=="cloudy")
                    {
                        $icon="http://cs-server.usc.edu:45678/hw/hw6/images/cloudy.png";
                    } 
                    else 
                    {
                      $icon="";
                    }
                    

                        if($unit=="si")
                        {
                          $precipIntensity=($tab["currently"]["precipIntensity"]/24.5);
                        }
                        else
                        {
                          $precipIntensity=$tab["currently"]["precipIntensity"];
                        }
                    
                        if($precipIntensity==0 && $precipIntensity<0.002)
                        {
                          $precipIntensityvalue="None";
                        }
                        else if ($precipIntensity>=0.02 && $precipIntensity<0.017)
                         {
                           $precipIntensityvalue="Very Light";
                        }
                        else if ($precipIntensity>=0.017 && $precipIntensity<0.1)
                         {
                           $precipIntensityvalue="Light";
                        }
                        else if ($precipIntensity>=0.1&& $precipIntensity<0.4)
                         {
                           $precipIntensityvalue="Moderate";
                        }
                        else if ($precipIntensity>=0.4)
                         {
                           $precipIntensityvalue="Heavy";
                        }
                    
                    $summary = $tab["currently"]["summary"];
                    $temp = $tab["currently"]["temperature"];
                    $rain = $tab["currently"]["precipProbability"] * 100 . "%";
                    $wind = ceil($tab["currently"]["windSpeed"] );
                    $dew = floor($tab["currently"]["dewPoint"]);
                    $humidity = $tab["currently"]["humidity"] * 100 . "%";
                    $visibility = round($tab["currently"]["visibility"]);
                    $sunrise = $tab["daily"]["data"][0]["sunriseTime"];
                    $sunset= $tab["daily"]["data"][0]["sunsetTime"];
                    $sunrisetime = date('h:i A', $sunrise);
                    $sunsettime = date('h:i A', $sunset);
                    
                }
                else {
                echo "<script type='text/javascript'>alert('Incorrect data filled by user');</script>";
            }
                }
            
        }
    ?>
<div >
<h1 style="text-align:center">Forecast Search</h1>   
    <form id="forecast" name="forecast" onsubmit="return validate()" action="<?php $_SERVER['PHP_SELF']; ?>" method="post">
        <table id="x" frame="box" style="text-align:left ; padding:15px" align="center" >
        <tr>
            <td><label>Street Address:*</label></td>
            <td><input type="text" name="stradd" id="address" value="<?php echo isset($_POST['stradd']) ? $_POST['stradd']: ''; ?>"></td>
        </tr>
        
        <tr>
            <td><label>City:*</label></td> 
            <td><input type="text"  name="city" id="city" value="<?php echo isset($_POST['city']) ? $_POST['city']: ''; ?>" ></td>
        </tr>
        <tr>
            <td><label >State:*</label></td> 
                <td><select name="state" id="state">
                        <option value="x" >Select Your State...</option>
  <option value="AL" <?php if (isset($_POST['state']) && ($_POST['state']=='AL')) echo 'selected="selected"'; ?>>Alabama</option
  <option value="AK" <?php if (isset($_POST['state']) && ($_POST['state']=='AK')) echo 'selected="selected"'; ?>>Alaska</option>
  <option value="AZ" <?php if (isset($_POST['state']) && ($_POST['state']=='AZ')) echo 'selected="selected"'; ?>>Arizona</option>
  <option value="AR" <?php if (isset($_POST['state']) && ($_POST['state']=='AR')) echo 'selected="selected"'; ?>>Arkansas</option>
  <option value="CA" <?php if (isset($_POST['state']) && ($_POST['state']=='CA')) echo 'selected="selected"'; ?>>California</option>
  <option value="CO" <?php if (isset($_POST['state']) && ($_POST['state']=='CO')) echo 'selected="selected"'; ?>>Colorado</option>
  <option value="CT" <?php if (isset($_POST['state']) && ($_POST['state']=='CT')) echo 'selected="selected"'; ?>>Connecticut</option>
  <option value="DE" <?php if (isset($_POST['state']) && ($_POST['state']=='DE')) echo 'selected="selected"'; ?>>Delaware</option>
  <option value="DC" <?php if (isset($_POST['state']) && ($_POST['state']=='DC')) echo 'selected="selected"'; ?>>District Of Columbia</option>
  <option value="FL" <?php if (isset($_POST['state']) && ($_POST['state']=='FL')) echo 'selected="selected"'; ?>>Florida</option>
  <option value="GA" <?php if (isset($_POST['state']) && ($_POST['state']=='GA')) echo 'selected="selected"'; ?>>Georgia</option>
  <option value="HI" <?php if (isset($_POST['state']) && ($_POST['state']=='HI')) echo 'selected="selected"'; ?>>Hawaii</option>
  <option value="ID" <?php if (isset($_POST['state']) && ($_POST['state']=='ID')) echo 'selected="selected"'; ?>>Idaho</option>
  <option value="IL" <?php if (isset($_POST['state']) && ($_POST['state']=='IL')) echo 'selected="selected"'; ?>>Illinois</option>
  <option value="IN" <?php if (isset($_POST['state']) && ($_POST['state']=='IN')) echo 'selected="selected"'; ?>>Indiana</option>
  <option value="IA" <?php if (isset($_POST['state']) && ($_POST['state']=='IA')) echo 'selected="selected"'; ?>>Iowa</option>
  <option value="KS" <?php if (isset($_POST['state']) && ($_POST['state']=='KS')) echo 'selected="selected"'; ?>>Kansas</option>
  <option value="KY" <?php if (isset($_POST['state']) && ($_POST['state']=='KY')) echo 'selected="selected"'; ?>>Kentucky</option>
  <option value="LA" <?php if (isset($_POST['state']) && ($_POST['state']=='LA')) echo 'selected="selected"'; ?>>Louisiana</option>
  <option value="ME" <?php if (isset($_POST['state']) && ($_POST['state']=='ME')) echo 'selected="selected"'; ?>>Maine</option>
  <option value="MD" <?php if (isset($_POST['state']) && ($_POST['state']=='MC')) echo 'selected="selected"'; ?>>Maryland</option>
  <option value="MA" <?php if (isset($_POST['state']) && ($_POST['state']=='MA')) echo 'selected="selected"'; ?>>Massachusetts</option>
  <option value="MI" <?php if (isset($_POST['state']) && ($_POST['state']=='MI')) echo 'selected="selected"'; ?>>Michigan</option>
  <option value="MN" <?php if (isset($_POST['state']) && ($_POST['state']=='MN')) echo 'selected="selected"'; ?>>Minnesota</option>
  <option value="MS" <?php if (isset($_POST['state']) && ($_POST['state']=='MS')) echo 'selected="selected"'; ?>>Mississippi</option>
  <option value="MO" <?php if (isset($_POST['state']) && ($_POST['state']=='MO')) echo 'selected="selected"'; ?>>Missouri</option>
  <option value="MT" <?php if (isset($_POST['state']) && ($_POST['state']=='MT')) echo 'selected="selected"'; ?>>Montana</option>
  <option value="NE" <?php if (isset($_POST['state']) && ($_POST['state']=='NE')) echo 'selected="selected"'; ?>>Nebraska</option>
  <option value="NV" <?php if (isset($_POST['state']) && ($_POST['state']=='NV')) echo 'selected="selected"'; ?>>Nevada</option>
  <option value="NH" <?php if (isset($_POST['state']) && ($_POST['state']=='NH')) echo 'selected="selected"'; ?>>New Hampshire</option>
  <option value="NJ" <?php if (isset($_POST['state']) && ($_POST['state']=='NJ')) echo 'selected="selected"'; ?>>New Jersey</option>
  <option value="NM" <?php if (isset($_POST['state']) && ($_POST['state']=='NM')) echo 'selected="selected"'; ?>>New Mexico</option>
  <option value="NY" <?php if (isset($_POST['state']) && ($_POST['state']=='NY')) echo 'selected="selected"'; ?>>New York</option>
  <option value="NC" <?php if (isset($_POST['state']) && ($_POST['state']=='NC')) echo 'selected="selected"'; ?>>North Carolina</option>
  <option value="ND" <?php if (isset($_POST['state']) && ($_POST['state']=='ND')) echo 'selected="selected"'; ?>>North Dakota</option>
  <option value="OH" <?php if (isset($_POST['state']) && ($_POST['state']=='OH')) echo 'selected="selected"'; ?>>Ohio</option>
  <option value="OK" <?php if (isset($_POST['state']) && ($_POST['state']=='OK')) echo 'selected="selected"'; ?>>Oklahoma</option>
  <option value="OR" <?php if (isset($_POST['state']) && ($_POST['state']=='OR')) echo 'selected="selected"'; ?>>Oregon</option>
  <option value="PA" <?php if (isset($_POST['state']) && ($_POST['state']=='PA')) echo 'selected="selected"'; ?>>Pennsylvania</option>
  <option value="RI" <?php if (isset($_POST['state']) && ($_POST['state']=='RI')) echo 'selected="selected"'; ?>>Rhode Island</option>
  <option value="SC" <?php if (isset($_POST['state']) && ($_POST['state']=='SC')) echo 'selected="selected"'; ?>>South Carolina</option>
  <option value="SD" <?php if (isset($_POST['state']) && ($_POST['state']=='SD')) echo 'selected="selected"'; ?>>South Dakota</option>
  <option value="TN" <?php if (isset($_POST['state']) && ($_POST['state']=='TN')) echo 'selected="selected"'; ?>>Tennessee</option>
  <option value="TX" <?php if (isset($_POST['state']) && ($_POST['state']=='TX')) echo 'selected="selected"'; ?>>Texas</option>
  <option value="UT" <?php if (isset($_POST['state']) && ($_POST['state']=='UT')) echo 'selected="selected"'; ?> >Utah</option>
  <option value="VT" <?php if (isset($_POST['state']) && ($_POST['state']=='VT')) echo 'selected="selected"'; ?>>Vermont</option>
  <option value="VA" <?php if (isset($_POST['state']) && ($_POST['state']=='VA')) echo 'selected="selected"'; ?>>Virginia</option>
  <option value="WA" <?php if (isset($_POST['state']) && ($_POST['state']=='WA')) echo 'selected="selected"'; ?>>Washington</option>
  <option value="WV" <?php if (isset($_POST['state']) && ($_POST['state']=='WV')) echo 'selected="selected"'; ?>>West Virginia</option>
  <option value="WI" <?php if (isset($_POST['state']) && ($_POST['state']=='WI')) echo 'selected="selected"'; ?>>Wisconsin</option>
  <option value="WY" <?php if (isset($_POST['state']) && ($_POST['state']=='WY')) echo 'selected="selected"'; ?>>Wyoming</option>

                        </select>
                    </td>
                </tr>
                <tr>
                    <td><label >Degree:*</label></td> 
                    <td><input id="degf" type="radio" name="degree" value="fahrenheit" checked <?php if (isset($_POST['degree']) && $_POST['degree']=="fahernheit") echo "checked";?>>Fahrenheit<input id="degc" type="radio" name="degree" value="celsius" <?php if (isset($_POST['degree']) && $_POST['degree']=="celsius") echo "checked";?> >Celsius</td>
                </tr>
                <tr> 
                    <td></td>
                    <td ><input id="submit" type="submit" name="submit" value="Search">  
                    <input id="clear" type="button" name="clear" value="Clear" onclick="erase()" ></td>
                </tr>
                <tr>
                    <td>*-<i>Mandatory fields.</i></td>
                </tr>
                <tr>
                    
                    <td colspan="3" style="text-align:center">
                    <a href="http://forecast.io">Powered by Forecast.io</a>
                    </td>
                </tr>
            
        
        </table>
</form>
    </div>
    <?php 
    if(isset($xml_resp) and $xml_resp->status[0] == "OK") {
        if(!isset($_POST["submit"])) {
            echo '<div id="display" style=" display:none;" >';
        }
        else {
            echo '<div id="display" style=" display:block">';
        }
                echo '<table id="d" frame="box" style="text-align: center ; padding:7px 90px" align="center">
                    <col width="150">
                    <col width="150">
                    <tr>
                        <td colspan="2"><strong>'. $summary .'</strong></td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <strong>';
                                if($unit == "us") {
                                    echo floor($temp) . '&#8457';
                                }
                                else {
                                    echo floor($temp) . '&#8451';
                                }
                
                    echo    '</strong>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2"><img src=' . $icon . ' '. 'title='. $summary . '> </td>
                    </tr>
                    <tr>
                        <td>Precipitation:</td>
                        <td>';
                            if(isset($precipIntensityvalue)) 
                                    {
                                        echo $precipIntensityvalue;
                                        } 
                        echo '</td>
                    </tr>
                    <tr>
                        <td>Chance of rain:</td>
                        <td>'. $rain. '
                        </td>
                    </tr>
                    <tr>
                        <td>Wind Speed:</td>
                        <td>';
                            if($unit=="si")
                                    {echo $wind." mps"; }
                                else
                                { echo $wind. " mph" ;}
                            echo '
                        </td>
                    </tr>
                    <tr>
                        <td>Dew Point:</td>
                        <td>'; if($unit == "us") {
                                    echo floor($dew) . '&#8457';
                                }
                                else {
                                    echo floor($dew) . '&#8451';
                                }
                        echo '</td>
                    </tr>
                    <tr>
                        <td>Humidity:</td>
                        <td>'. $humidity .
                        '</td>
                    </tr>
                    <tr>
                        <td>Visibility:</td>
                        <td>';
                            if($unit=="si")
                                    {echo $visibility." km"; }
                                else
                                { echo $visibility. " mi" ;}
                            
                        echo '</td>
                    </tr>
                    <tr>
                        <td>Sunrise:</td>
                        <td>' . $sunrisetime .
                        '</td>
                    </tr>
                    <tr>
                        <td>Sunset:</td>
                        <td>'. $sunsettime .
                        '</td>
                    </tr>
                </table>
            </div>';
    }
    ?>
</body>
</html>

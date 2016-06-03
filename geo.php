<?php
$request = json_decode(file_get_contents('php://input'), true);

/**
 * Build Request-Arrays
 */
$r_google = array();
foreach($request as $wifi) {

  //Google Geolocation API
  $r_google[] = array(
    'macAddress' => $wifi['bssid'],
    'signalStrength' => $wifi['signal'],
    'channel' => $wifi['channel'],
  );

  // Mozilla Location Service
  $r_mozilla[] = array(
    'macAddress' => $wifi['bssid'],
    'signalStrength' => $wifi['signal'],
    'channel' => $wifi['channel'],
  );

}

/**
 * Fetch Answers
 */
$a = array();

// Google Geolocation API
$a_google = json_decode(file_get_contents('https://www.googleapis.com/geolocation/v1/geolocate?key=XXX', false, stream_context_create(array(
  'http' => array(
    'header' => "Content-type: application/json\r\n",
    'method' => 'POST',
    'content' => json_encode(array('wifiAccessPoints' => $r_google)),
  )
))), true);
if(isset($a_google['accuracy']) && $a_google['accuracy'] < 10000) $a[] = array(
  'latitude' => $a_google['location']['lat'],
  'longitude' => $a_google['location']['lng'],
  'accuracy' => $a_google['accuracy'],
);

// Mozilla Geolocation Service
$a_mozilla = json_decode(file_get_contents('https://location.services.mozilla.com/v1/geolocate?key=test', false, stream_context_create(array(
  'http' => array(
    'header' => "Content-type: application/json\r\n",
    'method' => 'POST',
    'content' => json_encode(array('wifiAccessPoints' => $r_mozilla)),
  )
))), true);
if(isset($a_mozilla['accuracy']) && $a_mozilla['accuracy'] < 10000) $a[] = array(
  'latitude' => $a_mozilla['location']['lat'],
  'longitude' => $a_mozilla['location']['lng'],
  'accuracy' => $a_mozilla['accuracy'],
);

/**
 * Build Answer for the Router
 */
$index = -1;
foreach($a as $key => $geo) {
  if($index < 0) $index = $key;
  if($a[$index]['accuracy'] > $geo['accuracy']) $index = $key;
}
if($index == -1) echo(json_encode(array('error' => 'No Geolocation found')));
else echo(json_encode($a[$index]));
?>

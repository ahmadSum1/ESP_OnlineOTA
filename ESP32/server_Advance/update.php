<?php

header('Content-type: text/plain; charset=utf8', true);

//	Function to test existance of header value
function check_header($name, $value = false)
{
    if (!isset($_SERVER[$name])) {
        return false;
    }
    if ($value && $_SERVER[$name] != $value) {
        return false;
    }
    return true;
}

//	Function to send file to ESP8266
function sendFile($path)
{
    header($_SERVER["SERVER_PROTOCOL"] . ' 200 OK', true, 200);
    header('Content-Type: application/octet-stream', true);
    header('Content-Disposition: attachment; filename=' . basename($path));
    header('Content-Length: ' . filesize($path), true);
    header('x-MD5: ' . md5_file($path), true);
    readfile($path);
}

//esp32 header sample
/*User-Agent: ESP32-http-Update
X-Esp32-Ap-Mac: 24:0A:C4:A3:15:3D
X-Esp32-Chip-Size: 4194304
X-Esp32-Free-Space: 1310720
X-Esp32-Mode: sketch
X-Esp32-Sdk-Version: v3.2-18-g977854975
X-Esp32-Sketch-Md5: 55ceae8bfcc2c19e48a2c1005afc57af
X-Esp32-Sketch-Sha256: F23CF9FA59D32774DC8AFF17398C9064B7B6C54EE0B56881FF0AF2783C3AD745
X-Esp32-Sketch-Size: 719968
X-Esp32-Sta-Mac: 24:0A:C4:A3:15:3C
*/

//esp8266 header sample
/*
User-Agent: ESP8266-http-Update
X-Esp8266-Ap-Mac: CE:50:E3:17:C4:11
X-Esp8266-Chip-Size: 4194304
X-Esp8266-Free-Space: 3874816
X-Esp8266-Mode: sketch
X-Esp8266-Sdk-Version: 2.2.1(cfd48f3)
X-Esp8266-Sketch-Md5: 789996d857d880c882d6f3cb6bffdf34
X-Esp8266-Sketch-Size: 296800
X-Esp8266-Sta-Mac: CC:50:E3:17:C4:11
*/


//	Test for correct user agent (ESP only)
if (!check_header('HTTP_USER_AGENT', 'ESP8266-http-Update') && !check_header('HTTP_USER_AGENT', 'ESP32-http-Update')) {
    header($_SERVER["SERVER_PROTOCOL"] . ' 403 Forbidden', true, 403);
    echo "Invalid USER AGENT. This is only for ESP_XX updater!\n";
    exit();
}

//	Confirm existance of HTTP headers from ESP8266 or ESP32
if (
    (   check_header('HTTP_X_ESP8266_STA_MAC')     &&
        check_header('HTTP_X_ESP8266_AP_MAC')      &&
        check_header('HTTP_X_ESP8266_FREE_SPACE')  &&
        check_header('HTTP_X_ESP8266_SKETCH_SIZE') &&
        check_header('HTTP_X_ESP8266_SKETCH_MD5')  &&
        check_header('HTTP_X_ESP8266_CHIP_SIZE')   &&
        check_header('HTTP_X_ESP8266_SDK_VERSION') &&
        check_header('HTTP_USER_AGENT', 'ESP8266-http-Update')
    ) || 
        
    (   check_header('HTTP_X_ESP32_STA_MAC')       &&
        check_header('HTTP_X_ESP32_AP_MAC')        &&
        check_header('HTTP_X_ESP32_FREE_SPACE')    &&
        check_header('HTTP_X_ESP32_SKETCH_SIZE')   &&
        check_header('HTTP_X_ESP32_SKETCH_MD5')    &&
        check_header('HTTP_X_ESP32_CHIP_SIZE')     &&
        check_header('HTTP_X_ESP32_SDK_VERSION')   &&
        check_header('HTTP_USER_AGENT', 'ESP32-http-Update')
    ) 
) { 
    //headers checking, just letting anyone(esp8266 or esp32) update

} 

else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 403 Forbidden - Missing header value.', true, 403);
    exit();
}

//	Database associating ESP8266/ESP32 sta_MAC address
// $db = array(
//     "CC:50:E3:17:C4:11" => "WemosD1_1",
//     "24:0A:C4:A3:15:3C" => "ESP32_2"
// );

//	Confirm ESP MAC addres in database
// if (!isset($db[$_SERVER['HTTP_X_ESP8266_STA_MAC']])) {
//     header($_SERVER["SERVER_PROTOCOL"] . ' 500 ESP MAC not configured for updates', true, 500);
//     exit();
// }

//	Build filename
$localBinary_ESP8266 = "./latest/ESP8266-http-Update.bin";
$localBinary_ESP32 = "./latest/ESP32-http-Update.bin";

// Check if version has been set and does not match, if not, check if
// MD5 hash between local binary and ESP8266 binary do not match if not.
// then no update has been found.

if (($_SERVER["HTTP_X_ESP8266_SKETCH_MD5"] != md5_file($localBinary_ESP8266))
)                        // is there a mismatch between MD5 ... then
{
    sendFile($localBinary_ESP8266);
}
else if (($_SERVER["HTTP_X_ESP32_SKETCH_MD5"] != md5_file($localBinary_ESP32))
)                        // is there a mismatch between MD5 ... then
{
    sendFile($localBinary_ESP32);
}
 else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 304 Not Modified - no update required', true, 304);
}

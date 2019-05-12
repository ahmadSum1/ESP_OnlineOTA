/**
   httpUpdate.ino

    Created on: 27.11.2015

*/

#include <Arduino.h>
#include <WiFi.h>
#include <WiFiMulti.h>
#include <HTTPClient.h>
#include <HTTPUpdate.h>

WiFiMulti WiFiMulti;


#define firmware_version "v0.0.1"
#define dev_ID "test_01"

String update_server = "http://espota.ahmadsum1.dx.am";



#define ssid "test wifi"
#define password  "12345678"

void setup() {

  Serial.begin(115200);
  // Serial.setDebugOutput(true);

  Serial.println("\n\n\n");

  WiFi.mode(WIFI_STA);
  WiFiMulti.addAP(ssid, password);
  for (uint8_t t = 4; t > 0; t--) {
    Serial.printf("[SETUP] WAIT %d...\n", t);
    Serial.flush();
    delay(1000);
  }


}

void loop() {
  Serial.println(firmware_version);
  OTA_update();

}

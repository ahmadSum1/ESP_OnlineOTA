void OTA_update() {
  // wait for WiFi connection
  if ((WiFiMulti.run() == WL_CONNECTED)) {




    WiFiClient client;

    // The line below is optional. It can be used to blink the LED on the board during flashing
    // The LED will be on during download of one buffer of data from the network. The LED will
    // be off during writing that buffer to flash
    // On a good connection the LED should flash regularly. On a bad connection the LED will be
    // on much longer than it will be off. Other pins than LED_BUILTIN may be used. The second
    // value is used to put the LED on. If the LED is on with HIGH, that value should be passed
    httpUpdate.setLedPin(LED_BUILTIN, LOW);

    /*
        API:

        t_httpUpdate_return update(WiFiClient& client, const String& url, const String& currentVersion = "");

        t_httpUpdate_return update(WiFiClient& client, const String& host, uint16_t port, const String& uri = "/", const String& currentVersion = "");

        t_httpUpdate_return updateSpiffs(WiFiClient& client, const String& url, const String& currentVersion = "");
    */

    //t_httpUpdate_return ret = httpUpdate.update(client, "http://server/file.bin");
    // Or:
    //t_httpUpdate_return ret = httpUpdate.update(client, server, 80, filePath);
    //t_httpUpdate_return ret = httpUpdate.update("192.168.0.2", 80, "/esp/update/arduino.php", "optional current version string here");

    t_httpUpdate_return ret = httpUpdate.update(client, update_server, 80, "/ESP32/update.php", ("ver=" + firmware_version + "&dev=" + dev_ID) );

    switch (ret) {
      case HTTP_UPDATE_FAILED:
        Serial.printf("HTTP_UPDATE_FAILED Error (%d): %s\n", httpUpdate.getLastError(), httpUpdate.getLastErrorString().c_str());
        break;

      case HTTP_UPDATE_NO_UPDATES:
        Serial.println("HTTP_UPDATE_NO_UPDATES");
        break;

      case HTTP_UPDATE_OK:
        Serial.println("HTTP_UPDATE_OK");
        break;
    }
  }
}

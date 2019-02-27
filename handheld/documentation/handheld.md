# QPACK Handheld

Due to the nature of this project the app relies heavily on a PCB attached to the phone with a microUSB cable, acting as a USB Host over OTG (On the go).
The device is basically an ATmega, a serial communication chip (to make it programmable by the Arduino Suite), and attachment points for:
* 3 buttons (2 for the trigger, 1 thumbbutton)
* 1 Thermocouple (Probe)
* IR sensor
* Barcode Scanner
* Laser pointer (to indicate where the IR sensor is pointing)
It also incorporates a charging system for a 18650 Lithium cell and and a socket for a microUSB charger.
Once the cable is attached, the PCB charges both the attached phone and its own battery.
The PCB was developed by Kean Maizels for ACS.

## Versions

### Version 3.2

Schematics are in `QPack R3.2.pdf`. The following mail should explain some specifics in more detail

> *From:* Kean Maizels <Kean@kean.com.au <mailto:Kean@kean.com.au>>
> *Date:* 4 January 2019 at 11:21:09 am AEDT
> *To:* "Colin Atkinson (colin.p.atkinson@gmail.com)
> *Subject:* *QPack 3.2 schematic and OTG charging*
>
> Hi Colin,
>
> Attached is the schematic for the QPack R3.2 main PCB.
>
> Note there was a last minute change where I inserted a Schottky diode between 
> VCHG and drain of Q1, as per the pic below (`R3.2img001.png`) (from page 3 of the schema).  This 
> was needed as it turned out that the microcontroller could not reliably tell 
> when the charger was disconnected as there would be sufficient voltage feeding 
> back into the voltage divider for charge power sensing through Q1 & Q2 (both 
> of which are on when charging is enabled).
>
> As mentioned, the connection of an external charger energises relay K1 which 
> stops the second trigger switch SW2 from activating the USB_ID signal while 
> charging is also active.  SW2 is intended to “wake up” OTG host mode from 
> sleep state. The ATmega firmware detects the presence of the external charge 
> power and de-asserts USB_ID and  enables charging by asserting CHARGE_EN.
>
> The original design using the IOIO allowed the USB “serial” connection in host 
> or device mode, but with the “Arduino” style hardware we can only connect the 
> USB serial while in OTG host mode.  If the power supply circuitry all seems a 
> little complicated, it was primarily because of the limitations of USB OTG and 
> charging.
>
> Anyway, I think the hardware will actually allow what you want – i.e. charging 
> the secondary battery without disconnecting Android.  This is because the 
> ATmega firmware is responsible for controlling the CHARGE_EN and USB_ID 
> signals. Normally it switches these immediately to start charging, but we 
> could query the app to decide if that is the right thing to do.  The charging 
> of the secondary battery is always enabled as the external power connection 
> into U6 charger chip is prior to the charge enable circuitry.
>
> Currently that charge circuit is limited to 100mA, so it is pretty slow, but 
> that is just a factor of adjusting R22 (within thermal limits) and making sure 
> the external charger can supply enough current for both the secondary battery 
> and phone charging simultaneously.  I think most phones are smart enough to 
> reduce charge rate if the incoming charge voltage drops too much.
>
> Now, I just remembered that there is also an extension to the USB spec that 
> allows USB host mode and simultaneous charging.  I don’t know if the cheap 
> Samsung device supports it (requires both hardware and software support), so 
> we can try this out and if it is supported then we can almost certainly modify 
> our circuit to support this.  The secret is to not ground the USB_ID pin for 
> OTG host mode, but instead use a resistor to ground.  A quick Google says it 
> should be either 36.5k or 124k – will need to read the actual spec to check 
> which is correct.  See https://electronics.stackexchange.com/a/43568
>
> Alternatively it may be possible to create a custom Android image to work 
> around some of these problem, but I don’t know if we want to go down that path…
>
> Finally, as discussed there are almost certainly things we can do in the 
> software and/or firmware to further reduce power draw from the secondary 
> battery.  This is very dependent on interaction with the application, but I’m 
> happy to work together with you on this.
>
> Cheers,
>
> *Kean Maizels*
>
> Kean Electronics - IT Consulting & Embedded Systems Design
>
> Ph: 02 9457-0346   Mob: 0414 245 326

### Earlier versions
Previous attempts used an IOIO chip and Bluetooth for communication. Some references to these will still be visible in parts of code and documentation.


## Basic Cordova setup
The best place to start will be with Kean's test    
code. Here are his instructions:    
    
1. Cordova build environment for sample code (qpacktest3)    
    
2. ```
    cd \src\cordova
    ```
    (or wherever your Cordova code lives)    
    ```bash
    cordova create qpacktest3
    cd qpacktest3
    ```

3. Add the necessary plugins and platforms
   * Android
    ```    
    cordova platform add android@6.2.0    
    ```
   * Cordovarduino (for serial connection to Q-Pack handheld)
    ```
    cordova plugin add https://github.com/xseignard/cordovarduino    
    ```
    (Kean originally send this command:
    ```
    plugman install --platform android --project ./platforms/android --plugin https://github.com/xseignard/cordovarduino
    ```    
    It does not work due to `Cannot read property 'fail' of undefined`. Plugman seems to have been halfway deprecated.
  )   
   
4. Get the [test app](https://github.com/elfalko/qpacktest3)     
    
5. Build the app
    ```
    cordova build
    ```
   You should now be able to find the app under `<project_root>/platforms/android/build/outputs/apk/android-debug.apk`    
   There are two ways to get the app on your phone
   #### Download it to the phone
   The best practice for the Q-Pack as you don't have to open the casing to attach a USB cable. Run a simple http server on your PC, and download it from there. 
   For example use the one provided by the node.js package [http-server](https://www.npmjs.com/package/http-server): 
   1. Run it with `'http-server /opt/lampp/htdocs/acs/app/platforms/android/build/outputs/apk/`
   2. With the phone's browser, connect to the ip shown in the terminal window where you ran the server.
   3. Select the apk
   4. Install it
   
   #### Build and flash
   This requires a connection to your phone, either via USB or if [the phone is started with an open tcpip port over adb](https://developer.android.com/studio/command-line/adb#wireless "Android documentation") (note that that connection gets terminated after installing, and would require the USB connection to start in the first place)
   ```
   cordova run android
   ```    
   Troubleshooting:
   ```
   no target specified -> switch to emulator    
    exit code 2
   ```
   No attached android device is found.
   Make sure you have adb and the android development tools installed and in your path. This should help:
   ```
    cordova requirements    
      android target not installed - exit code 2
   ```
   Once you have them installed, you might need to reinstall the android platform
   ```
    cordova platforms remove android    
    cordova platforms add android   
   ``` 
   The emulator should now be running    
    
6. Installing
   1. The first time you try to install a homebrewn app, you need to allow apps from unsigned sources in the settings
   2. You need to give the app permission to use the connected USB device. This permission request pops up once you start the app and the USB device is connected

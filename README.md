# QAmC App
## Quality Assurance meets Chefs
This Repository contains all code necessary to run the [QAmC](http://qamc.co "QAmC's homepage") app.

## Folders
### toplevel / acs
This toplevel acts as the codebase for the __Q-Admin__ system, which is the server for the system.

### app
This contains the actual app. It is supposed to be used with the __cordova__ environment (called __phonegap__ before), enabling builds for android devices.

#### To setup

```bash
cd app
cordova prepare
```

#### To build an app

```bash
./go.sh #builds, bumps version number
```

#### To flash the app

##### via download
When developing for the Q-Pack, the USB port of the phone is not easily accessible. 
The easiest way to get the app onto the phone is therefore to download it.
To offer a place to download it from, a simple local webserver is suggested. 
_npm_ provides _http-server_. Install if necessary:
```bash
npm install -g http-server
```

Run in correct directory:
```bash
http-server app/platforms/android/build/outputs/apk/debug/
```

This will produce something like:
```bash
[f:app]$ http-server platforms/android/build/outputs/apk/debug/
Starting up http-server, serving platforms/android/build/outputs/apk/debug/
Available on:
  http://127.0.0.1:8080
  http://10.0.28.120:8080
Hit CTRL-C to stop the server
```
and you can open any of the given addresses on the handhelds browser and download the apk.
If you will download multiple times, consider setting a home screen shortcut to the ip to avoid repetitive typing,
You might need to change wifi to reach this if your developer machine is not in the QAmC network.
If you do, __make sure you switch WiFi back again or you will not reach Q-Admin anymore.__
If you build the app again, the server will automatically serve the new apk without any need for action from your side.

##### via USB
```bash
cordova run android
```

#### Install the app
After the download, a window usually pops up, showing the downloaded file.
Click it. The first time, it will tell you to change the settings allowing to install apps from unknown sources.
Do so.
Then, just click and install it.

#### www

Cordova allows you to write your app as you would write a website. It is then turned into an app by the build system.
The main code resides in the www folder, as it would with a website.

### udp
The base station __Q-Admin__ has to be reached by the handheld devices, the __Q-Packs__.
It sends its IP address out as a UDP Broadcast to let them know it.
This folder contains the necessary files

### monarch and monarch_9417
Printer specific code. The printer prints barcode labels. _monarch_9417_ is the currently used printer.

## Troubleshooting

### Q-Pack does not connect
* Check that Q-Pack and Q-Admin are in the same network
* Log into admin user and check that ip matches Q-Admin IP

### Q-Pack keeps beeping
* Recharge. The battery is probably flat

## Usage tips from hello world template

#### PhoneGap CLI

The hello-world template is the default when you create a new application using the [phonegap-cli][phonegap-cli-url].

    phonegap create my-app

Create an app using this template specifically:

    phonegap create my-app --template hello-world

To see a list of other available PhoneGap templates:

    phonegap template list

## [config.xml][config-xml]

#### android-minSdkVersion (Android only)

Minimum SDK version supported on the target device. Maximum version is blank by default.

This template sets the minimum to `14`.

    <preference name="android-minSdkVersion" value="14" />

#### &lt;access ...&gt; (All)

This template defaults to wide open access.

    <access origin="*" />

It is strongly encouraged that you restrict access to external resources in your application before releasing to production.

For more information on whitelist configuration, see the [Cordova Whitelist Guide][cordova-whitelist-guide] and the [Cordova Whitelist Plugin documentation][cordova-plugin-whitelist]

## [www/index.html][index-html]

#### Content Security Policy (CSP)

The default CSP is similarly open:

    <meta http-equiv="Content-Security-Policy" content="default-src * 'unsafe-inline'; style-src 'self' 'unsafe-inline'; media-src *" />

Much like the access tag above, you are strongly encouraged to use a more restrictive CSP in production.

A good starting point declaration might be:

    <meta http-equiv="Content-Security-Policy" content="default-src 'self' data: gap: 'unsafe-inline' https://ssl.gstatic.com; style-src 'self' 'unsafe-inline'; media-src *" />

For more information on the Content Security Policy, see the [section on CSP in the Cordova Whitelist Plugin documentation][cordova-plugin-whitelist-csp].

Another good resource for generating a good CSP declaration is [CSP is Awesome][csp-is-awesome]


[phonegap-cli-url]: http://github.com/phonegap/phonegap-cli
[cordova-app]: http://github.com/apache/cordova-app-hello-world
[bithound-img]: https://www.bithound.io/github/phonegap/phonegap-app-hello-world/badges/score.svg
[bithound-url]: https://www.bithound.io/github/phonegap/phonegap-app-hello-world
[config-xml]: https://github.com/phonegap/phonegap-template-hello-world/blob/master/config.xml
[index-html]: https://github.com/phonegap/phonegap-template-hello-world/blob/master/www/index.html
[cordova-whitelist-guide]: https://cordova.apache.org/docs/en/dev/guide/appdev/whitelist/index.html
[cordova-plugin-whitelist]: http://cordova.apache.org/docs/en/latest/reference/cordova-plugin-whitelist
[cordova-plugin-whitelist-csp]: http://cordova.apache.org/docs/en/latest/reference/cordova-plugin-whitelist#content-security-policy
[csp-is-awesome]: http://cspisawesome.com

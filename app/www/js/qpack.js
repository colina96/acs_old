// document.getElementById("startbutton").addEventListener("touchstart", qpack_init, false);

const QPACK_IOIO_DELAY          = 50;   // ms interval for checking IOIO events
const QPACK_BARCODE_DELAY       = 2500; // ms time for barcode read if trigger released
const QPACK_TEMP_DELAY          = 500;  // ms time for repeated temperature reading

const QPACK_I2C0_SDA            = 4;    // IOIO I2C Bus 0 SDA to MLX90614
const QPACK_I2C0_SCL            = 5;    // IOIO I2C Bus 0 SCL to MLX90614
const QPACK_BARCODE_RX          = 6;    // Barcode serial Rx, IOIO Uart Tx
const QPACK_BARCODE_TX          = 7;    // Barcode serial Tx, IOIO Uart Rx
const QPACK_SPI0_MISO           = 9;    // IOIO SPI Bus 0 MISO from MAX31855
const QPACK_SPI0_MOSI           = 10;   // IOIO SPI Bus 0 MOSI (unused)
const QPACK_SPI0_SCK            = 11;   // IOIO SPI Bus 0 CLK to MAX31855
const QPACK_SPI0_CS             = 12;   // IOIO SPI Bus 0 CS to MAX31855
const QPACK_BARCODE_TRIGGER     = 13;   // set true to scan, false to stop
const QPACK_BARCODE_READ        = 14;   // goes true on successful barcode read
const QPACK_TRIGGER_SWITCH      = 19;   // goes false on pulling trigger
const QPACK_THUMB_SWITCH        = 20;   // goes false on pressing thumb button
const QPACK_PROBE_SWITCH        = 21;   // goes false when probe folded in
const QPACK_LASER_ENABLE        = 22;   // set true to enable laser
const QPACK_BARCODE_DISABLE     = 23;   // set true to power off barcode scanner
const QPACK_SENSOR_DISABLE      = 24;   // set true to power off MLX90614, MAX31855
const QPACK_USBID_ENABLE        = 25;   // set true to pull USBID low (OTG mode)
const QPACK_BOOST_DISABLE       = 26;   // set true to disable charge boost converter
const QPACK_ANALOG_CHARGE       = 33;   // analog input measuring charge input
const QPACK_ANALOG_BATTERY      = 34;   // analog input measuring battery voltage
const QPACK_BEEPER_ENABLE       = 37;   // set true to actuate beeper (PWM?)
const QPACK_I2C0_RESPONSE       = 64;   // virtual pin for I2C Bus 0 responses
const QPACK_SPI0_RESPONSE       = 128;  // virtual pin for I2C Bus 0 responses

const MLX_ADDR   = 0x5a;    // Melexis I2C address

const ANALOG_MULT = 6.6;

var last_trigger = true;
var last_thumb   = true;
var last_probe   = true;

var scan_timer   = null;
var temp_timer   = null;

var barcode = "";

function status(t)
{
	console.log('status:' + t);
    document.getElementById('status').innerHTML += "<br>" + t;
}

function statusclear()
{
    document.getElementById('status').innerHTML = "";
}

function log(t)
{
	console.log(t);
    document.getElementById('log').innerHTML += "<br>" + t;
}

function logclear()
{
    document.getElementById('log').innerHTML = "";
}

function clear_logs()
{
	logclear();
	statusclear();
	TEST_trigger();
}
function qpack_init()
{
    logclear();
    qpack_start();
}

function qpack_start()
{
    qpack_stop();

    log("starting ioio");
    try {
        window.ioio.removeAllPinListeners();
        window.ioio.open({ // start ioio and open ports we need
            inputs: {
                analogue: [QPACK_ANALOG_CHARGE,QPACK_ANALOG_BATTERY],
                digital:[QPACK_BARCODE_READ,QPACK_TRIGGER_SWITCH,QPACK_THUMB_SWITCH,QPACK_PROBE_SWITCH],
            },
            outputs: {
                digital:[QPACK_BARCODE_TRIGGER,QPACK_LASER_ENABLE,QPACK_BARCODE_DISABLE,QPACK_SENSOR_DISABLE,
                         QPACK_USBID_ENABLE,QPACK_BOOST_DISABLE,QPACK_BEEPER_ENABLE]
            },
            uart: [
                { bus: 0, rxPin: QPACK_BARCODE_TX, txPin: QPACK_BARCODE_RX, baud: 9600, parity: 0, stopBits: 0 }
            ],
            twi: [
                { bus: 0, rate: 100 }
            ],
            spi: [
                { bus: 0, misoPin: QPACK_SPI0_MISO, mosiPin: QPACK_SPI0_MOSI, clkPin: QPACK_SPI0_SCK,
                  ssPin: QPACK_SPI0_CS, rate: 500 }
            ],
            delay: QPACK_IOIO_DELAY
        }, function() {
            log("IOIO OK");
            ioio_started = true;
            // success
            window.ioio.setDigitalOutput(QPACK_BARCODE_DISABLE, false);
            window.ioio.setDigitalOutput(QPACK_LASER_ENABLE, false);

            window.ioio.removeAllPinListeners();

            window.ioio.addPinListener(QPACK_BARCODE_TX, function(value) {
               // log("Uart Receive: " + value);
                qpack_cancelScan();
                for (var i = 0; i<value.length; i++) {
                    if (value[i]=='\r') {
                        log("Barcode: " + barcode);
                        process_barcode(barcode.substring(1, barcode.length - 1));
                        barcode = "";
                    }
                    else {
                        barcode += value[i];
                    }
                }
            });

            window.ioio.addPinListener(QPACK_I2C0_RESPONSE, function(value) {
                //log("I2C Response: " + value);
                //log("I2C Response len=" + value.length);
                var t = value[1] * 256 + value[0];
                t = (t * 2 - 27315) / 100;
                log("Non-contact temp: " + t.toFixed(1));
                temp_callback(t.toFixed(1));
            });

            window.ioio.addPinListener(QPACK_SPI0_RESPONSE, function(value) {
                //log("SPI Response: " + value);
                //log("SPI Response len=" + value.length);
                var t = value[0] * 64 + value[1] / 4
                if (value[0]>=128) {
                    t = 16384 - t;
                    t = (t * -25) / 100;
                }
                else {
                    t = (t * 25) / 100;
                }
                log("Probe temp: " + t.toFixed(1));
                temp_callback(t.toFixed(1));
            });

            window.ioio.addPinListener(QPACK_TRIGGER_SWITCH, function(value) {
            //	log("Trigger switch " + value + " - " + last_trigger);
                if (last_trigger==value) return;
                last_trigger = value;
                
                if (value==false) {
                    qpack_startScan();
                }
            });
            window.ioio.addPinListener(QPACK_THUMB_SWITCH, function(value) {
         //   	log("Thumb button " + value);
                if (last_thumb==value) return;
                last_thumb = value;
                
                if (value==false) {
                    qpack_readTemp()
                }
                else {
                    if (temp_timer!=null) clearTimeout(temp_timer);
                    window.ioio.setDigitalOutput(QPACK_LASER_ENABLE, false);
                }
            });
            window.ioio.addPinListener(QPACK_PROBE_SWITCH, function(value) {
                if (last_probe==value) return;
                last_probe = value;
                log("Probe detect " + value);
            });        

        }, function() {
            log("IOIO Fail");
            //fail
            return;
        }, function(vals) {
            //log("vals");
            statusclear();
            for(var i=0;i<vals.length;i++){
                var pin = vals[i];
                //for(var key in pin){console.log(key + ":" + pin[key]);}
                switch(pin.class){
                    case window.ioio.PIN_INPUT_DIGITAL:
                    case window.ioio.PIN_OUTPUT_DIGITAL:
                    //    status("Pin " + pin.pin + " " + pin.value);
                        break;
                    case window.ioio.PIN_INPUT_ANALOG:
                        var v = pin.value * ANALOG_MULT;
                    //    status("Pin " + pin.pin + " " + v.toFixed(1));
                        break;
                    case window.ioio.PIN_OUTPUT_PWM:
                    //    status("Pin " + pin.pin + " " + pin.value.toFixed(3));
                        break;
                }
            }
        });
    }
    catch (e) {
        log("IOIO Error");
        log(e);
    }
}

function qpack_stop()
{
    log("stopping ioio");
    window.ioio.close();
}

function qpack_pause()
{
    qpack_stop();
}

function qpack_resume()
{
    qpack_start();
}

function qpack_startScan()
{
    log("startScan");
    if (scan_timer!=null) clearTimeout(scan_timer);
    window.ioio.setDigitalOutput(QPACK_BARCODE_TRIGGER, true);
    scan_timer = setTimeout(function() { qpack_cancelScan(); }, QPACK_BARCODE_DELAY);
}

function qpack_cancelScan()
{
    log("cancelScan");
    if (scan_timer!=null) clearTimeout(scan_timer);
    scan_timer = null;
    window.ioio.setDigitalOutput(QPACK_BARCODE_TRIGGER, false);
}

function qpack_readTemp()
{
    if (temp_timer!=null) clearTimeout(temp_timer);
    if (last_probe) {
        log("readTemp probe");
        var req = [ 0xff, 0xff, 0xff, 0xff ];  // 4 dummy bytes for read
        window.ioio.writeReadSpi(0, 0, req);
    }
    else {
        log("readTemp non-contact");
        window.ioio.setDigitalOutput(QPACK_LASER_ENABLE, true);
        var req = [ 7 ];  // MLX object termp register - 2 byte word
        window.ioio.writeReadTwi(0, MLX_ADDR, req, 2);
    }
    if (last_thumb==false) {
        temp_timer = setTimeout(function() { qpack_readTemp(); }, QPACK_TEMP_DELAY);
    }
}
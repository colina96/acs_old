/*
 document.getElementById("startbutton").addEventListener("touchstart", qpack_init, false);
document.getElementById("sleepbutton").addEventListener("touchstart", qpack_sleep, false);
document.getElementById("readbarcode").addEventListener("touchstart", read_barcode, false);
* 
 */

var text = "";
var temp_timer = null;
var loglines = 0;

function status(t)
{
    document.getElementById('status').innerHTML += "<br>" + t;
}

function statusclear()
{
    document.getElementById('status').innerHTML = "";
}

function log(t)
{
	if (loglines++ > 100) {
		loglines = 0;
		document.getElementById('log').innerHTML  = '';
	}
	d=new Date();
	logtime = d.getHours()+":"+d.getMinutes()+":"+d.getSeconds();
    document.getElementById('log').innerHTML += "<br>[" + logtime + "] " + t;
    
}

function logclear()
{
    document.getElementById('log').innerHTML = "";
}

function qpack_init()
{
    logclear();
    qpack_start();
}

function qpack_start()
{
//    qpack_stop();

    log("starting qpack");
    try {
        serial.requestPermission(
            function success(data) {
            log("requestPermission success");
            serial.open({
                baudRate: 57600,
                dataBits: 8,
                stopBits: 1,
                parity: 0,
                dtr: true,
                rts: true,
                sleepOnPause: true
            }, function success() {
                log("serial open success");
                hide('log');
                serial.registerReadCallback(
                    function success(data2) {
                        var data = new Uint8Array(data2);
 //                       log("Uart Receive: " + data);
                        for (var i = 0; i<data.length; i++) {
                            if (data[i]==13||data[i]==10) {
                                //log(text);
                                if (text.length>0) process(text);
                                text = "";
                            }
                            else {
                                text += String.fromCharCode(data[i]);
                            }
                        }
                    },
                    function error() {
                        new Error("Failed to register read callback");
                    }
                );
                qpack_power(true);
            }, function error() {
            	show('log');
                log("serial open failed");
                //fail
                return;
            });
        }, function error() {
            log("requestPermission failed");
            show('log');
        });

    }
    catch (e) {
        log("serial error");
        log(e);
    }

}

function qpack_stop()
{
    qpack_power(false);
    log("closing serial");
    serial.close(function () {
        log("serial close success");
    }, function () {
        log("serial close failed");
    });
}

function get_internal_battery_voltage()
{
	if (typeof(serial) != 'undefined') serial.write('?');
}

function qpack_pause()
{
    qpack_stop();
}

function qpack_resume()
{
    qpack_start();
    // serial_write('?');
}

function qpack_power(on)
{
    //P/p turns on/off sensor power
    //B/b turns on/off barcode power
    if (on) serial.write("PB");
    else serial.write("pb");
}

function qpack_sleep()
{
    serial.write("Z");
}

function qpack_temp()
{
    //T reads probe
    //t reads IR
    serial.write("Tt");
    temp_timer = setTimeout(qpack_temp, 500);
}

function read_barcode()
{
        serial.write("BS");
}
function process(text)
{
    log(text);
    if (text=="+T") {
        //L turns on laser
        //S starts scan
    	if ((button_mode && button_mode == 'B') || user_id <= 0 ) {
    		serial.write("BS");
    	}
    	if((button_mode && button_mode == 'T')) { // read temperature
    		if (temp_probe) {
    			serial.write('LT');
    		}
    		else {
    			serial.write('Lt');
    		}
    	}
       // qpack_temp();
    }
    else if (text=="-T") {
        //l turns off laser
        //s ends scan
        serial.write("lsb");
        if (temp_timer!=null) clearTimeout(temp_timer);
        temp_timer = null;
    }
    else if (text=="+B") {
        //S starts scan
        // serial.write("S");
    	serial.write("?"); // read battery voltage
    }
    else if (text=="-B") {
        //s ends scan
        //serial.write("s");
    }
    else if (text=="+P") {
        //test low power consumption
        qpack_power(false);
    }
    else if (text=="-P") {
        //test low power consumption
        qpack_power(true);
    }
	else if (text.indexOf('[') >= 0 && text.indexOf(']') > 0) {
		var i = text.indexOf('[');
		var j = text.indexOf(']');
		var s = text.substring(i + 1, j);
		log("barcode " + s);
		serial.write('!')
		process_barcode(s);
		// serial_write('?');
	}
	else if (text.indexOf('T') == 0) {
		log ("IR temp reading " + text);
		var s = text.substring(1);
		temp_callback(s);
	}
	else if (text.indexOf('t') == 0) {
		log ("Probe temp reading " + text);
		var s = text.substring(1);
		temp_callback(s);
	}
	else if (text.indexOf('?') == 0) {
		var volts = text;
		if (text.indexOf(",")) {
			volts = text.substr(text.indexOf(",") + 1);
		}
		document.getElementById('battery_div').innerHTML = " Handset Battery: " + volts + " Volts";
	}else if (text=="zzz") {
	    log ("QPack sleeping");
	    show('log');
	    qpack_pause();
    }else if (text=='+C') {
	    log ("QPack charging");
	    show('log');
	    qpack_pause();
    }
}

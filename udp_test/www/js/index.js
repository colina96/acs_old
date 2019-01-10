/*
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 */
function log(m)
{
	document.getElementById('log_div').innerHTML += "<br>" + m;
	// console.log(msg);
}
function encode_utf8(s) {
	  return unescape(encodeURIComponent(s));
	}

function decode_utf8(s) {
  return decodeURIComponent(escape(s));
}

 function ab2str(buf) {
   var s = String.fromCharCode.apply(null, new Uint8Array(buf));
   return decode_utf8(decode_utf8(s))
 }

 function str2ab(str) {
	  var buf = new ArrayBuffer(str.length*2); // 2 bytes for each char
	  var bufView = new Uint16Array(buf);
	  for (var i=0, strLen=str.length; i<strLen; i++) {
	    bufView[i] = str.charCodeAt(i);
	  }
	  return buf;
	}

 
var udp = {
	port:1500,
	addr:"255.255.255.255",
	data:"Hello World!",
	socketId:-1,
    initialize: function() {
    	chrome.sockets.udp.create(function(createInfo) {
			log('UDP created socket ' + createInfo.socketId);
			this.socketId = createInfo.socketId;
			log("binding" + this.port);
			chrome.sockets.udp.bind(createInfo.socketId, '0.0.0.0', 1501, function(result) {
				log("bound socket result |" + result + "|");
				log('adding listener');
				chrome.sockets.udp.onReceive.addListener(function(msg)  //Listen for receiving messages
		        {
		                  log("got msg " + msg.socketId + " - " + ab2str(msg.data) + " from " + msg.remoteAddress);
		                  
		        });
				chrome.sockets.udp.onReceiveError.addListener(function(error)   //If error while receiving, do this
		        {
		        	log("got error " + error.socketId + " " + error.resultCode);
		        });
				
				log("done");
				this.send("raw test");
			});
		});
		
		
	
	},
	send: function(msg) {
		chrome.sockets.udp.send(this.socketId, str2ab(msg), this.addr, this.port, function(result) {
			if (result < 0) {
				log('send fail: ' + result);
				// chrome.sockets.udp.close(createInfo.socketId);
			} else {
				log('sendTo: success ' + port);
				// chrome.sockets.udp.close(createInfo.socketId);
			}
		});
	}
}

var udp_socket = -1;

function listen_in()
{
	var port = 1500;
	var addr = "255.255.255.255";
	var data = "Hello World!";
	log("starting 3"); 
	try {
		chrome.sockets.udp.create(function(createInfo) {
			log('created socket ' + createInfo.socketId);
			udp_socket = createInfo.socketId;
			chrome.sockets.udp.bind(createInfo.socketId, '0.0.0.0', 1500, function(result) {
				log("bound socket to 1500 result |" + result + "|");
				chrome.sockets.udp.onReceive.addListener(function(msg)  //Listen for receiving messages
		        {
		                  log("got msg " + msg.socketId + " - " + ab2str(msg.data) + " from " + msg.remoteAddress);
		                  
		        });
				chrome.sockets.udp.onReceiveError.addListener(function(error)   //If error while receiving, do this
		        {
		        	log("got error " + error.socketId + " " + error.resultCode);
		        });
				chrome.sockets.udp.send(createInfo.socketId, str2ab(data), addr, port, function(result) {
					if (result < 0) {
						log('send fail: ' + result);
						// chrome.sockets.udp.close(createInfo.socketId);
					} else {
						log('sendTo: success ' + port);
						// chrome.sockets.udp.close(createInfo.socketId);
					}
				});
				log("done");
			});
		});
		log('adding listener');
		
	
	}
	catch (e) {
		log("ERROR " + e);
	}
	
}

function retry_udp()
{
	log('retrying udp');
	if (udp_socket >= 0) {
		var port = 1500;
		var addr = "255.255.255.255";
		var data = "RETRY";
		log('trying udp');
		chrome.sockets.udp.send(createInfo.socketId, str2ab(data), addr, port, function(result) {
			if (result < 0) {
				log('send fail: ' + result);
				// chrome.sockets.udp.close(createInfo.socketId);
			} else {
				log('sendTo: success ' + port);
				// chrome.sockets.udp.close(createInfo.socketId);
			}
		});
	}
	else log('no socket');
}
var app = {
    // Application Constructor
    initialize: function() {
        document.addEventListener('deviceready', this.onDeviceReady.bind(this), false);
    },

    // deviceready Event Handler
    //
    // Bind any cordova events here. Common events are:
    // 'pause', 'resume', etc.
    onDeviceReady: function() {
        this.receivedEvent('deviceready');
		 listen_in();
		 setTimeout(retry_udp,5 * 1000);
      //  udp.initialize();
    },

    // Update DOM on a Received Event
    receivedEvent: function(id) {
        var parentElement = document.getElementById(id);
        var listeningElement = parentElement.querySelector('.listening');
        var receivedElement = parentElement.querySelector('.received');

        listeningElement.setAttribute('style', 'display:none;');
        receivedElement.setAttribute('style', 'display:block;');

        console.log('Received Event: ' + id);
    }
};

app.initialize();

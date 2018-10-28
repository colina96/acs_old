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
var user_id = -1;
var user_name = "";
var USER = null;

var app = {
    // Application Constructor
    initialize: function() {
    	console.log("Application Constructor");
        this.bindEvents();
        try {
        	var storage = window.localStorage;
        	if (storage.getItem('RESTHOME')) {
        		var s = storage.getItem('RESTHOME');
        		// alert ("got RESTHOME " + storage.getItem('RESTHOME') + "X" + typeof(storage.getItem('RESTHOME')) + "X");
        		if (s.length < 2){
        			// alert ("setting RESTHOME to ",RESTHOME);
            		storage.setItem('RESTHOME',RESTHOME);
                }
        	}
        	//else {
        		storage.setItem('RESTHOME',RESTHOME);
        	// }
        }
        catch(e) {
        	alert("ERROR  " + e);
        }
    },
    // Bind Event Listeners
    //
    // Bind any events that are required on startup. Common events are:
    // 'load', 'deviceready', 'offline', and 'online'.
    bindEvents: function() {
        document.addEventListener('deviceready', this.onDeviceReady, false);
    },
    // deviceready Event Handler
    //
    // The scope of 'this' is the event. In order to call the 'receivedEvent'
    // function, we must explicitly call 'app.receivedEvent(...);'
    onDeviceReady: function() {
    	console.log("onDeviceReady");
    	log("starting");
    	RESTHOME = "http://www.evoz.com.au/acs_test/REST/";
    	check_login();
    	// start_serial(); // arduino
    	// ioio_start();
    	qpack_init();
        app.receivedEvent('deviceready');
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

function setup_front_page()
{
	if (!USER) return;
	console.log('setup_front_page');
	console.log(USER);
	// this is really ugle but bootstrap breaks everything else..... TODO fix
	var div = document.getElementById('front_page_btns');
	var innerHTML = '';
	if (USER.dock && USER.dock == 1)
		innerHTML += "<button type='button' class='m_btn' onclick='goto_dock();'>Dock</button>";
	if (USER.kitchen && USER.kitchen == 1)
		innerHTML += "<button id='kit_btn' type='button' class='m_btn' onclick=\"goto_m_main('kitchen');\">Kitchen</button>";
	if (USER.plating && USER.plating == 1)
		innerHTML += "<button id='plat_btn' type='button'  class='m_btn' onclick=\"goto_plating_teams();\">Plating</button>";
	innerHTML += "<button id='out_btn' type='button'  class='m_btn' onclick=\"logout();\">Logout</button>";
	div.innerHTML = innerHTML;
}

function check_login()
{
var loginString ="";
console.log("checking login");
$("#login_fail").text("Checking login ..!" + RESTHOME);
$.ajax({
    type: "POST",crossDomain: true, cache: false,
    url:  RESTHOME + "login.php",
    data: loginString,
    dataType: 'json',
    success: function(data){
    	console.log("got login");
    	console.log(data);
    	//document.getElementById("res").innerHTML = "Logout Success..!" + data;
    	if(data['user_id']) {
        	//document.getElementById("res").innerHTML = "Login ID" + data['user_id'];
        	user_id = data['user_id'];
        	user_name = data['user'];
        	USER = data['USER'];
        	
        	if (user_id <= 0) {
        		// load_comps();
            	// load_preptypes();
        		barcode_mode = 'login';
        		openPage('login_div', this, 'red','mobile_main','tabclass');
        		$("#login_fail").text("Not logged in ..!");
        	}
        	else {
        		setup_front_page();
            $("#login_fail").text("Login OK..!");
            	console.log("Login OK");
        		load_comps();
            	load_preptypes();
        		user_name = data['user'];
        		document.getElementById('m_login').innerHTML = user_name;
        		openPage('mm1', this, 'red','mobile_main','tabclass');
        	}
           // localStorage.loginstatus = "true";
           //  window.location.href = "welcome.html";
        }
        else if(data == "error")
        {
            $("#login_fail").text("Login Failed..!");
        }
    }
});
}

function Xcheck_login()
{
	if (user_id <= 0) {
		openPage('login_div', this, 'red','mobile_main','tabclass');
	}
	else {
		openPage('mm1', this, 'red','mobile_main','tabclass');
		
	}
}

function set_admin()
{
	// document.getElementById('log_div').style.display = 'block';
	// goto_home();
	openPage('setup', this, 'red','mobile_main','tabclass');
	document.getElementsByName('resthome')[0].value = RESTHOME;
}

function save_resthome()
{
	RESTHOME=document.getElementsByName('resthome')[0].value;
	var storage = window.localStorage;
	
    storage.setItem('RESTHOME',RESTHOME);
}


function login(barcode_uid)
{
    //var email= "colin.p.atkinson@gmail.com";
    //var password= "acs";
	
	var loginString = null;
	if (barcode_uid > 0) {
		loginString="uid="+barcode_uid+"&login=login";
	}
	else {
		var email = document.getElementsByName('email')[0].value;
		var password = document.getElementsByName('password')[0].value;
    //$("#status").text("Authenticating...");
		console.log("Authenticating...",RESTHOME);
		loginString ="email="+email+"&password="+password+"&login=login";
		if (email == 'admin' && password == "zzz") {
			set_admin();
			return;
		}
	}
	qpack_start();
    console.log(loginString);
    
    $.ajax({
        type: "POST",crossDomain: true, cache: false,
        url:  RESTHOME + "login.php",
        data: loginString,
        dataType: 'json',
        success: function(data){
        	document.getElementById("login_fail").innerHTML = "2 Login Success..!" + data['user_id'];
        	console.log("Authenticated |",data,'|');
            if(data['user_id']) {
           	document.getElementById("login_fail").innerHTML = "2 Login ID" + data['user_id'];
            	user_id = data['user_id'];
            	user_name = data['user'];
            	USER = data['USER'];
            	if (user_id > 0) {
               		load_comps();
                	load_preptypes();
            		user_name = data['user'];
            		document.getElementById('m_login').innerHTML = user_name;
            		setup_front_page();
            		goto_home();
            	}
            	else {
            		document.getElementById('login_fail').innerHTML = "login fail for "+email + " " + password;
            		
            	}
            }
            else 
            {
            	$("#login_fail").text("login failed unknown error");
            }
        }
    });
}

function logout()
{
    var loginString ="logout=login";
    console.log('logging out');
    barcode_mode = 'login';
    openPage('login_div', this, 'red','mobile_main','tabclass');
    $.ajax({
        type: "POST",crossDomain: true, cache: false,
        url:  RESTHOME + "login.php",
        data: loginString,
        dataType: 'json',
        success: function(data){
        	console.log('logout got',data);
        	if(data['user_id']) {
            	user_id = data['user_id'];
            	if (user_id > 0) {
            		openPage('mm2', this, 'red','mobile_main','tabclass');
            	}
            	else {
            		openPage('login_div', this, 'red','mobile_main','tabclass');
            	}
               // localStorage.loginstatus = "true";
               //  window.location.href = "welcome.html";
            }
            else if(data == "error")
            {
                //$("#status").text("Login Failed..!");
            }
        }
    });
}

function openPage(pageName, elmnt, color,content_class,tab_class) 
{

	var popups = document.getElementsByClassName('popup');
    for (i = 0; i < popups.length; i++) {
    		popups[i].style.display = "none";
    } 
	var parent = document.getElementById(pageName).parentElement;
	console.log("opening page ",pageName,content_class);
	console.log('parent ',parent.id,parent.className);
    // Hide all elements with class="tabcontent" by default */
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName(content_class);
    for (i = 0; i < tabcontent.length; i++) {
    	// console.log("found tab ",tabcontent[i].id);
    	try {
    		tabcontent[i].style.display = "none";
    	}
        catch (e) {
        	console.log("who knows.....");
        }
    }

    // Remove the background color of all tablinks/buttons
/*    tablinks = document.getElementsByClassName(tab_class);
    // console.log("found tablinks ",tablinks.length);
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].style.backgroundColor = "";
    } */

    // Show the specific tab content
    document.getElementById(pageName).style.display = "block";

    // Add the specific color to the button used to open the tab content
    // elmnt.style.backgroundColor = color;
}

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


var app = {
    // Application Constructor
    initialize: function() {
    	console.log("Application Constructor");
        this.bindEvents();
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
    	RESTHOME = "http://10.0.0.32/acs/REST/";
    	check_login();
    	// start_serial(); // arduino
    	ioio_start();
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
    	//document.getElementById("res").innerHTML = "Logout Success..!" + data;
    	if(data['user_id']) {
        	//document.getElementById("res").innerHTML = "Login ID" + data['user_id'];
        	user_id = data['user_id'];
        	user_name = data['user'];
        	if (user_id <= 0) {
        		// load_comps();
            	// load_preptypes();
        		openPage('login_div', this, 'red','mobile_main','tabclass');
        		$("#login_fail").text("Not logged in ..!");
        	}
        	else {
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
	document.getElementById('log_div').style.display = 'block';
	goto_home();
}
function login(email,password)
{
    //var email= "colin.p.atkinson@gmail.com";
    //var password= "acs";
    var email = document.getElementsByName('email')[0].value;
    var password = document.getElementsByName('password')[0].value;
    //$("#status").text("Authenticating...");
    console.log("Authenticating...",RESTHOME);
    var loginString ="email="+email+"&password="+password+"&login=login";
    if (email == 'admin' && password == "zzz") {
    	set_admin();
    	return;
    }
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
            	if (user_id > 0) {
               		load_comps();
                	load_preptypes();
            		user_name = data['user'];
            		document.getElementById('m_login').innerHTML = user_name;
            		// openPage('mm2', this, 'red','mobile_main','tabclass');
            		goto_home();
            	}
            	else {
            		document.getElementById('login_fail').innerHTML = "login fail for "+email + " " + password;
            		// document.getElementById('login').style.display = 'block';
            		// document.getElementById('logout').style.display = 'none';
            	}
               // localStorage.loginstatus = "true";
               //  window.location.href = "welcome.html";
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

function openPage(pageName, elmnt, color,content_class,tab_class) {
	console.log("opening page ",pageName,content_class);
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



var form_layout = { 
		'USERS': 
		{ 
			'fields' : {
				'email' : { 'Title':'Login'  },
				'password' : { 'Title':'Password'  }
			}
		},
		'PARAMS':
		{
			'fields' : {
				'pkey' : { 'Title':'KEY' },
				'pvalue' : { 'Title':'VALUE' },
			}
		}
	}

function evoz_tools(tablename,div_id,csv_format,conditions)
{
	console.log('1 evoz_tool init',tablename,div_id,conditions);
	this.tablename = tablename;
	this.div_id = div_id;
	this.csv_format = csv_format;
	this.conditions = conditions;
	this.div = document.getElementById(div_id);
	
	this.get_data = get_data(this.tablename,div_id);
	this.result = null;
	this.build_form = function () {
		console.log('evoz_tool build_form',this.div_id);
		this.div = document.getElementById(div_id);
		var q = new Object();
		q.TABLENAME = this.tablename;
		q.action = 'GET';
		if (conditions) q.conditions = conditions;
		var div_id = this.div_id; // can't use this.div_id in ajax return
		var data =  {data: JSON.stringify(q)}; // just gets field definitions
		
		console.log(data);
		$.ajax({
	    	url: RESTHOME + "replace.php",
	        type: "POST",
	        dataType: 'json',
	        data: data,
	        success: function(result) {
	        	// this.result = result;
	            console.log(result);  
	            console.log('evoz_tool build_form result',div_id);
	            document.getElementById(div_id).innerHTML = '';
	            result.div_id = div_id;
	            if (result.data && result.data.length == 1) build_form(result,div_id);
	            if (result.data && result.data.length > 1) table_results(result,div_id);
	              
	        },
	        fail: (function (result) {
	            console.log("fail ",result);
	        })
	    });
	}
}

function build_empty_form(tablename,div_id)
{
	var q = new Object();
	q.TABLENAME = tablename;
	var data =  {data: JSON.stringify(q)}; // just gets field definitions
	
	console.log(data);
	$.ajax({
    	url: RESTHOME + "replace.php",
        type: "POST",
        dataType: 'json',
        data: data,
        success: function(result) {
        	// this.result = result;
            console.log(result);  
            console.log('evoz_tool build_form result',div_id);
            document.getElementById(div_id).innerHTML = '';
            result.div_id = div_id;
            if (result.fields) build_form(result,div_id);       
        },
        fail: (function (result) {
            console.log("fail ",result);
        })
    });
}
function get_data(tablename,div_id,conditions)
{
	console.log('get_data',tablename,div_id,conditions);
	var ret = Object();
	ret.TABLENAME = tablename;
	
	ret.action = 'GET';
	if (conditions) ret.conditions = conditions;
	// ret.conditions = 'id=4';
	console.log(ret);
	var postdata =  {data: JSON.stringify(ret)};
	$.ajax({
    	url: RESTHOME + "replace.php",
        type: "POST",
        dataType: 'json',
        data: postdata,
        success: function(result) {
            console.log('got result');
            console.log(result);    
         //   document.getElementById('settings_div').innerHTML = result;
            if (result.data && result.data.length == 1) build_form(result,div_id);
            if (result.data && result.data.length > 1) table_results(result,div_id);
        },
        fail: (function (result) {
            console.log("fail ",result);
        })
    });
	return(false);
}

function submit_form(tablename)
{
	console.log('submit_form',tablename);
	var iform = document.getElementById('form_' + tablename);
	var elements = iform.elements;
	
	if (!iform) console.log('cannot find',tablename);
	var valid = 0;
// 	console.log('form elements ' + elements.length);
	var data = Object();
	for (i=0; i<elements.length; i++){
		if (elements[i].type == 'checkbox') {
			data[elements[i].id] = elements[i].checked?1:0;
			elements[i].checked = false; // clear form
		}
		else {
			data[elements[i].id] = elements[i].value;
			if (elements[i].value.length > 0) valid ++;
			elements[i].value = ''; // clear form
		}
		
	   //  console.log(elements[i].id,elements[i].type,elements[i].value,elements[i].checked);
	}
	if (valid > 1) {
		var ret = Object();
		ret.TABLENAME = tablename;
		ret.data = data;
		ret.action = 'INSERT';
		console.log(ret);
		var postdata =  {data: JSON.stringify(ret)};
		$.ajax({
	    	url: RESTHOME + "replace.php",
	        type: "POST",
	        // dataType: 'json',
	        data: postdata,
	        success: function(result) {
	            console.log('got result');
	            console.log(result);    
	           //  document.getElementById('settings_div').innerHTML = result;
	            
	        },
	        fail: (function (result) {
	            console.log("fail ",result);
	        })
	    });
	}
	else console.log('no data to upload');
	return(false);
}



function table_results(data,div_id)
{
	var div = document.getElementById(div_id);
	div.innerHTML = '';
	var h1 = document.createElement('H1');
	if (form_layout[data.TABLENAME] && form_layout[data.TABLENAME].title)
		h1.innerHTML = form_layout[data.TABLENAME].title;
	else
		h1.innerHTML = data.TABLENAME;
	div.appendChild(h1);
	fields = data.fields;
	// headings
	var tab = document.createElement('table');
	
	if (form_layout[data.TABLENAME] && form_layout[data.TABLENAME].className) tab.className = form_layout[data.TABLENAME].className;
	else tab.className = 'data';
	var tr = document.createElement('tr');
	for (let i = 0; i < fields.length; i++) { 
		if (form_layout[data.TABLENAME] && form_layout[data.TABLENAME].fields) {
			if (form_layout[data.TABLENAME].fields[fields[i].Field] &&
					form_layout[data.TABLENAME].fields[fields[i].Field]['Title']) {
				var td = document.createElement('td');
				td.innerHTML = form_layout[data.TABLENAME].fields[fields[i].Field]['Title'];
				tr.appendChild(td);
			}
		}		
		else {
			var td = document.createElement('td');
			td.innerHTML = fields[i].Field;			
			tr.appendChild(td);
		}
	}
	tab.appendChild(tr);
	
	for (let i = 0; i < data.data.length; i++) { 
		var tr = document.createElement('tr');
		tr.setAttribute(
				"onclick",
				"get_data('" + data.TABLENAME + "','" + div_id + "','id=" +  data.data[i]['id'] + "');"
			);
		for (let j = 0; j < fields.length; j++) { 
			if (form_layout[data.TABLENAME] && form_layout[data.TABLENAME].fields) {
				if (form_layout[data.TABLENAME].fields[fields[i].Field] &&
						form_layout[data.TABLENAME].fields[fields[i].Field]['Title']) {
					var td = document.createElement('td');
					td.innerHTML = data.data[i][fields[j].Field];
					tr.appendChild(td);
				}
			}		
			else {
				var td = document.createElement('td');
				td.innerHTML = data.data[i][fields[j].Field];			
				tr.appendChild(td);
			}
			/*
			var td = document.createElement('td');
			if (data.data && data.data[i][fields[j].Field] ){
				td.innerHTML = data.data[i][fields[j].Field];
			}
			tr.appendChild(td); */
		}
		tab.appendChild(tr);
	}
	div.appendChild(tab);
	JSONToCSVConvertor(data.data, 'ReportTitle', true,div_id) ;	
	var btn = document.createElement('button');
	btn.innerHTML = 'New';
	btn.setAttribute(
			"onclick",
			"build_empty_form('" + data.TABLENAME + "','" + div_id + "');"
		);
	div.appendChild(btn);
}

function build_form(data,div_id)
{
	// var div_id = data.div_id;
	console.log('build_form - div ',div_id);
	console.log(data);
	var div = document.getElementById(div_id);
	div.innerHTML = '';
	var h1 = document.createElement('H1');
	h1.innerHTML = data.TABLENAME;
	div.appendChild(h1);
	var form = document.createElement('form');
	form.id = 'form_' + data.TABLENAME;
	
	fields = data.fields;
	var tab = document.createElement('table');
	for (let i = 0; i < fields.length; i++) { // ignore id
		var tr = document.createElement('tr');
		var td = document.createElement('td');
		if (form_layout[data.TABLENAME] && 
				form_layout[data.TABLENAME].fields && 
				form_layout[data.TABLENAME].fields[fields[i].Field] &&
				form_layout[data.TABLENAME].fields[fields[i].Field]['Title'])
			td.innerHTML = form_layout[data.TABLENAME].fields[fields[i].Field]['Title'];
		else 
			td.innerHTML = fields[i].Field;
		tr.appendChild(td);
		var td = document.createElement('td');
		let value = '';
		if (data.data && data.data[0][fields[i].Field] ){
			value = data.data[0][fields[i].Field];
		}
		if (fields[i].Field == 'id' && value > 0) {
			td.innerHTML = value + "<input id='" + fields[i].Field + "' type='hidden' value='" + value + "'>";
		}
		else if (fields[i].Type.indexOf('varchar') == 0) {
			td.innerHTML = "<input id='" + fields[i].Field + "' type='text' value='" + value + "'>";
		}
		else if (fields[i].Type == 'tinyint(1)') {
			if (value > 0) value = 'checked'
			td.innerHTML = "<input id='" + fields[i].Field + "' type='checkbox' value='1' " + value + ">";
		}
		else if (fields[i].Type.indexOf('int') >= 0) {
			td.innerHTML = "<input id='" + fields[i].Field + "' type='number' value='" + value + "'>";
		}
		else {
			td.innerHTML = value;// fields[i].Field;
		}
		tr.appendChild(td);
		if (fields[i].Field != 'id' || value > 0) tab.appendChild(tr);
	}
	form.appendChild(tab);
	var submit = document.createElement('div');
	submit.innerHTML = '<div class="btn" onclick="submit_form(\'' + data.TABLENAME + '\');">Submit</div>';
	// submit.setAttribute("onclick",submit_form);
	
	div.appendChild(form);	
	div.appendChild(submit);
	var get = document.createElement('div');
	get.innerHTML = '<div class="btn" onclick="get_data(\'' + data.TABLENAME + '\',\'' + div_id + '\');">Get All</div>';
	div.appendChild(get);
	
}


function close_popups()
{
	tabcontent = document.getElementsByClassName('popup');
    for (let i = 0; i < tabcontent.length; i++) {
    	// console.log("found tab ",tabcontent[i].id);
        tabcontent[i].style.display = "none";
    }
    if (document.getElementById('shield')) {
		document.getElementById('shield').style.display='none';
	}
}

function openPage(pageName, elmnt, color,content_class,tab_class) {
	close_popups();
    // console.log("opening page ",pageName,content_class);

    // Hide all elements with class="tabcontent" by default */
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName(content_class);
    for (let i = 0; i < tabcontent.length; i++) {
    	// console.log("found tab ",tabcontent[i].id);
        tabcontent[i].style.display = "none";
    }

    // Remove the background color of all tablinks/buttons
    tablinks = document.getElementsByClassName(tab_class);
    // console.log("found tablinks ",tablinks.length);
    for (let i = 0; i < tablinks.length; i++) {
        tablinks[i].style.backgroundColor = "";
    }
    //  elmnt.style.border-bottom = '1px solid black';
    // Show the specific tab content
    document.getElementById(pageName).style.display = "block";

    // Add the specific color to the button used to open the tab content
    // elmnt.style.backgroundColor = color;
}

// ref: http://stackoverflow.com/a/1293163/2343
// This will parse a delimited string into an array of
// arrays. The default delimiter is the comma, but this
// can be overriden in the second argument.
function CSVToArray( strData, strDelimiter ){
    // Check to see if the delimiter is defined. If not,
    // then default to comma.
    strDelimiter = (strDelimiter || ",");

    // Create a regular expression to parse the CSV values.
    var objPattern = new RegExp(
        (
            // Delimiters.
            "(\\" + strDelimiter + "|\\r?\\n|\\r|^)" +

            // Quoted fields.
            "(?:\"([^\"]*(?:\"\"[^\"]*)*)\"|" +

            // Standard fields.
            "([^\"\\" + strDelimiter + "\\r\\n]*))"
        ),
        "gi"
        );


    // Create an array to hold our data. Give the array
    // a default empty first row.
    var arrData = [[]];

    // Create an array to hold our individual pattern
    // matching groups.
    var arrMatches = null;


    // Keep looping over the regular expression matches
    // until we can no longer find a match.
    while (arrMatches = objPattern.exec( strData )){

        // Get the delimiter that was found.
        var strMatchedDelimiter = arrMatches[ 1 ];

        // Check to see if the given delimiter has a length
        // (is not the start of string) and if it matches
        // field delimiter. If id does not, then we know
        // that this delimiter is a row delimiter.
        if (
            strMatchedDelimiter.length &&
            strMatchedDelimiter !== strDelimiter
            ){

            // Since we have reached a new row of data,
            // add an empty row to our data array.
            arrData.push( [] );

        }

        var strMatchedValue;

        // Now that we have our delimiter out of the way,
        // let's check to see which kind of value we
        // captured (quoted or unquoted).
        if (arrMatches[ 2 ]){

            // We found a quoted value. When we capture
            // this value, unescape any double quotes.
            strMatchedValue = arrMatches[ 2 ].replace(
                new RegExp( "\"\"", "g" ),
                "\""
                );

        } else {

            // We found a non-quoted value.
            strMatchedValue = arrMatches[ 3 ];

        }


        // Now that we have our value string, let's add
        // it to the data array.
        arrData[ arrData.length - 1 ].push( strMatchedValue );
    }

    // Return the parsed data.
    return( arrData );
}

function upload_json(target)
{
	console.log('upload_json');
	console.log(target);
}
function show_json(json,div_id)
{
	
	console.log('show_json',div_id);
	var div = document.getElementById(div_id);
	div.innerHTML = '';
/*	var h1 = document.createElement('H1');
	h1.innerHTML = target.tablename;
	div.appendChild(h1); */
	console.log('got json rows = ', json.length,json[0].length);
	var tab = document.createElement('table');
	tab.className = 'data';
	var tr = document.createElement('tr');
	
	
	for (let i = 0; i < json.length; i++) { 
		var tr = document.createElement('tr');
/*		tr.setAttribute(
				"onclick",
				"get_data('" + data.TABLENAME + "','id=" +  data.data[i]['id'] + "');"
			);*/
		for (let j = 0; j < json[i].length; j++) { 
			var td = document.createElement('td');
			td.innerHTML = json[i][j];
			tr.appendChild(td);
		}
		tab.appendChild(tr);
	}
	div.appendChild(tab);
/*	var upload_btn = document.createElement('button');
	upload_btn.innerHTML = 'Upload to ' + target.tablename;
	upload_btn.addEventListener("click", function(event) { 
		upload_json(target); 
		event.preventDefault();
		});
	div.appendChild(upload_btn); */
}

var evoz_openFile = function(event,target) {
    var input = event.target;
    console.log('openFile');
    console.log(target);
    var reader = new FileReader();
    reader.onload = function(){
      var text = reader.result;
      var json = CSVToArray(text);
      if (target) target.json = json;
      console.log(json);
      show_json(json,target);
      
		// document.getElementById('output').innerHTML = text;
      console.log(reader.result.substring(0, 200));
    };
    reader.readAsText(input.files[0]);
  };
  
/* json to csv downloader - used to create test files to upload */
function JSONToCSVConvertor(JSONData, ReportTitle, ShowLabel,attachTo) 
{     

	//If JSONData is not an object then JSON.parse will parse the JSON string in an Object
	var arrData = typeof JSONData != 'object' ? JSON.parse(JSONData) : JSONData;
	var CSV = '';    
	//This condition will generate the Label/Header
	if (ShowLabel) {
	    var row = "";

	    //This loop will extract the label from 1st index of on array
	    for (var index in arrData[0]) {
	        //Now convert each value to string and comma-seprated
	        row += index + ',';
	    }
	    row = row.slice(0, -1);
	    //append Label row with line break
	    CSV += row + '\r\n';
	}

	//1st loop is to extract each row
	for (var i = 0; i < arrData.length; i++) {
	    var row = "";
	    //2nd loop will extract each column and convert it in string comma-seprated
	    for (var index in arrData[i]) {
	        row += '"' + arrData[i][index] + '",';
	    }
	    row.slice(0, row.length - 1);
	    //add a line break after each row
	    CSV += row + '\r\n';
	}

	if (CSV == '') {        
	    alert("Invalid data");
	    return;
	}   

	//this trick will generate a temp "a" tag
	var link = document.createElement("a");    
	link.id="lnkDwnldLnk";
	link.innerHTML = 'Download CSV';

	//this part will append the anchor tag and remove it after automatic click - not any more! cpa
	if (attachTo) document.getElementById(attachTo).appendChild(link);
	else document.body.appendChild(link);

	var csv = CSV;  
	blob = new Blob([csv], { type: 'text/csv' }); 
	var csvUrl = window.webkitURL.createObjectURL(blob);
	var filename = 'UserExport.csv';
	$("#lnkDwnldLnk")
	.attr({
	    'download': filename,
	    'href': csvUrl
	}); 
	
	//$('#lnkDwnldLnk')[0].click();    
	//document.body.removeChild(link);
}  

function show(div_id) 
{
	document.getElementById(div_id).style.display='block';
	console.log('show ' + div_id);
	if (document.getElementById('shield')) {
		console.log('showing shield');
		document.getElementById('shield').style.display='block';
	}
}

function hide(div_id) 
{
	document.getElementById(div_id).style.display='none';
	if (document.getElementById('shield')) {
		document.getElementById('shield').style.display='none';
	}
}

function get_db_data(tablename,conditions,callback) // simple single table REST call
{
	var q = new Object();
	q.TABLENAME = tablename;
	q.action = 'GET';
	if (conditions) q.conditions = conditions;
	var data =  {data: JSON.stringify(q)}; // just gets field definitions
	
	console.log(data);
	$.ajax({
    	url: RESTHOME + "replace.php",
        type: "POST",
        dataType: 'json',
        data: data,
        success: function(result) {
        	// this.result = result;
            console.log(result);  
            console.log('get_db_data result');
            if (callback) callback(result);
              
        },
        fail: (function (result) {
            console.log("fail ",result);
        })
    });
}
  
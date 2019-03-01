// Get the container element
var btnContainer = document.getElementById("menu_status");
// console.log(btnContainer);
// Get all buttons with class="btn" inside the container
var btns = btnContainer.getElementsByClassName("acs_menu_btn");

// Loop through the buttons and add the active class to the current/clicked button
for (var i = 0; i < btns.length; i++) {
    btns[i].addEventListener("click", function() {
        var current = document.getElementsByClassName("active");

        // If there's no active class
        if (current.length > 0) {
            current[0].className = current[0].className.replace(" active", "");
        }

        // Add the active class to the current/clicked button
        this.className += " active";
    });
}

// Get the container element
var btnContainer = document.getElementById("navbar_top");
// console.log(btnContainer);
// Get all buttons with class="btn" inside the container
var btns = btnContainer.getElementsByClassName("tabclass");

// Loop through the buttons and add the active class to the current/clicked button
for (var i = 0; i < btns.length; i++) {
    btns[i].addEventListener("click", function() {
        var current = document.getElementsByClassName("active");

        // If there's no active class
        if (current.length > 0) {
            current[0].className = current[0].className.replace(" active", "");
        }

        // Add the active class to the current/clicked button
        this.className += " active";
    });
}

// Get the container element
var container1 = document.getElementById("suppliers_subtabs");
// console.log(btnContainer);
// Get all buttons with class="btn" inside the container
var tabs = container1.getElementsByClassName("top_menu");

// Loop through the buttons and add the active class to the current/clicked button
for (var i = 0; i < tabs.length; i++) {
    tabs[i].addEventListener("click", function() {
        var current = container1.getElementsByClassName("top_menu_highlighted");
        console.log("highlighted: ",current);
        // If there's no active class
        if (current.length > 0) {
            current[0].className = current[0].className.replace("_highlighted", "");
        }
        this.className = "top_menu_highlighted";



        // Add the active class to the current/clicked button
    });
}

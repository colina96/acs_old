function save_resthome() {
    RESTHOME = document.getElementsByName('resthome')[0].value;
    var storage = window.localStorage;

    storage.setItem('RESTHOME', RESTHOME);
    test_rest();
}

function exit_app() {
    KioskPlugin.exitKiosk();
}

function new_offset_ir(){
    let offset = document.getElementById('ir_offset').value;
    set_IR_offset(offset);
}

function new_offset_probe(){
    let offset = document.getElementById('probe_offset').value;
    set_probe_offset(offset);
}

function set_setup_temp(temperature)
{
    console.log("set_setup_temp " );
    document.getElementById('setup_temp').innerHTML = temperature;
}

function test_probe(){
    temp_probe = true;
    read_temp('S');
}


function test_ir(){
    temp_probe = false;
    read_temp('S');
}
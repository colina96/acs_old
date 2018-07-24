<div class='acs_main'>
	
		<div class="acs_sidebar">
		  
		  <div class='acs_menu_space' >SELECT AN ACTION</div>
		  <button type='button' class='acs_comp_btn' href="#" id="new_comp_btn"  
		  	onclick="openPage('new_comp', this, 'red','comp_details','acs_comp_btn')">START NEW</button>
		  <button type='button' class='acs_comp_btn' href="#" id="future_menu" 
		  	onclick="openPage('future_menus', this, 'red','comp_details','acs_comp_btn')">ACTIVE COMPONENTS</button>
		  
		  
		</div>
	
		<div class="acs_right_content">
			<div id='new_comp' class='comp_details menu_details_active'>Start new component
			Search: <input type="text" id="search" >
			</div>
			<div id='active_comps' class='comp_details'>
			</div>
			<div id='expired_menus' class='comp_details'>
			</div>

		</div>
</div>

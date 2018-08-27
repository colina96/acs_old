create TABLE MENU_ITEM_LINK
(
	id smallint unsigned not null auto_increment, 
	menu_item_id int,
	component_id int,
	constraint pk_example primary key (id) 
);

drop table if exists plating_item;
create table PLATING_ITEM
(
	id smallint unsigned not null auto_increment, 
	user_id smallint unsigned not null,
	team_id smallint unsigned not null,
	menu_item_id smallint unsigned not null,
	time_started datetime,
	time_completed datetime,
	M1_temp float,
	M2_temp float,
	constraint pk_example primary key (id) 
);

drop table if exists  plating_item_component;
create table PLATING_ITEM_COMPONENT
(
	id smallint unsigned not null auto_increment, 
	user_id smallint unsigned not null,
	plating_item_id smallint unsigned not null,
	menu_item_component_id smallint unsigned not null,
	component_id int,
	M1_time datetime,
	time_completed datetime,
	M1_temp float,
	constraint pk_example primary key (id) 
);

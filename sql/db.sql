/* drop database acs; */;
/* CREATE database acs;  */;
use acs;
/* GRANT ALL PRIVILEGES ON *.* TO 'acs'@'localhost' IDENTIFIED BY 'acs';  */;

drop table if exists USERS;
CREATE TABLE USERS (
id smallint unsigned not null auto_increment,
email varchar(50) not null, 
password varchar(50) not null, 
firstname varchar(20) not null, 
lastname varchar(20) not null, 
function varchar(20) , 
admin BOOLEAN,
dock BOOLEAN,
kitchen BOOLEAN,
plating BOOLEAN,
supervisor BOOLEAN,
active BOOLEAN default true,
last_login datetime,
constraint pk_example primary key (id) );
INSERT INTO USERS (email,password,firstname ,lastname,function,admin,dock,kitchen,plating,supervisor)
VALUES
('col','acs','Colin','Atkinson','admin',1,1,1,1,1),
('david@qamc.co','acs','David','Cox','admin',1,1,1,1,1),
('dock','acs','Dock','One','Dock',0,1,0,0,0),
('ds','acs','Dock','Super','super',0,1,0,0,1),
('bob','acs','Bob the','Chef','chef',0,0,1,0,1),
('plating','acs','Plating','Person','chef',0,0,0,1,0),
('PS','acs','Plating','Super','chef',0,0,0,1,1),
('kean','acs','Kean','Maizels','guru',1,1,1,1,1),
('falk','acs','Falk','Schoenfeld','admin',1,1,1,1,1),
('mari','acs','Mari','Scotto','admin',1,1,1,1,1);

select * from USERS;

drop table if exists PREP_TYPES;
CREATE table PREP_TYPES (
	id smallint unsigned not null,
	code varchar(10) not null,
	dock tinyint,
	days_offset int,
	M1_temp int,
	M1_temp_above tinyint,
	M2_time_minutes int,
	M2_alarm_min int,
	M2_temp int,
	M2_temp_above tinyint,
	M3_time_minutes int,
	M3_alarm_min int,
	M3_temp int,
	M3_temp_above tinyint,
	shelf_life_days int,
	probe_type int,
	constraint pk_example primary key (id) 
);
insert into PREP_TYPES values
(1,'CC',	0, 3	,	75,  1,	120, 20, 21,0, 6 * 60,60,  5   ,0,6,0),
(2,'HF',	0, 3 * 7,	80,  1,	120, 20, 21,0, 6 * 60,60,  5   ,0,28,0),
(3,'ESL',	0, 3 * 7,	75,	 1,	120, 20, 21,0, 6 * 60,60,  5   ,0,90,0),
(4,'LR',	0, 3,		null,1,	null, 0,  0,0, null  ,null,null,0,6,0),
(5,'AHR' ,	0, 3,		5,   0,	45,  20, 15,0, null  ,null,null,0,6,0),
(6,'FRESH' ,1, 3,		5,   0,	null,  0, 0,0, null  ,null,null,0,6,0),
(7,'FROZEN',1, 3,		-5,   0,	null,  0, 0,0, null  ,null,null,0,6,0),
(8,'DRY'   ,1, 3,		15,   0,	null,  0, 0,0, null  ,null,null,0,6,0),
(9,'DECANT'   ,2, 3,		null,1,	null, 0,  0,0, null  ,null,null,0,6,0);

drop table if exists CORRECTIVE_ACTIONS;
CREATE table CORRECTIVE_ACTIONS
(
	id smallint not null  auto_increment,
	prep_type smallint not null,
	action_text varchar(100),
	constraint pk_example primary key (id)
);

insert into CORRECTIVE_ACTIONS values
(1,1,'Evacuate Blast Chiller, Hard Chill'),
(2,1,'Decant Product into Shallow Metal - Continue Chilling'),
(3,2,'Add more ice to ice bath'),
(4,3,'Evacuate Blast Chiller, Hard Chill'),
(5,3,'Decant Product into Shallow Metal - Continue Chilling'),
(6,5,'Refrigerate Product'),
(7,5,'Discard Product'),
(8,5,'Retrain Staff'),
(9,5,'Low Risk Item, QA Sign-Off'),
(10,6,'QA Sign-Off'),
(11,6,'Client Request'),
(12,7,'QA Sign-Off'),
(13,8,'QA Sign-Off');

drop table if exists MENUS;
CREATE TABLE MENUS (
	id int not null  auto_increment,
	start_date datetime,
	end_date datetime,
	description varchar(100),
	code varchar(20),
	comment varchar(100),
	
	constraint pk_example primary key (id)
);

insert into MENUS values (1,now(),now(),'INTERNATIONAL','35A','N/A');

drop table if exists MENU_ITEMS;
CREATE TABLE MENU_ITEMS (
	id int not null  auto_increment,
	menu_id int not null,
	code varchar(20),
	dish_name varchar(100),
	plating_team int,
	split1 int,
	split2 int,
	split3 int,
	constraint pk_example primary key (id)
);	

insert into MENU_ITEMS values (1,1,'F0601408','Duck Rillettes, Apple Beetroot Jelly',null,20,0,0);
insert into MENU_ITEMS values (2,1,'F4489315','Vanilla Sauce',null,20,0,0);

drop table if exists MENU_ITEM_COMPONENTS;
CREATE TABLE MENU_ITEM_COMPONENTS (
	id int not null  auto_increment,
	menu_id int,
	description varchar(100),
	prep_type int,
	probe_type int,
	location varchar(10),
	shelf_life_days int,
	high_risk BOOLEAN,
	label_at_dock BOOLEAN,
	constraint pk_example primary key (id)
);	

insert into MENU_ITEM_COMPONENTS values (1,1,'Duck Rillettes',1,1,null,null,FALSE,0);
insert into MENU_ITEM_COMPONENTS values (2,1,'Apple Beetroot Jelly',1,2,null,null,FALSE,0);
insert into MENU_ITEM_COMPONENTS values (3,1,'Vanilla Sauce',2,0,null,null,FALSE,0);
insert into MENU_ITEM_COMPONENTS values (4,0,'Cream',6,0,null,5,FALSE,1);
insert into MENU_ITEM_COMPONENTS values (5,0,'Steak',6,0,null,5,FALSE,1);

drop table if exists MENU_ITEM_LINK;
CREATE TABLE MENU_ITEM_LINK
(
	id smallint unsigned not null auto_increment, 
	menu_id int,
	menu_item_id int,
	component_id int,
	constraint pk_example primary key (id) 
);
insert into MENU_ITEM_LINK values (null,1,1,1);
insert into MENU_ITEM_LINK values (null,1,1,2);
insert into MENU_ITEM_LINK values (null,1,2,3);

/* component link links components with subcompoents that need to be tracked - ie high risk components */;
drop table if exists COMPONENT_LINK;
CREATE TABLE COMPONENT_LINK
(
	id smallint unsigned not null auto_increment, 
	menu_id int,
	component_id smallint unsigned not null,
	subcomponent_id smallint unsigned not null,
	constraint pk_example primary key (id) 
);
insert into COMPONENT_LINK values (null,1,3,4);

drop table if exists COMPONENT;
CREATE table COMPONENT (
	id smallint unsigned not null auto_increment, 
	comp_id int,
	description varchar(100),
	prep_type_id int,
	started datetime,
	M1_check_id int,
	M1_temp float,
	M1_time datetime,
	M1_chef_id int,
	M1_action_code int,
	M1_action_id int,
	M2_check_id int,
	M2_temp float,
	M2_time datetime,
	M2_chef_id int,
	M2_action_code int,
	M2_action_id int,
	M3_check_id int,
	M3_temp float,
	M3_time datetime,
	M3_chef_id int,
	M3_action_code int,
	M3_action_id int,
	finished datetime,
	shelf_life_days int,
	expiry_date datetime,
	decanted_from int,
	constraint pk_example primary key (id) 
);

drop table if exists PLATING_TEAM_MEMBER;
CREATE table PLATING_TEAM_MEMBER
(
	id smallint unsigned not null auto_increment, 
	user_id smallint unsigned not null,
	team_id smallint unsigned not null,
	time_added datetime,
	time_removed datetime,
	constraint pk_example primary key (id) 
);

drop table if exists PLATING_ITEM;
CREATE table PLATING_ITEM
(
	id smallint unsigned not null auto_increment, 
	user_id smallint unsigned not null,
	team_id smallint unsigned not null,
	menu_item_id smallint unsigned not null,
	time_started datetime,
	time_completed datetime,
	expiry_date datetime,
	M1_temp float,
	M2_temp float,
	num_labels smallint unsigned,
	num_trolley_labels smallint unsigned,
	num_completed smallint unsigned,
	corrective_item smallint,
	constraint pk_example primary key (id) 
);

drop table if exists PLATING_ITEM_COMPONENT;
CREATE table PLATING_ITEM_COMPONENT
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

drop table if exists INGREDIENTS;
create table INGREDIENTS
(
	id smallint unsigned not null auto_increment, 
	user_id smallint unsigned not null,
	menu_id int,
	component_id int,
	subcomponent_id int,
	M0_time datetime,
	M0_temp float,
	constraint pk_example primary key (id) 
);

drop table if exists SHIFTS;
create table SHIFTS
(
	id smallint unsigned not null auto_increment, 
	title varchar(10),
	start time,
	end time,
	constraint pk_example primary key (id) 
);

insert into SHIFTS values (null,'09:30 AM','5:00','9:30');
insert into SHIFTS values (null,'11:00 AM','9:30','11:00');
insert into SHIFTS values (null,'05:00 AM','11:00','5:40');

drop table if exists SHIFT_ORDERS;
create table SHIFT_ORDERS
(
	id smallint unsigned not null auto_increment, 
	menu_id int,
	menu_item_id int,
	s1 int default 0,
	s1_done int default 0,
	s2 int default 0,
	s2_done int default 0,
	s3 int default 0,
	s3_done int default 0,
	sdate datetime,
	constraint pk_example primary key (id) 
);

/* miscellaneous parameters that have to be stored somewhere */
drop table if exists PARAMS;
create table PARAMS
(
	id smallint unsigned not null auto_increment, 
	pkey varchar(30) not null,
	pvalue varchar (30),
	lastchange datetime,
	constraint pk_example primary key (id) 
);
insert into PARAMS values(null,'DOCK_LABELS_IP','10.0.0.99',now());
insert into PARAMS values(null,'DOCK_LABELS_PORT','9100',now());
insert into PARAMS values(null,'KITCHEN_LABELS_IP','10.0.0.99',now());
insert into PARAMS values(null,'KITCHEN_LABELS_PORT','9100',now());
insert into PARAMS values(null,'PLATING_LABELS1_IP','10.0.0.99',now());
insert into PARAMS values(null,'PLATING_LABELS1_PORT','9100',now());
insert into PARAMS values(null,'PLATING_LABELS2_IP','10.0.0.99',now());
insert into PARAMS values(null,'PLATING_LABELS2_PORT','9100',now());


drop table if exists SUPPLIERS;
create table SUPPLIERS
(
	id smallint unsigned not null auto_increment, 
	name varchar(30) not null,
	address1 varchar (30),
	address2 varchar (30),
	phone varchar (30),
	lastchange datetime,
	constraint pk_example primary key (id) 
);
insert into SUPPLIERS values(null,'Vic Meats','Mascot','Australia','123456789',now());
insert into SUPPLIERS values(null,'ANDREWS','Mascot','Australia','123456789',now());

drop table if exists PURCHASE_ORDERS;
create table PURCHASE_ORDERS
(
	id smallint unsigned not null auto_increment, 
	supplier_id int,
	comment varchar(100),
	date_created datetime,
	date_received datetime,
	lastchange datetime,
	constraint pk_example primary key (id) 
);

insert into PURCHASE_ORDERS values (null,1,'Weekly Order',now(),null,now());



drop table if exists PURCHASE_ORDER_ITEMS;
create table PURCHASE_ORDER_ITEMS
(
	id smallint unsigned not null auto_increment, 
	purchase_order_id int,
	menu_item_component_id int,
	item_code varchar(30),
	spec varchar(30) not null,
	UOM varchar(10),
	open_shelf_life int,
	prep_type int,
	date_received datetime,
	lastchange datetime,
	constraint pk_example primary key (id) 
);

insert into PURCHASE_ORDER_ITEMS values (null,1,4,'V100001','diced','each',3,6,null,now());
insert into PURCHASE_ORDER_ITEMS values (null,1,5,'V100001','diced','each',3,6,null,now());

drop database acs;
create database acs;
use acs;
GRANT ALL PRIVILEGES ON *.* TO 'acs'@'localhost' IDENTIFIED BY 'acs';

CREATE TABLE USERS ( id smallint unsigned not null auto_increment, 
email varchar(50) not null, 
password varchar(50) not null, 
firstname varchar(20) not null, 
lastname varchar(20) not null, 
function varchar(20) , 
admin int,
last_login datetime,
constraint pk_example primary key (id) );
INSERT INTO USERS ( id, email,password,firstname ,lastname,function,admin)
 VALUES ( null, 'col','acs','Colin','Atkinson','admin',1);
 INSERT INTO USERS ( id, email,password,firstname ,lastname,function,admin)
 VALUES ( null, 'david@qamc.co','acs','David','Cox','admin',1);
 INSERT INTO USERS ( id, email,password,firstname ,lastname,function,admin)
 VALUES ( null, 'chef1@gmail.com','acs','Chef','One','chef',0);
INSERT INTO USERS ( id, email,password,firstname ,lastname,function,admin)
 VALUES ( null, 'chef2@gmail.com','acs','Chef','Two','chef',0);
INSERT INTO USERS ( id, email,password,firstname ,lastname,function,admin)
 VALUES ( null, 'chef3@gmail.com','acs','Bob the','Chef','chef',0);
INSERT INTO USERS ( id, email,password,firstname ,lastname,function,admin)
 VALUES ( null, 'chef4@gmail.com','acs','Chef','Four','chef',0);
INSERT INTO USERS ( id, email,password,firstname ,lastname,function,admin)
 VALUES ( null, 'chef5@gmail.com','acs','Chef','Five','chef',0);
INSERT INTO USERS ( id, email,password,firstname ,lastname,function,admin)
 VALUES ( null, 'chef6@gmail.com','acs','Chef','Six','chef',0);
INSERT INTO USERS ( id, email,password,firstname ,lastname,function,admin)
 VALUES ( null, 'chef7@gmail.com','acs','Chef','Seven','chef',0);
INSERT INTO USERS ( id, email,password,firstname ,lastname,function,admin)
 VALUES ( null, 'chef8@gmail.com','acs','Chef','Eight','chef',0);
INSERT INTO USERS ( id, email,password,firstname ,lastname,function,admin)
 VALUES ( null, 'chef9@gmail.com','acs','Chef','Nine','chef',0);
INSERT INTO USERS ( id, email,password,firstname ,lastname,function,admin)
 VALUES ( null, 'chef10@gmail.com','acs','Chef','10','chef',0);
select * from USERS;

create table PREP_TYPES (
	id smallint unsigned not null,
	code varchar(4) not null,
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
	probe_type int
);
insert into PREP_TYPES values (1,'CC',	3	,	75,  1,	120, 20, 21,0, 6 * 60,60,  5   ,0,6,0);
insert into PREP_TYPES values (2,'HF',	3 * 7,	80,  1,	120, 20, 21,0, 6 * 60,60,  5   ,0,28,0);
insert into PREP_TYPES values (3,'ESL',	3 * 7,	75,	 1,	120, 20, 21,0, 6 * 60,60,  5   ,0,90,0);
insert into PREP_TYPES values (4,'LR',	3,		null,1,	null, 0,  0,0, null  ,null,null,0,6,0);
insert into PREP_TYPES values (5,'AHR',	3,		5,   0,	45,  20, 15,0, null  ,null,null,0,6,0);
	
create table CORRECTIVE_ACTIONS
(
	id smallint not null  auto_increment,
	prep_type smallint not null,
	action_text varchar(100),
	constraint pk_example primary key (id)
);

insert into CORRECTIVE_ACTIONS values (null,1,'Evacuate Blast Chiller, Hard Chill');
insert into CORRECTIVE_ACTIONS values (null,1,'Decant Product into Shallow Metal - Continue Chilling');
insert into CORRECTIVE_ACTIONS values (null,2,'Add more ice to ice bath');
insert into CORRECTIVE_ACTIONS values (null,3,'Evacuate Blast Chiller, Hard Chill');
insert into CORRECTIVE_ACTIONS values (null,3,'Decant Product into Shallow Metal - Continue Chilling');
insert into CORRECTIVE_ACTIONS values (null,5,'Refrigerate Product');
insert into CORRECTIVE_ACTIONS values (null,5,'Discard Product');
insert into CORRECTIVE_ACTIONS values (null,5,'Retrain Staff');
insert into CORRECTIVE_ACTIONS values (null,5,'Low Risk Item, QA Sign-Off');

create TABLE MENUS (
	id int not null  auto_increment,
	start_date datetime,
	end_date datetime,
	description varchar(100),
	code varchar(20),
	comment varchar(100),
	
	constraint pk_example primary key (id)
);

insert into MENUS values (1,now(),now(),'INTERNATIONAL','35A',"N/A");

create TABLE MENU_ITEMS (
	id int not null  auto_increment,
	menu_id int not null,
	code varchar(20),
	dish_name varchar(100),
	plating_team int,
	constraint pk_example primary key (id)
);	

insert into MENU_ITEMS values (1,1,'F0601408','Duck Rillettes, Apple Beetroot Jelly',null);
insert into MENU_ITEMS values (2,1,'F4489315','Vanilla Sauce',null);

create TABLE MENU_ITEM_COMPONENTS (
	id int not null  auto_increment,
	description varchar(100),
	prep_type int,
	probe_type int,
	location varchar(10),
	constraint pk_example primary key (id)
);	

insert into MENU_ITEM_COMPONENTS values (1,'Duck Rillettes',1,1,null);
insert into MENU_ITEM_COMPONENTS values (2,'Apple Beetroot Jelly',1,2,null);
insert into MENU_ITEM_COMPONENTS values (3,'Vanilla Sauce',2,0,null);

create TABLE MENU_ITEM_LINK
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
create table COMPONENT (
	id smallint unsigned not null auto_increment, 
	description varchar(50),
	prep_type_id int,
	M1_check_id int,
	M1_temp int,
	M1_time datetime,
	M1_chef_id int,
	M1_action_code int,
	M2_check_id int,
	M2_temp int,
	M2_time datetime,
	M2_chef_id int,
	M2_action_code int,
	M3_check_id int,
	M3_temp int,
	M3_time datetime,
	M3_chef_id int,
	M3_action_code int,
	finished datetime,
	shelf_life_days int,
	expiry_date datetime,
	constraint pk_example primary key (id) 
);
create table plating_team_member
(
	id smallint unsigned not null auto_increment, 
	user_id smallint unsigned not null,
	team_id smallint unsigned not null,
	time_added datetime,
	time_removed datetime,
	constraint pk_example primary key (id) 
);

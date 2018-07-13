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
 VALUES ( null, 'colin.p.atkinson@gmail.com','acs','Colin','Atkinson','admin',1);
select * from users;

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


	
	

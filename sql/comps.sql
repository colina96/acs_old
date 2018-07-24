drop table COMPONENT;
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
	constraint pk_example primary key (id) 
);

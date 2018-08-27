create table plating_team_member
(
	id smallint unsigned not null auto_increment, 
	user_id smallint unsigned not null,
	team_id smallint unsigned not null,
	time_added datetime,
	time_removed datetime,
	constraint pk_example primary key (id) 
);

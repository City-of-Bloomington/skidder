-- @copyright 2009 City of Bloomington, Indiana
-- @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
-- @author Cliff Ingham <inghamn@bloomington.in.gov>
create table people (
	id int unsigned not null primary key auto_increment,
	firstname varchar(128) not null,
	lastname varchar(128) not null,
	email varchar(255) not null
) engine=InnoDB;
insert people values(1,'Administrator','','');

create table users (
	id int unsigned not null primary key auto_increment,
	person_id int unsigned not null unique,
	username varchar(30) not null unique,
	password varchar(32),
	authenticationMethod varchar(40) not null default 'LDAP',
	foreign key (person_id) references people(id)
) engine=InnoDB;
insert users values(1,1,'admin',md5('admin'),'local');

create table roles (
	id int unsigned not null primary key auto_increment,
	name varchar(30) not null unique
) engine=InnoDB;
insert roles values(1,'Administrator');

create table user_roles (
	user_id int unsigned not null,
	role_id int unsigned not null,
	primary key (user_id,role_id),
	foreign key(user_id) references users (id),
	foreign key(role_id) references roles (id)
) engine=InnoDB;
insert user_roles values(1,1);

create table applications (
	id int unsigned not null primary key auto_increment,
	name varchar(128) not null,
	ip_address varchar(15) not null,
	unique (name,ip_address)
) engine=InnoDB;

create table entries (
	application_id int unsigned not null,
	timestamp timestamp not null default CURRENT_TIMESTAMP,
	script varchar(255) not null,
	type varchar(128) not null,
	message mediumtext,
	foreign key (application_id) references applications(id),
	key (script),
	key (type)
) engine=InnoDB;

create table notificationList (
	id int unsigned not null primary key auto_increment,
	application_id int unsigned not null,
	person_id int unsigned not null,
	unique (application_id,person_id),
	foreign key (application_id) references applications(id),
	foreign key (person_id) references people(id)
)engine=InnoDB;

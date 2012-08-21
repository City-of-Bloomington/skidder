alter table people add username varchar(40) unique;
alter table people add password varchar(40);
alter table people add authenticationMethod varchar(40);
alter table people add role varchar(30);

update people p, users u, user_roles ur, roles r
set p.username=u.username,
	p.password=u.password,
	p.authenticationMethod=u.authenticationMethod,
	p.role=r.name
where p.id=u.person_id
  and ur.user_id=u.id
  and ur.role_id=r.id;

drop table user_roles;
drop table roles;
drop table users;

alter table applications change ip_address ipAddress varchar(15) not null;
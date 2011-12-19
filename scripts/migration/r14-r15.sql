alter table users modify authenticationMethod varchar(40) not null default 'Employee';
update users set authenticationMethod='Employee' where authenticationMethod='LDAP';


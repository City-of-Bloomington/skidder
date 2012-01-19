-- These indexes were getting too big for the small virtual machines
-- We're not actually doing any queries that rely on them,
-- so we're purging them to save space and increase speed
alter table notifications drop foreign key notifications_ibfk_2;
alter table notifications drop index script;

alter table entries drop index script;
alter table entries drop index type;

alter table entries add request_uri varchar(255) not null after script;
update entries set request_uri=script;
update entries set script=substring_index(script,'?',1);

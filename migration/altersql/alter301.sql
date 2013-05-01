alter table footmark
add foreign key(a_id) references answer(id) on delete cascade;

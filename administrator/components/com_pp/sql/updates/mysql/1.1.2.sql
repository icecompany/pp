alter table `#__mkv_pp_operations`
    add checked_out int unsigned not null default 0,
    add checked_out_time datetime not null;


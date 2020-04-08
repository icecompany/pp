alter table `#__mkv_pp_operations`
    add checked_out int unsigned not null default 0,
    add checked_out_time datetime not null;

alter table `#__mkv_pp_plan`
    modify objectID smallint unsigned null default null;

alter table `#__mkv_pp_plan` drop foreign key `#__mkv_pp_plan_#__mkv_pp_objects_objectID_id_fk`;


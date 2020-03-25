create table `#__mkv_pp_operations`
(
    id             int unsigned      not null auto_increment primary key,
    taskID         smallint unsigned not null,
    date_operation date              not null,
    date_close     date              null default null,
    managerID      int               not null,
    directorID     int               not null,
    task           text              not null,
    result         text              null default null,
    index `#__mkv_pp_operations_date_operation_index` (date_operation),
    index `#__mkv_pp_operations_date_close_index` (date_close),
    constraint `#__mkv_pp_operations_#__mkv_pp_plan_taskID_id_fk` foreign key (taskID) references `#__mkv_pp_plan` (id),
    constraint `#__mkv_pp_operations_#__users_managerID_id_fk` foreign key (managerID) references `#__users` (id),
    constraint `#__mkv_pp_operations_#__users_directorID_id_fk` foreign key (directorID) references `#__users` (id)
) character set utf8
  collate utf8_general_ci;

alter table `#__mkv_pp_plan`
    drop index `#__mkv_pp_plan_status_index`,
    drop status;


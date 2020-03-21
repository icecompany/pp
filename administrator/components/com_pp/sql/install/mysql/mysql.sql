create table if not exists `#__mkv_pp_actions`
(
    id       smallint unsigned not null auto_increment primary key,
    title    varchar(255)      not null,
    ordering int unsigned      not null default 0,
    index `#__mkv_pp_actions_title_index` (title),
    index `#__mkv_pp_actions_ordering_index` (ordering)
) character set utf8
  collate utf8_general_ci;

create table if not exists `#__mkv_pp_objects`
(
    id       smallint unsigned not null auto_increment primary key,
    title    varchar(255)      not null,
    ordering int unsigned      not null default 0,
    index `#__mkv_pp_objects_title_index` (title),
    index `#__mkv_pp_objects_ordering_index` (ordering)
) character set utf8
  collate utf8_general_ci;

create table if not exists `#__mkv_pp_task_types`
(
    id       smallint unsigned not null auto_increment primary key,
    title    varchar(255)      not null,
    ordering int unsigned      not null default 0,
    index `#__mkv_pp_task_types_title_index` (title),
    index `#__mkv_pp_task_types_ordering_index` (ordering)
) character set utf8
  collate utf8_general_ci;

create table if not exists `#__mkv_pp_sections`
(
    id        smallint unsigned not null auto_increment primary key,
    parentID  smallint unsigned null     default null,
    managerID int               not null,
    title     varchar(255)      not null,
    ordering  int unsigned      not null default 0,
    index `#__mkv_pp_sections_title_index` (title),
    index `#__mkv_pp_sections_ordering_index` (ordering),
    constraint `#__mkv_pp_sections_#__users_managerID_id_fk` foreign key (managerID) references `#__users` (id)
) character set utf8
  collate utf8_general_ci;

create table if not exists `#__mkv_pp_plan`
(
    id           smallint unsigned not null auto_increment primary key,
    projectID    smallint unsigned not null,
    actionID     smallint unsigned not null,
    objectID     smallint unsigned not null,
    sectionID    smallint unsigned not null,
    typeID       smallint unsigned not null,
    managerID    int               not null,
    directorID   int               not null,
    contractorID int unsigned      not null,
    date_start   date              not null,
    date_end     date              not null,
    task         text              not null,
    result       text              not null,
    status       tinyint           not null default 0 comment 'Статус выполнения. 0 - не выполнена, 1 - выполнена, -1 - просрочена',
    index `#__mkv_pp_plan_date_start_index` (date_start),
    index `#__mkv_pp_plan_date_end_index` (date_end),
    index `#__mkv_pp_plan_status_index` (status),
    constraint `#__mkv_pp_plan_#__mkv_projects_projectID_id_fk` foreign key (projectID) references `#__mkv_projects` (id),
    constraint `#__mkv_pp_plan_#__mkv_pp_actions_actionID_id_fk` foreign key (actionID) references `#__mkv_pp_actions` (id),
    constraint `#__mkv_pp_plan_#__mkv_pp_objects_objectID_id_fk` foreign key (objectID) references `#__mkv_pp_objects` (id),
    constraint `#__mkv_pp_plan_#__mkv_pp_sections_sectionID_id_fk` foreign key (sectionID) references `#__mkv_pp_sections` (id),
    constraint `#__mkv_pp_plan_#__mkv_pp_task_types_typeID_id_fk` foreign key (typeID) references `#__mkv_pp_task_types` (id),
    constraint `#__mkv_pp_plan_#__users_managerID_id_fk` foreign key (managerID) references `#__users` (id),
    constraint `#__mkv_pp_plan_#__users_directorID_id_fk` foreign key (directorID) references `#__users` (id),
    constraint `#__mkv_pp_plan_#__mkv_companies_contractorID_id_fk` foreign key (contractorID) references `#__mkv_companies` (id)
) character set utf8
  collate utf8_general_ci;


create table `#__mkv_pp_versions` (
                                         id smallint unsigned auto_increment primary key,
                                         version varchar(20) not null,
                                         dat timestamp not null default current_timestamp
)
    character set utf8 collate utf8_general_ci;

alter table `#__mkv_pp_versions`
    add index `#__mkv_pp_versions_version_index` (version),
    add index `#__mkv_pp_versions_dat_index` (dat);

alter table `#__mkv_pp_tasks`
    add version_add smallint unsigned null default null after typeID,
    add foreign key `#__mkv_pp_tasks_#__mkv_pp_versions_version_add_id_fk` (version_add)
        references `#__mkv_pp_versions` (id)
        on update cascade on delete restrict;


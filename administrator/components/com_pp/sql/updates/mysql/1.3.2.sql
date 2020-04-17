alter table `#__mkv_pp_task_types`
    add `groupID` int unsigned null default null after title,
    add foreign key `#__mkv_pp_task_types_#__usergroups_groupID_id_fk` (groupID)
        references `#__usergroups` (id)
        on update cascade on delete restrict;
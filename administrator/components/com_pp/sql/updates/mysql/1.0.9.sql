alter table `#__mkv_pp_plan`
    add date_close date null default null comment 'Дата завершения' after date_end,
    add index `#__mkv_pp_plan_date_close_index` (date_close),
    drop foreign key `#__mkv_pp_plan_#__mkv_pp_actions_actionID_id_fk`,
    drop actionID,
    modify contractorID int unsigned null default null,
    modify result text null default null;


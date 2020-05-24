alter table `#__mkv_pp_operations`
    add index `#__mkv_pp_operations_managerID_date_op_index` (managerID, date_operation);

alter table `#__mkv_pp_operations`
    add index `#__mkv_pp_operations_directorID_date_op_index` (directorID, date_operation);

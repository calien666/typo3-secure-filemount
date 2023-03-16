CREATE TABLE sys_file_storage
(
    fe_group varchar(255) default '0' not null
);

CREATE TABLE tx_securefilemount_folder
(
    storage     int(11)      DEFAULT '0' NOT NULL,
    folder      varchar(255) default ''  not null,
    folder_hash varchar(40)  DEFAULT ''  NOT NULL,

    fe_group   varchar(255) default '0' not null,

    KEY folder (storage, folder_hash)
);

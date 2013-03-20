CREATE TABLE "sist_users" ( 
  "id_user"     INT serial NOT NULL,
  "login"       varchar(32) NOT NULL,
  "passwd"      character(32) NOT NULL,
  "first_name"  varchar(96) NULL,
  "last_name"   varchar(128) NULL,
  "created"     timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  "salt_hash"   character(32) NOT NULL,
  PRIMARY KEY("id_user")
)
;
ALTER TABLE "sist_users"
  ADD CONSTRAINT "login"
  UNIQUE ("login");

CREATE TABLE "sist_grp" ( 
  "id_grp"      INT serial NOT NULL,
  "description" varchar(255) NOT NULL,
  "created"     timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY("id_grp")
)
;
ALTER TABLE "sist_grp"
  ADD CONSTRAINT "description"
  UNIQUE ("description");

CREATE TABLE "sist_multigroup" ( 
  "id_user" int NOT NULL,
  "id_grp"  int NOT NULL,
  PRIMARY KEY("id_user","id_grp")
)
;
ALTER TABLE "sist_multigroup"
  ADD CONSTRAINT "fk_user"
  FOREIGN KEY("id_user")
  REFERENCES "sist_users"("id_user")
;
ALTER TABLE "sist_multigroup"
  ADD CONSTRAINT "fk_group"
  FOREIGN KEY("id_grp")
  REFERENCES "sist_grp"("id_grp");
  
CREATE TABLE "sist_activity" ( 
  "id_activity" INT serial NOT NULL,
  "id_user"     int NOT NULL,
  "at"          timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  "k"           character(3) NOT NULL,
  "v"           varchar(511) NULL,
  "session"     character(32) NOT NULL,
  PRIMARY KEY("id_activity")
)
;
ALTER TABLE "sist_activity"
  ADD CONSTRAINT "uk_activity"
  UNIQUE ("id_user", "session", "k", "at")
;
ALTER TABLE "sist_activity"
  ADD CONSTRAINT "fk_user"
  FOREIGN KEY("id_user")
  REFERENCES "sist_users"("id_user");

CREATE TABLE "sist_mails" ( 
  "id_mail"     INT serial NOT NULL,
  "entry_date"  timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  "to_name"     varchar(255) NOT NULL,
  "to_mail"     varchar(255) NOT NULL,
  "subject"     varchar(255) NOT NULL,
  "body"        text NOT NULL,
  "footer"      text NULL,
  "sent"        boolean NOT NULL DEFAULT FALSE,
  "sent_date"   timestamp NULL,
  PRIMARY KEY("id_mail")
)

CREATE TABLE "sist_errors" ( 
    "id_error"      INT serial NOT NULL,
    "errno"         int4 NOT NULL,
    "errstr"        varchar(511) NOT NULL,
    "errfile"       varchar(255) NOT NULL,
    "errline"       int4 NOT NULL,
    "errctx"        text NOT NULL,
    "id_user"       int4 NOT NULL,
    "at"            timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "seen"          boolean NOT NULL DEFAULT FALSE,
    "fixed_at"      int4 NOT NULL,
    "notes"         varchar(255) NULL,
    "duplicated_of" int4 NOT NULL DEFAULT 0,
    PRIMARY KEY(id_error)
);
ALTER TABLE "sist_errors"
    ADD CONSTRAINT "fk_user"
  FOREIGN KEY("id_user")
  REFERENCES "sist_users"("id_user");

CREATE TABLE "sist_menu" ( 
  "id_menu"     serial NOT NULL,
  "visible"     bool NOT NULL DEFAULT TRUE,
  "description" varchar(64) NOT NULL,
  "link"        varchar(255) NOT NULL,
  "id_grp"      int NOT NULL,
  "id_order"    int NOT NULL DEFAULT 0,
  "created"     timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY("id_menu")
)
;
ALTER TABLE "sist_menu"
  ADD CONSTRAINT "fk_grp"
  FOREIGN KEY("id_grp")
  REFERENCES "sist_grp"("id_grp");


CREATE TABLE "sist_options_descriptions" ( 
  "id_option"   character(3) NOT NULL,
  "description" varchar(255) NOT NULL,
  PRIMARY KEY("id_option")
)
;
ALTER TABLE "sist_options_descriptions"
  ADD CONSTRAINT "uk_description"
  UNIQUE ("description");
INSERT INTO sist_options_descriptions (id_option,description) VALUES ('sop','System Options');

CREATE TABLE "sist_sessions" ( 
  "id_session"  character(32) NOT NULL,
  "ip"          inet NOT NULL,
  "useragent"   varchar(511) NOT NULL,
  "at"          timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY("id_session")
);

CREATE TABLE "sist_options" ( 
  "id_option" character(3) NOT NULL,
  "name"      varchar(96) NOT NULL,
  "v"         varchar(128) NOT NULL DEFAULT ' ',
  "id_user"   int NOT NULL DEFAULT 0,
  "created"   timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY("id_option","name")
)
;
ALTER TABLE "sist_options"
  ADD CONSTRAINT "fk_user"
  FOREIGN KEY("id_user")
  REFERENCES "sist_users"("id_user")
;
ALTER TABLE "sist_options"
  ADD CONSTRAINT "fk_options"
  FOREIGN KEY("id_option")
  REFERENCES "sist_options_descriptions"("id_option");

INSERT INTO sist_users (id_user,login,passwd,salt_hash) VALUES (0,'','','');  
INSERT INTO sist_grp (id_grp,description) VALUES (0,'');

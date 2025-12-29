-- Tabla de estados
create table if not exists user_status (
  cod_user_status int unsigned auto_increment primary key,
  desc_user_status varchar(80) not null unique,
  created_at timestamp default current_timestamp
);

-- Tabla usuarios
create table if not exists users (
  cod_user bigint unsigned auto_increment primary key,
  full_name varchar(120) not null,
  email varchar(150) collate utf8mb4_unicode_ci not null unique,
  password_hash varchar(255) null, -- Nulo por que si ingresa con cuenta de Google, no tiene pass
  avatar_url varchar(255) null, -- opcional: foto de perfil (útil identificar al usuario)
  is_verified boolean default false,
  cod_user_status int unsigned not null,
  last_login_at timestamp null,
  created_at timestamp default current_timestamp,
  updated_at timestamp default current_timestamp on update current_timestamp,
  deleted_at timestamp null, -- Aquí: Soft Delete (si es NULL, el usuario existe)
  constraint fk_users_cod_user_status foreign key (cod_user_status) references user_status(cod_user_status) on delete restrict
);

-- Tabla para manejo de conexiones externas
create table if not exists user_social_accounts (
  cod_user_social_account bigint unsigned auto_increment primary key,
  cod_user bigint unsigned not null,
  provider varchar(50) not null, -- google, github o apple
  provider_id varchar(255) not null, -- ID único que proporciona la cuenta externa
  avatar_url varchar(255) null, -- imagen de usuario de la cuenta
  created_at timestamp default current_timestamp,
  constraint fk_user_social_accounts_cod_user foreign key (cod_user) references users(cod_user) on delete cascade,
  unique key uq_user_provider (cod_user, provider),
  unique key uq_provider_id (provider, provider_id)
);

-- Tabla para verificacion de correo
create table if not exists user_email_verification (
  cod_verification int unsigned auto_increment primary key,
  cod_user bigint unsigned not null,
  token varchar(100) not null unique,
  expires_at datetime not null,
  verified_at datetime null,
  created_at timestamp default current_timestamp,
  constraint fk_user_email_verification_cod_user foreign key (cod_user) references users(cod_user) on delete cascade,
  index idx_cod_user (cod_user)
);

-- Tabla de estados para tareas
create table if not exists task_status (
  cod_task_status int unsigned auto_increment primary key,
  desc_task_status varchar(80) not null unique,
  created_at timestamp default current_timestamp
);

-- Tabla de tareas
create table if not exists tasks (
  cod_task bigint unsigned auto_increment primary key,
  title varchar(150) not null,
  description text null,
  cod_task_status int unsigned not null,
  priority enum('low', 'medium', 'high') default 'medium',
  due_date datetime null,
  completed_at datetime null,
  created_by bigint unsigned not null,
  created_at timestamp default current_timestamp,
  updated_at timestamp default current_timestamp on update current_timestamp,
  deleted_at timestamp null, -- Aquí: Soft Delete para tareas (papelera)
  constraint fk_tasks_cod_task_status foreign key (cod_task_status) references task_status(cod_task_status) on delete restrict,
  constraint fk_tasks_created_by foreign key (created_by) references users(cod_user) on delete cascade,
  index idx_task_status (cod_task_status),
  index idx_created_by (created_by),
  index idx_tasks_due_date (due_date)
);

--Tabla de colaboradores para tareas
create table if not exists task_collaborators (
  cod_task_collaborator bigint unsigned auto_increment primary key,
  cod_task bigint unsigned not null,
  cod_user bigint unsigned not null,
  permission_level enum('viewer', 'editor') default 'editor', -- Opcional: Para controlar quien puede completar tareas
  assigned_at timestamp default current_timestamp,
  constraint fk_task_collaborators_cod_task foreign key (cod_task) references tasks(cod_task) on delete cascade,
  constraint fk_task_collaborators_cod_user foreign key (cod_user) references users(cod_user) on delete cascade,
  unique key uq_task_by_user (cod_task, cod_user),
  index idx_cod_user_collaborator (cod_user)
);

-- Tabla para los tipos de acciones en actividades de tareas
create table if not exists action_types_by_task_activity (
  cod_action_type int unsigned auto_increment primary key,
  desc_action_type varchar(50) not null unique,
  created_at timestamp default current_timestamp
);

-- Tabla de cambios
create table if not exists task_activity (
  cod_task_activity bigint unsigned auto_increment primary key,
  cod_task bigint unsigned not null,
  cod_user bigint unsigned null,
  cod_action_type int unsigned not null,
  details text null,
  created_at timestamp default current_timestamp,
  constraint fk_task_activity_cod_task foreign key (cod_task) references tasks(cod_task) on delete cascade,
  constraint fk_task_activity_cod_user foreign key (cod_user) references users(cod_user) on delete set null,
  constraint fk_task_activity_cod_action_type foreign key(cod_action_type) references action_types_by_task_activity(cod_action_type) on delete restrict,
  index idx_task_activity_task (cod_task),
  index idx_task_activity_user (cod_user),
  index idx_task_activity_action_type (cod_action_type)
);

-- Tabla para los tipos de notificaciones
create table if not exists notification_types (
  cod_notification_type int unsigned auto_increment primary key,
  desc_notification_type varchar(50) not null unique,
  detail_type varchar(120) not null,
  default_email boolean default true,
  default_in_app boolean default true,
  created_at timestamp default current_timestamp
);

-- Tabla principal de notificaciones
create table if not exists notifications (
  cod_notification bigint unsigned auto_increment primary key,
  cod_user bigint unsigned not null, -- Receptor
  cod_notification_type int unsigned not null,
  title varchar(100) not null,
  message text not null,
  cod_task bigint unsigned null, -- Tarea relacionada (opcional)
  is_read boolean default false,
  sent_email boolean default false,
  email_sent_at timestamp null,
  created_at timestamp default current_timestamp,
  constraint fk_notifications_cod_user foreign key (cod_user) references users(cod_user) on delete cascade,
  constraint fk_notifications_cod_notification_type foreign key (cod_notification_type) references notification_types(cod_notification_type) on delete restrict,
  constraint fk_notifications_cod_task foreign key (cod_task) references tasks(cod_task) on delete set null,
  index idx_user_unread (cod_user, is_read),
  index idx_user_created (cod_user, created_at),
  index idx_task (cod_task)
);

-- Tabla de preferencias del usuario
create table if not exists user_notification_settings (
  cod_user_notification_setting bigint unsigned auto_increment primary key,
  cod_user bigint unsigned not null,
  cod_notification_type int unsigned not null,
  email_enabled boolean default true,
  in_app_enabled boolean default true,
  created_at timestamp default current_timestamp,
  updated_at timestamp default current_timestamp on update current_timestamp,
  constraint fk_user_notification_settings_cod_user foreign key (cod_user) references users(cod_user) on delete cascade,
  constraint fk_user_notification_settings_cod_notification_type foreign key (cod_notification_type) references notification_types(cod_notification_type) on delete cascade,
  unique key uq_user_notification_type (cod_user, cod_notification_type)
);

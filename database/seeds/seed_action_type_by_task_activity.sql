-- Tipos de acciones para registro de actividades sobre tareas
insert into action_types_by_task_activity (desc_action_type) values
  ('created'),
  ('updated'),
  ('updated_status'),
  ('updated_priority'),
  ('shared_with_user'),
  ('remove_user_access'),
  ('completed');

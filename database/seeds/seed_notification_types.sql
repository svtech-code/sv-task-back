-- Tipos de notificaciones
insert into notification_types (desc_notification_type, detail_type) values
  ('TASK_ASSIGNED', 'Nueva tarea asignada'),
  ('TASK_COMPLETED', 'Tarea completada'),
  ('TASK_DUE_SOON', 'Tarea por vencer'),
  ('TASK_OVERDUE', 'Tarea vencida'),
  ('WELCOME_USER', 'Bienvenido al sistema'),
  ('COLLABORATOR_ADDED', 'Agregado como colaborador'),
  ('TASK_UPDATED', 'Tarea actualizada');

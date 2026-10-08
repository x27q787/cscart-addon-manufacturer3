REPLACE INTO ?:nomenclature_nodes (node_id, parent_id, id_path, status, node_type, position, timestamp) VALUES ('9', '7', '7/9', 'A', 'G', '0', '1415526000');
REPLACE INTO ?:nomenclature_nodes (node_id, parent_id, id_path, status, node_type, position, timestamp) VALUES ('10', '7', '7/10', 'A', 'G', '0', '1415736000');

REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`)
VALUES
  (1073, '2.png', 894, 305),
  (1072, '3.png', 894, 305);

REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`)
VALUES
  (952, 9, 'nomenclature_main', 1073, 0, 'M', 0),
  (951, 10, 'nomenclature_main', 1072, 0, 'M', 0);
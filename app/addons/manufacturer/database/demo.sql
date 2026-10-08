REPLACE INTO ?:nomenclature_nodes (node_id, parent_id, id_path, status, node_type, position, timestamp) VALUES ('7', '0', '7', 'A', 'M', '0', '1415336000');
REPLACE INTO ?:nomenclature_nodes (node_id, parent_id, id_path, status, node_type, position, timestamp) VALUES ('40', '7', '7/40', 'A', 'G', '0', UNIX_TIMESTAMP()-639485);
REPLACE INTO ?:nomenclature_nodes (node_id, parent_id, id_path, status, node_type, position, timestamp) VALUES ('41', '7', '7/41', 'A', 'G', '0', UNIX_TIMESTAMP()-458585);
REPLACE INTO ?:nomenclature_nodes (node_id, parent_id, id_path, status, node_type, position, timestamp) VALUES ('42', '7', '7/42', 'A', 'G', '0', UNIX_TIMESTAMP()-232323);
REPLACE INTO ?:nomenclature_nodes (node_id, parent_id, id_path, status, node_type, position, timestamp) VALUES ('43', '7', '7/43', 'A', 'G', '0', UNIX_TIMESTAMP()-111111);

REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`)
VALUES
  (1074, '1.png', 894, 305),
  (1073, '2.png', 894, 305),
  (1072, '3.png', 894, 305);

REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`)
VALUES
  (953, 40, 'nomenclature_main', 1074, 0, 'M', 0),
  (952, 41, 'nomenclature_main', 1073, 0, 'M', 0),
  (951, 42, 'nomenclature_main', 1072, 0, 'M', 0);

REPLACE INTO ?:nomenclature_links (product_id, node_id)
SELECT product_id, 43 FROM ?:products WHERE status = 'A' ORDER BY product_id ASC LIMIT 6;
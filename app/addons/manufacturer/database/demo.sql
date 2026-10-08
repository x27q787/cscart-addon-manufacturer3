REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window) VALUES ('7', '0', '7', 'A', 'M', '0', '1415336000', '0');

REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window) VALUES ('40', '7', '7/40', 'A', 'M', '0', UNIX_TIMESTAMP()-639485, '0');
REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window) VALUES ('41', '7', '7/41', 'A', 'M', '0', UNIX_TIMESTAMP()-458585, '0');
REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window) VALUES ('42', '7', '7/42', 'A', 'M', '0', UNIX_TIMESTAMP()-232323, '0');
REPLACE INTO ?:pages (page_id, parent_id, id_path, status, page_type, position, timestamp, new_window) VALUES ('43', '7', '7/43', 'A', 'M', '0', UNIX_TIMESTAMP()-111111, '0');

REPLACE INTO ?:images (`image_id`, `image_path`, `image_x`, `image_y`)
VALUES
  (1074, '1.png', 894, 305),
  (1073, '2.png', 894, 305),
  (1072, '3.png', 894, 305);


REPLACE INTO ?:images_links (`pair_id`, `object_id`, `object_type`, `image_id`, `detailed_id`, `type`, `position`)
VALUES
  (953, 40, 'manufacturer_page', 1074, 0, 'M', 0),
  (952, 41, 'manufacturer_page', 1073, 0, 'M', 0),
  (951, 42, 'manufacturer_page', 1072, 0, 'M', 0);

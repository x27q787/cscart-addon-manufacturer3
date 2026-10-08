REPLACE INTO ?:nomenclature_nodes (node_id, parent_id, id_path, status, position, timestamp) VALUES
(1, 0, '1', 'A', 0, UNIX_TIMESTAMP()),
(2, 1, '1/2', 'A', 10, UNIX_TIMESTAMP()-86400),
(3, 1, '1/3', 'A', 20, UNIX_TIMESTAMP()-172800);

REPLACE INTO ?:nomenclature_node_descriptions (node_id, lang_code, name, description, seo_name) VALUES
(1, 'ru', 'Производители', '', 'proizvoditeli'),
(1, 'en', 'Manufacturers', '', 'manufacturers'),
(2, 'ru', 'Ergom', '<p>Ergom — основной бренд каталога профессионального инструмента.</p>', 'ergom'),
(2, 'en', 'Ergom', '<p>Ergom is the main brand of the professional tool catalog.</p>', 'ergom'),
(3, 'ru', 'Ergom PRO', '<p>Профессиональная линейка для ежедневной работы под высокой нагрузкой.</p>', 'ergom-pro'),
(3, 'en', 'Ergom PRO', '<p>Professional line for daily high-load work.</p>', 'ergom-pro');
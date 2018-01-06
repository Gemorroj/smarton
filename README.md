### Test app using Symfony 4 and NelmioApiDocBundle


### generate entities
https://github.com/doctrine/DoctrineBundle/issues/729#issuecomment-348858634
```bash
php bin/console doctrine:mapping:convert --from-database annotation ./src/Entity
```


### create controllers, command and others
https://symfony.com/doc/current/bundles/SymfonyMakerBundle/index.html


### NelmioApiBundle
need `symfony/twig-bundle` and `symfony/asset` for `/api/doc/` page. see https://github.com/nelmio/NelmioApiDocBundle/issues/1141
bug? https://github.com/nelmio/NelmioApiDocBundle/issues/1168


### SQL. Use mariabd 10.2.12
```sql
CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facebook_id` bigint(20) unsigned NOT NULL COMMENT 'Facebook ID',
  `date_create` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Дата создания записи',
  `currency` char(3) NOT NULL COMMENT 'Валюта. Справочник ISO 4217',
  `total_cost` decimal(12,2) unsigned NOT NULL COMMENT 'Общая стоимость заказа',
  `is_legal_person` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Юридическое лицо',
  `attributes` json CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Произвольные атрибуты в JSON',
  PRIMARY KEY (`id`),
  CONSTRAINT `orders_attributes_json_valid` CHECK (json_valid(`attributes`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Заказы';
```

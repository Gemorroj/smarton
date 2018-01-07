### Test app using Symfony 4 and NelmioApiDocBundle


#### Структура БД
Используется MariaDB 10.2.12
```sql
CREATE DATABASE `smarton` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `smarton`;

CREATE TABLE `orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `facebook_id` bigint(20) unsigned NOT NULL COMMENT 'Facebook ID',
  `date_create` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Дата создания записи',
  `currency` char(3) NOT NULL COMMENT 'Валюта. Справочник ISO 4217',
  `total_cost` decimal(12,2) unsigned NOT NULL COMMENT 'Общая стоимость заказа',
  `is_legal_person` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Юридическое лицо',
  `attributes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Произвольные атрибуты в JSON',
  PRIMARY KEY (`id`),
  CONSTRAINT `orders_attributes_json_valid` CHECK (`attributes` IS NULL OR json_valid(`attributes`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Заказы';
```
Подробнее про тип `json` в mariadb можно посмотреть тут https://mariadb.com/resources/blog/json-mariadb-102


#### команда для генерации сущностей
```bash
php bin/console doctrine:mapping:convert --from-database annotation ./src/Entity
```
`doctrine:mapping:import` не работает, см. https://github.com/doctrine/DoctrineBundle/issues/729#issuecomment-348858634


#### NelmioApiBundle
Для создания REST API и документации к нему используется бандл `NelmioApiBundle`
Для документации (`/api/doc/`) дополнительно нужно установить `symfony/twig-bundle` и `symfony/asset`. См. https://github.com/nelmio/NelmioApiDocBundle/issues/1141
bug? https://github.com/nelmio/NelmioApiDocBundle/issues/1168


#### symfony/intl
`symfony/intl` требуется для валидации типов валют (ISO 4217)


#### команда для удаления записей за предыдущие дни
```bash
php bin/console app:cleaner today
```

#### команда для дампа записей за предыдущие дни
```bash
php bin/console app:dumper --gzip today /dump.sql.gz
```


#### Общая информация
Выбран `symfony4` + `NelmioApiBundle` во многом для личного обучения. symfony/flex пока что в продакшене не применял, т.к. инфраструктура новой версии фреймворка довольно сырая.
Например, используемый `NelmioApiBundle` всего лишь на днях научился работать с Symfony 4. См. https://github.com/symfony/recipes-contrib/pull/226

Требуемая производительность должна обеспечиваться плоской структурой БД (всего 1 таблица), а так же включенным кэшем (second level cache). Кэш поможет при выборках из таблицы, т.к., в реальном приложении, скорее всего будут операции не только на запись, но и на чтение.
Так же, приложение должно элементарно масштабироваться. Например, 1 `nginx` в качестве балансировщика, и масса независимых `php-fpm` на отдельных серверах. Кэш, соответственно, должен стать распределенным, например, храниться в `redis`.

Документация для работы с REST API находится по адресу http://example.com/api/doc или в json для `swagger` http://example.com/api/doc.json
Там же можно, выполнить запросы.

Произвольные пользвательские данные выполнены в виде `json` объекта.
Код валюты, сделан в виде справочника ISO 4217.
Выборка и удаление записей сделаны в виде консольных команд с возможностью задать произвольную дату (см. http://php.net/manual/ru/datetime.formats.relative.php).
Для автоматизации нужно поставить команды на выполнение в какой-нибудь планировщик. Например, `cron`.


### TODO:
- https://github.com/nelmio/NelmioApiDocBundle/issues/1168

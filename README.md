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

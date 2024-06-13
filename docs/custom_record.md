## Custom Record Item

You are free to create your own record item.
Just create class ```App\Entity\MyRecord``` extends ```PhpSentinel\BugCatcher\Entity\Record``` class.

When you have custom class you need registered it to discriminator map.
This is little bit tricky, because you need override all ORM mappings.

```
mkdir config/doctrine/BugCatcherBudnle
cp vendor/php-sentinel/bug-catcher-bundle/config/doctrine/* config/doctrine/BugCatcherBudnle
```

```xml
<!--config/doctrine/BugCatcherBudnle/Record.orm.xml-->
<!--...-->
<discriminator-map>
    <discriminator-mapping value="log" class="PhpSentinel\BugCatcher\Entity\RecordLog"/>
    <discriminator-mapping value="trace-log" class="PhpSentinel\BugCatcher\Entity\RecordLogTrace"/>
    <discriminator-mapping value="ping" class="PhpSentinel\BugCatcher\Entity\RecordPing"/>
    <discriminator-mapping value="my-record" class="App\Entity\MyRecord"/>
</discriminator-map>
        <!--...-->
```

Configure orm

```yaml
# config/packages/doctrine.yaml
doctrine:
    #...
    orm:
        #...
        mappings:
            BugCatcherBundle:
                type: xml
                dir: '%kernel.project_dir%/config/doctrine/BugCatcherBundle/'
                prefix: 'PhpSentinel\BugCatcher\Entity'
                alias: PhpSentinelBundle
```

Now you can create twig component ```MyRecord``` for rendering log row in dashboard. and configure it in services.yaml

```yaml
# config/services.yaml
parameters:
    dashboard_list_items:
        - RecordLog::class
        - RecordLogTrace::class
        - MyRecord::class
```
![](docs/header.png)


# Nova Databoards

Provides ready-to-use Analytics Databoards for Laravel Nova

[![Latest Stable Version](https://poser.pugx.org/nova-bi/nova-databoards/v)](//packagist.org/packages/nova-bi/nova-databoards) [![Total Downloads](https://poser.pugx.org/nova-bi/nova-databoards/downloads)](//packagist.org/packages/nova-bi/nova-databoards) [![Latest Unstable Version](https://poser.pugx.org/nova-bi/nova-databoards/v/unstable)](//packagist.org/packages/nova-bi/nova-databoards) [![License](https://poser.pugx.org/nova-bi/nova-databoards/license)](//packagist.org/packages/nova-bi/nova-databoards)

![](docs/nova-databoards-1.gif)

### Seperation of metric calculation and visualisation

The Nova-Databoards metric classes are the container for all metric calculations. The calculations are adoptable to the supported visualisations, so e.g. within a `Users`-metric you can calculate e.g. the total number of users for a Value-Visualisation or provide Trend-Data for a Trend-Visualisation.

### configurable and re-usable

With custom configurations you can make your Boards, Metrics, Filters and Visualisations re-usable - check out `nova-bi/nova-databoards/src/Models/Datametricables/users.php` how to use the same metric to show the number of total users and users with verified email.

![](docs/nova-databoards-2.gif)

Thanks to [laravel-schemaless-attributes](https://github.com/spatie/laravel-schemaless-attributes) you can add configuration options to your boards, metrics, filters and visualisations without changing the database schema.
 
### filterable and dynamic

By adding the Trait `nova-bi/nova-databoards/src/Traits/DynamicMetricsTrait.php` the Nova-Metrics (or any custom metric card) become attributable. 

Thumbs up to [Muzaffer Dede](https://novapackages.com/collaborators/muzafferdede) for developing [Nova Global Filter](https://novapackages.com/packages/nemrutco/nova-global-filter), which is essential for dynamic updates of the widgets on changing filters.


## Introduction

Data visualisation is a common business requirement. The default [Nova Metrics](https://nova.laravel.com/docs/3.0/metrics/defining-metrics.html#value-metrics) are providing a simple way to display certain data from your application. 

However the approach to bake metric calculation and visualisation into one file causes limitations if you e.g. want to re-use a metric for segments.

With Nova Metric you would require 3 files to visualize 3 filtered segments of the same KPI e.g.

- Revenue total
- Revenue by region
- Revenue by customer group

In Nova Databoard you would develop 1 filterable datametric with configuration options and different visualisations like Value, Trend, Partition or custom visuals.

Once the datametrics are developed you can configure unlimited widgets and assign them to unlimited databoards.

Databoards are filterable and dynamic - so when changing a filter the widgets are reloads with the new data. 


## Installation

Add the package using composer

    composer require nova-bi/nova-databoards


run Migrations

    php artisan migrate


Add to the `tools()`-method in your `NovaServiceProvider.php` like this:

```php
    use NovaBI\NovaDataboards\NovaDataboards;

    public function tools()
    {
        return [
            new NovaDataboards()
        ];
    }
```


**Recommended:** Publish Configuration File

    php artisan vendor:publish --provider="NovaBI\NovaDataboards\NovaDataboardsServiceProvider" --tag="config"


with `showToolMenu` you can configure if you want to use the Tool Menu default Resource Listing. Set to `false` when using with [Collapsible Resource Manager](https://novapackages.com/packages/digital-creative/collapsible-resource-manager).

    
    
    
**Optional:** Publish Migrations
    
    php artisan vendor:publish --provider="NovaBI\NovaDataboards\NovaDataboardsServiceProvider" --tag="migrations"



## Playground


By default the Playground-Setup is configured, which will give you following basic metrics from you Nova installation:

- Users
- Boards
- Widgets
- ActionEvents

Following visualisations are available (depending on the metric)

- Value
- Trend
- Partition


And these Filters are available.    

- DateFrom
- DateTo
- ActionEventTypes


## Direct Access to Dashboards
The nice [Collapsible Resource Manager](https://novapackages.com/packages/digital-creative/collapsible-resource-manager) package allows you to customize the menu structure. 

With the following code the Databoards are directly accessible through the Menu (see *know issues* below - do you know how to solve this?)

```php

    use NovaBI\NovaDataboards\NovaDataboards;
    use DigitalCreative\CollapsibleResourceManager\CollapsibleResourceManager;
    use DigitalCreative\CollapsibleResourceManager\Resources\Group;
    use DigitalCreative\CollapsibleResourceManager\Resources\NovaResource;
    use DigitalCreative\CollapsibleResourceManager\Resources\TopLevelResource;


    /**
     * Get the tools that should be listed in the Nova sidebar.
     *
     * @return array
     */
    public function tools()
    {
        $analyticsDataboards = [];

        $databoards = \NovaBI\NovaDataboards\Models\Databoard::all();
        $analyticsDataboards[] = NovaResource::make(\NovaBI\NovaDataboards\Nova\Databoard::class)->label(__('All Databoards'));
        foreach ($databoards as $databoard) {
            $analyticsDataboards[] = NovaResource::make(\NovaBI\NovaDataboards\Nova\Databoard::class)->detail($databoard->id)->label($databoard->name);
        }

        return [
            new NovaDataboards(),
            new CollapsibleResourceManager([
                'navigation' => [
                    TopLevelResource::make([
                        'label' => 'Databoards',
                        'icon' => '<svg fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor" class="sidebar-icon"><path d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>',
                        'resources' => $analyticsDataboards
                    ]),
                    TopLevelResource::make([
                        'label' => 'Admin',
                        'icon' => '<svg fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" stroke="currentColor"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>',
                        'resources' => [
                            \App\Nova\User::class,
                            Group::make([
                                    'label' => 'Databoard Configuration',
                                    'expanded' => false,
                                    'icon' => '',
                                    'resources' =>
                                        [
                                            \NovaBI\NovaDataboards\Nova\DataboardConfiguration::class,
                                            \NovaBI\NovaDataboards\Nova\Datafilter::class,
                                            \NovaBI\NovaDataboards\Nova\Datawidget::class
                                        ]
                                ]
                            )
                        ]
                    ]),
                ]
            ])
        ];
    }
```

### Known issue
- cards - and therefor the dashboard - are not updated when navigating directly between resource details views e.g. using [Collapsible Resource Manager
](https://novapackages.com/packages/digital-creative/collapsible-resource-manager), see [Issue 1:](https://github.com/Nova-BI/nova-databoards/issues/1)



## Extending

### Concept


Separation of Metric Calculation and Visualisation

Nova Databoards follow the Nova concept of Resources to represent Models and are structured as following:

`\Nova\Databoardables` -> `\Models\Databoardables`

`\Nova\Datafilterables` -> `\Models\Datafilterables`

`\Nova\Datametricables` -> `\Models\Datametricables`

`\Nova\Datavisualables` -> `\Models\Datavisualables`


You can place your custom Resources and Models in any subdirectory. To make them available please register in the configuration file `config/nova-databoards.php`. Please follow the Playground examples within the `vendor/nova-bi/nova-databoards/src/`directory.



## Roadmap

[Open Development tasks on github](https://github.com/Nova-BI/nova-databoards/issues?q=is%3Aopen+is%3Aissue+label%3Adevelopment)

- support for custom filter
- data range filter using (https://innologica.github.io/vue2-daterange-picker)
- enhance filter bar with main filters (always visibile) and secondary filters (click on button to add)
- Adding layout flexibility to Nova Cards (e.g. height, sort-order)
- artisan command to generate metrics, visuals
- adding visuals, e.g. Chart JS, Google Charts, APEX with common data api
- interactive visuals (todo - click on e.g. a partition will set a filter, which updates all widgets)
- expose metric data through API for external visualisation
- adding ETL for data aggregation
- GUI enhancements
    - select metric with icons / description
    - select visuals with icons / description
    - select filters with icons / description
- drag & drop sorting of widgets 


## Known issues

- Sorting relation-ships (Widgets -> Boards) are not supported yet, the order of widgets on a dashboard is natural [Nova Sortable](https://github.com/optimistdigital/nova-sortable), see https://github.com/optimistdigital/nova-sortable/issues/9

- 2nd level morphto using [Inline MorphTo Field](https://novapackages.com/packages/digital-creative/nova-inline-morph-to) can be edited, but changes are not stored. Should it be read-only as well? See https://github.com/dcasia/nova-inline-morph-to/issues/16

- custom filter not showing in global filter card, see https://github.com/nemrutco/nova-global-filter/issues/16

## Contributing

If you would like to contribute please fork the project and submit a PR.

Check out https://github.com/Nova-BI/nova-databoards/issues for open development tasks and issues.

## Credits notice

This package is highly depending on following selection of packages from the huge range of excellent packages for laravel and nova.

- [Collapsible Resource Manager](https://novapackages.com/packages/digital-creative/collapsible-resource-manager)
- [Inline MorphTo Field](https://novapackages.com/packages/digital-creative/nova-inline-morph-to)
- [Nova Field Dependency Container](https://novapackages.com/packages/epartment/nova-dependency-container)
- [Nova Global Filter](https://novapackages.com/packages/nemrutco/nova-global-filter)
- [Nova Sortable](https://novapackages.com/packages/optimistdigital/nova-sortable)
- [Nova Text Card](https://novapackages.com/packages/ericlagarda/nova-text-card)
- [laravel-schemaless-attributes](https://github.com/spatie/laravel-schemaless-attributes)







## License

This software is released under [The MIT License (MIT)](LICENSE).

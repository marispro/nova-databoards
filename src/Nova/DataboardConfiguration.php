<?php

namespace Cord\NovaDataboards\Nova;

use Cord\NovaDataboards\Nova\Databoardables\BaseBoard;

use Laravel\Nova\Resource;

use Cord\NovaDataboards\Traits\LoadMorphablesTrait;

use Digitalazgroup\PlainText\PlainText;
use Eminiarts\Tabs\Tabs;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use DigitalCreative\InlineMorphTo\InlineMorphTo;
use DigitalCreative\InlineMorphTo\HasInlineMorphToFields;
use Saumini\Count\RelationshipCount;
use OptimistDigital\NovaSortable\Traits\HasSortableRows;


class DataboardConfiguration extends Resource
{
//    public static $displayInNavigation = false;

    use HasSortableRows;
    use HasInlineMorphToFields;
    use LoadMorphablesTrait;


//    use TabsOnEdit;

    // Use this Trait

    public static $defaultSortField = 'sort_order';

    public static $group = 'Databoard';

    /**
     * The model the resource corresponds to.
     *
     * @var  string
     */
    public static $model = \Cord\NovaDataboards\Models\Databoard::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var  string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var  array
     */
    public static $search = [
        'name'
    ];

    /**
     * Get the displayable label of the resource.
     *
     * @return  string
     */
    public static function label()
    {
        return __('Databoard Configuration');
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return  string
     */
    public static function singularLabel()
    {
        return __('Databoard Configuration');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return  array
     */
    public function fields(Request $request)
    {

        $databoardables = config('nova-databoards.morphables.resources.databoardables.resources');

        /*
         * todo: autoload from config('nova-databoards.morphables.resources.databoardables.paths')
        $databoardables = $this->loadMorphables(config('nova-databoards.morphables.resources.databoardables'));
        $databoardables = array_filter($databoardables, function ($boardable) {
            return class_basename($boardable) != 'BaseBoard';
        });
        */

        $fields = [
            InlineMorphTo::make(__('Board'), 'databoardable')
                ->types($databoardables)->required()->hideFromIndex()
//                ->default(),
        ];

        return
            array_merge(
                [
                    Text::make(__('Name'), 'name'),
                    Textarea::make(__('Description'), 'description')
                        ->alwaysShow()
                        ->rows(3)
                        ->withMeta(['extraAttributes' => [
                            'placeholder' => __('Provide a short description for internal use')]
                        ])
                        ->help(
                            'Internal Description'
                        ),
                ],
                $fields,
                [
                    PlainText::make(__('Databoard Type'), function () {
                        if (method_exists($this->databoardable, 'label')) {
                            return $this->databoardable->label();
                        }
                        return '';
                    }),
                    
                    RelationshipCount::make('Data Widgets', 'datawidgets')->onlyOnIndex(),

                    (new Tabs('Relations', [
                        'Data Widgets' => [
                            BelongsToMany::make('datawidgets')
                                ->rules('required')

                        ]
                    ]))->defaultSearch(true),

                ]
            );

    }

    /**
     * Get the cards available for the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return  array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return  array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return  array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return  array
     */
    public function actions(Request $request)
    {
        return [];
    }

}

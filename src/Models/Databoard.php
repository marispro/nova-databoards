<?php
namespace NovaBI\NovaDataboards\Models;
use NovaBI\NovaDataboards\Traits\HasSchemalessAttributesTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Databoard extends Model implements Sortable
{
    use SortableTrait;

    use HasSchemalessAttributesTrait;

    public $casts = [
        'extra_attributes' => 'array',
    ];

    public $sortable = [
        'order_column_name' => 'sort_order',
        'sort_when_creating' => true,
    ];

    protected $table = 'databoards';
    public $timestamps = true;

    public $translatable = ['description'];

    public function databoardable()
    {
        return $this->morphTo();
    }

    public function datawidgets()
    {
        return $this->belongsToMany(Datawidget::class)->orderBy('datawidgets.sort_order', 'asc');
    }
    public function datafilters()
    {
        return $this->belongsToMany(Datafilter::class)->orderBy('datafilters.sort_order', 'asc');
    }

}

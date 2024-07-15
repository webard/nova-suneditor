<?php

namespace Webard\NovaSuneditor;

use Illuminate\Support\Arr;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\Expandable;
use Laravel\Nova\Fields\HasAttachments;
use Laravel\Nova\Fields\DependentFields;
use Laravel\Nova\Fields\FieldFilterable;
use Laravel\Nova\Contracts\FilterableField;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Fields\SupportsDependentFields;
use Laravel\Nova\Contracts\Deletable as DeletableContract;
use Laravel\Nova\Contracts\Storable as StorableContract;use Laravel\Nova\Fields\Filters\TextFilter;

class Suneditor extends Field implements FilterableField, DeletableContract, StorableContract
{
    use SupportsDependentFields;
    use Expandable;
    use FieldFilterable;
    use HasAttachments;
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'suneditor';

     /**
     * Indicates if the element should be shown on the index view.
     *
     * @var bool
     */
    public $showOnIndex = false;

     /**
     * Make the field filter.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return \Laravel\Nova\Fields\Filters\Filter
     */
    protected function makeFilter(NovaRequest $request)
    {
        return new TextFilter($this);
    }

    /**
     * Get the full path that the field is stored at on disk.
     *
     * @return string|null
     */
    public function getStoragePath()
    {
        return null;
    }


      /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  string  $requestAttribute
     * @param  \Illuminate\Database\Eloquent\Model|\Laravel\Nova\Support\Fluent  $model
     * @param  string  $attribute
     * @return void|\Closure
     */
    protected function fillAttribute(NovaRequest $request, $requestAttribute, $model, $attribute)
    {
        return $this->fillAttributeWithAttachment($request, $requestAttribute, $model, $attribute);
    }

     /**
     * Prepare the field for JSON serialization.
     *
     * @return array
     */
    public function serializeForFilter()
    {
        return transform($this->jsonSerialize(), function ($field) {
            return Arr::only($field, [
                'uniqueKey',
                'name',
                'attribute',
            ]);
        });
    }

    /**
     * Prepare the element for JSON serialization.
     *
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return array_merge(parent::jsonSerialize(), [
            'shouldShow' => $this->shouldBeExpanded(),
            'withFiles' => $this->withFiles,
        ]);
    }
}

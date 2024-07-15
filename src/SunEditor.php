<?php

namespace Webard\NovaSuneditor;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Laravel\Nova\Contracts\Deletable as DeletableContract;
use Laravel\Nova\Contracts\FilterableField;
use Laravel\Nova\Contracts\Storable as StorableContract;
use Laravel\Nova\Fields\Expandable;
use Laravel\Nova\Fields\Field;
use Laravel\Nova\Fields\FieldFilterable;
use Laravel\Nova\Fields\Filters\TextFilter;
use Laravel\Nova\Fields\HasAttachments;
use Laravel\Nova\Fields\SupportsDependentFields;
use Laravel\Nova\Http\Requests\NovaRequest;

class SunEditor extends Field implements DeletableContract, FilterableField, StorableContract
{
    use Expandable;
    use FieldFilterable;
    use HasAttachments;
    use SupportsDependentFields;

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

    public function withFiles($disk = null, $path = '/')
    {
        $this->withFiles = true;

        $this->disk($disk)->path($path);

        $this->attach(function (Request $request) {
            $request->validate([
                'file-0' => ['required', 'file'],
            ]);

            $disk = $this->getStorageDisk() ?? $this->getDefaultStorageDisk();

            return Storage::disk($disk)->url($request->file('file-0')->store($this->getStorageDir(), $disk));
        })
            //  ->detach(new DetachAttachment())
            //  ->delete(new DeleteAttachments($this))
            //  ->discard(new DiscardPendingAttachments())
            ->prunable();

        return $this;
    }

    /**
     * Hydrate the given attribute on the model based on the incoming request.
     *
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

    public function buttonListName(string $name): self
    {
        return $this->withMeta(['buttonListName' => $name]);
    }

    public function buttonList(array $buttonList): self
    {
        return $this->withMeta(['buttonList' => $buttonList]);
    }

    public function settings(array $settings): self
    {
        return $this->withMeta(['settings' => $settings + config('nova-suneditor.settings', [])]);
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

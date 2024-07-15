<?php

namespace Webard\NovaSunEditor\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;

class FieldAttachmentController extends Controller
{
    /**
     * Store an attachment for a SunEditor field.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(NovaRequest $request)
    {
        /** @var \Laravel\Nova\Fields\Field&\Laravel\Nova\Contracts\Storable $field */
        $field = $request->newResource()
            ->availableFields($request)
            ->filter(function ($field) {
                return optional($field)->withFiles === true;
            })
            ->findFieldByAttribute($request->field, function () {
                abort(404);
            });

        /** @phpstan-ignore-next-line */
        $url = call_user_func($field->attachCallback, $request);

        return response()->json([
            // "errorMessage": "insert error message",
            'result' => [
                [
                    'url' => $url,
                ],
            ],
        ]);
    }
}

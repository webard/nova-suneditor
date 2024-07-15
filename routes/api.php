<?php

use Illuminate\Support\Facades\Route;
use Webard\NovaSunEditor\Http\Controllers\FieldAttachmentController;

Route::post('/{resource}/field-attachment/{field}', [FieldAttachmentController::class, 'store']);

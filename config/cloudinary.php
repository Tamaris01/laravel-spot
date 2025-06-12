<?php

/*
 * This file is part of the Laravel Cloudinary package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Notification URL
    |--------------------------------------------------------------------------
    |
    | An HTTP or HTTPS URL to notify your application (a webhook) when the process
    | of uploads, deletes, and any API that accepts notification_url has completed.
    | This is optional and can be left null if you don't use webhook notifications.
    |
    */
    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Cloud URL
    |--------------------------------------------------------------------------
    |
    | The Cloudinary URL for authentication, in the form:
    | cloudinary://API_KEY:API_SECRET@CLOUD_NAME
    |
    | This is the main configuration used by the package to connect to your
    | Cloudinary account. It must be set in your .env file as CLOUDINARY_URL.
    |
    */
    'cloud_url' => env('CLOUDINARY_URL'),

    /*
    |--------------------------------------------------------------------------
    | Upload Preset
    |--------------------------------------------------------------------------
    |
    | Optionally specify an upload preset name that you have configured in
    | your Cloudinary dashboard. Upload presets allow you to define upload
    | behaviors, transformations, and security options.
    |
    */
    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),

    /*
    |--------------------------------------------------------------------------
    | Upload Route
    |--------------------------------------------------------------------------
    |
    | Route name or URL that returns the cloud image URL from the Blade upload widget.
    | This is used when you want to upload files directly from a form widget.
    |
    */
    'upload_route' => env('CLOUDINARY_UPLOAD_ROUTE'),

    /*
    |--------------------------------------------------------------------------
    | Upload Action
    |--------------------------------------------------------------------------
    |
    | Controller action that processes the upload request from the Blade upload widget.
    | This should point to a valid controller method in your application.
    |
    */
    'upload_action' => env('CLOUDINARY_UPLOAD_ACTION'),

];

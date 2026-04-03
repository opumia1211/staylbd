<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Deleted File Retention (Delayed Delete)
    |--------------------------------------------------------------------------
    | When you delete a product/image/logo etc., the file is moved to trash
    | and permanently removed after this many days. Set 15 or 30.
    */
    'trashed_retention_days' => (int) env('FILE_RETENTION_DAYS', 15),

    /*
    | Trash folder under storage/app (not web-accessible). Cron cleans it.
    */
    'trashed_path' => env('TRASHED_UPLOADS_PATH', 'trashed_uploads'),

    /*
    |--------------------------------------------------------------------------
    | Permanent Delete on Remove
    |--------------------------------------------------------------------------
    | true হলে delete action-এ file trash-এ না গিয়ে সাথে সাথে permanent delete হবে.
    */
    'permanent_delete_on_remove' => filter_var(env('PERMANENT_DELETE_UPLOADS', false), FILTER_VALIDATE_BOOLEAN),

];

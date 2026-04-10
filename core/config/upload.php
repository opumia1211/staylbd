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
    'permanent_delete_on_remove' => filter_var(env('PERMANENT_DELETE_UPLOADS', true), FILTER_VALIDATE_BOOLEAN),

    /*
    |--------------------------------------------------------------------------
    | Optimize: max longest edge (pixels)
    |--------------------------------------------------------------------------
    | After upload, raster images are downscaled so the longest edge is at most
    | this value (keeps originals lighter for web). Set 0 to disable extra downscale.
    */
    'max_optimize_edge' => (int) env('MAX_OPTIMIZE_IMAGE_EDGE', 2560),

    /*
    |--------------------------------------------------------------------------
    | Product WebP size target (bytes)
    |--------------------------------------------------------------------------
    | After WebP is generated, encoder may reduce quality / dimensions until
    | the file is at or below this size (best-effort). Set 0 to disable.
    */
    'max_product_webp_bytes' => (int) env('MAX_PRODUCT_WEBP_BYTES', 153600),

];

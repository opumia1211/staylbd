<?php

namespace App\Lib;

use App\Constants\FileInfo;
use Intervention\Image\Facades\Image;

class FileManager
{
    /*
    |--------------------------------------------------------------------------
    | File Manager
    |--------------------------------------------------------------------------
    |
    | FileManager class is using to manage edit, update, remove files. Developer
    | can manage any kind of files from here. But some limitations is here for image.
    | This class using a trait to manage the file paths and sizes. Developer can also
    | use this class as a helper function.
    |
    */

    /**
     * The file which will be uploaded
     *
     *
     * @var object
     */
    protected $file;

    /**
     * The path where will be uploaded
     *
     * @var string
     */
    public $path;

    /**
     * The size, if the file is image
     *
     * @var string
     */
    public $size;

    /**
     * Check the file is image or not
     *
     * @var boolean
     */
    protected $isImage;

    /**
     * Thumbnail version size, if required
     * and if the file is image
     *
     * @var string
     */
    public $thumb;

    /**
     * Old filename, which will be removed
     *
     * @var string
     */
    public $old;

    /**
     * Current filename, which is uploading
     *
     * @var string
     */
    public $filename;


    /**
     * Set the file and file type to properties if exist
     *
     * @param $file
     * @return void
     */
    protected static $imageMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    protected static $dangerousMimes = [
        'application/x-php', 'application/x-httpd-php', 'text/x-php', 'application/php',
        'application/x-executable', 'application/x-sharedlib', 'application/x-msdos-program',
        'application/x-msdownload', 'text/html', 'application/javascript', 'text/javascript',
    ];

    public function __construct($file = null)
    {
        $this->file = $file;
        if ($file) {
            $imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'JPG', 'JPEG', 'PNG', 'WEBP'];
            $ext = strtolower($file->getClientOriginalExtension());
            $mime = $this->getRealMimeType($file);

            if (in_array($ext, $imageExtensions)) {
                if ($mime && !in_array($mime, self::$imageMimes)) {
                    throw new \InvalidArgumentException('Invalid image type. Allowed: jpg, jpeg, png, webp.');
                }
                $this->isImage = true;
            } elseif ($ext === 'svg') {
                if (!filter_var(env('ALLOW_SVG_UPLOAD', true), FILTER_VALIDATE_BOOLEAN)) {
                    throw new \InvalidArgumentException('SVG upload is disabled for security.');
                }
                if ($mime && $mime !== 'image/svg+xml') {
                    throw new \InvalidArgumentException('Invalid SVG file.');
                }
                $this->isImage = false;
            } elseif ($ext === 'avif') {
                if ($mime && $mime !== 'image/avif') {
                    throw new \InvalidArgumentException('Invalid AVIF file.');
                }
                $this->isImage = false;
            } else {
                if ($mime && in_array($mime, self::$dangerousMimes)) {
                    throw new \InvalidArgumentException('File type not allowed for security.');
                }
                $this->isImage = false;
            }
        }
    }

    protected function getRealMimeType($file): ?string
    {
        if (!$file || !$file->isValid()) {
            return null;
        }
        $path = $file->getRealPath();
        if (!$path || !is_readable($path)) {
            return null;
        }
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if (!$finfo) {
            return null;
        }
        $mime = finfo_file($finfo, $path);
        finfo_close($finfo);
        return $mime ?: null;
    }

    /**
     * File upload process
     *
     * @return void
     */
    public function upload()
    {

        //create the directory if doesn't exists
        $path = $this->makeDirectory();
        if (!$path) throw new \Exception('File could not been created.');

        //remove the old file if exist
        if ($this->old) {
            $this->removeFile();
        }

        //get the filename
        $filename = $this->getFileName();
        $this->filename = $filename;

        //upload file or image
        if ($this->isImage == true) {
            $this->uploadImage();
        } else {
            $this->uploadFile();
        }
    }

    /**
     * Upload the file if this is image.
     * When GD is not available, falls back to simple file move (no resize).
     *
     * @return void
     */
    protected function uploadImage()
    {
        $gdAvailable = extension_loaded('gd') && function_exists('imagecreatetruecolor');

        if (!$gdAvailable) {
            $this->file->move($this->path, $this->filename);
            return;
        }

        try {
            $image = Image::make($this->file);

            if ($this->size) {
                $size = explode('x', strtolower($this->size));
                $image->resize($size[0], $size[1]);
            }
            $image->save($this->path . '/' . $this->filename);

            if ($this->thumb) {
                if ($this->old) {
                    $this->removeFile($this->path . '/thumb_' . $this->old);
                }
                $thumb = explode('x', $this->thumb);
                Image::make($this->file)->resize($thumb[0], $thumb[1])->save($this->path . '/thumb_' . $this->filename);
            }

            // Auto high-quality optimization + WebP for all admin uploads (logo, banner, product, category, etc.)
            if (function_exists('optimizeUploadedImage')) {
                $relativePath = rtrim(str_replace('\\', '/', $this->path), '/') . '/' . $this->filename;
                optimizeUploadedImage($relativePath);
            }
        } catch (\Throwable $e) {
            $this->file->move($this->path, $this->filename);
        }
    }


    /**
     * Upload the file if this is not a image
     *
     * @return void
     */
    protected function uploadFile()
    {
        $this->file->move($this->path, $this->filename);
    }

    /**
     * Make directory doesn't exists
     * Developer can also call this method statically
     *
     * @param $location
     * @return string
     */
    public function makeDirectory($location = null)
    {
        if (!$location) $location = $this->path;
        if (file_exists($location)) return true;
        return mkdir($location, 0755, true);
    }

    /**
     * Remove all directory inside the location
     * Developer can also call this method statically
     *
     * @param $location
     * @return void
     */
    public function removeDirectory($location = null)
    {
        if (!$location) $location = $this->path;
        if (!is_dir($location)) {
            throw new \InvalidArgumentException("$location must be a directory");
        }
        if (substr($location, strlen($location) - 1, 1) != '/') {
            $location .= '/';
        }
        $files = glob($location . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                static::removeDirectory($file);
            } else {
                unlink($file);
            }
        }
        rmdir($location);
    }

    /**
     * Remove the file if exists. Files are moved to trash and permanently
     * deleted by staylbd:cleanup-trashed-files after FILE_RETENTION_DAYS (15 or 30).
     *
     * @param $path
     * @return void
     */
    public function removeFile($path = null)
    {
        if (!$path) $path = $this->path . '/' . $this->old;

        $absPath = $path;
        if (!file_exists($path) && !preg_match('#^[A-Za-z]:#', str_replace('\\', '/', (string) $path))) {
            if (function_exists('public_path')) {
                $absPath = public_path($path);
            }
            if (!file_exists($absPath) && function_exists('base_path')) {
                $projectRoot = dirname(base_path());
                $absPath = rtrim($projectRoot, '/\\') . '/' . ltrim(str_replace('\\', '/', $path), '/');
            }
        }
        $hardDelete = (bool) config('upload.permanent_delete_on_remove', false);

        if (file_exists($absPath) && is_file($absPath)) {
            if ($hardDelete) {
                @unlink($absPath);
            } else {
                $this->moveToTrash($absPath);
            }
            $this->removeRasterSidecars($absPath, $hardDelete);
        }
        if ($this->thumb) {
            $thumbPath = dirname($absPath) . DIRECTORY_SEPARATOR . 'thumb_' . basename($absPath);
            if (file_exists($thumbPath) && is_file($thumbPath)) {
                if ($hardDelete) {
                    @unlink($thumbPath);
                } else {
                    $this->moveToTrash($thumbPath);
                }
            }
        }
    }

    /**
     * Remove thumb_* and parallel .webp (and common responsive WebP suffixes) next to a deleted raster.
     */
    protected function removeRasterSidecars(string $absPath, bool $hardDelete): void
    {
        $dir = dirname($absPath);
        $base = basename($absPath);
        $stem = pathinfo($base, PATHINFO_FILENAME);
        $ext = strtolower((string) pathinfo($base, PATHINFO_EXTENSION));
        $raster = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        if (!in_array($ext, $raster, true)) {
            return;
        }
        $extra = [
            $dir . DIRECTORY_SEPARATOR . 'thumb_' . $base,
            $dir . DIRECTORY_SEPARATOR . $stem . '.webp',
        ];
        foreach (['thumbnail', 'medium', 'large'] as $suffix) {
            $extra[] = $dir . DIRECTORY_SEPARATOR . $stem . '_' . $suffix . '.webp';
        }
        foreach ($extra as $p) {
            if ($p === '' || !file_exists($p) || !is_file($p)) {
                continue;
            }
            if ($hardDelete) {
                @unlink($p);
            } else {
                $this->moveToTrash($p);
            }
        }
    }

    /**
     * Move file to storage trashed folder for delayed permanent delete.
     */
    protected function moveToTrash(string $absPath): void
    {
        $trashBase = storage_path('app/' . (config('upload.trashed_path', 'trashed_uploads')));
        $dateFolder = $trashBase . DIRECTORY_SEPARATOR . date('Y-m-d');
        if (!is_dir($dateFolder)) {
            @mkdir($dateFolder, 0755, true);
        }
        $dest = $dateFolder . DIRECTORY_SEPARATOR . uniqid('', true) . '_' . basename($absPath);
        if (@rename($absPath, $dest) || @copy($absPath, $dest)) {
            @unlink($absPath);
        } else {
            @unlink($absPath);
        }
    }

    /**
     * Generating the filename which is uploading
     *
     * @return string
     */
    protected function getFileName()
    {
        return uniqid() . time() . '.' . $this->file->getClientOriginalExtension();
    }

    /**
     * Get access of array from fileInfo method as non-static method.
     * Also get some others method
     *
     * @return string|void
     */
    public function __call($method, $args)
    {
        $fileInfo = new FileInfo;
        $filePaths = $fileInfo->fileInfo();
        if (array_key_exists($method, $filePaths)) {
            $path = json_decode(json_encode($filePaths[$method]));
            return $path;
        } else {
            throw new \Exception("The method or FileInfo key '{$method}' does not exist in FileManager.");
        }
    }

    /**
     * Get access some non-static method as static method
     *
     * @return void
     */
    public static function __callStatic($method, $args)
    {
        $selfClass = new FileManager;
        $selfClass->$method(...$args);
    }
}

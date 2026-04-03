<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class VideoMaxDuration implements Rule
{
    protected $maxSeconds;

    public function __construct(int $maxSeconds = 30)
    {
        $this->maxSeconds = $maxSeconds;
    }

    public function passes($attribute, $value)
    {
        if (!$value || !$value->isValid()) {
            return true; // Let required/other rules handle invalid file
        }

        $path = $value->getRealPath();
        if (!$path || !is_readable($path)) {
            return true;
        }

        $duration = $this->getDuration($path);
        if ($duration === null) {
            return true; // Could not detect duration – allow (or set to false to reject)
        }

        return $duration <= $this->maxSeconds;
    }

    protected function getDuration(string $path): ?float
    {
        if (function_exists('shell_exec') && $this->isShellExecAllowed()) {
            $escaped = escapeshellarg($path);
            $cmd = "ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 {$escaped} 2>/dev/null";
            $out = @shell_exec($cmd);
            if ($out !== null && $out !== '') {
                $sec = (float) trim($out);
                return $sec > 0 ? $sec : null;
            }
        }

        return null;
    }

    protected function isShellExecAllowed(): bool
    {
        $disabled = explode(',', (string) ini_get('disable_functions'));
        return !in_array('shell_exec', array_map('trim', $disabled), true);
    }

    public function message()
    {
        return __('Video must be maximum :seconds seconds.', ['seconds' => $this->maxSeconds]);
    }
}

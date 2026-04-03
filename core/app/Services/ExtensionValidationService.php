<?php

namespace App\Services;

use App\Models\Extension;

class ExtensionValidationService
{
    /**
     * Compare semantic versions. Returns: -1 if a<b, 0 if equal, 1 if a>b.
     */
    public static function compareVersion(string $a, string $b): int
    {
        $va = array_map('intval', explode('.', preg_replace('/[^0-9.]/', '', $a) ?: '0'));
        $vb = array_map('intval', explode('.', preg_replace('/[^0-9.]/', '', $b) ?: '0'));
        $len = max(count($va), count($vb));
        for ($i = 0; $i < $len; $i++) {
            $pa = $va[$i] ?? 0;
            $pb = $vb[$i] ?? 0;
            if ($pa < $pb) return -1;
            if ($pa > $pb) return 1;
        }
        return 0;
    }

    /**
     * Check if extension meets dependency requirements.
     * dependency JSON: {"extension_act": "min_version", ...}
     */
    public static function checkDependencies(Extension $extension): array
    {
        $errors = [];
        $dep = $extension->dependency;
        if (!is_array($dep) && !is_object($dep)) {
            return $errors;
        }
        foreach ((array) $dep as $act => $minVer) {
            $depExt = Extension::where('act', $act)->first();
            if (!$depExt) {
                $errors[] = "Dependency '{$act}' not found.";
                continue;
            }
            if (!$depExt->status) {
                $errors[] = "Dependency '{$act}' must be enabled first.";
                continue;
            }
            $depVer = $depExt->version ?? '0';
            if (self::compareVersion($depVer, $minVer) < 0) {
                $errors[] = "Dependency '{$act}' requires version >= {$minVer}, has {$depVer}.";
            }
        }
        return $errors;
    }

    /**
     * Validate before enabling. Returns array of error messages.
     */
    public static function validateEnable(Extension $extension): array
    {
        $errors = self::checkDependencies($extension);
        return $errors;
    }

    /**
     * Check if disabling this extension would break others that depend on it.
     */
    public static function getDependents(Extension $extension): array
    {
        $dependents = [];
        $all = Extension::where('status', 1)->get();
        foreach ($all as $ext) {
            $dep = $ext->dependency ?? [];
            if (isset($dep[$extension->act])) {
                $dependents[] = $ext->name;
            }
        }
        return $dependents;
    }
}

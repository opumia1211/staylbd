<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class FileTypeValidate implements Rule
{
    protected $extensions;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($extensions)
    {
        $this->extensions = $extensions;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $extension = strtolower($value->getClientOriginalExtension());
        $allowed = array_map('strtolower', (array) $this->extensions);
        return in_array($extension, $allowed);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute file type is not supported.';
    }
}

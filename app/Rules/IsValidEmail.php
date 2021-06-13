<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class IsValidEmail implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
        $emailNotYahoo = !((bool) preg_match('/yahoo/', $value));
        return ($emailNotYahoo);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute may not use email from yahoo.';
    }
}

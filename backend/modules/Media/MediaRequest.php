<?php

namespace Media;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

/**
 * Class MediaRequest
 * @package Media
 */
class MediaRequest extends Request
{
    /**
     * @return string[]
     */
    public function validateToIndex()
    {
        return [
            'subject_id' => '',
            'media_type' => Rule::requiredIf(fn() => !!\request()->subject_id),
        ];
    }

    /**
     * @return string[]
     */
    public function validateToStore()
    {
        return [
            'subject_id'  => '',
            'media_type'  => Rule::requiredIf(fn() => !!\request()->subject_id),
            'description' => '',
            'is_public'   => '',
        ];
    }

    /**
     * @return string[]
     */
    public function validateToUpdate()
    {
        return [
            'filename'    => 'string',
            'description' => '',
        ];
    }
}

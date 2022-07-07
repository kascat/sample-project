<?php

namespace Sample;

/** */
class SampleRepository
{
    /** */
    public static function index($filters)
    {
        return SampleUser::query()->when($filters['name'] ?? null, function ($query, $name) {
            return $query->where('name', 'like', "%$name%");
        });
    }
}

<?php

namespace Media;

/**
 * Class MediaRepository
 * @package Media
 */
class MediaRepository
{
    /**
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Support\HigherOrderWhenProxy|\Illuminate\Support\Traits\Conditionable|mixed
     */
    public static function index(array $filters = [])
    {
        return Media::query()->when($filters['subject_id'] ?? null, function ($query, $value) {
            return $query->where('subject_id', $value);
        })->when($filters['media_type'] ?? null, function ($query, $value) {
            return $query->where('media_type', $value);
        });
    }

    /**
     * @param $mediaType
     * @param $subjectId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function mediaByRelationshipType($mediaType, $subjectId = null)
    {
        $query = Media::query()->where('media_type', $mediaType);

        if ($subjectId) {
            $query->where('subject_id', $subjectId);
        } else {
            $query->whereNull('subject_id');
        }

        return $query;
    }
}

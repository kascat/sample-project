<?php

namespace Sample;

/** */
trait SampleResponse
{
    /** */
    public function responseToIndex(array $data = [], int $statusCode = 200)
    {
        // prepare $data

        return response()->json($data, $statusCode);
    }
}

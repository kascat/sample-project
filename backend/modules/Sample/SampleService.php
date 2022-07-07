<?php

namespace Sample;

use App\Http\Services\Service;
use Illuminate\Http\Response;

/** */
class SampleService extends Service
{
    /** */
    public function index(array $filters)
    {
        $exceptionExample = false;
        if ($exceptionExample) {
            throw self::exception([
                'message' => 'Falha'
            ], Response::HTTP_NOT_FOUND);
        }

        $permissionsQuery = SampleRepository::index($filters);

        return self::buildReturn(
            $permissionsQuery
                ->with(\request()->with ?? [])
                ->paginate(\request()->perPage)
        );
    }

    /** */
    public function store(array $data)
    {
        $permission = SampleUser::create($data);

        return self::buildReturn($permission);
    }

    /** */
    public function update(SampleUser $permission, array $data)
    {
        $permission->update($data);

        return self::buildReturn($permission);
    }

    /** */
    public function destroy(SampleUser $permission)
    {
        $permission->delete();

        return self::buildReturn();
    }
}

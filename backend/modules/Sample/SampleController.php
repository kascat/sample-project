<?php

namespace Sample;

use App\Http\Controllers\Controller;

/** */
class SampleController extends Controller
{
    use SampleResponse;

    /** */
    private SampleService $sampleService;

    /** */
    public function __construct(SampleService $sampleService)
    {
        $this->sampleService = $sampleService;
    }

    /** */
    public function test(SampleRequest $request)
    {
        $response = $this->sampleService->test($request->validated());

        return $this->response($response);
    }

    /** */
    public function index(SampleRequest $request)
    {
        $result = $this->sampleService->index($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    /** */
    public function store(SampleRequest $request)
    {
        $result = $this->sampleService->store($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    /** */
    public function show(SampleUser $sampleUser)
    {
        return $this->response($sampleUser->load(\request('with') ?? [])->toArray());
    }

    /** */
    public function update(SampleRequest $request, SampleUser $sampleUser)
    {
        $result = $this->sampleService->update($sampleUser, $request->validated());

        return $this->response($result['response'], $result['status']);
    }

    /** */
    public function destroy(SampleUser $sampleUser)
    {
        $result = $this->sampleService->destroy($sampleUser);

        return $this->response($result['response'], $result['status']);
    }
}

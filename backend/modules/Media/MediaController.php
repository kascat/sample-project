<?php

namespace Media;

use App\Http\Controllers\Controller;

/**
 * Class MediaController
 * @package Media
 */
class MediaController extends Controller
{
    use MediaResponse;

    /** @var MediaService */
    private MediaService $mediaService;

    /**
     * MediaController constructor.
     * @param MediaService $mediaService
     */
    public function __construct(MediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    /**
     * @param MediaRequest $request
     * @return mixed
     */
    public function index(MediaRequest $request)
    {
        $result = $this->mediaService->index($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    /**
     * @param MediaRequest $request
     * @return mixed
     */
    public function store(MediaRequest $request)
    {
        $result = $this->mediaService->store($request->validated());

        return $this->response($result['response'], $result['status']);
    }

    /**
     * @param $mediaId
     * @return mixed
     */
    public function show($mediaId)
    {
        $result = $this->mediaService->show($mediaId);

        return $this->response($result['response'], $result['status']);
    }

    /**
     * @param MediaRequest $request
     * @param $mediaId
     * @return mixed
     */
    public function update(MediaRequest $request, $mediaId)
    {
        $result = $this->mediaService->update($mediaId, $request->validated());

        return $this->response($result['response'], $result['status']);
    }

    /**
     * @param $mediaId
     * @return mixed
     */
    public function destroy($mediaId)
    {
        $result = $this->mediaService->destroy($mediaId);

        return $this->response($result['response'], $result['status']);
    }
}

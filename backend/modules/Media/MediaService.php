<?php

namespace Media;

use App\Http\Services\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Class MediaService
 * @package Media
 */
class MediaService extends Service
{
    /**
     * Define o diretório do storage.
     * @var string|mixed
     */
    private string $spaceDirectory;

    /** Define o tempo do link temporário em horas. */
    const HOURS_TO_TEMP_URL = 12;

    /**
     * MediaService constructor.
     */
    public function __construct()
    {
        $this->spaceDirectory = env('DO_DIRECTORY');
    }

    /**
     * @param array $filters
     * @return array
     */
    public function index(array $filters)
    {
        $mediaQuery = MediaRepository::index($filters);

        $mediaItems = $mediaQuery
            ->with(\request()->with ?? [])
            ->paginate(\request()->perPage);

        $mediaItems->getCollection()
            ->transform(function ($media) {
                $media['url'] = self::generateTemporaryUrl($media);
                return $media;
            });

        return self::buildReturn($mediaItems);
    }

    /**
     * @param array $data
     * @return array
     */
    public function store(array $data)
    {
        self::prepareData($data, [
            'media_type'   => fn($value) => $value ?: null,
            'subject_type' => fn($value) => Media::SUBJECT_TYPES_MAPPING[$data['media_type'] ?? null] ?? null,
            'subject_id'   => fn($value) => $value ?: null,
            'description'  => fn($value) => $value ?: null,
            'is_public'    => fn($value) => $value ? 'public' : 'private',
        ], true);


        if ($data['media_type'] && !array_key_exists($data['media_type'], Media::SUBJECT_TYPES_MAPPING)) {
            throw self::exception([
                'message' => 'Upload não permitido para o tipo especificado'
            ]);
        }

        if (empty($allFiles = \request()->allFiles())) {
            throw self::exception([
                'message' => 'Nenhum arquivo para upload'
            ]);
        }

        if (in_array($data['media_type'], Media::REPLACEABLE_TYPES)) {
            $mediaToRemove = MediaRepository::mediaByRelationshipType($data['media_type'], $data['subject_id'])->get();
            foreach ($mediaToRemove as $item) {
                self::deleteMedia($item);
            }
        }

        $createdMedia = [];

        foreach ($allFiles as $file) {
            $extension = $file->extension();

            $storage = Storage::putFileAs(
                $this->spaceDirectory,
                $file,
                Str::uuid() . '.' . $extension,
                $data['is_public']
            );

            $media = Media::create([
                'media_type'   => $data['media_type'],
                'subject_id'   => $data['subject_id'],
                'subject_type' => $data['subject_type'],
                'filename'     => $file->getClientOriginalName(),
                'description'  => $data['description'],
                'path'         => $storage,
                'mime_type'    => $file->getMimeType(),
                'extension'    => $extension,
            ]);

            $media['url'] = self::generateTemporaryUrl($media);

            array_push($createdMedia, $media);
        }

        return self::buildReturn($createdMedia);
    }

    /**
     * @param $mediaId
     * @return array
     */
    public function show($mediaId)
    {
        $media = Media::with(\request('with') ?? [])->find($mediaId);

        if (!$media) {
            throw self::exception([
                'message' => 'Mídia não encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        $media['url'] = self::generateTemporaryUrl($media);

        return self::buildReturn($media->toArray());
    }

    /**
     * @param $mediaId
     * @param array $data
     * @return array
     */
    public function update($mediaId, array $data)
    {
        $media = Media::find($mediaId);

        if (!$media) {
            throw self::exception([
                'message' => 'Mídia não encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        $media->update($data);

        return self::buildReturn($media);
    }

    /**
     * @param $mediaId
     * @return array
     */
    public function destroy($mediaId)
    {
        $media = Media::find($mediaId);

        if (!$media) {
            throw self::exception([
                'message' => 'Mídia não encontrada'
            ], Response::HTTP_NOT_FOUND);
        }

        self::deleteMedia($media);

        return self::buildReturn();
    }

    /**
     * @param Media $media
     * @param int $hoursToTempUrl
     * @return string
     */
    public static function generateTemporaryUrl(Media $media, $hoursToTempUrl = self::HOURS_TO_TEMP_URL)
    {
        return Storage::temporaryUrl($media->path, now()->addHours($hoursToTempUrl));
    }

    /**
     * @param Media $media
     */
    private static function deleteMedia(Media $media)
    {
        Storage::delete($media->path);

        $media->delete();
    }
}

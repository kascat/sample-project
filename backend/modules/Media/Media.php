<?php

namespace Media;

use Users\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Media
 * @package Media
 */
class Media extends Model
{
    /**
     * Tipos de mídias
     */
    const MEDIA_TYPE_USER_PROFILE = 'user_profile';
    const MEDIA_TYPE_PROJECT_LOGO = 'project_logo';

    /**
     * Mapeamento media x model.
     *
     * O array abaixo especifica com qual tabela/model está vinculado cada tipo de mídia.
     * Caso não possua relacionamento pode receber null.
     */
    const SUBJECT_TYPES_MAPPING = [
        self::MEDIA_TYPE_USER_PROFILE => User::class,
        self::MEDIA_TYPE_PROJECT_LOGO => null,
    ];

    /**
     * Define quais tipos de mídias são substituíveis
     * A mídia é substituída quando a combinação de 'media_type' e 'subject_id' já existe
     */
    const REPLACEABLE_TYPES = [
        self::MEDIA_TYPE_USER_PROFILE,
        self::MEDIA_TYPE_PROJECT_LOGO,
    ];

    protected $table = 'media';

    /** @var string[] */
    protected $fillable = [
        'filename',
        'description',
        'path',
        'media_type',
        'mime_type',
        'extension',
        'subject_id',
        'subject_type',
    ];

    /**
     * Get the owning subject model.
     */
    public function subject()
    {
        return $this->morphTo();
    }
}

<?php

namespace App\Models;

use App\Models\Builders\CountryBuilder;
use App\Models\Concerns\HasIbgeCode;
use App\Support\Database\DateSerializer;
use App\Traits\LegacyAttribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use OpenApiGenerator\Attributes\Property;
use OpenApiGenerator\Attributes\Schema;
use OpenApiGenerator\Type;

#[
    Schema,
    Property(Type::INT, 'id'),
    Property(Type::STRING, 'name'),
    Property(Type::STRING, 'ibge_code'),
    Property(Type::STRING, 'created_at'),
    Property(Type::STRING, 'updated_at'),
]
class Country extends Model
{
    use LegacyAttribute;
    use DateSerializer;
    use HasIbgeCode;

    public const BRASIL = 45;

    /**
     * Builder dos filtros
     *
     * @var string
     */
    protected $builder = CountryBuilder::class;

    /**
     * @var array
     */
    protected $fillable = [
        'name', 'ibge_code',
    ];

    /**
     * @return HasMany
     */
    public function states()
    {
        return $this->hasMany(State::class);
    }
}

<?php

namespace Humweb\Taggable\Models;

use ArrayAccess;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Tag extends Model
{
    use HasFactory;
    use HasSlug;


    public $guarded = [];

    /**
     * @param  Builder  $query
     * @param  string   $name
     *
     * @return Builder
     */
    public function scopeContaining(Builder $query, string $name): Builder
    {
        return $query->where(function (Builder $query) use ($name) {
            $query
                ->orWhere('name', 'ilike', '%'.$name.'%')
                ->orWhere('slug', 'ilike', '%'.$name.'%');
        });
    }

    /**
     * @param  string|array|ArrayAccess  $values
     *
     * @return Collection|Tag|static
     */
    public static function findOrCreate(string|array|ArrayAccess $values): Collection|Tag|static
    {
        $tags = collect($values)->map(function ($value) {
            if ($value instanceof self) {
                return $value;
            }

            return static::findOrCreateFromString($value);
        });

        return is_string($values) ? $tags->first() : $tags;
    }

    /**
     * @param  string       $name
     * @param  string|null  $type
     * @param  string|null  $locale
     *
     * @return Builder|Model|object|null
     */
    public static function findFromString(string $name, string $type = null, string $locale = null): Model|object|Builder|null
    {
        return static::query()
            ->where("name", $name)
            ->first();
    }

    /**
     * @param  string       $name
     * @param  string|null  $type
     * @param  string|null  $locale
     *
     * @return Builder|Model|object|null
     */
    protected static function findOrCreateFromString(string $name, string $type = null, string $locale = null)
    {
        $tag = static::findFromString($name);

        if (! $tag) {
            $tag = static::create([
                'name' => $name,
            ]);
        }

        return $tag;
    }
}

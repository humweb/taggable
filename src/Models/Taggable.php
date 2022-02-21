<?php

namespace Humweb\Taggable\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait Taggable
{

    /**
     * @return MorphToMany
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable');
    }

    /**
     * @param  Builder                       $query
     * @param  string|array|ArrayAccess|Tag  $tags
     *
     * @return Builder
     */
    public function scopeWithAllTags(Builder $query, string|array|ArrayAccess|Tag $tags): Builder
    {
        $tags = static::convertToTags($tags);
        collect($tags)->each(function ($tag) use ($query) {
            $query->whereHas('tags', function (Builder $query) use ($tag) {
                $query->where('tags.id', $tag->id ?? 0);
            });
        });

        return $query;
    }

    /**
     * @param  Builder                       $query
     * @param  string|array|ArrayAccess|Tag  $tags
     *
     * @return Builder
     */
    public function scopeWithAnyTags(Builder $query, string|array|ArrayAccess|Tag $tags,): Builder
    {
        $tags = static::convertToTags($tags);

        return $query
            ->whereHas('tags', function (Builder $query) use ($tags) {
                $tagIds = collect($tags)->pluck('id');

                $query->whereIn('tags.id', $tagIds);
            });
    }


    /**
     * @param  array|ArrayAccess|Tag  $tags
     * @param  string|null            $type
     *
     * @return $this
     */
    public function attachTags(array | ArrayAccess | Tag $tags, string $type = null): static
    {

        $tags = collect(Tag::findOrCreate($tags, $type));

        $this->tags()->syncWithoutDetaching($tags->pluck('id')->toArray());

        return $this;
    }

    /**
     * @param  string|Tag   $tag
     * @param  string|null  $type
     *
     * @return Taggable
     */
    public function attachTag(string | Tag $tag, string | null $type = null)
    {
        return $this->attachTags([$tag], $type);
    }

    /**
     * @param  string|array|ArrayAccess  $tags
     *
     * @return $this
     */
    public function detachTags(string | array | ArrayAccess $tags): static
    {
        $tags = static::convertToTags($tags);

        collect($tags)
            ->filter()
            ->each(fn (Tag $tag) => $this->tags()->detach($tag));

        return $this;
    }

    /**
     * @param  string|Tag   $tag
     * @param  string|null  $type
     *
     * @return $this
     */
    public function detachTag(string | Tag $tag, string | null $type = null): static
    {
        return $this->detachTags([$tag], $type);
    }

    /**
     * @param  array|ArrayAccess  $tags
     *
     * @return $this
     */
    public function syncTags(array | ArrayAccess $tags): static
    {
        $tags = collect(Tag::findOrCreate($tags));

        $this->tags()->sync($tags->pluck('id')->toArray());

        return $this;
    }

    /**
     * @param $values
     *
     * @return \Illuminate\Support\Collection
     */
    protected static function convertToTags($values)
    {
        if ($values instanceof Tag || !is_array($values)) {
            $values = [$values];
        }

        return collect($values)->map(function ($value) {
            if ($value instanceof Tag) {
                return $value;
            }

            return Tag::findFromString($value);
        });
    }
}

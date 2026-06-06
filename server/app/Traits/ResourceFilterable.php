<?php

namespace App\Traits;

use Illuminate\Http\Resources\MissingValue;

trait ResourceFilterable
{
    /**
     * Filter null inputs.
     */
    protected function fields(): array
    {
        if (is_null($this->resource) || $this->resource instanceof MissingValue) {
            return [];
        }

        $prepared = $this->prepareResponse();
        $attributes = $this->resource->getAttributes() ?? [];
        $dynamicAppends = $this->resource->getDynamicAppends() ?? [];

        return collect($prepared)
            ->only(array_merge(array_keys($attributes), $dynamicAppends))
            ->toArray();
    }

    protected function prepareResponse(): array
    {
        $data = [];
        $class = $this->model;
        $classObj = new $class;
        $fields = array_merge($classObj->getQueryFields(), $classObj->getAppends());
        $hiddenFields = $classObj->getHidden() ?? [];
        $casts = (array) $classObj->getCasts();
        foreach ($fields as $field) {
            if (! in_array($field, $hiddenFields)) {
                $castType = $casts[$field] ?? null;
                if ($castType) {
                    switch ($castType) {
                        case 'datetime':
                            $data[$field] = optional($this->$field)->format('d-m-Y H:i:s');
                            break;
                        case 'date':
                            $data[$field] = optional($this->$field)->format('d-m-Y');
                            break;
                        default: // Used for id
                            $data[$field] = $this->$field;
                    }
                } else {
                    $data[$field] = $this->$field;
                }
            }
        }

        return $data;
    }

    protected function whenLoadedMedia(string $key, bool $isResource = false)
    {
        if (is_null($this->resource) || ! method_exists($this->resource, 'getMedia')) {
            return new MissingValue;
        }

        $mediaInput = request()->input('media');
        if (! empty($mediaInput)) {
            $mediaInput = is_string($mediaInput) ? explode(',', $mediaInput) : [];
            if (in_array($key, $mediaInput)) {
                $media = $this->resource->getMedia($key);
                if ($isResource) {
                    return $media ? $media->first() : null;
                }

                return $media;
            }
        }

        return new MissingValue;
    }
}

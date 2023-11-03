<?php

namespace App\Traits;

trait ApiDataProcessingTrait
{
    public function applyQuery($query, $model = null, $parameters = array())
    {
        // search
        $searchTerm = request('searchTerm', '');
        if (!empty($searchTerm) && !empty($model)) {
            $unSearchableColumns = $parameters['unSearchableColumns'] ?? array();
            $fillable = $model->getFillable();

            $query->where(function ($q) use ($searchTerm, $fillable, $unSearchableColumns) {
                foreach ($fillable as $field) {
                    if (!in_array($field, $unSearchableColumns)) {
                        $q->orWhere($field, 'like', '%' . $searchTerm . '%');
                    }
                }
            });
        }
    }

    public function applyValidate(array $validate, array $manyValidate = [], string $action = 'store', array $ignoreUnique = [])
    {
        if (!empty($manyValidate['name'])) {
            $validate[$manyValidate['name']] = 'required|array';

            if (!empty($manyValidate['validate']) && is_array($manyValidate['validate'])) {
                foreach ($manyValidate['validate'] as $key => $value) {
                    $validate[$manyValidate['name'] . '.*.' . $key] = $value;
                }
            }
        }

        if ($action == 'update') { // Bỏ validate required
            foreach ($validate as $key => $value) {
                $validate[$key] = str_replace('required', '', $value);
            }
        }

        if (!empty($ignoreUnique['id']) && !empty($ignoreUnique['fields']) && is_array($ignoreUnique['fields'])) {
            foreach ($ignoreUnique['fields'] as $field) {
                if (!empty($validate[$field]) && str_contains($validate[$field], 'unique')) {
                    $validate[$field] .= ',' . $ignoreUnique['id'];
                }
            }
        }

        return $validate;
    }
}

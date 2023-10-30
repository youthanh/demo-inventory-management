<?php 

namespace App\Traits;

trait ApiDataProcessingTrait
{
    protected function applyQuery($query, $model = null, $parameters = array())
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
}

<?php

namespace Artelogic\MongoRelations;

use Illuminate\Database\Eloquent\Relations\HasOneOrMany;

abstract class ArrayOneOrManyRelation extends HasOneOrMany
{
    /**
     * Get the results of the relationship.
     *
     * @return mixed
     */
    public function getResults()
    {
        return $this->query->get();
    }

    /**
     * Initialize the relation on a set of models.
     *
     * @param  array  $models
     * @param  string $relation
     * @return array
     */
    public function initRelation(array $models, $relation)
    {
        foreach ($models as $model) {
            $model->setRelation($relation, $this->related->newCollection());
        }

        return $models;
    }

    /**
     * Get all of the primary keys for an array of models.
     *
     * @param  array   $models
     * @param  string  $key
     * @return array
     */
    public function getKeys(array $models, $key = null)
    {
        $items = collect([]);
        collect($models)->each(function ($model) use (&$items, $key) {
            $value = $key ? $model->getAttribute($key) : $model->getKey();
            if (is_array($value)) {
                foreach ($value as $i) {
                    $items->push($i);
                }
            } else {
                $items->push($value);
            }
        });

        return $items->values()->unique()->sort()->all();
    }
}

<?php

namespace Artelogic\MongoRelations;

trait ArrayRelations
{
    /**
     * Define an embedded one-to-many relationship.
     *
     * @param  string $related
     * @param  string $localKey
     * @param  string $foreignKey
     * @return ArrayToManyRelation
     */
    protected function arrayToManyRelation($related, $foreignKey = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: $this->getForeignKey();
        if (is_null($localKey)) {
            list(, $caller) = debug_backtrace(false);
            $localKey = $caller['function'];
        }

        $instance = new $related;

        return new ArrayToManyRelation($instance->newQuery(), $this, $foreignKey, $localKey);
    }

    protected function manyToArrayRelation($related, $foreignKey = null, $foreignField = null, $localKey = null)
    {
        $foreignKey = $foreignKey ?: '_id';
        $localKey   = $localKey ?: $this->getKeyName();

        if (is_null($foreignField)) {
            list(, $caller) = debug_backtrace(false);

            $foreignField = $caller['function'];
        }

        $instance = new $related;

        return new ManyToArrayRelation($instance->newQuery(), $this, $foreignKey, $foreignField, $localKey);
    }
}

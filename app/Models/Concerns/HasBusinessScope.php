<?php

namespace App\Models\Concerns;

use App\Models\Business;
use Illuminate\Database\Eloquent\Builder;

trait HasBusinessScope
{
    /**
     * Boot del trait: agrega global scope y auto-set de business_id
     */
    protected static function bootHasBusinessScope(): void
    {
        // Global scope por negocio actual (si existe)
        static::addGlobalScope('business', function (Builder $builder) {
            // Aquí luego uniremos con el middleware que ponga este valor
            $businessId = app()->bound('currentBusinessId')
                ? app('currentBusinessId')
                : null;

            if ($businessId) {
                $builder->where(
                    $builder->getModel()->getTable() . '.business_id',
                    $businessId
                );
            }
        });

        // Al crear registros, rellenar business_id automáticamente
        static::creating(function ($model) {
            if (empty($model->business_id)) {
                $businessId = app()->bound('currentBusinessId')
                    ? app('currentBusinessId')
                    : null;

                if ($businessId) {
                    $model->business_id = $businessId;
                }
            }
        });
    }

    /**
     * Relación con Business
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Scope local: forBusiness($id)
     */
    public function scopeForBusiness(Builder $query, int|string $businessId): Builder
    {
        return $query->where($this->getTable() . '.business_id', $businessId);
    }

    /**
     * Scope local: forCurrentBusiness()
     * Útil cuando quieras forzar negocio actual manualmente.
     */
    public function scopeForCurrentBusiness(Builder $query): Builder
    {
        $businessId = app()->bound('currentBusinessId')
            ? app('currentBusinessId')
            : null;

        if ($businessId) {
            $query->where($this->getTable() . '.business_id', $businessId);
        }

        return $query;
    }
}

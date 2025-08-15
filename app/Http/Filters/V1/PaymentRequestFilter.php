<?php

namespace App\Http\Filters\V1;

class PaymentRequestFilter extends QueryFilter
{
    protected $sortable = [
        'amount',
        'purpose',
        'status',
        'createdAt' => 'created_at',
        'updatedAt' => 'updated_at',
        'expiresAt' => 'expires_at',
        'paidAt' => 'paid_at',
    ];

    public function status($value)
    {
        if (is_array($value)) {
            return $this->builder->whereIn('status', $value);
        }
        
        return $this->builder->where('status', $value);
    }

    public function amount($value)
    {
        if (is_array($value)) {
            // Handle range queries like ?filter[amount][gte]=100&filter[amount][lte]=500
            foreach ($value as $operator => $amount) {
                switch ($operator) {
                    case 'gte':
                        $this->builder->where('amount', '>=', $amount);
                        break;
                    case 'gt':
                        $this->builder->where('amount', '>', $amount);
                        break;
                    case 'lte':
                        $this->builder->where('amount', '<=', $amount);
                        break;
                    case 'lt':
                        $this->builder->where('amount', '<', $amount);
                        break;
                    case 'eq':
                        $this->builder->where('amount', '=', $amount);
                        break;
                }
            }
            return $this->builder;
        }
        
        return $this->builder->where('amount', $value);
    }

    public function purpose($value)
    {
        // Support wildcard searches
        if (str_contains($value, '*')) {
            $value = str_replace('*', '%', $value);
            return $this->builder->where('purpose', 'LIKE', $value);
        }
        
        return $this->builder->where('purpose', 'LIKE', "%{$value}%");
    }

    public function description($value)
    {
        // Support wildcard searches
        if (str_contains($value, '*')) {
            $value = str_replace('*', '%', $value);
            return $this->builder->where('description', 'LIKE', $value);
        }
        
        return $this->builder->where('description', 'LIKE', "%{$value}%");
    }

    public function currencyCode($value)
    {
        if (is_array($value)) {
            return $this->builder->whereIn('currency_code', $value);
        }
        
        return $this->builder->where('currency_code', $value);
    }

    public function isNegotiable($value)
    {
        return $this->builder->where('is_negotiable', filter_var($value, FILTER_VALIDATE_BOOLEAN));
    }

    public function isExpired($value)
    {
        $isExpired = filter_var($value, FILTER_VALIDATE_BOOLEAN);
        
        if ($isExpired) {
            return $this->builder->where('expires_at', '<=', now())
                                ->where('status', '!=', 'paid');
        }
        
        return $this->builder->where(function($query) {
            $query->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
        });
    }

    public function createdAfter($value)
    {
        return $this->builder->where('created_at', '>=', $value);
    }

    public function createdBefore($value)
    {
        return $this->builder->where('created_at', '<=', $value);
    }

    public function author($value)
    {
        return $this->builder->where('user_id', $value);
    }
}

<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    use ApiResponses;

    protected $policyClass;

    /**
     * Check if a relationship should be included in the response
     */
    public function include(string $relationship): bool 
    {
        $param = request()->get('include');

        if (!isset($param)) {
            return false;
        }

        $includeValues = explode(',', strtolower($param));

        return in_array(strtolower($relationship), $includeValues);
    }

    /**
     * Check if user is able to perform an action on a model
     */
    public function isAble($ability, $targetModel): bool 
    {
        try {
            $this->authorize($ability, $targetModel);
            return true;
        } catch (AuthorizationException $ex) {
            return false;
        }
    }

    /**
     * Get fields to include in response
     */
    protected function getFields(string $resourceType): ?array
    {
        $fields = request()->get('fields');
        
        if (!$fields || !isset($fields[$resourceType])) {
            return null;
        }

        return explode(',', $fields[$resourceType]);
    }

    /**
     * Get sort parameters
     */
    protected function getSortFields(): array
    {
        $sort = request()->get('sort');
        
        if (!$sort) {
            return [];
        }

        return explode(',', $sort);
    }

    /**
     * Get filter parameters
     */
    protected function getFilters(): array
    {
        return request()->get('filter', []);
    }
}

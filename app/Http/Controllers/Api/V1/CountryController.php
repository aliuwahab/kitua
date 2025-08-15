<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\CountryResource;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CountryController extends ApiController
{
    /**
     * Get all countries
     *
     * Retrieve a list of all available countries with their details including currency information.
     * Only active countries are returned by default.
     *
     * @group General
     * @unauthenticated
     *
     * @queryParam active boolean Filter by active status. Default: true. Example: true
     * @queryParam search string Search countries by name or code. Example: Ghana
     *
     * @response 200 {
     *   "data": [
     *     {
     *       "type": "country",
     *       "id": "9d2f8b10-1234-4567-8901-123456789abc",
     *       "attributes": {
     *         "name": "Ghana",
     *         "code": "GH",
     *         "currencyCode": "GHS",
     *         "currencySymbol": "GH₵",
     *         "currencyName": "Ghana Cedi",
     *         "isActive": true,
     *         "createdAt": "2025-08-15T10:15:30.000Z",
     *         "updatedAt": "2025-08-15T10:15:30.000Z"
     *       },
     *       "links": {
     *         "self": "http://localhost/api/v1/countries/9d2f8b10-1234-4567-8901-123456789abc"
     *       }
     *     },
     *     {
     *       "type": "country",
     *       "id": "9d2f8b10-1234-4567-8901-123456789def",
     *       "attributes": {
     *         "name": "Nigeria",
     *         "code": "NG",
     *         "currencyCode": "NGN",
     *         "currencySymbol": "₦",
     *         "currencyName": "Nigerian Naira",
     *         "isActive": true,
     *         "createdAt": "2025-08-15T10:15:30.000Z",
     *         "updatedAt": "2025-08-15T10:15:30.000Z"
     *       },
     *       "links": {
     *         "self": "http://localhost/api/v1/countries/9d2f8b10-1234-4567-8901-123456789def"
     *       }
     *     }
     *   ],
     *   "meta": {
     *     "total": 2,
     *     "count": 2
     *   },
     *   "links": {
     *     "self": "http://localhost/api/v1/countries"
     *   },
     *   "message": "Countries retrieved successfully"
     * }
     *
     * @response 200 scenario="Empty result" {
     *   "data": [],
     *   "meta": {
     *     "total": 0,
     *     "count": 0
     *   },
     *   "links": {
     *     "self": "http://localhost/api/v1/countries"
     *   },
     *   "message": "Countries retrieved successfully"
     * }
     */
    public function index(Request $request): JsonResponse
    {
        $query = Country::query();

        // Filter by active status (default to true)
        $active = $request->boolean('active', true);
        if ($active) {
            $query->active();
        }

        // Search functionality
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $countries = $query->orderBy('name')->get();

        $collection = CountryResource::collection($countries);
        $data = $collection->toArray(request());
        
        // Add meta and links data
        $data['meta'] = [
            'total' => $countries->count(),
            'count' => $countries->count()
        ];
        $data['links'] = [
            'self' => route('countries.index')
        ];
        
        return $this->success('Countries retrieved successfully', $data);
    }

    /**
     * Get country by ID
     *
     * Retrieve detailed information about a specific country by its UUID.
     *
     * @group General
     * @unauthenticated
     *
     * @urlParam country string required The UUID of the country. Example: 9d2f8b10-1234-4567-8901-123456789abc
     *
     * @response 200 {
     *   "data": {
     *     "type": "country",
     *     "id": "9d2f8b10-1234-4567-8901-123456789abc",
     *     "attributes": {
     *       "name": "Ghana",
     *       "code": "GH",
     *       "currencyCode": "GHS",
     *       "currencySymbol": "GH₵",
     *       "currencyName": "Ghana Cedi",
     *       "isActive": true,
     *       "createdAt": "2025-08-15T10:15:30.000Z",
     *       "updatedAt": "2025-08-15T10:15:30.000Z"
     *     },
     *     "links": {
     *       "self": "http://localhost/api/v1/countries/9d2f8b10-1234-4567-8901-123456789abc"
     *     }
     *   },
     *   "message": "Country retrieved successfully"
     * }
     *
     * @response 404 {
     *   "message": "Country not found",
     *   "errors": {
     *     "country": ["The specified country could not be found."]
     *   },
     *   "status": "error"
     * }
     */
    public function show(Country $country): JsonResponse
    {
        $resource = new CountryResource($country);
        return $this->success(
            'Country retrieved successfully',
            $resource->toArray(request())
        );
    }
}

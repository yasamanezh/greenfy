<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * لیست پلن‌های فعال
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $plans = Plan::active()
                ->with(['prices' => function($query) {
                    $query->where('is_active', true);
                }, 'features' => function($query) {
                    $query->wherePivot('is_available', true)
                        ->orderBy('plan_feature.sort_order');
                }])
                ->orderBy('sort_order')
                ->get();

            $plansData = $plans->map(function($plan) {
                return [
                    'id' => $plan->id,
                    'name' => $plan->name,
                    'slug' => $plan->slug,
                    'description' => $plan->description,
                    'color' => $plan->color,
                    'is_popular' => $plan->is_popular,
                    'is_recommended' => $plan->is_recommended,
                    'display_name' => $plan->display_name,
                    'prices' => $plan->prices->map(function($price) {
                        return [
                            'id' => $price->id,
                            'duration' => $price->duration->value,
                            'duration_label' => $price->duration->getLabelText(),
                            'price' => (float) $price->price,
                            'formatted_price' => number_format($price->price) . ' تومان',
                        ];
                    }),
                    'features' => $plan->features->map(function($feature) {
                        return [
                            'id' => $feature->id,
                            'name' => $feature->name,
                            'value' => $feature->pivot->value,
                            'is_available' => $feature->pivot->is_available,
                        ];
                    }),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'لیست پلن‌ها با موفقیت دریافت شد',
                'data' => $plansData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت لیست پلن‌ها',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * دریافت اطلاعات یک پلن خاص
     */
    public function show($id): JsonResponse
    {
        try {
            $plan = Plan::active()
                ->with(['prices' => function($query) {
                    $query->where('is_active', true);
                }, 'features' => function($query) {
                    $query->wherePivot('is_available', true)
                        ->orderBy('plan_feature.sort_order');
                }])
                ->findOrFail($id);

            $planData = [
                'id' => $plan->id,
                'name' => $plan->name,
                'slug' => $plan->slug,
                'description' => $plan->description,
                'color' => $plan->color,
                'is_popular' => $plan->is_popular,
                'is_recommended' => $plan->is_recommended,
                'display_name' => $plan->display_name,
                'prices' => $plan->prices->map(function($price) {
                    return [
                        'id' => $price->id,
                        'duration' => $price->duration->value,
                        'duration_label' => $price->duration->getLabelText(),
                        'price' => (float) $price->price,
                        'formatted_price' => number_format($price->price) . ' تومان',
                    ];
                }),
                'features' => $plan->features->map(function($feature) {
                    return [
                        'id' => $feature->id,
                        'name' => $feature->name,
                        'value' => $feature->pivot->value,
                        'is_available' => $feature->pivot->is_available,
                    ];
                }),
            ];

            return response()->json([
                'success' => true,
                'message' => 'اطلاعات پلن با موفقیت دریافت شد',
                'data' => $planData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'پلن مورد نظر یافت نشد',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

 
}


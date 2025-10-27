<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SmsPackage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SmsPackageController extends Controller
{
    /**
     * لیست پکیج‌های پیامک فعال
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $packages = SmsPackage::active()
                ->orderBy('sort_order')
                ->get();

            $packagesData = $packages->map(function($package) {
                return [
                    'id' => $package->id,
                    'name' => $package->name,
                    'slug' => $package->slug,
                    'description' => $package->description,
                    'sms_count' => $package->sms_count,
                    'validity_days' => $package->validity_days,
                    'price' => (float) $package->price,
                    'discount_price' => (float) $package->discount_price,
                    'discount_percentage' => (float) $package->discount_percentage,
                    'has_discount' => $package->has_discount,
                    'final_price' => (float) $package->final_price,
                    'formatted_price' => $package->formatted_price,
                    'formatted_final_price' => $package->formatted_final_price,
                    'price_per_sms' => (float) $package->price_per_sms,
                    'formatted_price_per_sms' => $package->formatted_price_per_sms,
                    'savings_amount' => (float) $package->savings_amount,
                    'formatted_savings' => $package->formatted_savings,
                    'popularity_badge' => $package->popularity_badge,
                    'popularity_color' => $package->popularity_color,
                    'recommended_for' => $package->getRecommendedFor(),
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'لیست پکیج‌های پیامک با موفقیت دریافت شد',
                'data' => $packagesData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'خطا در دریافت لیست پکیج‌های پیامک',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * دریافت اطلاعات یک پکیج خاص
     */
    public function show($id): JsonResponse
    {
        try {
            $package = SmsPackage::active()->findOrFail($id);

            $packageData = [
                'id' => $package->id,
                'name' => $package->name,
                'slug' => $package->slug,
                'description' => $package->description,
                'sms_count' => $package->sms_count,
                'validity_days' => $package->validity_days,
                'price' => (float) $package->price,
                'discount_price' => (float) $package->discount_price,
                'discount_percentage' => (float) $package->discount_percentage,
                'has_discount' => $package->has_discount,
                'final_price' => (float) $package->final_price,
                'formatted_price' => $package->formatted_price,
                'formatted_final_price' => $package->formatted_final_price,
                'price_per_sms' => (float) $package->price_per_sms,
                'formatted_price_per_sms' => $package->formatted_price_per_sms,
                'savings_amount' => (float) $package->savings_amount,
                'formatted_savings' => $package->formatted_savings,
                'popularity_badge' => $package->popularity_badge,
                'popularity_color' => $package->popularity_color,
                'recommended_for' => $package->getRecommendedFor(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'اطلاعات پکیج پیامک با موفقیت دریافت شد',
                'data' => $packageData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'پکیج مورد نظر یافت نشد',
                'error' => $e->getMessage(),
            ], 404);
        }
    }

  
}


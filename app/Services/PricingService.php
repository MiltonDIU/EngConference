<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\Profile;
use App\Models\Domain;
use Carbon\Carbon;

class PricingService
{
    /**
     * Calculate the cost for a single abstract
     * 
     * @param Profile $profile The user's profile
     * @param \App\Models\Paper|null $paper The paper being checked out
     * @return array Contains base_price, discount, final_price, currency, stage, authors_count
     */
    public static function calculatePaperCost(Profile $profile, ?\App\Models\Paper $paper = null)
    {
        $settings = Setting::pluck('value', 'key');
        
        $countryName = $profile->country->name ?? '';
        
        // 1. Determine base currency prefix
        $prefix = self::determineCurrencyPrefix($countryName);
        
        // 2. Determine if earlybird or regular
        $currentDate = Carbon::now();
        $earlyBirdSetting = $settings['early_registration_last_date'] ?? null;
        
        try {
            $earlyBirdDateLimit = $earlyBirdSetting ? Carbon::parse($earlyBirdSetting) : Carbon::parse('2000-01-01');
        } catch (\Exception $e) {
            \Log::warning("PricingService: Invalid early_registration_last_date format: '{$earlyBirdSetting}'. Falling back to regular price.");
            $earlyBirdDateLimit = Carbon::parse('2000-01-01'); // Force regular
        }
        
        $stage = $earlyBirdDateLimit->gt($currentDate) ? 'earlybird' : 'regular';
        
        // 3. Construct setting key
        $settingKey = "{$prefix}_{$stage}_price";
        if (!isset($settings[$settingKey])) {
            \Log::error("PricingService: Missing pricing setting key: '{$settingKey}'");
        }
        $basePrice = (float) ($settings[$settingKey] ?? 0);
        
        // 4. Check for special domain discount (DEN Users)
        $isSpecialDiscountTrue = ($settings['special_discount_is_true'] ?? 'false') === 'true';
        $finalPrice = $basePrice;
        
        if ($isSpecialDiscountTrue) {
            $userEmail = $profile->user->email ?? '';
            $emailParts = explode('@', $userEmail);
            $domain = end($emailParts);
            
            $allowedDomains = Domain::where('status', 1)->pluck('domain_name')->toArray();
            
            // If the user matches an allowed domain, they get the strict domain discount flat price
            if (in_array($domain, $allowedDomains)) {
                $flatDomainDiscountPrice = (float) ($settings['selected_domain_discount'] ?? $basePrice);
                // Ensure we don't accidentally increase the price if the discount is misconfigured
                if ($flatDomainDiscountPrice < $basePrice) {
                    $finalPrice = $flatDomainDiscountPrice;
                }
            }
        }
        
        $currencyCode = 'USD';
        if ($prefix === 'bdt') $currencyCode = 'BDT';
        elseif ($prefix === 'inr') $currencyCode = 'INR';
        elseif ($prefix === 'eur') $currencyCode = 'EUR';

        $authorCount = 1;
        if ($paper !== null) {
            $authorCount = max(1, $paper->authors()->count());
        }

        $totalBasePrice = $basePrice * $authorCount;
        $totalFinalPrice = $finalPrice * $authorCount;

        return [
            'base_price' => $totalBasePrice,
            'discount' => $totalBasePrice - $totalFinalPrice,
            'final_price' => $totalFinalPrice,
            'individual_base_price' => $basePrice,
            'individual_discount' => $basePrice - $finalPrice,
            'individual_final_price' => $finalPrice,
            'currency' => $currencyCode,
            'stage' => $stage,
            'authors_count' => $authorCount
        ];
    }
    
    /**
     * Calculate the cost for a participant (non-author)
     * 
     * @param Profile $profile
     * @return array Contains final_price and currency
     */
    public static function calculateParticipantPrice(Profile $profile)
    {
        $settings = Setting::pluck('value', 'key');
        $countryName = $profile->country->name ?? '';
        $prefix = self::determineCurrencyPrefix($countryName);
        
        $settingKey = "{$prefix}_participant_price";
        if (!isset($settings[$settingKey])) {
            \Log::error("PricingService: Missing participant pricing setting key: '{$settingKey}'");
        }
        $price = (float) ($settings[$settingKey] ?? 0);
        
        $currencyCode = strtoupper($prefix);

        return [
            'final_price' => $price,
            'currency' => $currencyCode
        ];
    }

    /**
     * Recalculates the total amount due for a user and updates their profile.
     * This is the "Source of Truth" for profiles.pay_amount.
     * 
     * @param Profile $profile
     * @return void
     */
    public static function updateProfileTotalDue(Profile $profile)
    {
        if ($profile->payment_status == '1') {
            return;
        }

        $totalAmount = 0;
        $currency = 'USD'; // Default

        if (!$profile->is_author) {
            // Logic for Participant Only
            $pricing = self::calculateParticipantPrice($profile);
            $totalAmount = $pricing['final_price'];
            $currency = $pricing['currency'];
        } else {
            // Logic for Author (Sum of all their papers)
            $papers = \App\Models\Paper::where('user_id', $profile->user_id)->get();
            
            foreach ($papers as $paper) {
                // Determine the cost based on the number of co-authors
                // We use the same Prefix and Stage as participants
                $paperPricing = self::calculatePaperCost($profile, $paper);
                $totalAmount += $paperPricing['final_price'];
                $currency = $paperPricing['currency'];
            }
        }

        $profile->update([
            'pay_amount' => $totalAmount,
            'currency' => $currency
        ]);
    }
    
    /**
     * Determines whether to charge BDT, INR, EUR, or USD
     * 
     * @param string $countryName Profile country name
     * @return string Prefix
     */
    public static function determineCurrencyPrefix($countryName)
    {
        $countryName = strtolower(trim($countryName ?? ''));
        
        if (empty($countryName)) {
            return 'usd';
        }
        
        if ($countryName === 'bangladesh') {
            return 'bdt';
        }
        
        if ($countryName === 'india') {
            return 'inr';
        }
        
        $europeanCountries = [
            'albania', 'andorra', 'austria', 'belarus', 'belgium', 'bosnia and herzegovina', 
            'bulgaria', 'croatia', 'cyprus', 'czech republic', 'denmark', 'estonia', 
            'finland', 'france', 'germany', 'greece', 'hungary', 'iceland', 'ireland', 
            'italy', 'kosovo', 'latvia', 'liechtenstein', 'lithuania', 'luxembourg', 
            'malta', 'moldova', 'monaco', 'montenegro', 'netherlands', 'north macedonia', 
            'norway', 'poland', 'portugal', 'romania', 'russia', 'san marino', 
            'serbia', 'slovakia', 'slovenia', 'spain', 'sweden', 'switzerland', 
            'ukraine', 'united kingdom', 'vatican city'
        ];
        
        if (in_array($countryName, $europeanCountries)) {
            return 'eur';
        }
        
        return 'usd';
    }
}

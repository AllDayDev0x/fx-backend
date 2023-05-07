<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SettingSeeder::class);
        $this->call(DemoSeeder::class);
        $this->call(BellNotificationSeeder::class);
        $this->call(CommissionSeeder::class);
        $this->call(NotificationCountSeeder::class);
        $this->call(PageDemoSeeder::class);
        $this->call(PaypalSeeder::class);
        $this->call(VerifiedBadgeSeeder::class);
        $this->call(MultiLanguageSeeder::class);
        $this->call(UserWelcomeSeeder::class);
        $this->call(VideoCallCommissionSeeder::class);
        $this->call(WatermarkLogoSeeder::class);
        $this->call(LiveVideoSeeder::class);
        $this->call(SocialLinkSeeder::class);
        $this->call(CoinPaymentSeeder::class);
        $this->call(ReferralSeeder::class);
        $this->call(ChatAssetSeeder::class);
        $this->call(PlaceholderSeeder::class);
        $this->call(StripeEnableSeeder::class);
        $this->call(AdsSeeder::class);
        $this->call(CCBillSeeder::class);
        $this->call(CartSeeder::class);
        $this->call(CaptchaSeeder::class);
        $this->call(OnlyWalletSeeder::class);
        $this->call(TokenSeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(SymbolPositionSeeder::class);
        $this->call(LiveCallSeeder::class);
    }
}

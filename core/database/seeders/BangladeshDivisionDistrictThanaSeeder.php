<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\District;
use App\Models\Thana;
use Illuminate\Database\Seeder;

class BangladeshDivisionDistrictThanaSeeder extends Seeder
{
    /** 8 divisions: name_en => [name_bn, sort] */
    protected $divisions = [
        'Barishal'   => ['বরিশাল', 1],
        'Chattogram' => ['চট্টগ্রাম', 2],
        'Dhaka'      => ['ঢাকা', 3],
        'Khulna'     => ['খুলনা', 4],
        'Mymensingh' => ['ময়মনসিংহ', 5],
        'Rajshahi'   => ['রাজশাহী', 6],
        'Rangpur'    => ['রংপুর', 7],
        'Sylhet'     => ['সিলেট', 8],
    ];

    /** division name_en => [district name_en => name_bn] */
    protected $districts = [
        'Barishal'   => ['Barguna' => 'বরগুনা', 'Barisal' => 'বরিশাল', 'Bhola' => 'ভোলা', 'Jhalokati' => 'ঝালকাঠি', 'Patuakhali' => 'পটুয়াখালী', 'Pirojpur' => 'পিরোজপুর'],
        'Chattogram' => ['Bandarban' => 'বান্দরবান', 'Brahmanbaria' => 'ব্রাহ্মণবাড়িয়া', 'Chandpur' => 'চাঁদপুর', 'Chittagong' => 'চট্টগ্রাম', 'Comilla' => 'কুমিল্লা', 'Cox\'s Bazar' => 'কক্সবাজার', 'Feni' => 'ফেনী', 'Khagrachhari' => 'খাগড়াছড়ি', 'Lakshmipur' => 'লক্ষ্মীপুর', 'Noakhali' => 'নোয়াখালী', 'Rangamati' => 'রাঙ্গামাটি'],
        'Dhaka'      => ['Dhaka' => 'ঢাকা', 'Faridpur' => 'ফরিদপুর', 'Gazipur' => 'গাজীপুর', 'Gopalganj' => 'গোপালগঞ্জ', 'Kishoreganj' => 'কিশোরগঞ্জ', 'Madaripur' => 'মাদারীপুর', 'Manikganj' => 'মানিকগঞ্জ', 'Munshiganj' => 'মুন্সিগঞ্জ', 'Narayanganj' => 'নারায়ণগঞ্জ', 'Narsingdi' => 'নরসিংদী', 'Rajbari' => 'রাজবাড়ী', 'Shariatpur' => 'শরীয়তপুর', 'Tangail' => 'টাঙ্গাইল'],
        'Khulna'     => ['Bagerhat' => 'বাগেরহাট', 'Chuadanga' => 'চুয়াডাঙ্গা', 'Jessore' => 'যশোর', 'Jhenaidah' => 'ঝিনাইদহ', 'Khulna' => 'খুলনা', 'Kushtia' => 'কুষ্টিয়া', 'Magura' => 'মাগুরা', 'Meherpur' => 'মেহেরপুর', 'Narail' => 'নড়াইল', 'Satkhira' => 'সাতক্ষীরা'],
        'Mymensingh' => ['Jamalpur' => 'জামালপুর', 'Mymensingh' => 'ময়মনসিংহ', 'Netrokona' => 'নেত্রকোণা', 'Sherpur' => 'শেরপুর'],
        'Rajshahi'   => ['Bogra' => 'বগুড়া', 'Joypurhat' => 'জয়পুরহাট', 'Naogaon' => 'নওগাঁ', 'Natore' => 'নাটোর', 'Chapainawabganj' => 'চাঁপাইনবাবগঞ্জ', 'Pabna' => 'পাবনা', 'Rajshahi' => 'রাজশাহী', 'Sirajganj' => 'সিরাজগঞ্জ'],
        'Rangpur'    => ['Dinajpur' => 'দিনাজপুর', 'Gaibandha' => 'গাইবান্ধা', 'Kurigram' => 'কুড়িগ্রাম', 'Lalmonirhat' => 'লালমনিরহাট', 'Nilphamari' => 'নীলফামারী', 'Panchagarh' => 'পঞ্চগড়', 'Rangpur' => 'রংপুর', 'Thakurgaon' => 'ঠাকুরগাঁও'],
        'Sylhet'     => ['Habiganj' => 'হবিগঞ্জ', 'Moulvibazar' => 'মৌলভীবাজার', 'Sunamganj' => 'সুনামগঞ্জ', 'Sylhet' => 'সিলেট'],
    ];

    public function run()
    {
        $thanaData = $this->loadThanaData();
        $divisionIds = [];

        foreach ($this->divisions as $divEn => $divMeta) {
            $d = Division::firstOrCreate(
                ['name_en' => $divEn],
                ['name_bn' => $divMeta[0], 'sort_order' => $divMeta[1]]
            );
            $divisionIds[$divEn] = $d->id;
        }

        $districtIds = [];
        $so = 0;
        foreach ($this->districts as $divEn => $distList) {
            $divisionId = $divisionIds[$divEn] ?? null;
            if (!$divisionId) {
                continue;
            }
            foreach ($distList as $distEn => $distBn) {
                $district = District::firstOrCreate(
                    ['name_en' => $distEn, 'division_id' => $divisionId],
                    ['name_bn' => $distBn, 'sort_order' => ++$so]
                );
                $districtIds[$distEn] = $district->id;
            }
        }

        foreach ($thanaData as $distEn => $thanas) {
            $districtId = $districtIds[$distEn] ?? null;
            if (!$districtId) {
                continue;
            }
            $sort = 0;
            foreach ($thanas as $t) {
                $en = $t['en'] ?? '';
                $bn = $t['bn'] ?? '';
                if ($en === '' && $bn === '') {
                    continue;
                }
                Thana::firstOrCreate(
                    ['district_id' => $districtId, 'name_en' => $en ?: $bn],
                    ['name_bn' => $bn ?: $en, 'sort_order' => ++$sort]
                );
            }
        }
    }

    protected function loadThanaData(): array
    {
        $jsonPath = database_path('bangladesh_thanas_full.json');
        if (is_file($jsonPath)) {
            $json = json_decode(file_get_contents($jsonPath), true);
            if (is_array($json)) {
                return $json;
            }
        }
        $path = app_path('Http/Helpers/bangladesh_thanas.php');
        if (!is_file($path)) {
            return [];
        }
        $data = require $path;
        return is_array($data) ? $data : [];
    }
}

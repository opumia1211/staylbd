<?php

namespace Database\Seeders;

use App\Models\Division;
use App\Models\District;
use App\Models\Thana;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run()
    {
        $divisionsData = [
            ['name_en' => 'Dhaka', 'name_bn' => 'ঢাকা', 'sort_order' => 1],
            ['name_en' => 'Chittagong', 'name_bn' => 'চট্টগ্রাম', 'sort_order' => 2],
            ['name_en' => 'Rajshahi', 'name_bn' => 'রাজশাহী', 'sort_order' => 3],
            ['name_en' => 'Khulna', 'name_bn' => 'খুলনা', 'sort_order' => 4],
            ['name_en' => 'Barisal', 'name_bn' => 'বরিশাল', 'sort_order' => 5],
            ['name_en' => 'Sylhet', 'name_bn' => 'সিলেট', 'sort_order' => 6],
            ['name_en' => 'Rangpur', 'name_bn' => 'রংপুর', 'sort_order' => 7],
            ['name_en' => 'Mymensingh', 'name_bn' => 'ময়মনসিংহ', 'sort_order' => 8],
        ];

        $divisionIdByName = [];
        foreach ($divisionsData as $i => $d) {
            $div = Division::firstOrCreate(
                ['name_en' => $d['name_en']],
                ['name_bn' => $d['name_bn'], 'sort_order' => $d['sort_order'], 'status' => 1]
            );
            $divisionIdByName[$d['name_en']] = $div->id;
        }

        $districtToDivision = getDefaultDistrictToDivisionMap();
        $districtsEnBn = getDefaultDistrictsEnBn();
        $districtIdByName = [];

        foreach ($districtsEnBn as $idx => $row) {
            $en = $row['en'] ?? '';
            $bn = $row['bn'] ?? '';
            if ($en === '') {
                continue;
            }
            $divId = $districtToDivision[$en] ?? $divisionIdByName['Dhaka'];
            $district = District::firstOrCreate(
                ['name_en' => $en, 'division_id' => $divId],
                ['name_bn' => $bn, 'sort_order' => $idx + 1, 'status' => 1]
            );
            $districtIdByName[$en] = $district->id;
        }

        $thanasByDistrict = getDefaultThanasByDistrict();
        foreach ($thanasByDistrict as $districtNameEn => $list) {
            $districtId = $districtIdByName[$districtNameEn] ?? null;
            if (!$districtId || !is_array($list)) {
                continue;
            }
            $sort = 0;
            foreach ($list as $t) {
                $en = $t['en'] ?? $t['name_en'] ?? '';
                $bn = $t['bn'] ?? $t['name_bn'] ?? '';
                if ($en === '' && $bn === '') {
                    continue;
                }
                if ($en === '') {
                    $en = $bn;
                }
                if ($bn === '') {
                    $bn = $en;
                }
                Thana::firstOrCreate(
                    ['district_id' => $districtId, 'name_en' => $en],
                    ['name_bn' => $bn, 'sort_order' => ++$sort, 'status' => 1]
                );
            }
        }

        $this->command->info('Locations seeded: 8 divisions, 64 districts, and thanas from bangladesh_thanas.');
    }
}

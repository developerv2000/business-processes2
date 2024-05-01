<?php

namespace Database\Seeders;

use App\Models\CountryCode;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountryCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = [
            'HK',
            'MN',
            'KE',
            'ET',
            'BY',
            'MD',
            'RU',
            'UA',
            'AZ',
            'AM',
            'GE',
            'LB',
            'DO',
            'JM',
            'LV',
            'GT',
            'NI',
            'VN',
            'KH',
            'MM',
            'PH',
            'PE',
            'CL',
            'AL',
            'RS',
            'AF',
            'KZ',
            'KG',
            'TJ',
            'TM',
            'UZ',
            'AU',
            'NZ',
            'NF',
            'CN',
            'KP',
            'KR',
            'MO',
            'TW',
            'JP',
            'BI',
            'DJ',
            'ZM',
            'ZW',
            'KM',
            'MU',
            'MG',
            'MW',
            'MZ',
            'RE',
            'RW',
            'SC',
            'SO',
            'TZ',
            'UG',
            'ER',
            'BG',
            'HU',
            'PL',
            'RO',
            'SK',
            'CZ',
            'AB',
            'OS',
            'BH',
            'IL',
            'JO',
            'IQ',
            'YE',
            'QA',
            'CY',
            'KW',
            'AE',
            'OM',
            'PS',
            'SA',
            'SY',
            'TR',
            'BJ',
            'BF',
            'GM',
            'GH',
            'GN',
            'GW',
            'CV',
            'CI',
            'LR',
            'MR',
            'ML',
            'NE',
            'NG',
            'SH',
            'SN',
            'SL',
            'TG',
            'AT',
            'BE',
            'DE',
            'LI',
            'LU',
            'MC',
            'NL',
            'FR',
            'CH',
            'CX',
            'IO',
            'CC',
            'UM',
            'HM',
            'TF',
            'AI',
            'AG',
            'AW',
            'BS',
            'BB',
            'BZ',
            'BQ',
            'VG',
            'VI',
            'HT',
            'GP',
            'GD',
            'DM',
            'CU',
            'CW',
            'MQ',
            'MS',
            'KY',
            'TC',
            'PR',
            'BL',
            'MF',
            'VC',
            'KN',
            'LC',
            'SX',
            'TT',
            'VU',
            'NC',
            'PG',
            'SB',
            'FJ',
            'GU',
            'KI',
            'MH',
            'FM',
            'NR',
            'PW',
            'MP',
            'AS',
            'NU',
            'CK',
            'PN',
            'WS',
            'TK',
            'TO',
            'TV',
            'WF',
            'PF',
            'BM',
            'GL',
            'CA',
            'PM',
            'US',
            'DZ',
            'EG',
            'EH',
            'LY',
            'MA',
            'SD',
            'TN',
            'SS',
            'GG',
            'DK',
            'JE',
            'IE',
            'IS',
            'LT',
            'NO',
            'IM',
            'GB',
            'FO',
            'FI',
            'SE',
            'SJ',
            'AX',
            'EE',
            'HN',
            'CR',
            'MX',
            'PA',
            'SV',
            'AO',
            'GA',
            'CM',
            'CG',
            'CD',
            'ST',
            'CF',
            'TD',
            'GQ',
            'BN',
            'ID',
            'LA',
            'MY',
            'SG',
            'TH',
            'TL',
            'AR',
            'BO',
            'BR',
            'VE',
            'GY',
            'CO',
            'PY',
            'SR',
            'UY',
            'FK',
            'GF',
            'EC',
            'AD',
            'BA',
            'GI',
            'GR',
            'ES',
            'IT',
            'MT',
            'VA',
            'PT',
            'MK',
            'SM',
            'SI',
            'HR',
            'ME',
            'BW',
            'LS',
            'YT',
            'NA',
            'SZ',
            'ZA',
            'BD',
            'BT',
            'IN',
            'IR',
            'MV',
            'NP',
            'PK',
            'LK',
            'BV',
            'GS',
            'AQ'
        ];

        for ($i = 0; $i < count($name); $i++) {
            $instance = new CountryCode();
            $instance->name = $name[$i];
            $instance->save();
        }
    }
}

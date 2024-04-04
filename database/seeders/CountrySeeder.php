<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $name = [
            'AFGHANISTAN',
            'ALBANIA',
            'ALGERIA',
            'ANDORRA',
            'ANGOLA',
            'ANTIGUA, AND BARBUDA',
            'ARGENTINA',
            'ARMENIA',
            'AUSTRALIA',
            'AUSTRIA',
            'AZERBAIJAN',
            'THE BAHAMAS',
            'BAHRAIN',
            'BANGLADESH',
            'BARBADOS',
            'BELARUS',
            'BELGIUM',
            'BELIZE',
            'BENIN',
            'BHUTAN',
            'BOLIVIA',
            'BOSNIA AND HERZEGOVINA',
            'BOTSWANA',
            'BRAZIL',
            'BRUNEI',
            'BULGARIA',
            'BURKINA FASO',
            'BURUNDI',
            'CAMBODIA',
            'CAMEROON',
            'CANADA',
            'CAPE VERDE',
            'CENTRAL AFRICAN REPUBLIC',
            'CHAD',
            'CHILE',
            'CHINA',
            'COLOMBIA',
            'COMOROS',
            'CONGO, REPUBLIC OF THE',
            'CONGO, DEMOCRATIC REPUBLIC OF THE',
            'COSTA RICA',
            'COTE D\'IVOIRE',
            'CROATIA',
            'CUBA',
            'CYPRUS',
            'CZECH REPUBLIC',
            'DENMARK',
            'DJIBOUTI',
            'DOMINICA',
            'DOMINICAN REPUBLIC',
            'EAST TIMOR (TIMOR-LESTE)',
            'ECUADOR',
            'EGYPT',
            'EL SALVADOR',
            'EQUATORIAL GUINEA',
            'ERITREA',
            'ESTONIA',
            'ETHIOPIA',
            'FIJI',
            'FINLAND',
            'FRANCE',
            'GABON',
            'THE GAMBIA',
            'GEORGIA',
            'GERMANY',
            'GHANA',
            'GREECE',
            'GRENADA',
            'GUATEMALA',
            'GUINEA',
            'GUINEA-BISSAU',
            'GUYANA',
            'HAITI',
            'HONDURAS',
            'HUNGARY',
            'ICELAND',
            'INDIA',
            'INDONESIA',
            'IRAN',
            'IRAQ',
            'IRELAND',
            'ISRAEL',
            'ITALY',
            'JAMAICA',
            'JAPAN',
            'JORDAN',
            'KAZAKHSTAN',
            'KENYA',
            'KIRIBATI',
            'KOREA, NORTH',
            'KOREA, SOUTH',
            'KOSOVO',
            'KUWAIT',
            'KYRGYZSTAN',
            'LAOS',
            'LATVIA',
            'LEBANON',
            'LESOTHO',
            'LIBERIA',
            'LIBYA',
            'LIECHTENSTEIN',
            'LITHUANIA',
            'LUXEMBOURG',
            'MACEDONIA',
            'MADAGASCAR',
            'MALAWI',
            'MALAYSIA',
            'MALDIVES',
            'MALI',
            'MALTA',
            'MARSHALL ISLANDS',
            'MAURITANIA',
            'MAURITIUS',
            'MEXICO',
            'MICRONESIA, FEDERATED STATES OF',
            'MOLDOVA',
            'MONACO',
            'MONGOLIA',
            'MONTENEGRO',
            'MOROCCO',
            'MOZAMBIQUE',
            'MYANMAR (BURMA)',
            'NAMIBIA',
            'NAURU',
            'NEPAL',
            'NETHERLANDS',
            'NEW ZEALAND',
            'NICARAGUA',
            'NIGER',
            'NIGERIA',
            'NORWAY',
            'OMAN',
            'PAKISTAN',
            'PALAU',
            'PANAMA',
            'PAPUA NEW GUINEA',
            'PARAGUAY',
            'PERU',
            'PHILIPPINES',
            'POLAND',
            'PORTUGAL',
            'QATAR',
            'ROMANIA',
            'RUSSIA',
            'RWANDA',
            'SAINT KITTS AND NEVIS',
            'SAINT LUCIA',
            'SAINT VINCENT AND THE GRENADINES',
            'SAMOA',
            'SAN MARINO',
            'SAO TOME AND PRINCIPE',
            'SAUDI ARABIA',
            'SENEGAL',
            'SERBIA',
            'SEYCHELLES',
            'SIERRA LEONE',
            'SINGAPORE',
            'SLOVAKIA',
            'SLOVENIA',
            'SOLOMON ISLANDS',
            'SOMALIA',
            'SOUTH AFRICA',
            'SOUTH SUDAN',
            'SPAIN',
            'SRI LANKA',
            'SUDAN',
            'SURINAME',
            'SWAZILAND',
            'SWEDEN',
            'SWITZERLAND',
            'SYRIA',
            'TAIWAN',
            'TAJIKISTAN',
            'TANZANIA',
            'THAILAND',
            'TOGO',
            'TONGA',
            'TRINIDAD AND TOBAGO',
            'TUNISIA',
            'TURKEY',
            'TURKMENISTAN',
            'TUVALU',
            'UGANDA',
            'UKRAINE',
            'UNITED ARAB EMIRATES',
            'UNITED KINGDOM',
            'UNITED STATES OF AMERICA',
            'URUGUAY',
            'UZBEKISTAN',
            'VANUATU',
            'VATICAN CITY (HOLY SEE)',
            'VENEZUELA',
            'VIETNAM',
            'YEMEN',
            'ZAMBIA',
            'ZIMBABWE',
        ];

        for ($i = 0; $i < count($name); $i++) {
            $item = new Country();
            $item->name = $name[$i];
            $item->save();
        }
    }
}

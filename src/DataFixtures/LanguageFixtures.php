<?php

namespace App\DataFixtures;

use App\Entity\Language;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class LanguageFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $langFlatData = self::getData();
        $languages = [];
        foreach ($langFlatData as $langData) {
            $language = new Language();
            $language->setName($langData[0]);
            $language->setCode($langData[1]);
            $language->setGoogleCode($langData[2]);
            $language->setRtl($langData[3]);
            $manager->persist($language);
            $languages[$langData[1]] = $language;
        }
        $manager->flush();
        
        foreach ($langFlatData as $langData) {
            $code = $langData[1];
            $this->addReference(self::buildReferenceName($code), $languages[$code]);
        }
    }
    
    public static function buildReferenceName($code)
    {
        return sprintf('language-%s', $code);
    }
    
    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public static function getData()
    {
        return [
            ['Abkhazian','abk',null,0],
            ['Afar','aar',null,0],
            ['Afrikaans','afr',null,0],
            ['Akan','aka',null,0],
            ['Albanian','alb','sq',0],
            ['Amharic','amh','am',0],
            ['Arabic (Standard)','arb','ar',1],
            ['Aragonese','arg',null,0],
            ['Armenian','arm',null,0],
            ['Assamese','asm',null,0],
            ['Avaric','ava',null,0],
            ['Avestan','ave',null,1],
            ['Aymara','aym',null,0],
            ['Azerbaijani','aze',null,0],
            ['Bambara','bam',null,0],
            ['Bashkir','bak',null,0],
            ['Basque','baq','eu',0],
            ['Belarusian','bel',null,0],
            ['Bengali','ben','bn',0],
            ['Bihari languages','bih',null,0],
            ['Bislama','bis',null,0],
            ['Bosnian','bos',null,0],
            ['Breton','bre',null,0],
            ['Bulgarian','bul','bg',0],
            ['Burmese','bur',null,0],
            ['Catalan, Valencian','cat',null,0],
            ['Chamorro','cha',null,0],
            ['Chechen','che',null,0],
            ['Chichewa, Chewa, Nyanja','nya',null,0],
            ['Chinese','chi','ch-CN',0],
            ['Chuvash','chv',null,0],
            ['Cornish','cor',null,0],
            ['Corsican','cos',null,0],
            ['Cree','cre',null,0],
            ['Croatian','hrv','hr',0],
            ['Czech','cze','cs',0],
            ['Danish','dan','da',0],
            ['Divehi, Dhivehi, Maldivian','div',null,1],
            ['Dutch, Flemish','dut','nl',0],
            ['Dzongkha','dzo',null,0],
            ['English','eng',null,0],
            ['Esperanto','epo',null,0],
            ['Estonian','est','et',0],
            ['Ewe','ewe',null,0],
            ['Faroese','fao',null,0],
            ['Fijian','fij',null,0],
            ['Finnish','fin','fi',0],
            ['French','fre','fr',0],
            ['Fulah','ful',null,1],
            ['Galician','glg',null,0],
            ['Georgian','geo',null,0],
            ['German','ger','de',0],
            ['Greek, Modern (1453-)','gre','el',0],
            ['Guarani','grn',null,0],
            ['Gujarati','guj','gu',0],
            ['Haitian, Haitian Creole','hat',null,0],
            ['Hausa','hau',null,1],
            ['Hebrew','heb','iw',1],
            ['Herero','her',null,0],
            ['Hindi','hin','hi',0],
            ['Hiri Motu','hmo',null,0],
            ['Hungarian','hun','hu',0],
            ['Indonesian','ind','id',0],
            ['Interlingue, Occidental','ile',null,0],
            ['Irish','gle',null,0],
            ['Igbo','ibo',null,0],
            ['Inupiaq','ipk',null,0],
            ['Ido','ido',null,0],
            ['Icelandic','ice','is',0],
            ['Italian','ita','it',0],
            ['Inuktitut','iku',null,0],
            ['Japanese','jpn','ja',0],
            ['Javanese','jav',null,0],
            ['Kalaallisut, Greenlandic','kal',null,0],
            ['Kannada','kan','kn',0],
            ['Kanuri','kau',null,0],
            ['Kashmiri','kas',null,1],
            ['Kazakh','kaz',null,0],
            ['Central Khmer','khm',null,0],
            ['Kikuyu, Gikuyu','kik',null,0],
            ['Kinyarwanda','kin',null,0],
            ['Kirghiz, Kyrgyz','kir',null,0],
            ['Komi','kom',null,0],
            ['Kongo','kon',null,0],
            ['Korean','kor','ko',0],
            ['Kurdish','kur',null,1],
            ['Kuanyama, Kwanyama','kua',null,0],
            ['Latin','lat',null,0],
            ['Luxembourgish, Letzeburgesch','ltz',null,0],
            ['Ganda','lug',null,0],
            ['Limburgan, Limburger, Limburgish','lim',null,0],
            ['Lingala','lin',null,0],
            ['Lao','lao',null,0],
            ['Lithuanian','lit','lt',0],
            ['Luba-Katanga','lub',null,0],
            ['Latvian','lav',null,0],
            ['Manx','glv',null,0],
            ['Macedonian','mac',null,0],
            ['Malagasy','mlg',null,0],
            ['Malay','may','ms',0],
            ['Malayalam','mal','ml',0],
            ['Maltese','mlt',null,0],
            ['Maori','mao',null,0],
            ['Marathi','mar','mr',0],
            ['Marshallese','mah',null,0],
            ['Mongolian','mon',null,0],
            ['Nauru','nau',null,0],
            ['Navajo, Navaho','nav',null,0],
            ['North Ndebele','nde',null,0],
            ['Nepali','nep',null,0],
            ['Ndonga','ndo',null,0],
            ['Norwegian Bokmål','nob',null,0],
            ['Norwegian Nynorsk','nno',null,0],
            ['Norwegian','nor','no',0],
            ['Sichuan Yi, Nuosu','iii',null,0],
            ['South Ndebele','nbl',null,0],
            ['Occitan','oci',null,0],
            ['Ojibwa','oji',null,0],
            ['Oromo','orm',null,0],
            ['Oriya','ori',null,0],
            ['Ossetian, Ossetic','oss',null,0],
            ['Panjabi, Punjabi','pan',null,0],
            ['Pali','pli',null,0],
            ['Persian','per',null,1],
            ['Polish','pol','pl',0],
            ['Pashto, Pushto','pus',null,0],
            ['Portuguese','por','pt',0],
            ['Quechua','que',null,0],
            ['Romansh','roh',null,0],
            ['Rundi','run',null,0],
            ['Romanian, Moldavian, Moldovan','rum','ro',0],
            ['Russian','rus','ru',0],
            ['Sanskrit','san',null,0],
            ['Sardinian','srd',null,0],
            ['Sindhi','snd',null,0],
            ['Northern Sami','sme',null,0],
            ['Samoan','smo',null,0],
            ['Sango','sag',null,0],
            ['Serbian','srp','sr',0],
            ['Gaelic, Scottish Gaelic','gla',null,0],
            ['Shona','sna',null,0],
            ['Sinhala, Sinhalese','sin',null,0],
            ['Slovak','slo','sk',0],
            ['Slovenian','slv','sl',0],
            ['Somali','som',null,0],
            ['Southern Sotho','sot',null,0],
            ['Spanish, Castilian','spa','es',0],
            ['Sundanese','sun',null,0],
            ['Swahili','swa','sw',0],
            ['Swati','ssw',null,0],
            ['Swedish','swe','sv',0],
            ['Tamil','tam','ta',0],
            ['Telugu','tel','te',0],
            ['Tajik','tgk',null,0],
            ['Thai','tha','th',0],
            ['Tigrinya','tir',null,0],
            ['Tibetan','tib',null,0],
            ['Turkmen','tuk',null,0],
            ['Tagalog','tgl','fil',0],
            ['Tswana','tsn',null,0],
            ['Tonga (Tonga Islands)','ton',null,0],
            ['Turkish','tur','tr',0],
            ['Tsonga','tso',null,0],
            ['Tatar','tat',null,0],
            ['Twi','twi',null,0],
            ['Tahitian','tah',null,0],
            ['Uighur, Uyghur','uig',null,0],
            ['Ukrainian','ukr','uk',0],
            ['Urdu','urd','ur',1],
            ['Uzbek','uzb',null,0],
            ['Venda','ven',null,0],
            ['Vietnamese','vie','vi',0],
            ['Volapük','vol',null,0],
            ['Walloon','wln',null,0],
            ['Welsh','wel','cy',0],
            ['Wolof','wol',null,0],
            ['Western Frisian','fry',null,0],
            ['Xhosa','xho',null,0],
            ['Yiddish','yid',null,1],
            ['Yoruba','yor',null,0],
            ['Zhuang, Chuang','zha',null,0],
            ['Zulu','zul',null,0]
        ];
    }
    
    public function getDependencies()
    {
        return [
            GroupFixtures::class,
        ];
    }
}

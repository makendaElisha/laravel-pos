<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

use function PHPUnit\Framework\isNull;

class KilwaStockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kilwa = Shop::where('name', Shop::KILWA)->first();
        $currentCode = DB::table('products')
            ->select(DB::raw('MAX(CAST(code AS SIGNED)) as max_code'))
            ->value('max_code');

        foreach ($this->kilwaStock() as $kilwaProduct) {
            $desiredProductName = trim($kilwaProduct['name']); // Trim the desired product name
            $dbProduct = Product::whereRaw('TRIM(name) = ?', [$desiredProductName])->first();
            // $dbProduct = Product::where('name', '=', $kilwaProduct['name'])->first();

            if (is_null($dbProduct)) {
                $currentCode += 1;

                $newProduct = Product::create([
                    'code' => $currentCode,
                    // 'name' => $kilwaProduct['name'],
                    'name' => $desiredProductName,
                    'items_in_box' => 1,
                    'sell_price' => 0,
                    'quantity' => 0
                ]);

                foreach (Shop::get() as $shop) {
                    ShopProduct::create([
                        'shop_id' => $shop->id,
                        'product_id' => $newProduct->id,
                        'quantity' => 0,
                        'sell_price' => $shop->name == Shop::KILWA ? $kilwaProduct['price'] : 0,
                    ]);
                }
            } else {
                \Log::info('********************************');
                \Log::info($dbProduct->id);
                \Log::info($dbProduct->name);
                \Log::info($kilwaProduct['name']);
                \Log::info('********************************');
            }
        }
    }

    private function kilwaStock()
    {
        return [
            ["name" => "BOUGIE GROUPE TGR", "price" => "1000"],
            ["name" => "TORCHE QX-2288", "price" => "1800"],
            ["name" => "BOBINE BOXER", "price" => "21000"],
            ["name" => "FILE COURANT 1 METRE 2 FILES", "price" => "500"],
            ["name" => "CRISHE", "price" => "18000"],
            ["name" => "FER A REPASSE", "price" => "20000"],
            ["name" => "SACHETA NUMERO 9 (JEUX)", "price" => "5000"],
            ["name" => "SACHETA NUMERO 7 (JEUX)", "price" => "5500"],
            ["name" => "INSTALATION DT", "price" => "18000"],
            ["name" => "SANGLE", "price" => "20000"],
            ["name" => "SEGMENT DT 150", "price" => "5000"],
            ["name" => "G DE 3 CASSEROL", "price" => "18000"],
            ["name" => "SACHETA NUMERO 10 (JEUX)", "price" => "3000"],
            ["name" => "PIGNON DATTAQUE TVS STAR", "price" => "2000"],
            ["name" => "MOYEU AVANT BOXER", "price" => "30000"],
            ["name" => "CDI BOXER", "price" => "5000"],
            ["name" => "AXE QUICK DT", "price" => "12000"],
            ["name" => "PINSCEAUX 1 POUSSE", "price" => "1000"],
            ["name" => "RADIO 2235", "price" => "25000"],
            ["name" => "SONILEX SL-B3", "price" => "18500"],
            ["name" => "RADIO SONILEX 883", "price" => "15000"],
            ["name" => "SONITEX ST-6297", "price" => "20000"],
            ["name" => "VALISE NUMERO 1", "price" => "0"],
            ["name" => "PNEUX MOTO ORGINAL", "price" => "0"],
            ["name" => "PNEUX MOTO 2 EME CHOIX", "price" => "30000"],
            ["name" => "BOOTE (PAIR)", "price" => "13000"],
            ["name" => "VALISE LUX NUMERO 1", "price" => "50000"],
            ["name" => "VALISE LUX NUMERO 2", "price" => "45000"],
            ["name" => "VALISE LUX NUMERO 3", "price" => "40000"],
            ["name" => "VALISE LUX NUMERO 4", "price" => "35000"],
            ["name" => "VALISE OMEGA NUMERO 1", "price" => "35000"],
            ["name" => "VALISE OMEGA NUMERO 2", "price" => "26000"],
            ["name" => "VALISE OMEGA NUMERO 3", "price" => "24000"],
            ["name" => "VALISE OMEGA NUMERO 4", "price" => "20000"],
            ["name" => "BECHE", "price" => "5500"],
            ["name" => "PANEAUX 40", "price" => "50000"],
            ["name" => "MOTEUR DT", "price" => "561000"],
            ["name" => "BETAX", "price" => "750"],
            ["name" => "PANDILE PETIT MODEL 7500FC", "price" => "7500"],
            ["name" => "BIC OBAMA", "price" => "100"],
            ["name" => "SAVONS TABLE", "price" => "3000"],
            ["name" => "SCIE A METEAUX COMPLET", "price" => "7000"],
            ["name" => "TASSE THE", "price" => "2000"],
            ["name" => "MARKER", "price" => "250"],
            ["name" => "SILICONE", "price" => "3000"],
            ["name" => "CYLINDRE DT 150 CG", "price" => "45000"],
            ["name" => "CYLINDRE DT 125", "price" => "40000"],
            ["name" => "BOBINE DALLIMAGE DT", "price" => "25000"],
            ["name" => "ROBINET DT", "price" => "3000"],
            ["name" => "CARBURATEUR DT 150", "price" => "21000"],
            ["name" => "TORCHE 1 PILE", "price" => "1500"],
            ["name" => "FROTOIRE", "price" => "6000"],
            ["name" => "BRIQUET", "price" => "300"],
            ["name" => "SACHETA NUMERO 1 (JEUX)", "price" => "9500"],
            ["name" => "SACHETA NUMERO 2 (JEUX)", "price" => "8500"],
            ["name" => "SACHETA NUMERO 3 (JEUX)", "price" => "7500"],
            ["name" => "SACHETA NUMERO 8 (JEUX)", "price" => "5800"],
            ["name" => "G DE 5 CASSEROL", "price" => "20000"],
            ["name" => "SACHETA NUMERO 4 (JEUX)", "price" => "7000"],
            ["name" => "SACHETA NUMERO 5 (JEUX)", "price" => "6500"],
            ["name" => "SACHETA NUMERO 6 (JEUX)", "price" => "6000"],
            ["name" => "AMPOULE BATERRY RONDE", "price" => "4500"],
            ["name" => "TUBE BATTERY PETIT MODEL", "price" => "3500"],
            ["name" => "TUBE BATERRY GRAND MODEL", "price" => "6000"],
            ["name" => "SCIE VELO", "price" => "15000"],
            ["name" => "PORTE MANET COMP TVS DEM", "price" => "20000"],
            ["name" => "MIROIR PETIT MODEL", "price" => "1000"],
            ["name" => "TETRICE", "price" => "2500"],
            ["name" => "ROULETTE PENT GRAND MODEL", "price" => "4500"],
            ["name" => "LAMPE TORCHE 3 PILE TGR NOUVO MODEL", "price" => "6000"],
            ["name" => "TORCHE (KIMUNI) PETIT MODEL", "price" => "1500"],
            ["name" => "STATE TIGER", "price" => "7500"],
            ["name" => "STARTE 2.5", "price" => "0"],
            ["name" => "AV", "price" => "1500"],
            ["name" => "TORCHE KIMUNI (2000) GRAND MODEL", "price" => "3000"],
            ["name" => "CHAINE MOTO", "price" => "8000"],
            ["name" => "GUIDON VELO", "price" => "17000"],
            ["name" => "SELE VELO RANGER", "price" => "12000"],
            ["name" => "GENTE VELO", "price" => "18000"],
            ["name" => "TAPIS (EN METRE)", "price" => "5000"],
            ["name" => "RAYONS VELO", "price" => "200"],
            ["name" => "POMPE VELO", "price" => "2500"],
            ["name" => "GRAISSE", "price" => "3000"],
            ["name" => "PILE TUGER", "price" => "300"],
            ["name" => "BILLE VELO", "price" => "10"],
            ["name" => "C 5287", "price" => "1500"],
            ["name" => "BAGUETTES", "price" => "200"],
            ["name" => "SERIRE MECO", "price" => "8000"],
            ["name" => "DISQUE A COUPE", "price" => "5000"],
            ["name" => "ANTENE RADIO", "price" => "2000"],
            ["name" => "PAPIER COLANT", "price" => "3500"],
            ["name" => "CHARNNIERE BRONZE", "price" => "7000"],
            ["name" => "BOUGIE BOXER", "price" => "1250"],
            ["name" => "VERRE EAU BOITTE", "price" => "12000"],
            ["name" => "RALLONGE", "price" => "9000"],
            ["name" => "CHARNIERE 3 POUSSE", "price" => "300"],
            ["name" => "POMELLE PORTE", "price" => "1500"],
            ["name" => "PANEAUX 60 W", "price" => "75000"],
            ["name" => "PANEAUX 200 W", "price" => "250000"],
            ["name" => "PANEAUX 30 W", "price" => "40000"],
            ["name" => "PANEAUX 20 W", "price" => "25000"],
            ["name" => "PANEAUX 80 W", "price" => "100000"],
            ["name" => "PANEAUX 100 W", "price" => "140000"],
            ["name" => "PANEAUX 50 W", "price" => "58000"],
            ["name" => "PANEAUX 5 W", "price" => "10000"],
            ["name" => "PANEAUX 10 W", "price" => "12000"],
            ["name" => "FOURCHE VELO AVANT", "price" => "9000"],
            ["name" => "JEUX DE GUDON VELO", "price" => "3000"],
            ["name" => "FOURCHE ARR VELO", "price" => "5000"],
            ["name" => "PINCE ARRIERE VELO", "price" => "5000"],
            ["name" => "SEL 5 RESSORT VELO", "price" => "17000"],
            ["name" => "2C (AXE CENTRAL)", "price" => "4500"],
            ["name" => "RADIO SONILEX SL-920", "price" => "18000"],
            ["name" => "RADIO SONITEC ST-22 92", "price" => "23000"],
            ["name" => "D 718", "price" => "400"],
            ["name" => "RADIO SL-882", "price" => "17000"],
            ["name" => "THERMOS THE 1 LITRE", "price" => "13000"],
            ["name" => "THERMOS THE 2 LITRES", "price" => "16000"],
            ["name" => "THERMOS BUKARI 2 LITRE", "price" => "20000"],
            ["name" => "TOPAZ G", "price" => "36000"],
            ["name" => "ELEGANCE G DE 4", "price" => "15000"],
            ["name" => "SIRAGE LIQUIDE", "price" => "1000"],
            ["name" => "SIRAGE SOLIDE", "price" => "1000"],
            ["name" => "CUVETTE REAL", "price" => "500"],
            ["name" => "CACHE PHAR Bx", "price" => "3000"],
            ["name" => "CACHE PHAR TVS", "price" => "3000"],
            ["name" => "CULASSE DT", "price" => "35000"],
            ["name" => "PEDALE VELO (PAIRE)", "price" => "5000"],
            ["name" => "KALAI T 18", "price" => "4000"],
            ["name" => "CHAMBRE AIR MOTO 300/17", "price" => "5500"],
            ["name" => "KIT BAC", "price" => "1500"],
            ["name" => "SCIE DT", "price" => "6000"],
            ["name" => "SOUPAPE TVS STARD", "price" => "3000"],
            ["name" => "AXE LEVIER VIT BX (SELECTEUR)", "price" => "5000"],
            ["name" => "CABLE AMBRIAGE TVS", "price" => "3000"],
            ["name" => "CLE DE BOUGIE", "price" => "1500"],
            ["name" => "PLATEAUX COPL TVS STARS", "price" => "50000"],
            ["name" => "EL DT", "price" => "20000"],
            ["name" => "RADIO MOTO", "price" => "32000"],
            ["name" => "JOIN SIMPLE DT 125", "price" => "1000"],
            ["name" => "REGLAGE TEND BX", "price" => "3000"],
            ["name" => "ROULETTE PENT PETIT MODEL", "price" => "3000"],
            ["name" => "JOIN COMPLET BOXER", "price" => "3500"],
            ["name" => "DISQUE TVS", "price" => "4500"],
            ["name" => "ROULEMENT 6002", "price" => "2000"],
            ["name" => "CABLE AMBRI BX", "price" => "2500"],
            ["name" => "CABLE ACCELE BX", "price" => "2500"],
            ["name" => "MOYEU ARR BX", "price" => "30000"],
            ["name" => "SOUS CULBITEUR DT STAND", "price" => "4500"],
            ["name" => "RESSORT [DIVER ]", "price" => "1500"],
            ["name" => "CALCULATRICE", "price" => "6000"],
            ["name" => "TABLEAU DE BORT TVS", "price" => "15000"],
            ["name" => "CARBURATEUR TIGER", "price" => "7500"],
            ["name" => "HUILE DE FREIN PETIT FORMAT", "price" => "2000"],
            ["name" => "BOUGIE TVS 125", "price" => "1500"],
            ["name" => "DISQUE DT", "price" => "4500"],
            ["name" => "LAME DE SCIE", "price" => "3500"],
            ["name" => "AXE ARRIERE BOXER", "price" => "2500"],
            ["name" => "CONTACTEUR SP DOBL TABLEAU BX", "price" => "8500"],
            ["name" => "ROULEMENT 628", "price" => "1000"],
            ["name" => "POMEL FENETRE", "price" => "1000"],
            ["name" => "AXE PIGNON BX SIMPLE", "price" => "15000"],
            ["name" => "PEDALE FREIN BOXER", "price" => "5000"],
            ["name" => "CHARNIERE 2.5 (250 FC)", "price" => "250"],
            ["name" => "PISTON DT 125", "price" => "10000"],
            ["name" => "ROULETTE 300 FC", "price" => "300"],
            ["name" => "QUICK DT", "price" => "8000"],
            ["name" => "CADENAT NUMERO 263", "price" => "1500"],
            ["name" => "VERNIE", "price" => "6000"],
            ["name" => "PINSCEAUX 2 POUSSE", "price" => "1500"],
            ["name" => "13005", "price" => "3000"],
            ["name" => "LASSER (SACHET)", "price" => "1500"],
            ["name" => "SEGMENT YOG BX ++", "price" => "4000"],
            ["name" => "PORTE DISQUE SP DT", "price" => "8500"],
            ["name" => "TORCHE 3 PILES CROYON", "price" => "1300"],
            ["name" => "PISTON BOXER YOG ++", "price" => "9000"],
            ["name" => "AXE ARR REAL", "price" => "2000"],
            ["name" => "TIGE FR BX", "price" => "2000"],
            ["name" => "LAMPE TORCHE 3 PILE CROYON 1000FC", "price" => "1000"],
            ["name" => "TIGE SEL", "price" => "1000"],
            ["name" => "DISOLUTION PETIT FORMAT", "price" => "300"],
            ["name" => "ETAIN EN METRE", "price" => "500"],
            ["name" => "CADENAT 40 MM", "price" => "4500"],
            ["name" => "TRIANO VELO", "price" => "500"],
            ["name" => "CLE VELO 9 TROUS", "price" => "1000"],
            ["name" => "DISOLUTION GRAND FORMAT", "price" => "1000"],
            ["name" => "POMPE MOTO PETIT FORMAT", "price" => "3000"],
            ["name" => "SERIRE MECO SANS POIGNET", "price" => "7000"],
            ["name" => "CADENAT 50 MM", "price" => "7000"],
            ["name" => "VOLANT MOTEUR DT", "price" => "15000"],
            ["name" => "RADIO ST-3284", "price" => "22000"],
            ["name" => "TABLEAU ANCIEN MODELE BX", "price" => "10000"],
            ["name" => "PLATEAU COPL BX", "price" => "30000"],
            ["name" => "AXE PIGNON DT", "price" => "7000"],
            ["name" => "CHAMBRE AIRE VELO", "price" => "4500"],
            ["name" => "CACHE PIGNON BOXER", "price" => "3500"],
            ["name" => "TDA 7379", "price" => "8000"],
            ["name" => "LAMPE TORCHE 3 PILE CROYON 1800", "price" => "1800"],
            ["name" => "SOUPAPE SIMBA BX", "price" => "3000"],
            ["name" => "VILBREQUIN DT 150", "price" => "50000"],
            ["name" => "BIEL DT", "price" => "9000"],
            ["name" => "FILS PANNEAUX (200FC) EN METRE", "price" => "350"],
            ["name" => "CAL FREIN BOXXER", "price" => "3500"],
            ["name" => "POMPE MOTO GRAND FORMAT", "price" => "4000"],
            ["name" => "PAPIER MERIE MESURE EN BIC", "price" => "500"],
            ["name" => "CABLE AMBRIAGE DT", "price" => "3000"],
            ["name" => "PISTON BOXXER SIMBA ++", "price" => "7000"],
            ["name" => "VILBREQUIN BOXER YOG", "price" => "42000"],
            ["name" => "BIEL BOXER", "price" => "12000"],
            ["name" => "PIVOT BOXER", "price" => "3000"],
            ["name" => "PORTE SCIE BX", "price" => "13000"],
            ["name" => "PISTON DT 150 ++", "price" => "10000"],
            ["name" => "BOULO VIDANGER BX", "price" => "1000"],
            ["name" => "PISTON TVS STAR JL ++", "price" => "8000"],
            ["name" => "TIRE CHAINE BX", "price" => "2000"],
            ["name" => "RAILL TENDER BX", "price" => "3500"],
            ["name" => "COUVRE SEL LOCAL", "price" => "5500"],
            ["name" => "MANETTE SP TVS (PAIR)", "price" => "4000"],
            ["name" => "FLASHER BX", "price" => "3500"],
            ["name" => "SOUPAPE DT 150", "price" => "3500"],
            ["name" => "SOUPAPE DT 125", "price" => "3500"],
            ["name" => "BOURAGE MOTEUR", "price" => "2000"],
            ["name" => "FILTRE A HUIL HIAS", "price" => "5000"],
            ["name" => "FILTRE A HUILE NOAH", "price" => "5000"],
            ["name" => "TUBE PETIT 3500 FC", "price" => "3500"],
            ["name" => "ROULOT PANEAU EN METRE", "price" => "200"],
            ["name" => "CONA SAWAN", "price" => "150"],
            ["name" => "CADENAT 70 MM", "price" => "7500"],
            ["name" => "HOUE", "price" => "9000"],
            ["name" => "HUILE HYDROLIQUE", "price" => "6000"],
            ["name" => "SAE 5O 5LITRES", "price" => "20000"],
            ["name" => "SAE 40 1LITRE", "price" => "7500"],
            ["name" => "SAE 50 1 LITRE", "price" => "7500"],
            ["name" => "GUIGNOTANT DT", "price" => "3500"],
            ["name" => "GARDE BOUT DT", "price" => "15000"],
            ["name" => "GARDE BOUT BX", "price" => "15000"],
            ["name" => "PLAQUETTE TELEVISION", "price" => "45000"],
            ["name" => "TIGE GUIDON VELO", "price" => "2000"],
            ["name" => "MOYEU REAL", "price" => "5000"],
            ["name" => "SPANNE 14/15", "price" => "1000"],
            ["name" => "CLOUE TANDER VELO", "price" => "1000"],
            ["name" => "BIC RASOIRE", "price" => "500"],
            ["name" => "REGULATEUR DT", "price" => "6000"],
            ["name" => "LEVIER DE VITESSE TVS", "price" => "4000"],
            ["name" => "CABLE ACCEL TVS", "price" => "3000"],
            ["name" => "CHAINE TENDEUR TVS", "price" => "3000"],
            ["name" => "FLASQUE DT", "price" => "12000"],
            ["name" => "PIGNON D'ATAQUE BX", "price" => "2000"],
            ["name" => "SEGMENT DT 125", "price" => "5000"],
            ["name" => "QUIGNOTANT BOXER (PAIR)", "price" => "5000"],
            ["name" => "CONTACTEUR COMPLET TVS", "price" => "20000"],
            ["name" => "TABLEAU NOUVEAU DOUBLE", "price" => "13000"],
            ["name" => "PANDILE GRAND MODELE", "price" => "12000"],
            ["name" => "TIGE FREIND TVS", "price" => "2000"],
            ["name" => "CHARNIERE 2 POUSSE", "price" => "250"],
            ["name" => "CHARNNIERE 1.5 POUSSE 150 FC", "price" => "150"],
            ["name" => "CABLE INSTALATION BX", "price" => "13000"],
            ["name" => "RETIEN HUILE BX", "price" => "1500"],
            ["name" => "VIS ANTENNE", "price" => "500"],
            ["name" => "CLIBITEUR BAJAJ", "price" => "15000"],
            ["name" => "PLATEAU SIMPLE BX", "price" => "20000"],
            ["name" => "SCIE BOXER", "price" => "9000"],
            ["name" => "SCIE TVS 125 VICTOR", "price" => "9000"],
            ["name" => "PIVOT TVS", "price" => "3000"],
            ["name" => "RAYON BX 300/16", "price" => "150"],
            ["name" => "CLOUS NUMERO 4 = 1 Kg", "price" => "4000"],
            ["name" => "RETIEN HUILE DT", "price" => "1500"],
            ["name" => "JOIN SP BX", "price" => "1500"],
            ["name" => "PORTE MANETTE CP BX", "price" => "14000"],
            ["name" => "PISTON JL BX ++", "price" => "8000"],
            ["name" => "FERODOS BOXER", "price" => "4500"],
            ["name" => "AMPOULE PHAR", "price" => "1000"],
            ["name" => "RETROVISEUR TVS", "price" => "5000"],
            ["name" => "DISQUE BOXER", "price" => "4000"],
            ["name" => "BOUGIE DT", "price" => "1500"],
            ["name" => "FERODOS TVS YOG", "price" => "4000"],
            ["name" => "PORTE DISQUE SP BX", "price" => "9000"],
            ["name" => "SEGMENT TVS STAR", "price" => "3500"],
            ["name" => "RETROVISEUR BX", "price" => "5500"],
            ["name" => "SCIE TENDEUR TVS", "price" => "5000"],
            ["name" => "CDI DT", "price" => "5000"],
            ["name" => "SEGMENT TVS 125", "price" => "4000"],
            ["name" => "CONTACTEUR COPL ANCIEN MODEL", "price" => "8000"],
            ["name" => "RESSORT QUICK BOXER", "price" => "2000"],
            ["name" => "PNEU DIAMOND", "price" => "16000"],
            ["name" => "GARDE BOUE VELO (PAIR)", "price" => "10000"],
            ["name" => "ROULEMENT 6203", "price" => "1500"],
            ["name" => "ROULEMENT 6301", "price" => "2000"],
            ["name" => "ROULEMENT 6300", "price" => "2000"],
            ["name" => "ROULEMENT 6204", "price" => "2000"],
            ["name" => "ROULEMENT 6302", "price" => "2000"],
            ["name" => "ROULEMENT 6303", "price" => "2000"],
            ["name" => "ROULEMENT 6304", "price" => "2000"],
            ["name" => "ROULEMENT 6200", "price" => "1500"],
            ["name" => "ROULEMENT 6004", "price" => "2000"],
            ["name" => "ROULEMENT 6202", "price" => "1500"],
            ["name" => "ROULEMENT 6201", "price" => "1500"],
            ["name" => "ROULEMENT 6000", "price" => "1500"],
            ["name" => "LEVIER DE VITESSE DT", "price" => "4500"],
            ["name" => "TIR CHAINE VELO", "price" => "75"],
            ["name" => "PORTE DISQUE COPL DT", "price" => "12000"],
            ["name" => "PORTE MANETTE COPL DT", "price" => "10000"],
            ["name" => "PORTE MANET", "price" => "14000"],
            ["name" => "PIGNO D;ATT T VS", "price" => "2000"],
            ["name" => "ROBINET BX", "price" => "4000"],
            ["name" => "QUICK BX", "price" => "8500"],
            ["name" => "LAMETONDESE", "price" => "3000"],
            ["name" => "PNEU JUMBO", "price" => "65000"],
            ["name" => "CYLINDRE TVS STAR JL", "price" => "42000"],
            ["name" => "AMORTISEUR DT", "price" => "45000"],
            ["name" => "BLOC MOTEUR PAIRE", "price" => "150000"],
            ["name" => "CYLINDRE BX SIMBA", "price" => "35000"],
            ["name" => "QUIGNOTANT DT", "price" => "3500"],
            ["name" => "400V 150", "price" => "3000"],
            ["name" => "C 2837", "price" => "2500"],
            ["name" => "400 V 100", "price" => "3500"],
            ["name" => "D 20 58", "price" => "1500"],
            ["name" => "35 V 3300", "price" => "4000"],
            ["name" => "TDA 7266", "price" => "4000"],
            ["name" => "300 V 3300", "price" => "3000"],
            ["name" => "TDA 2822", "price" => "4000"],
            ["name" => "D 50 38", "price" => "2500"],
            ["name" => "L 78 05", "price" => "1500"],
            ["name" => "C 5297", "price" => "4000"],
            ["name" => "SD 20 58", "price" => "4000"],
            ["name" => "S 13 003", "price" => "3000"],
            ["name" => "450 V 220", "price" => "4000"],
            ["name" => "A 11 86", "price" => "4000"],
            ["name" => "KA 22 06", "price" => "1500"],
            ["name" => "LA 41 92", "price" => "3000"],
            ["name" => "TEA 20 25", "price" => "1500"],
            ["name" => "IRFZ 46N", "price" => "3000"],
            ["name" => "IRF 10 10", "price" => "2500"],
            ["name" => "VOLUME 7 PATTE", "price" => "1500"],
            ["name" => "E 13 005-2", "price" => "3000"],
            ["name" => "IRF 32 05", "price" => "2500"],
            ["name" => "L 78 06", "price" => "1500"],
            ["name" => "400 V 22", "price" => "2000"],
            ["name" => "STRW", "price" => "4000"],
            ["name" => "TDA 2030", "price" => "1500"],
            ["name" => "TIP 41", "price" => "3500"],
            ["name" => "IRF 640", "price" => "3500"],
            ["name" => "D 10 47", "price" => "3500"],
            ["name" => "K 50", "price" => "1500"],
            ["name" => "VOLUME 3 PATTE", "price" => "1500"],
            ["name" => "LM 358", "price" => "2000"],
            ["name" => "TDA 61 07", "price" => "3000"],
            ["name" => "CABLE INST TVS STAR", "price" => "12000"],
            ["name" => "VOLUME 6 PATTE", "price" => "1500"],
            ["name" => "400 V 33", "price" => "2500"],
            ["name" => "5WR 22J", "price" => "4500"],
            ["name" => "PLATEAU TVS 125", "price" => "30000"],
            ["name" => "VC 3843", "price" => "2000"],
            ["name" => "TA 82 05", "price" => "4000"],
            ["name" => "JRC 45 58", "price" => "3500"],
            ["name" => "TDA 73 77", "price" => "8000"],
            ["name" => "CD 62 82", "price" => "3000"],
            ["name" => "KA 35 25", "price" => "2500"],
            ["name" => "T P 42", "price" => "3500"],
            ["name" => "2 SC 52 00", "price" => "3000"],
            ["name" => "KA 35 24", "price" => "2500"],
            ["name" => "TA 82 10", "price" => "3000"],
            ["name" => "LA 46 01", "price" => "3000"],
            ["name" => "TEA 1523", "price" => "4000"],
            ["name" => "SG 35 24", "price" => "1500"],
            ["name" => "TA 82 27", "price" => "2500"],
            ["name" => "KIA 62 82", "price" => "3000"],
            ["name" => "CULASSE COPL BX", "price" => "130000"],
            ["name" => "AMPOULE FEU DE POSITION", "price" => "600"],
            ["name" => "ALBRA CAM DT", "price" => "14000"],
            ["name" => "PISTON JL TVS 125 ++", "price" => "8000"],
            ["name" => "DECAMETRE", "price" => "10000"],
            ["name" => "PANEAUX 300 W", "price" => "320000"],
            ["name" => "PORTE BAGAGE MOTO", "price" => "16000"],
            ["name" => "RAILL TENDER TVS", "price" => "3500"],
            ["name" => "CARBURATEUR TVS", "price" => "22000"],
            ["name" => "PORTE DISQUE COPL BX", "price" => "16000"],
            ["name" => "CARBIRATEUR ASTRA COREA 2.5", "price" => "9000"],
            ["name" => "SONNERIE MOTOS", "price" => "3000"],
            ["name" => "LEVIER DE VITESSE BX", "price" => "3500"],
            ["name" => "QUIGNOTANT TVS", "price" => "5000"],
            ["name" => "SOCKET BOUGIE", "price" => "2500"],
            ["name" => "SOCKET AMPOULE PHAR", "price" => "2500"],
            ["name" => "FLASQUE BX", "price" => "15000"],
            ["name" => "CHAINE TENDEUR BX", "price" => "3000"],
            ["name" => "GIDON BX", "price" => "7000"],
            ["name" => "AMORTISEUR TVS", "price" => "60000"],
            ["name" => "FEU DE POSITION BX", "price" => "6500"],
            ["name" => "KIT CARBURATEUR", "price" => "2500"],
            ["name" => "PHAR COPL BX", "price" => "14000"],
            ["name" => "STATE tiger 2.5", "price" => "8500"],
            ["name" => "SACHETA NUMERO 10", "price" => "4500"],
            ["name" => "PNEU VELO PETIT 20X2.125", "price" => "8000"],
            ["name" => "PEDALE FREIND TVS", "price" => "5500"],
            ["name" => "SCIE TVS STAR 100", "price" => "9000"],
            ["name" => "CONTACTEUR DOUBLE TABLEAU COPL BX", "price" => "14000"],
            ["name" => "THERMOS THE 3.2 LITRE", "price" => "18000"],
            ["name" => "RADIO ST 6297", "price" => "24000"],
            ["name" => "CHAINE VELO", "price" => "6500"],
            ["name" => "PORTE MANETTE COPL TVS", "price" => "20000"],
            ["name" => "C2837", "price" => "4000"],
            ["name" => "TORCHE LAMPE 3 PILE TGR ANCIEN MODEL", "price" => "5000"],
            ["name" => "TORCHE 1 PILE CRAYON", "price" => "800"],
            ["name" => "BOBINE ALLIMAGE TVS", "price" => "21000"],
            ["name" => "D 669", "price" => "4000"],
            ["name" => "STR 66 54", "price" => "3500"],
            ["name" => "IRF 840", "price" => "3000"],
            ["name" => "STR 56 53", "price" => "5500"],
            ["name" => "C 26 55", "price" => "2000"],
            ["name" => "R.F 3205", "price" => "3000"],
            ["name" => "REGULATEUR BOXER", "price" => "10000"],
            ["name" => "TDA 20 50", "price" => "1500"],
            ["name" => "ST 30 03", "price" => "3000"],
            ["name" => "STR 6750", "price" => "4000"],
            ["name" => "SHEET CRISTAL", "price" => "25"],
            ["name" => "PISTON GROUPE 2.5", "price" => "8000"],
            ["name" => "PNEU NISH", "price" => "30000"],
            ["name" => "GROUPE APPOLOS", "price" => "200000"],
            ["name" => "RADIO S-L B3 USB/SD", "price" => "18000"],
            ["name" => "THERMOS THE 1.9 LITRE", "price" => "13000"],
            ["name" => "AXE LEVIER DE VIT DT (SEL)", "price" => "6000"],
            ["name" => "ROULOT FIL COURANT PAR 1 METRE", "price" => "400"],
            ["name" => "CABLE INSTALATION DT", "price" => "18000"],
            ["name" => "PORTE SCIE RIDER", "price" => "12000"],
            ["name" => "PNEU VELO PETI 16*2.125", "price" => "7000"],
            ["name" => "pneu vÃ©lo petit 14*2.125", "price" => "6000"],
            ["name" => "PORTE DISQUE COMP TVS", "price" => "20000"],
            ["name" => "PORTE SCIE TVS STAR", "price" => "13000"],
            ["name" => "PHAR TVS", "price" => "10500"],
            ["name" => "AXE PIGNOIN TVS", "price" => "7000"],
            ["name" => "REGLAGE TENDEUR TVS", "price" => "3500"],
            ["name" => "GARDE CHAINE", "price" => "20000"],
            ["name" => "BOULLON N 17 et N 19", "price" => "1000"],
            ["name" => "CAOUCHOU POSE PIED", "price" => "3000"],
            ["name" => "BOURAGE AMORTISSEUR BX", "price" => "2500"],
            ["name" => "FEU DE POSITION TVS", "price" => "6500"],
            ["name" => "KALAI T20", "price" => "4000"],
            ["name" => "CLOUS N     PRIX Par Kg", "price" => "6000"],
            ["name" => "SEGMENT SIMBA BOXER", "price" => "3000"],
            ["name" => "ROULEMENT 6003", "price" => "2000"],
            ["name" => "VISE CADRE", "price" => "750"],
            ["name" => "BOULLON VIDAGEUR TVS STAR", "price" => "2000"],
            ["name" => "TUYAU CARBURATEUR", "price" => "500"],
            ["name" => "AMPOULE QUIGNOTANT", "price" => "500"],
            ["name" => "CYLINDRE BOXER J ET L", "price" => "37000"],
            ["name" => "PORTE DISQUE SP TVS STARD", "price" => "10000"],
            ["name" => "TIRE CHAINE TVS", "price" => "5000"],
            ["name" => "JOIN DE QUILAS SP TVS", "price" => "1500"],
            ["name" => "VILBREQUIN TVS", "price" => "43000"],
            ["name" => "transmission comp SB BX", "price" => "20000"],
            ["name" => "TRANSMISSION COMP JL", "price" => "23000"],
            ["name" => "ALBRA CAME BOXER", "price" => "12000"],
            ["name" => "ALBRA CAME TVS STAR", "price" => "14000"],
            ["name" => "CABLE ACCELERATEUR DT", "price" => "3000"],
            ["name" => "CYLINDRE TVS STARD DEM", "price" => "42000"],
            ["name" => "REGLAGE TEND TVS DEM", "price" => "3500"],
            ["name" => "SOUPAPE TVS 125", "price" => "3500"],
            ["name" => "CYLINDRE TVS HLX 125", "price" => "45000"],
            ["name" => "SOUPAPE TVS HLX DEM", "price" => "3500"],
            ["name" => "JOINT COMPLET TVS", "price" => "3500"],
            ["name" => "SEGMENT BOXER JetL", "price" => "3500"],
            ["name" => "PLATEAU SP TVS STARD", "price" => "30000"],
            ["name" => "AMORTISEUR BOXER", "price" => "45000"],
            ["name" => "RESSORT QUICK TVS", "price" => "2000"],
            ["name" => "SEGMENT ASTRA COREA 2.5", "price" => "5000"],
            ["name" => "ROBINET TVS STAR", "price" => "5000"],
            ["name" => "ROBINET GROUPE", "price" => "3500"],
            ["name" => "AXE LEVIER VIT TVS (SELC)", "price" => "4500"],
            ["name" => "VILBREQUIN JL BX", "price" => "38000"],
            ["name" => "CADENAT 30 mm", "price" => "4000"],
            ["name" => "CADENAT 60 mm", "price" => "7500"],
            ["name" => "VILBREQUIN TVS 125", "price" => "50000"],
            ["name" => "THERMOS MET 2L", "price" => "17000"],
            ["name" => "CLIBITUER TVS", "price" => "13000"],
            ["name" => "REGULATEUR TVS", "price" => "23000"],
            ["name" => "CYLINDRE TVS YOG", "price" => "50000"],
            ["name" => "bonjour", "price" => "1"],
            ["name" => "ARBRE ACAM BX", "price" => "0"],
            ["name" => "BOBINE BX YOG", "price" => "1"],
            ["name" => "DISQUE BX YOG", "price" => "4500"],
            ["name" => "CARBURATEUR BX DOOM", "price" => "21000"],
            ["name" => "RESSORT FREIN RONDE", "price" => "1250"],
            ["name" => "BOBINE ALLUM TVS 125DEMAR", "price" => "25000"],
            ["name" => "COUVRE SEL ORIGINAL", "price" => "8500"],
            ["name" => "TUYAU D ECHAPPEMENT", "price" => "70000"],
            ["name" => "BIEN", "price" => "7"],
            ["name" => "8002", "price" => "8000"],
            ["name" => "CYLINDRE YOG BX", "price" => "40000"],
            ["name" => "VILBREQUIN TVS YOG", "price" => "50000"],
            ["name" => "RAYON DT", "price" => "6000"],
            ["name" => "COLLECTEUR", "price" => "8000"],
            ["name" => "PORTE DISQUE COMP YOG", "price" => "18000"],
            ["name" => "WWW", "price" => "2"],
            ["name" => "DISQUE BX JL", "price" => "3000"],
            ["name" => "PISTON TVS STAR JL +", "price" => "8000"],
            ["name" => "PISTON DC BX ++", "price" => "7500"],
            ["name" => "PISTON DC BX +", "price" => "7500"],
            ["name" => "FERODO TVS TVS", "price" => "3500"],
            ["name" => "LEVIER DE FREIN BX JL", "price" => "4250"],
            ["name" => "PISTON JL BX +", "price" => "8000"],
            ["name" => "PISTON TVS125 JL +", "price" => "8000"],
            ["name" => "PISTON BX YOG +", "price" => "9000"],
            ["name" => "SOUPAPE BX JL", "price" => "2500"],
            ["name" => "SEGMENT YOG BX +", "price" => "4000"],
            ["name" => "PISTON TVS STAR YOG ++", "price" => "7500"],
            ["name" => "PISTON TVS STAR YOG +", "price" => "7500"],
            ["name" => "PISTON BX SIMBA +", "price" => "7000"],
            ["name" => "PORTE DISQUE SP DOOM", "price" => "7000"],
            ["name" => "ARBRE ACAM TVS STAR", "price" => "9000"],
            ["name" => "SOUPAPE 2.5 GROUPE", "price" => "2500"],
            ["name" => "SOUPAPE YOG BX", "price" => "3500"],
            ["name" => "BOUCHON RESER BX", "price" => "3500"],
            ["name" => "AXE ARR TVS STAR", "price" => "2000"],
            ["name" => "PORTE DISQUE COMPT TVS 125", "price" => "15000"],
            ["name" => "INJECTEUR LG 505 186FA", "price" => "40000"],
            ["name" => "INJECTEUR COURT 5.5", "price" => "40000"],
            ["name" => "BOBINE 2.5 GROUPE", "price" => "5500"],
            ["name" => "SOUPAPE 5.5 GRP ESSEN", "price" => "4000"],
            ["name" => "PLATEAU COMP BX DOOM", "price" => "28000"],
            ["name" => "BIEL 2.5 GROUPE", "price" => "5000"],
            ["name" => "CDI TVS STAR", "price" => "18000"],
            ["name" => "PISTON DT 150 +", "price" => "10000"],
            ["name" => "CABLE BOUGIE BX", "price" => "1500"],
            ["name" => "BRAS FLASQUE BX", "price" => "2000"],
            ["name" => "RESSORT FREIN BX", "price" => "1000"],
            ["name" => "RESSORT FRNT TVS STAR", "price" => "1000"],
            ["name" => "CULASSE TVS STAR SP", "price" => "70000"],
            ["name" => "INSTALLATION TVS 125 HLX", "price" => "12000"],
            ["name" => "CONTACTEUR TVS SP HLX", "price" => "9500"],
        ];
    }
}

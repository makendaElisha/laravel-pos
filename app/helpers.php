<?php
if (!function_exists('activeSegment')) {
    function activeSegment($name, $segment = 2, $class = 'active')
    {
        return request()->segment($segment) == $name ? $class : '';
    }
}

if (!function_exists('posprice')) {
    function posprice($amount)
    {
        return number_format($amount, 0, ',', '.');
    }
}

if (!function_exists('movementTitle')) {
    function movementTitle($name)
    {
        $description = '';

        switch ($name) {
            case 'create_bill':
                $description = 'Vendu au magasin';
                break;

            case 'cancel_bill':
                $description = 'Suprimé de la facture';
                break;

            case 'store_increase':
                $description = 'store_increase';
                break;

            case 'store_return':
                $description = 'Retourné au dépot';
                break;

            case 'store_decrease':
                $description = 'Reduit du dépot';
                break;

            case 'shop_increase':
                $description = 'Ajouter au magasin';
                break;

            case 'shop_edited':
                $description = 'Nouveau stock saisi par admin';
                break;

            case 'manual_edit':
                $description = 'Nouveau stock modifié par admin';
                break;

            case 'init_stock':
                $description = 'Stock initial a la creation de article';
                break;
            case 'shop_petit_depot_increase':
                $description = 'Grand Depot vers Petit Depot';
                break;
            case 'petit_depot_vers_magasin':
                $description = 'Petit depot vers magasin';
                break;
            case 'petit_depot_edited':
                $description = 'Petit depot saisi par admin';
                break;

            default:
                $description = $name;
                break;
        }

        return $description;
    }
}

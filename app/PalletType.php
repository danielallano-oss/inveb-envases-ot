<?php

namespace App;

use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class PalletType extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $guarded = [];

    protected $fillable = [
        'id',
        'descripcion',
        'codigo',
        'largo',
        'ancho',
        'cant_pallet_expedicion_26',
        'size_pallet_expedicion_26',
        'cant_pallet_expedicion_27',
        'cant_pallet_expedicion_27',
        'cant_pallet_expedicion_28',
        'size_pallet_expedicion_28',
        'cant_pallet_expedicion_29',
        'size_pallet_expedicion_29',
        'cant_pallet_expedicion_30',
        'size_pallet_expedicion_30',
        'cant_pallet_expedicion_36',
        'size_pallet_expedicion_36',
        'cant_pallet_expedicion_40',
        'size_pallet_expedicion_40',
        'cant_pallet_expedicion_41',
        'size_pallet_expedicion_41',
        'cant_pallet_expedicion_42',
        'size_pallet_expedicion_42',
        'cant_pallet_expedicion_43',
        'size_pallet_expedicion_43',
        'active'
    ];

    // // id,
    // descripcion,
    // codigo,
    // largo,
    // ancho,
    //  cant_pallet_expedicion_26,
    //   size_pallet_expedicion_26,
    //   cant_pallet_expedicion_27,
    //    size_pallet_expedicion_27,
    //     cant_pallet_expedicion_28,
    //      size_pallet_expedicion_28,
    //       cant_pallet_expedicion_29,
    //        size_pallet_expedicion_29,
    //         cant_pallet_expedicion_30,
    //          size_pallet_expedicion_30,
    //           cant_pallet_expedicion_36,
    //            size_pallet_expedicion_36,
    //             cant_pallet_expedicion_40,
    //              size_pallet_expedicion_40,
    //               cant_pallet_expedicion_41,
    //                size_pallet_expedicion_41,
    //                 cant_pallet_expedicion_42,
    //                  size_pallet_expedicion_42,
    //                   cant_pallet_expedicion_43,
    //                    size_pallet_expedicion_43,
    //                     active,
    //                      created_at,
    //                       updated_at

    public function ots()
    {
        $this->hasMany(WorkOrder::class);
    }
}

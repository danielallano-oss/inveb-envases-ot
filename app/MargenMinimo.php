<?php

namespace App;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\Model;

class MargenMinimo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    
    protected $table = 'margenes_minimos'; 

    protected $fillable = ['id', 'rubro_id', 'hierarchie_id', 'cluster', 'minimo', 'address'];

    public function rubro()
    {
        return $this->hasOne(Rubro::class,'id','rubro_id');
    }
    public function hierarchie()
    {
        return $this->hasOne(Hierarchy::class,'id','hierarchie_id');
    }
    
    public function getRubroDescription() {
        if($this->rubro)
            return $this->rubro->descripcion;
        
        return false;
    }

    public function getHierarchyDescription() {
        if($this->hierarchie)
            return $this->hierarchie->descripcion;
        
        return false;
    }
}

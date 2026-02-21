<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class DetalleRequisicionSt extends Model
{
    protected $table = 'detalles_requisiciones_st';
    public $timestamps = false;
    protected $fillable = ['requisicion_st_id', 'codigo_producto', 'cantidad', 'observacion'];

    public function requisicionSt() {
        return $this->belongsTo(RequisicionSt::class, 'requisicion_st_id');
    }
}
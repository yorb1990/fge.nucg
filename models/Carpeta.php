<?php
namespace fge\nucg\models;
use Illuminate\Database\Eloquent\Model;
use App;
class CarpetaModel extends Model
{
    protected $table='carpetas';

    protected $fillable = ['nuc', 'clave', 'carpeta', 'id_estadosnuc'];
}
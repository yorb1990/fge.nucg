<?php
namespace fge\nucg\controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class nucgController extends Controller
{
    public function hnuc(Request $request){
        try{
            $nuc=$request->input('nuc');
            if(!preg_match('/^[0-9 A-Z]{4}-([0-9 A-Z]){6}-[0-9]{2}$/',$nuc)){
                return \Response::json(['message'=>'formato del nuc invalido'],506);
            }
            $nucr=new \fge\nucg\src\nucg();
            $nucr->nuccvv($nuc,$request->input('cvv'));
            $error='';
            if($nucr->IsValid($error)){            
                $nucd=new \fge\nucc\models\nucModel();
                $nucd=$nucd::where('nuc',$nuc)->first();
                if($nucd==null){
                    return \Response::json(['message'=>'no registrado'],506);
                }
                $nucd=$nucd::find($nucd->id);
                $nucd->enabled=true;
                $nucd->save();            
                $ipr=$request->ip();        
                $folio=new \fge\nucc\models\foliodoModel();
                $folio->nucl=$nuc;
                $folio->clave=$request->input('clave');
                $folio->id_estadosnuc=5;
                $folio->ip=$ipr;
                $folio->save();
                return \Response::json('habilitado',200);
            }
        } catch(\Exception $e) {
            \fge\nucg\src\log::escribe($request->ip(),$e);
            return \Response::json(['message'=>'error interno consulte al administrador'],506);
        }
    }
    public function regmod(Request $request){        
        try{
            $ip=(string)$request->ip();
            if($request->input('name')==null){
                return \Response::json(['message'=>'error nombre invalido'],506);
            }
            $modulo=new \fge\nucc\models\moduloModel();
            $modulo=$modulo
                ->where('name',$request->input('name'))
                ->where('ip',$ip)
                ->first();
            if($modulo==null){
                if(strlen($request->input('name'))>100){
                    return \Response::json(['message'=>'error nombre invalido (deve ser menor a 100 caracteres)'],506);
                }
                $name=strtoupper($request->input('name'));
                $name=str_replace("Á","A",$name);
                $name=str_replace("É","E",$name);
                $name=str_replace("Í","I",$name);
                $name=str_replace("Ó","O",$name);
                $name=str_replace("Ú","U",$name);
                $id=DB::table('modulos')->insertGetId([
                    'name'=>$name,'ip'=>$ip,'created_at'=>\Carbon\Carbon::now()
                ]);
                $clave=\fge\nucg\src\nucg::getclave($id);
                for($i=strlen($clave);$i<4;$i++){
                    $clave="0".$clave;
                }
                $modulo = \fge\nucc\models\moduloModel::find($id);
                $modulo->prefix = $clave;
                $modulo->save();
                /*DB::table('modulos')->where('id',$id)->update([
                    'prefix'=>$clave
                ]);*/
                return \Response::json(['message'=>$clave]);
            }
            return \Response::json(['message'=>$modulo->prefix]);
        } catch(\Exception $e) {
            \fge\nucg\src\log::escribe($request->ip(),$e);
            return \Response::json(['message'=>'error interno consulte al administrador'],506);
        }
    }
	public function gnuc(Request $request){
        try{
            $ipr=$request->ip();        
            $modulo=new \fge\nucc\models\moduloModel();
            $modulo=$modulo::where('prefix',$request->input('clave'))->first();
            if($modulo==null){
                return \Response::json(['message'=>'error clave invalida'],506);
            }
            $prefix=$modulo->prefix;
            $nucr=new \fge\nucg\src\nucg();
            $nucr->nnuc($prefix,date("y"));
            do{
                $nucd=new \fge\nucc\models\nucModel();
                $error='';
                if(!$nucr->fullnuc($error)){
                    return \Response::json(['message'=>$error],506);
                }            
                $nucd=$nucd::where('nuc',$nucr->nuc)->first();
            }while($nucd!=null);
            $nucd=new \fge\nucc\models\nucModel();
            $nucd->nuc=$nucr->nuc;
            $nucd->enabled=false;
            $nucd->save();
            $folio=new \fge\nucc\models\foliodoModel();
            $folio->clave=$request->input('clave');
            $folio->id_estadosnuc=1;
            $folio->ip=$ipr;
            $folio->nucl=$nucd->nuc;
            $folio->save();
            return \Response::json($nucr,200);
        } catch(\Exception $e) {
            \fge\nucg\src\log::escribe($request->ip(),$e);
            return \Response::json(['message'=>'error interno consulte al administrador'],506);
        }
    }
    public function cnuc(Request $request){
        try{
            $nuc=$request->input('nuc');
            if(!preg_match('/^[0-9 A-Z]{4}-([0-9 A-Z]){6}-[0-9]{2}$/',$nuc)){
                return \Response::json(['message'=>'formato del nuc invalido'],506);
            }
            $nucr=new \fge\nucg\src\nucg();
            $nucr->nuccvv($nuc,$request->input('cvv'));
            $error='';
            if($nucr->IsValid($error)){            
                $ipr=$request->ip();        
                $folio=new \fge\nucc\models\foliodoModel();
                $folio->nucl=$nuc;
                $folio->clave=$request->input('clave');
                $folio->id_estadosnuc=2;
                $folio->ip=$ipr;
                $folio->save();
                return \Response::json('valido',200);
            }
            return \Response::json(['message'=>$error],506);
        } catch(\Exception $e) {
            \fge\nucg\src\log::escribe($request->ip(),$e);
            return \Response::json(['message'=>'error interno consulte al administrador'],506);
        }
    }
    public function mnuc(Request $request){
        try{
            $nuc=$request->input('nuc');
            if(!preg_match('/^[0-9 A-Z]{4}-([0-9 A-Z]){6}-[0-9]{2}$/',$nuc)){
                return \Response::json(['message'=>'formato del nuc invalido'],506);
            }
            $nucr=new \fge\nucg\src\nucg();
            $nucr->nuccvv($nuc,$request->input('cvv'));
            $error='';
            if($nucr->IsValid($error)){            
                $nucd=new \fge\nucc\models\nucModel();
                $nucd=$nucd::where('nuc',$nuc)->first();
                if($nucd==null){
                    return \Response::json(['message'=>'nuc no registrado'],506);
                }
                $clave=new \fge\nucc\models\moduloModel();
                $clave=$clave::where('prefix',$request->input('clave'))->first();
                if($clave==null){
                    return \Response::json(['message'=>'clave no registrada'],506);
                }
                $ipr=$request->ip();        
                $folio=new \fge\nucc\models\foliodoModel();
                $folio->nucl=$nuc;
                $folio->clave=$request->input('clave');
                if($nucr->same($folio->clave)){
                    $folio->id_estadosnuc=4;
                }else{
                    $folio->id_estadosnuc=3;
                }            
                $folio->ip=$ipr;
                $folio->save();
                return \Response::json('movido',200);
            }
            return \Response::json(['message'=>$error],506);
        } catch(\Exception $e) {
            \fge\nucg\src\log::escribe($request->ip(),$e);
            return \Response::json(['message'=>'error interno consulte al administrador'],506);
        }
    }
}
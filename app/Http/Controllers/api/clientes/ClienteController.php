<?php

namespace App\Http\Controllers\api\clientes;

use App\Exports\admin\cliente\PlantillaClienteExport;
use App\Exports\admin\estudiante\PlantillaEstudianteExport;
use App\Exports\DynamicExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\clientes\ClienteStoreRequest;
use App\Http\Requests\clientes\ClienteUpdateRequest;
use App\Services\clientes\ClienteService;
use App\Services\estudiantes\EstudianteService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ClienteController extends Controller
{

    protected $_clienteService;
    protected $_estudianteService;
    public function __construct(){
        $this->_clienteService = new ClienteService();
        $this->_estudianteService = new EstudianteService();
    }

    public function index(Request $request)
    {
        try{
            $clientes = $this->_clienteService->index($request);
            if(!$clientes->getOk()) throw new Exception($clientes->getMsjError(), $clientes->getCode());
            return response()->json(['ok' => true, 'data' => $clientes->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function comboTipoCliente(Request $request)
    {
        try{
            $clientes = $this->_clienteService->comboTipoCliente($request);
            if(!$clientes->getOk()) throw new Exception($clientes->getMsjError(), $clientes->getCode());
            return response()->json(['ok' => true, 'data' => $clientes->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function comboClientes(Request $request)
    {
        try{
            $clientes = $this->_clienteService->comboClientes($request);
            if(!$clientes->getOk()) throw new Exception($clientes->getMsjError(), $clientes->getCode());
            return response()->json(['ok' => true, 'data' => $clientes->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function store(ClienteStoreRequest $request)
    {
        try{
            DB::beginTransaction();
            $cliente = $this->_clienteService->store($request);
            if(!$cliente->getOk()) throw new Exception($cliente->getMsjError(), $cliente->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $cliente->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function update(ClienteUpdateRequest $request, string $id)
    {
        try{
            DB::beginTransaction();
            $cliente = $this->_clienteService->update($id, $request);
            if(!$cliente->getOk()) throw new Exception($cliente->getMsjError(), $cliente->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $cliente->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function destroy($id)
    {
        try{
            DB::beginTransaction();
            $cliente = $this->_clienteService->delete($id);
            if(!$cliente->getOk()) throw new Exception($cliente->getMsjError(), $cliente->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $cliente->getData()],200);
        }catch(Exception $e){
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
    public function descargarPlantilla(Request $request){
        try {
            if($request->tipo_cliente == "ESTUDIANTE"){
                return Excel::download(new PlantillaEstudianteExport(), 'plantilla-estudiantes-' . time() . '.xlsx');
            }else{
                return Excel::download(new PlantillaClienteExport(), 'plantilla-clientes-' . time() . '.xlsx');
            }
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function cargaMasiva(Request $request){
        $request->validate([
            'file' => 'required|mimes:xls,xlsx|max:2048'
        ], [
            'file.required' => 'Debe seleccionar un archivo de Excel.',
            'file.mimes'    => 'El archivo debe ser un Excel con extensión .xls o .xlsx.',
            'file.max'      => 'El archivo no puede superar los 2 MB.',
        ]);
        try {
            DB::beginTransaction();
            $file = $request->file('file');
            $data = Excel::toArray([], $file)[0];
            if (empty($data) || count($data) <= 1) throw new Exception("El archivo está vacío o no tiene datos.");

            if($request->tipo_cliente == "ESTUDIANTE"){
                $clientes = $this->_clienteService->cargaMasivaEstudiante($data, $request->tipo_cliente);
            }else{
                $clientes = $this->_clienteService->cargaMasivaCliente($data, $request->tipo_cliente);
            }
            if(!$clientes->getOk()) throw new Exception($clientes->getMsjError(), $clientes->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $clientes->getData()],200);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function exportar(Request $request){
        try {
            if($request->tipo_cliente == "ESTUDIANTE"){
                $data = $this->_estudianteService->index($request);
            }else{
                $data = $this->_clienteService->index($request);
                
            }

            if(!$data->getOk()) throw new Exception($data->getMsjError(), $data->getCode());
            return Excel::download(new DynamicExport($data->getData(),"Clientes",null), 'clientes-' . time() . '.xlsx');
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

}

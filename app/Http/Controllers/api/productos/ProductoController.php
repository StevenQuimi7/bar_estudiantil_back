<?php

namespace App\Http\Controllers\api\productos;

use App\Exports\admin\producto\PlantillaProductoExport;
use App\Exports\DynamicExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\productos\ProductoStoreRequest;
use App\Http\Requests\productos\ProductoUpdateRequest;
use App\Services\productos\ProductoService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class ProductoController extends Controller
{
     protected $_productoService;
    public function __construct(){
        $this->_productoService = new ProductoService();
    }

    public function index(Request $request)
    {
        try{
            $productos = $this->_productoService->index($request);
            if(!$productos->getOk()) throw new Exception($productos->getMsjError(), $productos->getCode());
            return response()->json(['ok' => true, 'data' => $productos->getData()],200);
        }catch(Exception $e){
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function store(ProductoStoreRequest $request)
    {
        try{
            DB::beginTransaction();
            $producto = $this->_productoService->store($request);
            if(!$producto->getOk()) throw new Exception($producto->getMsjError(), $producto->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $producto->getData()],200);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
    
    public function update(ProductoUpdateRequest $request, string $id)
    {
        try{
            DB::beginTransaction();
            $producto = $this->_productoService->update($id, $request);
            if(!$producto->getOk()) throw new Exception($producto->getMsjError(), $producto->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $producto->getData()],200);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }

    public function destroy($id)
    {
        try{
            DB::beginTransaction();
            $producto = $this->_productoService->delete($id);
            if(!$producto->getOk()) throw new Exception($producto->getMsjError(), $producto->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $producto->getData()],200);
        }catch(Exception $e){
            DB::rollBack();
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
    public function descargarPlantilla(){
        try {
            return Excel::download(new PlantillaProductoExport(), 'plantilla-productos-' . time() . '.xlsx');
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
            $productosStore = $this->_productoService->cargaMasiva($data);
            if(!$productosStore->getOk()) throw new Exception($productosStore->getMsjError(), $productosStore->getCode());
            DB::commit();
            return response()->json(['ok' => true, 'data' => $productosStore->getData()],200);
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
    public function exportar(Request $request){
        try {
            $data = $this->_productoService->index($request);
            if(!$data->getOk()) throw new Exception($data->getMsjError(), $data->getCode());
            return Excel::download(new DynamicExport($data->getData(),"Productos",null), 'productos-' . time() . '.xlsx');
        } catch (Exception $e) {
            return response()->json(['ok' => false, 'msj' => $e->getMessage(), 500]);
        }
    }
    

}

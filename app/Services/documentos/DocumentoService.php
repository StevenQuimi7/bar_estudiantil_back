<?php
namespace App\Services\documentos;

use App\Models\categoria\Categoria;
use App\Utils\Response;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response AS ResponseHttp;
use Illuminate\Support\Str;

class DocumentoService
{
    public function __construct(){}

    public function guardarTemporal($request)
    {
        $res = new Response();
        try {

            $archivo = $request->file('file');
            
            // 1. Generar nombre único: tiempo_nombre-original.ext
            $nombreOriginal = pathinfo($archivo->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $archivo->getClientOriginalExtension();
            $nuevoNombre = time() . '_' . Str::slug($nombreOriginal) . '.' . $extension;

            // 2. Guardar en storage/app/temporales
            // 'local' es el disco por defecto en config/filesystems.php
            $path = $archivo->storeAs('temp', $nuevoNombre, 'local');

            if (!$path) {
                throw new \Exception("Error al escribir el archivo en el disco.");
            }

            // 3. Retornar la data necesaria (ruta, nombre, etc.)
            $res->setData([
                'nombre_archivo' => $nuevoNombre,
                'path'  => $path,
                'url_temporal'   => storage_path('app/' . $path)
            ]);
            $res->setOk(true);
            $res->setCode(201);
        } catch (\Exception $e) {
            Log::error("ERROR " . __FILE__ . ":" . __FUNCTION__ . " -> " . $e);
            $res->setOk(false);
            $res->setMsjError($e->getMessage());
            $res->setCode(500);
        }
        return $res;
    }

}


?>
<?php

namespace App\Http\Controllers\api\documentos;

use App\Http\Controllers\Controller;
use App\Services\documentos\DocumentoService;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class DocumentoController extends Controller
{

    protected $_documentoService;
    public function __construct(){
        $this->_documentoService = new DocumentoService();
    }

    public function guardarTemporal(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xls,xlsx,pdf,jpg,png|max:5120',
        ]);

        try {
            DB::beginTransaction();
            $documentoTemp = $this->_documentoService->guardarTemporal($request);
            if (!$documentoTemp->getOk()) throw new Exception($documentoTemp->getMsjError(), $documentoTemp->getCode());
            
            DB::commit();
            return response()->json(['ok' => true, 'data' => $documentoTemp->getData()], 200);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['ok' => false, 'msj' => $e->getMessage()], 500);
        }
    }

}


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\EstacionamentoService;

class EstacionamentoController extends Controller
{
    public function __construct(
        protected EstacionamentoService $service
    ) {}

    public function entrada(Request $request)
    {
        try {
            $request->validate([
                'placa' => 'required|string',
                'cameraId' => 'required|integer|exists:cameras,id'
            ]);

            $result = $this->service->registrarEntrada(
                $request->placa,
                $request->cameraId
            );

            return response()->json($result, 201);

        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function saida(Request $request)
    {
        try {
            $request->validate([
                'placa' => 'required|string'
            ]);

            $result = $this->service->registrarSaida($request->placa);

            return response()->json($result);

        } catch (\DomainException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function listar($placa)
    {
        return response()->json(
            $this->service->listarPorPlaca($placa)
        );
    }
}
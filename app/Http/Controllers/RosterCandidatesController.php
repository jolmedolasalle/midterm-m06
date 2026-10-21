<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\RosterStore;
use App\Http\Requests\EmptyShiftRequest;
use App\Services\ShiftCandidates;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class RosterCandidatesController extends Controller
{
    public function __construct(private readonly RosterStore $store)
    {
    }

    public function index(EmptyShiftRequest $request): View
    {
        $data = $request->validated();

        return view('roster.candidates', [
            'day' => $data['day'],
            'shift' => $data['shift'],
            'candidates' => ShiftCandidates::forSlot($this->store, $data['day'], $data['shift']),
        ]);
    }

    public function assignFirst(EmptyShiftRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Recalculamos al enviar el formulario: la lista consultada antes podría haber cambiado.
        $candidates = ShiftCandidates::forSlot($this->store, $data['day'], $data['shift']);

        if ($candidates === []) {
            return redirect()->route('roster.index')->withErrors([
                'employee_id' => 'Ningún empleado puede cubrir ese turno respetando las reglas de descanso y solapamiento.',
            ]);
        }

        $employee = $candidates[0];
        $this->store->assign($data['day'], $data['shift'], $employee->id);

        return redirect()->route('roster.index')->with(
            'status',
            "Asignación automática: {$employee->name} cubre el turno de {$data['shift']} del {$data['day']}."
        );
    }
}

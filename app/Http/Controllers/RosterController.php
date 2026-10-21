<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Contracts\RosterStore;
use App\Data\Shifts;
use App\Data\Staff;
use App\Http\Requests\AssignShiftRequest;
use App\Http\Requests\RemoveShiftRequest;
use App\Http\Requests\UpdateWeekTitleRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

final class RosterController extends Controller
{
    public function __construct(private readonly RosterStore $store)
    {
    }

    public function index(): View
    {
        $roster = [];

        // El contrato guarda ids; el componente de la plantilla recibe objetos Employee.
        foreach ($this->store->all() as $day => $dayShifts) {
            foreach ($dayShifts as $shift => $employeeId) {
                $roster[$day][$shift] = $employeeId === null ? null : Staff::find($employeeId);
            }
        }

        return view('roster.index', [
            'roster' => $roster,
            'title' => $this->store->title(),
        ]);
    }

    public function assign(AssignShiftRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $employee = Staff::findOrFail((int) $data['employee_id']);

        $this->store->assign($data['day'], $data['shift'], $employee->id);

        return redirect()->route('roster.index')->with(
            'status',
            "Turno de {$data['shift']} del {$data['day']} asignado a {$employee->name}."
        );
    }

    public function remove(RemoveShiftRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->store->remove($data['day'], $data['shift']);

        return redirect()->route('roster.index')->with(
            'status',
            "El turno de {$data['shift']} del {$data['day']} ha quedado libre."
        );
    }

    public function updateTitle(UpdateWeekTitleRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->store->setTitle($data['title'] ?? '');

        return redirect()->route('roster.index')->with('status', 'Título de la semana actualizado.');
    }

    public function summary(): View
    {
        $employees = [];
        $coverage = [];
        $emptySlots = [];
        $daysWithoutResponsible = [];

        foreach (Staff::all() as $employee) {
            $employees[$employee->id] = ['employee' => $employee, 'hours' => 0, 'excess' => 0];
        }

        // Todos los resultados se calculan a partir del contrato, sin guardarlos en sesión.
        foreach ($this->store->all() as $day => $dayShifts) {
            $hasResponsible = false;

            foreach ($dayShifts as $shift => $employeeId) {
                $coverage[$day][$shift] = $employeeId === null ? 0 : 1;

                if ($employeeId === null) {
                    $emptySlots[] = ['day' => $day, 'shift' => $shift];
                    continue;
                }

                $employees[$employeeId]['hours'] += Shifts::hours($shift);

                if ($employees[$employeeId]['employee']->role === 'responsable') {
                    $hasResponsible = true;
                }
            }

            if (!$hasResponsible) {
                $daysWithoutResponsible[] = $day;
            }
        }

        foreach ($employees as $id => $row) {
            $employees[$id]['excess'] = max(0, $row['hours'] - $row['employee']->contractHours);
        }

        return view('roster.summary', [
            'title' => $this->store->title(),
            'employees' => $employees,
            'coverage' => $coverage,
            'emptySlots' => $emptySlots,
            'daysWithoutResponsible' => $daysWithoutResponsible,
        ]);
    }
}

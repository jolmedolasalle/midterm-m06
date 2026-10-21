<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\Employee;
use App\Data\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class EmployeeController extends Controller
{
    private const ROLE_LABELS = [
        'responsable' => 'Responsable',
        'dependiente' => 'Dependiente',
        'almacen' => 'Almacén',
    ];

    public function index(Request $request): View
    {
        $requestedRole = $request->query('role');
        $role = is_string($requestedRole) && in_array($requestedRole, Staff::ROLES, true)
            ? $requestedRole
            : null;
        $sort = $request->query('sort') === 'hours' ? 'hours' : 'name';
        $employees = Staff::byRole($role);

        usort($employees, static function (Employee $first, Employee $second) use ($sort): int {
            if ($sort === 'hours' && $first->contractHours !== $second->contractHours) {
                return $first->contractHours <=> $second->contractHours;
            }

            // Las tildes no desplazan los nombres al final; los empates en horas se ordenan por nombre.
            return strcasecmp(Str::ascii($first->name), Str::ascii($second->name));
        });

        return view('employees.index', [
            'employees' => $employees,
            'role' => $role,
            'sort' => $sort,
            'roleLabels' => self::ROLE_LABELS,
            'totalEmployees' => count(Staff::all()),
        ]);
    }

    public function show(string $id): View
    {
        return view('employees.show', [
            'employee' => Staff::findOrFail((int) $id),
            'roleLabels' => self::ROLE_LABELS,
        ]);
    }
}

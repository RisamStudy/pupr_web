<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->has('role')) {
            $role = $request->role;
            $query->whereJsonContains('roles', $role);
        }

        $users = $query->get();

        // Get unique roles for the filter dropdown
        $allRoles = User::pluck('roles')->flatten()->unique();

        return view('users.index', compact('users', 'allRoles'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'roles' => 'required|array',
                'roles.*' => 'in:admin,operator,helper',
                'types' => 'required_if:roles,operator,helper|array',
                'types.*' => 'in:mekanik_alat_berat,operator_alat_berat,pembantu_mekanik,pembantu_operator',
            ]);

            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'roles' => $request->roles,
                'types' => $request->types,
                'status' => 'tersedia',
            ]);

            return redirect()->route('users.index')->with('success', 'User created successfully.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (QueryException $e) {
            return back()->withInput()->with('error', 'Failed to create user. Database error: ' . $e->getMessage());
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'An unexpected error occurred: ' . $e->getMessage());
        }
    }
    public function show(User $user, Request $request)
    {
        $filterType = $request->input('filter_type', 'all');
        $startDate = null;
        $endDate = null;

        switch ($filterType) {
            case 'week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                break;
            case 'custom':
                $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : null;
                $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : null;
                break;
            default:
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
        }
        $workHours = $user->calculateWorkHours($startDate, $endDate);

        $currentAssignment = $user->currentAssignment();
        $allAssignments = $user->workAssignments()->with(['village', 'district', 'city'])->get();

        return view('users.show', compact('user', 'currentAssignment', 'allAssignments', 'workHours', 'filterType', 'startDate', 'endDate'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'status' => 'required|in:tersedia,bertugas,tidak_ada',
            'roles' => 'required|array',
            'roles.*' => 'in:admin,operator,helper',
            'types' => 'required_if:roles.*,operator,helper|array',
            'types.*' => Rule::in(['mekanik_alat_berat', 'operator_alat_berat', 'pembantu_mekanik', 'pembantu_operator']),
            'password' => 'nullable|string|min:8|confirmed',
        ]);


        $updateData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'roles' => $validatedData['roles'],
            'status' => $validatedData['status'],
        ];

        // Handle types
        if (in_array('operator', $validatedData['roles']) || in_array('helper', $validatedData['roles'])) {
            $updateData['types'] = $validatedData['types'] ?? [];
        } else {
            $updateData['types'] = [];
        }

        // Handle password update
        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($updateData);

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->status = 'tidak ada';
        $user->save();
        return redirect()->route('users.index')->with('success', 'User deleted successfully.');
    }
}

<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UsersImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Find role and department IDs
        $role = Role::where('name', $row['role'])->orWhere('id', $row['role_id'])->first();
        $department = Department::where('name', $row['department'])->orWhere('id', $row['department_id'])->first();

        return new User([
            'name' => $row['name'],
            'email' => $row['email'],
            'password' => Hash::make($row['password'] ?? 'password'),
            'role_id' => $role->id ?? 3, // Default to regular user
            'department_id' => $department->id ?? null,
            'phone' => $row['phone'] ?? null,
            'status' => $row['status'] ?? 'active',
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'nullable|string|min:8',
            'role' => 'nullable|string|exists:roles,name',
            'role_id' => 'nullable|integer|exists:roles,id',
            'department' => 'nullable|string|exists:departments,name',
            'department_id' => 'nullable|integer|exists:departments,id',
            'phone' => 'nullable|string|max:20',
            'status' => 'nullable|in:active,inactive,suspended',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'Name is required',
            'email.required' => 'Email is required',
            'email.unique' => 'Email already exists',
            'role.exists' => 'Role not found',
            'department.exists' => 'Department not found',
        ];
    }
}
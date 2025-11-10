<?php


namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Models\Admin\Employee;
use App\Models\Admin\Department;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class EmployeeController extends Controller
{

    public function index()
    {
        $employees = Employee::with('user', 'department')->get();
        return view('admin.employee.index', compact('employees'));
    }


    public function create()
    {
        $departments = Department::where('status', 'active')->orderBy('dept_name')->get();
        $roles = Role::where('status', 'active')->where('group_type', 'admin')
                            ->whereNotIn('name', ['Admin', 'Super Admin'])->orderBy('name')->get();
        return view('admin.employee.create', compact('departments', 'roles'));
    }



    public function store(Request $request)
    {
        $request->merge([
            'ifsc_code' => strtoupper($request->ifsc_code),
            'pan_no' => strtoupper($request->pan_no),
        ]);

        $validated = $request->validate([
            'first_name'         => 'required|string|max:25',
            'last_name'          => 'required|string|max:25',
            'email'              => ['required' ,'email', Rule::unique('users')->whereNull('deleted_at') ],
            'phone'              => 'required|string|max:10|regex:/^[6-9][0-9]*$/',
            'password'           => 'required|confirmed|min:6',
            'date_of_joining'    => 'required|date',
            'emp_code'           => 'required|min:5',
            'status'             => ['required', Rule::in(['active', 'inactive'])],

            'dept_id'            => 'required|exists:departments,id',
            'role_id'            => 'required|exists:roles,id',
            'designation'        => 'required|string|max:100',
            'emp_type'           => ['required', Rule::in(['full_time', 'part_time', 'contract', 'intern'])],

            'date_of_birth'      => 'nullable|date',
            'work_location'      => 'nullable|string|max:255',
            'gender'             => ['required', Rule::in(['male', 'female', 'other'])],
            'emp_status'         => ['required', Rule::in(['active', 'probation', 'resigned', 'terminated', 'inactive'])],
            'full_address'       => 'nullable|string|max:1000',

            'bank_account_no'    => 'nullable|string|regex:/^[0-9]{9,18}$/',
            'ifsc_code'          => ['nullable', 'string', 'size:11', 'regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'bank_name'          => 'nullable|string|max:50',
            'pan_no'             => ['nullable', 'string', 'size:10', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],

            'blood_group'        => 'nullable|string|max:10',
            'marital_status'     => ['nullable', Rule::in(['single', 'married', 'divorced', 'widowed'])]
        ]);

        DB::beginTransaction();
        try{

            $username = strtolower($validated['first_name'] . '.' . $validated['last_name']);
            $originalUsername = $username;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = "{$originalUsername}{$counter}";
                $counter++;
            }

            $user = new User();
            $user->name = $validated['first_name'] . ' ' . $validated['last_name'];
            $user->username = $username;
            $user->email = $validated['email'] ;
            $user->phone = $validated['phone'] ;
            $user->password = Hash::make($validated['password']);
            $user->status = $validated['status'] ;

            $user->save();


            $role = Role::findOrFail($validated['role_id']);
            $user->assignRole($role);

            $user->role  =  $role->name;
            $user->save();

            $dateOfJoining = $validated['date_of_joining']
                ? Carbon::createFromFormat('d-m-Y', $validated['date_of_joining'])->format('Y-m-d')
                : '';

            $dateOfBirth = $validated['date_of_birth']
                ? Carbon::createFromFormat('d-m-Y', $validated['date_of_birth'])->format('Y-m-d')
                : '';

            $employee = new Employee();
            $employee->user_id         = $user->id;
            $employee->first_name      = $validated['first_name'];
            $employee->last_name       = $validated['last_name'];
            $employee->emp_code        = strtoupper($validated['emp_code']);
            $employee->designation     = strtoupper($validated['designation']);
            $employee->dept_id         = $validated['dept_id'];
            $employee->emp_type        = $validated['emp_type'];
            $employee->date_of_joining = $dateOfJoining;
            $employee->work_location   = $validated['work_location'] ?? null;
            $employee->gender          = $validated['gender'] ?? null;
            $employee->date_of_birth   = $dateOfBirth;
            $employee->blood_group     = $validated['blood_group'] ?? null;
            $employee->marital_status  = $validated['marital_status'] ?? null;
            $employee->full_address    = $validated['full_address'] ?? null;
            $employee->basic_salary    = $validated['basic_salary'] ?? 0;
            $employee->bank_account_no = $validated['bank_account_no'] ?? null;
            $employee->ifsc_code       = strtoupper($validated['ifsc_code']) ?? null;
            $employee->bank_name       = strtoupper($validated['bank_name']) ?? null;
            $employee->pan_no          = strtoupper($validated['pan_no']) ?? null;
            $employee->emp_status      = $validated['emp_status'] ?? 'active';

            $employee->save();

            DB::commit();
            return redirect()->route('employees.index')->with('success', 'Employee created successfully!');

        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Employee Creation failed: ' . $ex->getMessage());
            return back()->withInput()->with('error', 'Error creating employee: ' . $ex->getMessage());
        }

    }



    public function show(Employee $employee)
    {

    }



    public function edit(Employee $employee)
    {
        $departments = Department::all();
        $roles = Role::where('group_type', 'admin')->whereNotIn('name', ['Admin', 'Super Admin'])->get();

        return view('admin.employee.edit', compact('employee', 'departments', 'roles'));
    }


    public function update(Request $request, Employee $employee)
    {
        $request->merge([
            'ifsc_code' => strtoupper($request->ifsc_code),
            'pan_no' => strtoupper($request->pan_no),
            'emp_code' => strtoupper($request->emp_code),
            'designation' => strtoupper($request->designation),
            'bank_name' => strtoupper($request->bank_name),
        ]);

        $validated = $request->validate([
            'first_name'         => 'required|string|max:25',
            'last_name'          => 'required|string|max:25',
            'email'              => ['required','email', Rule::unique('users')->ignore($employee->user_id)->whereNull('deleted_at')],
            'phone'              => 'required|string|max:10|regex:/^[6-9][0-9]*$/',
            'password'           => 'nullable|confirmed|min:6',
            'date_of_joining'    => 'required|date',
            'emp_code'           => 'required|min:5',
            'status'             => ['required', Rule::in(['active', 'inactive'])],

            'dept_id'            => 'required|exists:departments,id',
            'role_id'            => 'required|exists:roles,id',
            'designation'        => 'required|string|max:100',
            'emp_type'           => ['required', Rule::in(['full_time', 'part_time', 'contract', 'intern'])],

            'date_of_birth'      => 'nullable|date',
            'work_location'      => 'nullable|string|max:255',
            'gender'             => ['required', Rule::in(['male', 'female', 'other'])],
            'emp_status'         => ['required', Rule::in(['active', 'probation', 'resigned', 'terminated', 'inactive'])],
            'full_address'       => 'nullable|string|max:1000',

            'bank_account_no'    => 'nullable|string|regex:/^[0-9]{9,18}$/',
            'ifsc_code'          => ['nullable','string','size:11','regex:/^[A-Z]{4}0[A-Z0-9]{6}$/'],
            'bank_name'          => 'nullable|string|max:50',
            'pan_no'             => ['nullable','string','size:10','regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],

            'blood_group'        => 'nullable|string|max:10',
            'marital_status'     => ['nullable', Rule::in(['single', 'married', 'divorced', 'widowed'])]
        ]);

        DB::beginTransaction();

        try {
            $user = $employee->user;
            if ($user) {
                $user->name = $validated['first_name'] . ' ' . $validated['last_name'];
                $user->email = $validated['email'];
                $user->phone = $validated['phone'];
                $user->status = $validated['status'];

                if ($request->filled('password')) {
                    $user->password = Hash::make($validated['password']);
                }

                $role = Role::findOrFail($validated['role_id']);
                $user->role = $role->name;
                $user->syncRoles([$role->name]);
                $user->save();
            }

            // Update employee
            $employee->first_name      = $validated['first_name'];
            $employee->last_name       = $validated['last_name'];
            $employee->emp_code        = $validated['emp_code'];
            $employee->designation     = $validated['designation'];
            $employee->dept_id         = $validated['dept_id'];
            $employee->emp_type        = $validated['emp_type'];
            $employee->date_of_joining = $validated['date_of_joining']
                ? Carbon::createFromFormat('d-m-Y', $validated['date_of_joining'])->format('Y-m-d')
                : null;
            $employee->work_location   = $validated['work_location'] ?? null;
            $employee->gender          = $validated['gender'] ?? null;
            $employee->date_of_birth   = $validated['date_of_birth']
                ? Carbon::createFromFormat('d-m-Y', $validated['date_of_birth'])->format('Y-m-d')
                : null;
            $employee->blood_group     = $validated['blood_group'] ?? null;
            $employee->marital_status  = $validated['marital_status'] ?? null;
            $employee->full_address    = $validated['full_address'] ?? null;
            $employee->basic_salary    = $validated['basic_salary'] ?? 0;
            $employee->bank_account_no = $validated['bank_account_no'] ?? null;
            $employee->ifsc_code       = $validated['ifsc_code'] ?? null;
            $employee->bank_name       = $validated['bank_name'] ?? null;
            $employee->pan_no          = $validated['pan_no'] ?? null;
            $employee->emp_status      = $validated['emp_status'] ?? 'active';

            $employee->save();

            DB::commit();
            return redirect()->route('employees.index')->with('success', 'Employee updated successfully!');

        } catch (Exception $ex) {
            DB::rollBack();
            Log::error('Employee Update failed: ' . $ex->getMessage());
            return back()->withInput()->with('error', 'Error updating employee: ' . $ex->getMessage());
        }


    }



    public function destroy(Employee $employee)
    {

        DB::beginTransaction();
        try {
            $employee->user()->delete();

            $employee->delete();

            DB::commit();
            return redirect()->route('employees.index')->with('success', 'Employee deleted successfully!');

        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('employees.index')->with('error', 'Error deleting employee: ' . $e->getMessage());
        }

    }


}

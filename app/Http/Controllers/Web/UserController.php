<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'staff')->latest();
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        $users = $query->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => 'staff',
            'pin' => $request->pin ? Hash::make($request->pin) : null,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'เพิ่มพนักงานสำเร็จ');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $request->validate([
            'name' => 'required',
            'username' => ['required', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->name = $request->name;
        $user->username = $request->username;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
        if ($request->filled('pin')) {
            $user->pin = Hash::make($request->pin);
        }
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'แก้ไขข้อมูลสำเร็จ');
    }

    public function destroy($id)
    {
        if (Auth::id() != $id)
            User::destroy($id);
        return back()->with('success', 'ลบพนักงานสำเร็จ');
    }

    public function profileForm()
    {
        return view('admin.profile', ['user' => Auth::user()]);
    }
    public function updateProfile(Request $request)
    { /* Logic */
        return back();
    }
    public function export()
    {
        // สั่งให้ดาวน์โหลดไฟล์ชื่อ users.xlsx ทันที
        return Excel::download(new UsersExport, 'users.xlsx');
    }
}

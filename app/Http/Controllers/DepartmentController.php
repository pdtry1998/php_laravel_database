<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    function index(){
        //ดึงข้อมูลแบบ Eloquent ผ่าน model
        // $departments = Department::all();

        // ดึงข้อมูลแบบ Query Builder ผ่าน DB
        // $departments = DB::table('departments')->get();

        //ทำเลขหน้าแบบ Eloquent
        //  $departments=Department::paginate(5);

        //ทำเลขหน้าแบบ Query Builder
        // $departments = DB::table('departments')->paginate(5);

        //เชื่อมตารางแบบ query builder
        // $departments = DB::table('departments')
        // ->join('users','departments.user_id','users.id')
        // ->select('departments.*','users.name')->paginate(5);

        //เชื่อมตารางแบบ Eloquent
        $departments=Department::paginate(4);
        $trashDepartment = Department::onlyTrashed()->paginate(2);

        return view('admin.department.index',compact('departments','trashDepartment'));
    }
    function store(Request $request) {

        // ตรวจสอบข้อมูล
        $request->validate([
            'department_name'=>'required|unique:departments|max:255'],
    ['department_name.required'=>'กรุณาป้อนชื่อแผนก',
    'department_name.max'=>'ห้ามป้อนเกิน 255 ตัวอักษร',
    'department_name.unique'=>'มีข้อมูลชื่อแผนกนี้ในฐานข้อมูลแล้ว'
    ]
    );
    // บันทึกข้อมูลแบบ Eloquent
    // $department = new Department; //model
    // $department->department_name  = $request->department_name;
    // $department->user_id = Auth::user()->id;
    // $department->save();


    // บันทึกข้อมูลแบบ Query Builder
    $data = array();
    $data["department_name"]= $request->department_name;
    $data["user_id"] = Auth::user()->id;
    DB::table('departments')->insert($data);

    return redirect()->back()->with('success','บันทึกข้อมูลเรียบร้อย');

    }

    function edit($id) {
        $department = Department::find($id);
        return view('admin.department.edit',compact('department'));
    }

    function update(Request $request , $id) {
         // ตรวจสอบข้อมูล
         $request->validate(
            [
                 'department_name'=>'required|unique:departments|max:255'
            ],
            [
                'department_name.required'=>'กรุณาป้อนชื่อแผนก',
                'department_name.max'=>'ห้ามป้อนเกิน 255 ตัวอักษร',
                'department_name.unique'=>'มีข้อมูลชื่อแผนกนี้ในฐานข้อมูลแล้ว'
            ]
        );
        $update = Department::find($id)->update([
            'department_name'=>$request->department_name,
            'user_id'=>Auth::user()->id
        ]);
        return redirect()->route('department')->with('success','อัพเดทข้อมูลเรียบร้อย');
    }

     function softdelete($id){
       $delete =  Department::find($id)->delete();
       return redirect()->back()->with('success','ลบข้อมูลเรียบร้อย');
    }


    function restore ($id) {
        $restore = Department::withTrashed()->find($id)->restore();
        return redirect()->back()->with('success','กู้คืนข้อมูลเรียบร้อย');
    }

    function delete($id) {
        $delete = Department::onlyTrashed()->find($id)->forceDelete();
        return redirect()->back()->with('success','ลบข้อมูลถาวรเรียบร้อย');
    }
}

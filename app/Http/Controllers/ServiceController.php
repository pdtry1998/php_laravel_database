<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ServiceController extends Controller
{
    function index() {
        $services=Service::paginate(4);
        
        return view('admin.service.index',compact('services'));
    }

    function edit($id) {
        $service = Service::find($id);
        return view('admin.service.edit',compact('service'));
    }

    function update(Request $request , $id) {
        // ตรวจสอบข้อมูล
        $request->validate(
            [
                'service_name'=>'required|max:255',
                // 'service_image'=>'mimes:jpg,jpeg,png'
            ],
            [
                'service_name.required'=>'กรุณาป้อนชื่อบริการ',
                // 'service_image.mimes'=>'กรุณาอัพโหลดไฟล์ jpg , jpeg และ png เท่านั้น'
            ]
       );
        // การเข้ารหัสรูปภาพ
        $service_image = $request->file('service_image');
        //อัพเดทภาพและชื่อ
        if($service_image){
            // Generate ชื่อภาพ
            $name_gen = hexdec(uniqid());
            // ดึงนามสกุลไฟล์ภาพ
              $img_ext = strtolower($service_image->getClientOriginalExtension());
            // รวมชื่อภาพ กับนามสกุลไฟล์ภาพ
              $img_name = $name_gen.'.'.$img_ext;
            //อัพโหลดและอัพเดทข้อมูล
              $upload_location = 'image/services/';
              $full_path = $upload_location.$img_name;

            //อัพเดทข้อมูล
              Service::find($id)->update([
                'service_name'=>$request->service_name,
                'service_image'=>$full_path,
            ]);

            // ลบภาพเก่าออกและอัพภาพใหม่แทนที่
                $old_image = $request -> old_image;
                \unlink($old_image);
                $service_image->move($upload_location,$img_name);
                return redirect()->route('service')->with('success','อัพเดตภาพเรียบร้อย');
        }else{
            //อัพเดทชื่ออย่างเดัยว
            Service::find($id)->update([
                'service_name'=>$request->service_name
            ]);
                return redirect()->route('service')->with('success','อัพเดตชื่อบริการเรียบร้อย');
        }
   }

    function store(Request $request) {
    // ตรวจสอบข้อมูล
    $request->validate(
        [
            'service_name'=>'required|unique:services|max:255',

            'service_image'=>'required|mimes:jpg,jpeg,png'
        ],
        [
            'service_name.required'=>'กรุณาป้อนชื่อบริการ',
            'service_name.max'=>'ห้ามป้อนเกิน 255 ตัวอักษร',
            'service_name.unique'=>'มีข้อมูลชื่อบริการนี้ในฐานข้อมูลแล้ว',

            'service_image.required'=>'กรุณาใส่ภาพประกอบการบริการ',
            'service_image.mimes'=>'กรุณาอัพโหลดไฟล์ jpg , jpeg และ png เท่านั้น'
        ]
    );
    // การเข้ารหัสรูปภาพ
    $service_image = $request->file('service_image');
    // Generate ชื่อภาพ
    $name_gen = hexdec(uniqid());
    // ดึงนามสกุลไฟล์ภาพ
    $img_ext = strtolower($service_image->getClientOriginalExtension());
    // รวมชื่อภาพ กับนามสกุลไฟล์ภาพ
    $img_name = $name_gen.'.'.$img_ext;
    //อัพโหลดและบันททึกข้อมูล
    $upload_location = 'image/services/';
    $full_path = $upload_location.$img_name;
    
    Service::insert([
        'service_name'=>$request->service_name,
        'service_image'=>$full_path,
        'created_at'=>Carbon::now()
    ]);
    $service_image->move($upload_location,$img_name);
    return redirect()->back()->with('success','บันทึกข้อมูลเรียบร้อย');
    }


    function delete($id) {
        // ลบภาพ
        $img = Service::find($id)->service_image;
        unlink($img);
        // //ลบข้อมูลจากฐานข้อมูล
        $delete = Service::find($id)->delete();
        return redirect()->back()->with('success','ลบข้อมูลเรียบร้อย');
    }

    
}
